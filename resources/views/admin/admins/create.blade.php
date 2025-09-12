@extends('layouts.admin')

@section('title', 'Tambah Admin - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-user-plus mr-3 text-primary-green"></i>Tambah Admin Baru
    </h1>
    <a href="{{ route('admin.admins.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Kembali
    </a>
</div>

<div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">          
            <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-4">
                @csrf
                
                <!-- Data Dasar -->
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="form-input @error('name') border-red-500 @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="Masukkan nama lengkap"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               class="form-input @error('email') border-red-500 @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="admin@sekolah.com"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2"></i>Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="form-input @error('username') border-red-500 @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan username"
                               required>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2"></i>Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               class="form-input @error('password') border-red-500 @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Minimal 8 karakter"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Role -->
                <div class="">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>Role <span class="text-red-500">*</span>
                    </label>
                    <select class="form-select @error('role') border-red-500 @enderror" 
                            id="role" 
                            name="role" 
                            required>
                        <option value="">Pilih Role</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>
                            Super Admin - Akses Penuh
                        </option>
                        <option value="wali_kelas" {{ old('role') == 'wali_kelas' ? 'selected' : '' }}>
                            Wali Kelas - Akses Terbatas
                        </option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kelas (jika wali kelas) -->
                <div id="classField" style="display: none;" class="">
                    <label for="assigned_class" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2"></i>Kelas yang Dikelola <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('assigned_class') border-red-500 @enderror" 
                           id="assigned_class" 
                           name="assigned_class" 
                           value="{{ old('assigned_class') }}" 
                           placeholder="Contoh: X IPA 1, XI IPS 2">
                    @error('assigned_class')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Wajib diisi untuk Wali Kelas</p>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           class="h-4 w-4 text-primary-green focus:ring-primary-green border-gray-300 rounded" 
                           id="is_active" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-3 block text-sm font-medium text-gray-700">
                        <i class="fas fa-toggle-on mr-2 text-primary-green"></i>Aktifkan Akun
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.admins.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Admin
                    </button>
                </div>
            </form>
</div>

<!-- Info Role -->
<div class="mt-6 bg-white rounded-2xl shadow-l overflow-hidden p-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi Role
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h5 class="font-semibold text-red-700 mb-2">
                    <i class="fas fa-crown mr-2"></i>Super Admin
                </h5>
                <ul class="text-sm text-red-600 space-y-1">
                    <li>• Akses ke semua menu</li>
                    <li>• Kelola admin lain</li>
                    <li>• Lihat semua data</li>
                </ul>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h5 class="font-semibold text-blue-700 mb-2">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>Wali Kelas
                </h5>
                <ul class="text-sm text-blue-600 space-y-1">
                    <li>• Akses terbatas sesuai kelas</li>
                    <li>• Kelola siswa di kelasnya</li>
                    <li>• Lihat presensi kelasnya</li>
                </ul>
            </div>
        </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('role').addEventListener('change', function() {
    const classField = document.getElementById('classField');
    const assignedClassInput = document.getElementById('assigned_class');
    
    if (this.value === 'wali_kelas') {
        classField.style.display = 'block';
        assignedClassInput.required = true;
    } else {
        classField.style.display = 'none';
        assignedClassInput.required = false;
        assignedClassInput.value = '';
    }
});

// Trigger on page load if role is already selected
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect.value === 'wali_kelas') {
        document.getElementById('classField').style.display = 'block';
        document.getElementById('assigned_class').required = true;
    }
});
</script>
@endsection