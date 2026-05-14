@extends('layouts.app')

@section('title', 'Dashboard Dokter')
@section('page-title', 'Dashboard Dokter')
@section('page-subtitle', 'Selamat datang, Dr. {{ Auth::user()?->name ?? "Dokter" }}')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

  <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 shadow-sm text-white">
    <div class="flex items-center justify-between mb-4">
      <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-stethoscope text-white"></i>
      </div>
      <span class="text-white/60 text-xs font-semibold uppercase tracking-wider">Total</span>
    </div>
    <p class="text-4xl font-bold">{{ $totalRekamMedis }}</p>
    <p class="text-white/80 text-sm mt-1">Rekam Medis</p>
  </div>

  <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 shadow-sm text-white">
    <div class="flex items-center justify-between mb-4">
      <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-user-clock text-white"></i>
      </div>
      <span class="text-white/60 text-xs font-semibold uppercase tracking-wider">Hari ini</span>
    </div>
    <p class="text-4xl font-bold">{{ $antrianHariIni }}</p>
    <p class="text-white/80 text-sm mt-1">Antrian Pasien</p>
  </div>

  <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 shadow-sm text-white">
    <div class="flex items-center justify-between mb-4">
      <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-check-circle text-white"></i>
      </div>
      <span class="text-white/60 text-xs font-semibold uppercase tracking-wider">Hari ini</span>
    </div>
    <p class="text-4xl font-bold">{{ $selesaiHariIni }}</p>
    <p class="text-white/80 text-sm mt-1">Pasien Selesai</p>
  </div>

</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

  <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
    <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
      <i class="fas fa-bolt text-blue-500"></i> Aksi Cepat
    </h2>
    <div class="space-y-3">
      <a href="{{ route('dokter.antrian') }}"
         class="flex items-center gap-3 px-5 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition font-semibold text-sm">
        <i class="fas fa-users"></i> Lihat Antrian Pasien Hari Ini
      </a>
      <a href="{{ route('dokter.pasien') }}"
         class="flex items-center gap-3 px-5 py-3.5 bg-slate-700 hover:bg-slate-800 text-white rounded-xl transition font-semibold text-sm">
        <i class="fas fa-notes-medical"></i> Lihat Data Semua Pasien
      </a>
    </div>
  </div>

  <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6 shadow-sm">
    <h2 class="font-bold text-lg text-slate-800 mb-3 flex items-center gap-2">
      <i class="fas fa-info-circle text-blue-500"></i> Panduan Alur Kerja
    </h2>
    <ol class="space-y-2 text-sm text-slate-700 list-none">
      <li class="flex gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span> Admin mendaftarkan & memanggil pasien</li>
      <li class="flex gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span> Pasien muncul di <strong>Antrian Pasien</strong> dengan status <em>Dipanggil</em></li>
      <li class="flex gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span> Dokter klik <strong>"Diagnosa & Resep"</strong> untuk mengisi form</li>
      <li class="flex gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">4</span> Resep bersifat opsional, pilih <em>Ya</em> jika diperlukan</li>
      <li class="flex gap-2"><span class="w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">5</span> Simpan → status pasien otomatis <strong>Selesai</strong></li>
    </ol>
  </div>

</div>
@endsection
