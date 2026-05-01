@extends('layouts.dokter')

@section('title', 'Dashboard Dokter')
@section('page-title', 'Dashboard Dokter')
@section('page-subtitle', 'Pantau rekam medis dan resep pasien Anda')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <p class="text-sm text-gray-500">Total Rekam Medis</p>
    <p class="text-4xl font-bold text-slate-800 mt-4">{{ $totalRekamMedis }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <p class="text-sm text-gray-500">Resep Dikirim</p>
    <p class="text-4xl font-bold text-slate-800 mt-4">{{ $totalResep }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <p class="text-sm text-gray-500">Resep Pending</p>
    <p class="text-4xl font-bold text-slate-800 mt-4">{{ $resepPending }}</p>
    <p class="text-xs text-gray-500 mt-1">Menunggu diproses apoteker</p>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h2 class="font-semibold text-lg text-gray-800 mb-3">Aksi cepat</h2>
    <div class="space-y-3">
      <a href="{{ route('dokter.resep.index') }}" class="block px-4 py-3 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition">Buka daftar rekam medis untuk resep</a>
      <a href="{{ route('dokter.resep.index') }}" class="block px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">Kirim resep baru</a>
    </div>
  </div>

  <div class="bg-green-50 rounded-xl border border-green-200 p-6 shadow-sm">
    <h2 class="font-semibold text-lg text-slate-900 mb-3">Catatan</h2>
    <p class="text-sm text-slate-800">Gunakan halaman resep untuk membuat resep hanya setelah pemeriksaan selesai dan masing-masing rekam medis dibuat. Resep akan dikirim ke apoteker dengan status <span class="font-semibold">Menunggu</span>.</p>
  </div>
</div>
@endsection
