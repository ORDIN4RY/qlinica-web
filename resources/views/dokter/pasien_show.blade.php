@extends('layouts.app')

@section('title', 'Buku Rekam Medis - ' . $pasien->nama)
@section('page-title', 'Buku Rekam Medis')
@section('page-subtitle', 'Riwayat medis komprehensif pasien')

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-5">
  <a href="{{ route('dokter.pasien') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm">
    <i class="fas fa-arrow-left"></i> Kembali ke Data Pasien
  </a>
</div>

{{-- Profil Singkat Pasien --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
  <div class="bg-[#1e3a8a] px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-white text-xl font-bold">
        {{ strtoupper(substr($pasien->nama ?? '?', 0, 1)) }}
      </div>
      <div>
        <h2 class="text-lg font-bold text-white">{{ $pasien->nama }}</h2>
        <p class="text-blue-300 text-sm font-mono mt-0.5"><i class="fas fa-id-card mr-1"></i> No. RM: {{ $pasien->no_rm }}</p>
      </div>
    </div>
    <div class="hidden md:flex gap-3">
      @if($pasien->jenis_kelamin === 'L')
        <span class="px-3 py-1 bg-blue-500/20 text-blue-300 text-xs font-bold rounded-lg border border-blue-500/30 flex items-center gap-1.5"><i class="fas fa-mars"></i> Laki-laki</span>
      @else
        <span class="px-3 py-1 bg-pink-500/20 text-pink-300 text-xs font-bold rounded-lg border border-pink-500/30 flex items-center gap-1.5"><i class="fas fa-venus"></i> Perempuan</span>
      @endif
      <span class="px-3 py-1 bg-white/10 text-white/90 text-xs font-bold rounded-lg border border-white/20">{{ $pasien->umur }} Tahun</span>
    </div>
  </div>
  
  <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
    <div>
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Gol. Darah</p>
      <p class="font-bold text-red-600 text-lg flex items-center gap-2">
        <i class="fas fa-tint"></i> {{ $pasien->golongan_darah ?? '-' }}
      </p>
    </div>
    <div>
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">NIK</p>
      <p class="font-semibold text-gray-800 text-sm">{{ $pasien->nik ?? '-' }}</p>
    </div>
    <div class="col-span-2 md:col-span-2">
      <p class="text-[10px] uppercase font-bold text-gray-400 mb-1 tracking-wider">Riwayat Alergi</p>
      @if($pasien->riwayat_alergi)
        <p class="font-semibold text-red-600 text-sm flex items-start gap-1.5">
          <i class="fas fa-exclamation-triangle mt-0.5"></i> {{ $pasien->riwayat_alergi }}
        </p>
      @else
        <p class="font-semibold text-gray-500 text-sm">Tidak ada riwayat alergi</p>
      @endif
    </div>
  </div>
</div>

{{-- Timeline Riwayat Kunjungan --}}
<h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
  <i class="fas fa-history text-blue-500"></i> Riwayat Kunjungan & Diagnosa
</h3>

@if($riwayatMedis->isEmpty())
  <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
      <i class="fas fa-file-medical text-3xl text-gray-300"></i>
    </div>
    <h4 class="font-bold text-gray-800 text-lg mb-1">Belum Ada Rekam Medis</h4>
    <p class="text-sm text-gray-500 max-w-sm mx-auto">Pasien ini belum pernah melakukan pemeriksaan atau diagnosa sebelumnya di klinik ini.</p>
  </div>
@else
  <div class="space-y-6">
    @foreach($riwayatMedis as $rm)
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col md:flex-row">
        
        {{-- Bagian Kiri: Tanggal & Info Dasar --}}
        <div class="bg-slate-50 border-r border-gray-200 p-6 md:w-1/4 shrink-0 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-2 text-xs font-bold text-blue-600 bg-blue-100/50 w-max px-3 py-1.5 rounded-lg mb-3">
              <i class="fas fa-calendar-check"></i>
              {{ \Carbon\Carbon::parse($rm->tanggal_periksa)->translatedFormat('d F Y') }}
            </div>
            
            <p class="text-[10px] uppercase font-bold text-gray-400 mb-0.5">Dokter Pemeriksa</p>
            <p class="font-semibold text-sm text-gray-800 flex items-center gap-2 mb-4">
              <i class="fas fa-user-md text-slate-400"></i> {{ $rm->dokter->nama ?? 'Tidak diketahui' }}
            </p>

            <div class="grid grid-cols-2 gap-3 mb-4">
              <div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-[9px] uppercase font-bold text-gray-400">Tekanan Darah</p>
                <p class="font-bold text-xs text-gray-800 mt-0.5">{{ $rm->tekanan_darah ?? '-' }}</p>
              </div>
              <div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-[9px] uppercase font-bold text-gray-400">Suhu Badan</p>
                <p class="font-bold text-xs text-gray-800 mt-0.5">{{ $rm->suhu ? $rm->suhu . ' °C' : '-' }}</p>
              </div>
            </div>
          </div>
          
          <div class="text-[10px] text-gray-400 font-mono">
            Kunjungan ID: #{{ $rm->id }}
          </div>
        </div>

        {{-- Bagian Kanan: Detail Medis --}}
        <div class="p-6 md:w-3/4 flex flex-col gap-5">
          
          {{-- Anamnesis & Fisik --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <p class="text-xs font-bold text-gray-800 mb-1.5 flex items-center gap-1.5">
                <i class="fas fa-comment-medical text-blue-500"></i> Keluhan / Anamnesis
              </p>
              <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100 leading-relaxed min-h-[80px]">
                {{ $rm->anamnesis ?? 'Tidak ada catatan keluhan.' }}
              </p>
            </div>
            <div>
              <p class="text-xs font-bold text-gray-800 mb-1.5 flex items-center gap-1.5">
                <i class="fas fa-stethoscope text-emerald-500"></i> Pemeriksaan Fisik
              </p>
              <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100 leading-relaxed min-h-[80px]">
                {{ $rm->pemeriksaan_fisik ?? 'Tidak ada catatan pemeriksaan fisik.' }}
              </p>
            </div>
          </div>

          {{-- Diagnosa ICD-X --}}
          <div>
            <p class="text-xs font-bold text-gray-800 mb-2 flex items-center gap-1.5">
              <i class="fas fa-biohazard text-red-500"></i> Diagnosa (ICD-X)
            </p>
            @if($rm->diagnosa->isEmpty())
              <span class="text-xs text-gray-500 italic bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">Belum ada diagnosa</span>
            @else
              <div class="flex flex-wrap gap-2">
                @foreach($rm->diagnosa as $diag)
                  <div class="flex items-center gap-2 bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg">
                    <span class="text-[10px] font-bold text-red-700 bg-red-200/50 px-1.5 py-0.5 rounded">{{ $diag->icdx->kode ?? '-' }}</span>
                    <span class="text-xs font-semibold text-red-900">{{ $diag->icdx->nama ?? 'Tidak diketahui' }}</span>
                    @if($diag->jenis === 'Primer')
                      <span class="text-[9px] bg-red-600 text-white font-bold px-1.5 py-0.5 rounded-full ml-1">Primer</span>
                    @endif
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          {{-- Resep Obat --}}
          <div>
            <p class="text-xs font-bold text-gray-800 mb-2 flex items-center gap-1.5">
              <i class="fas fa-pills text-purple-500"></i> Resep Diberikan
            </p>
            @if(!$rm->resep || $rm->resep->details->isEmpty())
              <span class="text-xs text-gray-500 italic bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">Tidak ada resep obat</span>
            @else
              <div class="bg-purple-50/30 border border-purple-100 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                  <thead class="bg-purple-50 text-purple-800 font-bold border-b border-purple-100">
                    <tr>
                      <th class="px-4 py-2">Nama Obat</th>
                      <th class="px-4 py-2 text-center">Jumlah</th>
                      <th class="px-4 py-2">Dosis & Aturan</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-purple-100/50">
                    @foreach($rm->resep->details as $detail)
                      <tr>
                        <td class="px-4 py-2 font-semibold text-gray-800">{{ $detail->obat->nama ?? 'Obat Terhapus' }}</td>
                        <td class="px-4 py-2 text-center font-mono font-bold text-purple-600">{{ $detail->jumlah }}</td>
                        <td class="px-4 py-2 text-gray-600">
                          {{ $detail->dosis ?? '-' }} 
                          @if($detail->aturan_pakai) <span class="mx-1 text-gray-300">|</span> <em>{{ $detail->aturan_pakai }}</em> @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

        </div>
      </div>
    @endforeach
  </div>
@endif

@endsection
