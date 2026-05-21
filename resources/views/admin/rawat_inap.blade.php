@extends('layouts.app')

@section('title', 'Rawat Inap')
@section('page-title', 'Admisi Rawat Inap')
@section('page-subtitle', 'Kelola pendaftaran mondok pasien dan pengalokasian kamar')

@section('content')
  <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <!-- Filter Status -->
    <div class="flex gap-2">
      <a href="{{ route('admin.rawat_inap', ['status' => 'Aktif']) }}"
        class="px-4 py-2 text-sm font-medium rounded-xl border transition {{ request('status', 'Aktif') == 'Aktif' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">Sedang
        Dirawat</a>
      <a href="{{ route('admin.rawat_inap', ['status' => 'Selesai']) }}"
        class="px-4 py-2 text-sm font-medium rounded-xl border transition {{ request('status') == 'Selesai' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">Selesai
        / Pulang</a>
    </div>

    <!-- Antrian rawat inap is now inline -->
  </div>

  @if($rekomendasiData->count() > 0 && auth()->user()->hasMenuAccess('Rawat Inap', 'tambah'))
  <div class="mb-6 bg-white rounded-xl shadow-sm border border-emerald-200 overflow-hidden">
    <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-100 flex items-center gap-2">
      <i class="fas fa-clipboard-list text-emerald-600"></i>
      <h3 class="font-bold text-emerald-800">Antrian Masuk Rawat Inap (Rekomendasi Dokter)</h3>
    </div>
    <div class="overflow-x-auto p-4">
      <div class="space-y-4">
        @foreach($rekomendasiData as $rm)
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row items-center gap-4 justify-between">
          <div class="flex-1">
            <h4 class="font-bold text-gray-800">{{ $rm->pasien->nama }}</h4>
            <p class="text-xs text-gray-500">No. RM: {{ $rm->pasien->no_rm }} | Dokter: dr. {{ $rm->dokter->nama }}</p>
          </div>
          <div class="flex-1 w-full md:w-auto">
            <form action="{{ route('admin.rawat_inap.store') }}" method="POST" class="flex flex-col md:flex-row items-center gap-3 w-full">
              @csrf
              <input type="hidden" name="pasien_id" value="{{ $rm->pasien_id }}">
              <input type="hidden" name="dokter_id" value="{{ $rm->dokter_id }}">
              <input type="hidden" name="jenis_penjamin" value="Umum">
              <input type="hidden" name="no_sep" value="">
              <input type="hidden" name="tgl_masuk" value="{{ now()->format('Y-m-d\TH:i') }}">
              
              <div class="w-full md:w-40">
                <select class="pilih-kelas w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm" data-target="kamar_select_{{ $rm->pasien_id }}" required>
                  <option value="">-- Pilih Kelas --</option>
                  @php
                      $availableClasses = $kamarsTersedia->filter(function($k) { return $k->terisi < $k->kapasitas; })->pluck('kelas')->unique();
                  @endphp
                  @foreach($availableClasses as $kelas)
                    <option value="{{ $kelas }}">{{ $kelas }}</option>
                  @endforeach
                </select>
              </div>

              <div class="w-full md:w-48">
                <select name="kamar_id" id="kamar_select_{{ $rm->pasien_id }}" class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 text-sm" required>
                  <option value="">-- Pilih Kamar --</option>
                  <!-- Options populated by JS -->
                </select>
              </div>

              <button type="submit" class="w-full md:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm shadow-sm transition whitespace-nowrap">
                <i class="fas fa-check mr-1"></i> Accept
              </button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <!-- Table -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left min-w-max whitespace-nowrap">
        <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
          <tr>
            <th class="px-6 py-4">Tgl Masuk</th>
            <th class="px-6 py-4">Pasien</th>
            <th class="px-6 py-4">Kamar</th>
            <th class="px-6 py-4">Penjamin / DPJP</th>
            <th class="px-6 py-4 text-center">Status</th>
            @if (auth()->user()->hasMenuAccess('Rawat Inap', 'edit'))
              <th class="px-6 py-4 text-right">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse ($rawat_inaps as $ri)
            <tr class="hover:bg-blue-50/50 transition group">
              <td class="px-6 py-4">
                <span class="block font-semibold text-gray-800">{{ $ri->tgl_masuk->format('d M Y') }}</span>
                <span class="text-xs text-gray-500">{{ $ri->tgl_masuk->format('H:i') }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="block font-bold text-blue-900">{{ $ri->pasien->nama }}</span>
                <span class="text-xs font-mono text-gray-500">RM: {{ $ri->pasien->no_rm }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="block font-semibold text-gray-800">{{ $ri->kamar->nama_kamar }}</span>
                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">{{ $ri->kamar->kelas }}</span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="block font-semibold {{ $ri->jenis_penjamin == 'Umum' ? 'text-gray-800' : 'text-emerald-700' }}">{{ $ri->jenis_penjamin }}</span>
                <span class="text-xs text-gray-500">DPJP: dr. {{ $ri->dokter->nama }}</span>
              </td>
              <td class="px-6 py-4 text-center">
                @if($ri->status === 'Aktif')
                  <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold animate-pulse"><i
                      class="fas fa-bed mr-1"></i> Dirawat</span>
                @else
                  <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i
                      class="fas fa-check-circle mr-1"></i> Selesai</span><br>
                  <span class="text-[10px] text-gray-400 mt-1 block">Tgl Keluar: {{ $ri->tgl_keluar->format('d/m/Y') }}</span>
                @endif
              </td>
              @if (auth()->user()->hasMenuAccess('Rawat Inap', 'edit') || auth()->user()->hasMenuAccess('Rawat Inap', 'hapus'))
              <td class="px-6 py-4 text-right space-x-1">
                @if($ri->status === 'Aktif' && auth()->user()->hasMenuAccess('Rawat Inap  ', 'edit'))
                  <button onclick="openCheckoutModal({{ $ri->id }}, '{{ $ri->pasien->nama }}')"
                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition shadow-sm">
                    <i class="fas fa-sign-out-alt mr-1"></i> Pulang
                </button>
                @endif
                @if($ri->billing)
                  <a href="{{ route('admin.billing.show', $ri->billing->id) }}"
                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition shadow-sm border border-gray-200">
                    <i class="fas fa-receipt"></i> Tagihan
                  </a>
                @endif
              </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-procedures text-4xl mb-3 text-gray-300 block"></i>
                Belum ada data pasien rawat inap.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination -->
    @if($rawat_inaps->hasPages())
      <div class="px-6 py-4 border-t border-gray-200">
        {{ $rawat_inaps->links() }}
      </div>
    @endif
  </div>

  <!-- Check-in Modal Removed -->

  <!-- Modal Check-Out -->
  <div id="checkOutModal" class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden fade-in-up">
      <div class="px-6 py-4 border-b border-amber-100 flex justify-between items-center bg-amber-50">
        <h3 class="font-bold text-lg text-amber-900">Check-Out Pasien</h3>
        <button onclick="closeModal('checkOutModal')" class="text-gray-400 hover:text-red-500 transition">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <form id="checkOutForm" method="POST">
        @csrf
        <div class="p-6 space-y-4">
          <div class="bg-amber-50 text-amber-800 p-3 rounded-lg text-sm mb-4 border border-amber-200">
            Pasien: <span id="co_nama_pasien" class="font-bold"></span>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal & Jam Keluar</label>
            <input type="datetime-local" name="tgl_keluar" value="{{ now()->format('Y-m-d\TH:i') }}" required
              class="w-full border-gray-300 rounded-xl focus:ring-amber-500 focus:border-amber-500 text-sm">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Kepulangan (Opsional)</label>
            <textarea name="catatan_keluar" rows="2"
              class="w-full border-gray-300 rounded-xl focus:ring-amber-500 focus:border-amber-500 text-sm"
              placeholder="Misal: Sembuh, Rujuk ke RSUD..."></textarea>
          </div>
          <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle"></i> Biaya rawat inap akan otomatis
            dikalkulasi ke tagihan kasir setelah proses check-out.</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
          <button type="button" onclick="closeModal('checkOutModal')"
            class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-xl font-medium transition text-sm">Batal</button>
          <button type="submit"
            class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-medium transition shadow-sm text-sm">Selesaikan
            Perawatan</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    const kamarsTersedia = @json($kamarsTersedia->filter(function($k) { return $k->terisi < $k->kapasitas; })->values());

    document.querySelectorAll('.pilih-kelas').forEach(select => {
      select.addEventListener('change', function() {
        const targetId = this.getAttribute('data-target');
        const targetSelect = document.getElementById(targetId);
        const kelas = this.value;
        
        targetSelect.innerHTML = '<option value="">-- Pilih Kamar --</option>';
        
        if (kelas) {
          const filteredKamars = kamarsTersedia.filter(k => k.kelas === kelas);
          filteredKamars.forEach(k => {
            const option = document.createElement('option');
            option.value = k.id;
            option.text = `${k.nama_kamar} - Rp ${parseInt(k.tarif_per_malam).toLocaleString('id-ID')}/malam`;
            targetSelect.appendChild(option);
          });
          
          if (filteredKamars.length > 0) {
            targetSelect.value = filteredKamars[0].id;
          }
        }
      });
    });

    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
      document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
      document.getElementById(id).classList.remove('flex');
    }

    function openCheckoutModal(id, namaPasien) {
      document.getElementById('co_nama_pasien').innerText = namaPasien;
      document.getElementById('checkOutForm').action = `/admin/rawat_inap/${id}/checkout`;
      openModal('checkOutModal');
    }
  </script>
@endpush