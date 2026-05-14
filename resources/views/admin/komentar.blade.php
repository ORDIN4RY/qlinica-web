@extends('layouts.app')

@section('page_title', 'Komentar Pasien')
@section('title', 'Komentar Pasien')
@section('breadcrumb', 'Kelola Komentar')
@section('nav_komentar', 'aktif')
@section('page-subtitle', 'Kelola komentar dan penilaian pasien untuk meningkatkan kualitas layanan klinik')

@push('styles')
<style>
  /* ── STAT CARD ── */
  .stat-card { transition: all .25s ease; }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(30,58,138,.13); }

  /* ── STAR ── */
  .star-filled { color: #f59e0b; }
  .star-empty  { color: #e2e8f0; }

  /* ── RATING BADGE ── */
  .rating-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 3px 8px;
    font-size: 12px;
    font-weight: 700;
    color: #92400e;
  }

  /* ── NOTE CELL ── */
  .note-cell {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* ── TABLE ROW ── */
  .table-row { transition: background .12s; }
  .table-row:hover { background: #f0f7ff; }

  /* ── PER PAGE SELECT ── */
  .per-page-select {
    padding: 6px 28px 6px 10px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 13px;
    color: #374151;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6,9 12,15 18,9'/%3E%3C/svg%3E") no-repeat right 6px center;
    appearance: none;
    outline: none;
    transition: all .16s;
    cursor: pointer;
  }
  .per-page-select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

  /* ── HIGHLIGHT ── */
  mark { background: #fef9c3; color: #713f12; border-radius: 3px; padding: 0 2px; }

  /* ── MODAL ── */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 33, 68, .45);
    z-index: 500;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
  }
  .modal-overlay.open { display: flex; }
  .modal-box {
    background: #fff;
    border-radius: 20px;
    width: 400px;
    max-width: 95vw;
    padding: 32px 28px;
    box-shadow: 0 24px 72px rgba(15,33,68,.18);
    animation: modalIn .2s ease;
    text-align: center;
  }
  @keyframes modalIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: none; }
  }

  /* ── TOAST ── */
  .toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 12px 22px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    z-index: 999;
    opacity: 0;
    transform: translateY(10px);
    transition: all .25s ease;
    pointer-events: none;
    box-shadow: 0 18px 38px rgba(0,0,0,.16);
  }
  .toast.show { opacity: 1; transform: translateY(0); }
  .toast-success { background: #16a34a; color: #fff; }
  .toast-error   { background: #dc2626; color: #fff; }
</style>
@endpush

@section('content')

{{-- ─── FLASH TOAST ──────────────────────────────────────────────────── --}}
@if(session('success'))
  <div id="flashToast" class="toast toast-success">{{ session('success') }}</div>
@elseif(session('error'))
  <div id="flashToast" class="toast toast-error">{{ session('error') }}</div>
@endif

{{-- ─── STAT BANNER ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-7">

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-comments text-blue-600 text-base"></i>
      </div>
      <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ number_format($komentars->total()) }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Komentar</div>
  </div>

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-star text-amber-500 text-base"></i>
      </div>
      <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Rating</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ number_format($avgRating ?? 0, 1) }}
      <span class="text-base font-semibold text-gray-400">/5</span>
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Rata-rata Penilaian</div>
  </div>

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-filter text-emerald-600 text-base"></i>
      </div>
      <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Hasil</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ number_format($komentars->total()) }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
      {{ $search ? 'Hasil "' . $search . '"' : 'Menampilkan Semua' }}
    </div>
  </div>

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-file-alt text-purple-600 text-base"></i>
      </div>
      <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Halaman</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ $komentars->currentPage() }} / {{ $komentars->lastPage() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Halaman Saat Ini</div>
  </div>

</div>

{{-- ─── TOOLBAR ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 px-6 py-4 flex flex-wrap items-center justify-between gap-3">

  <div class="flex items-center gap-2 text-sm text-gray-500">
    <span>Tampilkan</span>
    <form method="GET" action="{{ route('admin.komentar') }}" id="perPageForm">
      <input type="hidden" name="search" value="{{ $search ?? '' }}">
      <select name="per_page" class="per-page-select" onchange="document.getElementById('perPageForm').submit()">
        @foreach([10, 25, 50, 100] as $n)
          <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
        @endforeach
      </select>
    </form>
    <span>entri</span>
  </div>

  <form method="GET" action="{{ route('admin.komentar') }}" class="flex items-center gap-2">
    <input type="hidden" name="per_page" value="{{ $perPage }}">
    <div class="relative">
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
      <input type="text" name="search" value="{{ $search ?? '' }}"
        placeholder="Cari nama pasien atau No RM..."
        class="pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 w-72 transition">
    </div>
    <button type="submit"
      class="bg-blue-900 hover:bg-blue-800 text-white text-sm px-4 py-2.5 rounded-xl transition font-semibold">
      Cari
    </button>
    @if($search)
      <a href="{{ route('admin.komentar', ['per_page' => $perPage]) }}"
        class="text-sm text-gray-500 hover:text-red-500 px-3 py-2.5 rounded-xl border border-gray-200 hover:border-red-200 transition"
        title="Hapus pencarian">
        <i class="fas fa-times"></i>
      </a>
    @endif
  </form>

</div>

{{-- ─── TABLE ────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[900px]">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-14">No</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">No RM</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pasien</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Penilaian</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-44">Kritik</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-44">Saran</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Tanggal</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($komentars as $i => $item)
          <tr class="table-row">

            <td class="px-5 py-4 text-gray-400 font-semibold text-xs">
              {{ $komentars->firstItem() + $i }}
            </td>

            <td class="px-5 py-4">
              <span class="inline-block bg-gray-100 text-gray-700 font-mono text-xs font-bold px-2.5 py-1 rounded-lg">
                @if($search && $item->pasien)
                  {!! preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', e($item->pasien->no_rm)) !!}
                @else
                  {{ $item->pasien->no_rm ?? '—' }}
                @endif
              </span>
            </td>

            <td class="px-5 py-4 font-semibold text-gray-800">
              @if($search && $item->pasien)
                {!! preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', e($item->pasien->nama)) !!}
              @else
                {{ $item->pasien->nama ?? '—' }}
              @endif
            </td>

            <td class="px-5 py-4">
              @if($item->penilaian)
                <div class="rating-badge mb-1">
                  <i class="fas fa-star star-filled text-xs"></i>
                  {{ $item->penilaian }}
                </div>
                <div class="flex gap-0.5">
                  @for($s = 1; $s <= 5; $s++)
                    <i class="fas fa-star text-xs {{ $s <= $item->penilaian ? 'star-filled' : 'star-empty' }}"></i>
                  @endfor
                </div>
              @else
                <span class="text-gray-300 text-xs">—</span>
              @endif
            </td>

            <td class="px-5 py-4 text-gray-500 text-xs note-cell" title="{{ $item->kritik ?: '—' }}">
              {{ $item->kritik ?: '—' }}
            </td>

            <td class="px-5 py-4 text-gray-500 text-xs note-cell" title="{{ $item->saran ?: '—' }}">
              {{ $item->saran ?: '—' }}
            </td>

            <td class="px-5 py-4 text-gray-400 text-xs font-medium whitespace-nowrap">
              <i class="far fa-calendar-alt mr-1"></i>
              {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y') : '—' }}
            </td>

            <td class="px-5 py-4">
              <button
                onclick="openModal({{ $item->id }}, '{{ addslashes($item->pasien->nama ?? 'Pasien') }}')"
                class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 hover:border-red-300 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                <i class="fas fa-trash-alt text-xs"></i>
                Hapus
              </button>
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-20 text-gray-400">
              <i class="fas fa-comment-slash text-5xl mb-4 block opacity-20"></i>
              <p class="font-semibold text-base">
                Tidak ada komentar{{ $search ? ' untuk pencarian "' . $search . '"' : '' }}
              </p>
              @if($search)
                <a href="{{ route('admin.komentar') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">
                  Tampilkan semua data
                </a>
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ─── PAGINATION ──────────────────────────────────────────────── --}}
  @if($komentars->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
      <p class="text-xs text-gray-500">
        Menampilkan <strong>{{ $komentars->firstItem() }}–{{ $komentars->lastItem() }}</strong>
        dari <strong>{{ number_format($komentars->total()) }}</strong> komentar
      </p>
      <div class="flex items-center gap-1">

        @if($komentars->onFirstPage())
          <span class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 border border-gray-100 text-xs cursor-not-allowed">
            <i class="fas fa-chevron-left text-xs"></i>
          </span>
        @else
          <a href="{{ $komentars->previousPageUrl() }}"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-700 text-xs transition">
            <i class="fas fa-chevron-left text-xs"></i>
          </a>
        @endif

        @php
          $current = $komentars->currentPage();
          $last    = $komentars->lastPage();
          $start   = max(1, $current - 2);
          $end     = min($last, $current + 2);
        @endphp

        @if($start > 1)
          <a href="{{ $komentars->url(1) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">1</a>
          @if($start > 2)
            <span class="w-9 h-9 flex items-center justify-center text-gray-400 text-xs">…</span>
          @endif
        @endif

        @for($p = $start; $p <= $end; $p++)
          @if($p == $current)
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-900 text-white font-bold text-xs">{{ $p }}</span>
          @else
            <a href="{{ $komentars->url($p) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">{{ $p }}</a>
          @endif
        @endfor

        @if($end < $last)
          @if($end < $last - 1)
            <span class="w-9 h-9 flex items-center justify-center text-gray-400 text-xs">…</span>
          @endif
          <a href="{{ $komentars->url($last) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">{{ $last }}</a>
        @endif

        @if($komentars->hasMorePages())
          <a href="{{ $komentars->nextPageUrl() }}"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-700 text-xs transition">
            <i class="fas fa-chevron-right text-xs"></i>
          </a>
        @else
          <span class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 border border-gray-100 text-xs cursor-not-allowed">
            <i class="fas fa-chevron-right text-xs"></i>
          </span>
        @endif

      </div>
    </div>
  @else
    <div class="px-6 py-3 border-t border-gray-100">
      <p class="text-xs text-gray-400">Total <strong>{{ number_format($komentars->total()) }}</strong> komentar</p>
    </div>
  @endif
</div>

{{-- ─── MODAL HAPUS ─────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <i class="fas fa-trash-alt text-red-500 text-xl"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Komentar?</h3>
    <p class="text-sm text-gray-500 mb-6 leading-relaxed" id="modalMsg">
      Komentar ini akan dihapus secara permanen dan tidak dapat dikembalikan.
    </p>
    <div class="flex justify-center gap-3">
      <button onclick="closeModal()"
        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
        Batal
      </button>
      <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <button type="submit"
          class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
          Ya, Hapus
        </button>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function openModal(id, nama) {
    document.getElementById('modalMsg').textContent =
      'Komentar dari "' + nama + '" akan dihapus permanen dan tidak dapat dikembalikan.';
    document.getElementById('deleteForm').action = '/admin/komentar/' + id;
    document.getElementById('modalOverlay').classList.add('open');
  }
  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
  }
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  var flash = document.getElementById('flashToast');
  if (flash) {
    setTimeout(function() { flash.classList.add('show'); }, 100);
    setTimeout(function() { flash.classList.remove('show'); }, 3500);
  }

  document.querySelectorAll('.table-row').forEach(function(row) {
    row.addEventListener('click', function(e) {
      if (e.target.closest('button') || e.target.closest('form')) return;
      document.querySelectorAll('.table-row').forEach(r => r.style.background = '');
      this.style.background = '#e0f2fe';
    });
  });
</script>
@endpush
