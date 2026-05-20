@extends('layouts.app')

@section('title', 'Pemeriksaan Pasien - ' . $antrian->pasien?->nama)
@section('page-title', 'Pemeriksaan Pasien')
@section('page-subtitle', 'Berikan diagnosa medis dan resep obat digital')

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-5">
  <a href="{{ route('dokter.antrian') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Antrian
  </a>
</div>

<form id="diagnosaForm" action="{{ route('dokter.antrian.diagnosa', $antrian->id) }}" method="POST" class="space-y-6">
  @csrf

  {{-- Tampilkan error validasi jika ada --}}
  @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 shadow-sm">
      <p class="font-semibold mb-1 flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-red-500"></i> Terdapat kesalahan pada form:
      </p>
      <ul class="list-disc list-inside text-sm space-y-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Ringkasan Informasi Pasien -->
  <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white text-xl font-bold">
          {{ strtoupper(substr($antrian->pasien?->nama ?? '?', 0, 1)) }}
        </div>
        <div>
          <h2 class="text-lg font-bold text-white">{{ $antrian->pasien?->nama }}</h2>
          <p class="text-blue-300 text-sm font-mono mt-0.5"><i class="fas fa-id-card mr-1"></i> No. RM: {{ $antrian->pasien?->no_rm }}</p>
        </div>
      </div>
      <div class="flex gap-2">
        <span class="px-3 py-1 bg-white/10 text-white/90 text-xs font-bold rounded-lg border border-white/20">No. Antrian #{{ $antrian->no_antrian }}</span>
        @if($antrian->pasien?->jenis_kelamin === 'L')
          <span class="px-3 py-1 bg-blue-500/20 text-blue-300 text-xs font-bold rounded-lg border border-blue-500/30 flex items-center gap-1.5"><i class="fas fa-mars"></i> Laki-laki</span>
        @else
          <span class="px-3 py-1 bg-pink-500/20 text-pink-300 text-xs font-bold rounded-lg border border-pink-500/30 flex items-center gap-1.5"><i class="fas fa-venus"></i> Perempuan</span>
        @endif
        <span class="px-3 py-1 bg-white/10 text-white/90 text-xs font-bold rounded-lg border border-white/20">
          {{ $antrian->pasien?->tgl_lahir ? \Carbon\Carbon::parse($antrian->pasien->tgl_lahir)->age : '-' }} Tahun
        </span>
      </div>
    </div>
    
    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6 bg-slate-50/50">
      <div>
        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Golongan Darah</p>
        <p class="font-bold text-red-600 text-lg flex items-center gap-2">
          <i class="fas fa-tint"></i> {{ $antrian->pasien?->golongan_darah ?? '-' }}
        </p>
      </div>
      <div>
        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">NIK</p>
        <p class="font-semibold text-gray-800 text-sm">{{ $antrian->pasien?->nik ?? '-' }}</p>
      </div>
      <div>
        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Keluhan Awal Pasien</p>
        <p class="font-semibold text-gray-700 text-sm leading-relaxed">{{ $antrian->keluhan ?? 'Tidak ada keluhan tertulis' }}</p>
      </div>
      <div>
        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Riwayat Alergi</p>
        @if($antrian->pasien?->riwayat_alergi)
          <p class="font-semibold text-red-600 text-sm flex items-start gap-1.5">
            <i class="fas fa-exclamation-triangle mt-0.5"></i> {{ $antrian->pasien->riwayat_alergi }}
          </p>
        @else
          <p class="font-semibold text-gray-500 text-sm">Tidak ada riwayat alergi</p>
        @endif
      </div>
    </div>
  </div>

  <!-- Formulir Pemeriksaan Medis -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Kolom Kiri & Tengah: Data Diagnosa & Resep -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- Box 1: Anamnesis & Pemeriksaan Fisik -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-gray-800 text-base mb-2 flex items-center gap-2 border-b pb-3">
          <i class="fas fa-notes-medical text-blue-500"></i> Catatan Pemeriksaan Medis
        </h3>
        
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Anamnesis <span class="text-red-500">*</span></label>
          <textarea name="anamnesis" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Tuliskan riwayat keluhan penyakit saat ini..." required>{{ old('anamnesis') }}</textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Pemeriksaan Fisik</label>
          <textarea name="pemeriksaan_fisik" rows="4" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Tuliskan hasil pemeriksaan fisik pasien...">{{ old('pemeriksaan_fisik') }}</textarea>
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-bold text-slate-500 uppercase">Riwayat Alergi</label>
            <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-100 font-semibold">Tersimpan ke Profil Pasien</span>
          </div>
          <textarea name="riwayat_alergi" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Alergi obat, makanan, dll (Edit atau tambahkan jika ada)...">{{ old('riwayat_alergi', $antrian->pasien?->riwayat_alergi) }}</textarea>
        </div>
      </div>

      <!-- Box 2: Diagnosa ICD-10 -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-gray-800 text-base mb-2 flex items-center gap-2 border-b pb-3">
          <i class="fas fa-stethoscope text-red-500"></i> Diagnosa Penyakit (ICD-10)
        </h3>

        <div id="diagnosa-container" class="space-y-3">
          <div class="diagnosa-item bg-slate-50 p-4 rounded-xl border border-slate-200 relative">
            <div class="grid grid-cols-1 gap-3">
              <div class="relative">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Pencarian Diagnosa ICD-10 <span class="text-red-500">*</span></label>
                
                <!-- Hidden input for selected ICDX ID -->
                <input type="hidden" name="diagnosa[]" class="diagnosa-id" required>
                
                <!-- Text input for search and display -->
                <input type="text" 
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white diagnosa-search font-medium text-sm" 
                       placeholder="Ketik kode ICD atau nama penyakit (misal: A00)..." 
                       oninput="searchIcdx(this)" 
                       onfocus="searchIcdx(this)"
                       autocomplete="off">
                
                <!-- Floating Results Dropdown -->
                <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg icdx-dropdown divide-y divide-slate-100">
                  <!-- Dynamic search results will be injected here -->
                </div>
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

        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mt-4">
          <label class="block text-sm font-bold text-blue-800 mb-2">Diagnosa Primer <span class="text-red-500">*</span></label>
          <select name="diagnosa_primer" id="diagnosa-primer" class="w-full px-3 py-2.5 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" required>
            <option value="">Pilih diagnosa primer dari daftar di atas...</option>
          </select>
          <p class="text-[11px] text-blue-600 mt-1.5"><i class="fas fa-info-circle mr-1"></i> Diagnosa primer adalah diagnosa utama/utama yang menyebabkan pasien datang.</p>
        </div>
      </div>

      <!-- Box 3: Resep Obat Digital -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-gray-800 text-base mb-2 flex items-center gap-2 border-b pb-3">
          <i class="fas fa-pills text-purple-500"></i> Resep Obat Digital
        </h3>

        <div class="bg-purple-50/50 p-4 rounded-xl border border-purple-100">
          <label class="block text-sm font-bold text-purple-800 mb-2">Apakah ingin membuat resep obat?</label>
          <div class="flex gap-6 mt-1">
            <label class="inline-flex items-center cursor-pointer">
              <input type="radio" name="pakai_resep" value="Ya" class="form-radio text-purple-600 w-4 h-4" onchange="toggleResep(true)">
              <span class="ml-2 font-semibold text-sm text-slate-700">Ya, Buat Resep Digital</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
              <input type="radio" name="pakai_resep" value="Tidak" class="form-radio text-purple-600 w-4 h-4" checked onchange="toggleResep(false)">
              <span class="ml-2 font-semibold text-sm text-slate-700">Tidak Perlu Resep</span>
            </label>
          </div>
          <p class="text-[11px] text-purple-600 mt-2"><i class="fas fa-info-circle mr-1"></i> Jika memilih Ya, resep obat akan diteruskan otomatis ke meja Apoteker untuk diracik.</p>
        </div>

        <!-- Bagian Input Obat -->
        <div id="section-resep" class="hidden space-y-4 pt-3">
          <h4 class="font-bold text-slate-700 text-sm"><i class="fas fa-prescription-bottle-alt mr-1 text-purple-500"></i> Daftar Obat</h4>
          <div id="obat-container" class="space-y-3">
            <div class="border border-slate-200 rounded-xl p-4 obat-item bg-slate-50/50 relative">
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Nama Obat</label>
                  <select name="obat_id[]" class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm">
                    <option value="">Pilih obat...</option>
                    @foreach($obats as $obat)
                      <option value="{{ $obat->id }}">{{ $obat->nama }} (Stok: {{ $obat->stok }})</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Jumlah</label>
                  <input type="number" name="jumlah[]" min="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" placeholder="1">
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Dosis</label>
                  <input type="text" name="dosis[]" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" placeholder="2x1">
                </div>
                <div class="flex justify-end">
                  <button type="button" onclick="removeObat(this)" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold rounded-lg transition remove-obat-btn hidden items-center gap-1.5 w-full justify-center">
                    <i class="fas fa-trash-alt"></i> Hapus Baris
                  </button>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 pt-3 border-t border-slate-200/50">
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Aturan Pakai</label>
                  <div class="aturan-pakai-container">
                    <select name="aturan_pakai[]" class="aturan-pakai-select w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" onchange="toggleAturanPakaiCustom(this)">
                      <option value="Sesudah makan">Sesudah makan</option>
                      <option value="Sebelum makan">Sebelum makan</option>
                      <option value="Bersama makan">Bersama makan</option>
                      <option value="Sebelum tidur">Sebelum tidur</option>
                      <option value="custom">Lainnya (Ketik Manual)...</option>
                    </select>
                    <input type="text" class="aturan-pakai-custom hidden mt-2 w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" placeholder="Tulis aturan pakai sendiri...">
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Keterangan</label>
                  <input type="text" name="keterangan[]" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" placeholder="Keterangan tambahan (misal: habiskan)">
                </div>
              </div>
            </div>
          </div>
          
          <button type="button" onclick="addObat()" class="mt-2 flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition font-semibold text-sm border border-purple-100">
            <i class="fas fa-plus"></i> Tambah Obat Lain
          </button>
        </div>

        <!-- Catatan Dokter untuk Resep -->
        <div id="section-catatan-resep" class="hidden mt-4">
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Catatan Dokter untuk Apoteker</label>
          <textarea name="catatan_dokter" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Tulis instruksi khusus untuk apoteker jika ada..."></textarea>
        </div>
      </div>

    </div>

    <!-- Kolom Kanan: Rincian Pelayanan, TTV (Read-only), Tindakan, Prognosa -->
    <div class="space-y-6">
      
      <!-- Box 4: Pelayanan & Status -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-gray-800 text-base mb-2 flex items-center gap-2 border-b pb-3">
          <i class="fas fa-file-medical text-slate-600"></i> Status & Pelayanan
        </h3>
        
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
          <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700">
            {{ $antrian->rekamMedis?->pelayanan_kesehatan ?? '-' }}
          </div>
        </div>
        
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jenis Pelayanan</label>
          <div class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700">
            {{ $antrian->rekamMedis?->jenis_pelayanan ?? '-' }}
          </div>
        </div>
      </div>

      <!-- Box 5: Vital Signs (Read Only - Diisi oleh Admin saat pendaftaran) -->
      <div class="bg-amber-50/50 border border-amber-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-amber-200/60 pb-3">
          <i class="fas fa-heartbeat text-amber-500"></i>
          <h3 class="font-bold text-amber-800 text-base">Tanda-tanda Vital</h3>
          <span class="text-[9px] bg-amber-100 border border-amber-200 text-amber-700 px-2 py-0.5 rounded font-bold uppercase ml-auto">Admin</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="bg-white rounded-xl p-3 border border-amber-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Tekanan Darah</p>
            <p class="font-bold text-gray-800 text-sm">{{ $antrian->rekamMedis?->tekanan_darah ?? '—' }}</p>
            <input type="hidden" name="tekanan_darah" value="{{ $antrian->rekamMedis?->tekanan_darah }}">
          </div>
          <div class="bg-white rounded-xl p-3 border border-amber-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Suhu Tubuh</p>
            <p class="font-bold text-gray-800 text-sm">{{ $antrian->rekamMedis?->suhu ? $antrian->rekamMedis->suhu . ' °C' : '—' }}</p>
            <input type="hidden" name="suhu" value="{{ $antrian->rekamMedis?->suhu }}">
          </div>
          <div class="bg-white rounded-xl p-3 border border-amber-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Berat Badan</p>
            <p class="font-bold text-gray-800 text-sm">{{ $antrian->rekamMedis?->berat_badan ? $antrian->rekamMedis->berat_badan . ' kg' : '—' }}</p>
            <input type="hidden" name="berat_badan" value="{{ $antrian->rekamMedis?->berat_badan }}">
          </div>
          <div class="bg-white rounded-xl p-3 border border-amber-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Tinggi Badan</p>
            <p class="font-bold text-gray-800 text-sm">{{ $antrian->rekamMedis?->tinggi_badan ? $antrian->rekamMedis->tinggi_badan . ' cm' : '—' }}</p>
            <input type="hidden" name="tinggi_badan" value="{{ $antrian->rekamMedis?->tinggi_badan }}">
          </div>
          <div class="bg-white rounded-xl p-3 border border-amber-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Detak Nadi</p>
            <p class="font-bold text-gray-800 text-sm">{{ $antrian->rekamMedis?->nadi ? $antrian->rekamMedis->nadi . ' x/m' : '—' }}</p>
            <input type="hidden" name="nadi" value="{{ $antrian->rekamMedis?->nadi }}">
          </div>
          <div class="bg-white rounded-xl p-3 border border-amber-100">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Respirasi</p>
            <p class="font-bold text-gray-800 text-sm">{{ $antrian->rekamMedis?->respirasi ? $antrian->rekamMedis->respirasi . ' x/m' : '—' }}</p>
            <input type="hidden" name="respirasi" value="{{ $antrian->rekamMedis?->respirasi }}">
          </div>
        </div>
      </div>

      <!-- Box 6: Tindakan & Prognosa & Pengobatan -->
      <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-gray-800 text-base mb-2 flex items-center gap-2 border-b pb-3">
          <i class="fas fa-user-md text-emerald-500"></i> Rencana & Tindakan Medis
        </h3>

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tindakan</label>
          <textarea name="tindakan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Tindakan medis yang dilakukan...">{{ old('tindakan') }}</textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Rencana Pengobatan / Terapi</label>
          <textarea name="pengobatan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Rencana pengobatan yang diberikan...">{{ old('pengobatan') }}</textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Prognosa</label>
          <textarea name="prognosa" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Prognosa penyakit pasien...">{{ old('prognosa') }}</textarea>
        </div>
        
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Keadaan Keluar</label>
          <select name="keadaan_keluar" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            <option value="">Pilih keadaan...</option>
            <option value="Sembuh">Sembuh</option>
            <option value="Membaik">Membaik</option>
            <option value="Belum Sembuh">Belum Sembuh</option>
            <option value="Meninggal">Meninggal</option>
          </select>
        </div>

        <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-200">
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_rekomendasi_rawat_inap" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300" {{ old('is_rekomendasi_rawat_inap', $antrian->rekamMedis?->is_rekomendasi_rawat_inap) ? 'checked' : '' }}>
            <div>
              <span class="block font-bold text-blue-900 text-sm">Rekomendasikan Rawat Inap</span>
              <span class="block text-xs text-blue-600">Centang jika pasien ini membutuhkan layanan mondok/rawat inap.</span>
            </div>
          </label>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Rujukan Ke</label>
          <input type="text" name="rujukan_ke" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Nama faskes tujuan rujukan...">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Catatan Tambahan</label>
          <textarea name="catatan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Catatan tambahan dokter...">{{ old('catatan') }}</textarea>
        </div>
      </div>

    </div>

  </div>

  <!-- Submit Buttons di Bawah Halaman -->
  <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex flex-col sm:flex-row justify-end gap-3">
    <a href="{{ route('dokter.antrian') }}" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-center text-sm shadow-sm">
      Kembali & Batal
    </a>
    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-md flex items-center justify-center gap-2 text-sm">
      <i class="fas fa-save"></i> Simpan Hasil Diagnosa & Selesai
    </button>
  </div>

</form>

@endsection

@push('scripts')
<script>
// Preload ICD-10 data from database as JSON for supreme client-side filtering speed
const icdxData = @json(\App\Models\Icdx::select('id', 'kode', 'nama')->orderBy('kode')->get());

function toggleResep(show) {
  const sectionResep = document.getElementById('section-resep');
  const sectionCatatan = document.getElementById('section-catatan-resep');
  
  if (show) {
    sectionResep.classList.remove('hidden');
    sectionCatatan.classList.remove('hidden');
  } else {
    sectionResep.classList.add('hidden');
    sectionCatatan.classList.add('hidden');
  }
}

function searchIcdx(input) {
  const query = input.value.toLowerCase().trim();
  const dropdown = input.nextElementSibling; // The floating dropdown container
  
  // Clear previous search results
  dropdown.innerHTML = '';
  
  if (!query) {
    dropdown.classList.add('hidden');
    // Clear selected ID if search input is cleared
    const item = input.closest('.diagnosa-item');
    const hiddenId = item.querySelector('.diagnosa-id');
    if (hiddenId) hiddenId.value = '';
    updateDiagnosaPrimer();
    return;
  }
  
  // Filter top 10 matches client-side in a split second
  const matches = [];
  for (let i = 0; i < icdxData.length; i++) {
    const item = icdxData[i];
    if (item.kode.toLowerCase().includes(query) || item.nama.toLowerCase().includes(query)) {
      matches.push(item);
      if (matches.length >= 10) break; // Maximum 10 items for visual clarity and ultimate performance
    }
  }
  
  if (matches.length === 0) {
    dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500 font-medium">Tidak ada hasil cocok</div>';
    dropdown.classList.remove('hidden');
    return;
  }
  
  // Render search results dropdown
  matches.forEach(item => {
    const option = document.createElement('div');
    option.className = 'px-4 py-2.5 hover:bg-blue-50 text-sm text-slate-700 cursor-pointer font-medium transition flex justify-between items-center';
    option.innerHTML = `
      <span><strong class="text-blue-600">${item.kode}</strong> - ${item.nama}</span>
      <i class="fas fa-plus text-xs text-slate-300"></i>
    `;
    option.onclick = function() {
      // Set values and trigger primer selection update
      const hiddenId = input.previousElementSibling;
      if (hiddenId) hiddenId.value = item.id;
      input.value = `${item.kode} - ${item.nama}`;
      dropdown.classList.add('hidden');
      updateDiagnosaPrimer();
    };
    dropdown.appendChild(option);
  });
  
  dropdown.classList.remove('hidden');
}

// Close search dropdowns when clicking outside
document.addEventListener('click', function(e) {
  if (!e.target.classList.contains('diagnosa-search') && !e.target.closest('.icdx-dropdown')) {
    document.querySelectorAll('.icdx-dropdown').forEach(dropdown => {
      dropdown.classList.add('hidden');
    });
  }
});

function addDiagnosa() {
  const container = document.getElementById('diagnosa-container');
  const items = container.querySelectorAll('.diagnosa-item');
  const newItem = items[0].cloneNode(true);

  // Reset values for new cloned row
  const hiddenInput = newItem.querySelector('.diagnosa-id');
  if (hiddenInput) hiddenInput.value = '';
  
  const searchInput = newItem.querySelector('.diagnosa-search');
  if (searchInput) {
    searchInput.value = '';
    // Ensure event handlers bind correctly
    searchInput.oninput = function() { searchIcdx(this); };
    searchInput.onfocus = function() { searchIcdx(this); };
  }
  
  const dropdown = newItem.querySelector('.icdx-dropdown');
  if (dropdown) {
    dropdown.innerHTML = '';
    dropdown.classList.add('hidden');
  }

  // Show remove row button
  const removeBtn = newItem.querySelector('.remove-diagnosa-btn');
  if (removeBtn) {
    removeBtn.classList.remove('hidden');
    removeBtn.classList.add('flex');
  }

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
  const diagnosaItems = document.querySelectorAll('.diagnosa-item');
  const currentVal = primerSelect.value;

  // Clear primer choices
  primerSelect.innerHTML = '<option value="">Pilih diagnosa primer dari daftar di atas...</option>';

  let hasSelection = false;
  diagnosaItems.forEach(item => {
    const hiddenId = item.querySelector('.diagnosa-id').value;
    const searchVal = item.querySelector('.diagnosa-search').value;

    if (hiddenId && searchVal) {
      const option = new Option(searchVal, hiddenId);
      if (hiddenId === currentVal) {
        option.selected = true;
        hasSelection = true;
      }
      primerSelect.appendChild(option);
    }
  });
}

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

function addObat() {
  const container = document.getElementById('obat-container');
  const items = container.querySelectorAll('.obat-item');
  const newItem = items[0].cloneNode(true);

  // Reset input fields
  const inputs = newItem.querySelectorAll('input, select');
  inputs.forEach(input => {
    input.value = '';
  });

  // Reset the Aturan Pakai dropdown in the cloned item
  const selectAturan = newItem.querySelector('.aturan-pakai-select');
  const customAturan = newItem.querySelector('.aturan-pakai-custom');
  if (selectAturan && customAturan) {
    selectAturan.value = 'Sesudah makan';
    selectAturan.setAttribute('name', 'aturan_pakai[]');
    customAturan.value = '';
    customAturan.removeAttribute('name');
    customAturan.classList.add('hidden');
  }

  // Show delete button
  const removeBtn = newItem.querySelector('.remove-obat-btn');
  if (removeBtn) {
    removeBtn.classList.remove('hidden');
    removeBtn.classList.add('flex');
  }

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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  updateDiagnosaPrimer();
});
</script>
@endpush
