<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();
        
        // Filter berdasarkan wali kelas
        $currentAdmin = auth()->guard('admin')->user();
        if ($currentAdmin->isWaliKelas()) {
            $query->where('class', $currentAdmin->getAssignedClass());
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nfc_id', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }
        
        $students = $query->orderBy('name')->paginate(15);
        $classes = Student::distinct()->pluck('class')->sort();
        
        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|unique:students,nis',
            'name' => 'required|string|max:255',
            'nfc_id' => 'required|unique:students,nfc_id',
            'class' => 'required|string|max:50',
            'gender' => 'required|in:L,P',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Student::create($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'nfc_id' => 'required|unique:students,nfc_id,' . $student->id,
            'class' => 'required|string|max:50',
            'gender' => 'required|in:L,P',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function toggleStatus(Student $student)
    {
        $student->update(['is_active' => !$student->is_active]);

        $status = $student->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.students.index')
            ->with('success', "Siswa berhasil {$status}.");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $file = $request->file('file');

        // Jika maatwebsite/excel tersedia, gunakan; jika tidak, fallback ke CSV parser sederhana
        if (class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
            try {
                return \DB::transaction(function () use ($file) {
                    $import = new \App\Imports\StudentsImport();
                    \Maatwebsite\Excel\Facades\Excel::import($import, $file);
                    $summary = $import->getSummary();
                    return redirect()->route('admin.students.index')
                        ->with('success', "Import selesai: {$summary['inserted']} ditambah, {$summary['updated']} diperbarui, {$summary['skipped']} dilewati.");
                });
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
            }
        }

        // Fallback CSV parser (comma separated, header wajib: nis,name,class,nfc_id)
        if ($file->getClientOriginalExtension() === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                return redirect()->back()->with('error', 'Tidak dapat membaca file CSV.');
            }

            $header = fgetcsv($handle);
            if (!$header) {
                return redirect()->back()->with('error', 'Header CSV tidak ditemukan.');
            }
            $header = array_map('strtolower', $header);
            $required = ['nis','name','class','nfc_id'];
            foreach ($required as $col) {
                if (!in_array($col, $header)) {
                    return redirect()->back()->with('error', "Kolom '{$col}' wajib ada pada header CSV.");
                }
            }

            $inserted = 0; $updated = 0; $skipped = 0; $line = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                $data = array_combine($header, $row);
                if (!$data) { $skipped++; continue; }

                $validator = Validator::make($data, [
                    'nis' => 'required|string|max:255',
                    'name' => 'required|string|max:255',
                    'class' => 'required|string|max:50',
                    'nfc_id' => 'required|string|max:255',
                ]);
                if ($validator->fails()) { $skipped++; continue; }

                $student = Student::where('nis', $data['nis'])->orWhere('nfc_id', $data['nfc_id'])->first();
                if ($student) {
                    $student->fill([
                        'nis' => $data['nis'],
                        'name' => $data['name'],
                        'class' => $data['class'],
                        'nfc_id' => $data['nfc_id'],
                    ]);
                    if ($student->isDirty()) { $student->save(); $updated++; } else { $skipped++; }
                } else {
                    Student::create([
                        'nis' => $data['nis'],
                        'name' => $data['name'],
                        'class' => $data['class'],
                        'nfc_id' => $data['nfc_id'],
                        'is_active' => true,
                    ]);
                    $inserted++;
                }
            }
            fclose($handle);

            return redirect()->route('admin.students.index')
                ->with('success', "Import CSV selesai: {$inserted} ditambah, {$updated} diperbarui, {$skipped} dilewati.");
        }

        return redirect()->back()->with('error', 'Format file tidak didukung tanpa paket Excel. Unggah CSV atau pasang paket Excel.');
    }

    public function downloadTemplate()
    {
        $headers = ['Content-Type' => 'text/csv'];
        $filename = 'template_import_siswa.csv';
        $content = "nis,name,class,nfc_id,gender\n12345,Ahmad Siswa,X IPA 1,04A1B2C3,L\n";
        return response($content, 200, array_merge($headers, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]));
    }

    public function apiCheck($nfcId)
    {
        $student = Student::where('nfc_id', $nfcId)
                         ->where('is_active', true)
                         ->first();
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC tidak terdaftar atau siswa tidak aktif',
                'nfc_id' => $nfcId
            ], 404);
        }
        
        // Cek apakah sudah ada presensi hari ini
        $todayAttendance = \App\Models\Attendance::where('student_id', $student->id)
                                                ->whereDate('date', today())
                                                ->first();
        
        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class,
                'nis' => $student->nis,
                'nfc_id' => $student->nfc_id,
            ],
            'attendance_today' => $todayAttendance ? [
                'status' => $todayAttendance->status,
                'time' => $todayAttendance->entry_time,
                'date' => $todayAttendance->date,
            ] : null
        ]);
    }
}
