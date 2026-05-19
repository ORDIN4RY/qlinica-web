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
          <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Harga Beli</th>
          <th class="text-right px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Harga Jual</th>
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
          <td class="px-4 py-3 text-right text-gray-500 font-medium whitespace-nowrap">Rp {{ number_format($obat->harga_beli, 0, ',', '.') }}</td>
          <td class="px-4 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">Rp {{ number_format($obat->harga, 0, ',', '.') }}</td>
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
            <button onclick="openModalRestok({{ json_encode($obat) }})"
              class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg transition mr-1" title="Tambah Stok / Restok">
              <i class="fas fa-plus-circle"></i> Restok
            </button>
            <button onclick="openModalStokOpname({{ json_encode($obat) }})"
              class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg transition mr-1" title="Stok Opname">
              <i class="fas fa-boxes"></i> Opname
            </button>
            <button onclick="openModalRiwayatStokOpname({{ $obat->id }}, '{{ addslashes($obat->nama) }}')"
              class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 bg-gray-50 text-gray-700 hover:bg-gray-100 rounded-lg transition mr-1" title="Riwayat Opname">
              <i class="fas fa-history"></i> Riwayat
            </button>
            <button onclick="openModal({{ json_encode($obat) }})"
              class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition mr-1">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button onclick="confirmDelete({{ $obat->id }}, '{{ addslashes($obat->nama) }}')"
              class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition">
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
          <label class="block text-xs font-semibold text-gray-600 mb-1">Stok <span class="text-red-500">*</span><span id="stokLabelMessage" class="text-amber-600 font-normal"></span></label>
          <input type="number" id="fStok" min="0" placeholder="0"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none disabled:bg-gray-100 disabled:text-gray-500 disabled:border-gray-200">
          <p class="text-red-500 text-xs mt-1 hidden" id="errStok">Stok wajib diisi.</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Stok Minimum <span class="text-red-500">*</span></label>
          <input type="number" id="fStokMin" min="0" placeholder="10"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1 font-bold">Harga Beli (Rp) <span class="text-red-500">*</span></label>
          <input type="number" id="fHargaBeli" min="0" step="100" placeholder="Harga modal"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
          <p class="text-red-500 text-xs mt-1 hidden" id="errHargaBeli">Harga beli wajib diisi.</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1 font-bold">Harga Jual (Rp) <span class="text-red-500">*</span></label>
          <input type="number" id="fHarga" min="0" step="100" placeholder="Harga jual"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
          <p class="text-red-500 text-xs mt-1 hidden" id="errHarga">Harga jual wajib diisi.</p>
        </div>
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

{{-- ===== MODAL TAMBAH STOK / RESTOK ===== --}}
<div id="modalRestok" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
      <h3 class="text-white font-bold text-base flex items-center gap-2">
        <i class="fas fa-plus-circle"></i>
        <span>Penerimaan / Tambah Stok Obat</span>
      </h3>
      <button onclick="closeModalRestok()" class="text-white/80 hover:text-white transition text-xl leading-none">&times;</button>
    </div>

    {{-- Body --}}
    <form id="formRestok" class="p-6 space-y-4">
      <input type="hidden" id="restokObatId">
      
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Obat</label>
        <input type="text" id="restokObatNama" readonly
          class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 outline-none">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Stok Saat Ini</label>
          <input type="number" id="restokStokSaatIni" readonly
            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-bold text-gray-600 outline-none">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1 font-bold">Jumlah Masuk <span class="text-red-500">*</span></label>
          <input type="number" id="restokJumlahMasuk" min="1" required placeholder="Contoh: 100"
            class="w-full border border-indigo-300 rounded-lg px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1 font-bold">Supplier / Distributor <span class="text-red-500">*</span></label>
        <input type="text" id="restokSupplier" required placeholder="Contoh: PT. Kimia Farma, PBF Jaya..."
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-bold">Harga Beli Baru per Satuan <span class="text-gray-400">(opsional, isi jika modal berubah)</span></label>
        <input type="number" id="restokHargaBeli" min="0" step="100" placeholder="Biarkan kosong jika harga modal tetap"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Keterangan / No. Faktur <span class="text-gray-400">(opsional)</span></label>
        <input type="text" id="restokKeterangan" placeholder="Contoh: Faktur F-202605, Expired Jan 2028..."
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
      </div>

      {{-- Footer --}}
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeModalRestok()"
          class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium transition">Batal</button>
        <button type="submit" id="btnSimpanRestok"
          class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
          <i class="fas fa-check"></i> Tambah Stok
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL STOK OPNAME ===== --}}
<div id="modalStokOpname" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-amber-500 to-amber-600">
      <h3 class="text-white font-bold text-base flex items-center gap-2">
        <i class="fas fa-boxes"></i>
        <span>Stok Opname Obat</span>
      </h3>
      <button onclick="closeModalStokOpname()" class="text-white/80 hover:text-white transition text-xl leading-none">&times;</button>
    </div>

    {{-- Body --}}
    <form id="formStokOpname" class="p-6 space-y-4">
      <input type="hidden" id="opnameObatId">
      
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Obat</label>
        <input type="text" id="opnameObatNama" readonly
          class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 outline-none">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Stok di Sistem</label>
          <input type="number" id="opnameStokSistem" readonly
            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-bold text-gray-600 outline-none">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1 font-bold">Stok Fisik Sebenarnya <span class="text-red-500">*</span></label>
          <input type="number" id="opnameStokFisik" min="0" required placeholder="0"
            class="w-full border border-amber-300 rounded-lg px-3 py-2 text-sm font-bold focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none">
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Selisih Penyesuaian</label>
        <input type="text" id="opnameSelisih" readonly value="0"
          class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-bold text-gray-600 outline-none">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Penyesuaian / Keterangan <span class="text-red-500">*</span></label>
        <textarea id="opnameKeterangan" rows="3" required placeholder="Contoh: Expired 5 botol, Selisih hitung awal, Rusak kemasan..."
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none resize-none"></textarea>
      </div>

      {{-- Footer --}}
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="closeModalStokOpname()"
          class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium transition">Batal</button>
        <button type="submit" id="btnSimpanOpname"
          class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
          <i class="fas fa-save"></i> Simpan Penyesuaian
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ===== MODAL RIWAYAT STOK OPNAME ===== --}}
<div id="modalRiwayatStokOpname" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-800">
      <h3 class="text-white font-bold text-base flex items-center gap-2">
        <i class="fas fa-history"></i>
        <span>Riwayat Penyesuaian Stok (Stok Opname)</span>
      </h3>
      <button onclick="closeModalRiwayatStokOpname()" class="text-white/80 hover:text-white transition text-xl leading-none">&times;</button>
    </div>

    {{-- Body --}}
    <div class="p-6">
      <h4 class="font-bold text-gray-800 text-sm mb-3">Obat: <span id="riwayatObatNama" class="text-blue-700"></span></h4>
      
      <div class="overflow-y-auto max-h-80 border border-gray-200 rounded-xl">
        <table class="min-w-full text-xs text-left">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-2.5 font-semibold text-gray-600">Tanggal</th>
              <th class="px-4 py-2.5 font-semibold text-gray-600">Petugas</th>
              <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Stok Sistem</th>
              <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Stok Fisik</th>
              <th class="px-4 py-2.5 text-center font-semibold text-gray-600">Selisih</th>
              <th class="px-4 py-2.5 font-semibold text-gray-600">Alasan/Keterangan</th>
            </tr>
          </thead>
          <tbody id="riwayatStokOpnameTableBody">
            <!-- Ajax content -->
          </tbody>
        </table>
      </div>
    </div>

    {{-- Footer --}}
    <div class="flex justify-end px-6 py-4 bg-gray-50 border-t border-gray-100">
      <button onclick="closeModalRiwayatStokOpname()"
        class="px-5 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 text-sm font-medium transition">Tutup</button>
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
  document.getElementById('fStok').disabled     = obat ? true : false;
  document.getElementById('stokLabelMessage').textContent = obat ? ' (Ubah via Stok Opname)' : '';
  document.getElementById('fStokMin').value      = obat ? obat.stok_minimum : '10';
  document.getElementById('fHargaBeli').value    = obat ? obat.harga_beli : '';
  document.getElementById('fHarga').value        = obat ? obat.harga : '';
  document.getElementById('fKeterangan').value   = obat ? (obat.keterangan ?? '') : '';
  ['errNama','errStok','errHargaBeli','errHarga'].forEach(id => document.getElementById(id).classList.add('hidden'));
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
  const id        = document.getElementById('obatId').value;
  const nama  = document.getElementById('fNama').value.trim();
  const stok  = document.getElementById('fStok').value;
  const hargaBeli = document.getElementById('fHargaBeli').value;
  const harga     = document.getElementById('fHarga').value;

  // Validasi
  let valid = true;
  if (!nama)  { document.getElementById('errNama').classList.remove('hidden'); valid = false; }
  else        { document.getElementById('errNama').classList.add('hidden'); }
  if (stok === '') { document.getElementById('errStok').classList.remove('hidden'); valid = false; }
  else        { document.getElementById('errStok').classList.add('hidden'); }
  if (hargaBeli === '') { document.getElementById('errHargaBeli').classList.remove('hidden'); valid = false; }
  else        { document.getElementById('errHargaBeli').classList.add('hidden'); }
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
    harga_beli:   parseFloat(hargaBeli),
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
          row.cells[6].textContent = formatRp(o.harga_beli);
          row.cells[7].textContent = formatRp(o.harga);
          row.cells[8].innerHTML   = statusBadge(o.stok, o.stok_minimum);
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

// ── Modal Tambah Stok / Restok ──
function openModalRestok(obat) {
  document.getElementById('modalRestok').classList.remove('hidden');
  document.getElementById('restokObatId').value = obat.id;
  document.getElementById('restokObatNama').value = obat.nama;
  document.getElementById('restokStokSaatIni').value = obat.stok;
  document.getElementById('restokJumlahMasuk').value = '';
  document.getElementById('restokHargaBeli').value = '';
  document.getElementById('restokSupplier').value = '';
  document.getElementById('restokKeterangan').value = '';
}

function closeModalRestok() {
  document.getElementById('modalRestok').classList.add('hidden');
}

document.getElementById('formRestok').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('restokObatId').value;
  const jumlahMasuk = parseInt(document.getElementById('restokJumlahMasuk').value);
  const supplier = document.getElementById('restokSupplier').value.trim();
  const hargaBeli = document.getElementById('restokHargaBeli').value ? parseFloat(document.getElementById('restokHargaBeli').value) : null;
  const keterangan = document.getElementById('restokKeterangan').value.trim();

  if (!jumlahMasuk || jumlahMasuk < 1 || !supplier) return;

  const btn = document.getElementById('btnSimpanRestok');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

  try {
    const res = await fetch(`/apoteker/obat/${id}/restok`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ jumlah_masuk: jumlahMasuk, supplier, harga_beli: hargaBeli, keterangan })
    });
    const data = await res.json();

    if (data.success) {
      closeModalRestok();
      showAlert(data.message, 'green');
      setTimeout(() => window.location.reload(), 1000);
    } else {
      const err = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'Gagal menyimpan.');
      showAlert(err, 'red');
    }
  } catch (err) {
    showAlert('Terjadi kesalahan koneksi.', 'red');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-check"></i> Tambah Stok';
  }
});

// ── Modal Stok Opname ──
function openModalStokOpname(obat) {
  document.getElementById('modalStokOpname').classList.remove('hidden');
  document.getElementById('opnameObatId').value = obat.id;
  document.getElementById('opnameObatNama').value = obat.nama;
  document.getElementById('opnameStokSistem').value = obat.stok;
  document.getElementById('opnameStokFisik').value = obat.stok;
  document.getElementById('opnameSelisih').value = '0 (Tidak ada perubahan)';
  document.getElementById('opnameSelisih').className = 'w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-bold text-gray-600 outline-none';
  document.getElementById('opnameKeterangan').value = '';
}

function closeModalStokOpname() {
  document.getElementById('modalStokOpname').classList.add('hidden');
}

document.getElementById('opnameStokFisik').addEventListener('input', function() {
  const sistem = parseInt(document.getElementById('opnameStokSistem').value) || 0;
  const fisik = parseInt(this.value) || 0;
  const selisih = fisik - sistem;
  const inputSelisih = document.getElementById('opnameSelisih');
  if (selisih > 0) {
    inputSelisih.value = '+' + selisih + ' (Stok Bertambah)';
    inputSelisih.className = 'w-full bg-green-50 border border-green-200 rounded-lg px-3 py-2 text-sm font-bold text-green-700 outline-none';
  } else if (selisih < 0) {
    inputSelisih.value = selisih + ' (Stok Berkurang)';
    inputSelisih.className = 'w-full bg-red-50 border border-red-200 rounded-lg px-3 py-2 text-sm font-bold text-red-700 outline-none';
  } else {
    inputSelisih.value = '0 (Tidak ada perubahan)';
    inputSelisih.className = 'w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-bold text-gray-600 outline-none';
  }
});

document.getElementById('formStokOpname').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('opnameObatId').value;
  const stokFisik = parseInt(document.getElementById('opnameStokFisik').value);
  const keterangan = document.getElementById('opnameKeterangan').value.trim();

  if (stokFisik === '') return;

  const btn = document.getElementById('btnSimpanOpname');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

  try {
    const res = await fetch(`/apoteker/obat/${id}/stok-opname`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ stok_fisik: stokFisik, keterangan })
    });
    const data = await res.json();

    if (data.success) {
      closeModalStokOpname();
      showAlert(data.message, 'green');
      setTimeout(() => window.location.reload(), 1000);
    } else {
      const err = data.errors ? Object.values(data.errors).flat().join(' | ') : (data.message || 'Gagal menyimpan.');
      showAlert(err, 'red');
    }
  } catch (err) {
    showAlert('Terjadi kesalahan koneksi.', 'red');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Simpan Penyesuaian';
  }
});

// ── Modal Riwayat Stok Opname ──
async function openModalRiwayatStokOpname(id, nama) {
  document.getElementById('modalRiwayatStokOpname').classList.remove('hidden');
  document.getElementById('riwayatObatNama').textContent = nama;
  const tbody = document.getElementById('riwayatStokOpnameTableBody');
  tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>';

  try {
    const res = await fetch(`/apoteker/obat/${id}/riwayat-stok-opname`);
    const data = await res.json();

    if (data.success && data.riwayat.length > 0) {
      let html = '';
      data.riwayat.forEach(item => {
        const selisihCls = item.selisih.startsWith('+') ? 'text-green-600 font-bold' : (item.selisih === '0' ? 'text-gray-500' : 'text-red-600 font-bold');
        html += `
          <tr class="border-b border-gray-100 hover:bg-gray-50">
            <td class="px-4 py-3 text-gray-500 font-mono whitespace-nowrap">${item.tanggal}</td>
            <td class="px-4 py-3 font-medium text-gray-700">${item.petugas}</td>
            <td class="px-4 py-3 text-center text-gray-600">${item.stok_sistem}</td>
            <td class="px-4 py-3 text-center text-gray-800 font-bold">${item.stok_fisik}</td>
            <td class="px-4 py-3 text-center ${selisihCls}">${item.selisih}</td>
            <td class="px-4 py-3 text-gray-600 max-w-xs truncate" title="${item.keterangan}">${item.keterangan}</td>
          </tr>
        `;
      });
      tbody.innerHTML = html;
    } else {
      tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-info-circle mr-1"></i> Belum ada riwayat stok opname untuk obat ini.</td></tr>';
    }
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal memuat data.</td></tr>';
  }
}

function closeModalRiwayatStokOpname() {
  document.getElementById('modalRiwayatStokOpname').classList.add('hidden');
}

// Modal backdrop click
document.getElementById('modalObat').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
document.getElementById('modalHapus').addEventListener('click', function(e) {
  if (e.target === this) closeHapus();
});
document.getElementById('modalRestok').addEventListener('click', function(e) {
  if (e.target === this) closeModalRestok();
});
document.getElementById('modalStokOpname').addEventListener('click', function(e) {
  if (e.target === this) closeModalStokOpname();
});
document.getElementById('modalRiwayatStokOpname').addEventListener('click', function(e) {
  if (e.target === this) closeModalRiwayatStokOpname();
});

// Escape key press
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeModal();
    closeHapus();
    closeModalRestok();
    closeModalStokOpname();
    closeModalRiwayatStokOpname();
  }
});
</script>
@endsection
