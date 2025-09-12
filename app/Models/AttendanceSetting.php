<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'entry_time',
        'late_threshold',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'late_threshold' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }
}
