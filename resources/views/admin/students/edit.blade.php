@extends('layouts.admin')

@section('title', 'Edit Siswa - Sistem Presensi')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-user-edit me-2 text-success"></i>Edit Data Siswa
            </h1>
            <p class="text-muted mb-0">Ubah data siswa: {{ $student->name }}</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gray-800">
                        <i class="fas fa-user me-2 text-success"></i>Informasi Siswa
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.students.update', $student->id) }}">
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
                                        <label for="nis" class="form-label fw-semibold">
                                            NIS <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('nis') is-invalid @enderror"
                                               id="nis" 
                                               name="nis" 
                                               value="{{ old('nis', $student->nis) }}" 
                                               placeholder="Masukkan NIS siswa"
                                               required>
                                        @error('nis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Nomor Induk Siswa yang unik
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label fw-semibold">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $student->name) }}" 
                                               placeholder="Masukkan nama lengkap siswa"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data NFC, Kelas, dan Gender -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nfc_id" class="form-label fw-semibold">
                                            NFC ID <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('nfc_id') is-invalid @enderror"
                                               id="nfc_id" 
                                               name="nfc_id" 
                                               value="{{ old('nfc_id', $student->nfc_id) }}" 
                                               placeholder="Masukkan ID kartu NFC"
                                               required>
                                        @error('nfc_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-credit-card me-1"></i>ID unik dari kartu NFC yang akan digunakan untuk presensi
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="class" class="form-label fw-semibold">
                                            Kelas <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg @error('class') is-invalid @enderror"
                                               id="class" 
                                               name="class" 
                                               value="{{ old('class', $student->class) }}" 
                                               placeholder="Contoh: X IPA 1, XI IPS 2"
                                               required>
                                        @error('class')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-graduation-cap me-1"></i>Kelas siswa (contoh: X IPA 1, XI IPS 2, XII IPA 1)
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender" class="form-label fw-semibold">
                                            Jenis Kelamin <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control form-control-lg @error('gender') is-invalid @enderror"
                                                id="gender" 
                                                name="gender" 
                                                required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-4">
                                <i class="fas fa-save me-1"></i>Update Data
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
                        <i class="fas fa-info-circle me-2 text-info"></i>Informasi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <h6 class="text-success mb-2">Data Wajib:</h6>
                        <ul class="list-unstyled text-muted">
                            <li><i class="fas fa-check text-success me-2"></i>NIS, Nama, NFC ID, Kelas, dan Jenis Kelamin</li>
                        </ul>
                    </div>
                    <hr>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-1"></i>Perhatian
                        </h6>
                        <p class="mb-0 small">Perubahan NFC ID akan mempengaruhi sistem presensi. Pastikan kartu NFC sudah terdaftar dengan benar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection