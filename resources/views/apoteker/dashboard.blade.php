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
        <p class="stat-value">245</p>
      </div>
      <i class="fas fa-pills text-green-900 text-4xl opacity-20"></i>
    </div>
  </div>

  <!-- Resep Pending -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Resep Pending</p>
        <p class="stat-value">18</p>
      </div>
      <i class="fas fa-file-prescription text-yellow-500 text-4xl opacity-20"></i>
    </div>
  </div>

  <!-- Stok Menipis -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Stok Menipis</p>
        <p class="stat-value">7</p>
      </div>
      <i class="fas fa-exclamation-triangle text-red-500 text-4xl opacity-20"></i>
    </div>
  </div>

  <!-- Penjualan Hari Ini -->
  <div class="stat-card">
    <div class="flex items-center justify-between">
      <div>
        <p class="stat-label">Penjualan Hari Ini</p>
        <p class="stat-value">42</p>
      </div>
      <i class="fas fa-shopping-cart text-blue-900 text-4xl opacity-20"></i>
    </div>
  </div>
</div>

<!-- Info Box -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-6 flex items-start gap-4 mb-8">
  <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
  <div>
    <h3 class="font-semibold text-blue-900 mb-1">Informasi Penting</h3>
    <p class="text-sm text-blue-800">Periksa stok obat secara berkala dan pastikan resep pasien diproses dengan cepat. Jangan lupa catat setiap penjualan obat.</p>
  </div>
</div>

<!-- Recent Activities -->
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm">
  <h2 class="font-bold text-lg text-gray-800 mb-4">Aktivitas Terbaru</h2>
  <div class="space-y-3">
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
      <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <div>
          <p class="font-medium text-gray-700">Resep #RES-2026-001 selesai diproses</p>
          <p class="text-xs text-gray-500">10 menit yang lalu</p>
        </div>
      </div>
      <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Selesai</span>
    </div>
    
    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
      <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
        <div>
          <p class="font-medium text-gray-700">Stok Paracetamol 500mg menipis</p>
          <p class="text-xs text-gray-500">1 jam yang lalu</p>
        </div>
      </div>
      <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Perhatian</span>
    </div>

    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
      <div class="flex items-center gap-3">
        <i class="fas fa-plus-circle text-blue-500"></i>
        <div>
          <p class="font-medium text-gray-700">Pembelian obat baru dari supplier</p>
          <p class="text-xs text-gray-500">3 jam yang lalu</p>
        </div>
      </div>
      <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Baru</span>
    </div>
  </div>
</div>
@endsection
