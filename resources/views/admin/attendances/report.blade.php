@extends('layouts.admin')

@section('title', 'Laporan Presensi - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-chart-bar mr-3 text-primary-green"></i>Laporan Presensi
    </h1>
    <div class="flex space-x-3">
        <a href="{{ route('admin.attendances.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left btn-icon"></i>
            <span class="btn-text">Kembali</span>
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6 mb-6">
    <form method="GET" action="{{ route('admin.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" class="form-input" id="start_date" name="start_date" 
                   value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
            <input type="date" class="form-input" id="end_date" name="end_date" 
                   value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
        </div>
        <div>
            <label for="class" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
            <select class="form-select" id="class" name="class">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}" {{ request('class') === $class ? 'selected' : '' }}>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
            <select class="form-select" id="student_id" name="student_id">
                <option value="">Semua Siswa</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                        {{ $student->name }} ({{ $student->class }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end space-x-2 md:col-span-4">
            <button type="submit" class="btn-primary">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.report') }}" class="btn-secondary">
                <i class="fas fa-refresh mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    <!-- Total Presensi -->
    <div class="card p-6 border-l-4 border-blue-500 bg-gradient-to-r from-blue-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Total Presensi</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? '100%' : '0%' }} dari total
                </p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-calendar-check text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Hadir -->
    <div class="card p-6 border-l-4 border-green-500 bg-gradient-to-r from-green-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Hadir</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['present']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? number_format(($stats['present'] / $stats['total']) * 100, 1) : 0 }}% dari total
                </p>
            </div>
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="card p-6 border-l-4 border-yellow-500 bg-gradient-to-r from-yellow-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Terlambat</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['late']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? number_format(($stats['late'] / $stats['total']) * 100, 1) : 0 }}% dari total
                </p>
            </div>
            <div class="p-3 bg-yellow-100 rounded-full">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>

    <!-- Tidak Hadir -->
    <div class="card p-6 border-l-4 border-red-500 bg-gradient-to-r from-red-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Tidak Hadir</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['absent']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? number_format(($stats['absent'] / $stats['total']) * 100, 1) : 0 }}% dari total
                </p>
            </div>
            <div class="p-3 bg-red-100 rounded-full">
                <i class="fas fa-times-circle text-2xl text-red-600"></i>
            </div>
        </div>
    </div>

    <!-- Sakit -->
    <div class="card p-6 border-l-4 border-purple-500 bg-gradient-to-r from-purple-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Sakit</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['sick']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? number_format(($stats['sick'] / $stats['total']) * 100, 1) : 0 }}% dari total
                </p>
            </div>
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-user-injured text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <!-- Izin -->
    <div class="card p-6 border-l-4 border-gray-500 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Izin</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['permit']) }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $stats['total'] > 0 ? number_format(($stats['permit'] / $stats['total']) * 100, 1) : 0 }}% dari total
                </p>
            </div>
            <div class="p-3 bg-gray-100 rounded-full">
                <i class="fas fa-user-clock text-2xl text-gray-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Persentase -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-chart-pie mr-2 text-primary-green"></i>Ringkasan Kehadiran
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                <div>
                    <p class="text-sm font-medium text-green-800">Kehadiran Positif</p>
                    <p class="text-lg font-bold text-green-900">
                        {{ $stats['total'] > 0 ? number_format((($stats['present'] + $stats['late']) / $stats['total']) * 100, 1) : 0 }}%
                    </p>
                    <p class="text-xs text-green-600">{{ number_format($stats['present'] + $stats['late']) }} dari {{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-3"></div>
                <div>
                    <p class="text-sm font-medium text-red-800">Ketidakhadiran</p>
                    <p class="text-lg font-bold text-red-900">
                        {{ $stats['total'] > 0 ? number_format(($stats['absent'] / $stats['total']) * 100, 1) : 0 }}%
                    </p>
                    <p class="text-xs text-red-600">{{ number_format($stats['absent']) }} dari {{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                <div>
                    <p class="text-sm font-medium text-blue-800">Izin & Sakit</p>
                    <p class="text-lg font-bold text-blue-900">
                        {{ $stats['total'] > 0 ? number_format((($stats['sick'] + $stats['permit']) / $stats['total']) * 100, 1) : 0 }}%
                    </p>
                    <p class="text-xs text-blue-600">{{ number_format($stats['sick'] + $stats['permit']) }} dari {{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Data Presensi</h3>
        <div class="flex space-x-2">
            <button type="button" class="btn-success text-xs px-3 py-2" onclick="exportToExcel()">
                <i class="fas fa-file-excel mr-1"></i>Export Excel
            </button>
            <button type="button" class="btn-danger text-xs px-3 py-2" onclick="exportToPDF()">
                <i class="fas fa-file-pdf mr-1"></i>Export PDF
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="reportTable">
            <thead class="bg-primary-black">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Waktu Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attendances as $index => $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $attendance->student->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->student->class }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->entry_time ? $attendance->entry_time->format('H:i') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="badge badge-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : ($attendance->status === 'absent' ? 'danger' : ($attendance->status === 'sick' ? 'info' : 'secondary'))) }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data presensi untuk periode yang dipilih</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
function exportToExcel() {
    // Implementasi export ke Excel
    alert('Fitur export Excel akan segera tersedia');
}

function exportToPDF() {
    // Implementasi export ke PDF
    alert('Fitur export PDF akan segera tersedia');
}
</script>
@endsection

