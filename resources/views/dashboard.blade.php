@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data klinik Sahaduta')

@section('content')

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">

  <div class="card-hover bg-white rounded-2xl p-6 border border-blue-900/10 shadow-sm flex items-center gap-5 fade-in-up">
    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
      <i class="fas fa-users text-blue-900 text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-gray-500">Total Pasien</p>
      <p class="text-3xl font-bold text-gray-800">{{ $totalPatients }}</p>
    </div>
  </div>

  <div class="card-hover bg-white rounded-2xl p-6 border border-green-200 shadow-sm flex items-center gap-5 fade-in-up" style="animation-delay:.1s">
    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center flex-shrink-0">
      <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-gray-500">Kunjungan Hari Ini</p>
      <p class="text-3xl font-bold text-gray-800">{{ $todayVisits }}</p>
    </div>
  </div>

  <div class="card-hover bg-white rounded-2xl p-6 border border-purple-200 shadow-sm flex items-center gap-5 fade-in-up" style="animation-delay:.2s">
    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
      <i class="fas fa-virus text-purple-600 text-2xl"></i>
    </div>
    <div>
      <p class="text-sm text-gray-500">Jenis Penyakit</p>
      <p class="text-3xl font-bold text-gray-800">{{ $totalDiseases }}</p>
    </div>
  </div>

</div>

{{-- ===== CHARTS ROW ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">

  {{-- Line chart – monthly visits --}}
  <div class="lg:col-span-3 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm fade-in-up" style="animation-delay:.15s">
    <h3 class="font-semibold text-gray-800 mb-1">Kunjungan Pasien per Bulan</h3>
    <p class="text-xs text-gray-400 mb-5">Tahun {{ $year }}</p>
    <canvas id="lineChart" height="100"></canvas>
  </div>

  {{-- Donut chart – diseases --}}
  <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm fade-in-up" style="animation-delay:.25s">
    <h3 class="font-semibold text-gray-800 mb-1">Penyakit Terbanyak</h3>
    <p class="text-xs text-gray-400 mb-5">Top {{ $diseaseData->count() }} diagnosis</p>
    <canvas id="donutChart" height="200"></canvas>
  </div>

</div>

{{-- ===== RECENT PATIENTS TABLE ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden fade-in-up" style="animation-delay:.3s">
  <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
    <h3 class="font-semibold text-gray-800">Pasien Terbaru</h3>
    <a href="{{ route('patients.index') }}" class="text-sm text-blue-900 hover:underline font-medium">Lihat semua →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
        <tr>
          <th class="px-6 py-3 text-left">Nama</th>
          <th class="px-6 py-3 text-left">Penyakit</th>
          <th class="px-6 py-3 text-left">Tanggal Kunjungan</th>
          <th class="px-6 py-3 text-left">Gender</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($recentPatients as $p)
        <tr class="hover:bg-blue-50/40 transition">
          <td class="px-6 py-4 font-medium text-gray-800">{{ $p->name }}</td>
          <td class="px-6 py-4">
            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">{{ $p->disease }}</span>
          </td>
          <td class="px-6 py-4 text-gray-500">{{ $p->visit_date->format('d M Y') }}</td>
          <td class="px-6 py-4">
            @if($p->gender === 'L')
              <span class="px-2.5 py-1 bg-sky-100 text-sky-700 rounded-full text-xs font-medium">Laki-laki</span>
            @else
              <span class="px-2.5 py-1 bg-pink-100 text-pink-700 rounded-full text-xs font-medium">Perempuan</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">Belum ada data pasien.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // ===== LINE CHART =====
  const monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const monthlyData = @json($monthlyVisits);

  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: monthLabels,
      datasets: [{
        label: 'Kunjungan',
        data: monthlyData,
        borderColor: '#1e3a8a',
        backgroundColor: 'rgba(30,58,138,0.08)',
        borderWidth: 2.5,
        pointBackgroundColor: '#1e3a8a',
        pointRadius: 5,
        pointHoverRadius: 7,
        fill: true,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 },
          grid: { color: 'rgba(0,0,0,0.04)' }
        },
        x: { grid: { display: false } }
      }
    }
  });

  // ===== DONUT CHART =====
  const diseaseLabels = @json($diseaseData->pluck('disease'));
  const diseaseTotals = @json($diseaseData->pluck('total'));
  const palette = [
    '#1e3a8a','#2563eb','#3b82f6','#60a5fa','#93c5fd',
    '#7c3aed','#a855f7','#c084fc'
  ];

  new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
      labels: diseaseLabels,
      datasets: [{
        data: diseaseTotals,
        backgroundColor: palette,
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      cutout: '65%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { padding: 16, font: { size: 12 } }
        }
      }
    }
  });
</script>
@endpush