<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'description',
        'nfc_id',
        'student_id',
        'ip_address',
        'user_agent',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getEventTimeAttribute()
    {
        $context = $this->context ?? [];

        if (isset($context['timestamp']) && $context['timestamp']) {
            return Carbon::parse($context['timestamp']);
        }

        if (isset($context['date']) && isset($context['time']) && $context['date'] && $context['time']) {
            return Carbon::parse($context['date'] . ' ' . $context['time']);
        }

        if (isset($context['time']) && $context['time']) {
            return Carbon::parse($this->created_at->toDateString() . ' ' . $context['time']);
        }

        return $this->created_at;
    }
}


