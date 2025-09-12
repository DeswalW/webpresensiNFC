@extends('layouts.admin')

@section('title', 'Detail Admin - Sistem Presensi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user me-2"></i>Detail Admin
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0 mt-8">
        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning ms-2">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow p-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Informasi Admin</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <p class="form-control-plaintext">{{ $admin->name }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext">{{ $admin->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $admin->role === 'super_admin' ? 'danger' : ($admin->role === 'admin' ? 'primary' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas yang Dikelola</label>
                            <p class="form-control-plaintext">{{ $admin->assigned_class ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $admin->is_active ? 'success' : 'secondary' }}">
                                    {{ $admin->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Terakhir Login</label>
                            <p class="form-control-plaintext">
                                {{ $admin->last_login_at ? $admin->last_login_at->format('d F Y H:i') : 'Belum pernah login' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mt-4">
        <div class="card shadow p-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Informasi Sistem</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Dibuat:</strong><br>
                    <small class="text-muted">{{ $admin->created_at->format('d F Y H:i') }}</small>
                </div>
                
                <div class="mb-3">
                    <strong>Terakhir Diupdate:</strong><br>
                    <small class="text-muted">{{ $admin->updated_at->format('d F Y H:i') }}</small>
                </div>
                
                @if($admin->id !== auth()->guard('admin')->id())
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.admins.toggle-status', $admin) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $admin->is_active ? 'secondary' : 'success' }} w-100" 
                                onclick="return confirm('Yakin ingin {{ $admin->is_active ? 'menonaktifkan' : 'mengaktifkan' }} admin ini?')">
                            <i class="fas fa-{{ $admin->is_active ? 'ban' : 'check' }} me-1"></i>
                            {{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('Yakin ingin menghapus admin ini?')">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

