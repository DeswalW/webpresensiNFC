@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-clipboard-list mr-3 text-primary-green"></i>Log Aktivitas
    </h1>
</div>

<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NFC ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ optional($log->event_time)->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->action }}</td>
                        <td class="px-4 py-3 text-sm font-mono">{{ $log->nfc_id ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ optional($log->student)->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection


