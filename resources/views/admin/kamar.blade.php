@extends('layouts.app')

@section('title', 'Manajemen Kamar')
@section('page-title', 'Manajemen Kamar Rawat Inap')
@section('page-subtitle', 'Kelola daftar ruang perawatan, kelas, dan tarif sewa per malam')

@push('styles')
<style>
  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55);
    z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:20px; width:100%; max-width:540px;
    box-shadow:0 32px 80px rgba(15,23,42,.22);
    animation:modalIn .22s cubic-bezier(.4,0,.2,1); margin:16px; overflow:hidden; }
  @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:none} }
  .modal-head { padding:22px 28px 18px; border-bottom:1px solid #e5e7eb;
    display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
  .modal-body { padding:22px 28px; }
  .modal-foot { padding:16px 28px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; flex-shrink:0; }

  /* ── FORM ── */
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
</style>
@endpush

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
  <!-- Search -->
  <form action="{{ route('admin.kamar') }}" method="GET" class="w-full sm:w-auto relative">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kamar..." 
      class="w-full sm:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm text-sm">
    <i class="fas fa-search text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
  </form>

  <!-- Add Button -->
   @if(auth()->user()->hasMenuAccess('kamar', 'tambah'))
  <button onclick="openModal('tambahKamarModal')" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm flex items-center justify-center gap-2 text-sm">
    <i class="fas fa-plus"></i> Tambah Kamar Baru
  </button>
  @endif
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
        <tr>
          <th class="px-6 py-4">Kode</th>
          <th class="px-6 py-4">Nama Kamar</th>
          <th class="px-6 py-4">Kelas</th>
          <th class="px-6 py-4">Tarif per Malam</th>
          <th class="px-6 py-4 text-center">Kapasitas</th>
          <th class="px-6 py-4 text-center">Status</th>
          @if (auth()->user()->hasMenuAccess('kamar', 'edit') || auth()->user()->hasMenuAccess('kamar', 'hapus'))
          <th class="px-6 py-4 text-right">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($kamars as $k)
          <tr class="hover:bg-blue-50/50 transition group">
            <td class="px-6 py-4 font-mono text-gray-500">{{ $k->kode_kamar }}</td>
            <td class="px-6 py-4 font-semibold text-gray-800">{{ $k->nama_kamar }}</td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">{{ $k->kelas }}</span>
            </td>
            <td class="px-6 py-4 font-medium text-gray-900">Rp {{ number_format($k->tarif_per_malam, 0, ',', '.') }}</td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">{{ $k->terisi ?? 0 }} / {{ $k->kapasitas ?? 1 }}</span>
            </td>
            <td class="px-6 py-4 text-center">
              @if($k->status === 'Tersedia' && !$k->isFull())
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i> Tersedia ({{ $k->availableBeds() }} kosong)</span>
              @elseif($k->status === 'Tersedia' && $k->isFull())
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold"><i class="fas fa-bed mr-1"></i> Penuh</span>
              @elseif($k->status === 'Terisi')
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold"><i class="fas fa-bed mr-1"></i> Terisi Penuh</span>
              @else
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fas fa-wrench mr-1"></i> Perbaikan</span>
              @endif
            </td>
            @if(auth()->user()->hasMenuAccess('kamar', 'edit') || auth()->user()->hasMenuAccess('kamar', 'hapus'))
            <td class="px-6 py-4 text-right space-x-1">
              @if(auth()->user()->hasMenuAccess('kamar', 'edit'))
              <button onclick="editKamar({{ $k }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              @endif
              @if(auth()->user()->hasMenuAccess('kamar', 'hapus'))
              <button onclick="openDelModal({{ $k->id }}, '{{ addslashes($k->nama_kamar) }}')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                <i class="fas fa-trash-alt"></i>
              </button>
              @endif
            </td>
            @endif
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
              <i class="fas fa-bed text-4xl mb-3 text-gray-300 block"></i>
              Belum ada data kamar rawat inap.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <!-- Pagination -->
  @if($kamars->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
      {{ $kamars->links() }}
    </div>
  @endif
</div>

<!-- Modal Tambah/Edit -->
<div id="tambahKamarModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-head bg-gray-50/50">
      <h3 class="font-bold text-lg text-gray-800" id="modalTitle">Tambah Kamar Baru</h3>
      <button onclick="closeModal('tambahKamarModal')" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-gray-500">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <form id="kamarForm" action="{{ route('admin.kamar.store') }}" method="POST">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <div class="modal-body space-y-4">
        <div class="form-group">
          <label>Kode Kamar <span class="text-red-500 normal-case font-normal">*</span></label>
          <input type="text" name="kode_kamar" id="kode_kamar" placeholder="Contoh: K-101" required class="form-input">
        </div>
        <div class="form-group">
          <label>Nama Kamar <span class="text-red-500 normal-case font-normal">*</span></label>
          <input type="text" name="nama_kamar" id="nama_kamar" placeholder="Nama Kamar/Ruang" required class="form-input">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="form-group">
            <label>Kelas <span class="text-red-500 normal-case font-normal">*</span></label>
            <select name="kelas" id="kelas" required class="form-select">
              <option value="VIP">VIP</option>
              <option value="Kelas 1">Kelas 1</option>
              <option value="Kelas 2">Kelas 2</option>
              <option value="Kelas 3">Kelas 3</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status <span class="text-red-500 normal-case font-normal">*</span></label>
            <select name="status" id="status" required class="form-select">
              <option value="Tersedia">Tersedia</option>
              <option value="Perbaikan">Perbaikan</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="form-group">
            <label>Tarif Sewa per Malam (Rp) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" name="tarif_per_malam" id="tarif_per_malam" min="0" placeholder="0" required class="form-input font-mono font-bold text-green-700">
          </div>
          <div class="form-group">
            <label>Kapasitas (Kasur) <span class="text-red-500 normal-case font-normal">*</span></label>
            <input type="number" name="kapasitas" id="kapasitas" min="1" value="1" required class="form-input">
          </div>
        </div>
      </div>
      <div class="modal-foot bg-gray-50/50">
        <button type="button" onclick="closeModal('tambahKamarModal')" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
        <button type="submit" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl font-bold text-sm shadow-md transition">
          <i class="fas fa-save mr-1.5"></i> Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ─── MODAL HAPUS ─── --}}
<div class="modal-overlay" id="delKamarOverlay">
  <div class="bg-white rounded-2xl p-8 w-full max-w-sm mx-4 text-center shadow-2xl" style="animation:modalIn .22s ease">
    <div class="confirm-icon bg-red-50">
      <i class="fas fa-trash text-red-500 text-xl"></i>
    </div>
    <h3 class="text-base font-bold text-gray-800 mb-2">Hapus Kamar?</h3>
    <p class="text-sm text-gray-500 mb-6" id="delMsg">Data kamar akan dihapus secara permanen.</p>
    <div class="flex justify-center gap-3">
      <button onclick="closeDelModal()"
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
  function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
    
    // Reset if it's open for new
    if (id === 'tambahKamarModal') {
      document.getElementById('modalTitle').innerText = 'Tambah Kamar Baru';
      document.getElementById('formMethod').value = 'POST';
      document.getElementById('kamarForm').action = "{{ route('admin.kamar.store') }}";
      document.getElementById('kode_kamar').value = '';
      document.getElementById('nama_kamar').value = '';
      document.getElementById('tarif_per_malam').value = '';
      document.getElementById('kapasitas').value = '1';
      document.getElementById('kelas').value = 'VIP';
      document.getElementById('status').value = 'Tersedia';
    }
  }

  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
  }

  function editKamar(kamar) {
    openModal('tambahKamarModal');
    document.getElementById('modalTitle').innerText = 'Edit Kamar';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('kamarForm').action = `/admin/kamar/${kamar.id}`;
    
    document.getElementById('kode_kamar').value = kamar.kode_kamar;
    document.getElementById('nama_kamar').value = kamar.nama_kamar;
    document.getElementById('kelas').value = kamar.kelas;
    document.getElementById('status').value = kamar.status;
    document.getElementById('tarif_per_malam').value = kamar.tarif_per_malam;
    document.getElementById('kapasitas').value = kamar.kapasitas || 1;
  }

  function openDelModal(id, namaKamar) {
    document.getElementById('delMsg').textContent = 'Kamar "' + namaKamar + '" akan dihapus secara permanen.';
    document.getElementById('delForm').action = '/admin/kamar/' + id;
    document.getElementById('delKamarOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeDelModal() {
    document.getElementById('delKamarOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }

  // Close modals on clicking overlay
  document.getElementById('tambahKamarModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal('tambahKamarModal');
  });
  document.getElementById('delKamarOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeDelModal();
  });

  // Close modals on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModal('tambahKamarModal');
      closeDelModal();
    }
  });
</script>
@endpush
