@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan Apotek')
@section('page-subtitle', 'Analisis penjualan dan stok obat')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8">
  <!-- Filter -->
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col justify-between">
    <div>
      <h3 class="font-bold text-gray-800 mb-2">Filter Laporan</h3>
      <p class="text-xs text-gray-500 mb-4">Saring laporan penjualan obat apotek berdasarkan rentang tanggal perolehan.</p>
    </div>
    <form action="{{ route('apoteker.laporan') }}" method="GET" class="space-y-3">
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
          <input type="date" name="tgl_awal" value="{{ $tglAwal }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
          <input type="date" name="tgl_akhir" value="{{ $tglAkhir }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        </div>
      </div>
      <div class="flex gap-2 pt-1">
        <button type="submit" class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">Tampilkan Laporan</button>
        @if($tglAwal || $tglAkhir)
          <a href="{{ route('apoteker.laporan') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-semibold flex items-center">Reset</a>
        @endif
      </div>
    </form>
  </div>

  <!-- Quick Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3">
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
      <p class="text-xs font-semibold text-green-700 uppercase tracking-wider">Total Penjualan Obat</p>
      <p class="text-2xl font-black text-green-900 mt-1">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
      <span class="text-[10px] text-green-600 font-medium mt-1">Berdasarkan Billing Terbayar (Lunas)</span>
    </div>
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
      <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Laba Bersih Apotek (Margin)</p>
      <p class="text-2xl font-black text-indigo-900 mt-1">Rp {{ number_format($totalMargin, 0, ',', '.') }}</p>
      <span class="text-[10px] text-indigo-600 font-medium mt-1">Penjualan setelah dikurangi HPP modal</span>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 shadow-sm flex flex-col justify-between">
      <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Resep Selesai</p>
      <p class="text-2xl font-black text-blue-900 mt-1">{{ $resepCount }} Resep</p>
      <span class="text-[10px] text-blue-600 font-medium mt-1">Resep sukses diserahkan ke pasien</span>
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
        @forelse($topSelling as $item)
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2.5 font-medium text-gray-700 whitespace-nowrap">{{ $item->nama_item }}</td>
          <td class="px-3 py-2.5 text-center text-gray-600 font-bold">{{ $item->total_terjual }}</td>
          <td class="px-3 py-2.5 text-right font-semibold text-gray-800 whitespace-nowrap">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="3" class="px-3 py-8 text-center text-gray-400">Belum ada data penjualan obat.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h3 class="font-bold text-lg text-gray-800 mb-4">Status Stok Obat</h3>
    <div class="space-y-4">
      <div>
        <div class="flex items-center justify-between mb-1">
          <p class="text-sm font-medium text-gray-700">Tersedia (Stok &gt; Minimum)</p>
          <span class="text-sm font-bold text-green-600">{{ $pctTersedia }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pctTersedia }}%"></div>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1">
          <p class="text-sm font-medium text-gray-700">Menipis (Stok &le; Minimum)</p>
          <span class="text-sm font-bold text-amber-600">{{ $pctMenipis }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pctMenipis }}%"></div>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between mb-1">
          <p class="text-sm font-medium text-gray-700">Habis (Stok 0)</p>
          <span class="text-sm font-bold text-red-600">{{ $pctHabis }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pctHabis }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const labels = {!! json_encode($grafikHari) !!};
  const dataSales = {!! json_encode($grafikData) !!};

  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Penjualan Obat (Rp)',
        data: dataSales,
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
              return 'Rp ' + Number(value).toLocaleString('id-ID');
            }
          }
        }
      }
    }
  });
</script>

