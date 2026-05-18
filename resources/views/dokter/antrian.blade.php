@extends('layouts.dokter')

@section('title', 'Antrian Pasien')
@section('page-title', 'Antrian Pasien')
@section('page-subtitle', 'Kelola antrian pasien hari ini')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Antrian</p>
    <p class="text-3xl font-bold text-slate-800">{{ $jumlahAntrian }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Menunggu</p>
    <p class="text-3xl font-bold text-amber-600">{{ $antrians->where('status', 'Menunggu')->count() }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Dipanggil</p>
    <p class="text-3xl font-bold text-blue-700">{{ $Dipanggil }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Selesai</p>
    <p class="text-3xl font-bold text-green-700">{{ $selesai }}</p>
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
                <span class="px-4 py-2 bg-gray-100 text-gray-500 text-sm rounded-lg">Menunggu Panggilan</span>
              @elseif(in_array($antrian->status, ['Dipanggil', 'Dilayani']))
                <button type="button" 
                  onclick="openDiagnosaModal(
                    '{{ $antrian->id }}', 
                    '{{ addslashes($antrian->pasien?->nama) }}', 
                    '{{ $antrian->pasien?->no_rm }}', 
                    '{{ $antrian->pasien?->tgl_lahir ? \Carbon\Carbon::parse($antrian->pasien->tgl_lahir)->age : '-' }}', 
                    '{{ $antrian->pasien?->jenis_kelamin }}', 
                    '{{ addslashes($antrian->keluhan) }}',
                    '{{ $antrian->rekamMedis?->tekanan_darah }}',
                    '{{ $antrian->rekamMedis?->suhu }}',
                    '{{ $antrian->rekamMedis?->berat_badan }}',
                    '{{ $antrian->rekamMedis?->tinggi_badan }}',
                    '{{ $antrian->rekamMedis?->nadi }}',
                    '{{ $antrian->rekamMedis?->respirasi }}',
                    {{ json_encode($antrian->pasien?->riwayat_alergi ?? '') }}
                  )"
                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  Diagnosa & Resep
                </button>
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
<div id="diagnosaModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 sm:p-6">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden" style="animation: modalIn .2s ease">
    
    <div class="px-6 py-4 border-b border-gray-100 bg-slate-800 flex items-center justify-between shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
          <i class="fas fa-stethoscope text-white"></i>
        </div>
        <h3 class="font-bold text-white text-base">
          Pemeriksaan: <span id="modalPasienNama" class="text-blue-300"></span>
        </h3>
      </div>
      <button type="button" onclick="closeDiagnosaModal()" class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center text-white transition">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>

    <div class="p-4 sm:p-6 overflow-y-auto flex-1">


      <form id="diagnosaForm" action="" method="POST" class="space-y-6">
        @csrf

        {{-- Tampilkan error validasi jika ada --}}
        @if($errors->any())
          <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3">
            <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle text-red-500"></i> Terdapat kesalahan pada form:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Informasi Pasien -->
        <div class="bg-gray-50 p-4 rounded-lg">
          <h4 class="font-semibold text-gray-800 mb-2">Informasi Pasien</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><strong>No. RM:</strong> <span id="modalNoRm"></span></div>
            <div><strong>Nama:</strong> <span id="modalNama"></span></div>
            <div><strong>Umur:</strong> <span id="modalUmur"></span> tahun</div>
            <div><strong>Jenis Kelamin:</strong> <span id="modalJk"></span></div>
            <div><strong>Keluhan:</strong> <span id="modalKeluhan"></span></div>
          </div>
        </div>

        <!-- Informasi Pelayanan (Advanced Diagnosis) -->
        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 shadow-sm">
          <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-file-medical text-slate-600"></i> Informasi Pelayanan & Status
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Kasus Penyakit <span class="text-red-500">*</span></label>
              <select name="kasus_penyakit" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                <option value="">— Pilih Kasus —</option>
                <option value="Baru">Baru</option>
                <option value="Lama">Lama</option>
                <option value="KKL">KKL</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Status Pasien</label>
              <select name="status_pasien" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Pilih Status —</option>
                <option value="Baru">Baru</option>
                <option value="Lama">Lama</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pelayanan Kesehatan</label>
              <select name="pelayanan_kesehatan" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Pilih Pelayanan —</option>
                <option value="BPJS">BPJS</option>
                <option value="UMUM">UMUM</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jenis Pelayanan</label>
              <select name="jenis_pelayanan" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Pilih Jenis —</option>
                <option value="Poli Umum">Poli Umum</option>
                <option value="Poli Gigi">Poli Gigi</option>
                <option value="Poli KIA">Poli KIA</option>
                <option value="UGD">UGD</option>
                <option value="Laboratorium">Laboratorium</option>
                <option value="Baby Spa">Baby Spa</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="block text-sm font-bold text-slate-700 mb-2">Rencana Pengobatan / Terapi</label>
          <textarea name="pengobatan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Rencana pengobatan yang diberikan kepada pasien..."></textarea>
        </div>

        <!-- Pemeriksaan Fisik & Anamnesis -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Anamnesis</label>
              <textarea name="anamnesis" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Riwayat penyakit, keluhan pasien..."></textarea>
            </div>
            <div>
              <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Riwayat Alergi</label>
                <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100">Dapat ditambah/diubah</span>
              </div>
              <textarea name="riwayat_alergi" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Alergi obat, makanan, dll (Edit atau tambahkan jika ada)..."></textarea>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pemeriksaan Fisik</label>
            <textarea name="pemeriksaan_fisik" rows="7" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Hasil pemeriksaan fisik..."></textarea>
          </div>
        </div>

        <!-- Vital Signs (Read Only - dari Pemeriksaan Awal Admin) -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
          <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-lock text-amber-500 text-sm"></i>
            <h4 class="font-semibold text-amber-800 text-sm">Tanda Vital — Hasil Pemeriksaan Awal</h4>
            <span class="text-xs text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full font-medium">Diisi oleh Admin</span>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="bg-white rounded-lg p-3 border border-amber-100">
              <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Tekanan Darah</p>
              <p class="font-bold text-gray-800 text-base" id="displayTekananDarah">—</p>
              <input type="hidden" name="tekanan_darah" id="hiddenTekananDarah">
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
              <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Suhu</p>
              <p class="font-bold text-gray-800 text-base" id="displaySuhu">—</p>
              <p class="text-xs text-gray-400">°C</p>
              <input type="hidden" name="suhu" id="hiddenSuhu">
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
              <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Berat Badan</p>
              <p class="font-bold text-gray-800 text-base" id="displayBeratBadan">—</p>
              <p class="text-xs text-gray-400">kg</p>
              <input type="hidden" name="berat_badan" id="hiddenBeratBadan">
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
              <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Tinggi Badan</p>
              <p class="font-bold text-gray-800 text-base" id="displayTinggiBadan">—</p>
              <p class="text-xs text-gray-400">cm</p>
              <input type="hidden" name="tinggi_badan" id="hiddenTinggiBadan">
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
              <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Nadi</p>
              <p class="font-bold text-gray-800 text-base" id="displayNadi">—</p>
              <p class="text-xs text-gray-400">x/menit</p>
              <input type="hidden" name="nadi" id="hiddenNadi">
            </div>
            <div class="bg-white rounded-lg p-3 border border-amber-100">
              <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Respirasi</p>
              <p class="font-bold text-gray-800 text-base" id="displayRespirasi">—</p>
              <p class="text-xs text-gray-400">x/menit</p>
              <input type="hidden" name="respirasi" id="hiddenRespirasi">
            </div>
          </div>
        </div>


        <!-- Diagnosa -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
          <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-stethoscope text-blue-600"></i> Diagnosa (ICD-10)
          </h4>
          <div id="diagnosa-container" class="space-y-3">
            <div class="diagnosa-item bg-white p-3 rounded-lg border border-gray-200 shadow-sm relative">
              <div class="grid grid-cols-1 gap-3">
                <div class="relative">
                  <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Diagnosa</label>
                  <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2 diagnosa-search" placeholder="Ketik kode atau nama penyakit..." onkeyup="filterDiagnosa(this)">
                  <select name="diagnosa[]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 diagnosa-select" required onchange="updateDiagnosaPrimer()">
                    <option value="">-- Pilih Hasil Pencarian --</option>
                    @foreach(\App\Models\Icdx::orderBy('kode')->get() as $icdx)
                      <option value="{{ $icdx->id }}">{{ $icdx->kode }} - {{ $icdx->nama }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <button type="button" onclick="removeDiagnosa(this)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 remove-diagnosa-btn hidden items-center justify-center shadow-md">
                <i class="fas fa-times text-xs"></i>
              </button>
            </div>
          </div>
          <button type="button" onclick="addDiagnosa()" class="mt-3 flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-semibold text-sm">
            <i class="fas fa-plus-circle"></i> Tambah Diagnosa Lain
          </button>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
          <label class="block text-sm font-bold text-blue-800 mb-2">Diagnosa Primer <span class="text-red-500">*</span></label>
          <select name="diagnosa_primer" id="diagnosa-primer" class="w-full px-3 py-2 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" required>
            <option value="">Pilih diagnosa primer dari daftar di atas...</option>
          </select>
          <p class="text-xs text-blue-600 mt-1">Diagnosa primer adalah diagnosa utama yang menyebabkan pasien datang.</p>
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

        <!-- Pilihan Resep -->
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
          <label class="block text-sm font-medium text-blue-800 mb-2">Apakah ingin membuat resep obat?</label>
          <div class="flex gap-4">
            <label class="inline-flex items-center">
              <input type="radio" name="pakai_resep" value="Ya" class="form-radio text-blue-600" onchange="toggleResep(true)">
              <span class="ml-2 text-sm text-gray-700">Ya, Buat Resep</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" name="pakai_resep" value="Tidak" class="form-radio text-blue-600" checked onchange="toggleResep(false)">
              <span class="ml-2 text-sm text-gray-700">Tidak</span>
            </label>
          </div>
          <p class="text-xs text-blue-600 mt-1">* Jika memilih Ya, data resep akan diteruskan ke Apoteker.</p>
        </div>

        <!-- Resep Obat -->
        <div id="section-resep" class="hidden">
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
        <div id="section-catatan-resep" class="hidden">
          <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Dokter untuk Apoteker</label>
          <textarea name="catatan_dokter" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Instruksi khusus untuk apoteker..."></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-5 border-t border-gray-200 mt-6">
          <button type="button" onclick="closeDiagnosaModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition text-center">Batal</button>
          <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-save"></i> Simpan Diagnosa & Selesai
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection

@push('scripts')
<script>
function openDiagnosaModal(id, nama, noRm, umur, jk, keluhan, td, suhu, bb, tb, nadi, res, alergi) {
  const modal = document.getElementById('diagnosaModal');
  const form = document.getElementById('diagnosaForm');
  
  // Set data pasien
  document.getElementById('modalPasienNama').textContent = nama;
  document.getElementById('modalNama').textContent = nama;
  document.getElementById('modalNoRm').textContent = noRm;
  document.getElementById('modalUmur').textContent = umur;
  document.getElementById('modalJk').textContent = jk;
  document.getElementById('modalKeluhan').textContent = keluhan;

  // Set display TTV (read-only) + hidden input
  const ttvMap = [
    { display: 'displayTekananDarah', hidden: 'hiddenTekananDarah', val: td, suffix: '' },
    { display: 'displaySuhu',         hidden: 'hiddenSuhu',         val: suhu, suffix: '' },
    { display: 'displayBeratBadan',   hidden: 'hiddenBeratBadan',   val: bb,   suffix: '' },
    { display: 'displayTinggiBadan',  hidden: 'hiddenTinggiBadan',  val: tb,   suffix: '' },
    { display: 'displayNadi',         hidden: 'hiddenNadi',         val: nadi, suffix: '' },
    { display: 'displayRespirasi',    hidden: 'hiddenRespirasi',    val: res,  suffix: '' },
  ];
  ttvMap.forEach(function(item) {
    const displayEl = document.getElementById(item.display);
    const hiddenEl  = document.getElementById(item.hidden);
    if (displayEl) displayEl.textContent = (item.val && item.val !== 'null' && item.val !== '') ? item.val : '—';
    if (hiddenEl)  hiddenEl.value = (item.val && item.val !== 'null') ? item.val : '';
  });
  
  // Set action form
  form.action = `/dokter/antrian/${id}/diagnosa`;
  
  // Reset resep toggle
  document.querySelector('input[name="pakai_resep"][value="Tidak"]').checked = true;
  toggleResep(false);

  const alergiInput = document.querySelector('textarea[name="riwayat_alergi"]');
  if(alergiInput) alergiInput.value = alergi || '';
  
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeDiagnosaModal() {
  document.getElementById('diagnosaModal').classList.add('hidden');
  document.getElementById('diagnosaModal').classList.remove('flex');
}


function toggleResep(show) {
  const sectionResep = document.getElementById('section-resep');
  const sectionCatatan = document.getElementById('section-catatan-resep');
  
  if (show) {
    sectionResep.classList.remove('hidden');
    sectionCatatan.classList.remove('hidden');
  } else {
    sectionResep.classList.add('hidden');
    sectionCatatan.classList.add('hidden');
    // Clear inputs if hidden (optional but safer)
    // sectionResep.querySelectorAll('input, select').forEach(i => i.value = '');
  }
}

function filterDiagnosa(input) {
  const filter = input.value.toLowerCase();
  const select = input.nextElementSibling;
  const options = select.getElementsByTagName('option');
  
  let matchCount = 0;
  for (let i = 1; i < options.length; i++) {
    const txtValue = options[i].textContent || options[i].innerText;
    if (txtValue.toLowerCase().indexOf(filter) > -1) {
      options[i].style.display = "";
      matchCount++;
    } else {
      options[i].style.display = "none";
    }
  }
}

function addDiagnosa() {
  const container = document.getElementById('diagnosa-container');
  const items = container.querySelectorAll('.diagnosa-item');
  const newItem = items[0].cloneNode(true);

  // Reset nilai
  const searchInput = newItem.querySelector('.diagnosa-search');
  searchInput.value = '';
  
  const select = newItem.querySelector('.diagnosa-select');
  select.value = '';
  select.required = false;
  
  // Reset visibility options
  const options = select.getElementsByTagName('option');
  for (let i = 0; i < options.length; i++) {
    options[i].style.display = "";
  }

  // Tampilkan tombol hapus
  const removeBtn = newItem.querySelector('.remove-diagnosa-btn');
  removeBtn.classList.remove('hidden');
  removeBtn.classList.add('flex');

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
  const currentVal = primerSelect.value;

  // Clear options
  primerSelect.innerHTML = '<option value="">Pilih diagnosa primer dari daftar di atas...</option>';

  // Add current diagnosa options
  let hasSelection = false;
  diagnosaSelects.forEach(select => {
    if (select.value) {
      const option = select.options[select.selectedIndex];
      if (option.value) {
        const newOption = new Option(option.text, option.value);
        if (option.value === currentVal) {
          newOption.selected = true;
          hasSelection = true;
        }
        primerSelect.appendChild(newOption);
      }
    }
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
@endpush