@extends('layouts.app')

@section('title', 'Antrian Pemeriksaan Pasien')
@section('page-title', 'Antrian Pemeriksaan')
@section('page-subtitle', 'Kelola daftar antrian pasien Anda secara real-time')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
  
  <!-- Total Antrian -->
  <div class="bg-gradient-to-br from-white to-slate-50/50 rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-300 flex items-center justify-between">
    <div>
      <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Antrian</p>
      <p class="text-3xl font-extrabold text-slate-800">{{ $jumlahAntrian }}</p>
    </div>
    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
      <i class="fas fa-users text-lg"></i>
    </div>
  </div>

  <!-- Menunggu -->
  <div class="bg-gradient-to-br from-white to-amber-50/10 rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-300 flex items-center justify-between">
    <div>
      <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Menunggu</p>
      <p class="text-3xl font-extrabold text-amber-600">{{ $antrians->where('status', 'Menunggu')->count() }}</p>
    </div>
    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 border border-amber-100">
      <i class="fas fa-clock text-lg"></i>
    </div>
  </div>

  <!-- Dipanggil -->
  <div class="bg-gradient-to-br from-white to-blue-50/10 rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-300 flex items-center justify-between">
    <div>
      <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dipanggil</p>
      <p class="text-3xl font-extrabold text-blue-600">{{ $Dipanggil }}</p>
    </div>
    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100">
      <i class="fas fa-bullhorn text-lg"></i>
    </div>
  </div>

  <!-- Selesai -->
  <div class="bg-gradient-to-br from-white to-emerald-50/10 rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition duration-300 flex items-center justify-between">
    <div>
      <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Selesai</p>
      <p class="text-3xl font-extrabold text-emerald-600">{{ $selesai }}</p>
    </div>
    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
      <i class="fas fa-check-circle text-lg"></i>
    </div>
  </div>

</div>

<!-- List Queue Container -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
  <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
        Daftar Antrian Hari Ini
        @if($hasAll)
          <span class="text-[10px] font-bold px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full border border-purple-200">Semua Dokter</span>
        @else
          <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full border border-blue-200">Antrian Saya</span>
        @endif
      </h2>
      <p class="text-xs text-slate-400 font-medium mt-0.5">Urutan default: aktif di atas, batal di bawah</p>
    </div>

    {{-- Sort By --}}
    <form method="GET" action="{{ route('dokter.antrian') }}" class="flex items-center gap-2">
      <label class="text-xs font-bold text-slate-500 whitespace-nowrap">Urutkan:</label>
      <select name="sort" onchange="this.form.submit()"
        class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition cursor-pointer">
        <option value="default"     {{ $sortBy === 'default'     ? 'selected' : '' }}>Default (Status + Terbaru)</option>
        <option value="nomor_asc"   {{ $sortBy === 'nomor_asc'   ? 'selected' : '' }}>No. Antrian ↑</option>
        <option value="nomor_desc"  {{ $sortBy === 'nomor_desc'  ? 'selected' : '' }}>No. Antrian ↓</option>
        <option value="nama_asc"    {{ $sortBy === 'nama_asc'    ? 'selected' : '' }}>Nama A–Z</option>
        <option value="nama_desc"   {{ $sortBy === 'nama_desc'   ? 'selected' : '' }}>Nama Z–A</option>
        <option value="status_asc"  {{ $sortBy === 'status_asc'  ? 'selected' : '' }}>Status A–Z</option>
        <option value="status_desc" {{ $sortBy === 'status_desc' ? 'selected' : '' }}>Status Z–A</option>
      </select>
    </form>
  </div>

  @if($antrians->isEmpty())
    <div class="p-12 text-center">
      <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 mx-auto mb-4">
        <i class="fas fa-folder-open text-2xl"></i>
      </div>
      <h3 class="font-bold text-slate-700 text-base">Tidak Ada Antrian</h3>
      <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Semua antrian pasien hari ini kosong atau belum terdaftar.</p>
    </div>
  @else
    <div class="divide-y divide-slate-100">
      @foreach($antrians as $antrian)
        @php
          $statusColors = [
            'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200/50',
            'Dipanggil' => 'bg-blue-50 text-blue-700 border-blue-200/50 animate-pulse',
            'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50',
            'Batal' => 'bg-rose-50 text-rose-700 border-rose-200/50',
          ];
        @endphp

        <div class="p-5 hover:bg-slate-50/50 transition duration-150 {{ $antrian->status === 'Dipanggil' ? 'bg-blue-50/30' : '' }}">
          <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            
            <!-- Left Side: Queue Number & Patient Details -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 min-w-0 flex-1">
              
              <div class="flex items-center gap-4">
                <!-- Queue Number Circle -->
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex flex-col items-center justify-center border font-mono {{ $antrian->status === 'Dipanggil' ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                  <span class="text-[9px] uppercase font-bold tracking-wider leading-none {{ $antrian->status === 'Dipanggil' ? 'text-blue-100' : 'text-slate-400' }}">No</span>
                  <span class="text-xl font-extrabold leading-none mt-1">{{ sprintf("%02d", $antrian->no_antrian) }}</span>
                </div>

                <!-- Small screen wrapper for badge so it shows nicely next to queue number on mobile -->
                <span class="sm:hidden px-3 py-1 rounded-lg text-xs font-bold border {{ $statusColors[$antrian->status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                  {{ $antrian->status }}
                </span>
              </div>

              <!-- Details Grid -->
              <div class="min-w-0 flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6">
                <!-- Col 1: Name & RM -->
                <div class="min-w-0">
                  <h3 class="font-extrabold text-slate-800 truncate text-base hover:text-blue-600 transition">
                    {{ $antrian->pasien?->nama ?? 'Pasien tidak ditemukan' }}
                  </h3>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-semibold text-slate-400"><i class="fas fa-id-card text-[10px]"></i> {{ $antrian->pasien?->no_rm ?? '-' }}</span>
                    @if($antrian->pasien?->jenis_kelamin)
                      <span class="text-slate-300 text-[10px]">•</span>
                      <span class="text-xs font-semibold text-slate-400">
                        @if($antrian->pasien->jenis_kelamin === 'L')
                          <i class="fas fa-mars text-blue-500 text-[10px]"></i> Laki-laki
                        @else
                          <i class="fas fa-venus text-pink-500 text-[10px]"></i> Perempuan
                        @endif
                      </span>
                    @endif
                  </div>
                </div>

                <!-- Col 2: Keluhan -->
                <div class="sm:border-l sm:border-slate-100 sm:pl-4 flex flex-col justify-center min-w-0">
                  <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Keluhan Utama</p>
                  <p class="text-xs text-slate-600 font-medium truncate mt-0.5" title="{{ $antrian->keluhan }}">{{ $antrian->keluhan ?? 'Tidak ada keluhan tertulis' }}</p>
                </div>

                <!-- Col 3: Golongan Darah & Alergi -->
                <div class="sm:border-l sm:border-slate-100 sm:pl-4 flex flex-col justify-center">
                  <div class="flex gap-5">
                    <div>
                      <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Gol. Darah</p>
                      <p class="text-xs font-extrabold text-rose-600 mt-0.5"><i class="fas fa-tint"></i> {{ $antrian->pasien?->golongan_darah ?? '-' }}</p>
                    </div>
                    <div class="min-w-0">
                      <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Alergi</p>
                      <p class="text-xs font-semibold {{ $antrian->pasien?->riwayat_alergi ? 'text-red-500' : 'text-slate-400' }} mt-0.5 truncate max-w-[140px]" title="{{ $antrian->pasien?->riwayat_alergi }}">
                        {{ $antrian->pasien?->riwayat_alergi ?: 'Tidak ada' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Side: Status Badge & Actions -->
            <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-3 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100">
              
              <!-- Hidden on mobile badge since it is wrapping above -->
              <span class="hidden sm:inline-flex px-3 py-1 rounded-lg text-xs font-bold border {{ $statusColors[$antrian->status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                <i class="fas fa-circle text-[5px] mr-1.5 align-middle"></i>{{ $antrian->status }}
              </span>

              @if($antrian->status === 'Menunggu')
                <button disabled class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed">
                  <i class="fas fa-clock"></i> Menunggu Panggilan
                </button>
              @elseif($antrian->status === 'Dipanggil')
                <a href="{{ route('dokter.antrian.periksa', $antrian->id) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white text-xs font-bold rounded-xl transition-all shadow-sm hover:shadow-md shadow-blue-500/10">
                  <i class="fas fa-notes-medical"></i> Periksa & Diagnosa
                </a>
              @elseif($antrian->status === 'Selesai')
                <a href="{{ route('dokter.pasien.show', $antrian->pasien_id) }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition border border-emerald-200/50">
                  <i class="fas fa-clipboard-check"></i> Riwayat Medis
                </a>
              @else
                <span class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-rose-50 text-rose-600 text-xs font-bold rounded-xl border border-rose-200/50">
                  <i class="fas fa-ban"></i> Dibatalkan
                </span>
              @endif
            </div>

          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
