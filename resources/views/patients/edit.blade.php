@extends('layouts.app')

@section('title', 'Edit Pasien')
@section('page-title', 'Edit Pasien')
@section('page-subtitle', 'Perbarui data pasien: ' . $patient->name)

@section('content')

<div class="max-w-3xl">
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
      <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
        <i class="fas fa-pen text-amber-600"></i>
      </div>
      <h3 class="font-semibold text-gray-800">Edit Data: <span class="text-blue-900">{{ $patient->name }}</span></h3>
    </div>

    <form method="POST" action="{{ route('patients.update', $patient) }}" class="p-6 space-y-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- Nama --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="{{ old('name', $patient->name) }}" required
            class="w-full px-4 py-2.5 border @error('name') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm">
          @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- NIK --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
          <input type="text" name="nik" value="{{ old('nik', $patient->nik) }}" required maxlength="16"
            class="w-full px-4 py-2.5 border @error('nik') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm font-mono">
          @error('nik')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Usia --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Usia <span class="text-red-500">*</span></label>
          <input type="number" name="age" value="{{ old('age', $patient->age) }}" required min="0" max="150"
            class="w-full px-4 py-2.5 border @error('age') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm">
          @error('age')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Gender --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
          <select name="gender" required
            class="w-full px-4 py-2.5 border @error('gender') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm bg-white">
            <option value="L" {{ old('gender', $patient->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('gender', $patient->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
          </select>
          @error('gender')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- No. Telp --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
          <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" required
            class="w-full px-4 py-2.5 border @error('phone') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm">
          @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Penyakit --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Penyakit / Diagnosis <span class="text-red-500">*</span></label>
          <input type="text" name="disease" value="{{ old('disease', $patient->disease) }}" required
            class="w-full px-4 py-2.5 border @error('disease') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm">
          @error('disease')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Tanggal kunjungan --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan <span class="text-red-500">*</span></label>
          <input type="date" name="visit_date" value="{{ old('visit_date', $patient->visit_date->format('Y-m-d')) }}" required
            class="w-full px-4 py-2.5 border @error('visit_date') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm">
          @error('visit_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Alamat --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
          <textarea name="address" required rows="2"
            class="w-full px-4 py-2.5 border @error('address') border-red-400 @else border-gray-200 @enderror rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm resize-none">{{ old('address', $patient->address) }}</textarea>
          @error('address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Catatan --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
          <textarea name="notes" rows="2"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 text-sm resize-none">{{ old('notes', $patient->notes) }}</textarea>
        </div>

      </div>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit"
          class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl text-sm font-semibold shadow transition">
          <i class="fas fa-save mr-1.5"></i> Simpan Perubahan
        </button>
        <a href="{{ route('patients.index') }}"
           class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

@endsection
