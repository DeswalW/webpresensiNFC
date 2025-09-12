<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Statistik hari ini
        $totalStudents = Student::where('is_active', true)->count();
        $presentToday = Attendance::whereDate('date', $today)
                                 ->whereIn('status', ['present', 'late'])
                                 ->count();
        $lateToday = Attendance::whereDate('date', $today)
                               ->where('status', 'late')
                               ->count();
        $absentToday = $totalStudents - $presentToday;

        // Statistik minggu ini
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();
        
        $weeklyStats = Attendance::whereBetween('date', [$weekStart, $weekEnd])
                                ->selectRaw('status, COUNT(*) as count')
                                ->groupBy('status')
                                ->pluck('count', 'status')
                                ->toArray();

        // Presensi terbaru
        $recentAttendances = Attendance::with('student')
                                      ->whereDate('date', $today)
                                      ->orderBy('entry_time', 'desc')
                                      ->limit(10)
                                      ->get();

        // Grafik kehadiran 7 hari terakhir
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $present = Attendance::whereDate('date', $date)
                                ->whereIn('status', ['present', 'late'])
                                ->count();
            $absent = $totalStudents - $present;
            
            $last7Days->push([
                'date' => $date->format('d/m'),
                'present' => $present,
                'absent' => $absent,
            ]);
        }

        return view('admin.dashboard', compact(
            'totalStudents',
            'presentToday',
            'lateToday',
            'absentToday',
            'weeklyStats',
            'recentAttendances',
            'last7Days'
        ));
    }
}


