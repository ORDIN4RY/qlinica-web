@extends('layouts.app')

@section('title', 'Buat Resep')
@section('page-title', 'Buat Resep')
@section('page-subtitle', 'Kirim resep pasien ke apoteker')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
  <div class="mb-6">
    <p class="text-sm text-gray-500">Pasien</p>
    <h2 class="text-xl font-semibold text-gray-800">{{ $rekamMedis->pasien?->nama ?? 'Pasien tidak ditemukan' }}</h2>
    <p class="text-sm text-gray-600">No. RM: {{ $rekamMedis->pasien?->no_rm ?? '-' }}</p>
    <p class="text-sm text-gray-600">Tanggal pemeriksaan: {{ optional($rekamMedis->tanggal_periksa)?->isoFormat('D MMMM YYYY HH:mm') }}</p>
  </div>

  <form action="{{ route('dokter.resep.store', $rekamMedis) }}" method="POST" id="resep-form">
    @csrf
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Resep / Instruksi Dokter</label>
      <textarea name="catatan_dokter" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-600" placeholder="Contoh: Minum obat setelah makan, istirahat cukup...">{{ old('catatan_dokter', $rekamMedis->catatan) }}</textarea>
    </div>

    <div class="space-y-4" id="obat-rows">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 items-end border rounded-xl p-4 bg-slate-50">
        <div class="lg:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Obat</label>
          <select name="obat_id[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
            <option value="">Pilih obat</option>
            @foreach($obats as $obat)
              <option value="{{ $obat->id }}">{{ $obat->nama }} ({{ $obat->stok }} stok)</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
          <input name="jumlah[]" type="number" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Dosis</label>
          <input name="dosis[]" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: 3x1 setelah makan" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Aturan Pakai</label>
          <input name="aturan_pakai[]" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: Setelah makan" />
        </div>
        <div class="flex gap-2">
          <button type="button" id="add-row" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">Tambah Obat</button>
        </div>
      </div>
    </div>

    <div class="mt-6 flex gap-3 flex-col sm:flex-row">
      <button type="submit" class="px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Kirim Resep</button>
      <a href="{{ route('dokter.resep.index') }}" class="px-5 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Kembali</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const obatRows = document.getElementById('obat-rows');
  document.getElementById('add-row').addEventListener('click', function () {
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-1 lg:grid-cols-5 gap-3 items-end border rounded-xl p-4 bg-slate-50';
    newRow.innerHTML = `
      <div class="lg:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Obat</label>
        <select name="obat_id[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
          <option value="">Pilih obat</option>
          @foreach($obats as $obat)
            <option value="{{ $obat->id }}">{{ $obat->nama }} ({{ $obat->stok }} stok)</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
        <input name="jumlah[]" type="number" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Dosis</label>
        <input name="dosis[]" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: 3x1 setelah makan" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Aturan Pakai</label>
        <input name="aturan_pakai[]" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Contoh: Setelah makan" />
      </div>
      <div class="flex items-center justify-end gap-2">
        <button type="button" class="remove-row px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">Hapus</button>
      </div>`;

    obatRows.appendChild(newRow);
  });

  obatRows.addEventListener('click', function (event) {
    if (event.target.matches('.remove-row')) {
      event.target.closest('.grid').remove();
    }
  });
</script>
@endpush
@endsection
