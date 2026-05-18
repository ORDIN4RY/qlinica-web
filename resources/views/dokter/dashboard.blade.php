@extends('layouts.app')

@section('title', 'Dashboard Dokter')
@section('page-title', 'Dashboard Dokter')
@section('page-subtitle', 'Selamat datang, Dr. {{ Auth::user()?->name ?? "Dokter" }}')

@section('content')
@php
  $activeQueues = \App\Models\Antrian::with(['pasien', 'rekamMedis'])
      ->where('tanggal', now()->toDateString())
      ->whereIn('status', ['Dipanggil', 'Dilayani'])
      ->orderBy('no_antrian')
      ->get();

  $pegawai = auth()->user()->pegawai;
  $topPenyakit = \App\Models\RekamMedisDiagnosa::select('icdx_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as n'))
      ->whereHas('rekamMedis', function($q) use ($pegawai) {
          if ($pegawai) {
              $q->where('dokter_id', $pegawai->id);
          }
      })
      ->groupBy('icdx_id')
      ->orderByDesc('n')
      ->limit(5)
      ->with('icdx')
      ->get();
@endphp
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

  <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col">
    <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center justify-between">
      <span class="flex items-center gap-2">
        <i class="fas fa-chart-pie text-blue-500"></i> Penyakit Terbanyak (Kasus Anda)
      </span>
    </h2>
    <div class="flex-1 overflow-y-auto pr-1">
      @if($topPenyakit->isEmpty())
        <div class="text-center py-10 text-gray-400">
          <i class="fas fa-clipboard-list text-4xl mb-3 text-blue-200"></i>
          <p class="font-semibold text-sm text-gray-700">Belum ada data diagnosa</p>
          <p class="text-xs text-gray-400 mt-1">Anda belum mencatat riwayat diagnosa ICD-X.</p>
        </div>
      @else
        <div class="space-y-3">
          @foreach($topPenyakit as $index => $penyakit)
            <div class="p-3 bg-blue-50/50 hover:bg-blue-50 border border-blue-100 rounded-xl transition flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-sm shrink-0">{{ $index + 1 }}</span>
                <div>
                  <h4 class="font-bold text-sm text-gray-800 line-clamp-1" title="{{ $penyakit->icdx->nama ?? 'Tidak diketahui' }}">{{ $penyakit->icdx->nama ?? 'Tidak diketahui' }}</h4>
                  <p class="text-xs text-gray-500 font-mono mt-0.5">ICD-X: {{ $penyakit->icdx->kode ?? '-' }}</p>
                </div>
              </div>
              <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1.5 rounded-lg shrink-0">{{ $penyakit->n }} Kasus</span>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col">
    <h2 class="font-bold text-lg text-gray-800 mb-4 flex items-center justify-between">
      <span class="flex items-center gap-2">
        <i class="fas fa-user-clock text-emerald-500"></i> Pasien Aktif Saat Ini
      </span>
      <span class="text-xs bg-emerald-50 text-emerald-600 font-semibold px-2.5 py-1 rounded-full border border-emerald-100 flex items-center gap-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live
      </span>
    </h2>
    <div class="flex-1 overflow-y-auto max-h-[300px] pr-1">
      @if($activeQueues->isEmpty())
        <div class="text-center py-10 text-gray-400">
          <i class="fas fa-check-circle text-4xl mb-3 text-emerald-500 opacity-80"></i>
          <p class="font-semibold text-sm text-gray-700">Tidak ada pasien aktif</p>
          <p class="text-xs text-gray-400 mt-1 max-w-[280px] mx-auto">Semua pasien selesai dilayani atau menunggu panggilan dari Admin.</p>
        </div>
      @else
        <div class="space-y-3">
          @foreach($activeQueues as $aq)
            <div class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition flex items-center justify-between gap-3">
              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <span class="text-[10px] font-bold font-mono px-2 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-200">#{{ $aq->no_antrian }}</span>
                  <span class="text-[10px] font-semibold px-2 py-0.5 bg-amber-50 text-amber-700 rounded border border-amber-200 capitalize">{{ $aq->status }}</span>
                </div>
                <h4 class="font-semibold text-sm text-gray-800 mt-1.5 truncate">{{ $aq->pasien->nama ?? '—' }}</h4>
                <p class="text-[10px] text-gray-500 mt-0.5 font-mono">RM: {{ $aq->pasien->no_rm ?? '—' }}</p>
              </div>
              <a href="{{ route('dokter.antrian') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shrink-0 shadow-sm">
                Periksa <i class="fas fa-chevron-right text-[9px]"></i>
              </a>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
