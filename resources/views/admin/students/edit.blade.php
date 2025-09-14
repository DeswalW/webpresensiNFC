@extends('layouts.admin')

@section('title', 'Edit Siswa - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-user-edit mr-3 text-primary-green"></i>Edit Data Siswa
        </h1>
        <p class="text-gray-600 mt-1">Ubah data siswa: {{ $student->name }}</p>
    </div>
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
        
        <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
                        
            <!-- Data Wajib -->
            <div class="space-y-4">
                <div>
                    <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card mr-2"></i>NIS <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('nis') border-red-500 @enderror" 
                           id="nis" 
                           name="nis" 
                           value="{{ old('nis', $student->nis) }}" 
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
                           value="{{ old('name', $student->name) }}" 
                           placeholder="Masukkan nama lengkap siswa"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Data NFC, Kelas, dan Gender -->
            <div class="space-y-4">
                <div>
                    <label for="nfc_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-credit-card mr-2"></i>NFC ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('nfc_id') border-red-500 @enderror" 
                           id="nfc_id" 
                           name="nfc_id" 
                           value="{{ old('nfc_id', $student->nfc_id) }}" 
                           placeholder="Masukkan ID kartu NFC"
                           required>
                    @error('nfc_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">ID unik dari kartu NFC yang akan digunakan untuk presensi</p>
                </div>
                
                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-graduation-cap mr-2"></i>Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="form-input @error('class') border-red-500 @enderror" 
                           id="class" 
                           name="class" 
                           value="{{ old('class', $student->class) }}" 
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
                        <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.students.index') }}" class="btn-secondary btn-icon-mobile">
                    <i class="fas fa-times btn-icon"></i>
                    <span class="btn-text">Batal</span>
                </a>
                <button type="submit" class="btn-primary btn-icon-mobile">
                    <i class="fas fa-save btn-icon"></i>
                    <span class="btn-text">Update Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Information Card -->
<div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden p-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi
    </h4>
    
    <div class="mb-4">
        <h6 class="text-primary-green mb-2">Data Wajib:</h6>
        <ul class="list-unstyled text-gray-600">
            <li><i class="fas fa-check text-primary-green mr-2"></i>NIS, Nama, NFC ID, Kelas, dan Jenis Kelamin</li>
        </ul>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h6 class="font-semibold text-yellow-800 mb-2">
            <i class="fas fa-exclamation-triangle mr-1"></i>Perhatian
        </h6>
        <p class="text-sm text-yellow-700">Perubahan NFC ID akan mempengaruhi sistem presensi. Pastikan kartu NFC sudah terdaftar dengan benar.</p>
    </div>
</div>
@endsection