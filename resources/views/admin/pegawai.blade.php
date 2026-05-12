@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('page-title', 'Data Pegawai')
@section('page-subtitle', 'Kelola data pegawai dan akun login mereka')

@push('styles')
<style>
  /* ── STATS ── */
  .stat-card { transition: all .25s ease; }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(30,58,138,.13); }

  /* ── TABLE ── */
  .table-row { transition: background .12s; }
  .table-row:hover { background: #f0f7ff; }

  /* ── ROLE BADGES ── */
  .role-admin    { background:#fef3c7; color:#92400e; }
  .role-dokter   { background:#eff6ff; color:#1d4ed8; }
  .role-perawat  { background:#ecfdf5; color:#065f46; }
  .role-apoteker { background:#f5f3ff; color:#5b21b6; }

  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:20px; width:100%; max-width:640px;
    height:90vh; display:flex; flex-direction:column;
    box-shadow:0 32px 80px rgba(15,23,42,.22);
    animation:modalIn .22s cubic-bezier(.4,0,.2,1); margin:16px; overflow:hidden; }
  @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:none} }
  .modal-head { padding:22px 28px 18px; border-bottom:1px solid #e5e7eb;
    display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
  .modal-body { padding:22px 28px; overflow-y:auto; flex:1 1 0; min-height:0; scroll-behavior:smooth; }
  .modal-body::-webkit-scrollbar { width:5px; }
  .modal-body::-webkit-scrollbar-track { background:#f8fafc; border-radius:99px; }
  .modal-body::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:99px; }
  .modal-foot { padding:16px 28px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0; }

  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .span2 { grid-column:1/-1; }
  .form-group label { display:block; font-size:12px; font-weight:700;
    color:#6b7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
  .form-input, .form-select {
    width:100%; padding:9px 13px; border:1.5px solid #e5e7eb;
    border-radius:10px; font-size:13.5px; color:#1e293b; background:#fff;
    outline:none; transition:all .16s; box-sizing:border-box; font-family:inherit; }
  .form-input:focus, .form-select:focus {
    border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
  .form-input::placeholder { color:#9ca3af; }

  /* ── CONFIRM MODAL ── */
  .confirm-icon { width:52px; height:52px; border-radius:50%;
    display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }

  /* ── DIVIDER ── */
  .section-divider { border:none; border-top:1.5px dashed #e5e7eb; margin:10px 0 4px; }

  @media (max-width:640px) {
    .form-grid { grid-template-columns:1fr; }
    .span2 { grid-column:auto; }
  }
</style>
@endpush

@section('content')

{{-- ─── STAT CARDS ─────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-7">

  {{-- Total --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-users text-blue-600 text-base ml-3"></i>
      </div>
      <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">Terdaftar</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">{{ $pegawais->total() }}</div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Pegawai</div>
  </div>

  {{-- Dokter --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-user-doctor text-blue-600 text-base ml-3"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ \App\Models\Pegawai::whereHas('user', fn($q) => $q->where('role','dokter'))->count() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Dokter</div>
  </div>

  {{-- Perawat --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-user-nurse text-emerald-600 text-base ml-3"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ \App\Models\Pegawai::whereHas('user', fn($q) => $q->where('role','perawat'))->count() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Perawat</div>
  </div>

  {{-- Apoteker --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
        <i class="fas fa-pills text-purple-600 text-base ml-3"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ \App\Models\Pegawai::whereHas('user', fn($q) => $q->where('role','apoteker'))->count() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Apoteker</div>
  </div>

</div>

{{-- ─── TOOLBAR ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
  <button id="btnTambah"
    class="flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-md">
    <i class="fas fa-plus text-xs"></i> Tambah Pegawai
  </button>

  <form method="GET" action="{{ route('admin.pegawai') }}" class="flex items-center gap-2">
    <div class="relative">
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
      <input type="text" name="search" value="{{ $search ?? '' }}"
        placeholder="Cari nama, NIK, email..."
        class="pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 w-64 transition">
    </div>
    <button type="submit"
      class="bg-blue-900 hover:bg-blue-800 text-white text-sm px-4 py-2.5 rounded-xl transition font-semibold">
      Cari
    </button>
    @if($search)
      <a href="{{ route('admin.pegawai') }}"
        class="text-sm text-gray-500 hover:text-red-500 px-3 py-2.5 rounded-xl border border-gray-200 hover:border-red-200 transition">
        <i class="fas fa-times"></i>
      </a>
    @endif
  </form>
</div>

{{-- ─── TABLE CARD ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[900px]">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">NIK</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Spesialisasi</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No. SIP</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No HP</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($pegawais as $i => $p)
          <tr class="table-row">
            {{-- No --}}
            <td class="px-5 py-4 text-gray-400 font-semibold text-xs">
              {{ $pegawais->firstItem() + $i }}
            </td>
            {{-- NIK --}}
            <td class="px-5 py-4 text-gray-500 text-xs font-mono">{{ $p->nik ?: '—' }}</td>
            {{-- Nama + Email --}}
            <td class="px-5 py-4">
              <div class="font-semibold text-gray-800">{{ $p->nama }}</div>
              <div class="text-xs text-gray-400 font-mono">{{ $p->user->email ?? '—' }}</div>
            </td>
            {{-- Role --}}
            <td class="px-5 py-4">
              @php $role = $p->user->role ?? 'unknown'; @endphp
              <span class="role-{{ $role }} text-xs font-bold px-3 py-1 rounded-full capitalize">
                {{ ucfirst($role) }}
              </span>
            </td>
            {{-- Spesialisasi --}}
            <td class="px-5 py-4 text-gray-600 text-xs">{{ $p->spesialisasi ?: '—' }}</td>
            {{-- No SIP --}}
            <td class="px-5 py-4 text-gray-500 text-xs font-mono">{{ $p->no_sip ?: '—' }}</td>
            {{-- No HP --}}
            <td class="px-5 py-4 text-gray-500 text-xs">{{ $p->no_hp ?: '—' }}</td>
            {{-- Aksi --}}
            <td class="px-5 py-4">
              <div class="flex items-center gap-2">
                <button
                  onclick="openInfo({{ $p->id }})"
                  class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                  title="Detail">
                  <i class="fas fa-info text-xs"></i>
                </button>
                <button
                  onclick="openEdit({{ $p->id }})"
                  class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                  title="Edit">
                  <i class="fas fa-pen text-xs"></i>
                </button>
                <button
                  onclick="openDel({{ $p->id }}, '{{ addslashes($p->nama) }}')"
                  class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition"
                  title="Hapus">
                  <i class="fas fa-trash text-xs"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-16 text-gray-400">
              <i class="fas fa-user-slash text-4xl mb-4 block opacity-30"></i>
              <p class="font-semibold">Tidak ada data pegawai{{ $search ? ' untuk pencarian "' . $search . '"' : '' }}</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($pegawais->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
      <p class="text-xs text-gray-500">
        Menampilkan <strong>{{ $pegawais->firstItem() }}–{{ $pegawais->lastItem() }}</strong>
        dari <strong>{{ $pegawais->total() }}</strong> data
      </p>
      <div class="flex items-center gap-1">
        @if($pegawais->onFirstPage())
          <span class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 border border-gray-100 font-semibold text-xs cursor-not-allowed">
            <i class="fas fa-chevron-left text-xs"></i>
          </span>
        @else
          <a href="{{ $pegawais->previousPageUrl() }}"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">
            <i class="fas fa-chevron-left text-xs"></i>
          </a>
        @endif

        @foreach($pegawais->getUrlRange(max(1,$pegawais->currentPage()-2), min($pegawais->lastPage(),$pegawais->currentPage()+2)) as $page => $url)
          @if($page == $pegawais->currentPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-900 text-white font-bold text-xs">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">{{ $page }}</a>
          @endif
        @endforeach

        @if($pegawais->hasMorePages())
          <a href="{{ $pegawais->nextPageUrl() }}"
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
      <p class="text-xs text-gray-400">Total <strong>{{ $pegawais->total() }}</strong> data</p>
    </div>
  @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL TAMBAH / EDIT
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <div class="modal-head">
      <h2 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Pegawai</h2>
      <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>

    <form id="pegawaiForm" method="POST" action="{{ route('admin.pegawai.store') }}"
      style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="_pegawai_id" id="formPegawaiId" value="">

      <div class="modal-body">
        <div class="form-grid">

          {{-- Nama --}}
          <div class="form-group span2">
            <label>Nama Lengkap <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="nama" id="fNama" class="form-input" placeholder="Nama lengkap pegawai" required>
          </div>

          {{-- NIK --}}
          <div class="form-group">
            <label>NIK <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="nik" id="fNik" class="form-input" placeholder="16 digit NIK" maxlength="20" required>
          </div>

          {{-- Role --}}
          <div class="form-group">
            <label>Role <span class="text-red-500 normal-case font-normal">*</span></label>
            <select name="role" id="fRole" class="form-select" required>
              <option value="">— Pilih Role —</option>
              <option value="admin">Admin</option>
              <option value="dokter">Dokter</option>
              <option value="perawat">Perawat</option>
              <option value="apoteker">Apoteker</option>
            </select>
          </div>

          {{-- Spesialisasi --}}
          <div class="form-group" id="groupSpesialisasi" style="display:none;">
            <label>Spesialisasi <span class="text-xs text-gray-400 normal-case font-normal">(opsional, khusus dokter)</span></label>
            <input type="text" name="spesialisasi" id="fSpesialisasi" class="form-input" placeholder="Contoh: Penyakit Dalam">
          </div>

          {{-- No SIP --}}
          <div class="form-group" id="groupSip" style="display:none;">
            <label>No. SIP <span class="text-red-500 normal-case font-normal" id="sipReq" style="display:none;">*</span> <span class="text-xs text-gray-400 normal-case font-normal" id="sipHint"></span></label>
            <input type="text" name="no_sip" id="fSip" class="form-input" placeholder="Nomor SIP" maxlength="60">
          </div>

          {{-- No HP --}}
          <div class="form-group">
            <label>No HP <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="no_hp" id="fHp" class="form-input" placeholder="08xxxxxxxxxx" maxlength="15" required>
          </div>

          {{-- Alamat --}}
          <div class="form-group span2">
            <label>Alamat</label>
            <input type="text" name="alamat" id="fAlamat" class="form-input" placeholder="Alamat lengkap">
          </div>

          <hr class="section-divider span2">

          {{-- Email --}}
          <div class="form-group span2">
            <label>Email Akun <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="email" name="email" id="fEmail" class="form-input" placeholder="email@example.com" required>
          </div>

          {{-- Password --}}
          <div class="form-group span2">
            <label>Password <span class="text-red-500 normal-case font-normal" id="pwReq">*</span></label>
            <input type="password" name="password" id="fPassword" class="form-input" placeholder="Min. 6 karakter">
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
          <i class="fas fa-save mr-1.5"></i> Simpan
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
    <h3 class="text-base font-bold text-gray-800 mb-2">Hapus Pegawai?</h3>
    <p class="text-sm text-gray-500 mb-6" id="delMsg">Data pegawai akan dihapus.</p>
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

{{-- ═══════════════════════════════════════════════════════════
     MODAL INFO / DETAIL
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="infoOverlay">
  <div class="modal-box">
    <div class="modal-head">
      <h2 class="text-lg font-bold text-gray-800">Detail Pegawai</h2>
      <button onclick="closeInfo()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <div class="modal-body bg-gray-50/50">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-6">

        <div class="col-span-1 md:col-span-2 pb-5 mb-2 border-b border-gray-200">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-3xl font-bold shadow-sm">
              <i class="fas fa-user-doctor"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold text-gray-800" id="infoNama">Nama Pegawai</h3>
              <p class="text-sm text-gray-500 font-mono" id="infoEmail">email@example.com</p>
              <span class="text-xs font-bold px-3 py-1 rounded-full mt-1 inline-block" id="infoRoleBadge">Role</span>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">NIK</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoNik">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">No HP</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoHp">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Spesialisasi</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoSpesialisasi">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">No. SIP</label>
          <div class="text-[15px] font-semibold text-gray-800 font-mono" id="infoSip">-</div>
        </div>
        <div class="col-span-1 md:col-span-2">
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alamat</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoAlamat">-</div>
        </div>

      </div>
    </div>
    <div class="modal-foot bg-gray-50/50">
      <button type="button" onclick="closeInfo()" class="px-6 py-2.5 rounded-xl bg-blue-900 border text-white text-sm font-bold shadow-md hover:bg-blue-800 transition">Tutup Detail</button>
    </div>
  </div>
</div>

{{-- Data JSON pegawai untuk form edit --}}
<script id="pegawaiData" type="application/json">
  {!! json_encode($pegawais->map(fn($p) => [
    'id'           => $p->id,
    'nik'          => $p->nik ?? '',
    'nama'         => $p->nama,
    'email'        => $p->user->email ?? '',
    'role'         => $p->user->role ?? '',
    'spesialisasi' => $p->spesialisasi ?? '',
    'no_sip'       => $p->no_sip ?? '',
    'no_hp'        => $p->no_hp ?? '',
    'alamat'       => $p->alamat ?? '',
  ])->keyBy('id')) !!}
</script>

@endsection

@push('scripts')
<script>
  var pegawaiMap = JSON.parse(document.getElementById('pegawaiData').textContent);
  var BASE_URL   = '{{ url("/admin/pegawai") }}';
  var editingId  = null;

  /* ── ROLE LOGIC ── */
  document.getElementById('fRole').addEventListener('change', function() {
    var role = this.value;
    var sipGroup = document.getElementById('groupSip');
    var sipReq = document.getElementById('sipReq');
    var sipHint = document.getElementById('sipHint');
    var spesialisasiGroup = document.getElementById('groupSpesialisasi');
    var fSip = document.getElementById('fSip');
    var fSpesialisasi = document.getElementById('fSpesialisasi');

    if (role === 'dokter') {
      sipGroup.style.display = 'block';
      spesialisasiGroup.style.display = 'block';
      fSip.required = true;
      sipReq.style.display = 'inline';
      sipHint.textContent = '(khusus dokter)';
      fSip.placeholder = 'Nomor SIP Dokter';
    } else if (role === 'perawat') {
      sipGroup.style.display = 'block';
      spesialisasiGroup.style.display = 'none';
      fSpesialisasi.value = '';
      fSip.required = true;
      sipReq.style.display = 'inline';
      sipHint.textContent = '(khusus perawat)';
      fSip.placeholder = 'Nomor SIP Perawat';
    } else if (role === 'apoteker') {
      sipGroup.style.display = 'block';
      spesialisasiGroup.style.display = 'none';
      fSpesialisasi.value = '';
      fSip.required = true;
      sipReq.style.display = 'inline';
      sipHint.textContent = '(khusus apoteker)';
      fSip.placeholder = 'Nomor SIP Apoteker';
    } else { // admin or empty
      sipGroup.style.display = 'none';
      spesialisasiGroup.style.display = 'none';
      fSip.value = '';
      fSpesialisasi.value = '';
      fSip.required = false;
      sipReq.style.display = 'none';
      sipHint.textContent = '';
      fSip.placeholder = 'Nomor SIP';
    }
  });

  /* ── MODAL TAMBAH/EDIT ── */
  function openAdd() {
    editingId = null;
    document.getElementById('modalTitle').textContent      = 'Tambah Pegawai';
    document.getElementById('pegawaiForm').action          = '{{ route("admin.pegawai.store") }}';
    document.getElementById('formMethod').value            = 'POST';
    document.getElementById('formPegawaiId').value         = '';
    document.getElementById('pwReq').textContent           = '*';
    document.getElementById('fPassword').placeholder       = 'Min. 6 karakter';
    document.getElementById('fPassword').required          = true;
    document.getElementById('fEmail').readOnly             = false;
    clearForm();
    document.getElementById('fRole').dispatchEvent(new Event('change'));
    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function openEdit(id) {
    var p = pegawaiMap[id];
    if (!p) return;
    editingId = id;
    document.getElementById('modalTitle').textContent      = 'Edit Pegawai';
    document.getElementById('pegawaiForm').action          = BASE_URL + '/' + id;
    document.getElementById('formMethod').value            = 'PUT';
    document.getElementById('formPegawaiId').value         = id;
    document.getElementById('pwReq').textContent           = '(opsional)';
    document.getElementById('fPassword').placeholder       = 'Kosongkan jika tidak diubah';
    document.getElementById('fPassword').required          = false;

    document.getElementById('fNama').value         = p.nama;
    document.getElementById('fNik').value          = p.nik;
    document.getElementById('fRole').value         = p.role;
    document.getElementById('fSpesialisasi').value = p.spesialisasi;
    document.getElementById('fSip').value          = p.no_sip;
    document.getElementById('fHp').value           = p.no_hp;
    document.getElementById('fAlamat').value       = p.alamat;
    document.getElementById('fEmail').value        = p.email;
    document.getElementById('fPassword').value     = '';

    document.getElementById('fRole').dispatchEvent(new Event('change'));

    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  function clearForm() {
    ['fNama','fNik','fSpesialisasi','fSip','fHp','fAlamat','fEmail','fPassword'].forEach(function(id) {
      document.getElementById(id).value = '';
    });
    document.getElementById('fRole').value = '';
  }

  /* ── MODAL HAPUS ── */
  function openDel(id, nama) {
    document.getElementById('delMsg').textContent = 'Pegawai "' + nama + '" akan dihapus.';
    document.getElementById('delForm').action     = BASE_URL + '/' + id;
    document.getElementById('delOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDel() {
    document.getElementById('delOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  /* ── MODAL INFO ── */
  var roleColors = {
    admin:    'background:#fef3c7;color:#92400e',
    dokter:   'background:#eff6ff;color:#1d4ed8',
    perawat:  'background:#ecfdf5;color:#065f46',
    apoteker: 'background:#f5f3ff;color:#5b21b6',
  };

  function openInfo(id) {
    var p = pegawaiMap[id];
    if (!p) return;

    document.getElementById('infoNama').textContent        = p.nama || '-';
    document.getElementById('infoEmail').textContent       = p.email || '-';
    document.getElementById('infoNik').textContent         = p.nik || '-';
    document.getElementById('infoHp').textContent          = p.no_hp || '-';
    document.getElementById('infoSpesialisasi').textContent = p.spesialisasi || '-';
    document.getElementById('infoSip').textContent         = p.no_sip || '-';
    document.getElementById('infoAlamat').textContent      = p.alamat || '-';

    var badge = document.getElementById('infoRoleBadge');
    badge.textContent = p.role ? p.role.charAt(0).toUpperCase() + p.role.slice(1) : '-';
    badge.style.cssText = roleColors[p.role] || 'background:#f3f4f6;color:#374151';

    document.getElementById('infoOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeInfo() {
    document.getElementById('infoOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  /* ── EVENTS ── */
  document.getElementById('btnTambah').addEventListener('click', openAdd);

  ['modalOverlay','delOverlay','infoOverlay'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal(); closeDel(); closeInfo();
      }
    });
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal(); closeDel(); closeInfo(); }
  });
</script>
@endpush