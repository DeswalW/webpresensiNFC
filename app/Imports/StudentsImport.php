<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\Validator;

class StudentsImport
{
    private int $inserted = 0;
    private int $updated = 0;
    private int $skipped = 0;

    public function import($rows): void
    {
        foreach ($rows as $row) {
            $data = [
                'nis' => $row['nis'] ?? null,
                'name' => $row['name'] ?? null,
                'class' => $row['class'] ?? null,
                'nfc_id' => $row['nfc_id'] ?? null,
                'gender' => $row['gender'] ?? null,
            ];

            $validator = Validator::make($data, [
                'nis' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'class' => 'required|string|max:50',
                'nfc_id' => 'required|string|max:255',
                'gender' => 'required|in:L,P',
            ]);
            if ($validator->fails()) { $this->skipped++; continue; }

            $student = Student::where('nis', $data['nis'])->orWhere('nfc_id', $data['nfc_id'])->first();
            if ($student) {
                $student->fill($data);
                if ($student->isDirty()) { $student->save(); $this->updated++; } else { $this->skipped++; }
            } else {
                Student::create($data + ['is_active' => true]);
                $this->inserted++;
            }
        }
    }

    public function getSummary(): array
    {
        return [
            'inserted' => $this->inserted,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
        ];
    }
}



