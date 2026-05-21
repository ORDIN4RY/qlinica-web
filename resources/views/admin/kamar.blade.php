@extends('layouts.app')

@section('title', 'Manajemen Kamar')
@section('page-title', 'Manajemen Kamar Rawat Inap')
@section('page-subtitle', 'Kelola daftar ruang perawatan, kelas, dan tarif sewa per malam')

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
              <form action="{{ route('admin.kamar.destroy', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kamar ini?');">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
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
<div id="tambahKamarModal" class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden fade-in-up">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
      <h3 class="font-bold text-lg text-gray-800" id="modalTitle">Tambah Kamar Baru</h3>
      <button onclick="closeModal('tambahKamarModal')" class="text-gray-400 hover:text-red-500 transition">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>
    <form id="kamarForm" action="{{ route('admin.kamar.store') }}" method="POST">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <div class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Kode Kamar</label>
          <input type="text" name="kode_kamar" id="kode_kamar" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kamar</label>
          <input type="text" name="nama_kamar" id="nama_kamar" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
            <select name="kelas" id="kelas" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
              <option value="VIP">VIP</option>
              <option value="Kelas 1">Kelas 1</option>
              <option value="Kelas 2">Kelas 2</option>
              <option value="Kelas 3">Kelas 3</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
            <select name="status" id="status" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
              <option value="Tersedia">Tersedia</option>
              <option value="Perbaikan">Perbaikan</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tarif Sewa per Malam (Rp)</label>
            <input type="number" name="tarif_per_malam" id="tarif_per_malam" min="0" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-mono font-bold text-green-700">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Kapasitas (Kasur)</label>
            <input type="number" name="kapasitas" id="kapasitas" min="1" value="1" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
          </div>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
        <button type="button" onclick="closeModal('tambahKamarModal')" class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-xl font-medium transition text-sm">Batal</button>
        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition shadow-sm text-sm">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    // Reset if it's open for new
    document.getElementById('modalTitle').innerText = 'Tambah Kamar Baru';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('kamarForm').action = "{{ route('admin.kamar.store') }}";
    document.getElementById('kode_kamar').value = '';
    document.getElementById('nama_kamar').value = '';
    document.getElementById('tarif_per_malam').value = '';
    document.getElementById('kapasitas').value = '1';
  }

  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
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
</script>
@endpush
