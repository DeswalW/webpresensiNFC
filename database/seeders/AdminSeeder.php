<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@sekolah.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Wali Kelas X DPB',
            'email' => 'walikelas1@sekolah.com',
            'username' => 'walikelas1',
            'password' => Hash::make('admin123'),
            'role' => 'wali_kelas',
            'assigned_class' => 'X DPB',
            'is_active' => true,
        ]);

        Admin::create([
            'name' => 'Wali Kelas X DKV',
            'email' => 'walikelas2@sekolah.com',
            'username' => 'walikelas2',
            'password' => Hash::make('admin123'),
            'role' => 'wali_kelas',
            'assigned_class' => 'X DKV',
            'is_active' => true,
        ]);
    }
}
