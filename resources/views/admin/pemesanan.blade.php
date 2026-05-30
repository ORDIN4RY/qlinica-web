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
  .s-menunggu-ttv { background:#faf5ff; color:#7c3aed; }
  .s-menunggu-ttv::before { background:#7c3aed; }
  .s-menunggu-dokter { background:#eff6ff; color:#2563eb; }
  .s-menunggu-dokter::before { background:#2563eb; }
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

  .btn-ttv {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 700;
    padding: 5px 12px; border-radius: 8px;
    background: #faf5ff; color: #7c3aed;
    border: 1px solid #e9d5ff; cursor: pointer;
    transition: all .15s; white-space: nowrap;
  }
  .btn-ttv:hover { background: #f3e8ff; }

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

    {{-- Tombol Layar Display --}}
    <a href="{{ route('antrian.display') }}" target="_blank" id="btnLayarDisplay"
       title="Buka layar display antrian untuk ruang tunggu"
       style="display:inline-flex;align-items:center;gap:8px;background:rgba(124,58,237,0.08);color:#7c3aed;font-size:13.5px;font-weight:700;padding:10px 18px;border-radius:12px;border:1.5px solid rgba(124,58,237,0.25);text-decoration:none;transition:all .2s;white-space:nowrap;">
      <i class="fas fa-tv text-xs"></i> Layar Display
    </a>

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
                @if($a->rekamMedis)
                  <span class="status-badge s-menunggu-dokter">Menunggu Dokter</span>
                @else
                  <span class="status-badge s-menunggu-ttv">Menunggu TTV</span>
                @endif
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
                  <button type="button" class="btn-panggil" onclick="panggilStatusLangsung({{ $a->id }}, '{{ addslashes($a->pasien->nama ?? '') }}')" title="Panggil Pasien">
                    <i class="fas fa-bullhorn text-xs"></i> Panggil
                  </button>
                @elseif($st === 'dipanggil')
                  @if(!$a->rekamMedis)
                    <button type="button" class="btn-ttv" onclick="openPanggil({{ $a->id }}, '{{ addslashes($a->pasien->nama ?? '') }}', '{{ $a->pasien->no_bpjs ?? '' }}')" title="Pemeriksaan Awal TTV">
                      <i class="fas fa-notes-medical text-xs"></i> Pemeriksaan Awal
                    </button>
                    <button type="button" class="btn-panggil" onclick="panggilStatusLangsung({{ $a->id }}, '{{ addslashes($a->pasien->nama ?? '') }}')" title="Panggil Ulang Pasien">
                      <i class="fas fa-redo text-xs"></i> Panggil Ulang
                    </button>
                  @endif
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
  <div class="modal-box shadow-2xl rounded-2xl border border-gray-100 overflow-hidden bg-white">
    <div class="modal-head bg-slate-50 border-b border-slate-100 flex items-center justify-between px-6 py-4">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
          <i class="fas fa-heartbeat text-sm"></i>
        </div>
        <h2 class="text-base font-bold text-gray-800 font-sora">Pemeriksaan Awal (TTV)</h2>
      </div>
      <button type="button" onclick="closePanggil()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <form id="panggilForm" method="POST" action="">
      @csrf
      <div class="modal-body px-6 py-5">
        <div class="bg-blue-50/50 rounded-xl p-3.5 border border-blue-100/50 flex items-center gap-3 mb-5">
          <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-700 flex items-center justify-center">
            <i class="fas fa-user-injured text-sm"></i>
          </div>
          <div>
            <div class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Pasien Yang Diperiksa</div>
            <div class="text-sm font-bold text-gray-800" id="panggilPasienName"></div>
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 font-sora">
          {{-- Jenis Pelayanan (BPJS/Umum) --}}
          <div class="form-group col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Pelayanan <span class="text-red-500">*</span></label>
            <select id="selectJenisPelayanan" name="jenis_pelayanan" class="form-select border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" required>
              <option value="">— Pilih Jenis —</option>
              <option value="Umum">Umum</option>
              <option value="BPJS">BPJS</option>
            </select>
          </div>

          {{-- Input BPJS (Ditampilkan dinamis jika BPJS dipilih) --}}
          <div id="bpjsInputGroup" class="form-group col-span-2 hidden bg-blue-50/50 p-4 rounded-xl border border-blue-100">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nomor Kartu BPJS / NIK <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="text" name="no_bpjs" id="inputNoBpjs" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="Masukkan 13 digit No Kartu atau 16 digit NIK">
              <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
            <p class="text-[10px] text-gray-500 mt-2"><i class="fas fa-info-circle"></i> Sistem akan otomatis memvalidasi keaktifan kartu BPJS saat form disubmit.</p>
          </div>

          {{-- Layanan Kesehatan (Poli) --}}
          <div class="form-group col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Layanan Kesehatan <span class="text-red-500">*</span></label>
            <select name="pelayanan_kesehatan" class="form-select border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" required>
              <option value="">— Pilih Layanan —</option>
              <option value="Poli Umum">Poli Umum</option>
              <option value="Poli Gigi">Poli Gigi</option>
              <option value="Poli KIA">Poli KIA</option>
              <option value="UGD">UGD</option>
              <option value="Laboratorium">Laboratorium</option>
              <option value="Baby Spa">Baby Spa</option>
            </select>
          </div>

          {{-- Vital Signs --}}
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tekanan Darah <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="text" name="tekanan_darah" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="120/80" required>
              <i class="fas fa-gauge absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Suhu (°C) <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" step="0.1" name="suhu" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="36.5" required min="30" max="45">
              <i class="fas fa-temperature-half absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Berat Badan (kg) <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" step="0.1" name="berat_badan" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="60.5" required min="1" max="200">
              <i class="fas fa-weight-scale absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tinggi Badan (cm) <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" step="0.1" name="tinggi_badan" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="165" required min="30" max="250">
              <i class="fas fa-ruler-vertical absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nadi (x/menit) <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" name="nadi" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="80" required min="40" max="200">
              <i class="fas fa-heartbeat absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Respirasi (x/menit) <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" name="respirasi" class="form-input pl-9 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 rounded-xl transition" placeholder="20" required min="10" max="60">
              <i class="fas fa-wind absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-foot bg-slate-50 border-t border-slate-100 flex justify-end gap-3 px-6 py-4">
        <button type="button" onclick="closePanggil()"
          class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-100 transition">
          Batal
        </button>
        <button type="submit"
          class="px-6 py-2.5 rounded-xl text-white text-xs font-bold transition shadow-md hover:brightness-110 flex items-center gap-2"
          style="background:linear-gradient(135deg,#059669,#10b981)">
          <i class="fas fa-check"></i> Simpan & Panggil
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

{{-- ── CUSTOM ALERT MODAL ── --}}
<div class="modal-overlay" id="customAlertModal">
  <div class="bg-white rounded-2xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl" style="animation:modalIn .22s ease">
    <div class="confirm-icon bg-blue-50 text-blue-500 mb-4 mx-auto w-12 h-12 rounded-full flex items-center justify-center">
      <i id="customAlertIcon" class="fas fa-info-circle text-xl text-blue-500"></i>
    </div>
    <h3 class="text-base font-bold text-gray-800 mb-2" id="customAlertTitle">Informasi</h3>
    <p class="text-sm text-gray-500 mb-6" id="customAlertMessage">Pesan informasi.</p>
    <div class="flex justify-center">
      <button id="customAlertBtn"
        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition">
        OK
      </button>
    </div>
  </div>
</div>

{{-- ── CUSTOM CONFIRM MODAL ── --}}
<div class="modal-overlay" id="customConfirmModal">
  <div class="bg-white rounded-2xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl" style="animation:modalIn .22s ease">
    <div id="customConfirmIconContainer" class="confirm-icon bg-blue-50 text-blue-500 mb-4 mx-auto w-12 h-12 rounded-full flex items-center justify-center">
      <i id="customConfirmIcon" class="fas fa-question-circle text-xl text-blue-500"></i>
    </div>
    <h3 class="text-base font-bold text-gray-800 mb-2" id="customConfirmTitle">Konfirmasi</h3>
    <p class="text-sm text-gray-500 mb-6" id="customConfirmMessage">Apakah Anda yakin?</p>
    <div class="flex justify-center gap-3">
      <button id="customConfirmNoBtn"
        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
        Batal
      </button>
      <button id="customConfirmYesBtn"
        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition">
        Ya
      </button>
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
  function openPanggil(id, nama, noBpjs = '') {
    document.getElementById('panggilPasienName').textContent = nama;
    document.getElementById('panggilForm').action = BASE_ANTRIAN + '/' + id + '/panggil';
    document.getElementById('inputNoBpjs').value = noBpjs;
    
    // Reset BPJS selection
    document.getElementById('selectJenisPelayanan').value = '';
    document.getElementById('bpjsInputGroup').classList.add('hidden');
    document.getElementById('inputNoBpjs').required = false;

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

  /* ── TOGGLE BPJS TTV ── */
  document.getElementById('selectJenisPelayanan').addEventListener('change', function() {
    const bpjsGroup = document.getElementById('bpjsInputGroup');
    const inputBpjs = document.getElementById('inputNoBpjs');
    if (this.value === 'BPJS') {
      bpjsGroup.classList.remove('hidden');
      inputBpjs.required = true;
    } else {
      bpjsGroup.classList.add('hidden');
      inputBpjs.required = false;
      inputBpjs.value = '';
    }
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

  /* ── CUSTOM ALERT / CONFIRM UTILITIES ── */
  function showCustomAlert(title, message, type = 'info') {
    return new Promise((resolve) => {
      const modal = document.getElementById('customAlertModal');
      const titleEl = document.getElementById('customAlertTitle');
      const messageEl = document.getElementById('customAlertMessage');
      const iconEl = document.getElementById('customAlertIcon');
      const btnEl = document.getElementById('customAlertBtn');
      const iconContainer = iconEl.parentElement;

      titleEl.textContent = title;
      messageEl.textContent = message;

      if (type === 'danger' || type === 'error') {
        iconContainer.className = "confirm-icon bg-red-50 text-red-500 mb-4 mx-auto w-12 h-12 rounded-full flex items-center justify-center";
        iconEl.className = "fas fa-exclamation-circle text-xl text-red-500";
        btnEl.className = "px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition shadow-md hover:shadow-lg";
      } else {
        iconContainer.className = "confirm-icon bg-blue-50 text-blue-500 mb-4 mx-auto w-12 h-12 rounded-full flex items-center justify-center";
        iconEl.className = "fas fa-info-circle text-xl text-blue-500";
        btnEl.className = "px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition shadow-md hover:shadow-lg";
      }

      modal.classList.add('open');
      document.body.style.overflow = 'hidden';

      const handleClose = () => {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        btnEl.removeEventListener('click', handleClose);
        resolve();
      };
      btnEl.addEventListener('click', handleClose);
    });
  }

  function showCustomConfirm(title, message, options = {}) {
    return new Promise((resolve) => {
      const modal = document.getElementById('customConfirmModal');
      const titleEl = document.getElementById('customConfirmTitle');
      const messageEl = document.getElementById('customConfirmMessage');
      const iconContainer = document.getElementById('customConfirmIconContainer');
      const iconEl = document.getElementById('customConfirmIcon');
      const btnYes = document.getElementById('customConfirmYesBtn');
      const btnNo = document.getElementById('customConfirmNoBtn');

      titleEl.textContent = title;
      messageEl.textContent = message;

      btnYes.textContent = options.yesText || 'Ya';
      btnNo.textContent = options.noText || 'Batal';

      if (options.type === 'danger') {
        iconContainer.className = "confirm-icon bg-red-50 text-red-500 mb-4 mx-auto w-12 h-12 rounded-full flex items-center justify-center";
        iconEl.className = "fas fa-exclamation-triangle text-xl text-red-500";
        btnYes.className = "px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition shadow-md hover:shadow-lg";
      } else {
        iconContainer.className = "confirm-icon bg-blue-50 text-blue-500 mb-4 mx-auto w-12 h-12 rounded-full flex items-center justify-center";
        iconEl.className = "fas fa-question-circle text-xl text-blue-500";
        btnYes.className = "px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition shadow-md hover:shadow-lg";
      }

      modal.classList.add('open');
      document.body.style.overflow = 'hidden';

      const cleanup = (result) => {
        modal.classList.remove('open');
        document.body.style.overflow = '';
        btnYes.removeEventListener('click', handleYes);
        btnNo.removeEventListener('click', handleNo);
        resolve(result);
      };

      const handleYes = () => cleanup(true);
      const handleNo = () => cleanup(false);

      btnYes.addEventListener('click', handleYes);
      btnNo.addEventListener('click', handleNo);
    });
  }

  function closeCustomAlert() {
    const modal = document.getElementById('customAlertModal');
    if (modal && modal.classList.contains('open')) {
      const btnEl = document.getElementById('customAlertBtn');
      if (btnEl) btnEl.click();
    }
  }

  function closeCustomConfirm() {
    const modal = document.getElementById('customConfirmModal');
    if (modal && modal.classList.contains('open')) {
      const btnNo = document.getElementById('customConfirmNoBtn');
      if (btnNo) btnNo.click();
    }
  }

  /* ── ESCAPE ── */
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModalAntrian();
      closeBatal();
      closePanggil();
      closeCustomAlert();
      closeCustomConfirm();
    }
  });

  document.querySelector('#modalAntrian form').addEventListener('submit', function(e) {
    if (!selectedId.value) {
      e.preventDefault();
      showCustomAlert('Pasien Belum Dipilih', 'Silakan pilih pasien dari daftar sebelum menyimpan antrian.', 'error').then(() => {
        searchPasienInput.focus();
      });
    }
  });

  function loadRealtimeData() {
    return fetch('{{ route("admin.antrian.realtime") }}')
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
  }

  // Polling Realtime Antrian (setiap 5 detik)
  setInterval(loadRealtimeData, 5000); // 5000ms = 5 detik

  /* ══════════════════════════════════════════════
     VOICE TTS — SUARA PEREMPUAN INSTANSI
     (berjalan langsung di halaman ini, tanpa
     perlu membuka layar Display terlebih dahulu)
  ══════════════════════════════════════════════ */
  const POLI_TTS_MAP_ADM = {
    'Poli Umum'    : 'Poli Umum',
    'Poli Gigi'    : 'Poli Gigi',
    'Poli KIA'     : 'Poli K. I. A.',
    'UGD'          : 'Unit Gawat Darurat',
    'Laboratorium' : 'Laboratorium',
    'Baby Spa'     : 'Baby Spa',
  };

  let _femaleVoiceAdm = null;

  function _loadVoicesAdm() {
    const voices = window.speechSynthesis.getVoices();
    const candidates = [
      voices.find(v => v.lang === 'id-ID' && /female|wanita|perempuan|woman/i.test(v.name)),
      voices.find(v => v.lang.startsWith('id') && /female|woman/i.test(v.name)),
      voices.find(v => v.lang === 'ms-MY' && /female|woman/i.test(v.name)),
      voices.find(v => /google bahasa indonesia|google id/i.test(v.name)),
      voices.find(v => /microsoft andi|zira|hazel|susan|karen|samantha|moira|fiona|tessa/i.test(v.name)),
      voices.find(v => v.lang === 'id-ID'),
      voices.find(v => v.lang.startsWith('id')),
      voices.find(v => v.lang === 'ms-MY'),
      voices.find(v => /female|woman|zira|hazel|susan|karen|samantha/i.test(v.name)),
    ];
    _femaleVoiceAdm = candidates.find(v => v !== undefined) || null;
  }

  if ('speechSynthesis' in window) {
    window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged = _loadVoicesAdm;
    setTimeout(_loadVoicesAdm, 300);
  }

  function _speakPanggil(noAntrian, nama, poli) {
    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();

    const poliText = poli ? (POLI_TTS_MAP_ADM[poli] || poli) : null;
    const digitized = noAntrian.split('').join(', ');

    const text =
      'Perhatian. ' +
      'Nomor antrian, ' + digitized + '. ' +
      'Nomor antrian, ' + digitized + '. ' +
      (poliText
        ? 'Dimohon kepada pasien atas nama ' + (nama || 'yang bersangkutan') + ', harap segera menuju ' + poliText + '. '
        : 'Dimohon kepada pasien atas nama ' + (nama || 'yang bersangkutan') + ', harap segera menuju loket pendaftaran. ') +
      'Terima kasih.';

    setTimeout(() => {
      const utter  = new SpeechSynthesisUtterance(text);
      utter.lang   = 'id-ID';
      utter.rate   = 0.88;
      utter.pitch  = 1.15;
      utter.volume = 1;
      if (_femaleVoiceAdm) utter.voice = _femaleVoiceAdm;
      window.speechSynthesis.speak(utter);
    }, 200);
  }

  /* ─── Toast notifikasi panggilan ─── */
  (function _injectCallToast() {
    if (document.getElementById('_admCallToast')) return;
    const el = document.createElement('div');
    el.id = '_admCallToast';
    el.innerHTML = `
      <div id="_admCallToastInner" style="
        position:fixed; top:20px; left:50%; transform:translateX(-50%) translateY(-100px);
        z-index:9999; background:#1e3a8a; border:1px solid rgba(96,165,250,.35);
        border-radius:20px; padding:14px 26px; display:flex; align-items:center; gap:14px;
        box-shadow:0 16px 48px rgba(0,0,0,.2); min-width:300px;
        opacity:0; transition:transform .45s cubic-bezier(.34,1.56,.64,1), opacity .3s;">
        <div style="width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:12px;
          display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;flex-shrink:0;">
          <i class="fas fa-bullhorn"></i>
        </div>
        <div>
          <div id="_admToastNum"  style="font-family:'Sora',sans-serif;font-size:24px;font-weight:900;color:#bfdbfe;"></div>
          <div id="_admToastSub"  style="font-size:11px;color:rgba(255,255,255,.7);font-weight:600;margin-top:2px;"></div>
        </div>
      </div>`;
    document.body.appendChild(el);
  })();

  function _showCallToast(noAntrian, nama, poli) {
    const inner = document.getElementById('_admCallToastInner');
    if (!inner) return;
    document.getElementById('_admToastNum').textContent = 'No. ' + noAntrian;
    document.getElementById('_admToastSub').textContent = poli
      ? 'Harap menuju ' + poli + ' — ' + (nama || '')
      : 'Menuju loket pendaftaran — ' + (nama || '');
    inner.style.transform = 'translateX(-50%) translateY(0)';
    inner.style.opacity   = '1';
    clearTimeout(inner._toastTimer);
    inner._toastTimer = setTimeout(() => {
      inner.style.transform = 'translateX(-50%) translateY(-100px)';
      inner.style.opacity   = '0';
    }, 6500);
  }

  /* ══════════════════════════════════════════════
     PANGGIL STATUS LANGSUNG
  ══════════════════════════════════════════════ */
  async function panggilStatusLangsung(id, nama) {
    const confirmed = await showCustomConfirm(
      'Konfirmasi Panggilan',
      'Panggil pasien "' + nama + '"? Status antrian akan berubah menjadi Dipanggil.',
      { yesText: 'Ya, Panggil', noText: 'Batal' }
    );
    if (!confirmed) return;

    try {
      const response = await fetch(BASE_ANTRIAN + '/' + id + '/status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ _method: 'PATCH', status: 'Dipanggil' })
      });

      const data = await response.json();
      if (data.success) {
        // Langsung bunyikan suara & tampilkan toast — tanpa buka layar display
        _speakPanggil(data.no_antrian, data.nama, data.poli);
        _showCallToast(data.no_antrian, data.nama, data.poli);
        loadRealtimeData();
      } else {
        showCustomAlert('Gagal', data.message || 'Gagal mengubah status antrian.', 'error');
      }
    } catch (err) {
      console.error(err);
      showCustomAlert('Kesalahan', 'Terjadi kesalahan saat menghubungi server.', 'error');
    }
  }
</script>
@endpush