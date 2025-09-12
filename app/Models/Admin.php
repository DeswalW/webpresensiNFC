<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'assigned_class',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isWaliKelas()
    {
        return $this->role === 'wali_kelas';
    }

    public function getAssignedClass()
    {
        return $this->assigned_class;
    }
}
