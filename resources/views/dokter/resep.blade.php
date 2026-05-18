@extends('layouts.app')

@section('title', 'Resep Dokter')
@section('page-title', 'Resep Dokter')
@section('page-subtitle', 'Pilih rekam medis pasien untuk membuat resep')

@section('content')
<div class="flex flex-col gap-4">
  <form action="{{ route('dokter.resep.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
    <input type="text" name="search" value="{{ old('search', $search) }}" placeholder="Cari nama pasien atau No. RM..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-600">
    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition">Cari</button>
  </form>

  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <h2 class="font-semibold text-lg text-gray-800 mb-4">Daftar Rekam Medis</h2>
    @if($rekamMedis->isEmpty())
      <p class="text-sm text-gray-500">Tidak ada rekam medis pasien tersedia.</p>
    @else
      <div class="space-y-4">
        @foreach($rekamMedis as $item)
          <div class="border rounded-xl p-4 bg-slate-50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
              <div class="min-w-0">
                <p class="text-sm text-gray-500">No. RM: {{ $item->pasien?->no_rm ?? '-' }}</p>
                <h3 class="font-semibold text-gray-800 truncate">{{ $item->pasien?->nama ?? 'Pasien tidak ditemukan' }}</h3>
                <p class="text-sm text-gray-600">Tanggal pemeriksaan: {{ optional($item->tanggal_periksa)?->isoFormat('D MMMM YYYY HH:mm') }}</p>
              </div>
              <div class="flex flex-wrap gap-2 items-center">
                @if($item->resep)
                  <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Resep sudah dibuat</span>
                  <a href="{{ route('dokter.resep.create', $item) }}" class="px-3 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-900 transition">Lihat</a>
                @else
                  <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">Belum ada resep</span>
                  <a href="{{ route('dokter.resep.create', $item) }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">Buat Resep</a>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $rekamMedis->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
