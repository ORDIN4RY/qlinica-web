@extends('layouts.app')

@section('title', 'Data ICD-X')
@section('page-title', 'Data ICD-X')
@section('page-subtitle', 'Referensi kode diagnosa International Classification of Diseases')

@push('styles')
<style>
  /* ── TABLE ROW HOVER ── */
  .table-row { transition: background .12s; }
  .table-row:hover { background: #f0f7ff; }

  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:20px; width:100%; max-width:520px;
    display:flex; flex-direction:column;
    box-shadow:0 32px 80px rgba(15,23,42,.22);
    animation:modalIn .22s cubic-bezier(.4,0,.2,1); margin:16px; overflow:hidden; }
  @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:none} }
  .modal-head { padding:22px 28px 18px; border-bottom:1px solid #e5e7eb;
    display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
  .modal-body { padding:22px 28px; }
  .modal-foot { padding:16px 28px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0; }

  .form-group label { display:block; font-size:12px; font-weight:700;
    color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
  .form-input, .form-textarea {
    width:100%; padding:9px 13px; border:1.5px solid #e5e7eb;
    border-radius:10px; font-size:13.5px; color:#1e293b; background:#fff;
    outline:none; transition:all .16s; box-sizing:border-box; font-family:inherit; }
  .form-input:focus, .form-textarea:focus {
    border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
  .form-textarea { resize:vertical; min-height: 70px; }
  .form-input::placeholder, .form-textarea::placeholder { color:#9ca3af; }
  .form-input.error, .form-textarea.error { border-color:#ef4444; }
  .err-text { font-size:11px; color:#ef4444; margin-top:4px; display:none; }
  .err-text.show { display:block; }

  /* ── CONFIRM MODAL ── */
  .confirm-icon { width:52px; height:52px; border-radius:50%;
    display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }

  .kode-badge { font-family: monospace; font-size: 12px; font-weight: 700;
    background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 6px; }

  @media (max-width:640px) {
    .hide-sm { display: none; }
  }
</style>
@endpush

@section('content')

{{-- ─── FLASH MESSAGES ─────────────────────────────────────────────── --}}
@if(session('success'))
  <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl shadow-sm">
    <i class="fas fa-check-circle text-green-500 text-lg"></i>
    <span>{{ session('success') }}</span>
  </div>
@endif
@if(session('error'))
  <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-sm">
    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
    <span>{{ session('error') }}</span>
  </div>
@endif

{{-- ─── TOOLBAR ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
  <button id="btnTambah"
    class="flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-md">
    <i class="fas fa-plus text-xs"></i> Tambah ICD-X
  </button>

  <div class="flex items-center gap-3 flex-wrap">
    {{-- Per page --}}
    <form method="GET" action="{{ route('admin.icdx') }}" class="flex items-center gap-2">
      @if($search)
        <input type="hidden" name="search" value="{{ $search }}">
      @endif
      <label class="text-sm text-gray-500 whitespace-nowrap">Tampilkan</label>
      <select name="per_page" onchange="this.form.submit()"
        class="border border-gray-200 rounded-xl text-sm px-3 py-2 focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition">
        @foreach([10, 25, 50, 100] as $pp)
          <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
        @endforeach
      </select>
      <span class="text-sm text-gray-500">entri</span>
    </form>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.icdx') }}" class="flex items-center gap-2">
      <input type="hidden" name="per_page" value="{{ $perPage }}">
      <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
        <input type="text" name="search" value="{{ $search ?? '' }}"
          placeholder="Cari kode atau nama diagnosa..."
          class="pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 w-64 transition">
      </div>
      <button type="submit"
        class="bg-blue-900 hover:bg-blue-800 text-white text-sm px-4 py-2.5 rounded-xl transition font-semibold">
        Cari
      </button>
      @if($search)
        <a href="{{ route('admin.icdx', ['per_page' => $perPage]) }}"
          class="text-sm text-gray-500 hover:text-red-500 px-3 py-2.5 rounded-xl border border-gray-200 hover:border-red-200 transition">
          <i class="fas fa-times"></i>
        </a>
      @endif
    </form>
  </div>
</div>

{{-- ─── TABLE CARD ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[600px]">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-36">Kode ICD-X</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Diagnosa</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @if(isset($error))
          <tr>
            <td colspan="4" class="text-center py-8 text-red-500 font-semibold">
              <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
            </td>
          </tr>
        @endif
        
        @forelse($icdxs as $i => $icdx)
          <tr class="table-row">
            {{-- No --}}
            <td class="px-5 py-3.5 text-gray-400 font-semibold text-xs">
              {{ $icdxs instanceof \Illuminate\Pagination\LengthAwarePaginator ? $icdxs->firstItem() + $i : $i + 1 }}
            </td>
            {{-- Kode --}}
            <td class="px-5 py-3.5">
              <span class="kode-badge">{{ $icdx->kode }}</span>
              @if(isset($icdx->is_api))
                <span class="ml-2 text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">API WHO</span>
              @endif
            </td>
            {{-- Nama --}}
            <td class="px-5 py-3.5 text-gray-500 text-xs hide-sm">{{ $icdx->nama ?: '—' }}</td>
            {{-- Aksi --}}
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2">
                @if(isset($icdx->is_api))
                  <form method="POST" action="{{ route('admin.icdx.store') }}" class="m-0 p-0">
                    @csrf
                    <input type="hidden" name="kode" value="{{ $icdx->kode }}">
                    <input type="hidden" name="nama" value="{{ $icdx->nama }}">
                    <button type="submit" class="text-[11px] bg-blue-900 text-white hover:bg-blue-800 px-3 py-1.5 rounded-lg transition shadow-sm font-semibold flex items-center gap-1.5">
                      <i class="fas fa-download text-[10px]"></i> Simpan
                    </button>
                  </form>
                @else
                  <button
                    onclick="openEdit({{ $icdx->id }}, '{{ addslashes($icdx->kode) }}', '{{ addslashes($icdx->nama) }}')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                    title="Edit">
                    <i class="fas fa-pen text-xs"></i>
                  </button>
                  <button
                    onclick="openDel({{ $icdx->id }}, '{{ addslashes($icdx->kode) }}', '{{ addslashes($icdx->nama) }}')"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                    title="Hapus">
                    <i class="fas fa-trash text-xs"></i>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-16 text-gray-400">
              <i class="fas fa-file-medical-alt text-4xl mb-4 block opacity-30"></i>
              <p class="font-semibold">Tidak ada data ICD-X{{ $search ? ' untuk pencarian "' . $search . '"' : '' }}</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($icdxs instanceof \Illuminate\Pagination\LengthAwarePaginator && $icdxs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
      <p class="text-xs text-gray-500">
        Menampilkan <strong>{{ $icdxs->firstItem() }}–{{ $icdxs->lastItem() }}</strong>
        dari <strong>{{ number_format($icdxs->total()) }}</strong> data
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

        {{-- Pages --}}
        @foreach($icdxs->getUrlRange(max(1, $icdxs->currentPage()-2), min($icdxs->lastPage(), $icdxs->currentPage()+2)) as $page => $url)
          @if($page == $icdxs->currentPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-900 text-white font-bold text-xs">{{ $page }}</span>
          @else
            <a href="{{ $url }}"
              class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">
              {{ $page }}
            </a>
          @endif
        @endforeach

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
      <p class="text-xs text-gray-400">Total <strong>{{ number_format(count($icdxs)) }}</strong> data</p>
    </div>
  @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL TAMBAH / EDIT
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <div class="modal-head">
      <h2 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Data ICD-X</h2>
      <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>

    <form id="icdxForm" method="POST" action="{{ route('admin.icdx.store') }}">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">

      <div class="modal-body">
        <div class="space-y-4">

          {{-- WHO Search --}}
          <div class="form-group border-b border-gray-100 pb-4 mb-2">
            <label>Cari dari Database WHO (Online)</label>
            <div class="flex gap-2 relative">
              <input type="text" id="whoSearchInput" class="form-input flex-1" placeholder="Ketik kata kunci penyakit (Bhs. Inggris)...">
              <button type="button" id="btnWhoSearch" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-2 rounded-xl text-sm font-bold transition flex items-center justify-center">
                <i class="fas fa-search"></i>
              </button>
            </div>
            <div id="whoLoader" class="hidden mt-2 text-xs text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari...</div>
            <div id="whoResults" class="mt-2 max-h-48 overflow-y-auto hidden border border-gray-200 rounded-xl bg-gray-50 shadow-inner">
              <!-- Results go here -->
            </div>
          </div>

          {{-- Kode --}}
          <div class="form-group">
            <label>Kode ICD-X <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="kode" id="fKode" class="form-input"
              placeholder="Contoh: A00, B01.1" maxlength="10">
            <p class="err-text" id="errKode"></p>
          </div>

          {{-- Nama Indonesia --}}
          <div class="form-group">
            <label>Nama Diagnosa <span class="text-red-500 normal-case font-normal">*</span></label>
            <textarea name="nama" id="fNama" class="form-textarea"
              placeholder="Nama diagnosa dalam Bahasa Ilmiah"></textarea>
            <p class="err-text" id="errNama"></p>
          </div>

        </div>
      </div>

      <div class="modal-foot">
        <button type="button" onclick="closeModal()"
          class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
          Batal
        </button>
        <button type="submit" id="btnSimpan"
          class="px-6 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-sm font-bold transition shadow-md">
          <i class="fas fa-save mr-1.5"></i> <span id="btnSimpanText">Simpan</span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL HAPUS
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="delOverlay">
  <div class="bg-white rounded-2xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl" style="animation:modalIn .22s ease">
    <div class="confirm-icon bg-red-50">
      <i class="fas fa-trash text-red-500 text-xl"></i>
    </div>
    <h3 class="text-base font-bold text-gray-800 mb-2">Hapus Data ICD-X?</h3>
    <p class="text-sm text-gray-500 mb-6" id="delMsg">Data ini akan dihapus permanen.</p>
    <div class="flex justify-center gap-3">
      <button onclick="closeDel()"
        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
        Batal
      </button>
      <form id="delForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <button type="submit"
          class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition">
          Ya, Hapus
        </button>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  var BASE_URL = '{{ url("/admin/icdx") }}';
  var editingId = null;

  /* ── MODAL TAMBAH/EDIT ── */
  function openAdd() {
    editingId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Data ICD-X';
    document.getElementById('icdxForm').action        = '{{ route("admin.icdx.store") }}';
    document.getElementById('formMethod').value       = 'POST';
    document.getElementById('btnSimpanText').textContent = 'Simpan';
    clearForm();
    clearErrors();
    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function openEdit(id, kode, nama) {
    editingId = id;
    document.getElementById('modalTitle').textContent = 'Edit Data ICD-X';
    document.getElementById('icdxForm').action        = BASE_URL + '/' + id;
    document.getElementById('formMethod').value       = 'PUT';
    document.getElementById('btnSimpanText').textContent = 'Simpan Perubahan';
    clearErrors();

    document.getElementById('fKode').value = kode;
    document.getElementById('fNama').value = nama;

    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  function clearForm() {
    ['fKode', 'fNama'].forEach(function(id) {
      document.getElementById(id).value = '';
    });
    const whoSearchInput = document.getElementById('whoSearchInput');
    const whoResults = document.getElementById('whoResults');
    if(whoSearchInput) whoSearchInput.value = '';
    if(whoResults) {
        whoResults.innerHTML = '';
        whoResults.classList.add('hidden');
    }
  }

  function clearErrors() {
    ['errKode', 'errNama'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) { el.textContent = ''; el.classList.remove('show'); }
    });
    ['fKode', 'fNama'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) el.classList.remove('error');
    });
  }

  /* ── MODAL HAPUS ── */
  function openDel(id, kode, nama) {
    document.getElementById('delMsg').textContent = 'Kode "' + kode + '" — ' + nama.substring(0, 60) + (nama.length > 60 ? '...' : '') + ' akan dihapus permanen.';
    document.getElementById('delForm').action     = BASE_URL + '/' + id;
    document.getElementById('delOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDel() {
    document.getElementById('delOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  /* ── EVENTS ── */
  document.getElementById('btnTambah').addEventListener('click', openAdd);

  document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
  document.getElementById('delOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeDel();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal(); closeDel(); }
  });

  /* ── WHO API SEARCH ── */
  const whoSearchInput = document.getElementById('whoSearchInput');
  const btnWhoSearch = document.getElementById('btnWhoSearch');
  const whoLoader = document.getElementById('whoLoader');
  const whoResults = document.getElementById('whoResults');

  btnWhoSearch.addEventListener('click', performWhoSearch);
  whoSearchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      performWhoSearch();
    }
  });

  function performWhoSearch() {
    const q = whoSearchInput.value.trim();
    if (q.length < 2) return;

    whoLoader.classList.remove('hidden');
    whoResults.classList.add('hidden');
    whoResults.innerHTML = '';

    fetch(`/api/icd/search?q=${encodeURIComponent(q)}`)
      .then(res => res.json())
      .then(data => {
        whoLoader.classList.add('hidden');
        whoResults.classList.remove('hidden');
        
        if (data.error) {
          whoResults.innerHTML = `<div class="p-3 text-xs text-red-500 text-center">Gagal memuat: ${data.error}</div>`;
          return;
        }

        const entities = data.DestinationEntities || [];
        if (entities.length === 0) {
          whoResults.innerHTML = `<div class="p-3 text-xs text-gray-500 text-center">Tidak ada hasil ditemukan.</div>`;
          return;
        }

        let html = '<div class="divide-y divide-gray-200">';
        entities.forEach(entity => {
          // Hanya ambil jika memiliki theCode (artinya itu penyakit spesifik, bukan sekadar grup besar)
          if (entity.theCode) {
            // Bersihkan tag HTML dari Title
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = entity.Title || '';
            const cleanTitle = tempDiv.textContent || tempDiv.innerText || '';

            html += `
              <div class="p-3 hover:bg-blue-50 cursor-pointer transition flex justify-between items-center" onclick="selectWhoResult('${entity.theCode}', '${cleanTitle.replace(/'/g, "\\'")}')">
                <div>
                  <span class="font-bold text-xs text-blue-800">${entity.theCode}</span>
                  <p class="text-xs text-gray-600 mt-0.5">${cleanTitle}</p>
                </div>
                <i class="fas fa-arrow-right text-gray-300 text-xs"></i>
              </div>
            `;
          }
        });
        html += '</div>';

        if (html === '<div class="divide-y divide-gray-200"></div>') {
            html = `<div class="p-3 text-xs text-gray-500 text-center">Hasil tidak memiliki kode spesifik.</div>`;
        }

        whoResults.innerHTML = html;
      })
      .catch(err => {
        whoLoader.classList.add('hidden');
        whoResults.classList.remove('hidden');
        whoResults.innerHTML = `<div class="p-3 text-xs text-red-500 text-center">Terjadi kesalahan koneksi.</div>`;
      });
  }

  function selectWhoResult(kode, nama) {
    document.getElementById('fKode').value = kode;
    document.getElementById('fNama').value = nama;
    whoResults.classList.add('hidden');
    whoSearchInput.value = '';
  }
</script>
@endpush
