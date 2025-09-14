@extends('layouts.admin')

@section('title', 'Input Manual Presensi - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-edit mr-3 text-primary-green"></i>Input Manual Presensi
    </h1>
    <a href="{{ route('admin.attendances.index') }}" class="btn-secondary btn-icon-mobile">
        <i class="fas fa-arrow-left"></i>
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
        
        <form method="POST" action="{{ route('admin.attendances.manual-entry') }}" class="gap-y-4">
            @csrf
            
            <!-- Data Presensi -->
            <div class="space-y-4">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Siswa <span class="text-red-500">*</span>
                    </label>
                    <select class="form-select @error('student_id') border-red-500 @enderror" 
                            id="student_id" 
                            name="student_id" 
                            required>
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->class }}) - NIS: {{ $student->nis }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Ketik untuk mencari siswa berdasarkan nama, kelas, atau NIS</p>
                </div>
                
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           class="form-input @error('date') border-red-500 @enderror" 
                           id="date" 
                           name="date" 
                           value="{{ old('date', now()->format('Y-m-d')) }}" 
                           required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="entry_time" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock mr-2"></i>Waktu Masuk <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           class="form-input @error('entry_time') border-red-500 @enderror" 
                           id="entry_time" 
                           name="entry_time" 
                           value="{{ old('entry_time', now()->format('H:i')) }}" 
                           required>
                    @error('entry_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>Status <span class="text-red-500">*</span>
                    </label>
                    <select class="form-select @error('status') border-red-500 @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="">Pilih Status</option>
                        <option value="present" {{ old('status') === 'present' ? 'selected' : '' }}>
                            Hadir
                        </option>
                        <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>
                            Terlambat
                        </option>
                        <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>
                            Tidak Hadir
                        </option>
                        <option value="sick" {{ old('status') === 'sick' ? 'selected' : '' }}>
                            Sakit
                        </option>
                        <option value="permit" {{ old('status') === 'permit' ? 'selected' : '' }}>
                            Izin
                        </option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2"></i>Catatan
                    </label>
                    <textarea class="form-input @error('notes') border-red-500 @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3" 
                              placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.attendances.index') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Simpan Presensi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Info Status -->
<div class="mt-6 bg-white rounded-2xl shadow-lg overflow-hidden p-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi Status
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-3">
            <div class="flex items-center">
                <span class="badge badge-success mr-3">Hadir</span>
                <span class="text-sm text-gray-600">Siswa hadir tepat waktu</span>
            </div>
            <div class="flex items-center">
                <span class="badge badge-warning mr-3">Terlambat</span>
                <span class="text-sm text-gray-600">Siswa hadir tapi terlambat</span>
            </div>
            <div class="flex items-center">
                <span class="badge badge-danger mr-3">Tidak Hadir</span>
                <span class="text-sm text-gray-600">Siswa tidak hadir tanpa keterangan</span>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center">
                <span class="badge badge-info mr-3">Sakit</span>
                <span class="text-sm text-gray-600">Siswa tidak hadir karena sakit</span>
            </div>
            <div class="flex items-center">
                <span class="badge badge-secondary mr-3">Izin</span>
                <span class="text-sm text-gray-600">Siswa tidak hadir dengan izin</span>
            </div>
        </div>
    </div>
    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-yellow-700">
            <i class="fas fa-info-circle mr-2"></i>
            Jika sudah ada presensi untuk siswa dan tanggal yang sama, data akan diupdate.
        </p>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #289341;
        box-shadow: 0 0 0 1px #289341;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#student_id').select2({
        placeholder: 'Pilih Siswa',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada siswa ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });
});
</script>
@endsection