@extends('layouts.app')

@section('title', 'Kuitansi & Detail Billing')
@section('page-title', 'Detail Billing')
@section('page-subtitle', 'Rincian invoice pembayaran pasien')

@push('styles')
<style>
  @media print {
    /* Sembunyikan sidebar, top header, tombol aksi, dan elemen non-cetak lainnya */
    aside, header, nav, footer, .no-print, #sidebar, #sidebarToggleBtn {
      display: none !important;
    }
    body {
      background: white !important;
      color: black !important;
    }
    .print-container {
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      border: none !important;
      box-shadow: none !important;
    }
    .md\:ml-64 {
      margin-left: 0 !important;
    }
    main {
      padding: 0 !important;
    }
  }
</style>
@endpush

@section('content')
<div class="space-y-6">

  <!-- Tombol Aksi Kiri/Kembali (Sembunyikan saat Print) -->
  <div class="flex items-center justify-between no-print">
    <a href="{{ route('admin.billing') }}" class="px-4 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 transition text-sm font-semibold flex items-center gap-2">
      <i class="fas fa-arrow-left"></i> Kembali ke Antrean
    </a>
    
    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-semibold flex items-center gap-2 shadow-sm">
        <i class="fas fa-print"></i> Cetak Kuitansi (Print)
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    <!-- Bagian Invoice (Printable Card) -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm print-container p-6 md:p-8 space-y-6">
      
      <!-- Header Invoice (Kop Klinik) -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-blue-900 rounded-xl flex items-center justify-center">
            <i class="fas fa-clinic-medical text-white text-2xl"></i>
          </div>
          <div>
            <h3 class="font-bold text-xl text-blue-900">QLINICA</h3>
            <p class="text-xs text-gray-500">Jl. Ahmad Yani No. 23</p>
            <p class="text-[10px] text-gray-400">Telp: (0361) 123-4567 | www.qlinica.com</p>
          </div>
        </div>
        <div class="text-right sm:text-right">
          <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full border mb-2 {{ $billing->status === 'Lunas' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200' }}">
            {{ $billing->status }}
          </span>
          <h4 class="font-mono text-sm font-semibold text-gray-900">{{ $billing->no_invoice }}</h4>
          <p class="text-xs text-gray-500">Tanggal: {{ $billing->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
        </div>
      </div>

      <!-- Ringkasan Informasi Pasien & Dokter -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 rounded-xl p-4 text-sm text-gray-700">
        <div>
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Informasi Pasien</p>
          <p class="font-bold text-gray-900">{{ $billing->pasien?->nama ?? '-' }}</p>
          <p class="text-xs text-gray-600">No. Rekam Medis: <span class="font-mono font-semibold">{{ $billing->pasien?->no_rm ?: '–' }}</span></p>
          <p class="text-xs text-gray-600">Jenis Kelamin: {{ $billing->pasien?->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
          <p class="text-xs text-gray-600">NIK: {{ $billing->pasien?->nik ?: '–' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pemeriksaan Klinis</p>
          <p class="font-semibold text-gray-800">Dokter: {{ $billing->rekamMedis?->dokter?->nama ?? '–' }}</p>
          <p class="text-xs text-gray-600">Layanan Kesehatan: {{ $billing->rekamMedis?->pelayanan_kesehatan ?: '-' }}</p>
          <p class="text-xs text-gray-600">Jenis Pelayanan: {{ $billing->rekamMedis?->jenis_pelayanan ?: 'Umum' }}</p>
          <p class="text-xs text-gray-600">Kasus Penyakit: {{ $billing->rekamMedis?->kasus_penyakit ?: 'Baru' }}</p>
        </div>
      </div>

      <!-- Detail Layanan & Rincian Harga -->
      <div class="space-y-4">
        <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2 uppercase tracking-wide">Rincian Item Tagihan</h4>
        
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-gray-700">
            <thead>
              <tr class="border-b border-gray-200 text-gray-500 font-semibold text-left">
                <th class="py-2">Deskripsi Item</th>
                <th class="py-2 text-center">Kategori</th>
                <th class="py-2 text-center">Qty</th>
                <th class="py-2 text-right">Harga Satuan</th>
                <th class="py-2 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($billing->details as $d)
                <tr>
                  <td class="py-3 pr-2 font-medium text-gray-900">
                    {{ $d->nama_item }}
                  </td>
                  <td class="py-3 text-center text-xs">
                    @php
                      $badgeClass = 'bg-gray-50 text-gray-700';
                      if ($d->kategori === 'Registrasi') $badgeClass = 'bg-indigo-50 text-indigo-700';
                      elseif ($d->kategori === 'Tindakan') $badgeClass = 'bg-amber-50 text-amber-700';
                      elseif ($d->kategori === 'Obat') $badgeClass = 'bg-emerald-50 text-emerald-700';
                      elseif ($d->kategori === 'Kamar') $badgeClass = 'bg-blue-50 text-blue-700';
                    @endphp
                    <span class="px-2 py-0.5 rounded-full font-semibold {{ $badgeClass }}">
                      {{ $d->kategori }}
                    </span>
                  </td>
                  <td class="py-3 text-center font-mono">
                    {{ $d->jumlah }}
                  </td>
                  <td class="py-3 text-right font-mono">
                    Rp {{ number_format($d->harga_satuan, 2, ',', '.') }}
                  </td>
                  <td class="py-3 text-right font-mono font-semibold text-gray-900">
                    Rp {{ number_format($d->subtotal, 2, ',', '.') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Total & Footer Kuitansi -->
      <div class="border-t border-gray-100 pt-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center gap-4">
          <div class="text-xs text-gray-500 max-w-sm text-left">
            <p class="font-semibold text-gray-800">Catatan:</p>
            <p>Bukti pembayaran ini sah dan diterbitkan secara elektronik oleh Klinik QLINICA setelah lunas pembayaran.</p>
          </div>
          
          <div class="w-full sm:w-80 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Sub Biaya Registrasi:</span>
              <span class="font-mono">Rp {{ number_format($billing->biaya_registrasi, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Sub Biaya Tindakan:</span>
              <span class="font-mono">Rp {{ number_format($billing->biaya_tindakan, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Sub Biaya Obat-obatan:</span>
              <span class="font-mono">Rp {{ number_format($billing->biaya_obat, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Sub Biaya Kamar Inap:</span>
              <span class="font-mono">Rp {{ number_format($billing->biaya_kamar, 2, ',', '.') }}</span>
            </div>
            @if($billing->potongan_bpjs > 0)
              <div class="flex justify-between text-emerald-600 font-semibold">
                <span>Potongan BPJS ({{ $billing->no_bpjs }}):</span>
                <span class="font-mono">-Rp {{ number_format($billing->potongan_bpjs, 2, ',', '.') }}</span>
              </div>
            @endif
            <div class="flex justify-between text-lg font-bold text-gray-900 border-t border-gray-200 pt-2">
              <span>Grand Total:</span>
              <span class="font-mono text-blue-900">Rp {{ number_format($billing->grand_total, 2, ',', '.') }}</span>
            </div>

            @if($billing->status === 'Lunas')
              <div class="flex justify-between text-sm mt-2">
                <span>Jumlah Dibayar:</span>
                <span class="font-mono">Rp {{ number_format($billing->jumlah_dibayar ?? 0, 2, ',', '.') }}</span>
              </div>
              <div class="flex justify-between text-sm font-semibold">
                <span>Kembalian:</span>
                <span class="font-mono">Rp {{ number_format($billing->kembalian ?? 0, 2, ',', '.') }}</span>
              </div>
            @endif

          </div>
        </div>
      </div>

      <!-- Tanda Tangan Kasir (Hanya muncul jika sudah LUNAS) -->
      @if($billing->status === 'Lunas')
        <div class="flex justify-end pt-8">
          <div class="text-center w-64 space-y-12">
            <div class="text-xs text-gray-500">
              <p>Denpasar, {{ $billing->paid_at->isoFormat('D MMMM YYYY') }}</p>
              <p>Kasir Penerima,</p>
            </div>
            <div>
              <p class="font-bold text-gray-800 underline">{{ $billing->kasir?->nama ?? 'Apoteker/Resepsionis' }}</p>
              <p class="text-[10px] text-gray-400 font-mono">Metode: {{ $billing->metode_pembayaran }}</p>
            </div>
          </div>
        </div>
      @endif

    </div>

    <!-- Bagian Pembayaran Kasir (no-print) -->
    <div class="lg:col-span-1 bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6 no-print">
      <h3 class="font-bold text-lg text-gray-800 border-b border-gray-100 pb-3">
        <i class="fas fa-wallet text-blue-900 mr-2"></i>Pembayaran Kasir
      </h3>

      @if($billing->rawatInap && $billing->rawatInap->status === 'Aktif')
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-xl text-center shadow-sm mt-4">
            <i class="fas fa-procedures text-3xl mb-3 text-blue-500 block"></i>
            <h4 class="font-bold text-sm">Pasien Masih Dirawat</h4>
            <p class="text-xs mt-1">Sistem pembayaran terkunci sementara. Pembayaran baru dapat diproses ke Kasir setelah pasien di-Check-Out dari Rawat Inap.</p>
            <a href="{{ route('admin.rawat_inap') }}" class="inline-block mt-4 text-xs font-semibold bg-white border border-blue-300 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-100 transition">Kembali ke Rawat Inap</a>
        </div>
      @elseif($billing->status === 'Belum Bayar')
        <form id="form-pembayaran" action="{{ route('admin.billing.bayar', $billing) }}" method="POST" class="space-y-6">
          @csrf
          <input type="hidden" id="grand_total_value" value="{{ $billing->grand_total }}">
          <div class="space-y-2">
            <label class="block text-xs font-bold text-gray-500 uppercase">Metode Pembayaran</label>
            <div class="grid grid-cols-2 gap-3">
              
              <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-blue-50/50 hover:border-blue-300 transition group">
                <input type="radio" name="metode_pembayaran" value="Tunai" checked class="sr-only peer">
                <div class="peer-checked:bg-blue-900 text-gray-500 w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 transition group-hover:bg-blue-100">
                  <i class="fas fa-money-bill-wave text-sm"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700">Tunai</span>
              </label>

              <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-blue-50/50 hover:border-blue-300 transition group">
                <input type="radio" name="metode_pembayaran" value="QRIS" class="sr-only peer">
                <div class="peer-checked:bg-blue-900 text-gray-500 w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 transition group-hover:bg-blue-100">
                  <i class="fas fa-qrcode text-sm"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700">QRIS</span>
              </label>

              <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-blue-50/50 hover:border-blue-300 transition group">
                <input type="radio" name="metode_pembayaran" value="Debit" class="sr-only peer">
                <div class="peer-checked:bg-blue-900 text-gray-500 w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 transition group-hover:bg-blue-100">
                  <i class="fas fa-credit-card text-sm"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700">Debit / EDC</span>
              </label>

              <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-blue-50/50 hover:border-blue-300 transition group">
                <input type="radio" name="metode_pembayaran" value="Asuransi" class="sr-only peer">
                <div class="peer-checked:bg-blue-900 text-gray-500 w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 transition group-hover:bg-blue-100">
                  <i class="fas fa-shield-alt text-sm"></i>
                </div>
                <span class="text-xs font-semibold text-gray-700">Asuransi / BPJS</span>
              </label>

            </div>
          </div>

          <!-- Input BPJS (Ditampilkan dinamis jika Asuransi / BPJS dipilih) -->
          <div id="bpjs-input-wrapper" class="hidden border border-gray-200 rounded-xl p-4 bg-gray-50/50 space-y-3 transition">
            <label class="block text-xs font-bold text-gray-500 uppercase">Nomor Kartu BPJS (13 Digit)</label>
            <div class="flex gap-2">
              <input type="text" id="no_bpjs_input" value="{{ $billing->no_bpjs }}" placeholder="Contoh: 0001234567890" class="flex-grow px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <button type="button" id="btn-cek-bpjs" class="px-4 py-2 bg-blue-900 text-white rounded-lg text-xs font-semibold hover:bg-blue-800 transition">Cek BPJS</button>
            </div>
            <div id="bpjs-verification-status" class="text-xs hidden"></div>
          </div>

          <!-- Panel QRIS (Ditampilkan dinamis jika QRIS dipilih) -->
          <div id="qris-panel" class="hidden border border-amber-200 rounded-xl p-4 bg-amber-50/40 space-y-3 transition">
            <!-- State 1: Belum di-generate -->
            <div id="qris-before-generate">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-qrcode text-white text-sm"></i>
                </div>
                <div>
                  <p class="text-xs font-bold text-gray-800">Bayar via QRIS Midtrans</p>
                  <p class="text-[11px] text-gray-500">Mendukung semua e-wallet & bank (GoPay, OVO, Dana, BCA, dll)</p>
                </div>
              </div>
              <button type="button" id="btn-generate-qr"
                class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 shadow-sm">
                <i class="fas fa-qrcode"></i> Generate QR Code Pembayaran
              </button>
            </div>

            <!-- State 2: QR sudah di-generate, menunggu scan -->
            <div id="qris-waiting" class="hidden text-center space-y-3">
              <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Scan QR Code Berikut</p>
              <div class="flex justify-center">
                <img id="qris-image" src="" alt="QR Code QRIS" class="w-48 h-48 rounded-xl border-2 border-amber-300 shadow-md object-contain bg-white p-2">
              </div>
              <div class="flex items-center justify-center gap-2 text-amber-700 text-xs font-semibold">
                <i class="fas fa-circle-notch animate-spin"></i>
                <span id="qris-status-text">Menunggu pembayaran dari pasien...</span>
              </div>
              <p class="text-[10px] text-gray-400">QR berlaku 30 menit. Status diperbarui otomatis setiap 5 detik.</p>
              <button type="button" id="btn-refresh-qr"
                class="text-xs text-amber-700 underline hover:text-amber-900 transition">
                <i class="fas fa-redo-alt mr-1"></i>Generate Ulang QR
              </button>
            </div>

            <!-- State 3: Pembayaran berhasil -->
            <div id="qris-success" class="hidden text-center space-y-2 py-4">
              <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl">
                <i class="fas fa-check-circle"></i>
              </div>
              <p class="font-bold text-emerald-800 text-sm">Pembayaran QRIS Berhasil!</p>
              <p class="text-xs text-emerald-600">Halaman akan diperbarui otomatis...</p>
            </div>
          </div>

          <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-2 text-sm text-gray-700">
            <div class="flex justify-between font-bold text-gray-900 text-base">
              <span>Total Bayar:</span>
              <span class="font-mono text-blue-900">Rp {{ number_format($billing->grand_total, 2, ',', '.') }}</span>
            </div>
            @if($billing->potongan_bpjs > 0)
              <div class="flex justify-between text-xs text-emerald-700 font-semibold border-b border-blue-200/50 pb-1.5">
                <span>Potongan BPJS Terpasang:</span>
                <span>Rp {{ number_format($billing->potongan_bpjs, 2, ',', '.') }}</span>
              </div>
            @endif
            <p class="text-[11px] text-blue-800 mt-1 leading-relaxed">
              <i class="fas fa-exclamation-circle mr-1"></i> Setelah mengonfirmasi pembayaran, resep obat pasien akan otomatis masuk antrean Apotek untuk segera diserahkan.
            </p>
          </div>

          <div id="tunai-panel" class="space-y-2">
            <label class="block text-xs font-bold text-gray-500 uppercase">Jumlah Dibayar (Tunai)</label>
            <input type="number" step="0.01" min="0" name="jumlah_dibayar" id="jumlah_dibayar" placeholder="Masukkan jumlah tunai dari pelanggan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <div id="kembalian-display" class="text-sm font-semibold text-gray-800">Kembalian: Rp 0,00</div>
          </div>

          <!-- Tombol submit — disembunyikan saat QRIS dipilih -->
          <button type="submit" id="btn-submit-bayar" class="w-full py-3 bg-blue-900 text-white rounded-xl hover:bg-blue-800 transition font-bold text-sm shadow-md flex items-center justify-center gap-2">
            <i class="fas fa-check-circle"></i> Selesaikan &amp; Cetak Kuitansi
          </button>
        </form>
      @else
        <div class="space-y-4">
          <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-center space-y-2">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-lg">
              <i class="fas fa-check-circle"></i>
            </div>
            <h4 class="font-bold text-emerald-900">Tagihan Sudah Lunas!</h4>
            <p class="text-xs text-emerald-800 leading-relaxed">
              Pembayaran telah diterima dengan sukses pada tanggal {{ $billing->paid_at->isoFormat('D MMMM YYYY [pukul] HH:mm') }}.
            </p>
          </div>

          <div class="text-sm text-gray-700 space-y-2">
            <div class="flex justify-between border-b border-gray-100 py-1.5">
              <span class="text-gray-500">Metode Bayar:</span>
              <span class="font-semibold text-gray-800">{{ $billing->metode_pembayaran }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1.5">
              <span class="text-gray-500">Kasir Penerima:</span>
              <span class="font-semibold text-gray-800">{{ $billing->kasir?->nama ?? 'Apoteker/Resepsionis' }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 py-1.5">
              <span class="text-gray-500">Total Nominal:</span>
              <span class="font-mono font-bold text-blue-900">Rp {{ number_format($billing->grand_total, 2, ',', '.') }}</span>
            </div>
            @if($billing->jumlah_dibayar)
              <div class="flex justify-between border-b border-gray-100 py-1.5">
                <span class="text-gray-500">Jumlah Dibayar:</span>
                <span class="font-mono font-semibold text-gray-800">Rp {{ number_format($billing->jumlah_dibayar, 2, ',', '.') }}</span>
              </div>
            @endif
            <div class="flex justify-between border-b border-gray-100 py-1.5">
              <span class="text-gray-500">Kembalian:</span>
              <span class="font-mono font-bold text-blue-900">Rp {{ number_format($billing->kembalian ?? 0, 2, ',', '.') }}</span>
            </div>
          </div>
          
          <button onclick="window.print()" class="w-full py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-bold text-sm flex items-center justify-center gap-2">
            <i class="fas fa-print"></i> Cetak Ulang Kuitansi
          </button>
        </div>
      @endif

    </div>

  </div>

</div>

<!-- Script untuk radio button styling, BPJS, dan QRIS Midtrans -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const radios        = document.querySelectorAll('input[type="radio"][name="metode_pembayaran"]');
    const bpjsRadio     = document.querySelector('input[type="radio"][name="metode_pembayaran"][value="Asuransi"]');
    const qrisRadio     = document.querySelector('input[type="radio"][name="metode_pembayaran"][value="QRIS"]');
    const bpjsWrapper   = document.getElementById('bpjs-input-wrapper');
    const qrisPanel     = document.getElementById('qris-panel');
    const btnCekBpjs    = document.getElementById('btn-cek-bpjs');
    const noBpjsInput   = document.getElementById('no_bpjs_input');
    const bpjsStatus    = document.getElementById('bpjs-verification-status');
    const btnSubmit     = document.getElementById('btn-submit-bayar');

    // ── QRIS Elements ──
    const btnGenerateQr    = document.getElementById('btn-generate-qr');
    const btnRefreshQr     = document.getElementById('btn-refresh-qr');
    const qrisBeforeGen    = document.getElementById('qris-before-generate');
    const qrisWaiting      = document.getElementById('qris-waiting');
    const qrisSuccessDiv   = document.getElementById('qris-success');
    const qrisImage        = document.getElementById('qris-image');
    const qrisStatusText   = document.getElementById('qris-status-text');
    
    let qrisPollingInterval = null;

    // ── Update visual state radio buttons ──
    function updateSelection() {
      radios.forEach(radio => {
        const card    = radio.closest('label');
        const iconDiv = card.querySelector('div');
        const spanText = card.querySelector('span');
        
        if (radio.checked) {
          card.classList.add('bg-blue-50', 'border-blue-900', 'ring-2', 'ring-blue-900/10');
          iconDiv.classList.remove('bg-gray-100', 'text-gray-500');
          iconDiv.classList.add('bg-blue-900', 'text-white');
          spanText.classList.add('text-blue-900', 'font-bold');
        } else {
          card.classList.remove('bg-blue-50', 'border-blue-900', 'ring-2', 'ring-blue-900/10');
          iconDiv.classList.add('bg-gray-100', 'text-gray-500');
          iconDiv.classList.remove('bg-blue-900', 'text-white');
          spanText.classList.remove('text-blue-900', 'font-bold');
        }
      });
      
      // Toggle panel BPJS
      if (bpjsWrapper) {
        if (bpjsRadio && bpjsRadio.checked) {
          bpjsWrapper.classList.remove('hidden');
        } else {
          bpjsWrapper.classList.add('hidden');
        }
      }

      // Toggle panel QRIS & sembunyikan tombol Submit biasa
      if (qrisPanel && btnSubmit) {
        if (qrisRadio && qrisRadio.checked) {
          qrisPanel.classList.remove('hidden');
          btnSubmit.classList.add('hidden');
          // Stop polling jika switch ke metode lain sebelum scan
        } else {
          qrisPanel.classList.add('hidden');
          btnSubmit.classList.remove('hidden');
          stopQrisPolling();
        }
      }
    }

    radios.forEach(radio => {
      radio.addEventListener('change', updateSelection);
    });

    // ── BPJS Verification ──
    if (btnCekBpjs) {
      btnCekBpjs.addEventListener('click', function() {
        const noBpjs = noBpjsInput.value.trim();
        if (!noBpjs) {
          alert('Silakan masukkan nomor kartu BPJS Kesehatan terlebih dahulu.');
          return;
        }

        btnCekBpjs.disabled = true;
        btnCekBpjs.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Verifikasi...';
        bpjsStatus.classList.remove('hidden', 'text-emerald-600', 'text-red-600');
        bpjsStatus.classList.add('text-gray-500');
        bpjsStatus.innerHTML = '<i class="fas fa-circle-notch animate-spin mr-1"></i> Menghubungkan ke API PCare BPJS...';

        fetch("{{ route('admin.billing.cek-bpjs', $billing) }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ no_bpjs: noBpjs })
        })
        .then(response => response.json())
        .then(data => {
          btnCekBpjs.disabled = false;
          btnCekBpjs.innerHTML = 'Cek BPJS';
          
          if (data.success) {
            bpjsStatus.classList.remove('hidden', 'text-gray-500', 'text-red-600');
            bpjsStatus.classList.add('text-emerald-600', 'font-semibold', 'mt-2');
            
            let nameAlert = '';
            let nikBadge = '';
            
            if (data.data.nik_sistem && data.data.nik_bpjs) {
              if (data.data.is_nik_match) {
                nikBadge = `<div class="mt-1 px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg text-[11px] font-semibold flex items-center gap-1.5 w-fit">` +
                           `<i class="fas fa-id-card"></i> NIK Cocok (100% Terverifikasi)` +
                           `</div>`;
              } else {
                nikBadge = `<div class="mt-1 px-2.5 py-1 bg-red-50 text-red-800 border border-red-200 rounded-lg text-[11px] font-semibold flex items-center gap-1.5 w-fit">` +
                           `<i class="fas fa-exclamation-circle text-red-500"></i> NIK Mismatch (Periksa Fisik KTP!)` +
                           `</div>`;
              }
            } else if (data.data.nik_bpjs) {
              nikBadge = `<div class="mt-1 px-2.5 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-lg text-[11px] flex items-center gap-1.5 w-fit">` +
                         `<i class="fas fa-info-circle"></i> NIK BPJS: ${data.data.nik_bpjs} (Belum ada NIK di Sistem)` +
                         `</div>`;
            }

            if (!data.data.is_name_match) {
              nameAlert = `<div class="mt-2 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg text-xs font-normal space-y-1">` +
                          `<div class="flex items-center gap-1.5 font-bold text-amber-900">` +
                          `<i class="fas fa-exclamation-triangle text-amber-500"></i>` +
                          `<span>Peringatan Nama Tidak Cocok!</span>` +
                          `</div>` +
                          `• Nama di BPJS: <strong class="underline">${data.data.nama}</strong><br/>` +
                          `• Nama di Sistem: <strong class="underline">${data.data.nama_sistem}</strong><br/>` +
                          `• Persentase Kemiripan: <strong class="text-amber-900">${data.data.similarity}%</strong><br/>` +
                          `<span class="text-[10px] text-amber-600 block mt-1 leading-normal">Mohon kasir memverifikasi fisik kartu identitas pasien (KTP/KIS) sebelum melanjutkan pembayaran.</span>` +
                          `</div>`;
            }
            
            bpjsStatus.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${data.message}<br/>` +
                                   `<span class="text-xs text-gray-600 font-normal">` +
                                   `• Nama: <strong>${data.data.nama}</strong><br/>` +
                                   `• Tipe: ${data.data.jenis_peserta}<br/>` +
                                   `• Potongan: <strong class="text-emerald-600">Rp ${data.data.potongan}</strong></span>` + 
                                   nikBadge +
                                   nameAlert;
            
            const delay = (data.data.is_name_match && (!data.data.nik_sistem || data.data.is_nik_match)) ? 1800 : 5000;
            setTimeout(() => { window.location.reload(); }, delay);
          } else {
            bpjsStatus.classList.remove('hidden', 'text-gray-500', 'text-emerald-600');
            bpjsStatus.classList.add('text-red-600', 'font-semibold', 'mt-2');
            bpjsStatus.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message}`;
          }
        })
        .catch(error => {
          btnCekBpjs.disabled = false;
          btnCekBpjs.innerHTML = 'Cek BPJS';
          bpjsStatus.classList.remove('hidden', 'text-gray-500');
          bpjsStatus.classList.add('text-red-600', 'font-semibold', 'mt-2');
          bpjsStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Terjadi kesalahan koneksi API.';
          console.error(error);
        });
      });
    }

    // ── QRIS Logic ──

    function generateQrisCode() {
      if (btnGenerateQr) {
        btnGenerateQr.disabled = true;
        btnGenerateQr.innerHTML = '<i class="fas fa-circle-notch animate-spin mr-1"></i> Menghubungi Midtrans...';
      }

      fetch("{{ route('admin.billing.generate-qris', $billing) }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.qr_url) {
          // Tampilkan QR image
          qrisImage.src = data.qr_url;
          qrisBeforeGen.classList.add('hidden');
          qrisWaiting.classList.remove('hidden');
          // Mulai polling status
          startQrisPolling();
        } else {
          alert('Gagal generate QR: ' + (data.message || 'Error tidak diketahui.'));
          if (btnGenerateQr) {
            btnGenerateQr.disabled = false;
            btnGenerateQr.innerHTML = '<i class="fas fa-qrcode"></i> Generate QR Code Pembayaran';
          }
        }
      })
      .catch(err => {
        console.error(err);
        alert('Terjadi error koneksi. Coba lagi.');
        if (btnGenerateQr) {
          btnGenerateQr.disabled = false;
          btnGenerateQr.innerHTML = '<i class="fas fa-qrcode"></i> Generate QR Code Pembayaran';
        }
      });
    }

    function startQrisPolling() {
      // Poll setiap 5 detik
      qrisPollingInterval = setInterval(function() {
        fetch("{{ route('admin.billing.check-qris-status', $billing) }}", {
          method: 'GET',
          headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'settlement') {
            stopQrisPolling();
            // Tampilkan success state
            qrisWaiting.classList.add('hidden');
            qrisSuccessDiv.classList.remove('hidden');
            // Redirect ke halaman yang sama setelah 2 detik untuk reload kuitansi
            setTimeout(() => { window.location.reload(); }, 2000);
          } else if (data.status === 'expire' || data.status === 'cancel' || data.status === 'deny') {
            stopQrisPolling();
            if (qrisStatusText) {
              qrisStatusText.innerHTML = `<span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>${data.message}</span>`;
            }
            // Kembali ke state awal setelah 3 detik
            setTimeout(() => {
              qrisWaiting.classList.add('hidden');
              qrisBeforeGen.classList.remove('hidden');
              if (btnGenerateQr) {
                btnGenerateQr.disabled = false;
                btnGenerateQr.innerHTML = '<i class="fas fa-qrcode"></i> Generate QR Code Baru';
              }
            }, 3000);
          } else if (data.status === 'pending') {
            if (qrisStatusText) {
              qrisStatusText.textContent = 'Menunggu pembayaran dari pasien...';
            }
          }
        })
        .catch(err => console.error('QRIS polling error:', err));
      }, 5000);
    }

    function stopQrisPolling() {
      if (qrisPollingInterval) {
        clearInterval(qrisPollingInterval);
        qrisPollingInterval = null;
      }
    }

    if (btnGenerateQr) {
      btnGenerateQr.addEventListener('click', generateQrisCode);
    }

    if (btnRefreshQr) {
      btnRefreshQr.addEventListener('click', function() {
        stopQrisPolling();
        qrisWaiting.classList.add('hidden');
        qrisBeforeGen.classList.remove('hidden');
        if (btnGenerateQr) {
          btnGenerateQr.disabled = false;
          btnGenerateQr.innerHTML = '<i class="fas fa-qrcode"></i> Generate QR Code Pembayaran';
        }
      });
    }

    // Inisialisasi awal
    // Kembalian (tunai) - hitung real-time
    const grandTotalEl = document.getElementById('grand_total_value');
    const paidEl = document.getElementById('jumlah_dibayar');
    const kembalianDisplayEl = document.getElementById('kembalian-display');

    function formatIDR(n) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 2 }).format(n);
    }

    function updateKembalian() {
      if (!grandTotalEl || !kembalianDisplayEl) return;
      const grand = parseFloat(grandTotalEl.value) || 0;
      const paid = paidEl ? parseFloat(paidEl.value) || 0 : 0;
      const change = paid - grand;
      if (change < 0) {
        kembalianDisplayEl.textContent = 'Kurang: ' + formatIDR(Math.abs(change));
        kembalianDisplayEl.classList.add('text-red-600');
        kembalianDisplayEl.classList.remove('text-emerald-700');
      } else {
        kembalianDisplayEl.textContent = 'Kembalian: ' + formatIDR(change);
        kembalianDisplayEl.classList.remove('text-red-600');
        kembalianDisplayEl.classList.add('text-emerald-700');
      }
    }

    if (paidEl) {
      paidEl.addEventListener('input', updateKembalian);
      paidEl.addEventListener('change', updateKembalian);
    }

    updateKembalian();

    // Jika diarahkan dari proses pembayaran, cetak otomatis kuitansi
    @if(session('print_kuitansi') && $billing->status === 'Lunas')
      setTimeout(function() { window.print(); }, 600);
    @endif

    updateSelection();
  });
</script>
@endsection

