@extends('layouts.admin')

@section('title', 'Edit Admin - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-responsive-3xl font-bold text-gray-900">
            <i class="fas fa-user-edit mr-3 text-primary-green"></i>Edit Data Admin
        </h1>
    </div>
    <a href="{{ route('admin.admins.index') }}" class="btn-secondary btn-icon-mobile">
        <i class="fas fa-arrow-left btn-icon"></i>
        <span class="btn-text">Kembali</span>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="p-6">
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
                        
            <!-- Data Wajib -->
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $admin->name) }}" 
                           placeholder="Masukkan nama lengkap admin"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Nama lengkap admin yang akan ditampilkan</p>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           class="form-input @error('email') border-red-500 @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $admin->email) }}" 
                           placeholder="admin@sekolah.com"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Email yang akan digunakan untuk login</p>
                </div>
            </div>

            <!-- Data Login -->
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('username') border-red-500 @enderror" 
                           id="username" 
                           name="username" 
                           value="{{ old('username', $admin->username) }}" 
                           placeholder="Masukkan username"
                           required>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Username unik untuk login</p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password Baru
                    </label>
                    <input type="password" 
                           class="form-input @error('password') border-red-500 @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Kosongkan jika tidak ingin mengubah">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                </div>
            </div>

            <!-- Role dan Kelas -->
            <div class="space-y-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>Role <span class="text-red-500">*</span>
                    </label>
                    <select class="form-select @error('role') border-red-500 @enderror" 
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Super Admin: akses penuh, Wali Kelas: akses terbatas</p>
                </div>
                
                <div>
                    <label for="assigned_class" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2"></i>Kelas yang Dikelola
                    </label>
                    <input type="text" 
                           class="form-input @error('assigned_class') border-red-500 @enderror" 
                           id="assigned_class" 
                           name="assigned_class" 
                           value="{{ old('assigned_class', $admin->assigned_class) }}" 
                           placeholder="Contoh: X IPA 1, XI IPS 2">
                    @error('assigned_class')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Wajib diisi jika role adalah Wali Kelas</p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" 
                       class="h-4 w-4 text-primary-green focus:ring-primary-green border-gray-300 rounded" 
                       id="is_active" 
                       name="is_active" 
                       value="1" 
                       {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="ml-3 block text-sm font-medium text-gray-700">
                    <i class="fas fa-toggle-on mr-2 text-primary-green"></i>Aktifkan Akun
                </label>
            </div>
            <p class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>Akun yang tidak aktif tidak dapat login
            </p>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.admins.index') }}" class="btn-secondary btn-icon-mobile">
                    <i class="fas fa-times btn-icon"></i>
                    <span class="ml-1">Batal</span>
                </a>
                <button type="submit" class="btn-primary btn-icon-mobile">
                    <i class="fas fa-save btn-icon"></i>
                    <span class="ml-1">Update Admin</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Information Card -->
<div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden p-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi Admin
    </h4>
    <div class="mb-4">
        <h6 class="text-primary-green mb-2">Data Saat Ini:</h6>
        <ul class="list-unstyled text-gray-600 space-y-1">
            <li><i class="fas fa-user mr-2"></i><strong>Nama:</strong> {{ $admin->name }}</li>
            <li><i class="fas fa-envelope mr-2"></i><strong>Email:</strong> {{ $admin->email }}</li>
            <li><i class="fas fa-user-tag mr-2"></i><strong>Role:</strong> 
                @if($admin->role === 'super_admin')
                    <span class="badge badge-danger">Super Admin</span>
                @else
                    <span class="badge badge-info">Wali Kelas</span>
                @endif
            </li>
            <li><i class="fas fa-calendar mr-2"></i><strong>Bergabung:</strong> {{ $admin->created_at->format('d M Y') }}</li>
        </ul>
    </div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h6 class="font-semibold text-yellow-800 mb-2">
            <i class="fas fa-exclamation-triangle mr-1"></i>Perhatian
        </h6>
        <p class="text-sm text-yellow-700">Perubahan role akan mempengaruhi akses admin. Pastikan perubahan sudah benar.</p>
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