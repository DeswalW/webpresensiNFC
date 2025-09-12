<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\AttendanceSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('student');
        
        // Filter berdasarkan wali kelas
        $currentAdmin = auth()->guard('admin')->user();
        if ($currentAdmin->isWaliKelas()) {
            $query->whereHas('student', function($q) use ($currentAdmin) {
                $q->where('class', $currentAdmin->getAssignedClass());
            });
        }
        
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('class')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class', $request->class);
            });
        }
        
        $attendances = $query->orderBy('entry_time', 'desc')->paginate(20);
        $classes = Student::distinct()->pluck('class')->sort();
        
        return view('admin.attendances.index', compact('attendances', 'classes'));
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        $query = Attendance::with('student')
                          ->whereBetween('date', [$startDate, $endDate]);
        
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('class')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class', $request->class);
            });
        }
        
        $attendances = $query->orderBy('date', 'desc')->get();
        
        // Hitung statistik
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'permit' => $attendances->where('status', 'permit')->count(),
        ];
        
        $students = Student::where('is_active', true)->orderBy('name')->get();
        $classes = Student::distinct()->pluck('class')->sort();
        
        return view('admin.attendances.report', compact(
            'attendances', 
            'stats', 
            'students', 
            'classes',
            'startDate',
            'endDate'
        ));
    }

    public function manualEntry(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'date' => 'required|date',
                'entry_time' => 'required|date_format:H:i',
                'status' => 'required|in:present,late,absent,sick,permit',
                'notes' => 'nullable|string',
            ]);
            
            // Cek apakah sudah ada presensi untuk siswa dan tanggal tersebut
            $existing = Attendance::where('student_id', $request->student_id)
                                 ->whereDate('date', $request->date)
                                 ->first();
            
            if ($existing) {
                $existing->update([
                    'entry_time' => $request->entry_time,
                    'status' => $request->status,
                    'notes' => $request->notes,
                ]);
            } else {
                Attendance::create([
                    'student_id' => $request->student_id,
                    'date' => $request->date,
                    'entry_time' => $request->entry_time,
                    'status' => $request->status,
                    'notes' => $request->notes,
                    'nfc_id' => 'MANUAL',
                ]);
            }
            
            return redirect()->route('admin.attendances.index')
                ->with('success', 'Presensi berhasil ditambahkan.');
        }
        
        $students = Student::where('is_active', true)->orderBy('name')->get();
        return view('admin.attendances.manual-entry', compact('students'));
    }

    public function apiRecord(Request $request)
    {
        $request->validate([
            'nfc_id' => 'required|string',
            'timestamp' => 'required|date',
        ]);
        
        $student = Student::where('nfc_id', $request->nfc_id)
                         ->where('is_active', true)
                         ->first();
        
        if (!$student) {
            ActivityLog::create([
                'actor_type' => 'system',
                'action' => 'attendance.rejected',
                'description' => 'NFC tidak dikenali atau siswa nonaktif',
                'nfc_id' => $request->nfc_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'context' => [
                    'reason' => 'student_not_found_or_inactive',
                    'timestamp' => $request->timestamp,
                ],
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC tidak terdaftar atau siswa tidak aktif',
                'uid' => $request->nfc_id
            ], 404);
        }
        
        $timestamp = Carbon::parse($request->timestamp);
        $date = $timestamp->toDateString();
        $time = $timestamp->format('H:i:s');
        
        // Ambil pengaturan presensi
        $settings = AttendanceSetting::getActive();
        $status = 'present';
        
        if ($settings) {
            // Normalisasi waktu pengaturan ke format H:i:s untuk perbandingan string yang konsisten
            $entry = optional($settings->entry_time)->format('H:i:s');
            $late  = optional($settings->late_threshold)->format('H:i:s');
            $end   = optional($settings->end_time)->format('H:i:s');

            // Tolak jika sebelum jam masuk atau setelah batas akhir (jika diatur)
            if ($entry && $time < $entry) {
                ActivityLog::create([
                    'actor_type' => 'system',
                    'action' => 'attendance.rejected',
                    'description' => 'Presensi sebelum jam masuk',
                    'nfc_id' => $request->nfc_id,
                    'student_id' => $student->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'context' => [
                        'time' => $time,
                        'entry_time' => $entry,
                    ],
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi belum dibuka',
                    'uid' => $request->nfc_id,
                ], 400);
            }

            if ($end && $time > $end) {
                ActivityLog::create([
                    'actor_type' => 'system',
                    'action' => 'attendance.rejected',
                    'description' => 'Presensi setelah batas akhir',
                    'nfc_id' => $request->nfc_id,
                    'student_id' => $student->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'context' => [
                        'time' => $time,
                        'end_time' => $end,
                    ],
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi sudah ditutup',
                    'uid' => $request->nfc_id,
                ], 400);
            }

            if ($late && $time > $late) {
                $status = 'late';
            }
        }
        
        // Cek apakah sudah ada presensi hari ini
        $existing = Attendance::where('student_id', $student->id)
                             ->whereDate('date', $date)
                             ->first();
        
        if ($existing) {
            ActivityLog::create([
                'actor_type' => 'system',
                'action' => 'attendance.rejected',
                'description' => 'Presensi duplikat pada hari yang sama',
                'nfc_id' => $request->nfc_id,
                'student_id' => $student->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'context' => [
                    'date' => $date,
                ],
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan presensi hari ini',
                'uid' => $request->nfc_id,
                'student_name' => $student->name
            ], 400);
        } else {
            // Buat presensi baru
            Attendance::create([
                'student_id' => $student->id,
                'date' => $date,
                'entry_time' => $time,
                'status' => $status,
                'nfc_id' => $request->nfc_id,
            ]);
            ActivityLog::create([
                'actor_type' => 'system',
                'action' => 'attendance.recorded',
                'description' => 'Presensi dicatat',
                'nfc_id' => $request->nfc_id,
                'student_id' => $student->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'context' => [
                    'status' => $status,
                    'time' => $time,
                    'date' => $date,
                ],
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dicatat',
            'uid' => $request->nfc_id,
            'student' => [
                'name' => $student->name,
                'class' => $student->class,
                'status' => $status,
                'time' => $time,
            ]
        ]);
    }

    public function apiStatus()
    {
        $settings = AttendanceSetting::getActive();
        $today = today();
        
        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'present_today' => Attendance::whereDate('date', $today)
                                        ->whereIn('status', ['present', 'late'])
                                        ->count(),
            'absent_today' => Attendance::whereDate('date', $today)
                                       ->where('status', 'absent')
                                       ->count(),
            'late_threshold' => $settings ? $settings->late_threshold : '08:00:00',
            'server_time' => now()->format('Y-m-d H:i:s'),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
