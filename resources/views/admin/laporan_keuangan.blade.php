@extends('layouts.app')

@section('title', 'Laporan Keuangan & Kasir')
@section('page-title', 'Laporan Keuangan & Kasir')
@section('page-subtitle', 'Rekapitulasi transaksi, pendapatan klinik, dan klaim asuransi/BPJS')

@push('styles')
<style>
  .table-card { background: var(--putih, #fff); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; }
  .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
  table.laporan-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
  table.laporan-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
  table.laporan-table th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1e293b; white-space: nowrap; }
  table.laporan-table td { padding: 12px 16px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 13px; }
  table.laporan-table tbody tr:hover { background: #f8fafc; }
  
  @media print {
      body * { visibility: hidden; }
      .print-area, .print-area * { visibility: visible; }
      .print-area { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
      nav, aside, header, form, .no-print { display: none !important; }
  }
</style>
@endpush

@section('content')
<div class="print-area space-y-6">

  {{-- FILTER SECTION (no-print) --}}
  <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 no-print">
    <form method="GET" action="{{ route('admin.laporan.keuangan') }}" id="filterForm">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        
        {{-- Periode --}}
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Rentang Waktu</label>
          <select name="periode" id="periode" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            <option value="hari" {{ $periode == 'hari' ? 'selected' : '' }}>Hari Ini</option>
            <option value="minggu" {{ $periode == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="bulan" {{ $periode == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="tahun" {{ $periode == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
            <option value="custom" {{ $periode == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
          </select>
        </div>

        {{-- Metode Pembayaran --}}
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Metode Pembayaran</label>
          <select name="metode_pembayaran" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Metode</option>
            <option value="Tunai" {{ request('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="Debit" {{ request('metode_pembayaran') == 'Debit' ? 'selected' : '' }}>Debit / EDC</option>
            <option value="QRIS" {{ request('metode_pembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
            <option value="Asuransi" {{ request('metode_pembayaran') == 'Asuransi' ? 'selected' : '' }}>Asuransi / BPJS</option>
          </select>
        </div>

        {{-- Status Tagihan --}}
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Status Pembayaran</label>
          <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Status</option>
            <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="Belum Bayar" {{ request('status') == 'Belum Bayar' ? 'selected' : '' }}>Belum Bayar</option>
            <option value="Batal" {{ request('status') == 'Batal' ? 'selected' : '' }}>Dibatalkan</option>
          </select>
        </div>

        {{-- Tanggal Awal & Akhir (Custom) --}}
        <div id="customDateRange" class="grid grid-cols-2 gap-2" style="{{ $periode == 'custom' ? '' : 'display:none;' }}">
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tgl Awal</label>
            <input type="date" name="tgl_awal" value="{{ $tgl_awal }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tgl Akhir</label>
            <input type="date" name="tgl_akhir" value="{{ $tgl_akhir }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
          </div>
        </div>
      </div>

      <div class="flex items-center justify-between border-t border-gray-100 pt-4">
        <div class="flex gap-2">
          <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
            <i class="fas fa-print mr-2"></i> Print Laporan
          </button>
          <button type="button" onclick="exportCSV()" class="px-5 py-2.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-semibold rounded-xl transition">
            <i class="fas fa-file-excel mr-2"></i> Export Excel (CSV)
          </button>
        </div>
        <div class="flex items-center gap-3">
          <button type="submit" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl transition shadow-md">
            Terapkan Filter
          </button>
          @if(request()->anyFilled(['periode','metode_pembayaran','status','tgl_awal','tgl_akhir']))
            <a href="{{ route('admin.laporan.keuangan') }}" class="px-4 py-2.5 text-red-500 hover:bg-red-50 text-sm font-semibold rounded-xl transition">
              Reset
            </a>
          @endif
        </div>
      </div>
    </form>
  </div>

  {{-- STATS CARDS --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pendapatan Kotor</p>
        <h3 class="text-xl font-bold text-gray-900 font-mono">Rp {{ number_format($totalPendapatanKotor, 2, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400">Total konsultasi, tindakan, & obat</p>
      </div>
      <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Klaim Asuransi / BPJS</p>
        <h3 class="text-xl font-bold text-emerald-600 font-mono">Rp {{ number_format($totalKlaimBpjs, 2, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400">Tanggungan pihak ketiga</p>
      </div>
      <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
        <i class="fas fa-shield-alt"></i>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pemasukan Bersih (Cash)</p>
        <h3 class="text-xl font-bold text-blue-900 font-mono">Rp {{ number_format($totalPendapatanBersih, 2, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400">Dana tunai / bank yang masuk</p>
      </div>
      <div class="w-12 h-12 bg-blue-50 text-blue-900 rounded-xl flex items-center justify-center text-xl">
        <i class="fas fa-wallet"></i>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
      <div class="space-y-1">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Piutang (Belum Bayar)</p>
        <h3 class="text-xl font-bold text-amber-600 font-mono">Rp {{ number_format($totalBelumBayar, 2, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400">Tagihan kasir tertunda</p>
      </div>
      <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
        <i class="fas fa-clock"></i>
      </div>
    </div>

  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- CHART AREA --}}
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800 text-base">Tren Pendapatan Harian</h3>
        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider bg-gray-100 px-2.5 py-1 rounded-full">{{ $periode }}</span>
      </div>
      <div class="relative h-72">
        @if($grafikData->isEmpty())
          <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
            <i class="fas fa-chart-line text-4xl opacity-20 mb-2"></i>
            <p class="text-sm font-semibold">Tidak ada transaksi lunas di periode ini</p>
          </div>
        @else
          <canvas id="revenueChart"></canvas>
        @endif
      </div>
    </div>

    {{-- PAYMENT METHODS SHARE --}}
    <div class="lg:col-span-1 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
      <h3 class="font-bold text-gray-800 text-base mb-4">Metode Pembayaran (Lunas)</h3>
      
      <div class="space-y-4 flex-grow flex flex-col justify-center">
        @php
          $maxShare = collect($pendapatanMetode)->max() ?: 1;
        @endphp

        @foreach($pendapatanMetode as $m => $val)
          @php
            $percentage = $totalPendapatanBersih > 0 ? ($val / $totalPendapatanBersih) * 100 : 0;
            $colorClasses = [
              'Tunai' => 'bg-indigo-600',
              'Debit' => 'bg-blue-500',
              'QRIS' => 'bg-amber-500',
              'Asuransi' => 'bg-emerald-500',
            ];
            $iconClasses = [
              'Tunai' => 'fa-money-bill-wave text-indigo-600 bg-indigo-50',
              'Debit' => 'fa-credit-card text-blue-500 bg-blue-50',
              'QRIS' => 'fa-qrcode text-amber-500 bg-amber-50',
              'Asuransi' => 'fa-shield-alt text-emerald-500 bg-emerald-50',
            ];
          @endphp
          <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs text-gray-700">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] {{ $iconClasses[$m] ?? 'fa-wallet text-gray-500 bg-gray-50' }}">
                  <i class="fas {{ explode(' ', $iconClasses[$m])[0] }}"></i>
                </div>
                <span class="font-semibold">{{ $m === 'Asuransi' ? 'BPJS / Asuransi' : $m }}</span>
              </div>
              <span class="font-bold font-mono">Rp {{ number_format($val, 2, ',', '.') }} ({{ round($percentage, 1) }}%)</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div class="{{ $colorClasses[$m] ?? 'bg-gray-600' }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
            </div>
          </div>
        @endforeach
      </div>

    </div>

  </div>

  {{-- DETAILED TRANSACTIONS TABLE --}}
  <div class="table-card">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="font-bold text-gray-800 text-base">Rincian Transaksi Tagihan</h3>
      <p class="text-xs text-gray-500 font-medium">Menampilkan transaksi pasien dan penagihan kasir</p>
    </div>
    
    <div class="table-wrap">
      <table class="laporan-table text-left" id="dataTable">
        <thead>
          <tr>
            <th>Waktu Buat</th>
            <th>No. Invoice</th>
            <th>Nama Pasien</th>
            <th>Status Pasien</th>
            <th class="text-right">Biaya Registrasi</th>
            <th class="text-right">Biaya Tindakan</th>
            <th class="text-right">Biaya Obat</th>
            <th class="text-right">Potongan BPJS</th>
            <th class="text-right">Total Bersih</th>
            <th class="text-center">Metode Bayar</th>
            <th class="text-center">Status</th>
            <th>Kasir Penerima</th>
          </tr>
        </thead>
        <tbody>
          @forelse($billings as $b)
            @php
              $statusClasses = [
                'Belum Bayar' => 'bg-amber-100 text-amber-800 border-amber-200',
                'Lunas' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'Batal' => 'bg-red-100 text-red-800 border-red-200',
              ];
            @endphp
            <tr>
              <td class="font-semibold">{{ $b->created_at->format('d/m/Y H:i') }}</td>
              <td class="font-mono text-xs text-blue-900 font-semibold">{{ $b->no_invoice }}</td>
              <td>
                <div class="font-bold text-gray-800">{{ $b->pasien?->nama ?? 'Pasien' }}</div>
                <div class="text-[10px] text-gray-400 font-mono">RM: {{ $b->pasien?->no_rm ?: '-' }}</div>
              </td>
              <td>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $b->no_bpjs ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                  {{ $b->no_bpjs ? 'BPJS' : 'UMUM' }}
                </span>
              </td>
              <td class="text-right font-mono">Rp {{ number_format($b->biaya_registrasi, 2, ',', '.') }}</td>
              <td class="text-right font-mono">Rp {{ number_format($b->biaya_tindakan, 2, ',', '.') }}</td>
              <td class="text-right font-mono">Rp {{ number_format($b->biaya_obat, 2, ',', '.') }}</td>
              <td class="text-right font-mono text-emerald-600 font-semibold">-Rp {{ number_format($b->potongan_bpjs, 2, ',', '.') }}</td>
              <td class="text-right font-bold text-gray-900 font-mono">Rp {{ number_format($b->grand_total, 2, ',', '.') }}</td>
              <td class="text-center font-semibold text-xs text-gray-700">{{ $b->metode_pembayaran ?: '–' }}</td>
              <td class="text-center">
                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusClasses[$b->status] ?? 'bg-gray-100 text-gray-800' }}">
                  {{ $b->status }}
                </span>
              </td>
              <td class="font-medium">{{ $b->kasir?->nama ?? '–' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="text-center py-12 text-gray-400">
                <i class="fas fa-folder-open text-4xl mb-4 opacity-30 block"></i>
                <p class="font-semibold text-gray-500">Data keuangan tidak ditemukan</p>
                <p class="text-xs mt-1">Coba sesuaikan filter atau rentang waktu yang dipilih.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    @if($billings->hasPages())
      <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
        <div class="text-xs text-gray-500">
          Menampilkan data ke-{{ $billings->firstItem() ?? 0 }} sampai {{ $billings->lastItem() ?? 0 }} dari total {{ $billings->total() }} data
        </div>
        <div>
          {{ $billings->links('pagination::tailwind') }}
        </div>
      </div>
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
  document.getElementById('periode').addEventListener('change', function() {
    var cr = document.getElementById('customDateRange');
    if (this.value === 'custom') {
      cr.style.display = 'grid';
    } else {
      cr.style.display = 'none';
      document.querySelector('input[name="tgl_awal"]').value = '';
      document.querySelector('input[name="tgl_akhir"]').value = '';
    }
  });

  // Render Chart.js
  @if(!$grafikData->isEmpty())
    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('revenueChart').getContext('2d');
      const keys = {!! json_encode($grafikData->keys()) !!};
      const values = {!! json_encode($grafikData->values()) !!};

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: keys,
          datasets: [{
            label: 'Pendapatan Bersih (Rp)',
            data: values,
            borderColor: '#1e3a8a',
            backgroundColor: 'rgba(30, 58, 138, 0.05)',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#1e3a8a',
            pointRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return 'Rp ' + value.toLocaleString('id-ID');
                }
              }
            }
          }
        }
      });
    });
  @endif

  function exportCSV() {
    let rows = [];
    let headers = ['Tanggal Pembuatan', 'No Invoice', 'Nama Pasien', 'Jenis Kasus', 'Biaya Registrasi', 'Biaya Tindakan', 'Biaya Obat', 'Potongan BPJS', 'Total Bersih', 'Metode Pembayaran', 'Status', 'Kasir Penerima'];
    rows.push(headers.join(','));

    let tableRows = document.querySelectorAll('#dataTable tbody tr');
    tableRows.forEach(tr => {
      let cols = tr.querySelectorAll('td');
      if(cols.length > 1) { // Skip empty state row
        let rowData = [];
        cols.forEach((td, idx) => {
          let text = td.innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '""').trim();
          // Hapus prefix Rp atau tanda minus pada angka jika ada
          if (idx >= 4 && idx <= 8) {
            text = text.replace(/Rp\s?/g, '').replace(/\./g, '').replace(/,/g, '.').replace(/-/g, '');
          }
          rowData.push('"' + text + '"');
        });
        rows.push(rowData.join(','));
      }
    });

    let csvContent = rows.join('\n');
    let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement("a");
    let url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", "laporan_keuangan_klinik.csv");
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
</script>
@endpush
