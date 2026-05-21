@extends('layouts.app')

@section('title', 'Resep & Farmasi')
@section('page-title', 'Daftar Resep & Pelayanan Farmasi')
@section('page-subtitle', 'Kelola resep, proses dispensing obat, dan cetak etiket sesuai SOP Kefarmasian')

@push('styles')
<style>
  .modal-overlay { background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); transition: all 0.3s ease; }
</style>
@endpush

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
  <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6">
    <div>
      <h2 class="font-bold text-lg text-gray-800">Pelayanan Resep Pasien</h2>
      <p class="text-sm text-gray-500">Proses penyusunan, skrining farmasetik, dan penyerahan obat kepada pasien.</p>
    </div>
    <form method="GET" action="{{ route('apoteker.resep') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
      <input name="search" value="{{ old('search', $search) }}" type="text" placeholder="Cari pasien / no resep..." class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-600 text-sm">
      <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:border-green-600 text-sm">
        @foreach(['Semua','Menunggu','Menunggu Pembayaran','Sudah Dibayar','Selesai','Dibatalkan'] as $option)
          <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
        @endforeach
      </select>
      <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition text-sm font-semibold">Filter</button>
    </form>
  </div>

  @if($resepList->isEmpty())
    <div class="p-12 text-center rounded-2xl bg-yellow-50/50 border border-yellow-100 text-yellow-900">
      <i class="fas fa-prescription-bottle-alt text-4xl text-yellow-500/60 mb-3"></i>
      <p class="font-bold">Resep Tidak Ditemukan</p>
      <p class="text-xs text-yellow-700/80 mt-1">Tidak ada daftar antrian resep pasien yang sesuai dengan pencarian Anda.</p>
    </div>
  @else
    <div class="space-y-6">
      @foreach($resepList as $resep)
        @php
          $statusClasses = [
            'Menunggu' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'Menunggu Pembayaran' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Sudah Dibayar' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Selesai' => 'bg-green-100 text-green-800 border-green-200',
            'Dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
          ];
        @endphp

        <div class="border border-gray-100 rounded-2xl p-5 bg-white shadow-sm hover:shadow-md transition duration-300">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
            <div class="min-w-0">
              <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <span class="text-green-600">#RES-{{ $resep->id }}</span>
                <span class="text-gray-300">|</span>
                <span>{{ $resep->rekamMedis?->pasien?->nama ?? 'Pasien' }}</span>
              </h3>
              <p class="text-xs text-gray-400 mt-1">
                Tanggal: <strong>{{ optional($resep->rekamMedis?->tanggal_periksa)?->isoFormat('D MMMM YYYY') ?? '-' }}</strong> 
                <span class="mx-2">•</span> 
                Dokter Pemeriksa: <strong>{{ $resep->dokter?->nama ?? '-' }}</strong>
              </p>
            </div>
            <span class="px-3 py-1 rounded-full font-bold text-xs border {{ $statusClasses[$resep->status] ?? 'bg-gray-100 text-gray-800' }}">
              {{ $resep->status }}
            </span>
          </div>

          <div class="grid gap-6 md:grid-cols-2 mb-5">
            {{-- DAFTAR OBAT --}}
            <div class="bg-gray-50/50 rounded-2xl p-4 border border-gray-100">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Item Resep Obat:</p>
              <ul class="space-y-3">
                @foreach($resep->details as $detail)
                  <li class="flex items-start justify-between text-sm border-b border-dashed border-gray-200/60 pb-2 last:border-0 last:pb-0">
                    <div class="space-y-0.5">
                      <span class="font-semibold text-gray-800">{{ $detail->obat?->nama ?? 'Obat' }}</span>
                      <span class="block text-xs text-gray-400 font-mono">Dosis: {{ $detail->dosis ?? 'Sesuai Dosis' }}</span>
                      <span class="block text-xs text-green-600 font-medium bg-green-50 px-2 py-0.5 rounded-md w-fit mt-1"><i class="fas fa-hand-holding-medical mr-1 text-[10px]"></i>{{ $detail->aturan_pakai ?? 'Sesuai Aturan Pakai' }}</span>
                    </div>
                    <div class="text-right">
                      <span class="font-bold text-gray-900 font-mono">x{{ $detail->jumlah }}</span>
                      <span class="block text-[10px] text-gray-400 uppercase">{{ $detail->obat?->satuan ?: 'Pcs' }}</span>
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>

            {{-- CATATAN & TIMELINE --}}
            <div class="space-y-3 text-sm text-gray-700">
              @if($resep->catatan_dokter)
                <div class="p-3.5 bg-indigo-50/30 rounded-xl border border-indigo-100/50">
                  <p class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1 flex items-center gap-1.5"><i class="fas fa-comment-medical text-indigo-600"></i> Catatan Dokter</p>
                  <p class="text-xs text-indigo-800 leading-normal">{{ $resep->catatan_dokter }}</p>
                </div>
              @endif

              @if($resep->catatan_apoteker)
                <div class="p-3.5 bg-green-50/30 rounded-xl border border-green-100/50">
                  <p class="text-xs font-bold text-green-900 uppercase tracking-wider mb-1 flex items-center gap-1.5"><i class="fas fa-user-shield text-green-600"></i> Catatan Pelayanan Apoteker</p>
                  <p class="text-xs text-green-800 leading-normal">{{ $resep->catatan_apoteker }}</p>
                </div>
              @endif

              <div class="text-[10px] text-gray-400 space-y-1 bg-gray-50 p-3 rounded-xl border border-gray-100/80">
                <p class="flex items-center justify-between"><span><i class="fas fa-plus-circle mr-1"></i> Resep Dibuat:</span> <strong class="text-gray-500">{{ $resep->created_at->format('d/m/Y H:i') }}</strong></p>
                <p class="flex items-center justify-between"><span><i class="fas fa-play-circle mr-1"></i> Mulai Diproses:</span> <strong class="text-gray-500">{{ $resep->diproses_at ? $resep->diproses_at->format('d/m/Y H:i') : '-' }}</strong></p>
                <p class="flex items-center justify-between"><span><i class="fas fa-check-circle mr-1"></i> Selesai Diserahkan:</span> <strong class="text-gray-500">{{ $resep->selesai_at ? $resep->selesai_at->format('d/m/Y H:i') : '-' }}</strong></p>
              </div>
            </div>
          </div>

          {{-- ACTIONS BUTTONS --}}
          <div class="flex flex-wrap gap-2.5 border-t border-gray-100 pt-4">
            
            @if($resep->status === 'Menunggu')
              
              {{-- TRIGGER SKRINING MODAL --}}
              <button type="button" onclick="openSkriningModal('{{ $resep->id }}')" class="px-5 py-2.5 bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-stethoscope"></i> Lakukan Skrining & Proses Resep
              </button>
              
              <form action="{{ route('apoteker.resep.update', $resep) }}" method="POST" class="inline-block" id="form-kembalikan-{{ $resep->id }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="kembalikan">
                <input type="hidden" name="catatan_apoteker" id="catatan-kembalikan-{{ $resep->id }}">
                <button type="button" onclick="confirmKembalikan('{{ $resep->id }}')" class="px-5 py-2.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl hover:bg-red-100 transition">
                  Kembalikan Resep
                </button>
              </form>

            @elseif($resep->status === 'Menunggu Pembayaran')
              
              <span class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-xl font-bold">
                <i class="fas fa-clock animate-pulse text-amber-500"></i> Menunggu Kasir (Belum Bayar)
              </span>

              <form action="{{ route('apoteker.resep.update', $resep) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <input type="hidden" name="action" value="kembalikan">
                <button type="submit" class="px-4 py-2.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl hover:bg-red-100 transition">
                  Batalkan & Tarik Tagihan
                </button>
              </form>

            @elseif($resep->status === 'Sudah Dibayar')
              
              {{-- TRIGGER HANDOVER MODAL --}}
              <button type="button" onclick="openHandoverModal('{{ $resep->id }}', '{{ addslashes($resep->rekamMedis?->pasien?->nama) }}', '{{ $resep->rekamMedis?->pasien?->no_rm }}')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
                <i class="fas fa-check-double"></i> Verifikasi & Serahkan Obat (SOP 5 Benar)
              </button>

              {{-- CETAK ETIKET --}}
              <button type="button" onclick="printEtiket('{{ $resep->id }}', '{{ addslashes($resep->rekamMedis?->pasien?->nama) }}', '{{ $resep->rekamMedis?->pasien?->no_rm }}', '{{ optional($resep->rekamMedis?->tanggal_periksa)->format('d/m/Y') }}', {{ json_encode($resep->details->map(function($d) { return ['nama' => $d->obat?->nama, 'kategori' => $d->obat?->kategori, 'jumlah' => $d->jumlah, 'dosis' => $d->dosis, 'aturan_pakai' => $d->aturan_pakai]; })) }})" class="px-5 py-2.5 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold rounded-xl hover:bg-blue-100 transition flex items-center gap-1.5">
                <i class="fas fa-print"></i> Cetak Etiket Obat
              </button>

            @elseif($resep->status === 'Selesai')
              
              <button type="button" class="px-4 py-2.5 bg-gray-100 text-gray-400 text-xs rounded-xl border border-gray-200 font-semibold cursor-not-allowed" disabled>
                <i class="fas fa-handshake mr-1"></i> Obat Telah Diserahkan
              </button>

              {{-- CETAK ETIKET ULANG --}}
              <button type="button" onclick="printEtiket('{{ $resep->id }}', '{{ addslashes($resep->rekamMedis?->pasien?->nama) }}', '{{ $resep->rekamMedis?->pasien?->no_rm }}', '{{ optional($resep->rekamMedis?->tanggal_periksa)->format('d/m/Y') }}', {{ json_encode($resep->details->map(function($d) { return ['nama' => $d->obat?->nama, 'kategori' => $d->obat?->kategori, 'jumlah' => $d->jumlah, 'dosis' => $d->dosis, 'aturan_pakai' => $d->aturan_pakai]; })) }})" class="px-5 py-2.5 bg-gray-50 border border-gray-200 text-gray-600 text-xs font-semibold rounded-xl hover:bg-gray-100 transition flex items-center gap-1.5">
                <i class="fas fa-print"></i> Cetak Ulang Etiket
              </button>

            @else
              <span class="px-4 py-2 bg-gray-100 text-gray-700 text-xs rounded-xl border font-semibold">Resep {{ $resep->status }}</span>
            @endif

          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

{{-- ========================================================
     MODAL 1: SKRINING KEFARMASIAN (SOAP / ACCREDITATION)
     ======================================================== --}}
<div id="skriningModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
  <div class="fixed inset-0 modal-overlay bg-slate-900/60" onclick="closeSkriningModal()"></div>
  <div class="bg-white rounded-2xl max-w-lg w-full mx-4 shadow-xl border border-slate-100 z-10 overflow-hidden">
    <div class="px-6 py-4 bg-blue-900 text-white flex items-center justify-between">
      <h3 class="font-bold text-sm uppercase tracking-wider flex items-center gap-2"><i class="fas fa-stethoscope"></i> Skrining Pelayanan Farmasi</h3>
      <button onclick="closeSkriningModal()" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
    <form id="skriningForm" method="POST" action="">
      @csrf
      @method('PATCH')
      <input type="hidden" name="action" value="proses">
      
      <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
        <p class="text-xs text-gray-500 leading-normal">Berdasarkan Peraturan Menteri Kesehatan RI (SOP Akreditasi), Apoteker wajib melakukan skrining administratif, farmasetik, dan klinis sebelum memproses resep:</p>
        
        <div class="space-y-2.5 border-t border-gray-100 pt-3">
          <label class="flex items-start gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="mt-0.5 rounded border-gray-300 text-blue-900 focus:ring-blue-900/30">
            <span><strong>Skrining Administratif:</strong> Nama dokter, paraf dokter, identitas pasien (Nama, No RM, Umur), dan tanggal resep lengkap & sesuai.</span>
          </label>

          <label class="flex items-start gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="mt-0.5 rounded border-gray-300 text-blue-900 focus:ring-blue-900/30">
            <span><strong>Skrining Farmasetik:</strong> Bentuk sediaan, kekuatan dosis, stabilitas obat, dan aturan pakai obat jelas serta tidak menimbulkan interpretasi ganda.</span>
          </label>

          <label class="flex items-start gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="mt-0.5 rounded border-gray-300 text-blue-900 focus:ring-blue-900/30">
            <span><strong>Skrining Klinis:</strong> Dosis obat tepat, tidak ada duplikasi obat, tidak ada alergi/kontraindikasi klinis yang berbahaya bagi pasien.</span>
          </label>
        </div>

        <div class="space-y-1.5 pt-2 border-t border-gray-100">
          <label class="block text-xs font-bold text-gray-500 uppercase">Catatan / Rekomendasi Apoteker</label>
          <textarea name="catatan_apoteker" placeholder="Masukkan instruksi khusus atau catatan peracikan jika ada..." class="w-full px-4 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-green-600 h-20 resize-none"></textarea>
        </div>
      </div>

      <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
        <button type="button" onclick="closeSkriningModal()" class="px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition">Batal</button>
        <button type="submit" class="px-5 py-2 text-xs font-bold bg-blue-900 text-white rounded-xl hover:bg-blue-800 transition shadow-md">Setujui & Kirim ke Kasir</button>
      </div>
    </form>
  </div>
</div>

{{-- ========================================================
     MODAL 2: 5 BENAR PENYERAHAN OBAT (CLINICAL PATIENT SAFETY)
     ======================================================== --}}
<div id="handoverModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
  <div class="fixed inset-0 modal-overlay bg-slate-900/60" onclick="closeHandoverModal()"></div>
  <div class="bg-white rounded-2xl max-w-lg w-full mx-4 shadow-xl border border-slate-100 z-10 overflow-hidden">
    <div class="px-6 py-4 bg-emerald-600 text-white flex items-center justify-between">
      <h3 class="font-bold text-sm uppercase tracking-wider flex items-center gap-2"><i class="fas fa-check-circle"></i> Verifikasi Penyerahan (SOP 5 Benar)</h3>
      <button onclick="closeHandoverModal()" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
    </div>
    <form id="handoverForm" method="POST" action="">
      @csrf
      @method('PATCH')
      <input type="hidden" name="action" value="selesai">
      
      <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
        <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
          <p class="text-xs text-emerald-800 flex items-center gap-1.5"><i class="fas fa-user-circle"></i> Pasien: <strong id="handover-pasien-nama"></strong> (<strong id="handover-pasien-rm"></strong>)</p>
        </div>
        <p class="text-xs text-gray-500 leading-normal">Untuk menjamin keselamatan pasien (*patient safety*), Apoteker wajib mencentang validasi **5 Benar Penyerahan Obat** bersama pasien secara langsung:</p>
        
        <div class="space-y-2 border-t border-gray-100 pt-3">
          <label class="flex items-center gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/30">
            <span><strong>1. Benar Pasien:</strong> Nama dan No RM dicocokkan dengan identitas pasien.</span>
          </label>

          <label class="flex items-center gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/30">
            <span><strong>2. Benar Obat:</strong> Nama obat yang diserahkan sesuai dengan resep dokter.</span>
          </label>

          <label class="flex items-center gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/30">
            <span><strong>3. Benar Dosis:</strong> Dosis obat dicocokkan kembali agar tidak berlebih/kurang.</span>
          </label>

          <label class="flex items-center gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/30">
            <span><strong>4. Benar Rute Pemberian:</strong> Cara pakai obat (minum, salep, tetes) dijelaskan secara jelas.</span>
          </label>

          <label class="flex items-center gap-2.5 text-xs text-gray-700 leading-normal cursor-pointer">
            <input type="checkbox" required class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/30">
            <span><strong>5. Benar Waktu Konsumsi:</strong> Frekuensi konsumsi obat (sebelum/sesudah makan, dll) dijelaskan.</span>
          </label>
        </div>

        <div class="space-y-1.5 pt-2 border-t border-gray-100">
          <label class="block text-xs font-bold text-gray-500 uppercase">Catatan Konseling Obat (Optional)</label>
          <textarea name="catatan_apoteker" placeholder="Tambahkan catatan khusus untuk pasien, misalnya: 'Simpan di lemari es', 'Kocok dahulu', dsb..." class="w-full px-4 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-green-600 h-20 resize-none"></textarea>
        </div>
      </div>

      <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
        <button type="button" onclick="closeHandoverModal()" class="px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition">Batal</button>
        <button type="submit" class="px-5 py-2 text-xs font-bold bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-md">Verifikasi & Serahkan Obat</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // ──── Skrining Modal ────
  function openSkriningModal(resepId) {
    const modal = document.getElementById('skriningModal');
    const form = document.getElementById('skriningForm');
    form.action = `/apoteker/resep/${resepId}`;
    modal.classList.remove('hidden');
  }

  function closeSkriningModal() {
    const modal = document.getElementById('skriningModal');
    modal.classList.add('hidden');
  }

  // ──── Handover Modal ────
  function openHandoverModal(resepId, pasienNama, pasienRm) {
    const modal = document.getElementById('handoverModal');
    const form = document.getElementById('handoverForm');
    form.action = `/apoteker/resep/${resepId}`;
    
    document.getElementById('handover-pasien-nama').innerText = pasienNama;
    document.getElementById('handover-pasien-rm').innerText = pasienRm;
    
    modal.classList.remove('hidden');
  }

  function closeHandoverModal() {
    const modal = document.getElementById('handoverModal');
    modal.classList.add('hidden');
  }

  // ──── Confirm Kembalikan ────
  function confirmKembalikan(resepId) {
    const note = prompt('Masukkan alasan pengembalian resep kepada Dokter:');
    if (note !== null) {
      document.getElementById(`catatan-kembalikan-${resepId}`).value = note;
      document.getElementById(`form-kembalikan-${resepId}`).submit();
    }
  }

  // ──── PRINT ETIKET OBAT ENGINE (CLINIC ACCREDITED STANDARD) ────
  function printEtiket(resepId, pasienNama, pasienRm, tglResep, arrayObat) {
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    let labelsHtml = '';

    arrayObat.forEach((obat, idx) => {
      // Auto-detect etiket color based on category
      const externalCats = ['salep', 'krim', 'tetes', 'gel', 'obat luar', 'spray', 'inhaler', 'tetes telinga', 'tetes mata', 'salep mata'];
      const katLower = (obat.kategori || '').toLowerCase();
      
      let isExternal = false;
      externalCats.forEach(c => {
        if (katLower.includes(c)) isExternal = true;
      });

      const labelColorClass = isExternal ? 'external-label' : 'internal-label';
      const labelTextType = isExternal ? 'OBAT LUAR - TIDAK BOLEH DIMINUM' : 'OBAT DALAM (DIMINUM)';

      labelsHtml += `
        <div class="etiket-card ${labelColorClass}">
          <div class="header">
            <h3>QLINICA</h3>
            <p>Jl. Ahmad Yani No. 23 • Telp: (0361) 123456</p>
          </div>
          <div class="divider"></div>
          <table class="patient-info">
            <tr>
              <td>No. Resep:</td>
              <td><strong>#RES-${resepId}</strong></td>
              <td>Tgl:</td>
              <td><strong>${tglResep}</strong></td>
            </tr>
            <tr>
              <td>Nama Pasien:</td>
              <td colspan="3" class="patient-name"><strong>${pasienNama}</strong> (${pasienRm})</td>
            </tr>
          </table>
          <div class="divider"></div>
          <div class="obat-body">
            <h4 class="obat-name">${obat.nama} <span class="obat-qty">(${obat.jumlah} ${obat.kategori || 'Pcs'})</span></h4>
            <div class="aturan-box">
              <h2>${obat.aturan_pakai || 'Sesuai Aturan'}</h2>
              <h3>${obat.dosis || '1 Porsi'}</h3>
            </div>
          </div>
          <div class="footer-msg">
            <p class="type-text">${labelTextType}</p>
            <p class="wish">SEMOGA LEKAS SEMBUH</p>
          </div>
        </div>
      `;
    });

    const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Cetak Etiket Obat - #RES-${resepId}</title>
        <style>
          @page {
            size: 80mm 50mm;
            margin: 0;
          }
          body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
          }
          .etiket-card {
            width: 78mm;
            height: 48mm;
            padding: 1.5mm;
            box-sizing: border-box;
            border: 1mm solid #000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            margin: 0.5mm auto;
          }
          
          /* Color borders for clinical standard */
          .internal-label {
            border-color: #1e3a8a; /* Deep Blue for Internal */
          }
          .external-label {
            border-color: #b91c1c; /* Deep Red/Blue for External */
          }
          
          .header {
            text-align: center;
          }
          .header h3 {
            margin: 0;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.5px;
          }
          .header p {
            margin: 1px 0 0 0;
            font-size: 7px;
            color: #475569;
          }
          .divider {
            border-bottom: 0.5px solid #000;
            margin: 1.5px 0;
          }
          .patient-info {
            width: 100%;
            font-size: 7.5px;
            border-collapse: collapse;
          }
          .patient-info td {
            padding: 0.5px 0;
            vertical-align: top;
          }
          .patient-name {
            font-size: 8.5px;
            text-transform: uppercase;
          }
          .obat-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 1px 0;
          }
          .obat-name {
            margin: 0 0 2px 0;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
          }
          .obat-qty {
            font-weight: normal;
            font-size: 8px;
          }
          .aturan-box {
            background: #f1f5f9;
            padding: 2px 4px;
            border-radius: 4px;
            width: 90%;
            border: 0.2px solid #cbd5e1;
          }
          .aturan-box h2 {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
          }
          .aturan-box h3 {
            margin: 1px 0 0 0;
            font-size: 9px;
            font-weight: bold;
            color: #334155;
          }
          .footer-msg {
            text-align: center;
          }
          .type-text {
            margin: 0;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.2px;
          }
          .internal-label .type-text {
            color: #1e3a8a;
          }
          .external-label .type-text {
            color: #b91c1c;
            background: #fee2e2;
            padding: 1px 0;
            border-radius: 2px;
          }
          .wish {
            margin: 1px 0 0 0;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #64748b;
          }
        </style>
      </head>
      <body>
        ${labelsHtml}
        <script>
          window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 500);
          }
        <\/script>
      </body>
      </html>
    `;

    printWindow.document.write(htmlContent);
    printWindow.document.close();
  }
</script>
@endpush
