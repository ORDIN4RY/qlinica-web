@extends('layouts.app')

@section('title', 'Data Pasien')
@section('page-title', 'Data Pasien')
@section('page-subtitle', 'Kelola data rekam medis pasien terdaftar')

@push('styles')
<style>
  /* ── STATS ── */
  .stat-card { transition: all .25s ease; }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(30,58,138,.13); }

  /* ── TABLE ── */
  .table-row { transition: background .12s; }
  .table-row:hover { background: #f0f7ff; }

  /* ── BADGES ── */
  .badge-l { background:#eff6ff; color:#2563eb; }
  .badge-p { background:#f5f3ff; color:#7c3aed; }
  .blood-a  { background:#fef2f2; color:#dc2626; }
  .blood-b  { background:#eff6ff; color:#2563eb; }
  .blood-ab { background:#f5f3ff; color:#7c3aed; }
  .blood-o  { background:#ecfdf5; color:#059669; }

  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:20px; width:100%; max-width:680px;
    height:90vh; display:flex; flex-direction:column;
    box-shadow:0 32px 80px rgba(15,23,42,.22);
    animation:modalIn .22s cubic-bezier(.4,0,.2,1); margin:16px; overflow:hidden; }
  @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:none} }
  .modal-head { padding:22px 28px 18px; border-bottom:1px solid #e5e7eb;
    display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
  .modal-body { padding:22px 28px; overflow-y:auto; flex:1 1 0; min-height:0;
    scroll-behavior:smooth; }
  .modal-body::-webkit-scrollbar { width:5px; }
  .modal-body::-webkit-scrollbar-track { background:#f8fafc; border-radius:99px; }
  .modal-body::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:99px; }
  .modal-body::-webkit-scrollbar-thumb:hover { background:#94a3b8; }
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
  .form-input.error, .form-select.error { border-color:#ef4444; }
  .err-text { font-size:11px; color:#ef4444; margin-top:4px; display:none; }
  .err-text.show { display:block; }

  /* ── CONFIRM MODAL ── */
  .confirm-icon { width:52px; height:52px; border-radius:50%;
    display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }

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
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-content-center">
        <i class="fas fa-users text-blue-600 text-base ml-3"></i>
      </div>
      <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">Terdaftar</span>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">{{ $pasiens->total() }}</div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Pasien</div>
  </div>

  {{-- Laki-laki --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-content-center">
        <i class="fas fa-mars text-indigo-600 text-base ml-3"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ \App\Models\Pasien::where('jenis_kelamin','L')->count() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Laki-laki</div>
  </div>

  {{-- Perempuan --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-content-center">
        <i class="fas fa-venus text-purple-600 text-base ml-3"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ \App\Models\Pasien::where('jenis_kelamin','P')->count() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Perempuan</div>
  </div>

  {{-- Baru Bulan Ini --}}
  <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-content-center">
        <i class="fas fa-user-plus text-emerald-600 text-base ml-3"></i>
      </div>
    </div>
    <div class="text-3xl font-extrabold text-gray-800 leading-none mb-1">
      {{ \App\Models\Pasien::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() }}
    </div>
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Baru Bulan Ini</div>
  </div>

</div>

{{-- ─── TOOLBAR ─────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
    <button id="btnTambah"
      class="flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shadow-md">
      <i class="fas fa-plus text-xs"></i> Tambah Pasien
    </button>
  </div>

  <form method="GET" action="{{ route('admin.pasien') }}" class="flex items-center gap-2">
    <div class="relative">
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
      <input type="text" name="search" value="{{ $search ?? '' }}"
        placeholder="Cari nama, NIK, No RM..."
        class="pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 w-64 transition">
    </div>
    <button type="submit"
      class="bg-blue-900 hover:bg-blue-800 text-white text-sm px-4 py-2.5 rounded-xl transition font-semibold">
      Cari
    </button>
    @if($search)
      <a href="{{ route('admin.pasien') }}"
        class="text-sm text-gray-500 hover:text-red-500 px-3 py-2.5 rounded-xl border border-gray-200 hover:border-red-200 transition">
        <i class="fas fa-times"></i>
      </a>
    @endif
  </form>
</div>

{{-- Tampilkan Error Validasi (jika ada) --}}
@if ($errors->any())
  <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-sm mb-5">
    <div class="flex items-center gap-3 font-semibold mb-2">
      <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
      <span>Terjadi Kesalahan Validasi:</span>
    </div>
    <ul class="list-disc list-inside text-sm ml-8 text-red-600">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

{{-- ─── TABLE CARD ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[960px]">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No RM</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">NIK</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pasien</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Lahir / Umur</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">J. Kelamin</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Gol. Darah</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No HP</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Kota</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Riwayat Alergi</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($pasiens as $i => $p)
          @php
            $umur = $p->tgl_lahir ? \Carbon\Carbon::parse($p->tgl_lahir)->age : null;
          @endphp
          <tr class="table-row">
            {{-- No --}}
            <td class="px-5 py-4 text-gray-400 font-semibold text-xs">
              {{ $pasiens->firstItem() + $i }}
            </td>
            {{-- No RM --}}
            <td class="px-5 py-4">
              <span class="text-blue-700 font-bold font-mono text-xs tracking-wide">{{ $p->no_rm }}</span>
            </td>
            {{-- NIK --}}
            <td class="px-5 py-4 text-gray-500 text-xs font-mono">{{ $p->nik ?: '—' }}</td>
            {{-- Nama --}}
            <td class="px-5 py-4">
              <div class="font-semibold text-gray-800">{{ $p->nama }}</div>
              @if($p->nama_kk)
                <div class="text-xs text-gray-400">KK: {{ $p->nama_kk }}</div>
              @endif
            </td>
            {{-- Tgl Lahir / Umur --}}
            <td class="px-5 py-4">
              <div class="text-gray-700 text-xs">{{ $p->tgl_lahir ? \Carbon\Carbon::parse($p->tgl_lahir)->isoFormat('D MMM YYYY') : '—' }}</div>
              @if($umur !== null)
                <div class="text-xs text-gray-400">{{ $umur }} tahun</div>
              @endif
            </td>
            {{-- Jenis Kelamin --}}
            <td class="px-5 py-4">
              @if($p->jenis_kelamin === 'L')
                <span class="badge-l text-xs font-bold px-3 py-1 rounded-full">Laki-laki</span>
              @else
                <span class="badge-p text-xs font-bold px-3 py-1 rounded-full">Perempuan</span>
              @endif
            </td>
            {{-- Golongan Darah --}}
            <td class="px-5 py-4">
              @if($p->golongan_darah)
                @php $gd = strtolower($p->golongan_darah); @endphp
                <span class="blood-{{ $gd }} text-xs font-bold w-8 h-8 rounded-lg inline-flex items-center justify-center">
                  {{ $p->golongan_darah }}
                </span>
              @else
                <span class="text-gray-300 text-xs">—</span>
              @endif
            </td>
            {{-- No HP --}}
            <td class="px-5 py-4 text-gray-500 text-xs">{{ $p->user->phone ?? '—' }}</td>
            {{-- Kota --}}
            <td class="px-5 py-4 text-gray-600 text-xs">{{ $p->kota ?: ($p->desa ?: '—') }}</td>
            {{-- Riwayat Alergi --}}
            <td class="px-5 py-4 text-gray-600 text-xs">
              {{ $p->riwayat_alergi ?: '—' }}
            </td>
            {{-- Aksi --}}
            <td class="px-5 py-4">
              <div class="flex items-center gap-2">
                <button
                  onclick="openInfo({{ $p->id }})"
                  class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                  title="Info">
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
            <td colspan="11" class="text-center py-16 text-gray-400">
              <i class="fas fa-user-slash text-4xl mb-4 block opacity-30"></i>
              <p class="font-semibold">Tidak ada data pasien{{ $search ? ' untuk pencarian "' . $search . '"' : '' }}</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($pasiens->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
      <p class="text-xs text-gray-500">
        Menampilkan <strong>{{ $pasiens->firstItem() }}–{{ $pasiens->lastItem() }}</strong>
        dari <strong>{{ $pasiens->total() }}</strong> data
      </p>
      <div class="flex items-center gap-1">
        {{-- Prev --}}
        @if($pasiens->onFirstPage())
          <span class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-300 border border-gray-100 font-semibold text-xs cursor-not-allowed">
            <i class="fas fa-chevron-left text-xs"></i>
          </span>
        @else
          <a href="{{ $pasiens->previousPageUrl() }}"
            class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-600 border border-gray-200 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">
            <i class="fas fa-chevron-left text-xs"></i>
          </a>
        @endif

        {{-- Pages --}}
        @foreach($pasiens->getUrlRange(max(1, $pasiens->currentPage()-2), min($pasiens->lastPage(), $pasiens->currentPage()+2)) as $page => $url)
          @if($page == $pasiens->currentPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-900 text-white font-bold text-xs">{{ $page }}</span>
          @else
            <a href="{{ $url }}"
              class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700 font-semibold text-xs transition">
              {{ $page }}
            </a>
          @endif
        @endforeach

        {{-- Next --}}
        @if($pasiens->hasMorePages())
          <a href="{{ $pasiens->nextPageUrl() }}"
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
      <p class="text-xs text-gray-400">Total <strong>{{ $pasiens->total() }}</strong> data</p>
    </div>
  @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL TAMBAH / EDIT
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box" >
    <div class="modal-head">
      <h2 class="text-lg font-bold text-gray-800" id="modalTitle">Tambah Pasien</h2>
      <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>

    <form id="pasienForm" method="POST" action="{{ route('admin.pasien.store') }}" style="overflow-y: scroll;">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="_pasien_id" id="formPasienId" value="">

      <div class="modal-body">
        <div class="form-grid">

          {{-- No RM --}}
          <div class="form-group">
            <label>No Rekam Medik <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="no_rm" id="fRm" class="form-input"
              placeholder="Contoh: RM-20240101-0001" maxlength="15">
            <p class="err-text" id="errRm"></p>
          </div>

          {{-- NIK --}}
          <div class="form-group">
            <label>NIK</label>
            <input type="text" name="nik" id="fNik" class="form-input"
              placeholder="16 digit NIK" maxlength="16">
            <p class="err-text" id="errNik"></p>
          </div>

          {{-- Nama --}}
          <div class="form-group span2">
            <label>Nama Lengkap <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="text" name="nama" id="fNama" class="form-input"
              placeholder="Nama lengkap pasien">
            <p class="err-text" id="errNama"></p>
          </div>

          {{-- Nama KK --}}
          <div class="form-group span2">
            <label>Nama Kepala Keluarga</label>
            <input type="text" name="nama_kk" id="fKk" class="form-input" placeholder="Nama KK">
          </div>

          {{-- Tgl Lahir --}}
          <div class="form-group">
            <label>Tanggal Lahir <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="date" name="tgl_lahir" id="fTgl" class="form-input">
            <p class="err-text" id="errTgl"></p>
          </div>

          {{-- Jenis Kelamin --}}
          <div class="form-group">
            <label>Jenis Kelamin <span class="text-red-500 normal-case font-normal">*</span></label>
            <select name="jenis_kelamin" id="fJenkel" class="form-select">
              <option value="">— Pilih —</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
            <p class="err-text" id="errJenkel"></p>
          </div>

          {{-- Golongan Darah --}}
          <div class="form-group">
            <label>Golongan Darah</label>
            <select name="golongan_darah" id="fDarah" class="form-select">
              <option value="">— Pilih —</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="AB">AB</option>
              <option value="O">O</option>
            </select>
          </div>

          {{-- No HP --}}
          <div class="form-group">
            <label>No HP</label>
            <input type="text" name="no_hp" id="fHp" class="form-input"
              placeholder="08xxxxxxxxxx" maxlength="15">
          </div>

          {{-- Alamat --}}
          <div class="form-group span2">
            <label>Alamat</label>
            <input type="text" name="alamat" id="fAlamat" class="form-input" placeholder="Alamat lengkap">
          </div>

          {{-- Desa --}}
          <div class="form-group">
            <label>Desa / Kelurahan</label>
            <input type="text" name="desa" id="fDesa" class="form-input" placeholder="Nama desa">
          </div>

          {{-- Kota --}}
          <div class="form-group">
            <label>Kota / Kabupaten</label>
            <input type="text" name="kota" id="fKota" class="form-input" placeholder="Nama kota">
          </div>

          {{-- Agama --}}
          <div class="form-group">
            <label>Agama</label>
            <select name="agama_id" id="fAgama" class="form-select">
              <option value="">— Pilih Agama —</option>
              @foreach($agamas as $ag)
                <option value="{{ $ag->id }}">{{ $ag->agama }}</option>
              @endforeach
            </select>
          </div>

          {{-- Pendidikan --}}
          <div class="form-group">
            <label>Pendidikan</label>
            <select name="pendidikan_id" id="fPendidikan" class="form-select">
              <option value="">— Pilih Pendidikan —</option>
              @foreach($pendidikans as $pd)
                <option value="{{ $pd->id }}">{{ $pd->pendidikan }}</option>
              @endforeach
            </select>
          </div>

          {{-- Pekerjaan --}}
          <div class="form-group span2">
            <label>Pekerjaan</label>
            <select name="pekerjaan_id" id="fPekerjaan" class="form-select">
              <option value="">— Pilih Pekerjaan —</option>
              @foreach($pekerjaans as $pk)
                <option value="{{ $pk->id }}">{{ $pk->pekerjaan }}</option>
              @endforeach
            </select>
          </div>

          {{-- Riwayat Alergi --}}
          <div class="form-group span2">
            <label>Riwayat Alergi</label>
            <textarea name="riwayat_alergi" id="fAlergi" class="form-input"
              placeholder="Riwayat alergi obat, makanan, dll (opsional)" rows="2"></textarea>
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
    <h3 class="text-base font-bold text-gray-800 mb-2">Hapus Pasien?</h3>
    <p class="text-sm text-gray-500 mb-6" id="delMsg">Data pasien akan dihapus.</p>
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
</div>{{-- ═══════════════════════════════════════════════════════════
     MODAL INFO
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="infoOverlay">
  <div class="modal-box">
    <div class="modal-head">
      <h2 class="text-lg font-bold text-gray-800">Detail Pasien</h2>
      <button onclick="closeInfo()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <div class="modal-body bg-gray-50/50">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-6">
        
        <div class="col-span-1 md:col-span-2 pb-5 mb-2 border-b border-gray-200">
          <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-3xl font-bold shadow-sm">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h3 class="text-2xl font-bold text-gray-800" id="infoNama">Nama Pasien</h3>
              <p class="text-sm text-gray-500 font-mono font-semibold" id="infoRm">RM-000000</p>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">NIK</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoNik">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Kepala Keluarga</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoKk">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tanggal Lahir</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoTgl">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Jenis Kelamin</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoJenkel">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Golongan Darah</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoDarah">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">No HP</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoHp">-</div>
        </div>
        

        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Agama</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoAgama">-</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Pendidikan</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoPendidikan">-</div>
        </div>
        <div class="col-span-1 md:col-span-2">
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Pekerjaan</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoPekerjaan">-</div>
        </div>
        <div class="col-span-1 md:col-span-2">
          <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Alamat Lengkap</label>
          <div class="text-[15px] font-semibold text-gray-800" id="infoAlamat">-</div>
        </div>
      </div>
    </div>
    <div class="modal-foot bg-gray-50/50">
      <button type="button" onclick="closeInfo()" class="px-6 py-2.5 rounded-xl bg-blue-900 border text-white text-sm font-bold shadow-md hover:bg-blue-800 transition">Tutup Detail</button>
    </div>
  </div>
</div>



{{-- Data JSON pasien untuk form edit --}}
<script id="pasienData" type="application/json">
  {!! json_encode($pasiens->map(fn($p) => [
    'id'             => $p->id,
    'no_rm'          => $p->no_rm,
    'nik'            => $p->nik ?? '',
    'nama'           => $p->nama,
    'nama_kk'        => $p->nama_kk ?? '',
    'tgl_lahir'      => $p->tgl_lahir ? \Carbon\Carbon::parse($p->tgl_lahir)->format('Y-m-d') : '',
    'jenis_kelamin'  => $p->jenis_kelamin,
    'golongan_darah' => $p->golongan_darah ?? '',
    'alamat'         => $p->alamat ?? '',
    'desa'           => $p->desa ?? '',
    'kota'           => $p->kota ?? '',
    'no_hp'          => $p->user->phone ?? '',
    'agama_id'       => $p->agama_id ?? '',
    'pendidikan_id'  => $p->pendidikan_id ?? '',
    'pekerjaan_id'   => $p->pekerjaan_id ?? '',
    'riwayat_alergi' => $p->riwayat_alergi ?? '',
  ])->keyBy('id')) !!}
</script>

@endsection

@push('scripts')
<script>
  var pasienMap    = JSON.parse(document.getElementById('pasienData').textContent);
  var BASE_URL     = '{{ url("/admin/pasien") }}';
  var editingId    = null;

  /* ── MODAL TAMBAH/EDIT ── */
  function openAdd() {
    editingId = null;
    document.body.style.overflow = 'hidden';
    document.getElementById('modalTitle').textContent = 'Tambah Pasien';
    document.getElementById('pasienForm').action      = '{{ route("admin.pasien.store") }}';
    document.getElementById('formMethod').value       = 'POST';
    document.getElementById('formPasienId').value     = '';

    clearForm();
    clearErrors();
    document.getElementById('modalOverlay').classList.add('open');
  }

  function openEdit(id) {
    var p = pasienMap[id];
    if (!p) return;
    editingId = id;
    document.getElementById('modalTitle').textContent = 'Edit Pasien';
    document.getElementById('pasienForm').action      = BASE_URL + '/' + id;
    document.getElementById('formMethod').value       = 'PUT';
    document.getElementById('formPasienId').value     = id;

    clearErrors();

    document.getElementById('fRm').value         = p.no_rm;
    document.getElementById('fNik').value         = p.nik;
    document.getElementById('fNama').value        = p.nama;
    document.getElementById('fKk').value          = p.nama_kk;
    document.getElementById('fTgl').value         = p.tgl_lahir;
    document.getElementById('fJenkel').value      = p.jenis_kelamin;
    document.getElementById('fDarah').value       = p.golongan_darah;
    document.getElementById('fHp').value          = p.no_hp;
    document.getElementById('fAlamat').value      = p.alamat;
    document.getElementById('fDesa').value        = p.desa;
    document.getElementById('fKota').value        = p.kota;
    document.getElementById('fAgama').value       = p.agama_id;
    document.getElementById('fPendidikan').value  = p.pendidikan_id;
    document.getElementById('fPekerjaan').value   = p.pekerjaan_id;
    document.getElementById('fAlergi').value      = p.riwayat_alergi;

    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  function clearForm() {
    ['fRm','fNik','fNama','fKk','fHp','fAlamat','fDesa','fKota','fAlergi'].forEach(function(id) {
      document.getElementById(id).value = '';
    });
    ['fJenkel','fDarah','fAgama','fPendidikan','fPekerjaan'].forEach(function(id) {
      document.getElementById(id).value = '';
    });
    document.getElementById('fTgl').value = '';
  }

  function clearErrors() {
    ['errRm','errNik','errNama','errTgl','errJenkel'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) { el.textContent = ''; el.classList.remove('show'); }
    });
    ['fRm','fNik','fNama','fTgl','fJenkel'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) el.classList.remove('error');
    });
  }

  /* ── MODAL HAPUS ── */
  function openDel(id, nama) {
    document.getElementById('delMsg').textContent    = 'Pasien "' + nama + '" akan dihapus.';
    document.getElementById('delForm').action        = BASE_URL + '/' + id;
    document.getElementById('delOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDel() {
    document.getElementById('delOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  /* ── MODAL INFO ── */
  function openInfo(id) {
    var p = pasienMap[id];
    if (!p) return;
    
    document.getElementById('infoNama').textContent = p.nama || '-';
    document.getElementById('infoRm').textContent = p.no_rm || '-';
    document.getElementById('infoNik').textContent = p.nik || '-';
    document.getElementById('infoKk').textContent = p.nama_kk || '-';
    document.getElementById('infoTgl').textContent = p.tgl_lahir ? p.tgl_lahir : '-';
    document.getElementById('infoJenkel').textContent = p.jenis_kelamin === 'L' ? 'Laki-laki' : (p.jenis_kelamin === 'P' ? 'Perempuan' : '-');
    document.getElementById('infoDarah').textContent = p.golongan_darah || '-';
    document.getElementById('infoHp').textContent = p.no_hp || '-';
    
    var fullAlamat = [];
    if(p.alamat) fullAlamat.push(p.alamat);
    if(p.desa) fullAlamat.push(p.desa);
    if(p.kota) fullAlamat.push(p.kota);
    document.getElementById('infoAlamat').textContent = fullAlamat.length > 0 ? fullAlamat.join(', ') : '-';
    
    var getSelectText = function(selectId, val) {
      if(!val) return '-';
      var sel = document.getElementById(selectId);
      if(!sel) return '-';
      for(var i=0; i<sel.options.length; i++) {
        if(sel.options[i].value == val) return sel.options[i].text;
      }
      return '-';
    };
    
    document.getElementById('infoAgama').textContent = getSelectText('fAgama', p.agama_id);
    document.getElementById('infoPendidikan').textContent = getSelectText('fPendidikan', p.pendidikan_id);
    document.getElementById('infoPekerjaan').textContent = getSelectText('fPekerjaan', p.pekerjaan_id);

    document.getElementById('infoOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeInfo() {
    document.getElementById('infoOverlay').classList.remove('open');
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
  document.getElementById('infoOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeInfo();
  });

  // Tutup dengan Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal(); closeDel(); closeInfo(); }
  });


</script>
@endpush