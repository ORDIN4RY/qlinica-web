@extends('layouts.app')

@section('title', 'Pemeriksaan Pasien - ' . $antrian->pasien?->nama)
@section('page-title', 'Pemeriksaan Pasien')
@section('page-subtitle', 'Berikan diagnosa medis dan resep obat digital')

@push('styles')
<style>
  /* ── Step Wizard Progress Bar ─────────────────────── */
  .step-item { display: flex; flex-direction: column; align-items: center; position: relative; flex: 1; }
  .step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 18px;
    left: 55%;
    width: 90%;
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
    transition: background 0.4s;
  }
  .step-item.done:not(:last-child)::after   { background: #1e3a8a; }
  .step-item.active:not(:last-child)::after { background: #e2e8f0; }

  .step-circle {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 800;
    border: 2px solid #e2e8f0;
    background: #fff; color: #94a3b8;
    position: relative; z-index: 1;
    transition: all 0.3s;
  }
  .step-item.active .step-circle { border-color: #1e3a8a; background: #1e3a8a; color: #fff; box-shadow: 0 0 0 4px rgba(30,58,138,0.15); }
  .step-item.done   .step-circle { border-color: #1e3a8a; background: #1e3a8a; color: #fff; }

  .step-label { font-size: 10px; font-weight: 700; margin-top: 6px; text-align: center; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; max-width: 80px; }
  .step-item.active .step-label { color: #1e3a8a; }
  .step-item.done   .step-label { color: #1e3a8a; }

  /* ── Step Content Panels ─────────────────────────── */
  .step-panel { display: none; animation: fadeSlide 0.35s ease; }
  .step-panel.active { display: block; }
  @keyframes fadeSlide {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── Step header card ───────────────────────────── */
  .step-header-card {
    border-left: 4px solid #1e3a8a;
    background: linear-gradient(135deg, #eff6ff 0%, #fff 100%);
  }
</style>
@endpush

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-5">
  <a href="{{ route('dokter.antrian') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Antrian
  </a>
</div>

{{-- Tampilkan error validasi jika ada --}}
@if($errors->any())
  <div class="bg-red-50 border border-red-300 text-red-800 rounded-lg px-4 py-3 shadow-sm mb-5">
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

{{-- ═══════════════════════════════════════════════════ --}}
{{-- Kartu Info Pasien (Selalu Tampil di Atas)           --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
  <div class="bg-[#1e3a8a] px-6 py-4 flex items-center justify-between">
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

  <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-5 bg-slate-50/50">
    <div>
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Golongan Darah</p>
      <p class="font-bold text-red-600 text-lg flex items-center gap-2"><i class="fas fa-tint"></i> {{ $antrian->pasien?->golongan_darah ?? '-' }}</p>
    </div>
    <div>
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">NIK</p>
      <p class="font-semibold text-gray-800 text-sm">{{ $antrian->pasien?->nik ?? '-' }}</p>
    </div>
    <div>
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Keluhan Awal</p>
      <p class="font-semibold text-gray-700 text-sm leading-relaxed">{{ $antrian->keluhan ?? 'Tidak ada keluhan tertulis' }}</p>
    </div>
    <div>
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Riwayat Alergi</p>
      @if($antrian->pasien?->riwayat_alergi)
        <p class="font-semibold text-red-600 text-sm flex items-start gap-1.5"><i class="fas fa-exclamation-triangle mt-0.5"></i> {{ $antrian->pasien->riwayat_alergi }}</p>
      @else
        <p class="font-semibold text-gray-500 text-sm">Tidak ada riwayat alergi</p>
      @endif
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{-- STEP WIZARD PROGRESS BAR                           --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">
  <div class="flex items-start justify-between" id="step-progress">
    <div class="step-item active" id="step-indicator-1">
      <div class="step-circle"><i class="fas fa-notes-medical"></i></div>
      <span class="step-label">Anamnesis</span>
    </div>
    <div class="step-item" id="step-indicator-2">
      <div class="step-circle"><i class="fas fa-stethoscope"></i></div>
      <span class="step-label">Diagnosa ICD-10</span>
    </div>
    <div class="step-item" id="step-indicator-3">
      <div class="step-circle"><i class="fas fa-pills"></i></div>
      <span class="step-label">Resep Obat</span>
    </div>
    <div class="step-item" id="step-indicator-4">
      <div class="step-circle"><i class="fas fa-clipboard-check"></i></div>
      <span class="step-label">Finalisasi</span>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{-- FORM UTAMA (semua step dalam 1 form)               --}}
{{-- ═══════════════════════════════════════════════════ --}}
<form id="diagnosaForm" action="{{ route('dokter.antrian.diagnosa', $antrian->id) }}" method="POST">
  @csrf

  {{-- ══════════════════════════════════════════ --}}
  {{-- STEP 1: Anamnesis & Pemeriksaan Fisik      --}}
  {{-- ══════════════════════════════════════════ --}}
  <div class="step-panel active" id="panel-1">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="step-header-card px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
          <i class="fas fa-notes-medical text-blue-700"></i>
        </div>
        <div>
          <h3 class="font-bold text-gray-800">Langkah 1 dari 4 — Anamnesis & Pemeriksaan</h3>
          <p class="text-xs text-gray-500 mt-0.5">Catat riwayat keluhan dan hasil pemeriksaan fisik pasien</p>
        </div>
      </div>
      <div class="p-6 space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
            Anamnesis <span class="text-red-500">*</span>
            <span class="ml-2 normal-case font-normal text-slate-400">— Riwayat keluhan penyakit saat ini</span>
          </label>
          <textarea id="anamnesis" name="anamnesis" rows="4"
            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"
            placeholder="Contoh: Pasien datang dengan keluhan demam sejak 3 hari yang lalu, disertai nyeri kepala dan batuk kering..."
            required>{{ old('anamnesis') }}</textarea>
          <p class="text-[11px] text-slate-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Tuliskan riwayat keluhan secara kronologis (kapan mulai, sifat keluhan, faktor yang memperburuk/memperingan)</p>
        </div>

        <div class="border-t border-slate-100 pt-5">
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">
            Pemeriksaan Fisik
            <span class="ml-2 normal-case font-normal text-slate-400">— Hasil pemeriksaan objektif</span>
          </label>
          <textarea name="pemeriksaan_fisik" rows="4"
            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"
            placeholder="Contoh: KU: Tampak sakit sedang. Tanda vital: TD 120/80, N 88x/m, S 38,5°C, RR 20x/m. Kepala: normosefali. Thoraks: simetris, ronki (-), whezing (-)...">{{ old('pemeriksaan_fisik') }}</textarea>
        </div>

        <div class="border-t border-slate-100 pt-5">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-bold text-slate-500 uppercase">Riwayat Alergi</label>
            <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-100 font-semibold"><i class="fas fa-user-edit mr-1"></i>Tersimpan ke Profil Pasien</span>
          </div>
          <textarea name="riwayat_alergi" rows="2"
            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"
            placeholder="Alergi obat, makanan, dll (Edit atau tambahkan jika ada)...">{{ old('riwayat_alergi', $antrian->pasien?->riwayat_alergi) }}</textarea>
        </div>

        {{-- TTV (read-only) tampil di step 1 sebagai referensi --}}
        <div class="border-t border-slate-100 pt-5">
          <p class="text-xs font-bold text-slate-500 uppercase mb-3"><i class="fas fa-heartbeat text-amber-500 mr-1"></i>Tanda-tanda Vital <span class="normal-case font-normal text-slate-400 ml-1">(diisi admin saat pendaftaran)</span></p>
          <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
            @php $ttv = [
              ['label'=>'Tek. Darah', 'value'=> $antrian->rekamMedis?->tekanan_darah, 'unit'=>''],
              ['label'=>'Suhu', 'value'=> $antrian->rekamMedis?->suhu, 'unit'=>'°C'],
              ['label'=>'Berat', 'value'=> $antrian->rekamMedis?->berat_badan, 'unit'=>'kg'],
              ['label'=>'Tinggi', 'value'=> $antrian->rekamMedis?->tinggi_badan, 'unit'=>'cm'],
              ['label'=>'Nadi', 'value'=> $antrian->rekamMedis?->nadi, 'unit'=>'x/m'],
              ['label'=>'Respirasi', 'value'=> $antrian->rekamMedis?->respirasi, 'unit'=>'x/m'],
            ]; @endphp
            @foreach($ttv as $v)
            <div class="bg-amber-50 rounded-xl p-3 border border-amber-100 text-center">
              <p class="text-[9px] text-amber-600 font-bold uppercase mb-1">{{ $v['label'] }}</p>
              <p class="font-bold text-gray-800 text-sm">{{ $v['value'] ? $v['value'].' '.$v['unit'] : '—' }}</p>
            </div>
            @endforeach
          </div>
          {{-- Hidden inputs TTV --}}
          <input type="hidden" name="tekanan_darah" value="{{ $antrian->rekamMedis?->tekanan_darah }}">
          <input type="hidden" name="suhu" value="{{ $antrian->rekamMedis?->suhu }}">
          <input type="hidden" name="berat_badan" value="{{ $antrian->rekamMedis?->berat_badan }}">
          <input type="hidden" name="tinggi_badan" value="{{ $antrian->rekamMedis?->tinggi_badan }}">
          <input type="hidden" name="nadi" value="{{ $antrian->rekamMedis?->nadi }}">
          <input type="hidden" name="respirasi" value="{{ $antrian->rekamMedis?->respirasi }}">
        </div>
      </div>
    </div>
    {{-- Nav --}}
    <div class="flex justify-end mt-4">
      <button type="button" onclick="goToStep(2)" class="px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition shadow-md flex items-center gap-2 text-sm">
        Lanjut ke Diagnosa <i class="fas fa-arrow-right"></i>
      </button>
    </div>
  </div>

  {{-- ══════════════════════════════════════════ --}}
  {{-- STEP 2: Diagnosa ICD-10                   --}}
  {{-- ══════════════════════════════════════════ --}}
  <div class="step-panel" id="panel-2">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="step-header-card px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
          <i class="fas fa-stethoscope text-red-600"></i>
        </div>
        <div>
          <h3 class="font-bold text-gray-800">Langkah 2 dari 4 — Diagnosa Penyakit (ICD-10)</h3>
          <p class="text-xs text-gray-500 mt-0.5">Cari dan pilih kode diagnosa ICD-10 sesuai kondisi pasien</p>
        </div>
      </div>
      <div class="p-6 space-y-5">

        {{-- Panduan singkat --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 items-start">
          <i class="fas fa-lightbulb text-blue-500 mt-0.5"></i>
          <div class="text-sm text-blue-800">
            <strong>Cara mengisi:</strong> Ketik kode ICD-10 (misal: <code class="bg-blue-100 px-1 rounded">A09</code>) atau nama penyakit (misal: <code class="bg-blue-100 px-1 rounded">diare</code>) pada kolom pencarian. Anda bisa menambahkan lebih dari satu diagnosa jika pasien memiliki kondisi penyerta.
          </div>
        </div>

        <div id="diagnosa-container" class="space-y-3">
          <div class="diagnosa-item bg-slate-50 p-4 rounded-xl border border-slate-200 relative">
            <p class="text-xs font-bold text-slate-500 uppercase mb-2"><i class="fas fa-search mr-1 text-slate-400"></i>Diagnosa #1</p>
            <div class="relative">
              <input type="hidden" name="diagnosa[]" class="diagnosa-id" required>
              <input type="text"
                     class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white diagnosa-search font-medium text-sm"
                     placeholder="Ketik kode ICD atau nama penyakit (misal: A00, demam berdarah)..."
                     oninput="searchIcdx(this)"
                     onfocus="searchIcdx(this)"
                     autocomplete="off">
              <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg icdx-dropdown divide-y divide-slate-100"></div>
            </div>
            <button type="button" onclick="removeDiagnosa(this)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 remove-diagnosa-btn hidden items-center justify-center shadow-md">
              <i class="fas fa-times text-xs"></i>
            </button>
          </div>
        </div>

        <button type="button" onclick="addDiagnosa()" class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-semibold text-sm border border-blue-100">
          <i class="fas fa-plus-circle"></i> Tambah Diagnosa Penyerta
        </button>

        {{-- Diagnosa Primer --}}
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 mt-2">
          <label class="block text-sm font-bold text-blue-800 mb-2">
            <i class="fas fa-star text-yellow-500 mr-1"></i>Diagnosa Primer <span class="text-red-500">*</span>
          </label>
          <select name="diagnosa_primer" id="diagnosa-primer" class="w-full px-3 py-2.5 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" required>
            <option value="">— Pilih diagnosa primer dari daftar di atas —</option>
          </select>
          <p class="text-[11px] text-blue-600 mt-1.5"><i class="fas fa-info-circle mr-1"></i>Diagnosa primer adalah kondisi utama yang menyebabkan pasien berobat saat ini.</p>
        </div>
      </div>
    </div>
    {{-- Nav --}}
    <div class="flex justify-between mt-4">
      <button type="button" onclick="goToStep(1)" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-sm flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Kembali
      </button>
      <button type="button" onclick="goToStep(3)" class="px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition shadow-md flex items-center gap-2 text-sm">
        Lanjut ke Resep Obat <i class="fas fa-arrow-right"></i>
      </button>
    </div>
  </div>

  {{-- ══════════════════════════════════════════ --}}
  {{-- STEP 3: Resep Obat Digital                --}}
  {{-- ══════════════════════════════════════════ --}}
  <div class="step-panel" id="panel-3">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="step-header-card px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
          <i class="fas fa-pills text-purple-600"></i>
        </div>
        <div>
          <h3 class="font-bold text-gray-800">Langkah 3 dari 4 — Resep Obat Digital</h3>
          <p class="text-xs text-gray-500 mt-0.5">Buat resep digital yang akan diteruskan otomatis ke meja apoteker</p>
        </div>
      </div>
      <div class="p-6 space-y-5">

        <div class="bg-purple-50/60 p-4 rounded-xl border border-purple-100">
          <label class="block text-sm font-bold text-purple-800 mb-3">Apakah pasien membutuhkan resep obat?</label>
          <div class="flex gap-6">
            <label class="inline-flex items-center cursor-pointer">
              <input type="radio" name="pakai_resep" value="Ya" class="form-radio text-purple-600 w-4 h-4" onchange="toggleResep(true)">
              <span class="ml-2 font-semibold text-sm text-slate-700">Ya, Buat Resep Digital</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
              <input type="radio" name="pakai_resep" value="Tidak" class="form-radio text-purple-600 w-4 h-4" checked onchange="toggleResep(false)">
              <span class="ml-2 font-semibold text-sm text-slate-700">Tidak Perlu Resep</span>
            </label>
          </div>
          <p class="text-[11px] text-purple-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Jika memilih Ya, resep akan dikirim otomatis ke apoteker untuk diproses.</p>
        </div>

        <div id="section-resep" class="hidden space-y-4">
          <h4 class="font-bold text-slate-700 text-sm flex items-center gap-2"><i class="fas fa-prescription-bottle-alt text-purple-500"></i> Daftar Obat yang Diresepkan</h4>
          <div id="obat-container" class="space-y-3">
            <div class="border border-slate-200 rounded-xl p-4 obat-item bg-slate-50/50 relative">
              <p class="text-xs font-bold text-slate-400 uppercase mb-3">Obat #1</p>
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Nama Obat</label>
                  <div class="relative">
                    <input type="hidden" name="obat_id[]" class="obat-id" required>
                    <input type="text"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white obat-search font-medium text-sm"
                           placeholder="Ketik nama obat..."
                           oninput="searchObat(this)"
                           onfocus="searchObat(this)"
                           autocomplete="off">
                    <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg obat-dropdown divide-y divide-slate-100"></div>
                  </div>
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
                    <i class="fas fa-trash-alt"></i> Hapus
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
                  <input type="text" name="keterangan[]" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-medium text-sm" placeholder="Contoh: habiskan, jangan dihentikan">
                </div>
              </div>
            </div>
          </div>
          <button type="button" onclick="addObat()" class="flex items-center gap-2 px-4 py-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition font-semibold text-sm border border-purple-100">
            <i class="fas fa-plus"></i> Tambah Obat Lain
          </button>
        </div>

        <div id="section-catatan-resep" class="hidden">
          <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Catatan Dokter untuk Apoteker</label>
          <textarea name="catatan_dokter" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm" placeholder="Instruksi khusus untuk apoteker (opsional)..."></textarea>
        </div>

        {{-- Jika tidak pakai resep --}}
        <div id="section-no-resep" class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center text-gray-500 text-sm">
          <i class="fas fa-check-circle text-green-500 mr-2 text-lg"></i>Tidak ada resep untuk pasien ini. Lanjutkan ke tahap finalisasi.
        </div>
      </div>
    </div>
    {{-- Nav --}}
    <div class="flex justify-between mt-4">
      <button type="button" onclick="goToStep(2)" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-sm flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Kembali
      </button>
      <button type="button" onclick="goToStep(4)" class="px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition shadow-md flex items-center gap-2 text-sm">
        Lanjut ke Finalisasi <i class="fas fa-arrow-right"></i>
      </button>
    </div>
  </div>

  {{-- ══════════════════════════════════════════ --}}
  {{-- STEP 4: Finalisasi & Submit               --}}
  {{-- ══════════════════════════════════════════ --}}
  <div class="step-panel" id="panel-4">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- Kolom Kiri: Status & Pelayanan + Tindakan --}}
      <div class="space-y-6">
        {{-- Status & Pelayanan --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="step-header-card px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
              <i class="fas fa-file-medical text-slate-600"></i>
            </div>
            <div>
              <h3 class="font-bold text-gray-800">Status & Pelayanan</h3>
              <p class="text-xs text-gray-500 mt-0.5">Tentukan kasus dan status pasien</p>
            </div>
          </div>
          <div class="p-6 space-y-4">
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
        </div>

        {{-- Rencana & Tindakan --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="step-header-card px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
              <i class="fas fa-user-md text-emerald-600"></i>
            </div>
            <div>
              <h3 class="font-bold text-gray-800">Rencana & Tindakan Medis</h3>
              <p class="text-xs text-gray-500 mt-0.5">Tindakan, terapi, dan prognosa</p>
            </div>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tindakan Medis</label>
              <textarea name="tindakan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm" placeholder="Tindakan medis yang dilakukan (misal: injeksi, nebulisasi, dll)...">{{ old('tindakan') }}</textarea>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Rencana Pengobatan / Terapi</label>
              <textarea name="pengobatan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm" placeholder="Rencana pengobatan yang diberikan...">{{ old('pengobatan') }}</textarea>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Prognosa</label>
              <textarea name="prognosa" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm" placeholder="Prognosa (misal: Bonam, Dubia, Malam)...">{{ old('prognosa') }}</textarea>
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
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Rujukan Ke</label>
              <input type="text" name="rujukan_ke" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm" placeholder="Nama faskes tujuan rujukan (kosongkan jika tidak dirujuk)...">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Catatan Tambahan</label>
              <textarea name="catatan" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm" placeholder="Catatan lain untuk rekam medis...">{{ old('catatan') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- Kolom Kanan: Rawat Inap + Ringkasan --}}
      <div class="space-y-6">
        {{-- Rawat Inap --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="step-header-card px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
              <i class="fas fa-bed text-blue-600"></i>
            </div>
            <div>
              <h3 class="font-bold text-gray-800">Rekomendasi Rawat Inap</h3>
              <p class="text-xs text-gray-500 mt-0.5">Jika pasien membutuhkan mondok</p>
            </div>
          </div>
          <div class="p-6">
            @php
              $availableClasses = App\Models\Kamar::where('status','Tersedia')->whereRaw('terisi < kapasitas')->select('kelas')->distinct()->pluck('kelas');
              $hasAvailableRooms = !$availableClasses->isEmpty();
            @endphp
            <label class="flex items-start gap-3 {{ $hasAvailableRooms ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }} p-4 border border-blue-100 rounded-xl bg-blue-50/40 hover:bg-blue-50 transition">
              <input type="checkbox" name="is_rekomendasi_rawat_inap" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300 mt-0.5"
                {{ old('is_rekomendasi_rawat_inap', $antrian->rekamMedis?->is_rekomendasi_rawat_inap) ? 'checked' : '' }}
                {{ !$hasAvailableRooms ? 'disabled' : '' }}
                onchange="toggleRekomendasi(this)">
              <div>
                <span class="block font-bold text-blue-900 text-sm">Rekomendasikan Rawat Inap</span>
                <span class="block text-xs text-blue-600 mt-0.5">{{ $hasAvailableRooms ? 'Centang jika pasien membutuhkan layanan mondok.' : 'Semua kamar penuh — tidak tersedia saat ini.' }}</span>
              </div>
            </label>

            <div class="mt-4">
              <p class="text-xs font-bold text-gray-500 uppercase mb-2">Kamar Tersedia Saat Ini:</p>
              <div class="flex flex-wrap gap-2">
                @if(!$hasAvailableRooms)
                  <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Tidak ada kamar kosong</span>
                @else
                  @foreach($availableClasses as $kelas)
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check mr-1"></i>{{ $kelas }}</span>
                  @endforeach
                @endif
              </div>
              <p class="text-xs text-gray-400 mt-2">{{ $hasAvailableRooms ? 'Sampaikan kepada keluarga untuk menentukan kelas kamar di bagian resepsionis.' : 'Tidak ada kamar yang tersedia saat ini.' }}</p>
            </div>
          </div>
        </div>

        {{-- Ringkasan Pemeriksaan (preview) --}}
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl shadow-lg p-6 text-white">
          <h3 class="font-bold text-white mb-4 flex items-center gap-2"><i class="fas fa-clipboard-list text-blue-300"></i> Ringkasan Pemeriksaan</h3>
          <div class="space-y-3 text-sm">
            <div class="flex gap-2">
              <i class="fas fa-check-circle text-green-400 mt-0.5 flex-shrink-0"></i>
              <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold">Anamnesis</p>
                <p class="text-white/90" id="summary-anamnesis">—</p>
              </div>
            </div>
            <div class="flex gap-2">
              <i class="fas fa-check-circle text-green-400 mt-0.5 flex-shrink-0"></i>
              <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold">Diagnosa</p>
                <p class="text-white/90" id="summary-diagnosa">—</p>
              </div>
            </div>
            <div class="flex gap-2">
              <i class="fas fa-check-circle text-green-400 mt-0.5 flex-shrink-0"></i>
              <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold">Resep</p>
                <p class="text-white/90" id="summary-resep">—</p>
              </div>
            </div>
          </div>
          <p class="text-xs text-slate-500 mt-4 border-t border-slate-700 pt-3">Periksa kembali semua data sebelum menyimpan.</p>
        </div>
      </div>
    </div>

    {{-- Nav + Submit --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm flex flex-col sm:flex-row justify-between gap-3 mt-4">
      <button type="button" onclick="goToStep(3)" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition text-sm flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Kembali
      </button>
      <div class="flex gap-3">
        <a href="{{ route('dokter.antrian') }}" class="px-6 py-3 bg-white border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-center text-sm">
          Batal
        </a>
        <button type="submit" class="px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition shadow-md flex items-center gap-2 text-sm">
          <i class="fas fa-save"></i> Simpan & Selesai
        </button>
      </div>
    </div>
  </div>

</form>

@endsection

@push('scripts')
<script>
// ── Data Preload ──────────────────────────────────────────
const icdxData = @json(\App\Models\Icdx::select('id', 'kode', 'nama')->orderBy('kode')->get());

@php
$obatArray = $obats->map(function($o) {
  return ['id' => $o->id, 'nama' => $o->nama, 'stok' => $o->stok, 'is_fornas' => $o->is_fornas];
})->toArray();
@endphp
const obatData = @json($obatArray);

// ── Step Navigation ───────────────────────────────────────
let currentStep = 1;
const totalSteps = 4;

function goToStep(step) {
  // Validasi step sebelum maju
  if (step > currentStep) {
    if (!validateStep(currentStep)) return;
  }

  // Sembunyikan panel aktif
  document.getElementById('panel-' + currentStep).classList.remove('active');
  updateIndicator(currentStep, step > currentStep ? 'done' : '');

  currentStep = step;

  // Tampilkan panel baru
  document.getElementById('panel-' + currentStep).classList.add('active');
  updateIndicator(currentStep, 'active');

  // Scroll ke atas
  window.scrollTo({ top: 0, behavior: 'smooth' });

  // Update ringkasan di step 4
  if (currentStep === 4) updateSummary();
}

function updateIndicator(step, state) {
  const el = document.getElementById('step-indicator-' + step);
  el.classList.remove('active', 'done');
  if (state) el.classList.add(state);
}

function validateStep(step) {
  if (step === 1) {
    const anamnesis = document.getElementById('anamnesis').value.trim();
    if (!anamnesis) {
      showStepError('Anamnesis wajib diisi sebelum melanjutkan!');
      document.getElementById('anamnesis').focus();
      return false;
    }
  }
  if (step === 2) {
    const primerVal = document.getElementById('diagnosa-primer').value;
    if (!primerVal) {
      showStepError('Pilih minimal satu diagnosa dan tentukan diagnosa primernya!');
      return false;
    }
  }
  return true;
}

function showStepError(msg) {
  // Hapus notif lama
  const old = document.getElementById('step-error-toast');
  if (old) old.remove();

  const toast = document.createElement('div');
  toast.id = 'step-error-toast';
  toast.className = 'fixed top-5 left-1/2 -translate-x-1/2 z-[200] bg-red-600 text-white text-sm font-semibold px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 transition-all';
  toast.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${msg}`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3500);
}

function updateSummary() {
  // Anamnesis
  const an = document.getElementById('anamnesis').value.trim();
  document.getElementById('summary-anamnesis').textContent = an ? (an.length > 80 ? an.slice(0, 80) + '...' : an) : '—';

  // Diagnosa
  const diagnosas = [];
  document.querySelectorAll('.diagnosa-search').forEach(inp => {
    if (inp.value) diagnosas.push(inp.value.split(' - ')[0]); // hanya kode
  });
  document.getElementById('summary-diagnosa').textContent = diagnosas.length ? diagnosas.join(', ') : '—';

  // Resep
  const pakai = document.querySelector('input[name="pakai_resep"]:checked')?.value;
  if (pakai === 'Ya') {
    const obats = [];
    document.querySelectorAll('.obat-search').forEach(inp => { if (inp.value) obats.push(inp.value); });
    document.getElementById('summary-resep').textContent = obats.length ? obats.join(', ') : 'Ya (belum diisi obat)';
  } else {
    document.getElementById('summary-resep').textContent = 'Tidak ada resep';
  }
}

// ── ICD-10 Search ─────────────────────────────────────────
function searchIcdx(input) {
  const query = input.value.toLowerCase().trim();
  const dropdown = input.nextElementSibling;
  dropdown.innerHTML = '';

  if (!query) {
    dropdown.classList.add('hidden');
    const item = input.closest('.diagnosa-item');
    const hiddenId = item.querySelector('.diagnosa-id');
    if (hiddenId) hiddenId.value = '';
    updateDiagnosaPrimer();
    return;
  }

  const matches = [];
  for (let i = 0; i < icdxData.length; i++) {
    const item = icdxData[i];
    if (item.kode.toLowerCase().includes(query) || item.nama.toLowerCase().includes(query)) {
      matches.push(item);
      if (matches.length >= 10) break;
    }
  }

  if (matches.length === 0) {
    dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500 font-medium">Tidak ada hasil cocok</div>';
    dropdown.classList.remove('hidden');
    return;
  }

  matches.forEach(item => {
    const option = document.createElement('div');
    option.className = 'px-4 py-2.5 hover:bg-blue-50 text-sm text-slate-700 cursor-pointer font-medium transition flex justify-between items-center';
    option.innerHTML = `<span><strong class="text-blue-600">${item.kode}</strong> - ${item.nama}</span><i class="fas fa-plus text-xs text-slate-300"></i>`;
    option.onclick = function() {
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

function searchObat(input) {
  const query = input.value.toLowerCase().trim();
  const dropdown = input.nextElementSibling;
  dropdown.innerHTML = '';

  if (!query) {
    dropdown.classList.add('hidden');
    const item = input.closest('.obat-item');
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
    dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500 font-medium">Tidak ada obat cocok</div>';
    dropdown.classList.remove('hidden');
    return;
  }

  matches.forEach(item => {
    const option = document.createElement('div');
    option.className = 'px-4 py-2.5 hover:bg-purple-50 text-sm text-slate-700 cursor-pointer font-medium transition flex justify-between items-center';
    let fornasBadge = item.is_fornas ? `<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase ml-2 border border-green-200">Fornas</span>` : '';
    option.innerHTML = `<div class="flex items-center"><span>${item.nama}</span>${fornasBadge}</div><span class="text-xs text-slate-400">Stok: ${item.stok}</span>`;
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
  if (!e.target.classList.contains('diagnosa-search') && !e.target.closest('.icdx-dropdown')) {
    document.querySelectorAll('.icdx-dropdown').forEach(d => d.classList.add('hidden'));
  }
  if (!e.target.classList.contains('obat-search') && !e.target.closest('.obat-dropdown')) {
    document.querySelectorAll('.obat-dropdown').forEach(d => d.classList.add('hidden'));
  }
});

// ── Diagnosa CRUD ─────────────────────────────────────────
function addDiagnosa() {
  const container = document.getElementById('diagnosa-container');
  const items = container.querySelectorAll('.diagnosa-item');
  const newItem = items[0].cloneNode(true);
  const num = items.length + 1;

  newItem.querySelector('p').textContent = `Diagnosa #${num}`;

  const hiddenInput = newItem.querySelector('.diagnosa-id');
  if (hiddenInput) hiddenInput.value = '';

  const searchInput = newItem.querySelector('.diagnosa-search');
  if (searchInput) {
    searchInput.value = '';
    searchInput.oninput = function() { searchIcdx(this); };
    searchInput.onfocus = function() { searchIcdx(this); };
  }

  const dropdown = newItem.querySelector('.icdx-dropdown');
  if (dropdown) { dropdown.innerHTML = ''; dropdown.classList.add('hidden'); }

  const removeBtn = newItem.querySelector('.remove-diagnosa-btn');
  if (removeBtn) { removeBtn.classList.remove('hidden'); removeBtn.classList.add('flex'); }

  container.appendChild(newItem);
  updateDiagnosaPrimer();
}

function removeDiagnosa(button) {
  const item = button.closest('.diagnosa-item');
  const container = document.getElementById('diagnosa-container');
  if (container.querySelectorAll('.diagnosa-item').length > 1) {
    item.remove();
    updateDiagnosaPrimer();
  }
}

function updateDiagnosaPrimer() {
  const primerSelect = document.getElementById('diagnosa-primer');
  const currentVal = primerSelect.value;
  primerSelect.innerHTML = '<option value="">— Pilih diagnosa primer dari daftar di atas —</option>';

  document.querySelectorAll('.diagnosa-item').forEach(item => {
    const hiddenId = item.querySelector('.diagnosa-id').value;
    const searchVal = item.querySelector('.diagnosa-search').value;
    if (hiddenId && searchVal) {
      const option = new Option(searchVal, hiddenId);
      if (hiddenId === currentVal) option.selected = true;
      primerSelect.appendChild(option);
    }
  });
}

// ── Resep ─────────────────────────────────────────────────
function toggleResep(show) {
  document.getElementById('section-resep').classList.toggle('hidden', !show);
  document.getElementById('section-catatan-resep').classList.toggle('hidden', !show);
  document.getElementById('section-no-resep').classList.toggle('hidden', show);

  // Required attribute pada obat-id
  document.querySelectorAll('.obat-id').forEach(inp => {
    inp.required = show;
  });
}

// Init resep state
toggleResep(false);

function addObat() {
  const container = document.getElementById('obat-container');
  const items = container.querySelectorAll('.obat-item');
  const newItem = items[0].cloneNode(true);
  const num = items.length + 1;

  newItem.querySelector('p').textContent = `Obat #${num}`;

  newItem.querySelectorAll('input, select').forEach(input => { input.value = ''; });
  const hiddenInput = newItem.querySelector('.obat-id');
  if (hiddenInput) hiddenInput.value = '';

  const searchInput = newItem.querySelector('.obat-search');
  if (searchInput) {
    searchInput.value = '';
    searchInput.oninput = function() { searchObat(this); };
    searchInput.onfocus = function() { searchObat(this); };
  }

  const dropdown = newItem.querySelector('.obat-dropdown');
  if (dropdown) { dropdown.innerHTML = ''; dropdown.classList.add('hidden'); }

  const selectAturan = newItem.querySelector('.aturan-pakai-select');
  const customAturan = newItem.querySelector('.aturan-pakai-custom');
  if (selectAturan && customAturan) {
    selectAturan.value = 'Sesudah makan';
    selectAturan.setAttribute('name', 'aturan_pakai[]');
    customAturan.value = '';
    customAturan.removeAttribute('name');
    customAturan.classList.add('hidden');
  }

  const removeBtn = newItem.querySelector('.remove-obat-btn');
  if (removeBtn) { removeBtn.classList.remove('hidden'); removeBtn.classList.add('flex'); }

  container.appendChild(newItem);
}

function removeObat(button) {
  const item = button.closest('.obat-item');
  const container = document.getElementById('obat-container');
  if (container.querySelectorAll('.obat-item').length > 1) item.remove();
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

function toggleRekomendasi(checkbox) {
  if (checkbox.disabled) { checkbox.checked = false; return; }
}

document.addEventListener('DOMContentLoaded', function() {
  updateDiagnosaPrimer();
});
</script>
@endpush
