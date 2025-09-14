@extends('layouts.admin')

@section('title', 'Detail Admin - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-user mr-3 text-primary-green"></i>Detail Admin
    </h1>
    <div class="flex space-x-3">
        <a href="{{ route('admin.admins.index') }}" class="btn-secondary btn-icon-mobile">
            <i class="fas fa-arrow-left btn-icon"></i>
            <span class="btn-text">Kembali</span>
        </a>
        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn-warning btn-icon-mobile">
            <i class="fas fa-edit btn-icon"></i>
            <span class="btn-text">Edit</span>
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-user mr-2 text-primary-green"></i>Informasi Admin
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <p class="text-gray-900 font-medium">{{ $admin->name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <p class="text-gray-900 font-medium">{{ $admin->email }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <p>
                    <span class="badge {{ $admin->role === 'super_admin' ? 'badge-danger' : 'badge-info' }}">
                        {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                    </span>
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas yang Dikelola</label>
                <p class="text-gray-900 font-medium">{{ $admin->assigned_class ?? '-' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <p>
                    <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-secondary' }}">
                        {{ $admin->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Login</label>
                <p class="text-gray-900 font-medium">
                    {{ $admin->last_login_at ? $admin->last_login_at->format('d F Y H:i') : 'Belum pernah login' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Sistem -->
<div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden p-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi Sistem
    </h4>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat</label>
            <p class="text-gray-900 font-medium">{{ $admin->created_at->format('d F Y H:i') }}</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Diupdate</label>
            <p class="text-gray-900 font-medium">{{ $admin->updated_at->format('d F Y H:i') }}</p>
        </div>
    </div>
    
    @if($admin->id !== auth()->guard('admin')->id())
    <div class="flex space-x-3 pt-4 border-t border-gray-200">
        <form method="POST" action="{{ route('admin.admins.toggle-status', $admin) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-{{ $admin->is_active ? 'secondary' : 'success' }} btn-icon-mobile" 
                    onclick="return confirm('Yakin ingin {{ $admin->is_active ? 'menonaktifkan' : 'mengaktifkan' }} admin ini?')">
                <i class="fas fa-{{ $admin->is_active ? 'ban' : 'check' }} btn-icon"></i>
                <span class="btn-text">{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
            </button>
        </form>
        
        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger btn-icon-mobile" 
                    onclick="return confirm('Yakin ingin menghapus admin ini?')">
                <i class="fas fa-trash btn-icon"></i>
                <span class="btn-text">Hapus</span>
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

