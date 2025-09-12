@extends('layouts.admin')

@section('title', 'Edit Admin - Sistem Presensi')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-user-edit me-2 text-success"></i>Edit Data Admin
            </h1>
            <p class="text-muted mb-0">Ubah data admin: {{ $admin->name }}</p>
        </div>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gray-800">
                        <i class="fas fa-user-cog me-2 text-success"></i>Informasi Admin
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Data Wajib -->
                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-asterisk me-1"></i>Data Wajib
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label fw-semibold">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $admin->name) }}" 
                                               placeholder="Masukkan nama lengkap admin"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Nama lengkap admin yang akan ditampilkan
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label fw-semibold">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control form-control-lg @error('email') is-invalid @enderror"
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $admin->email) }}" 
                                               placeholder="admin@sekolah.com"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-envelope me-1"></i>Email yang akan digunakan untuk login
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Login -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username" class="form-label fw-semibold">
                                            Username <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('username') is-invalid @enderror"
                                               id="username" 
                                               name="username" 
                                               value="{{ old('username', $admin->username) }}" 
                                               placeholder="Masukkan username"
                                               required>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-user me-1"></i>Username unik untuk login
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-label fw-semibold">
                                            Password Baru
                                        </label>
                                        <input type="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                                               id="password" 
                                               name="password" 
                                               placeholder="Kosongkan jika tidak ingin mengubah">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lock me-1"></i>Kosongkan jika tidak ingin mengubah password
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role dan Kelas -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-user-tag me-1"></i>Role & Akses
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role" class="form-label fw-semibold">
                                            Role <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg @error('role') is-invalid @enderror" 
                                                id="role" 
                                                name="role" 
                                                required>
                                            <option value="">Pilih Role</option>
                                            <option value="super_admin" {{ old('role', $admin->role) == 'super_admin' ? 'selected' : '' }}>
                                                Super Admin
                                            </option>
                                            <option value="wali_kelas" {{ old('role', $admin->role) == 'wali_kelas' ? 'selected' : '' }}>
                                                Wali Kelas
                                            </option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>Super Admin: akses penuh, Wali Kelas: akses terbatas
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="assigned_class" class="form-label fw-semibold">
                                            Kelas yang Dikelola
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('assigned_class') is-invalid @enderror"
                                               id="assigned_class" 
                                               name="assigned_class" 
                                               value="{{ old('assigned_class', $admin->assigned_class) }}" 
                                               placeholder="Contoh: X IPA 1, XI IPS 2">
                                        @error('assigned_class')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-graduation-cap me-1"></i>Wajib diisi jika role adalah Wali Kelas
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="fas fa-toggle-on me-1 text-success"></i>Aktifkan Akun
                                </label>
                                <small class="form-text text-muted d-block">
                                    <i class="fas fa-info-circle me-1"></i>Akun yang tidak aktif tidak dapat login
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-4">
                                <i class="fas fa-save me-1"></i>Update Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Information Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h6 class="card-title mb-0 text-gray-800">
                        <i class="fas fa-info-circle me-2 text-info"></i>Informasi Admin
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <h6 class="text-success mb-2">Data Saat Ini:</h6>
                        <ul class="list-unstyled text-muted small">
                            <li><i class="fas fa-user me-2"></i><strong>Nama:</strong> {{ $admin->name }}</li>
                            <li><i class="fas fa-envelope me-2"></i><strong>Email:</strong> {{ $admin->email }}</li>
                            <li><i class="fas fa-user-tag me-2"></i><strong>Role:</strong> 
                                @if($admin->role === 'super_admin')
                                    <span class="badge bg-danger">Super Admin</span>
                                @else
                                    <span class="badge bg-info">Wali Kelas</span>
                                @endif
                            </li>
                            <li><i class="fas fa-calendar me-2"></i><strong>Bergabung:</strong> {{ $admin->created_at->format('d M Y') }}</li>
                        </ul>
                    </div>
                    <hr>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-1"></i>Perhatian
                        </h6>
                        <p class="mb-0 small">Perubahan role akan mempengaruhi akses admin. Pastikan perubahan sudah benar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const assignedClassField = document.getElementById('assigned_class');
    const assignedClassLabel = assignedClassField.previousElementSibling;
    
    if (this.value === 'wali_kelas') {
        assignedClassField.required = true;
        assignedClassLabel.innerHTML = 'Kelas yang Dikelola <span class="text-danger">*</span>';
    } else {
        assignedClassField.required = false;
        assignedClassLabel.innerHTML = 'Kelas yang Dikelola';
    }
});

// Set initial state
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const assignedClassField = document.getElementById('assigned_class');
    const assignedClassLabel = assignedClassField.previousElementSibling;
    
    if (roleSelect.value === 'wali_kelas') {
        assignedClassField.required = true;
        assignedClassLabel.innerHTML = 'Kelas yang Dikelola <span class="text-danger">*</span>';
    } else {
        assignedClassField.required = false;
        assignedClassLabel.innerHTML = 'Kelas yang Dikelola';
    }
});
</script>
@endsection