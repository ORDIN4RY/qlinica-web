@extends('layouts.dokter')

@section('title', 'Antrian Pasien')
@section('page-title', 'Antrian Pasien')
@section('page-subtitle', 'Kelola antrian pasien hari ini')

@section('content')
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Antrian</p>
    <p class="text-3xl font-bold text-slate-800">{{ $jumlahAntrian }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Menunggu</p>
    <p class="text-3xl font-bold text-slate-800">{{ $antrians->where('status', 'Menunggu')->count() }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Dipanggil</p>
    <p class="text-3xl font-bold text-slate-800">{{ $Dipanggil }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Selesai</p>
    <p class="text-3xl font-bold text-slate-800">{{ $selesai }}</p>
  </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
  <h2 class="font-semibold text-lg text-gray-800 mb-4">Daftar Antrian Hari Ini</h2>

  @if($antrians->isEmpty())
    <p class="text-sm text-gray-500">Tidak ada antrian hari ini.</p>
  @else
    <div class="space-y-3">
      @foreach($antrians as $antrian)
        @php
          $statusClasses = [
            'Menunggu' => 'bg-gray-100 text-gray-700',
            'Dipanggil' => 'bg-blue-100 text-blue-700',
            'Dilayani' => 'bg-yellow-100 text-yellow-700',
            'Selesai' => 'bg-green-100 text-green-700',
            'Batal' => 'bg-red-100 text-red-700',
          ];
        @endphp

        <div class="border rounded-xl p-4 {{ $antrian->status === 'Dipanggil' ? 'border-blue-300 bg-blue-50' : 'bg-white' }}">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="min-w-0">
              <p class="text-sm text-gray-500">No. Antrian: <span class="font-bold">{{ $antrian->no_antrian }}</span></p>
              <h3 class="font-semibold text-gray-800 truncate">{{ $antrian->pasien?->nama ?? 'Pasien tidak ditemukan' }}</h3>
              <p class="text-sm text-gray-600">No. RM: {{ $antrian->pasien?->no_rm ?? '-' }}</p>
              <p class="text-sm text-gray-600">Keluhan: {{ $antrian->keluhan ?? 'Tidak ada keluhan' }}</p>
            </div>
            <div class="flex flex-col gap-2 items-end">
              <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $statusClasses[$antrian->status] ?? 'bg-gray-100 text-gray-700' }}">{{ $antrian->status }}</span>

              @if($antrian->status === 'Menunggu')
                <form action="{{ route('dokter.antrian.panggil', $antrian) }}" method="POST" class="inline-block">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="Dipanggil">
                  <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Panggil Pasien</button>
                </form>
              @elseif($antrian->status === 'Dipanggil')
                <div class="flex gap-2">
                  <form action="{{ route('dokter.antrian.panggil', $antrian) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Dilayani">
                    <button type="submit" class="px-3 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">Mulai Layani</button>
                  </form>
                  <form action="{{ route('dokter.antrian.panggil', $antrian) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Selesai">
                    <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">Selesai</button>
                  </form>
                </div>
              @elseif($antrian->status === 'Dilayani')
                <form action="{{ route('dokter.antrian.panggil', $antrian) }}" method="POST" class="inline-block">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="Selesai">
                  <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">Selesai Layani</button>
                </form>
              @else
                <span class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">Status: {{ $antrian->status }}</span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<!-- Modal Form Diagnosa -->
@php
  $pasienDilayani = $antrians->where('status', 'Dilayani')->first();
@endphp

@if($pasienDilayani)
<div id="diagnosaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
  <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
    <div class="mt-3">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          Pemeriksaan Pasien: {{ $pasienDilayani->pasien?->nama ?? 'Pasien tidak ditemukan' }}
        </h3>
        <button onclick="closeDiagnosaModal()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form action="{{ route('dokter.antrian.diagnosa', $pasienDilayani->id) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Informasi Pasien -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h4 class="font-semibold text-gray-800 mb-2">Informasi Pasien</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><strong>No. RM:</strong> {{ $pasienDilayani->pasien?->no_rm ?? '-' }}</div>
            <div><strong>Nama:</strong> {{ $pasienDilayani->pasien?->nama ?? '-' }}</div>
            <div><strong>Umur:</strong> {{ $pasienDilayani->pasien?->tanggal_lahir ? \Carbon\Carbon::parse($pasienDilayani->pasien->tanggal_lahir)->age : '-' }} tahun</div>
            <div><strong>Jenis Kelamin:</strong> {{ $pasienDilayani->pasien?->jenis_kelamin ?? '-' }}</div>
            <div><strong>Keluhan:</strong> {{ $pasienDilayani->keluhan ?? 'Tidak ada keluhan' }}</div>
          </div>
        </div>

        <!-- Pemeriksaan Fisik -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Anamnesis</label>
            <textarea name="anamnesis" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Riwayat penyakit, keluhan pasien..."></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pemeriksaan Fisik</label>
            <textarea name="pemeriksaan_fisik" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Hasil pemeriksaan fisik..."></textarea>
          </div>
        </div>

        <!-- Vital Signs -->
        <div>
          <h4 class="font-semibold text-gray-800 mb-3">Tanda Vital</h4>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tekanan Darah</label>
              <input type="text" name="tekanan_darah" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="120/80">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Suhu (°C)</label>
              <input type="number" step="0.1" name="suhu" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="36.5">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg)</label>
              <input type="number" step="0.1" name="berat_badan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="70">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan (cm)</label>
              <input type="number" step="0.1" name="tinggi_badan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="170">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nadi (x/menit)</label>
              <input type="number" name="nadi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="80">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Respirasi (x/menit)</label>
              <input type="number" name="respirasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="20">
            </div>
          </div>
        </div>

        <!-- Diagnosa -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosa <span class="text-red-500">*</span></label>
          <div id="diagnosa-container" class="space-y-2">
            <div class="flex gap-2 items-center diagnosa-item">
              <select name="diagnosa[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 diagnosa-select" required>
                <option value="">Pilih diagnosa...</option>
                @foreach(\App\Models\Icdx::orderBy('kode')->get() as $icdx)
                  <option value="{{ $icdx->id }}">{{ $icdx->kode }} - {{ $icdx->nama }}</option>
                @endforeach
              </select>
              <button type="button" onclick="removeDiagnosa(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 remove-diagnosa-btn hidden">Hapus</button>
            </div>
          </div>
          <button type="button" onclick="addDiagnosa()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tambah Diagnosa</button>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Diagnosa Primer <span class="text-red-500">*</span></label>
          <select name="diagnosa_primer" id="diagnosa-primer" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="">Pilih diagnosa primer...</option>
          </select>
        </div>

        <!-- Tindakan dan Prognosa -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tindakan</label>
            <textarea name="tindakan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tindakan yang dilakukan..."></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Prognosa</label>
            <textarea name="prognosa" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Prognosa pasien..."></textarea>
          </div>
        </div>

        <!-- Keadaan Keluar dan Rujukan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Keadaan Keluar</label>
            <select name="keadaan_keluar" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Pilih keadaan keluar...</option>
              <option value="Sembuh">Sembuh</option>
              <option value="Membaik">Membaik</option>
              <option value="Belum Sembuh">Belum Sembuh</option>
              <option value="Meninggal">Meninggal</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rujukan Ke</label>
            <input type="text" name="rujukan_ke" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="RS/Puskesmas tujuan rujukan...">
          </div>
        </div>

        <!-- Catatan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
          <textarea name="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Catatan tambahan..."></textarea>
        </div>

        <!-- Resep Obat -->
        <div>
          <h4 class="font-semibold text-gray-800 mb-3">Resep Obat</h4>
          <div id="obat-container" class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-4 obat-item">
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nama Obat</label>
                  <select name="obat_id[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih obat...</option>
                    @foreach(\App\Models\Obat::orderBy('nama')->get() as $obat)
                      <option value="{{ $obat->id }}">{{ $obat->nama }} (Stok: {{ $obat->stok }})</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                  <input type="number" name="jumlah[]" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Dosis</label>
                  <input type="text" name="dosis[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="2x1">
                </div>
                <div class="flex gap-2">
                  <button type="button" onclick="removeObat(this)" class="px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 remove-obat-btn hidden">Hapus</button>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Aturan Pakai</label>
                  <input type="text" name="aturan_pakai[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Sesudah makan">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                  <input type="text" name="keterangan[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Keterangan tambahan">
                </div>
              </div>
            </div>
          </div>
          <button type="button" onclick="addObat()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tambah Obat</button>
        </div>

        <!-- Catatan Dokter untuk Resep -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Dokter untuk Apoteker</label>
          <textarea name="catatan_dokter" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Instruksi khusus untuk apoteker..."></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-4 pt-4 border-t">
          <button type="button" onclick="closeDiagnosaModal()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Batal</button>
          <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Simpan Diagnosa & Selesai</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection

<script>
function closeDiagnosaModal() {
  document.getElementById('diagnosaModal').remove();
}

function addDiagnosa() {
  const container = document.getElementById('diagnosa-container');
  const items = container.querySelectorAll('.diagnosa-item');
  const newItem = items[0].cloneNode(true);

  // Reset nilai
  const select = newItem.querySelector('.diagnosa-select');
  select.value = '';
  select.required = false;

  // Tampilkan tombol hapus
  const removeBtn = newItem.querySelector('.remove-diagnosa-btn');
  removeBtn.classList.remove('hidden');

  container.appendChild(newItem);
  updateDiagnosaPrimer();
}

function removeDiagnosa(button) {
  const item = button.closest('.diagnosa-item');
  const container = document.getElementById('diagnosa-container');
  const items = container.querySelectorAll('.diagnosa-item');

  if (items.length > 1) {
    item.remove();
    updateDiagnosaPrimer();
  }
}

function updateDiagnosaPrimer() {
  const primerSelect = document.getElementById('diagnosa-primer');
  const diagnosaSelects = document.querySelectorAll('.diagnosa-select');

  // Clear options
  primerSelect.innerHTML = '<option value="">Pilih diagnosa primer...</option>';

  // Add current diagnosa options
  diagnosaSelects.forEach(select => {
    if (select.value) {
      const option = select.options[select.selectedIndex];
      if (option.value) {
        const newOption = new Option(option.text, option.value);
        primerSelect.appendChild(newOption);
      }
    }
  });

  // Update options when diagnosa changes
  diagnosaSelects.forEach(select => {
    select.addEventListener('change', updateDiagnosaPrimer);
  });
}

function addObat() {
  const container = document.getElementById('obat-container');
  const items = container.querySelectorAll('.obat-item');
  const newItem = items[0].cloneNode(true);

  // Reset nilai
  const inputs = newItem.querySelectorAll('input, select');
  inputs.forEach(input => {
    input.value = '';
  });

  // Tampilkan tombol hapus
  const removeBtn = newItem.querySelector('.remove-obat-btn');
  removeBtn.classList.remove('hidden');

  container.appendChild(newItem);
}

function removeObat(button) {
  const item = button.closest('.obat-item');
  const container = document.getElementById('obat-container');
  const items = container.querySelectorAll('.obat-item');

  if (items.length > 1) {
    item.remove();
  }
}

// Initialize when modal is shown
document.addEventListener('DOMContentLoaded', function() {
  updateDiagnosaPrimer();
});
</script>