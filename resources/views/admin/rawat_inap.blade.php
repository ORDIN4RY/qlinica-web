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

    <!-- Check-in Button -->
    @if(auth()->user()->hasMenuAccess('Rawat Inap', 'tambah'))
      <button onclick="openModal('checkInModal')"
        class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm flex items-center justify-center gap-2 text-sm">
        <i class="fas fa-procedures"></i> Pasien Masuk (Check-In)
      </button>
    @endif
  </div>

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

  <!-- Modal Check-In -->
  <div id="checkInModal" class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden fade-in-up">
      <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-lg text-gray-800">Pasien Masuk Rawat Inap (Check-In)</h3>
        <button onclick="closeModal('checkInModal')" class="text-gray-400 hover:text-red-500 transition">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <form action="{{ route('admin.rawat_inap.store') }}" method="POST">
        @csrf
        <div class="p-6 space-y-5">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Pasien</label>
              <select name="pasien_id" required
                class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">-- Pilih Pasien --</option>
                @foreach($pasiens as $p)
                  <option value="{{ $p->id }}" {{ in_array($p->id, $rekomendasiIds) ? 'class=bg-yellow-100 font-bold text-yellow-800' : '' }}>
                    {{ $p->no_rm }} - {{ $p->nama }}
                    {{ in_array($p->id, $rekomendasiIds) ? ' ⭐ (Rekomendasi Mondok)' : '' }}
                  </option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Kamar / Bed</label>
              <select name="kamar_id" required
                class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">-- Pilih Kamar Tersedia --</option>
                @foreach($kamarsTersedia as $k)
                  <option value="{{ $k->id }}">{{ $k->nama_kamar }} ({{ $k->kelas }}) - Rp
                    {{ number_format($k->tarif_per_malam, 0, ',', '.') }}/malam</option>
                @endforeach
              </select>
              @if($kamarsTersedia->isEmpty())
                <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-triangle"></i> Tidak ada kamar tersedia.
                </p>
              @endif
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Dokter Penanggung Jawab (DPJP)</label>
              <select name="dokter_id" required
                class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                @foreach($dokters as $d)
                  <option value="{{ $d->id }}">dr. {{ $d->nama }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal & Jam Masuk</label>
              <input type="datetime-local" name="tgl_masuk" value="{{ now()->format('Y-m-d\TH:i') }}" required
                class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
          </div>

          <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
            <h4 class="font-bold text-sm text-blue-900 border-b border-blue-200 pb-2">Penjamin Pembayaran</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Penjamin</label>
                <select name="jenis_penjamin" id="jenis_penjamin" onchange="toggleSep()" required
                  class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                  <option value="Umum">Umum / Pribadi</option>
                  <option value="BPJS KESEHATAN">BPJS Kesehatan (INA-CBG)</option>
                </select>
              </div>
              <div id="no_sep_container" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. SEP BPJS</label>
                <input type="text" name="no_sep" id="no_sep" placeholder="Wajib untuk klaim"
                  class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
          <button type="button" onclick="closeModal('checkInModal')"
            class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-xl font-medium transition text-sm">Batal</button>
          <button type="submit"
            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition shadow-sm text-sm"
            @if($kamarsTersedia->isEmpty()) disabled @endif>Proses Check-In</button>
        </div>
      </form>
    </div>
  </div>

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
    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
      document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
      document.getElementById(id).classList.remove('flex');
    }

    function toggleSep() {
      const penjamin = document.getElementById('jenis_penjamin').value;
      const sepContainer = document.getElementById('no_sep_container');
      const sepInput = document.getElementById('no_sep');
      if (penjamin === 'BPJS KESEHATAN') {
        sepContainer.classList.remove('hidden');
        sepInput.setAttribute('required', 'required');
      } else {
        sepContainer.classList.add('hidden');
        sepInput.removeAttribute('required');
      }
    }

    function openCheckoutModal(id, namaPasien) {
      document.getElementById('co_nama_pasien').innerText = namaPasien;
      document.getElementById('checkOutForm').action = `/admin/rawat_inap/${id}/checkout`;
      openModal('checkOutModal');
    }
  </script>
@endpush