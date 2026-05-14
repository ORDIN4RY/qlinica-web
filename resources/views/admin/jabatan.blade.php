@extends('layouts.app')

@section('title', 'Kelola Jabatan & Hak Akses')
@section('page-title', 'Kelola Jabatan & Hak Akses')
@section('page-subtitle', 'Atur hak akses menu untuk setiap jabatan pegawai')

@push('styles')
<style>
  /* ── Tab Jabatan ── */
  .jabatan-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .jabatan-tab-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    background: #fff;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    transition: all .18s;
    white-space: nowrap;
  }
  .jabatan-tab-btn:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
  .jabatan-tab-btn.active {
    border-color: #1d4ed8;
    background: #1d4ed8;
    color: #fff;
    box-shadow: 0 4px 12px rgba(29,78,216,.25);
  }
  .jabatan-tab-btn .badge {
    border-radius: 20px;
    padding: 1px 8px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(255,255,255,.25);
  }
  .jabatan-tab-btn:not(.active) .badge { background: #f3f4f6; color: #9ca3af; }

  /* ── Tombol hapus tab ── */
  .tab-delete-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: rgba(255,255,255,.3);
    color: inherit;
    font-size: 10px;
    transition: background .15s;
    border: none; cursor: pointer; padding: 0; line-height: 1;
  }
  .jabatan-tab-btn:not(.active) .tab-delete-btn { background: #fee2e2; color: #dc2626; }
  .jabatan-tab-btn.active .tab-delete-btn:hover { background: rgba(255,255,255,.5); }
  .jabatan-tab-btn:not(.active) .tab-delete-btn:hover { background: #fca5a5; }

  /* ── Panel Hak Akses ── */
  .akses-panel { display: none; }
  .akses-panel.active { display: block; animation: fadeSlideIn .22s ease; }
  @keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── Tabel ── */
  .access-table { border-collapse: separate; border-spacing: 0; }
  .access-table th { background: #f8fafc; }
  .access-table td, .access-table th { border-bottom: 1px solid #e5e7eb; }
  .access-table tr:last-child td { border-bottom: none; }
  .access-table tbody tr:hover td { background: #f0f7ff; }

  /* ── Checkbox ── */
  .cb-wrap { display: inline-flex; align-items: center; gap: 6px; cursor: pointer; }
  .cb-wrap input[type="checkbox"] { width: 17px; height: 17px; accent-color: #2563eb; cursor: pointer; }
  .cb-wrap input[type="checkbox"]:disabled { opacity: .35; cursor: not-allowed; }
  .cb-label { font-size: 12px; color: #4b5563; user-select: none; }

  /* ── Select All row ── */
  .select-all-row td { background: #f1f5f9 !important; }
  .select-all-row td:first-child { font-weight: 700; color: #374151; font-size: 12px; }

  /* ── Modal ── */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 9999;
    align-items: center; justify-content: center;
  }
  .modal-overlay.open { display: flex; }
  .modal-box {
    background: #fff; border-radius: 20px; padding: 28px;
    width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: fadeSlideIn .2s ease;
  }

  /* ── Alert ── */
  .alert { border-radius: 12px; padding: 14px 18px; font-size: 13px; display: flex; gap: 10px; align-items: flex-start; }
  .alert-success { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
  .alert-error { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
</style>
@endpush

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
  <div class="alert alert-success mb-5">
    <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
    <span>{{ session('success') }}</span>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-error mb-5">
    <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
    <span>{{ session('error') }}</span>
  </div>
@endif

{{-- Info Card --}}
<div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6">
  <div class="flex items-start gap-3">
    <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
      <i class="fas fa-info text-blue-600 text-sm"></i>
    </div>
    <div>
      <h4 class="font-bold text-blue-900 text-sm mb-1">Cara Mengatur Hak Akses</h4>
      <p class="text-sm text-blue-700 leading-relaxed">
        Pilih jabatan, centang hak akses yang diinginkan untuk setiap menu, lalu klik <strong>Simpan Perubahan</strong>.
        Jabatan yang masih memiliki pegawai <strong>tidak dapat dihapus</strong>.
      </p>
    </div>
  </div>
</div>

{{-- Header Tab + Tombol Tambah --}}
<div class="flex items-center justify-between gap-4 mb-4">
  <div class="jabatan-tabs" id="jabatanTabs">
    @foreach($jabatans as $i => $jabatan)
      <button
        type="button"
        class="jabatan-tab-btn {{ $i === 0 ? 'active' : '' }}"
        onclick="switchTab({{ $jabatan->id }})"
        id="tab-btn-{{ $jabatan->id }}"
      >
        <i class="fas fa-user-tie text-sm"></i>
        {{ $jabatan->nama_jabatan }}
        <span class="badge">{{ $jabatan->pegawais_count }}</span>
        {{-- Tombol hapus (hanya jika 0 pegawai) --}}
        @if($jabatan->pegawais_count === 0)
          <span
            class="tab-delete-btn"
            onclick="event.stopPropagation(); confirmDelete({{ $jabatan->id }}, '{{ addslashes($jabatan->nama_jabatan) }}')"
            title="Hapus jabatan"
          ><i class="fas fa-times"></i></span>
        @endif
      </button>
    @endforeach
  </div>

  {{-- Tombol Tambah Jabatan --}}
  <button type="button" onclick="document.getElementById('modalTambah').classList.add('open')"
    class="flex-shrink-0 flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm">
    <i class="fas fa-plus"></i> Tambah Jabatan
  </button>
</div>

{{-- Panel Hak Akses per Jabatan --}}
@foreach($jabatans as $i => $jabatan)
  @php
    $formId      = 'form-jabatan-' . $jabatan->id;
    $jabatanAkses = $hakAkses[$jabatan->id] ?? collect();
  @endphp

  <div class="akses-panel {{ $i === 0 ? 'active' : '' }}" id="panel-{{ $jabatan->id }}">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

      {{-- Header --}}
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
        <div>
          <h3 class="font-bold text-gray-800 text-sm">
            <i class="fas fa-user-tie text-blue-600 mr-2"></i>{{ $jabatan->nama_jabatan }}
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $jabatan->pegawais_count }} pegawai terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
          @if($jabatan->pegawais_count === 0)
            <button type="button"
              onclick="confirmDelete({{ $jabatan->id }}, '{{ addslashes($jabatan->nama_jabatan) }}')"
              class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold transition border border-red-200">
              <i class="fas fa-trash-alt"></i> Hapus Jabatan
            </button>
          @else
            <span class="text-xs text-gray-400 italic">
              <i class="fas fa-lock mr-1"></i>Tidak bisa dihapus (ada pegawai)
            </span>
          @endif
          <button type="submit" form="{{ $formId }}"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold transition shadow-sm">
            <i class="fas fa-save"></i> Simpan Perubahan
          </button>
        </div>
      </div>

      {{-- Tabel --}}
      <div class="overflow-x-auto">
        <form id="{{ $formId }}" method="POST" action="{{ route('admin.jabatan.akses', $jabatan->id) }}">
          @csrf
          @method('PUT')
          <table class="access-table w-full text-sm">
            <thead>
              <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Menu</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Lihat</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Tambah</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Edit</th>
                <th class="text-center px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Hapus</th>
              </tr>
            </thead>
            <tbody>

              {{-- Select All Row --}}
              <tr class="select-all-row">
                <td class="px-5 py-2.5 text-xs text-gray-500">Pilih / Hapus Semua</td>
                <td class="px-4 py-2.5 text-center">
                  <label class="cb-wrap justify-center">
                    <input type="checkbox" class="sa-lihat" data-jabatan="{{ $jabatan->id }}"
                      onchange="selectAll(this,'lihat',{{ $jabatan->id }})">
                  </label>
                </td>
                <td class="px-4 py-2.5 text-center">
                  <label class="cb-wrap justify-center">
                    <input type="checkbox" class="sa-tambah" data-jabatan="{{ $jabatan->id }}"
                      onchange="selectAll(this,'tambah',{{ $jabatan->id }})">
                  </label>
                </td>
                <td class="px-4 py-2.5 text-center">
                  <label class="cb-wrap justify-center">
                    <input type="checkbox" class="sa-edit" data-jabatan="{{ $jabatan->id }}"
                      onchange="selectAll(this,'edit',{{ $jabatan->id }})">
                  </label>
                </td>
                <td class="px-4 py-2.5 text-center">
                  <label class="cb-wrap justify-center">
                    <input type="checkbox" class="sa-hapus" data-jabatan="{{ $jabatan->id }}"
                      onchange="selectAll(this,'hapus',{{ $jabatan->id }})">
                  </label>
                </td>
              </tr>

              {{-- Rows per Menu --}}
              @foreach($menus as $menu)
                @php
                  $akses  = $jabatanAkses[$menu->id] ?? null;
                  $lihat  = $akses?->bisa_lihat  ?? false;
                  $tambah = $akses?->bisa_tambah ?? false;
                  $edit   = $akses?->bisa_edit   ?? false;
                  $hapus  = $akses?->bisa_hapus  ?? false;
                  $prefix = "akses[{$menu->id}]";
                @endphp
                <tr class="border-b border-gray-50 last:border-b-0">
                  <td class="px-5 py-3 text-gray-700 font-medium">{{ $menu->nama_menu }}</td>

                  <td class="px-4 py-3 text-center">
                    <label class="cb-wrap justify-center">
                      <input type="checkbox" name="{{ $prefix }}[lihat]" value="1"
                        {{ $lihat ? 'checked' : '' }} class="cb-lihat"
                        data-jabatan="{{ $jabatan->id }}" data-menu="{{ $menu->id }}"
                        onchange="toggleLevels(this)">
                      <span class="cb-label">Lihat</span>
                    </label>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <label class="cb-wrap justify-center">
                      <input type="checkbox" name="{{ $prefix }}[tambah]" value="1"
                        {{ $tambah ? 'checked' : '' }} {{ $lihat ? '' : 'disabled' }}
                        class="cb-level cb-tambah"
                        data-jabatan="{{ $jabatan->id }}" data-menu="{{ $menu->id }}">
                      <span class="cb-label">Tambah</span>
                    </label>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <label class="cb-wrap justify-center">
                      <input type="checkbox" name="{{ $prefix }}[edit]" value="1"
                        {{ $edit ? 'checked' : '' }} {{ $lihat ? '' : 'disabled' }}
                        class="cb-level cb-edit"
                        data-jabatan="{{ $jabatan->id }}" data-menu="{{ $menu->id }}">
                      <span class="cb-label">Edit</span>
                    </label>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <label class="cb-wrap justify-center">
                      <input type="checkbox" name="{{ $prefix }}[hapus]" value="1"
                        {{ $hapus ? 'checked' : '' }} {{ $lihat ? '' : 'disabled' }}
                        class="cb-level cb-hapus"
                        data-jabatan="{{ $jabatan->id }}" data-menu="{{ $menu->id }}">
                      <span class="cb-label">Hapus</span>
                    </label>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </form>
      </div>

      {{-- Footer Simpan --}}
      <div class="flex justify-end px-5 py-4 border-t border-gray-100 bg-gray-50">
        <button type="submit" form="{{ $formId }}"
          class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold transition shadow-sm">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
      </div>
    </div>
  </div>
@endforeach

{{-- Pesan kosong jika belum ada jabatan --}}
@if($jabatans->isEmpty())
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
    <i class="fas fa-user-tie text-4xl mb-3 opacity-30"></i>
    <p class="font-semibold text-sm">Belum ada jabatan. Tambahkan jabatan pertama.</p>
  </div>
@endif

{{-- ══════════════════════════════════════════════════════
     MODAL: Tambah Jabatan
═══════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalTambah">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
        <i class="fas fa-plus-circle text-emerald-600"></i> Tambah Jabatan Baru
      </h3>
      <button type="button" onclick="document.getElementById('modalTambah').classList.remove('open')"
        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition text-lg">
        &times;
      </button>
    </div>

    <form method="POST" action="{{ route('admin.jabatan.store') }}">
      @csrf
      <div class="mb-5">
        <label class="block text-xs font-bold text-gray-600 mb-1.5" for="nama_jabatan">Nama Jabatan</label>
        <input
          type="text"
          id="nama_jabatan"
          name="nama_jabatan"
          value="{{ old('nama_jabatan') }}"
          class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
          placeholder="contoh: Kepala Bagian, Staf Administrasi..."
          autofocus
        >
        @error('nama_jabatan')
          <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex justify-end gap-2">
        <button type="button"
          onclick="document.getElementById('modalTambah').classList.remove('open')"
          class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
          Batal
        </button>
        <button type="submit"
          class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold transition shadow-sm">
          <i class="fas fa-save"></i> Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: Konfirmasi Hapus
═══════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalHapus">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-bold text-red-700 text-base flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-red-500"></i> Hapus Jabatan
      </h3>
      <button type="button" onclick="document.getElementById('modalHapus').classList.remove('open')"
        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition text-lg">
        &times;
      </button>
    </div>

    <p class="text-sm text-gray-600 mb-1">Yakin ingin menghapus jabatan:</p>
    <p class="font-bold text-gray-800 text-sm mb-5 bg-red-50 border border-red-100 rounded-xl px-4 py-2" id="hapusNamaJabatan"></p>
    <p class="text-xs text-gray-500 mb-6">Semua hak akses jabatan ini juga akan ikut dihapus. Aksi ini tidak dapat dibatalkan.</p>

    <form method="POST" id="formHapusJabatan">
      @csrf
      @method('DELETE')
      <div class="flex justify-end gap-2">
        <button type="button"
          onclick="document.getElementById('modalHapus').classList.remove('open')"
          class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
          Batal
        </button>
        <button type="submit"
          class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-sm font-bold transition shadow-sm">
          <i class="fas fa-trash-alt"></i> Ya, Hapus
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  /* ── Switch Tab ── */
  function switchTab(jabatanId) {
    document.querySelectorAll('.akses-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.jabatan-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-'   + jabatanId).classList.add('active');
    document.getElementById('tab-btn-' + jabatanId).classList.add('active');
  }

  /* ── Toggle level (Tambah/Edit/Hapus) saat Lihat di-toggle ── */
  function toggleLevels(lihatCb) {
    const row    = lihatCb.closest('tr');
    const levels = row.querySelectorAll('.cb-level');
    levels.forEach(cb => {
      cb.disabled = !lihatCb.checked;
      if (!lihatCb.checked) cb.checked = false;
    });
    syncSelectAll(lihatCb.dataset.jabatan);
  }

  /* ── Select All per kolom ── */
  function selectAll(masterCb, type, jabatanId) {
    const panel   = document.getElementById('panel-' + jabatanId);
    const targets = panel.querySelectorAll('.cb-' + type);
    targets.forEach(cb => { if (!cb.disabled) cb.checked = masterCb.checked; });

    if (type === 'lihat') {
      targets.forEach(cb => {
        const row = cb.closest('tr');
        if (!row) return;
        row.querySelectorAll('.cb-level').forEach(l => {
          l.disabled = !masterCb.checked;
          if (!masterCb.checked) l.checked = false;
        });
      });
    }
  }

  /* ── Sinkronkan header select-all ── */
  function syncSelectAll(jabatanId) {
    const panel = document.getElementById('panel-' + jabatanId);
    ['lihat','tambah','edit','hapus'].forEach(type => {
      const cbs   = panel.querySelectorAll('.cb-' + type + ':not(.sa-' + type + ')');
      const saBox = panel.querySelector('.sa-' + type);
      if (!saBox || !cbs.length) return;
      saBox.checked = [...cbs].every(cb => cb.checked);
    });
  }

  /* ── Konfirmasi Hapus ── */
  function confirmDelete(jabatanId, namaJabatan) {
    document.getElementById('hapusNamaJabatan').textContent = namaJabatan;
    document.getElementById('formHapusJabatan').action = '/admin/jabatan/' + jabatanId;
    document.getElementById('modalHapus').classList.add('open');
  }

  /* ── Tutup modal jika klik overlay ── */
  ['modalTambah','modalHapus'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('click', function(e) {
      if (e.target === el) el.classList.remove('open');
    });
  });

  /* ── Buka modal tambah otomatis jika ada validasi error ── */
  @if($errors->has('nama_jabatan'))
    document.getElementById('modalTambah').classList.add('open');
  @endif
</script>
@endpush
