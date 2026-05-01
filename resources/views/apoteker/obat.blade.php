@extends('layouts.apoteker')

@section('title', 'Data Obat')
@section('page-title', 'Data Obat')
@section('page-subtitle', 'Kelola stok dan informasi obat di apotek')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <h2 class="font-bold text-lg text-gray-800">Daftar Obat</h2>
    <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm">
      <i class="fas fa-plus"></i> Tambah Obat
    </button>
  </div>

  <!-- Search & Filter -->
  <div class="mb-6 flex flex-col sm:flex-row gap-3">
    <input type="text" placeholder="Cari nama obat..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm">
    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
      <option>Semua Kategori</option>
      <option>Tablet</option>
      <option>Kapsul</option>
      <option>Sirup</option>
    </select>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto -mx-4 md:mx-0">
    <div class="inline-block min-w-full align-middle">
      <table class="min-w-full text-sm">
      <thead class="bg-gray-100 border-b border-gray-300">
        <tr>
          <th class="text-left px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">Nama Obat</th>
          <th class="text-left px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">Kategori</th>
          <th class="text-center px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">Stok</th>
          <th class="text-right px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">Harga</th>
          <th class="text-center px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">Status</th>
          <th class="text-center px-3 py-2 font-semibold text-gray-700 whitespace-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Paracetamol 500mg</td>
          <td class="px-3 py-2 whitespace-nowrap">Tablet</td>
          <td class="px-3 py-2 text-center font-medium">15</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 3.000</td>
          <td class="px-3 py-2 text-center">
            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 font-medium text-xs rounded-full">Menipis</span>
          </td>
          <td class="px-3 py-2 text-center whitespace-nowrap">
            <button class="text-blue-600 hover:underline text-xs mr-2">Edit</button>
            <button class="text-red-600 hover:underline text-xs">Hapus</button>
          </td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Amoxicillin 500mg</td>
          <td class="px-3 py-2 whitespace-nowrap">Kapsul</td>
          <td class="px-3 py-2 text-center font-medium">45</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 8.500</td>
          <td class="px-3 py-2 text-center">
            <span class="px-2 py-1 bg-green-100 text-green-700 font-medium text-xs rounded-full">Tersedia</span>
          </td>
          <td class="px-3 py-2 text-center whitespace-nowrap">
            <button class="text-blue-600 hover:underline text-xs mr-2">Edit</button>
            <button class="text-red-600 hover:underline text-xs">Hapus</button>
          </td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Ibuprofen 400mg</td>
          <td class="px-3 py-2 whitespace-nowrap">Tablet</td>
          <td class="px-3 py-2 text-center font-medium">0</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 5.000</td>
          <td class="px-3 py-2 text-center">
            <span class="px-2 py-1 bg-red-100 text-red-700 font-medium text-xs rounded-full">Habis</span>
          </td>
          <td class="px-3 py-2 text-center whitespace-nowrap">
            <button class="text-blue-600 hover:underline text-xs mr-2">Edit</button>
            <button class="text-red-600 hover:underline text-xs">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
    </div>
  </div>

  <!-- Pagination -->
  <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
    <p class="text-sm text-gray-500">Menampilkan 3 dari 50 data</p>
    <div class="flex gap-2">
      <button class="px-3 py-1 border rounded hover:bg-gray-100 text-sm">← Sebelumnya</button>
      <button class="px-3 py-1 border rounded bg-green-600 text-white text-sm">1</button>
      <button class="px-3 py-1 border rounded hover:bg-gray-100 text-sm">2</button>
      <button class="px-3 py-1 border rounded hover:bg-gray-100 text-sm">Selanjutnya →</button>
    </div>
  </div>
</div>
@endsection
