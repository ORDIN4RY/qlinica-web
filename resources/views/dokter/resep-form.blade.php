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
          <div class="relative">
            <input type="hidden" name="obat_id[]" class="obat-id" required>
            <input type="text"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white obat-search focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   placeholder="Ketik nama obat..."
                   oninput="searchObat(this)"
                   onfocus="searchObat(this)"
                   autocomplete="off">
            <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg obat-dropdown divide-y divide-gray-100">
            </div>
          </div>
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
          <div class="aturan-pakai-container">
            <select name="aturan_pakai[]" class="aturan-pakai-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" onchange="toggleAturanPakaiCustom(this)">
              <option value="Sesudah makan">Sesudah makan</option>
              <option value="Sebelum makan">Sebelum makan</option>
              <option value="Bersama makan">Bersama makan</option>
              <option value="Sebelum tidur">Sebelum tidur</option>
              <option value="custom">Lainnya (Ketik Manual)...</option>
            </select>
            <input type="text" class="aturan-pakai-custom hidden mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" placeholder="Tulis aturan pakai sendiri...">
          </div>
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
  const obatData = @json($obats->map(function($o) {
    return [
      'id' => $o->id,
      'nama' => $o->nama,
      'stok' => $o->stok,
      'is_fornas' => $o->is_fornas
    ];
  }));

  function searchObat(input) {
    const query = input.value.toLowerCase().trim();
    const dropdown = input.nextElementSibling;
    
    dropdown.innerHTML = '';
    
    if (!query) {
      dropdown.classList.add('hidden');
      const item = input.closest('.grid');
      const hiddenId = item.querySelector('.obat-id');
      if (hiddenId) hiddenId.value = '';
      return;
    }
    
    const matches = [];
    for (let i = 0; i < obatData.length; i++) {
      const item = obatData[i];
      if (item.nama.toLowerCase().includes(query)) {
        matches.push(item);
        if (matches.length >= 10) break;
      }
    }
    
    if (matches.length === 0) {
      dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada obat cocok</div>';
      dropdown.classList.remove('hidden');
      return;
    }
    
    matches.forEach(item => {
      const option = document.createElement('div');
      option.className = 'px-4 py-2 hover:bg-blue-50 text-sm text-gray-700 cursor-pointer flex justify-between items-center';
      
      let fornasBadge = item.is_fornas ? `<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase ml-2 border border-green-200" title="Ditanggung BPJS">Fornas</span>` : '';
      let stokBadge = `<span class="text-xs text-gray-400">Stok: ${item.stok}</span>`;
      
      option.innerHTML = `
        <div class="flex items-center"><span>${item.nama}</span>${fornasBadge}</div>
        ${stokBadge}
      `;
      
      option.onclick = function() {
        const hiddenId = input.previousElementSibling;
        if (hiddenId) hiddenId.value = item.id;
        input.value = item.nama;
        dropdown.classList.add('hidden');
      };
      dropdown.appendChild(option);
    });
    
    dropdown.classList.remove('hidden');
  }

  document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('obat-search') && !e.target.closest('.obat-dropdown')) {
      document.querySelectorAll('.obat-dropdown').forEach(dropdown => {
        dropdown.classList.add('hidden');
      });
    }
  });

  function toggleAturanPakaiCustom(select) {
    const container = select.closest('.aturan-pakai-container');
    const customInput = container.querySelector('.aturan-pakai-custom');
    
    if (select.value === 'custom') {
      select.removeAttribute('name');
      customInput.setAttribute('name', 'aturan_pakai[]');
      customInput.classList.remove('hidden');
      customInput.focus();
    } else {
      select.setAttribute('name', 'aturan_pakai[]');
      customInput.removeAttribute('name');
      customInput.classList.add('hidden');
      customInput.value = '';
    }
  }

  const obatRows = document.getElementById('obat-rows');
  document.getElementById('add-row').addEventListener('click', function () {
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-1 lg:grid-cols-5 gap-3 items-end border rounded-xl p-4 bg-slate-50';
    newRow.innerHTML = `
      <div class="lg:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Obat</label>
        <div class="relative">
          <input type="hidden" name="obat_id[]" class="obat-id" required>
          <input type="text"
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white obat-search focus:ring-2 focus:ring-blue-500 focus:outline-none"
                 placeholder="Ketik nama obat..."
                 oninput="searchObat(this)"
                 onfocus="searchObat(this)"
                 autocomplete="off">
          <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg obat-dropdown divide-y divide-gray-100">
          </div>
        </div>
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
        <div class="aturan-pakai-container">
          <select name="aturan_pakai[]" class="aturan-pakai-select w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" onchange="toggleAturanPakaiCustom(this)">
            <option value="Sesudah makan">Sesudah makan</option>
            <option value="Sebelum makan">Sebelum makan</option>
            <option value="Bersama makan">Bersama makan</option>
            <option value="Sebelum tidur">Sebelum tidur</option>
            <option value="custom">Lainnya (Ketik Manual)...</option>
          </select>
          <input type="text" class="aturan-pakai-custom hidden mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" placeholder="Tulis aturan pakai sendiri...">
        </div>
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
