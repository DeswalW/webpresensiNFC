@extends('layouts.admin')

@section('title', 'Dashboard - Sistem Presensi')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-tachometer-alt mr-2 sm:mr-3 text-primary-green"></i>Dashboard
    </h1>
    <div class="text-gray-600">
        <span class="text-responsive-sm">{{ now()->format('d F Y H:i') }}</span>
    </div>
</div>

<!-- Statistik Hari Ini -->
<div class="grid-responsive-4 mb-6 sm:mb-8">
    <div class="card p-4 sm:p-6 border-l-4 border-primary-green">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-responsive-sm font-medium text-gray-600 uppercase tracking-wide">Total Siswa</p>
                <p class="text-responsive-3xl font-bold text-gray-900">{{ $totalStudents }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-primary-green bg-opacity-10 rounded-full">
                <i class="fas fa-users text-xl sm:text-2xl text-primary-green"></i>
            </div>
        </div>
    </div>

    <div class="card p-4 sm:p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-responsive-sm font-medium text-gray-600 uppercase tracking-wide">Hadir Hari Ini</p>
                <p class="text-responsive-3xl font-bold text-gray-900">{{ $presentToday }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-green-100 rounded-full">
                <i class="fas fa-check-circle text-xl sm:text-2xl text-green-500"></i>
            </div>
        </div>
    </div>

    <div class="card p-4 sm:p-6 border-l-4 border-primary-yellow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-responsive-sm font-medium text-gray-600 uppercase tracking-wide">Terlambat</p>
                <p class="text-responsive-3xl font-bold text-gray-900">{{ $lateToday }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-clock text-xl sm:text-2xl text-primary-yellow"></i>
            </div>
        </div>
    </div>

    <div class="card p-4 sm:p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-responsive-sm font-medium text-gray-600 uppercase tracking-wide">Tidak Hadir</p>
                <p class="text-responsive-3xl font-bold text-gray-900">{{ $absentToday }}</p>
            </div>
            <div class="p-2 sm:p-3 bg-red-100 rounded-full">
                <i class="fas fa-times-circle text-xl sm:text-2xl text-red-500"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Grafik Kehadiran 7 Hari Terakhir -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-l overflow-hidden p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h3 class="text-responsive-lg font-semibold text-gray-900">Kehadiran 7 Hari Terakhir</h3>
            </div>
            <div class="h-48 sm:h-64">
                <canvas id="attendanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Statistik Minggu Ini -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-l overflow-hidden p-4 sm:p-6">
            <h3 class="text-responsive-lg font-semibold text-gray-900 mb-4 sm:mb-6">Statistik Minggu Ini</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Hadir</span>
                        <span class="badge badge-success">{{ $weeklyStats['present'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalStudents > 0 ? (($weeklyStats['present'] ?? 0) / $totalStudents) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Terlambat</span>
                        <span class="badge badge-warning">{{ $weeklyStats['late'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-primary-yellow h-2 rounded-full" style="width: {{ $totalStudents > 0 ? (($weeklyStats['late'] ?? 0) / $totalStudents) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Tidak Hadir</span>
                        <span class="badge badge-danger">{{ $weeklyStats['absent'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalStudents > 0 ? (($weeklyStats['absent'] ?? 0) / $totalStudents) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Sakit</span>
                        <span class="badge badge-info">{{ $weeklyStats['sick'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalStudents > 0 ? (($weeklyStats['sick'] ?? 0) / $totalStudents) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Izin</span>
                        <span class="badge badge-secondary">{{ $weeklyStats['permit'] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gray-500 h-2 rounded-full" style="width: {{ $totalStudents > 0 ? (($weeklyStats['permit'] ?? 0) / $totalStudents) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Presensi Terbaru -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-4">
        <h3 class="text-responsive-lg font-semibold text-gray-900">Presensi Terbaru Hari Ini</h3>
        <a href="{{ route('admin.attendances.index') }}" class="btn-primary w-full sm:w-auto text-center">
            <i class="fas fa-eye mr-2"></i>Lihat Semua
        </a>
    </div>
    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider mobile-hidden">Kelas</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider mobile-hidden">Waktu Masuk</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentAttendances as $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 sm:px-6 py-4 text-sm font-medium text-gray-900">
                        <div class="mobile-hidden">{{ $attendance->student->name }}</div>
                        <div class="mobile-only">
                            <div class="font-medium">{{ $attendance->student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $attendance->student->class }} • {{ $attendance->entry_time ? $attendance->entry_time->format('H:i') : '-' }}</div>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 mobile-hidden">{{ $attendance->student->class }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 mobile-hidden">{{ $attendance->entry_time ? $attendance->entry_time->format('H:i') : '-' }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        <span class="badge badge-{{ $attendance->status_color }}">
                            {{ $attendance->status_label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-3 sm:px-6 py-4 text-center text-sm text-gray-500">Belum ada presensi hari ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Grafik kehadiran
const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($last7Days->pluck('date')),
        datasets: [{
            label: 'Hadir',
            data: @json($last7Days->pluck('present')),
            borderColor: '#289341',
            backgroundColor: 'rgba(40, 147, 65, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Tidak Hadir',
            data: @json($last7Days->pluck('absent')),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            }
        },
        elements: {
            point: {
                radius: 4,
                hoverRadius: 6
            }
        }
    }
});
</script>
@endsection


