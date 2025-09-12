@extends('layouts.admin')

@section('title', 'Data Siswa - Sistem Presensi')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
    <h1 class="text-responsive-3xl font-bold text-gray-900">
        <i class="fas fa-users mr-2 sm:mr-3 text-primary-green"></i>Data Siswa
    </h1>
    <div class="btn-group-responsive">
        <a href="{{ route('admin.students.template') }}" class="btn-secondary">
            <i class="fas fa-download mr-2"></i>Template CSV
        </a>
        <a href="{{ route('admin.students.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Siswa
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-4 sm:p-6 mb-4 sm:mb-6">
    <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="search" class="form-label-responsive">Cari</label>
            <input type="text" class="form-input" id="search" name="search" 
                   value="{{ request('search') }}" placeholder="Nama, NIS, atau NFC ID">
        </div>
        <div>
            <label for="class" class="form-label-responsive">Kelas</label>
            <select class="form-select" id="class" name="class">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="btn-group-responsive md:col-span-2">
            <button type="submit" class="btn-primary">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <a href="{{ route('admin.students.index') }}" class="btn-secondary">
                <i class="fas fa-undo mr-2"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Siswa -->
<div class="bg-white rounded-2xl shadow-l overflow-hidden p-4 sm:p-6">
    <div class="table-responsive">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-primary-black">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider mobile-hidden">NIS</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider mobile-hidden">NFC ID</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider mobile-hidden">Kelas</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider mobile-hidden">Gender</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $index => $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 + ($students->currentPage() - 1) * $students->perPage() }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 mobile-hidden">{{ $student->nis }}</td>
                    <td class="px-3 sm:px-6 py-4 text-sm text-gray-900">
                        <div class="mobile-hidden">{{ $student->name }}</div>
                        <div class="mobile-only">
                            <div class="font-medium">{{ $student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->nis }} • {{ $student->class }}</div>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 mobile-hidden">
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $student->nfc_id }}</code>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 mobile-hidden">{{ $student->class }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 mobile-hidden">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        @if($student->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                            <a href="{{ route('admin.students.edit', $student) }}" 
                               class="btn-info text-xs px-2 sm:px-3 py-1 text-center">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.students.toggle-status', $student) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-{{ $student->is_active ? 'warning' : 'success' }} text-xs px-2 sm:px-3 py-1 w-full sm:w-auto"
                                        onclick="return confirm('{{ $student->is_active ? 'Nonaktifkan' : 'Aktifkan' }} siswa ini?')">
                                    <i class="fas fa-{{ $student->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger text-xs px-3 py-1"
                                        onclick="return confirm('Hapus siswa ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data siswa</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        {{ $students->links() }}
    </div>
    </div>

    <!-- Kartu Import di bawah -->
    <div class="bg-white rounded-2xl shadow-l overflow-hidden p-6 mt-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-import mr-2 text-primary-green"></i>Import Data Siswa
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File (xlsx, xls, csv)</label>
                <input class="form-input" type="file" id="file" name="file" accept=".xlsx,.xls,.csv,.txt" required>
                <p class="mt-1 text-sm text-gray-500">Gunakan <a href="{{ route('admin.students.template') }}" class="text-primary-green underline">template CSV</a> dengan header: <code>nis,name,class,nfc_id,gender</code></p>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-success">
                    <i class="fas fa-upload mr-2"></i>Mulai Import
                </button>
            </div>
        </form>
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>Catatan:</strong> Baris dengan NIS atau NFC ID yang sudah ada akan diperbarui otomatis. Baris yang tidak valid akan dilewati.
            </p>
        </div>
    </div>
@endsection