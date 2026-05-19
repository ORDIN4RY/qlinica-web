@extends('layouts.app')

@section('title', 'Resep')
@section('page-title', 'Daftar Resep')
@section('page-subtitle', 'Kelola dan proses resep pasien')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm">
  <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6">
    <div>
      <h2 class="font-bold text-lg text-gray-800">Resep Pasien</h2>
      <p class="text-sm text-gray-500">Kelola proses resep sesuai status dan catatan apoteker.</p>
    </div>
    <form method="GET" action="{{ route('apoteker.resep') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
      <input name="search" value="{{ old('search', $search) }}" type="text" placeholder="Cari nama pasien atau nomor resep..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
      <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
        @foreach(['Semua','Menunggu','Diproses','Selesai','Dibatalkan'] as $option)
          <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
        @endforeach
      </select>
      <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">Filter</button>
    </form>
  </div>

  @if($resepList->isEmpty())
    <div class="p-8 rounded-xl bg-yellow-50 border border-yellow-200 text-yellow-900">
      Tidak ada resep ditemukan untuk kriteria pencarian Anda.
    </div>
  @else
    <div class="space-y-4">
      @foreach($resepList as $resep)
        @php
          $statusClasses = [
            'Menunggu' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'Diproses' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'Menunggu Pembayaran' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'Sudah Dibayar' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'Selesai' => 'bg-green-100 text-green-800 border border-green-200',
            'Dibatalkan' => 'bg-red-100 text-red-800 border border-red-200',
          ];
        @endphp

        <div class="border rounded-lg p-4 bg-white shadow-sm">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
            <div class="min-w-0">
              <h3 class="font-semibold text-gray-800 truncate">#RES-{{ $resep->id }} - {{ $resep->rekamMedis?->pasien?->nama ?? 'Pasien tidak ditemukan' }}</h3>
              <p class="text-sm text-gray-600">Tanggal: {{ optional($resep->rekamMedis?->tanggal_periksa)?->isoFormat('D MMMM YYYY') ?? '-' }} | Dokter: {{ $resep->dokter?->nama ?? 'Dokter tidak ditemukan' }}</p>
            </div>
            <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $statusClasses[$resep->status] ?? 'bg-gray-200 text-gray-800' }}">{{ $resep->status }}</span>
          </div>

          <div class="grid gap-4 md:grid-cols-2 mb-4">
            <div>
              <p class="text-sm text-gray-700 mb-2 font-semibold">Obat yang diminta:</p>
              <ul class="space-y-2 text-sm text-gray-700">
                @foreach($resep->details as $detail)
                  <li class="flex flex-col gap-1">
                    <span class="font-medium">{{ $detail->obat?->nama ?? 'Obat tidak ditemukan' }} @if($detail->obat && $detail->obat->satuan) ({{ $detail->obat->satuan }}) @endif</span>
                    <span class="text-gray-600">Jumlah: {{ $detail->jumlah }} | Dosis: {{ $detail->dosis ?? '–' }} | Aturan: {{ $detail->aturan_pakai ?? '–' }}</span>
                  </li>
                @endforeach
              </ul>
            </div>
            <div class="space-y-3 text-sm text-gray-700">
              @if($resep->catatan_dokter)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                  <p class="font-semibold text-gray-800">Catatan Dokter</p>
                  <p>{{ $resep->catatan_dokter }}</p>
                </div>
              @endif

              @if($resep->catatan_apoteker)
                <div class="p-3 bg-green-50 rounded-xl border border-green-100">
                  <p class="font-semibold text-green-900">Catatan Apoteker</p>
                  <p>{{ $resep->catatan_apoteker }}</p>
                </div>
              @endif

              <div class="text-xs text-gray-500">
                <p>Diproses: {{ optional($resep->diproses_at)?->diffForHumans() ?? '-' }}</p>
                <p>Selesai: {{ optional($resep->selesai_at)?->diffForHumans() ?? '-' }}</p>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            @if($resep->status === 'Menunggu')
              <form action="{{ route('apoteker.resep.update', $resep) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="proses">
                <button type="submit" class="px-4 py-2 bg-blue-900 text-white text-sm rounded-lg hover:bg-blue-800 transition font-semibold shadow-sm">Kalkulasi Harga & Tagih</button>
              </form>
              <form action="{{ route('apoteker.resep.update', $resep) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="kembalikan">
                <button type="submit" class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg hover:bg-red-100 transition">Kembalikan</button>
              </form>
            @elseif($resep->status === 'Menunggu Pembayaran')
              <span class="inline-flex items-center gap-1 px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg font-semibold">
                <i class="fas fa-clock animate-pulse"></i> Menunggu Pembayaran di Kasir
              </span>
              <form action="{{ route('apoteker.resep.update', $resep) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="kembalikan">
                <button type="submit" class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg hover:bg-red-100 transition">Batalkan</button>
              </form>
            @elseif($resep->status === 'Sudah Dibayar' || $resep->status === 'Diproses')
              <form action="{{ route('apoteker.resep.update', $resep) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="selesai">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition font-semibold shadow-sm">
                  <i class="fas fa-check-double mr-1"></i> Serahkan Obat (Selesai)
                </button>
              </form>
            @elseif($resep->status === 'Selesai')
              <button type="button" class="px-4 py-2 bg-gray-100 text-gray-400 text-sm rounded-lg border border-gray-200" disabled>
                <i class="fas fa-handshake mr-1"></i> Obat Telah Diserahkan
              </button>
            @else
              <span class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg border">Resep {{ $resep->status }}</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
