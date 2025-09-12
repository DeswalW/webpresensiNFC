@extends('layouts.admin')

@section('title', 'Pengaturan - Sistem Presensi')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-cog mr-3 text-primary-green"></i>Pengaturan Sistem
    </h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clock mr-2 text-primary-green"></i>Pengaturan Jam Presensi
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="entry_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="time" class="form-input @error('entry_time') border-red-500 @enderror" 
                               id="entry_time" name="entry_time" 
                               value="{{ old('entry_time', $settings ? $settings->entry_time->format('H:i') : '07:00') }}" required>
                        @error('entry_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Jam mulai presensi masuk</p>
                    </div>
                    
                    <div>
                        <label for="late_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                            Batas Terlambat <span class="text-red-500">*</span>
                        </label>
                        <input type="time" class="form-input @error('late_threshold') border-red-500 @enderror" 
                               id="late_threshold" name="late_threshold" 
                               value="{{ old('late_threshold', $settings ? $settings->late_threshold->format('H:i') : '07:15') }}" required>
                        @error('late_threshold')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Batas waktu untuk dianggap terlambat</p>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Batas Akhir Presensi
                        </label>
                        <input type="time" class="form-input @error('end_time') border-red-500 @enderror" 
                               id="end_time" name="end_time" 
                               value="{{ old('end_time', $settings && $settings->end_time ? $settings->end_time->format('H:i') : '08:00') }}">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Presensi setelah jam ini akan ditolak otomatis</p>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-l overflow-hidden p-6 mb-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-primary-green"></i>Informasi Pengaturan
                </h3>
            </div>
            <div class="space-y-4">
                <div>
                    <h4 class="font-medium text-gray-900">Jam Masuk</h4>
                    <p class="text-sm text-gray-600">Siswa yang hadir sebelum jam ini akan dianggap hadir tepat waktu.</p>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900">Batas Terlambat</h4>
                    <p class="text-sm text-gray-600">Siswa yang hadir antara jam masuk dan batas terlambat akan dianggap terlambat.</p>
                </div>
                
                <hr class="border-gray-200">
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">Status Saat Ini:</h4>
                    @if($settings)
                        <p class="text-sm text-blue-800 mb-1"><strong>Jam Masuk:</strong> {{ $settings->entry_time->format('H:i') }}</p>
                        <p class="text-sm text-blue-800"><strong>Batas Terlambat:</strong> {{ $settings->late_threshold->format('H:i') }}</p>
                        @if($settings->end_time)
                            <p class="text-sm text-blue-800"><strong>Batas Akhir:</strong> {{ $settings->end_time->format('H:i') }}</p>
                        @endif
                    @else
                        <p class="text-sm text-blue-800">Belum ada pengaturan yang disimpan.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-l overflow-hidden p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>Peringatan
                </h3>
            </div>
            <ul class="space-y-3">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span class="text-sm text-gray-700">Batas terlambat harus lebih besar dari jam masuk</span>
                </li>
                
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                    <span class="text-sm text-gray-700">Pengaturan akan berlaku untuk semua presensi baru</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

