<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nis',
        'name',
        'nfc_id',
        'class',
        'gender',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getTodayAttendanceAttribute()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }

    public function getAttendanceStatusAttribute()
    {
        $attendance = $this->today_attendance;
        return $attendance ? $attendance->status : 'absent';
    }
}
