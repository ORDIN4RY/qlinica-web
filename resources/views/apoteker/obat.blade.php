@extends('layouts.app')

@section('title', 'Stok Obat')
@section('page-title', 'Stok Obat')
@section('page-subtitle', 'Kelola stok dan informasi obat di apotek')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ===== STATISTIK ===== --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  @php
    $total   = $obats->total();
    $habis   = \App\Models\Obat::where('stok', 0)->count();
    $menipis = \App\Models\Obat::whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0)->count();
    $aman    = \App\Models\Obat::whereColumn('stok', '>', 'stok_minimum')->count();
  @endphp
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Jenis</p>
    <p class="text-3xl font-bold text-slate-800">{{ $total }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Stok Aman</p>
    <p class="text-3xl font-bold text-green-600">{{ $aman }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Menipis</p>
    <p class="text-3xl font-bold text-amber-500">{{ $menipis }}</p>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
    <p class="text-sm text-gray-500">Habis</p>
    <p class="text-3xl font-bold text-red-600">{{ $habis }}</p>
  </div>
</div>

{{-- ===== TABEL ===== --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 gap-3">
    <h2 class="font-bold text-lg text-gray-800">Daftar Obat</h2>
    <button onclick="openModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2 text-sm font-medium">
      <i class="fas fa-plus"></i> Tambah Obat
    </button>
  </div>

  {{-- Search & Filter --}}
  <form method="GET" action="{{ route('apoteker.obat') }}" class="mb-5 flex flex-col sm:flex-row gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode obat..."
      class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm outline-none">
    <select name="kategori" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm outline-none">
      <option value="">Semua Kategori</option>
      @foreach($kategoriList as $kat)
        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
      @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 text-sm font-medium">
      <i class="fas fa-search mr-1"></i> Cari
    </button>
    @if(request('search') || request('kategori'))
      <a href="{{ route('apoteker.obat') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium flex items-center gap-1">
        <i class="fas fa-times"></i> Reset
      </a>
    @endif
  </form>

  {{-- Alert --}}
  <div id="alertBox" class="hidden mb-4 px-4 py-3 rounded-lg text-sm font-medium"></div>

  {{-- Table --}}
  <div class="overflow-x-auto -mx-4 md:mx-0">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Kode</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Nama Obat</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Kategori</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Satuan</th>
          <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Stok</th>
          <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Min</th>
          <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Harga</th>
          <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Status</th>
          <th class="text-center px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody id="obatTableBody">
        @forelse($obats as $obat)
        <tr class="border-b border-gray-100 hover:bg-gray-50 transition" id="row-{{ $obat->id }}">
          <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $obat->kode ?? '-' }}</td>
          <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $obat->nama }}</td>
          <td class="px-4 py-3 text-gray-600">{{ $obat->kategori ?? '-' }}</td>
          <td class="px-4 py-3 text-gray-600">{{ $obat->satuan ?? '-' }}</td>
          <td class="px-4 py-3 text-center font-bold {{ $obat->stok == 0 ? 'text-red-600' : ($obat->stok <= $obat->stok_minimum ? 'text-amber-600' : 'text-green-700') }}">
            {{ $obat->stok }}
          </td>
          <td class="px-4 py-3 text-center text-gray-500">{{ $obat->stok_minimum }}</td>
          <td class="px-4 py-3 text-right whitespace-nowrap text-gray-700">Rp {{ number_format($obat->harga, 0, ',', '.') }}</td>
          <td class="px-4 py-3 text-center">
            @if($obat->stok == 0)
              <span class="px-2 py-1 bg-red-100 text-red-700 font-medium text-xs rounded-full">Habis</span>
            @elseif($obat->stok <= $obat->stok_minimum)
              <span class="px-2 py-1 bg-amber-100 text-amber-700 font-medium text-xs rounded-full">Menipis</span>
            @else
              <span class="px-2 py-1 bg-green-100 text-green-700 font-medium text-xs rounded-full">Tersedia</span>
            @endif
          </td>
          <td class="px-4 py-3 text-center whitespace-nowrap">
            <button onclick="openModal({{ json_encode($obat) }})"
              class="inline-flex items-center gap-1 text-xs px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition mr-1">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button onclick="confirmDelete({{ $obat->id }}, '{{ addslashes($obat->nama) }}')"
              class="inline-flex items-center gap-1 text-xs px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition">
              <i class="fas fa-trash"></i> Hapus
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="px-4 py-10 text-center text-gray-400">
            <i class="fas fa-box-open text-4xl mb-3 block"></i>
            Belum ada data obat.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($obats->hasPages())
  <div class="flex flex-col sm:flex-row items-center justify-between mt-5 gap-3">
    <p class="text-sm text-gray-500">
      Menampilkan {{ $obats->firstItem() }}–{{ $obats->lastItem() }} dari {{ $obats->total() }} data
    </p>
    <div class="flex gap-1">
      {{ $obats->links('vendor.pagination.simple-tailwind') }}
    </div>
  </div>
  @endif
</div>

{{-- ===== MODAL TAMBAH / EDIT ===== --}}
<div id="modalObat" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
      <h3 class="text-white font-bold text-base flex items-center gap-2">
        <i class="fas fa-pills"></i>
        <span id="modalTitle">Tambah Obat</span>
      </h3>
      <button onclick="closeModal()" class="text-white/80 hover:text-white transition text-xl leading-none">&times;</button>
    </div>

    {{-- Body --}}
    <form id="formObat" class="p-6 space-y-4">
      <input type="hidden" id="obatId">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Obat <span class="text-gray-400">(opsional)</span></label>
          <input type="text" id="fKode" maxlength="20" placeholder="Otomatis jika kosong"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan</label>
          <input type="text" id="fSatuan" maxlength="20" placeholder="tablet, kapsul, ml..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Obat <span class="text-red-500">*</span></label>
        <input type="text" id="fNama" maxlength="100" placeholder="Nama obat"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        <p class="text-red-500 text-xs mt-1 hidden" id="errNama">Nama obat wajib diisi.</p>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
        <input type="text" id="fKategori" maxlength="50" list="kategoriSuggestions" placeholder="antibiotik, analgesik, vitamin..."
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        <datalist id="kategoriSuggestions">
          @foreach($kategoriList as $kat)
            <option value="{{ $kat }}">
          @endforeach
        </datalist>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Stok <span class="text-red-500">*</span></label>
          <input type="number" id="fStok" min="0" placeholder="0"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
          <p class="text-red-500 text-xs mt-1 hidden" id="errStok">Stok wajib diisi.</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
          <input type="number" id="fStokMin" min="0" placeholder="10"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
        <input type="number" id="fHarga" min="0" step="100" placeholder="0"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        <p class="text-red-500 text-xs mt-1 hidden" id="errHarga">Harga wajib diisi.</p>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
        <textarea id="fKeterangan" rows="2" placeholder="Catatan tambahan..."
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none resize-none"></textarea>
      </div>

      {{-- Footer --}}
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeModal()"
          class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium transition">Batal</button>
        <button type="submit" id="btnSimpan"
          class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
          <i class="fas fa-save"></i> Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
<div id="modalHapus" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
    <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
      <i class="fas fa-trash-alt"></i>
    </div>
    <h3 class="font-bold text-gray-800 text-base mb-1">Hapus Obat?</h3>
    <p class="text-gray-500 text-sm mb-5">Obat <strong id="hapusNama" class="text-gray-700"></strong> akan dihapus secara permanen.</p>
    <div class="flex gap-3 justify-center">
      <button onclick="closeHapus()" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium">Batal</button>
      <button id="btnHapus" onclick="doDelete()"
        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium flex items-center gap-2 transition">
        <i class="fas fa-trash"></i> Hapus
      </button>
    </div>
  </div>
</div>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const STORE  = '{{ route("apoteker.obat.store") }}';
const UPDATE = (id) => `/apoteker/obat/${id}`;
const DELETE = (id) => `/apoteker/obat/${id}`;

let deleteId = null;

// ── Modal Tambah/Edit ──
function openModal(obat = null) {
  document.getElementById('modalObat').classList.remove('hidden');
  document.getElementById('modalTitle').textContent = obat ? 'Edit Obat' : 'Tambah Obat';
  document.getElementById('obatId').value        = obat ? obat.id : '';
  document.getElementById('fKode').value         = obat ? (obat.kode ?? '') : '';
  document.getElementById('fNama').value         = obat ? obat.nama : '';
  document.getElementById('fSatuan').value       = obat ? (obat.satuan ?? '') : '';
  document.getElementById('fKategori').value     = obat ? (obat.kategori ?? '') : '';
  document.getElementById('fStok').value         = obat ? obat.stok : '';
  document.getElementById('fStokMin').value      = obat ? obat.stok_minimum : '10';
  document.getElementById('fHarga').value        = obat ? obat.harga : '';
  document.getElementById('fKeterangan').value   = obat ? (obat.keterangan ?? '') : '';
  ['errNama','errStok','errHarga'].forEach(id => document.getElementById(id).classList.add('hidden'));
}
function closeModal() { document.getElementById('modalObat').classList.add('hidden'); }

// ── Modal Hapus ──
function confirmDelete(id, nama) {
  deleteId = id;
  document.getElementById('hapusNama').textContent = nama;
  document.getElementById('modalHapus').classList.remove('hidden');
}
function closeHapus() { document.getElementById('modalHapus').classList.add('hidden'); deleteId = null; }

// ── Alert ──
function showAlert(msg, type='green') {
  const box = document.getElementById('alertBox');
  const cls  = { green:'bg-green-50 border border-green-300 text-green-800', red:'bg-red-50 border border-red-300 text-red-800' };
  box.className = `mb-4 px-4 py-3 rounded-lg text-sm font-medium ${cls[type]}`;
  box.textContent = msg;
  box.classList.remove('hidden');
  setTimeout(() => box.classList.add('hidden'), 4000);
}

// ── Status badge helper ──
function statusBadge(stok, min) {
  if (stok == 0) return '<span class="px-2 py-1 bg-red-100 text-red-700 font-medium text-xs rounded-full">Habis</span>';
  if (stok <= min) return '<span class="px-2 py-1 bg-amber-100 text-amber-700 font-medium text-xs rounded-full">Menipis</span>';
  return '<span class="px-2 py-1 bg-green-100 text-green-700 font-medium text-xs rounded-full">Tersedia</span>';
}
function stokColor(stok, min) {
  if (stok == 0) return 'text-red-600';
  if (stok <= min) return 'text-amber-600';
  return 'text-green-700';
}
function formatRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

// ── Submit Form ──
document.getElementById('formObat').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id    = document.getElementById('obatId').value;
  const nama  = document.getElementById('fNama').value.trim();
  const stok  = document.getElementById('fStok').value;
  const harga = document.getElementById('fHarga').value;

  // Validasi
  let valid = true;
  if (!nama)  { document.getElementById('errNama').classList.remove('hidden'); valid = false; }
  else        { document.getElementById('errNama').classList.add('hidden'); }
  if (stok === '') { document.getElementById('errStok').classList.remove('hidden'); valid = false; }
  else        { document.getElementById('errStok').classList.add('hidden'); }
  if (harga === '') { document.getElementById('errHarga').classList.remove('hidden'); valid = false; }
  else        { document.getElementById('errHarga').classList.add('hidden'); }
  if (!valid) return;

  const btn = document.getElementById('btnSimpan');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

  const payload = {
    kode:         document.getElementById('fKode').value.trim() || null,
    nama,
    satuan:       document.getElementById('fSatuan').value.trim() || null,
    kategori:     document.getElementById('fKategori').value.trim() || null,
    stok:         parseInt(stok),
    stok_minimum: parseInt(document.getElementById('fStokMin').value || 10),
    harga:        parseFloat(harga),
    keterangan:   document.getElementById('fKeterangan').value.trim() || null,
  };

  try {
    const url    = id ? UPDATE(id) : STORE;
    const method = id ? 'PUT' : 'POST';
    const res    = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.success) {
      closeModal();
      showAlert(data.message, 'green');

      if (id) {
        // Update row yang ada
        const o = data.obat;
        const row = document.getElementById('row-' + id);
        if (row) {
          row.cells[0].textContent = o.kode ?? '-';
          row.cells[1].textContent = o.nama;
          row.cells[2].textContent = o.kategori ?? '-';
          row.cells[3].textContent = o.satuan ?? '-';
          row.cells[4].className   = `px-4 py-3 text-center font-bold ${stokColor(o.stok, o.stok_minimum)}`;
          row.cells[4].textContent = o.stok;
          row.cells[5].textContent = o.stok_minimum;
          row.cells[6].textContent = formatRp(o.harga);
          row.cells[7].innerHTML   = statusBadge(o.stok, o.stok_minimum);
        }
      } else {
        // Reload untuk tampilkan data baru di tabel
        window.location.reload();
      }
    } else {
      // Tampilkan error validasi server
      const errors = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'Terjadi kesalahan.');
      showAlert(errors, 'red');
    }
  } catch (err) {
    showAlert('Terjadi kesalahan koneksi.', 'red');
    console.error(err);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
  }
});

// ── Hapus ──
async function doDelete() {
  if (!deleteId) return;
  const btn = document.getElementById('btnHapus');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

  try {
    const res  = await fetch(DELETE(deleteId), {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();

    if (data.success) {
      closeHapus();
      showAlert(data.message, 'green');
      const row = document.getElementById('row-' + deleteId);
      if (row) {
        row.style.transition = 'opacity 0.3s';
        row.style.opacity = '0';
        setTimeout(() => row.remove(), 300);
      }
    } else {
      showAlert(data.message || 'Gagal menghapus data.', 'red');
    }
  } catch (err) {
    showAlert('Terjadi kesalahan koneksi.', 'red');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-trash"></i> Hapus';
  }
}

// Tutup modal saat klik backdrop
document.getElementById('modalObat').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
document.getElementById('modalHapus').addEventListener('click', function(e) {
  if (e.target === this) closeHapus();
});
// Tutup modal dengan Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { closeModal(); closeHapus(); }
});
</script>
@endsection
