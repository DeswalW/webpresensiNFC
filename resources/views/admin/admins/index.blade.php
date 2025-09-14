@extends('layouts.admin')

@section('title', 'Manajemen Admin - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-user-shield mr-3 text-primary-green"></i>Manajemen Admin
    </h1>
    <a href="{{ route('admin.admins.create') }}" class="btn-primary btn-icon-mobile">
        <i class="fas fa-plus btn-icon"></i>
        <span class="btn-text">Tambah Admin</span>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary-black">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kelas yang Dikelola</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($admins as $index => $admin)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admins->firstItem() + $index }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $admin->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $admin->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="badge {{ $admin->role === 'super_admin' ? 'bg-red-100 text-red-800' : ($admin->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $admin->assigned_class ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $admin->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.admins.show', $admin) }}" class="btn-info btn-action-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn-warning btn-action-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($admin->id !== auth()->guard('admin')->id())
                            <form method="POST" action="{{ route('admin.admins.toggle-status', $admin) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-{{ $admin->is_active ? 'secondary' : 'success' }} btn-action-sm" 
                                        onclick="return confirm('Yakin ingin {{ $admin->is_active ? 'menonaktifkan' : 'mengaktifkan' }} admin ini?')">
                                    <i class="fas fa-{{ $admin->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-action-sm" 
                                        onclick="return confirm('Yakin ingin menghapus admin ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data admin</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="flex justify-center mt-6">
        {{ $admins->links() }}
    </div>
</div>
@endsection
