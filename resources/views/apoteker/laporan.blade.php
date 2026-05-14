@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan Apotek')
@section('page-subtitle', 'Analisis penjualan dan stok obat')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8">
  <!-- Filter -->
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <h3 class="font-bold text-gray-800 mb-4">Filter Laporan</h3>
    <div class="space-y-3">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
          <option>Hari Ini</option>
          <option>7 Hari Terakhir</option>
          <option>30 Hari Terakhir</option>
          <option>Custom Range</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
          <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
          <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
        </div>
      </div>
      <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">Tampilkan Laporan</button>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="space-y-3">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
      <p class="text-sm text-green-800">Total Penjualan (Bulan Ini)</p>
      <p class="text-3xl font-bold text-green-900">Rp 12.450.000</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <p class="text-sm text-blue-800">Resep Diproses (Bulan Ini)</p>
      <p class="text-3xl font-bold text-blue-900">156</p>
    </div>
  </div>
</div>

<!-- Sales Chart -->
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm mb-8">
  <h3 class="font-bold text-lg text-gray-800 mb-4">Grafik Penjualan 7 Hari Terakhir</h3>
  <div class="w-full overflow-hidden">
    <canvas id="salesChart" class="w-full" height="80"></canvas>
  </div>
</div>

<!-- Top Selling Drugs -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h3 class="font-bold text-lg text-gray-800 mb-4">10 Obat Terlaris</h3>
    <div class="overflow-x-auto -mx-4 md:mx-0">
      <div class="inline-block min-w-full align-middle">
        <table class="min-w-full text-sm">
      <thead class="bg-gray-100 border-b">
        <tr>
          <th class="text-left px-3 py-2 font-semibold whitespace-nowrap">Obat</th>
          <th class="text-center px-3 py-2 font-semibold whitespace-nowrap">Terjual</th>
          <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Paracetamol 500mg</td>
          <td class="px-3 py-2 text-center">145</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 435.000</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Amoxicillin 500mg</td>
          <td class="px-3 py-2 text-center">89</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 756.500</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Ibuprofen 400mg</td>
          <td class="px-3 py-2 text-center">67</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 335.000</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Vitamin B12</td>
          <td class="px-3 py-2 text-center">45</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 405.000</td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2 whitespace-nowrap">Cetirizine 10mg</td>
          <td class="px-3 py-2 text-center">38</td>
          <td class="px-3 py-2 text-right whitespace-nowrap">Rp 228.000</td>
        </tr>
      </tbody>
    </table>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h3 class="font-bold text-lg text-gray-800 mb-4">Status Stok Obat</h3>
    <div class="space-y-3">
      <div>
        <div class="flex items-center justify-between mb-1">
          <p class="text-sm font-medium text-gray-700">Tersedia</p>
          <span class="text-sm font-bold text-green-600">85%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1">
          <p class="text-sm font-medium text-gray-700">Menipis</p>
          <span class="text-sm font-bold text-yellow-600">10%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-yellow-500 h-2 rounded-full" style="width: 10%"></div>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1">
          <p class="text-sm font-medium text-gray-700">Habis</p>
          <span class="text-sm font-bold text-red-600">5%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-red-500 h-2 rounded-full" style="width: 5%"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
      datasets: [{
        label: 'Penjualan (Rp)',
        data: [1200000, 1900000, 1500000, 2200000, 1800000, 2400000, 1600000],
        borderColor: '#16a34a',
        backgroundColor: 'rgba(22, 163, 74, 0.1)',
        tension: 0.4,
        fill: true,
        borderWidth: 3,
        pointRadius: 6,
        pointBackgroundColor: '#16a34a',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top',
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
            }
          }
        }
      }
    }
  });
</script>

