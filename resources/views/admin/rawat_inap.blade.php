@extends('layouts.app')

@section('title', 'Rawat Inap')
@section('page-title', 'Admisi Rawat Inap')
@section('page-subtitle', 'Kelola pendaftaran mondok pasien dan pengalokasian kamar')

@push('styles')
<style>
  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.65);
    z-index:999; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:#fff; border-radius:20px; width:100%; max-width:480px;
    box-shadow:0 32px 80px rgba(15,23,42,.22);
    animation:modalIn .22s cubic-bezier(.4,0,.2,1); margin:16px; overflow:hidden; }
  @keyframes modalIn { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:none} }
  .modal-head { padding:22px 28px 18px; border-bottom:1px solid #fef3c7;
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
    border-color:#d97706; box-shadow:0 0 0 3px rgba(217,119,6,.1); }
  .modal-box-resep { max-width: 800px !important; }

  /* ── AKSI DROPDOWN ── */
  .aksi-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; font-size:12px; font-weight:700;
    border:1.5px solid #e5e7eb; border-radius:10px;
    background:#fff; color:#374151; cursor:pointer;
    transition:all .15s; user-select:none; white-space:nowrap;
  }
  .aksi-btn:hover { background:#f8fafc; border-color:#d1d5db; box-shadow:0 2px 8px rgba(0,0,0,.07); }
  .aksi-btn i { font-size:9px; transition:transform .15s; }
  .aksi-btn.is-open i { transform:rotate(180deg); }

  .aksi-menu {
    position:fixed; background:#fff;
    border:1px solid #e5e7eb; border-radius:12px;
    box-shadow:0 12px 36px rgba(15,23,42,.15);
    z-index:9999; min-width:175px; overflow:hidden;
    display:none;
  }
  .aksi-menu.open { display:block; animation:aksiIn .12s ease; }
  @keyframes aksiIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
  .aksi-menu-item {
    display:flex; align-items:center; gap:10px;
    padding:10px 16px; font-size:13px; font-weight:600;
    color:#374151; cursor:pointer; transition:background .1s;
    border:none; background:none; width:100%; text-align:left; text-decoration:none;
  }
  .aksi-menu-item:hover { background:#f8fafc; }
  .aksi-menu-item i { font-size:11px; width:14px; text-align:center; flex-shrink:0; }
  .aksi-menu-sep { height:1px; background:#f0f4f8; margin:4px 0; }
</style>
@endpush

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
      <a href="{{ route('admin.rawat_inap', ['status' => 'Antrian']) }}"
        class="px-4 py-2 text-sm font-medium rounded-xl border transition {{ request('status') == 'Antrian' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }} flex items-center gap-2">
        <span>Antrian Masuk</span>
        @if($rekomendasiData->count() > 0)
          <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
            {{ $rekomendasiData->count() }}
          </span>
        @endif
      </a>
    </div>

    <!-- Antrian rawat inap is now inline -->
  </div>

  @if(request('status') == 'Antrian')
  <div class="bg-white rounded-xl shadow-sm border border-emerald-100 overflow-hidden">
    <div class="bg-emerald-50/60 px-6 py-4 border-b border-emerald-100 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="fas fa-clipboard-list text-emerald-600 text-lg"></i>
        <h3 class="font-bold text-emerald-800">Antrian Masuk Rawat Inap (Rekomendasi Dokter)</h3>
      </div>
      <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">{{ $rekomendasiData->count() }} Permintaan</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left min-w-max whitespace-nowrap">
        <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
          <tr>
            <th class="px-6 py-4">Pasien</th>
            <th class="px-6 py-4">Rekomendasi Oleh</th>
            <th class="px-6 py-4">Penjamin</th>
            <th class="px-6 py-4">Kelas</th>
            <th class="px-6 py-4">Pilih Kamar</th>
            @if(auth()->user()->hasMenuAccess('Rawat Inap', 'tambah'))
              <th class="px-6 py-4 text-center">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($rekomendasiData as $rm)
            <tr class="hover:bg-emerald-50/10 transition duration-150">
              <td class="px-6 py-4">
                <span class="block font-bold text-gray-800">{{ $rm->pasien->nama }}</span>
                <span class="text-xs font-mono text-gray-500">RM: {{ $rm->pasien->no_rm }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="block font-semibold text-gray-700">dr. {{ $rm->dokter->nama }}</span>
                <span class="text-xs text-gray-500">Periksa: {{ \Carbon\Carbon::parse($rm->tanggal_periksa)->format('d M Y') }}</span>
              </td>
              @if(auth()->user()->hasMenuAccess('Rawat Inap', 'tambah'))
              <td class="px-6 py-4">
                <form action="{{ route('admin.rawat_inap.store') }}" method="POST" class="contents">
                  @csrf
                  <input type="hidden" name="pasien_id" value="{{ $rm->pasien_id }}">
                  <input type="hidden" name="dokter_id" value="{{ $rm->dokter_id }}">
                  <input type="hidden" name="tgl_masuk" value="{{ now()->format('Y-m-d\TH:i') }}">

                  <div class="w-44">
                    <select name="jenis_penjamin" id="penjamin_{{ $rm->pasien_id }}" onchange="onPenjaminChange(this, {{ $rm->pasien_id }})" class="form-select text-xs py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500/20" required>
                      <option value="Umum">Umum (Mandiri)</option>
                      <option value="BPJS KESEHATAN">BPJS KESEHATAN</option>
                    </select>
                  </div>
              </td>
              <td class="px-6 py-4">
                  <div class="w-36">
                    <select class="pilih-kelas form-select text-xs py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500/20" data-target="kamar_select_{{ $rm->pasien_id }}" required>
                      <option value="">-- Pilih Kelas --</option>
                      @php
                          $availableClasses = $kamarsTersedia->filter(function($k) { return $k->terisi < $k->kapasitas; })->pluck('kelas')->unique();
                      @endphp
                      @foreach($availableClasses as $kelas)
                        <option value="{{ $kelas }}">{{ $kelas }}</option>
                      @endforeach
                    </select>
                  </div>
              </td>
              <td class="px-6 py-4">
                  <div class="w-56">
                    <select name="kamar_id" id="kamar_select_{{ $rm->pasien_id }}" class="form-select text-xs py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500/20" required>
                      <option value="">-- Pilih Kamar --</option>
                      <!-- Options populated by JS -->
                    </select>
                  </div>
              </td>
              <td class="px-6 py-4 text-center">
                  <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-md transition-all duration-200">
                    <i class="fas fa-check"></i> Accept
                  </button>
                </form>
              </td>
              @else
              <td colspan="4" class="px-6 py-4 text-gray-500 text-xs italic">
                Anda tidak memiliki akses untuk memproses admisi.
              </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-clipboard-list text-4xl mb-3 text-emerald-100 block"></i>
                Tidak ada antrian masuk rawat inap saat ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @else
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
                @if($ri->reseps->isNotEmpty())
                  <div class="mt-1.5 flex flex-wrap gap-1 max-w-[200px]">
                    @foreach($ri->reseps as $resep)
                      @php
                        $badgeColor = match($resep->status) {
                          'Menunggu' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                          'Diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                          'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                          'Dibatalkan' => 'bg-red-50 text-red-750 border-red-200',
                          default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                      @endphp
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}" title="Resep Pasien">
                        #RES-{{ $resep->id }} ({{ $resep->status }})
                      </span>
                    @endforeach
                  </div>
                @endif
              </td>
              <td class="px-6 py-4 text-center">
                @php
                  $hariDirawat = max(1, (int) \Carbon\Carbon::parse($ri->tgl_masuk)->startOfDay()->diffInDays(now()->startOfDay()) + 1);
                  $isBpjsMelebihiLimit = $ri->jenis_penjamin === 'BPJS KESEHATAN' && $ri->status === 'Aktif' && $hariDirawat >= 5;
                @endphp
                @if($ri->status === 'Aktif')
                  <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold {{ $isBpjsMelebihiLimit ? '' : 'animate-pulse' }}"><i
                      class="fas fa-bed mr-1"></i> Dirawat ({{ $hariDirawat }} hari)</span>
                  @if($isBpjsMelebihiLimit)
                    <div class="mt-1.5">
                      <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 border border-red-300 rounded-lg text-[10px] font-bold">
                        <i class="fas fa-exclamation-triangle"></i>
                        Melebihi 5 hari BPJS! Pertimbangkan rujukan ke RS.
                      </span>
                    </div>
                  @endif
                @else
                  <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i
                      class="fas fa-check-circle mr-1"></i> Selesai</span><br>
                  <span class="text-[10px] text-gray-400 mt-1 block">Tgl Keluar: {{ $ri->tgl_keluar->format('d/m/Y') }}</span>
                @endif
              </td>
              @if (auth()->user()->hasMenuAccess('Rawat Inap', 'edit') || auth()->user()->hasMenuAccess('Rawat Inap', 'hapus'))
              <td class="px-6 py-4 text-right">
                @php
                  $hasAktifAction = $ri->status === 'Aktif' && auth()->user()->hasMenuAccess('Rawat Inap', 'edit');
                  $hasTagihan = (bool)$ri->billing;
                @endphp
                @if($hasAktifAction || $hasTagihan)
                <button class="aksi-btn" onclick="toggleAksiMenu(this,'aksi-{{ $ri->id }}')">
                  Aksi <i class="fas fa-chevron-down"></i>
                </button>
                <div id="aksi-{{ $ri->id }}" class="aksi-menu">
                  @if($hasAktifAction)
                    <button class="aksi-menu-item"
                      onclick="openResepModal({{ $ri->id }},'{{ addslashes($ri->pasien->nama) }}');closeAksiMenu();">
                      <i class="fas fa-pills" style="color:#059669"></i> Resep
                    </button>
                    <button class="aksi-menu-item"
                      onclick="openPindahModal({{ $ri->id }},'{{ $ri->pasien->nama }}',{{ $ri->kamar_id }},'{{ $ri->kamar->kelas }}');closeAksiMenu();">
                      <i class="fas fa-exchange-alt" style="color:#4f46e5"></i> Pindah Kamar
                    </button>
                    <button class="aksi-menu-item"
                      onclick="openCheckoutModal({{ $ri->id }},'{{ $ri->pasien->nama }}');closeAksiMenu();">
                      <i class="fas fa-sign-out-alt" style="color:#d97706"></i> Pulang
                    </button>
                  @endif
                  @if($hasTagihan)
                    @if($hasAktifAction)<div class="aksi-menu-sep"></div>@endif
                    <a href="{{ route('admin.billing.show', $ri->billing->id) }}" class="aksi-menu-item">
                      <i class="fas fa-receipt" style="color:#6b7280"></i> Tagihan
                    </a>
                  @endif
                </div>
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
  @endif

  <!-- Check-in Modal Removed -->

  <!-- Modal Check-Out -->
  <div id="checkOutModal" class="modal-overlay">
    <div class="modal-box">
      <div class="modal-head bg-amber-50/50">
        <h3 class="font-bold text-lg text-amber-900">Check-Out Pasien</h3>
        <button onclick="closeModal('checkOutModal')" class="w-9 h-9 rounded-xl bg-amber-100/50 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-amber-800">
          <i class="fas fa-times text-sm"></i>
        </button>
      </div>
      <form id="checkOutForm" method="POST">
        @csrf
        <div class="modal-body space-y-4">
          <div class="bg-amber-50 text-amber-800 p-4 rounded-xl text-sm border border-amber-200 flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 flex-shrink-0">
              <i class="fas fa-user-injured text-lg"></i>
            </div>
            <div>
              <div class="text-[10px] text-amber-500 font-bold uppercase tracking-wider">Nama Pasien</div>
              <span id="co_nama_pasien" class="font-bold text-base text-amber-900"></span>
            </div>
          </div>
          <div class="form-group">
            <label>Tanggal & Jam Keluar</label>
            <input type="datetime-local" name="tgl_keluar" value="{{ now()->format('Y-m-d\TH:i') }}" readonly class="form-input bg-gray-50 text-gray-500 cursor-not-allowed border-gray-200">
          </div>
          <div class="form-group">
            <label>Keadaan Pasien Saat Keluar</label>
            <select name="keadaan_keluar" class="form-input" required>
              <option value="">Pilih Keadaan...</option>
              <option value="Sembuh">Sembuh</option>
              <option value="Membaik">Membaik</option>
              <option value="Belum Sembuh">Belum Sembuh</option>
              <option value="Dirujuk">Dirujuk</option>
              <option value="Meninggal">Meninggal</option>
            </select>
          </div>
          <div class="form-group">
            <label>Catatan Kepulangan (Opsional)</label>
            <textarea name="catatan_keluar" rows="2" class="form-input" placeholder="Misal: Sembuh, Rujuk ke RSUD..."></textarea>
          </div>
          <div class="bg-blue-50 border border-blue-100 text-blue-800 p-3.5 rounded-xl text-xs flex gap-2.5 items-start">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 text-sm"></i>
            <p class="leading-relaxed">Biaya rawat inap akan otomatis dikalkulasi ke tagihan kasir setelah proses check-out.</p>
          </div>
        </div>
        <div class="modal-foot bg-gray-50/50">
          <button type="button" onclick="closeModal('checkOutModal')" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
            Batal
          </button>
          <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm shadow-md transition">
            Selesaikan Perawatan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Pindah Kamar -->
  <div id="pindahKamarModal" class="modal-overlay">
    <div class="modal-box">
      <div class="modal-head bg-indigo-50/50">
        <h3 class="font-bold text-lg text-indigo-900">Pindah Kamar Pasien</h3>
        <button onclick="closeModal('pindahKamarModal')" class="w-9 h-9 rounded-xl bg-indigo-100/50 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-indigo-800">
          <i class="fas fa-times text-sm"></i>
        </button>
      </div>
      <form id="pindahKamarForm" method="POST">
        @csrf
        <div class="modal-body space-y-4">
          <div class="bg-indigo-50 text-indigo-800 p-4 rounded-xl text-sm border border-indigo-200 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 flex-shrink-0">
              <i class="fas fa-user-injured text-lg"></i>
            </div>
            <div>
              <div class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">Nama Pasien</div>
              <span id="pindah_nama_pasien" class="font-bold text-base text-indigo-900"></span>
            </div>
          </div>
          
          <div class="form-group">
            <label>Tanggal & Jam Pindah</label>
            <input type="datetime-local" name="tgl_pindah" value="{{ now()->format('Y-m-d\TH:i') }}" readonly class="form-input bg-gray-50 text-gray-500 cursor-not-allowed border-gray-200">
          </div>

          <div class="form-group">
            <label>Pilih Kelas Kamar Baru <span class="text-red-500 normal-case font-normal">*</span></label>
            <select id="pindah_kelas_select" class="form-select" required>
              <option value="">-- Pilih Kelas --</option>
              @php
                  $availableClasses = $kamarsTersedia->filter(function($k) { return $k->terisi < $k->kapasitas; })->pluck('kelas')->unique();
              @endphp
              @foreach($availableClasses as $kelas)
                <option value="{{ $kelas }}">{{ $kelas }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label>Pilih Kamar Baru <span class="text-red-500 normal-case font-normal">*</span></label>
            <select name="kamar_id" id="pindah_kamar_select" class="form-select" required>
              <option value="">-- Pilih Kamar --</option>
            </select>
          </div>

          <div class="flex items-center gap-2.5 py-1">
            <input type="checkbox" name="sertakan_biaya_lama" id="sertakan_biaya_lama" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="sertakan_biaya_lama" class="text-xs font-semibold text-gray-700 select-none cursor-pointer">
              Sertakan/Tagih biaya kamar sebelumnya
            </label>
          </div>

          <div class="bg-blue-50 border border-blue-100 text-blue-800 p-3.5 rounded-xl text-xs flex gap-2.5 items-start">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 text-sm"></i>
            <p class="leading-relaxed">Jika "Sertakan/Tagih biaya kamar sebelumnya" dicentang, kamar lama tetap ditagih sesuai durasi huni (minimal 1 malam jika hari yang sama). Jika tidak dicentang, kamar lama dibebaskan (gratis) dan kamar baru akan dihitung sejak awal segmen kamar lama.</p>
          </div>
        </div>
        <div class="modal-foot bg-gray-50/50">
          <button type="button" onclick="closeModal('pindahKamarModal')" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
            Batal
          </button>
          <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md transition">
            Proses Pindah Kamar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Resep -->
  <div id="resepModal" class="modal-overlay">
    <div class="modal-box modal-box-resep">
      <div class="modal-head bg-emerald-50/50 border-b border-emerald-100">
        <h3 class="font-bold text-lg text-emerald-950 flex items-center gap-2">
          <i class="fas fa-prescription-bottle-alt text-emerald-600 text-xl"></i>
          Beri Resep Medis Pasien
        </h3>
        <button onclick="closeModal('resepModal')" type="button" class="w-9 h-9 rounded-xl bg-emerald-100/50 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition text-emerald-800">
          <i class="fas fa-times text-sm"></i>
        </button>
      </div>
      <form id="resepForm" method="POST">
        @csrf
        <div class="modal-body space-y-4 max-h-[60vh] overflow-y-auto">
          <!-- Patient Info -->
          <div class="bg-emerald-50/70 text-emerald-900 p-4 rounded-xl text-sm border border-emerald-150 flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100/80 rounded-lg flex items-center justify-center text-emerald-600 flex-shrink-0">
              <i class="fas fa-user-injured text-lg"></i>
            </div>
            <div>
              <div class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Nama Pasien</div>
              <span id="resep_nama_pasien" class="font-bold text-base text-emerald-950"></span>
            </div>
          </div>

          <!-- Catatan Resep -->
          <div class="form-group">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Catatan Resep / Instruksi Dokter</label>
            <textarea name="catatan_dokter" rows="2" class="form-input" placeholder="Contoh: Resep harian, berikan setelah makan pagi..."></textarea>
          </div>

          <!-- Dynamic Obat List -->
          <div>
            <div class="flex justify-between items-center mb-3">
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Daftar Obat</label>
              <button type="button" onclick="addObatRow()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-plus text-[10px]"></i> Tambah Obat
              </button>
            </div>

            <div class="space-y-3" id="resep-obat-rows">
              <!-- Dynamically populated rows -->
            </div>
          </div>
        </div>

        <div class="modal-foot bg-gray-50/50">
          <button type="button" onclick="closeModal('resepModal')" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition">
            Batal
          </button>
          <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md transition">
            Kirim Resep ke Apotek
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    const kamarsTersedia = @json($kamarsTersedia->filter(function($k) { return $k->terisi < $k->kapasitas; })->values());
    const KELAS_BPJS = ['Kelas 1', 'Kelas 2', 'Kelas 3']; // VIP tidak ditanggung BPJS

    /**
     * Dipanggil saat penjamin berubah di form rawat inap.
     * Jika BPJS dipilih, sembunyikan opsi kelas VIP.
     */
    function onPenjaminChange(selectEl, pasienId) {
      const isBpjs = selectEl.value === 'BPJS KESEHATAN';
      const kelasSelect = document.querySelector(`[data-target="kamar_select_${pasienId}"]`);
      if (!kelasSelect) return;

      // Tampilkan/sembunyikan VIP option
      Array.from(kelasSelect.options).forEach(opt => {
        if (opt.value === 'VIP') {
          opt.disabled = isBpjs;
          opt.style.display = isBpjs ? 'none' : '';
          opt.text = isBpjs ? 'VIP (Tidak ditanggung BPJS)' : 'VIP';
        }
      });

      // Jika VIP sedang terpilih dan BPJS dipilih, reset ke opsi pertama yang valid
      if (isBpjs && kelasSelect.value === 'VIP') {
        const firstValid = Array.from(kelasSelect.options).find(o => !o.disabled && o.value);
        if (firstValid) {
          kelasSelect.value = firstValid.value;
          kelasSelect.dispatchEvent(new Event('change'));
        }
      }
    }

    document.querySelectorAll('.pilih-kelas').forEach(select => {
      select.addEventListener('change', function() {
        const targetId = this.getAttribute('data-target');
        const targetSelect = document.getElementById(targetId);
        const kelas = this.value;

        // Cek apakah penjamin adalah BPJS
        const pasienId = targetId.replace('kamar_select_', '');
        const penjaminEl = document.getElementById('penjamin_' + pasienId);
        const isBpjs = penjaminEl && penjaminEl.value === 'BPJS KESEHATAN';
        
        targetSelect.innerHTML = '<option value="">-- Pilih Kamar --</option>';
        
        if (kelas) {
          // Jika BPJS, hanya tampilkan kamar yang bukan VIP
          const filteredKamars = kamarsTersedia.filter(k => {
            if (k.kelas !== kelas) return false;
            if (isBpjs && !KELAS_BPJS.includes(k.kelas)) return false;
            return true;
          });
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
      document.getElementById(id).classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('open');
      document.body.style.overflow = '';
    }

    // Close modal on clicking overlay
    document.getElementById('checkOutModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal('checkOutModal');
    });
    document.getElementById('pindahKamarModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal('pindahKamarModal');
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal('checkOutModal');
        closeModal('pindahKamarModal');
      }
    });

    function openCheckoutModal(id, namaPasien) {
      document.getElementById('co_nama_pasien').innerText = namaPasien;
      document.getElementById('checkOutForm').action = `/admin/rawat_inap/${id}/checkout`;
      openModal('checkOutModal');
    }

    function openPindahModal(id, namaPasien, currentKamarId, currentKelas) {
      document.getElementById('pindah_nama_pasien').innerText = namaPasien;
      document.getElementById('pindahKamarForm').action = `/admin/rawat_inap/${id}/pindah`;
      
      const kelasSelect = document.getElementById('pindah_kelas_select');
      kelasSelect.setAttribute('data-current-kamar', currentKamarId);
      kelasSelect.value = '';
      
      document.getElementById('pindah_kamar_select').innerHTML = '<option value="">-- Pilih Kamar --</option>';
      
      openModal('pindahKamarModal');
    }

    document.getElementById('pindah_kelas_select').addEventListener('change', function() {
      const targetSelect = document.getElementById('pindah_kamar_select');
      const kelas = this.value;
      
      targetSelect.innerHTML = '<option value="">-- Pilih Kamar --</option>';
      
      if (kelas) {
        const currentKamarId = parseInt(this.getAttribute('data-current-kamar') || 0);
        const filteredKamars = kamarsTersedia.filter(k => k.kelas === kelas && k.id !== currentKamarId);
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

    // ── RESEP MEDIS REPEATER ──
    let resepRowIndex = 0;
    @php
    $obatArray = $obats->map(function($o) {
      return [
        'id' => $o->id,
        'nama' => $o->nama,
        'stok' => $o->stok,
        'is_fornas' => $o->is_fornas ?? false
      ];
    })->toArray();
    @endphp
    const obatData = @json($obatArray);

    function searchObat(input) {
      const query = input.value.toLowerCase().trim();
      const dropdown = input.nextElementSibling;
      
      dropdown.innerHTML = '';
      
      if (!query) {
        dropdown.classList.add('hidden');
        const item = input.closest('.relative');
        const hiddenId = item.querySelector('.obat-id');
        if (hiddenId) hiddenId.value = '';
        return;
      }
      
      const matches = [];
      for (let i = 0; i < obatData.length; i++) {
        const item = obatData[i];
        if (item.nama.toLowerCase().includes(query)) {
          matches.push(item);
          if (matches.length >= 10) break;
        }
      }
      
      if (matches.length === 0) {
        dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada obat cocok</div>';
        dropdown.classList.remove('hidden');
        return;
      }
      
      matches.forEach(item => {
        const option = document.createElement('div');
        option.className = 'px-4 py-2 hover:bg-emerald-50 text-sm text-gray-700 cursor-pointer flex justify-between items-center';
        
        let fornasBadge = item.is_fornas ? `<span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase ml-2 border border-green-200" title="Ditanggung BPJS">Fornas</span>` : '';
        let stokBadge = `<span class="text-xs text-gray-400">Stok: ${item.stok}</span>`;
        
        option.innerHTML = `
          <div class="flex items-center"><span>${item.nama}</span>${fornasBadge}</div>
          ${stokBadge}
        `;
        
        option.onclick = function() {
          const hiddenId = input.previousElementSibling;
          if (hiddenId) hiddenId.value = item.id;
          input.value = item.nama;
          dropdown.classList.add('hidden');
        };
        dropdown.appendChild(option);
      });
      
      dropdown.classList.remove('hidden');
    }

    document.addEventListener('click', function(e) {
      if (!e.target.classList.contains('obat-search') && !e.target.closest('.obat-dropdown')) {
        document.querySelectorAll('.obat-dropdown').forEach(dropdown => {
          dropdown.classList.add('hidden');
        });
      }
    });

    function openResepModal(id, namaPasien) {
      document.getElementById('resep_nama_pasien').innerText = namaPasien;
      document.getElementById('resepForm').action = `/admin/rawat_inap/${id}/resep`;
      document.getElementById('resep-obat-rows').innerHTML = '';
      resepRowIndex = 0;
      addObatRow();
      openModal('resepModal');
    }

    function addObatRow() {
      resepRowIndex++;
      const container = document.getElementById('resep-obat-rows');
      const rowDiv = document.createElement('div');
      rowDiv.id = `row-obat-${resepRowIndex}`;
      rowDiv.className = 'grid grid-cols-1 md:grid-cols-12 gap-2.5 p-3.5 bg-gray-50/50 rounded-xl border border-gray-200 items-end relative';
      
      rowDiv.innerHTML = `
        <div class="md:col-span-4 relative">
          <label class="block text-[11px] font-bold text-gray-500 mb-1">Nama Obat</label>
          <input type="hidden" name="obat_id[]" class="obat-id" required>
          <input type="text"
                 class="form-input text-xs w-full obat-search"
                 placeholder="Ketik nama obat..."
                 oninput="searchObat(this)"
                 onfocus="searchObat(this)"
                 autocomplete="off" required>
          <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg obat-dropdown divide-y divide-gray-100">
          </div>
        </div>
        <div class="md:col-span-2">
          <label class="block text-[11px] font-bold text-gray-500 mb-1">Jumlah</label>
          <input name="jumlah[]" type="number" min="1" value="1" class="form-input text-xs" required />
        </div>
        <div class="md:col-span-2">
          <label class="block text-[11px] font-bold text-gray-500 mb-1">Dosis</label>
          <input name="dosis[]" type="text" class="form-input text-xs" placeholder="Contoh: 3x1" required />
        </div>
        <div class="md:col-span-3">
          <label class="block text-[11px] font-bold text-gray-500 mb-1">Aturan Pakai</label>
          <div class="aturan-pakai-container-ri">
            <select class="form-select text-xs aturan-pakai-select-ri" onchange="toggleAturanPakaiCustomRi(this)" name="aturan_pakai[]" required>
              <option value="Sesudah makan">Sesudah makan</option>
              <option value="Sebelum makan">Sebelum makan</option>
              <option value="Bersama makan">Bersama makan</option>
              <option value="Sebelum tidur">Sebelum tidur</option>
              <option value="custom">Lainnya (Ketik)...</option>
            </select>
            <input type="text" class="form-input text-xs aturan-pakai-custom-ri hidden mt-1" placeholder="Aturan pakai manual...">
          </div>
        </div>
        <div class="md:col-span-1 flex justify-center pb-0.5">
          <button type="button" onclick="removeObatRow(${resepRowIndex})" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition border border-red-200">
            <i class="fas fa-trash-alt text-xs"></i>
          </button>
        </div>
      `;
      container.appendChild(rowDiv);
    }

    function removeObatRow(rowId) {
      const container = document.getElementById('resep-obat-rows');
      if (container.children.length > 1) {
        const row = document.getElementById(`row-obat-${rowId}`);
        if (row) row.remove();
      } else {
        alert('Resep harus memiliki minimal 1 obat.');
      }
    }

    function toggleAturanPakaiCustomRi(select) {
      const container = select.closest('.aturan-pakai-container-ri');
      const customInput = container.querySelector('.aturan-pakai-custom-ri');
      
      if (select.value === 'custom') {
        select.removeAttribute('name');
        customInput.setAttribute('name', 'aturan_pakai[]');
        customInput.classList.remove('hidden');
        customInput.required = true;
        customInput.focus();
      } else {
        select.setAttribute('name', 'aturan_pakai[]');
        customInput.removeAttribute('name');
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
      }
    }

    // ── AKSI DROPDOWN ──
    let _aksiCloseTimer = null;
    function toggleAksiMenu(btn, menuId) {
      const menu = document.getElementById(menuId);
      const isOpen = menu.classList.contains('open');
      closeAksiMenu();
      if (!isOpen) {
        const rect       = btn.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;

        // Reset dulu agar bisa ukur tinggi sebenarnya
        menu.style.top    = '-9999px';
        menu.style.bottom = 'auto';
        menu.style.right  = (window.innerWidth - rect.right) + 'px';
        menu.style.left   = 'auto';
        menu.classList.add('open');

        // Ukur tinggi aktual setelah open
        const actualH = menu.offsetHeight;

        if (spaceBelow < actualH + 10 && spaceAbove > spaceBelow) {
          // Muncul ke ATAS
          menu.style.top    = 'auto';
          menu.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
        } else {
          // Muncul ke BAWAH (default)
          menu.style.top    = (rect.bottom + 6) + 'px';
          menu.style.bottom = 'auto';
        }

        btn.classList.add('is-open');
      }
    }
    function closeAksiMenu() {
      document.querySelectorAll('.aksi-menu.open').forEach(m => m.classList.remove('open'));
      document.querySelectorAll('.aksi-btn.is-open').forEach(b => b.classList.remove('is-open'));
    }
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.aksi-btn') && !e.target.closest('.aksi-menu')) closeAksiMenu();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeAksiMenu();
    });
    // Tutup dropdown saat scroll supaya tidak menimpa
    window.addEventListener('scroll', closeAksiMenu, { passive: true });
    document.querySelector('.overflow-x-auto')?.addEventListener('scroll', closeAksiMenu, { passive: true });
  </script>
@endpush