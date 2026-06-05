@extends('layouts.app')

@section('title', 'Laporan Penanganan')
@section('page-title', 'Laporan Penanganan')
@section('page-subtitle', 'Rekapitulasi riwayat penanganan pasien')

@push('styles')
<style>
  .table-card { background: var(--putih, #fff); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; }
  .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
  table.laporan-table { width: 100%; border-collapse: collapse; min-width: 1200px; }
  table.laporan-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
  table.laporan-table th { padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1e293b; white-space: nowrap; }
  table.laporan-table td { padding: 12px 16px; color: #475569; border-bottom: 1px solid #f1f5f9; vertical-align: top; font-size: 13px; line-height: 1.5; }
  table.laporan-table tbody tr:hover { background: #f8fafc; }
  
  @media print { body { margin: 0; } }
</style>
@endpush

@section('content')

{{-- FILTER SECTION --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 p-6">
  <form method="GET" action="{{ route('admin.laporan.penanganan') }}" id="filterForm">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
      
      {{-- Periode --}}
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Rentang Waktu</label>
        <select name="periode" id="periode" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
          <option value="">Semua Waktu</option>
          <option value="hari" {{ request('periode') == 'hari' ? 'selected' : '' }}>Hari Ini</option>
          <option value="minggu" {{ request('periode') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
          <option value="bulan" {{ request('periode') == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
          <option value="tahun" {{ request('periode') == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
          <option value="custom" {{ request('periode') == 'custom' ? 'selected' : '' }}>Custom Tanggal</option>
        </select>
      </div>

      {{-- Dokter --}}
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Dokter Pemeriksa</label>
        <select name="dokter_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
          <option value="">Semua Dokter</option>
          @foreach($dokters as $d)
            <option value="{{ $d->id }}" {{ request('dokter_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
          @endforeach
        </select>
      </div>

      {{-- Kasus Penyakit --}}
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kasus Penyakit</label>
        <select name="kasus_penyakit" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
          <option value="">Semua Kasus</option>
          <option value="Baru" {{ request('kasus_penyakit') == 'Baru' ? 'selected' : '' }}>Baru</option>
          <option value="Lama" {{ request('kasus_penyakit') == 'Lama' ? 'selected' : '' }}>Lama</option>
        </select>
      </div>

      {{-- Keadaan Keluar --}}
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Keadaan Keluar</label>
        <select name="keadaan_keluar" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
          <option value="">Semua Keadaan</option>
          @foreach($keadaan_keluars as $k)
            <option value="{{ $k }}" {{ request('keadaan_keluar') == $k ? 'selected' : '' }}>{{ $k }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Custom Dates (Hidden if not custom) --}}
    <div id="customDateRange" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4" style="{{ request('periode') == 'custom' ? '' : 'display:none;' }}">
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Awal</label>
        <input type="date" name="tgl_awal" value="{{ request('tgl_awal') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Akhir</label>
        <input type="date" name="tgl_akhir" value="{{ request('tgl_akhir') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
      </div>
    </div>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-t border-gray-100 pt-4 gap-3">
      <div class="flex flex-wrap gap-2">
        <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
          <i class="fas fa-print mr-2"></i> Print
        </button>
        <button type="button" onclick="exportCSV()" class="px-5 py-2.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-semibold rounded-xl transition">
          <i class="fas fa-file-excel mr-2"></i> Export CSV
        </button>
      </div>
      <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Pasien / No RM..." class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm flex-1 sm:w-64 focus:outline-none focus:border-blue-500">
        <button type="submit" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl transition shadow-md whitespace-nowrap">
          Terapkan Filter
        </button>
        @if(request()->anyFilled(['periode','dokter_id','kasus_penyakit','keadaan_keluar','search','tgl_awal','tgl_akhir']))
          <a href="{{ route('admin.laporan.penanganan') }}" class="px-4 py-2.5 text-red-500 hover:bg-red-50 text-sm font-semibold rounded-xl transition">
            Reset
          </a>
        @endif
      </div>
    </div>
  </form>
</div>

{{-- AREA CETAK (tersembunyi di layar, muncul saat print) --}}
<div id="printArea" style="display:none;">
  <div id="printHeader" style="text-align:center;margin-bottom:12px;">
    <div style="font-size:15pt;font-weight:800;color:#1e3a8a;">LAPORAN PENANGANAN PASIEN</div>
    <div style="font-size:9pt;color:#64748b;margin-top:3px;">
      QLINICA &mdash; Dicetak: <span id="printTanggal"></span>
    </div>
    @if(request()->anyFilled(['periode','dokter_id','kasus_penyakit','keadaan_keluar','search','tgl_awal','tgl_akhir']))
    <div style="font-size:8pt;color:#94a3b8;margin-top:2px;">
      Filter aktif:
      @if(request('periode')) Periode: {{ ucfirst(request('periode')) }} @endif
      @if(request('tgl_awal')) {{ request('tgl_awal') }} s/d {{ request('tgl_akhir') }} @endif
      @if(request('search')) | Pencarian: {{ request('search') }} @endif
    </div>
    @endif
  </div>
  <div style="overflow:visible;">
    <table class="laporan-table" style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th>Tanggal</th><th>No RM</th><th>Nama Pasien</th><th>J.Kelamin</th>
          <th>Umur</th><th>Kode ICDX</th><th>Pengobatan</th><th>Tindakan</th>
          <th>Keadaan Keluar</th><th>Prognosa</th><th>Pelayanan</th>
          <th>Jenis Pelayanan</th><th>Pemeriksa</th>
        </tr>
      </thead>
      <tbody id="printBody"></tbody>
    </table>
  </div>
  <div style="font-size:8pt;color:#94a3b8;margin-top:10px;text-align:right;">Total: <span id="printTotal"></span> data</div>
</div>

{{-- DATA TABLE --}}
<div class="table-card">
  <div class="table-wrap">
    <table class="laporan-table" id="dataTable">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>No RM</th>
          <th>Nama Pasien</th>
          <th>Jenis Kelamin</th>
          <th>Umur</th>
          <th>Kode ICDX</th>
          <th>Pengobatan</th>
          <th>Tindakan</th>
          <th>Keadaan Keluar</th>
          <th>Prognosa</th>
          <th>Pelayanan Kesehatan</th>
          <th>Jenis Pelayanan</th>
          <th>Pemeriksa</th>
        </tr>
      </thead>
      <tbody>
        @forelse($laporans as $r)
          <tr>
            <td class="font-semibold">{{ $r->tanggal_periksa ? $r->tanggal_periksa->format('d/m/Y H:i') : '-' }}</td>
            <td class="font-mono text-xs text-blue-600 font-bold">{{ $r->pasien->no_rm ?? '-' }}</td>
            <td class="font-bold text-gray-800">{{ $r->pasien->nama ?? '-' }}</td>
            <td>{{ $r->pasien->jenis_kelamin ?? '-' }}</td>
            <td>{{ $r->pasien->umur !== null ? $r->pasien->umur . ' Tahun' : '-' }}</td>
            <td>
              @php
                $primer = $r->diagnosa->where('is_primer', true)->first();
              @endphp
              @if($primer && $primer->icdx)
                <span class="font-bold text-blue-700" title="{{ $primer->icdx->nama_id }}">{{ $primer->icdx->kode }}</span>
              @else
                -
              @endif
            </td>
            <td>
              <div class="text-xs text-gray-600 line-clamp-2" title="{{ $r->pengobatan }}">{{ $r->pengobatan ?: '-' }}</div>
            </td>
            <td>
              <div class="text-xs text-gray-600 line-clamp-2" title="{{ $r->tindakan }}">{{ $r->tindakan ?: '-' }}</div>
            </td>
            <td>
              @if($r->keadaan_keluar)
                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">{{ $r->keadaan_keluar }}</span>
              @else
                -
              @endif
            </td>
            <td>{{ $r->prognosa ?: '-' }}</td>
            <td>{{ $r->pelayanan_kesehatan ?: '-' }}</td>
            <td>{{ $r->jenis_pelayanan ?: '-' }}</td>
            <td class="font-medium">{{ $r->dokter->nama ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="13" class="text-center py-12 text-gray-400">
              <i class="fas fa-folder-open text-4xl mb-4 opacity-30 block"></i>
              <p class="font-semibold text-gray-500">Data penanganan tidak ditemukan</p>
              <p class="text-xs mt-1">Coba sesuaikan filter atau rentang waktu yang dipilih.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($laporans->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
      <div class="text-xs text-gray-500">
        Menampilkan data ke-{{ $laporans->firstItem() ?? 0 }} sampai {{ $laporans->lastItem() ?? 0 }} dari total {{ $laporans->total() }} data
      </div>
      <div>
        {{ $laporans->links('pagination::tailwind') }}
      </div>
    </div>
  @endif
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

  function exportCSV() {
    // Gunakan tab-separated + BOM agar Excel langsung membaca kolom dengan benar
    const BOM = '\uFEFF';
    const SEP = '\t';
    const NL  = '\r\n';

    const headers = [
      'Tanggal','No RM','Nama Pasien','Jenis Kelamin','Umur',
      'Kode ICDX','Pengobatan','Tindakan','Keadaan Keluar',
      'Prognosa','Pelayanan Kesehatan','Jenis Pelayanan','Pegawai'
    ];

    let rows = [ headers.join(SEP) ];

    document.querySelectorAll('#dataTable tbody tr').forEach(tr => {
      const cols = tr.querySelectorAll('td');
      if (cols.length <= 1) return; // skip empty-state row
      const rowData = Array.from(cols).map(td => {
        // Ambil teks bersih, hapus newline & tab
        return td.innerText.replace(/[\t\r\n]+/g, ' ').trim();
      });
      rows.push(rowData.join(SEP));
    });

    const content = BOM + rows.join(NL);
    const blob    = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const link    = document.createElement('a');
    link.href     = URL.createObjectURL(blob);
    link.download = 'laporan_penanganan_' + new Date().toISOString().slice(0,10) + '.csv';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
  /* ── PRINT via popup window ── */
  function doPrint() {
    const rows    = document.querySelectorAll('#dataTable tbody tr');
    const tanggal = new Date().toLocaleString('id-ID', { dateStyle:'long', timeStyle:'short' });

    let rowsHtml = '';
    rows.forEach(tr => {
      const cells = tr.querySelectorAll('td');
      if (cells.length <= 1) return;
      rowsHtml += '<tr>';
      cells.forEach(td => {
        rowsHtml += '<td>' + (td.innerText.replace(/\n+/g,' ').trim() || '-') + '</td>';
      });
      rowsHtml += '</tr>';
    });

    const html = `<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8">
<title>Laporan Penanganan | QLINICA</title>
<style>
  @page { size: A4 landscape; margin: 12mm 10mm; }
  body  { font-family: Arial, sans-serif; font-size: 9pt; margin: 0; }
  .hdr  { text-align:center; margin-bottom: 10px; }
  .hdr h1 { font-size:13pt; font-weight:800; color:#1e3a8a; margin:0; }
  .hdr p  { font-size:8pt; color:#64748b; margin:2px 0 0; }
  table { width:100%; border-collapse:collapse; font-size:8pt; }
  th    { background:#dbeafe; color:#1e3a8a; padding:5px 6px; border:1px solid #93c5fd;
          font-weight:700; text-align:left; white-space:nowrap; }
  td    { padding:4px 6px; border:1px solid #cbd5e1; vertical-align:top;
          word-wrap:break-word; }
  tr:nth-child(even) td { background:#f8fafc; }
  thead { display:table-header-group; }
  tr    { page-break-inside:avoid; }
  .foot { font-size:7.5pt; color:#94a3b8; text-align:right; margin-top:8px; }
</style></head><body>
<div class="hdr">
  <h1>LAPORAN PENANGANAN PASIEN &mdash; QLINICA</h1>
  <p>Dicetak: ${tanggal}</p>
</div>
<table>
  <thead><tr>
    <th>Tanggal</th><th>No RM</th><th>Nama Pasien</th><th>J.Kel</th>
    <th>Umur</th><th>Kode ICDX</th><th>Pengobatan</th><th>Tindakan</th>
    <th>Keadaan Keluar</th><th>Prognosa</th><th>Pelayanan</th>
    <th>Jenis Pelayanan</th><th>Pemeriksa</th>
  </tr></thead>
  <tbody>${rowsHtml}</tbody>
</table>
<div class="foot">Total: ${rows.length} data</div>
</body></html>`;

    const w = window.open('', '_blank', 'width=1200,height=800');
    w.document.write(html);
    w.document.close();
    w.focus();
    w.onload = () => { w.print(); };
    // fallback jika onload tidak trigger
    setTimeout(() => { try { w.print(); } catch(e){} }, 800);
  }

  /* ── EXPORT sebagai file Excel (.xls HTML table) ── */
  function exportCSV() {
    const rows    = document.querySelectorAll('#dataTable tbody tr');
    const tanggal = new Date().toLocaleString('id-ID', { dateStyle:'long', timeStyle:'short' });

    const headers = [
      'Tanggal','No RM','Nama Pasien','Jenis Kelamin','Umur',
      'Kode ICDX','Pengobatan','Tindakan','Keadaan Keluar',
      'Prognosa','Pelayanan Kesehatan','Jenis Pelayanan','Pegawai'
    ];

    let thHtml = headers.map(h => `<th>${h}</th>`).join('');
    let tbHtml = '';
    rows.forEach(tr => {
      const cells = tr.querySelectorAll('td');
      if (cells.length <= 1) return;
      tbHtml += '<tr>';
      cells.forEach(td => {
        const txt = td.innerText.replace(/[\r\n]+/g,' ').trim() || '-';
        tbHtml += `<td>${txt}</td>`;
      });
      tbHtml += '</tr>';
    });

    const xls = `<html xmlns:o="urn:schemas-microsoft-com:office:office"
  xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8">
<style>
  th { background:#dbeafe; font-weight:bold; border:1px solid #93c5fd; padding:5px 8px; }
  td { border:1px solid #e2e8f0; padding:4px 8px; }
  table { border-collapse:collapse; font-family:Arial; font-size:10pt; }
</style>
</head><body>
<h3 style="font-family:Arial;">Laporan Penanganan Pasien &mdash; QLINICA</h3>
<p style="font-family:Arial;font-size:10px;color:#64748b;">Diekspor: ${tanggal}</p>
<table>
  <thead><tr>${thHtml}</tr></thead>
  <tbody>${tbHtml}</tbody>
</table>
</body></html>`;

    const blob = new Blob([xls], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const link = document.createElement('a');
    link.href  = URL.createObjectURL(blob);
    link.download = 'laporan_penanganan_' + new Date().toISOString().slice(0,10) + '.xls';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  // Pasang doPrint ke tombol Print
  document.querySelector('button[onclick="window.print()"]').setAttribute('onclick','doPrint()');
  // Pasang exportCSV ke tombol Export CSV
  document.querySelector('button[onclick="exportCSV()"]')?.setAttribute('onclick','exportCSV()');
</script>
@endpush
