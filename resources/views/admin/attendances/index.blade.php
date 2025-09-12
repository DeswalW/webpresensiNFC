@extends('layouts.admin')

@section('title', 'Data Presensi - Sistem Presensi')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-calendar-check mr-2 sm:mr-3 text-primary-green"></i>Data Presensi
    </h1>
    <div class="btn-group-responsive">
        <a href="{{ route('admin.attendances.manual-entry') }}" class="btn-success">
            <i class="fas fa-plus mr-2"></i>Input Manual
        </a>
        <a href="{{ route('admin.report') }}" class="btn-info">
            <i class="fas fa-chart-bar mr-2"></i>Laporan
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.attendances.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="date" class="form-label-responsive">Tanggal</label>
            <input type="date" class="form-input" id="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}">
        </div>
        <div>
            <label for="status" class="form-label-responsive">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">Semua Status</option>
                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                <option value="sick" {{ request('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                <option value="permit" {{ request('status') == 'permit' ? 'selected' : '' }}>Izin</option>
            </select>
        </div>
        <div>
            <label for="class" class="form-label-responsive">Kelas</label>
            <select class="form-select" id="class" name="class">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="btn-group-responsive md:col-span-1">
            <button type="submit" class="btn-primary">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.attendances.index') }}" class="btn-secondary">
                <i class="fas fa-undo mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Presensi -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary-black">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Waktu Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">NFC ID</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attendances as $index => $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 + ($attendances->currentPage() - 1) * $attendances->perPage() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $attendance->student->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->student->nis }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->student->class }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->entry_time ? $attendance->entry_time->format('H:i') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="badge badge-{{ $attendance->status_color }}">
                            {{ $attendance->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $attendance->nfc_id }}</code>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data presensi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        {{ $attendances->links() }}
    </div>
</div>
@endsection

