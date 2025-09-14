@extends('layouts.admin')

@section('title', 'Tambah Siswa - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-user-plus mr-3 text-primary-green"></i>Tambah Siswa
    </h1>
    <a href="{{ route('admin.students.index') }}" class="btn-secondary btn-icon-mobile">
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
        
        <form method="POST" action="{{ route('admin.students.store') }}" class="gap-6">
            @csrf
            
            <!-- Data Dasar -->
            <div class="space-y-4">
                <div>
                    <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2"></i>NIS <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('nis') border-red-500 @enderror" 
                           id="nis" 
                           name="nis" 
                           value="{{ old('nis') }}" 
                           placeholder="Masukkan NIS siswa"
                           required>
                    @error('nis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Nomor Induk Siswa yang unik</p>
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Masukkan nama lengkap siswa"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="nfc_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2"></i>NFC ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('nfc_id') border-red-500 @enderror" 
                           id="nfc_id" 
                           name="nfc_id" 
                           value="{{ old('nfc_id') }}" 
                           placeholder="Masukkan ID kartu NFC"
                           required>
                    @error('nfc_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">ID unik dari kartu NFC untuk presensi</p>
                </div>
                
                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2"></i>Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('class') border-red-500 @enderror" 
                           id="class" 
                           name="class" 
                           value="{{ old('class') }}" 
                           placeholder="Contoh: X IPA 1, XI IPS 2"
                           required>
                    @error('class')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Kelas siswa (contoh: X IPA 1, XI IPS 2, XII IPA 1)</p>
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2"></i>Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select class="form-select @error('gender') border-red-500 @enderror" 
                            id="gender" 
                            name="gender" 
                            required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.students.index') }}" class="btn-secondary btn-icon-mobile">
                    <i class="fas fa-times btn-icon"></i>
                    <span class="ml-1">Batal</span>
                </a>
                <button type="submit" class="btn-primary btn-icon-mobile">
                    <i class="fas fa-save btn-icon"></i>
                    <span class="ml-1">Simpan Siswa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Info -->
<div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden p-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi
    </h4>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h5 class="font-semibold text-blue-700 mb-2">
            <i class="fas fa-lightbulb mr-2"></i>Tips
        </h5>
        <p class="text-sm text-blue-600">Pastikan NFC ID unik dan sesuai dengan kartu yang akan digunakan untuk presensi. NIS juga harus unik dalam sistem.</p>
    </div>
</div>
@endsection