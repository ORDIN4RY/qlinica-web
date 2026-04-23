@extends('layouts.app')

@section('title', 'Kode ICDX')
@section('page-title', 'Kode ICDX')
@section('page-subtitle', 'Referensi International Classification of Diseases (ICD-10)')

@push('styles')
<style>
  /* ── STATS ── */
  .stat-card { transition: all .25s ease; }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(30,58,138,.13); }

  /* ── TABLE ── */
  .table-row { transition: background .12s; }
  .table-row:hover { background: #f0f7ff; }

  /* ── KODE BADGE ── */
  .kode-badge {
    display: inline-block;
    background: #eff6ff;
    color: #1d4ed8;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 8px;
    border: 1px solid #bfdbfe;
    letter-spacing: .5px;
  }

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
</style>
@endpush

@section('content') 

{{-- ─── STAT BANNER ───────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-7">

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-book-medical text-blue-600 text-base"></i>
      </div>
      <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">ICD-10</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ number_format($icdxs->total()) }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Kode Penyakit</div>
  </div>

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-filter text-emerald-600 text-base"></i>
      </div>
      <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Hasil</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ number_format($icdxs->total()) }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
      {{ $search ? 'Hasil Pencarian "' . $search . '"' : 'Menampilkan Semua' }}
    </div>
  </div>

  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-file-medical text-purple-600 text-base"></i>
      </div>
      <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Halaman</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ $icdxs->currentPage() }} / {{ $icdxs->lastPage() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Halaman Saat Ini</div>
  </div>

</div>

{{-- ─── TOOLBAR ────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 px-6 py-4 flex flex-wrap items-center justify-between gap-3">

  {{-- Per page --}}
  <div class="flex items-center gap-2 text-sm text-gray-500">
    <span>Tampilkan</span>
    <form method="GET" action="{{ route('admin.icdx') }}" id="perPageForm">
      <input type="hidden" name="search" value="{{ $search ?? '' }}">
      <select name="per_page" class="per-page-select" onchange="document.getElementById('perPageForm').submit()">
        @foreach([10, 15, 25, 50, 100] as $n)
          <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }}</option>
        @endforeach
      </select>
    </form>
    <span>entri</span>
  </div>

  {{-- Search --}}
  <form method="GET" action="{{ route('admin.icdx') }}" class="flex items-center gap-2">
    <input type="hidden" name="per_page" value="{{ $perPage }}">
    <div class="relative">
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
      <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}"
        placeholder="Cari kode atau nama penyakit..."
        class="pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 w-72 transition">
    </div>
    <button type="submit"
      class="bg-blue-900 hover:bg-blue-800 text-white text-sm px-4 py-2.5 rounded-xl transition font-semibold">
      Cari
    </button>
    @if($search)
      <a href="{{ route('admin.icdx', ['per_page' => $perPage]) }}"
        class="text-sm text-gray-500 hover:text-red-500 px-3 py-2.5 rounded-xl border border-gray-200 hover:border-red-200 transition"
        title="Hapus pencarian">
        <i class="fas fa-times"></i>
      </a>
    @endif
  </form>

</div>

{{-- ─── TABLE CARD ─────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[600px]">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">No</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-44">Kode ICDX</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama ICDX</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50" id="icdxTableBody">
        @forelse($icdxs as $i => $item)
          <tr class="table-row">
            {{-- No --}}
            <td class="px-5 py-4 text-gray-400 font-semibold text-xs">
              {{ $icdxs->firstItem() + $i }}
            </td>

            {{-- Kode --}}
            <td class="px-5 py-4">
              @if($search)
                <span class="kode-badge">{!! preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', e($item->kd_icdx)) !!}</span>
              @else
                <span class="kode-badge">{{ $item->kd_icdx }}</span>
              @endif
            </td>

            {{-- Nama ICDX --}}
            <td class="px-5 py-4 text-gray-700 font-medium">
              @if($search && $item->nama_icdx)
                {!! preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark>$1</mark>', e($item->nama_icdx)) !!}
              @else
                {{ $item->nama_icdx ?? '—' }}
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center py-20 text-gray-400">
              <i class="fas fa-file-medical text-5xl mb-4 block opacity-20"></i>
              <p class="font-semibold text-base">Tidak ada data ICDX{{ $search ? ' untuk pencarian "' . $search . '"' : '' }}</p>
              @if($search)
                <a href="{{ route('admin.icdx') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">
                  Tampilkan semua data
                </a>
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ─── PAGINATION ─────────────────────────────────────── --}}
  @if($icdxs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
      <p class="text-xs text-gray-500">
        Menampilkan <strong>{{ $icdxs->firstItem() }}–{{ $icdxs->lastItem() }}</strong>
        dari <strong>{{ number_format($icdxs->total()) }}</strong> kode penyakit
      </p>
      <div class="flex items-center gap-1">
        {{-- Prev --}}
        @if($icdxs->onFirstPage())
          <span class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 border border-gray-100 font-semibold text-xs cursor-not-allowed">
            <i class="fas fa-chevron-left text-xs"></i>
          </span>
        @else
          <a href="{{ $icdxs->previousPageUrl() }}"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">
            <i class="fas fa-chevron-left text-xs"></i>
          </a>
        @endif

        {{-- Page numbers (max 5 around current) --}}
        @php
          $current  = $icdxs->currentPage();
          $last     = $icdxs->lastPage();
          $start    = max(1, $current - 2);
          $end      = min($last, $current + 2);
        @endphp

        @if($start > 1)
          <a href="{{ $icdxs->url(1) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">1</a>
          @if($start > 2)
            <span class="w-9 h-9 flex items-center justify-center text-gray-400 text-xs">…</span>
          @endif
        @endif

        @for($p = $start; $p <= $end; $p++)
          @if($p == $current)
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-900 text-white font-bold text-xs">{{ $p }}</span>
          @else
            <a href="{{ $icdxs->url($p) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">{{ $p }}</a>
          @endif
        @endfor

        @if($end < $last)
          @if($end < $last - 1)
            <span class="w-9 h-9 flex items-center justify-center text-gray-400 text-xs">…</span>
          @endif
          <a href="{{ $icdxs->url($last) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">{{ $last }}</a>
        @endif

        {{-- Next --}}
        @if($icdxs->hasMorePages())
          <a href="{{ $icdxs->nextPageUrl() }}"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">
            <i class="fas fa-chevron-right text-xs"></i>
          </a>
        @else
          <span class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 border border-gray-100 font-semibold text-xs cursor-not-allowed">
            <i class="fas fa-chevron-right text-xs"></i>
          </span>
        @endif
      </div>
    </div>
  @else
    <div class="px-6 py-3 border-t border-gray-100">
      <p class="text-xs text-gray-400">Total <strong>{{ number_format($icdxs->total()) }}</strong> kode penyakit</p>
    </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
  // Auto-submit search on Enter, already handled by form submit
  // Highlight active row info
  document.querySelectorAll('.table-row').forEach(function(row) {
    row.addEventListener('click', function() {
      document.querySelectorAll('.table-row').forEach(r => r.style.background = '');
      this.style.background = '#e0f2fe';
    });
  });
</script>
@endpush
