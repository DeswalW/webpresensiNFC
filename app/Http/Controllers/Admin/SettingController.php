<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AttendanceSetting::getActive();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'entry_time' => 'required|date_format:H:i',
            'late_threshold' => 'required|date_format:H:i|after:entry_time',
            'end_time' => 'nullable|date_format:H:i|after:late_threshold',
        ]);

        // Nonaktifkan pengaturan lama
        AttendanceSetting::where('is_active', true)->update(['is_active' => false]);

        // Buat pengaturan baru
        AttendanceSetting::create([
            'entry_time' => $request->entry_time,
            'late_threshold' => $request->late_threshold,
            'end_time' => $request->end_time,
            'is_active' => true,
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan presensi berhasil diperbarui.');
    }
}
