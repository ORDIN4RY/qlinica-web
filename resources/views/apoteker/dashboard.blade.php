@extends('layouts.app')

@section('title', 'Dashboard Apoteker')
@section('page-title', 'Dashboard Apoteker')
@section('page-subtitle', 'Kelola persediaan obat dan resep pasien')

@push('styles')
<style>
  .stat-card { 
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  .stat-card:hover { 
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }
  .stat-value {
    font-size: 28px;
    font-weight: bold;
    color: #16a34a;
    margin: 10px 0;
  }
  .stat-label {
    color: #6b7280;
    font-size: 14px;
  }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-8">
  <!-- Total Obat -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Total Obat</p>
        <p class="stat-value">{{ $totalObat }}</p>
      </div>
      <i class="fas fa-pills text-green-900 text-4xl opacity-20"></i>
    </div>
  </div>

  <!-- Resep Pending -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Resep Pending</p>
        <p class="stat-value text-yellow-600">{{ $resepPending }}</p>
      </div>
      <i class="fas fa-file-prescription text-yellow-500 text-4xl opacity-20"></i>
    </div>
  </div>

  <!-- Stok Menipis -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Stok Menipis</p>
        <p class="stat-value text-red-600">{{ $stokMenipis }}</p>
      </div>
      <i class="fas fa-exclamation-triangle text-red-500 text-4xl opacity-20"></i>
    </div>
  </div>

  <!-- Penjualan Hari Ini -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Resep Selesai (Hari Ini)</p>
        <p class="stat-value text-blue-600">{{ $penjualanHariIni }}</p>
      </div>
      <i class="fas fa-shopping-cart text-blue-900 text-4xl opacity-20"></i>
    </div>
  </div>
</div>

<!-- Critical Stock Alert Widget -->
@if($obatKritis->isNotEmpty())
<div class="bg-red-50 border border-red-200 rounded-xl p-4 md:p-6 shadow-sm mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="p-2 bg-red-100 rounded-lg text-red-600">
      <i class="fas fa-exclamation-triangle text-xl animate-bounce"></i>
    </div>
    <div>
      <h2 class="font-bold text-base text-red-900">Peringatan Stok Kritis!</h2>
      <p class="text-xs text-red-700 font-medium">Terdapat {{ $stokMenipis }} item obat yang telah mencapai atau berada di bawah ambang batas minimum. Harap lakukan pengadaan / restok segera.</p>
    </div>
  </div>
  <div class="overflow-x-auto bg-white rounded-lg border border-red-100 shadow-xs">
    <table class="min-w-full text-xs text-left">
      <thead class="bg-red-100 text-red-950 font-semibold border-b border-red-200">
        <tr>
          <th class="px-4 py-2.5">Kode</th>
          <th class="px-4 py-2.5">Nama Obat</th>
          <th class="px-4 py-2.5 text-center">Stok Saat Ini</th>
          <th class="px-4 py-2.5 text-center">Batas Minimum</th>
          <th class="px-4 py-2.5 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-red-50">
        @foreach($obatKritis as $o)
        <tr class="hover:bg-red-50/50 transition">
          <td class="px-4 py-3 text-red-800 font-mono text-[10px]">{{ $o->kode ?? '-' }}</td>
          <td class="px-4 py-3 font-semibold text-red-950">{{ $o->nama }}</td>
          <td class="px-4 py-3 text-center font-extrabold text-red-600 bg-red-50/80">{{ $o->stok }}</td>
          <td class="px-4 py-3 text-center text-red-700 font-medium">{{ $o->stok_minimum }}</td>
          <td class="px-4 py-3 text-right">
            <a href="{{ route('apoteker.obat') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-600 text-white rounded-md text-[10px] font-bold hover:bg-red-700 transition shadow-sm">
              <i class="fas fa-truck-loading"></i> Kelola & Restok
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<!-- Info Box -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-6 flex items-start gap-4 mb-8">
  <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
  <div>
    <h3 class="font-semibold text-blue-900 mb-1">Informasi Penting</h3>
    <p class="text-sm text-blue-800">Selalu pastikan persediaan fisik obat sesuai dengan sistem. Lakukan stok opname secara teratur setiap akhir bulan untuk mencegah selisih nilai finansial apotek.</p>
  </div>
</div>

<!-- Recent Activities -->
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm">
  <h2 class="font-bold text-lg text-gray-800 mb-4">Aktivitas Log Stok Terbaru</h2>
  <div class="space-y-3">
    @forelse($aktivitasTerbaru as $act)
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100/70 transition">
      <div class="flex items-center gap-3">
        <i class="{{ $act['icon'] }} text-base"></i>
        <div>
          <p class="font-medium text-gray-700 text-xs md:text-sm">{!! $act['pesan'] !!}</p>
          <p class="text-[10px] text-gray-500 font-medium mt-1"><i class="far fa-clock mr-1"></i>{{ $act['waktu'] }}</p>
        </div>
      </div>
      <span class="px-2.5 py-0.5 {{ $act['badge'] }} text-[10px] font-bold rounded-full whitespace-nowrap">{{ $act['tipe'] }}</span>
    </div>
    @empty
    <div class="text-center py-10 text-gray-400">
      <i class="fas fa-history text-3xl mb-2 opacity-30"></i>
      <p class="text-sm">Belum ada catatan aktivitas penyesuaian stok terbaru.</p>
    </div>
    @endforelse
  </div>
</div>
@endsection
