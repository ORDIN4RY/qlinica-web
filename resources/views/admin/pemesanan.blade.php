@extends('layouts.app')

@section('title', 'Pemesanan')
@section('pemesanan_active', 'active')
@section('page-title', 'Manajemen Antrian')
@section('page-subtitle', 'Kelola antrian pasien hari ini')

@push('styles')
<style>
  /* ── STAT CARDS ── */
  .antrian-stat {
    border-radius: 20px;
    padding: 28px 32px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center; gap: 8px;
    transition: transform .25s, box-shadow .25s;
    position: relative; overflow: hidden;
  }
  .antrian-stat:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(30,58,138,.18); }
  .antrian-stat::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
    pointer-events: none;
  }
  .antrian-stat.biru  { background: linear-gradient(135deg, #1d4ed8, #2563eb); }
  .antrian-stat.navy  { background: linear-gradient(135deg, #0f2144, #1e3a8a); }
  .antrian-stat .stat-angka {
    font-family: 'Sora', sans-serif;
    font-size: 56px; font-weight: 800;
    color: #fff; line-height: 1; letter-spacing: -2px;
  }
  .antrian-stat .stat-label {
    font-size: 13px; font-weight: 700;
    color: rgba(255,255,255,.8);
    text-transform: uppercase; letter-spacing: 1.5px;
  }
  .antrian-stat .stat-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
  }

  /* ── TOOLBAR ── */
  .toolbar-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 6px rgba(30,58,138,.06);
    padding: 16px 22px;
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;
  }

  /* ── BTN AMBIL ── */
  .btn-ambil {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff; font-size: 13.5px; font-weight: 700;
    padding: 10px 22px; border-radius: 12px;
    border: none; cursor: pointer;
    box-shadow: 0 4px 14px rgba(37,99,235,.35);
    transition: all .2s; white-space: nowrap;
  }
  .btn-ambil:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,99,235,.4); background: linear-gradient(135deg, #1e40af, #3b82f6); }
  .btn-ambil:active { transform: none; }

  /* ── FILTER CHIPS ── */
  .filter-chip {
    padding: 6px 14px; border-radius: 99px;
    font-size: 12px; font-weight: 600;
    border: 1.5px solid #e5e7eb; color: #6b7280;
    background: #f9fafb; cursor: pointer; transition: all .15s;
    white-space: nowrap;
  }
  .filter-chip:hover  { border-color: #93c5fd; color: #2563eb; background: #eff6ff; }
  .filter-chip.active { border-color: #2563eb; color: #2563eb; background: #eff6ff; font-weight: 700; }

  /* ── TABLE ── */
  .tbl-wrap {
    background: #fff; border-radius: 18px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 6px rgba(30,58,138,.06);
    overflow: hidden;
  }
  .tbl-row { transition: background .12s; }
  .tbl-row:hover { background: #f0f7ff; }

  /* ── STATUS BADGES ── */
  .status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700;
    padding: 4px 10px; border-radius: 99px;
  }
  .status-badge::before { content:''; width:6px; height:6px; border-radius:50%; flex-shrink:0; }
  .s-menunggu  { background:#fefce8; color:#ca8a04; }
  .s-menunggu::before  { background:#ca8a04; }
  .s-dipanggil { background:#eff6ff; color:#2563eb; }
  .s-dipanggil::before { background:#2563eb; }
  .s-dilayani  { background:#ecfdf5; color:#059669; }
  .s-dilayani::before  { background:#059669; }
  .s-selesai   { background:#f1f5f9; color:#64748b; }
  .s-selesai::before   { background:#64748b; }
  .s-batal     { background:#fef2f2; color:#dc2626; }
  .s-batal::before     { background:#dc2626; }

  /* ── JENIS PEMESAN ── */
  .jenis-online  { background:#eff6ff; color:#2563eb; }
  .jenis-offline { background:#f0fdf4; color:#16a34a; }
  .jenis-walkin  { background:#faf5ff; color:#7c3aed; }

  /* ── NOMOR ANTRIAN ── */
  .no-antrian {
    width: 36px; height: 36px; border-radius: 10px;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff; font-weight: 800; font-size: 13px;
    display: inline-flex; align-items: center; justify-content: center;
    box-shadow: 0 3px 8px rgba(37,99,235,.3);
  }

  /* ── AKSI ── */
  .btn-panggil {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700;
    padding: 5px 12px; border-radius: 8px;
    background: #eff6ff; color: #2563eb;
    border: 1px solid #dbeafe; cursor: pointer;
    transition: all .15s; white-space: nowrap;
  }
  .btn-panggil:hover { background: #dbeafe; }

  .btn-selesai {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700;
    padding: 5px 12px; border-radius: 8px;
    background: #ecfdf5; color: #059669;
    border: 1px solid #a7f3d0; cursor: pointer;
    transition: all .15s; white-space: nowrap;
  }
  .btn-selesai:hover { background: #d1fae5; }

  .btn-batal {
    width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; background: #fef2f2; color: #dc2626;
    border: 1px solid #fecaca; cursor: pointer; transition: all .15s;
  }
  .btn-batal:hover { background: #fee2e2; }

  /* ── SEARCH ── */
  .search-wrap { position: relative; }
  .search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px; }
  .search-input {
    padding: 8px 14px 8px 32px;
    border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: 13px; color: #1e293b; outline: none;
    transition: all .16s; width: 220px; font-family: inherit;
    background: #fff;
  }
  .search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }

  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
  .modal-overlay.open { display:flex; }
  .modal-box {
    background:#fff; border-radius:20px; width:100%; max-width:520px;
    box-shadow:0 32px 80px rgba(15,23,42,.22);
    animation:modalIn .22s cubic-bezier(.4,0,.2,1); margin:16px; overflow:hidden;
  }
  @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:none} }
  .modal-head { padding:20px 26px 16px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
  .modal-body { padding:22px 26px; }
  .modal-foot { padding:14px 26px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; }

  .form-group label { display:block; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
  .form-input, .form-select {
    width:100%; padding:9px 13px; border:1.5px solid #e5e7eb; border-radius:10px;
    font-size:13.5px; color:#1e293b; background:#fff; outline:none;
    transition:all .16s; box-sizing:border-box; font-family:inherit;
  }
  .form-input:focus, .form-select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }

  /* ── CONFIRM ── */
  .confirm-icon { width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }

  /* ── SHOW ENTRIES ── */
  .entries-select {
    padding: 6px 10px; border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13px; color: #374151; outline:none; font-family:inherit;
    background:#fff; cursor:pointer;
  }
  .entries-select:focus { border-color:#2563eb; }

  /* ── PAGINATION ── */
  .pg-btn {
    width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center;
    border-radius:8px; border:1.5px solid #e5e7eb; font-size:12px; font-weight:600;
    color:#6b7280; transition:all .15s; cursor:pointer; background:#fff;
  }
  .pg-btn:hover:not(.disabled):not(.active) { border-color:#93c5fd; color:#2563eb; background:#eff6ff; }
  .pg-btn.active { background:#1e3a8a; color:#fff; border-color:#1e3a8a; }
  .pg-btn.disabled { opacity:.4; cursor:not-allowed; }

  @keyframes fadeInUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none} }
  .anim { animation: fadeInUp .4s ease both; }
</style>
@endpush

@section('content')

{{-- ─── STAT CARDS ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6 anim">

  <div class="antrian-stat biru">
    <div class="stat-icon">
      <i class="fas fa-list-ol text-white text-lg"></i>
    </div>
    <div class="stat-angka" id="statJumlah">{{ $jumlahAntrian ?? 0 }}</div>
    <div class="stat-label">Jumlah Antrian</div>
  </div>

  <div class="antrian-stat" style="background: linear-gradient(135deg, #059669, #10b981);">
    <div class="stat-icon">
      <i class="fas fa-check-circle text-white text-lg"></i>
    </div>
    <div class="stat-angka" id="statSelesai">{{ $terpanggil ?? 0 }}</div>
    <div class="stat-label">Yang Dipanggil</div>
  </div>

</div>

{{-- ─── TOOLBAR ─────────────────────────────────────────────────── --}}
<div class="toolbar-card mb-5 anim" style="animation-delay:.07s">
  <div class="flex flex-wrap items-center gap-3">
    {{-- Tombol Ambil Antrian --}}
    @if(auth()->user()->hasMenuAccess('Antrian Pemesanan', 'tambah'))
    <button class="btn-ambil" id="btnAmbilAntrian">
      <i class="fas fa-plus text-xs"></i> Ambil Antrian
    </button>
    @endif

    {{-- Filter Status --}}
    <div class="flex items-center gap-2 flex-wrap">
      <button class="filter-chip active" data-filter="semua">Semua</button>
      <button class="filter-chip" data-filter="menunggu">Menunggu</button>
      <button class="filter-chip" data-filter="selesai">Dilayani</button>
    </div>
  </div>

  <div class="flex items-center gap-3">
    {{-- Show Entries --}}
    <div class="flex items-center gap-2">
      <span class="text-xs text-gray-500 font-semibold whitespace-nowrap">Tampilkan</span>
      <select class="entries-select" id="entriesSelect">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
      <span class="text-xs text-gray-500 font-semibold">baris</span>
    </div>
    {{-- Search --}}
    <div class="search-wrap">
      <i class="fas fa-search"></i>
      <input type="text" class="search-input" id="searchInput" placeholder="Cari nama, No RM...">
    </div>
  </div>
</div>

{{-- ─── TABLE ───────────────────────────────────────────────────── --}}
<div class="tbl-wrap anim" style="animation-delay:.14s">
  <div class="overflow-x-auto">
    <table class="w-full text-sm" style="min-width:900px" id="antrianTable">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No Antrian</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No RM</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pasien</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu Pesan</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Pemesan</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
          @if(auth()->user()->hasMenuAccess('Antrian Pemesanan', 'update'))
            <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50" id="antrianBody">
        @forelse($antrians ?? [] as $a)
          <tr class="tbl-row" data-status="{{ strtolower($a->status) }}">
            <td class="px-5 py-3.5">
              <div class="no-antrian">{{ $a->nomor_antrian }}</div>
            </td>
            <td class="px-5 py-3.5">
              <span class="text-blue-700 font-bold font-mono text-xs tracking-wide">{{ $a->pasien->no_rm ?? '—' }}</span>
            </td>
            <td class="px-5 py-3.5">
              <div class="font-semibold text-gray-800 text-sm">{{ $a->pasien->nama ?? '—' }}</div>
            </td>
            <td class="px-5 py-3.5">
              @if(($a->pasien->jenis_kelamin ?? '') === 'L')
                <span class="text-xs font-bold px-3 py-1 rounded-full" style="background:#eff6ff;color:#2563eb">♂ Laki-laki</span>
              @else
                <span class="text-xs font-bold px-3 py-1 rounded-full" style="background:#f5f3ff;color:#7c3aed">♀ Perempuan</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-gray-500 text-xs">
              {{ $a->created_at ? \Carbon\Carbon::parse($a->created_at)->isoFormat('D MMM · HH:mm') : '—' }}
            </td>
            <td class="px-5 py-3.5">
              @php $jenis = strtolower($a->jenis_pemesan ?? 'offline'); @endphp
              @if($jenis === 'online')
                <span class="jenis-online text-xs font-bold px-3 py-1 rounded-full">
                  <i class="fas fa-wifi text-xs mr-1"></i>Online
                </span>
              @elseif($jenis === 'walk-in' || $jenis === 'walkin')
                <span class="jenis-walkin text-xs font-bold px-3 py-1 rounded-full">
                  <i class="fas fa-walking text-xs mr-1"></i>Walk-in
                </span>
              @else
                <span class="jenis-offline text-xs font-bold px-3 py-1 rounded-full">
                  <i class="fas fa-phone text-xs mr-1"></i>Offline
                </span>
              @endif
            </td>
            <td class="px-5 py-3.5">
              @php $st = strtolower($a->status ?? 'menunggu'); @endphp
              @if($st === 'menunggu')
                <span class="status-badge s-menunggu">Menunggu</span>
              @elseif($st === 'dipanggil')
                <span class="status-badge s-dipanggil">Dipanggil</span>
              @elseif($st === 'dilayani')
                <span class="status-badge s-dilayani">Dilayani</span>
              @elseif($st === 'selesai')
                <span class="status-badge s-selesai">Selesai</span>
              @elseif($st === 'batal')
                <span class="status-badge s-batal">Batal</span>
              @endif
            </td>

            @if(auth()->user()->hasMenuAccess('Antrian Pemesanan', 'update'))
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                @if($st === 'menunggu')
                  <button type="button" class="btn-panggil" onclick="openPanggil({{ $a->id }}, '{{ addslashes($a->pasien->nama ?? '') }}')" title="Panggil Pasien">
                    <i class="fas fa-bullhorn text-xs"></i> Panggil
                  </button>
                @endif
                @if(!in_array($st, ['selesai','batal']))
                  <button class="btn-batal" onclick="openBatal({{ $a->id }}, '{{ addslashes($a->pasien->nama ?? '') }}')" title="Batalkan">
                    <i class="fas fa-times text-xs"></i>
                  </button>
                @endif
              </div>
            </td>
            @endif
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="8" class="text-center py-16 text-gray-400">
              <i class="fas fa-inbox text-4xl mb-4 block opacity-25"></i>
              <p class="font-semibold text-sm">Belum ada antrian hari ini</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Footer tabel --}}
  <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
    <p class="text-xs text-gray-500" id="tableInfo">
      Menampilkan <strong>{{ count($antrians ?? []) }}</strong> antrian
    </p>
    <div class="flex items-center gap-1" id="pagination">
      {{-- Pagination di-render via JS jika pakai DataTables, atau Laravel pagination di sini --}}
      @if(isset($antrians) && method_exists($antrians, 'hasPages') && $antrians->hasPages())
        @if($antrians->onFirstPage())
          <span class="pg-btn disabled"><i class="fas fa-chevron-left text-xs"></i></span>
        @else
          <a href="{{ $antrians->previousPageUrl() }}" class="pg-btn"><i class="fas fa-chevron-left text-xs"></i></a>
        @endif
        @foreach($antrians->getUrlRange(max(1,$antrians->currentPage()-2), min($antrians->lastPage(),$antrians->currentPage()+2)) as $page => $url)
          @if($page == $antrians->currentPage())
            <span class="pg-btn active">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="pg-btn">{{ $page }}</a>
          @endif
        @endforeach
        @if($antrians->hasMorePages())
          <a href="{{ $antrians->nextPageUrl() }}" class="pg-btn"><i class="fas fa-chevron-right text-xs"></i></a>
        @else
          <span class="pg-btn disabled"><i class="fas fa-chevron-right text-xs"></i></span>
        @endif
      @endif
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════
     MODAL AMBIL ANTRIAN
════════════════════════════════════ --}}
<div class="modal-overlay" id="modalAntrian">
  <div class="modal-box">
    <div class="modal-head">
      <h2 class="text-base font-bold text-gray-800">Ambil Antrian</h2>
      <button onclick="closeModalAntrian()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <form method="POST" action="{{ route('admin.antrian.store') }}">
      @csrf
      <div class="modal-body">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

          {{-- Cari Pasien --}}
          <div class="form-group" style="grid-column:1/-1">
            <label>Nama / No RM Pasien <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="search_pasien" id="searchPasien" class="form-input"
              placeholder="Ketik nama atau No RM..." autocomplete="off">
            <input type="hidden" name="pasien_id" id="selectedPasienId">
            <div id="pasienSuggest" style="position:relative; z-index:10;"></div>
          </div>

          {{-- Pasien terpilih preview --}}
          <div id="pasienPreview" style="grid-column:1/-1; display:none;">
            <div style="background:#f0f7ff; border:1.5px solid #dbeafe; border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
              <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#1e3a8a,#2563eb);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:14px;" id="prevAvatar">A</div>
              <div>
                <div style="font-weight:700;color:#1e293b;font-size:14px;" id="prevNama">—</div>
                <div style="font-size:12px;color:#2563eb;font-weight:600;" id="prevRm">—</div>
              </div>
            </div>
          </div>

          <input type="hidden" name="jenis_pemesan" value="Offline">
          <input type="hidden" name="status" value="Menunggu">

          {{-- <div class="form-group">
            <label>Jenis Pemesan</label>
            <input type="text" class="form-input" value="Offline" readonly>
          </div> --}}

          {{-- Status Awal
          <div class="form-group">
            <label>Status Awal</label>
            <input type="text" class="form-input" value="Menunggu" readonly>
          </div> --}}

          {{-- Catatan --}}
          <div class="form-group" style="grid-column:1/-1">
            <label>Catatan (opsional)</label>
            <input type="text" name="catatan" class="form-input" placeholder="Catatan tambahan...">
          </div>

        </div>
      </div>
      <div class="modal-foot">
        <button type="button" onclick="closeModalAntrian()"
          class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
          Batal
        </button>
        <button type="submit"
          class="px-6 py-2.5 rounded-xl text-white text-sm font-bold transition shadow-md"
          style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
          <i class="fas fa-plus mr-1.5"></i> Tambahkan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════
     MODAL PANGGIL & PEMERIKSAAN TTV
════════════════════════════════════ --}}
<div class="modal-overlay" id="modalPanggil">
  <div class="modal-box">
    <div class="modal-head">
      <h2 class="text-base font-bold text-gray-800">Pemeriksaan Awal (TTV)</h2>
      <button onclick="closePanggil()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <form id="panggilForm" method="POST" action="">
      @csrf
      <div class="modal-body">
        <p class="text-sm text-gray-600 mb-4">Pemeriksaan awal untuk pasien: <strong id="panggilPasienName"></strong></p>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
          
          {{-- Dokter --}}
          <div class="form-group" style="grid-column:1/-1">
            <label>Pilih Dokter <span class="text-red-500 normal-case font-normal">*</span></label>
            <select name="dokter_id" class="form-select" required>
              <option value="">— Pilih Dokter —</option>
              @foreach($dokters as $d)
                <option value="{{ $d->id }}">{{ $d->nama }}</option>
              @endforeach
            </select>
          </div>

          {{-- Vital Signs --}}
          <div class="form-group">
            <label>Tekanan Darah <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="tekanan_darah" class="form-input" placeholder="Contoh: 120/80" required>
          </div>
          <div class="form-group">
            <label>Suhu (°C) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" step="0.1" name="suhu" class="form-input" placeholder="Contoh: 36.5" required min="30" max="45">
          </div>
          <div class="form-group">
            <label>Berat Badan (kg) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" step="0.1" name="berat_badan" class="form-input" placeholder="Contoh: 60.5" required min="1" max="200">
          </div>
          <div class="form-group">
            <label>Tinggi Badan (cm) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" step="0.1" name="tinggi_badan" class="form-input" placeholder="Contoh: 165" required min="30" max="250">
          </div>
          <div class="form-group">
            <label>Nadi (x/menit) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" name="nadi" class="form-input" placeholder="Contoh: 80" required min="40" max="200">
          </div>
          <div class="form-group">
            <label>Respirasi (x/menit) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" name="respirasi" class="form-input" placeholder="Contoh: 20" required min="10" max="60">
          </div>
          
        </div>

      </div>
      <div class="modal-foot">
        <button type="button" onclick="closePanggil()"
          class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
          Batal
        </button>
        <button type="submit"
          class="px-6 py-2.5 rounded-xl text-white text-sm font-bold transition shadow-md"
          style="background:linear-gradient(135deg,#059669,#10b981)">
          <i class="fas fa-check mr-1.5"></i> Simpan & Panggil
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════
     MODAL KONFIRMASI BATAL
════════════════════════════════════ --}}
<div class="modal-overlay" id="modalBatal">
  <div class="bg-white rounded-2xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl" style="animation:modalIn .22s ease">
    <div class="confirm-icon bg-red-50">
      <i class="fas fa-ban text-red-500 text-xl"></i>
    </div>
    <h3 class="text-base font-bold text-gray-800 mb-2">Batalkan Antrian?</h3>
    <p class="text-sm text-gray-500 mb-6" id="batalMsg">Antrian pasien akan dibatalkan.</p>
    <div class="flex justify-center gap-3">
      <button onclick="closeBatal()"
        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
        Kembali
      </button>
      <form id="batalForm" method="POST" action="">
        @csrf @method('PATCH')
        <input type="hidden" name="status" value="Batal">
        <button type="submit"
          class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition">
          Ya, Batalkan
        </button>
      </form>
    </div>
  </div>
</div>



@endsection

@push('scripts')
<script>
  var BASE_ANTRIAN = '{{ url("/admin/antrian") }}';





  /* ── MODAL ANTRIAN ── */
  document.getElementById('btnAmbilAntrian').addEventListener('click', function() {
    document.getElementById('modalAntrian').classList.add('open');
    document.body.style.overflow = 'hidden';
  });
  function closeModalAntrian() {
    document.getElementById('modalAntrian').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.getElementById('modalAntrian').addEventListener('click', function(e) {
    if (e.target === this) closeModalAntrian();
  });

  /* ── MODAL PANGGIL ── */
  function openPanggil(id, nama) {
    document.getElementById('panggilPasienName').textContent = nama;
    document.getElementById('panggilForm').action = BASE_ANTRIAN + '/' + id + '/panggil';
    document.getElementById('modalPanggil').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closePanggil() {
    document.getElementById('modalPanggil').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.getElementById('modalPanggil').addEventListener('click', function(e) {
    if (e.target === this) closePanggil();
  });

  /* ── MODAL BATAL ── */
  function openBatal(id, nama) {
    document.getElementById('batalMsg').textContent = 'Antrian pasien "' + nama + '" akan dibatalkan.';
    document.getElementById('batalForm').action     = BASE_ANTRIAN + '/' + id + '/status';
    document.getElementById('modalBatal').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeBatal() {
    document.getElementById('modalBatal').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.getElementById('modalBatal').addEventListener('click', function(e) {
    if (e.target === this) closeBatal();
  });

  /* ── ESCAPE ── */
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModalAntrian(); closeBatal(); closePanggil(); }
  });

  /* ── FILTER CHIPS ── */
  document.querySelectorAll('.filter-chip').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.filter-chip').forEach(function(b) { b.classList.remove('active'); });
      this.classList.add('active');
      var filter = this.dataset.filter;
      document.querySelectorAll('#antrianBody tr[data-status]').forEach(function(row) {
        if (filter === 'semua' || row.dataset.status === filter) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  });

  /* ── SEARCH ── */
  document.getElementById('searchInput').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#antrianBody tr[data-status]').forEach(function(row) {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  /* ── AUTOCOMPLETE PASIEN ── */
  var searchPasienInput = document.getElementById('searchPasien');
  var suggestBox        = document.getElementById('pasienSuggest');
  var selectedId        = document.getElementById('selectedPasienId');

  searchPasienInput.addEventListener('input', function() {
    var q = this.value.trim();
    if (q.length < 2) { suggestBox.innerHTML = ''; return; }
    fetch('{{ route("admin.pasien.search") }}?q=' + encodeURIComponent(q))
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data.length) { suggestBox.innerHTML = ''; return; }
        var html = '<div style="position:absolute;top:4px;left:0;right:0;background:#fff;border:1.5px solid #dbeafe;border-radius:12px;box-shadow:0 8px 24px rgba(37,99,235,.12);overflow:hidden;z-index:50;">';
        data.forEach(function(p) {
          html += '<div class="suggest-item" style="padding:10px 14px;cursor:pointer;display:flex;align-items:center;gap:10px;transition:background .12s;" data-id="' + p.id + '" data-nama="' + p.nama + '" data-rm="' + p.no_rm + '">'
            + '<div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#1e3a8a,#2563eb);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:12px;flex-shrink:0;">' + p.nama.charAt(0).toUpperCase() + '</div>'
            + '<div><div style="font-size:13px;font-weight:600;color:#1e293b;">' + p.nama + '</div>'
            + '<div style="font-size:11px;color:#2563eb;font-weight:600;">' + p.no_rm + '</div></div>'
            + '</div>';
        });
        html += '</div>';
        suggestBox.innerHTML = html;
        suggestBox.querySelectorAll('.suggest-item').forEach(function(item) {
          item.addEventListener('mouseenter', function() { this.style.background = '#f0f7ff'; });
          item.addEventListener('mouseleave', function() { this.style.background = ''; });
          item.addEventListener('click', function() {
            var id   = this.dataset.id;
            var nama = this.dataset.nama;
            var rm   = this.dataset.rm;
            selectedId.value = id;
            searchPasienInput.value = nama + ' — ' + rm;
            suggestBox.innerHTML = '';
            document.getElementById('pasienPreview').style.display = 'block';
            document.getElementById('prevAvatar').textContent = nama.charAt(0).toUpperCase();
            document.getElementById('prevNama').textContent   = nama;
            document.getElementById('prevRm').textContent     = rm;
          });
        });
      });
  });
  document.addEventListener('click', function(e) {
    if (!searchPasienInput.contains(e.target)) suggestBox.innerHTML = '';
  });

  document.querySelector('#modalAntrian form').addEventListener('submit', function(e) {
    if (!selectedId.value) {
      e.preventDefault();
      alert('Silakan pilih pasien dari daftar sebelum menyimpan antrian.');
      searchPasienInput.focus();
    }
  });

  // Polling Realtime Antrian (setiap 5 detik)
  setInterval(function() {
    fetch('{{ route("admin.antrian.realtime") }}')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        // Update statistik kartu di bagian atas
        document.getElementById('statJumlah').innerText = data.jumlahAntrian;
        document.getElementById('statSelesai').innerText = data.terpanggil;

        // Simpan query pencarian & filter aktif saat ini
        const activeChip = document.querySelector('.filter-chip.active');
        const activeFilter = activeChip ? activeChip.dataset.filter : 'semua';
        const searchQuery = document.getElementById('searchInput').value.toLowerCase();

        // Update body tabel dengan HTML baru
        const antrianBody = document.getElementById('antrianBody');
        antrianBody.innerHTML = data.html;

        // Re-apply filter status & pencarian agar tidak ter-reset
        document.querySelectorAll('#antrianBody tr[data-status]').forEach(function(row) {
          const matchesFilter = (activeFilter === 'semua' || row.dataset.status === activeFilter);
          const matchesSearch = row.textContent.toLowerCase().includes(searchQuery);

          if (matchesFilter && matchesSearch) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });

        // Update footer info baris
        document.getElementById('tableInfo').innerHTML = 'Menampilkan <strong>' + data.jumlahAntrian + '</strong> antrian';
      })
      .catch(function(err) { console.error('Gagal mengambil data antrian realtime:', err); });
  }, 5000); // 5000ms = 5 detik
</script>
@endpush