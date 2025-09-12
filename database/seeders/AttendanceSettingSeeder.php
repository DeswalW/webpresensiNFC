<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceSetting;

class AttendanceSettingSeeder extends Seeder
{
    public function run()
    {
        AttendanceSetting::create([
            'entry_time' => '07:00:00',
            'late_threshold' => '07:15:00',
            'end_time' => '08:00:00',
            'is_active' => true,
        ]);
    }
}