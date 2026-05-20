<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Kunjungan - Sahaduta</title>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Font & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      min-height: 100vh;
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .custom-shadow {
      box-shadow: 0 10px 30px -10px rgba(30, 41, 59, 0.05), 0 1px 3px -1px rgba(30, 41, 59, 0.02);
    }
    .btn-anim {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-anim:hover {
      transform: translateY(-2px);
    }
  </style>
</head>
<body class="text-slate-800 antialiased py-8">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- ===== HEADER & NAV ===== -->
    <header class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-aos="fade-down">
      <div>
        <a href="{{ route('pasien.portal') }}" class="btn-anim inline-flex items-center gap-2 text-sm font-semibold text-blue-900 bg-white hover:bg-blue-50 border border-blue-900/10 px-4 py-2 rounded-2xl shadow-sm mb-3">
          <i class="fas fa-arrow-left text-xs"></i> Kembali ke Dashboard
        </a>
        <h1 class="text-3xl font-extrabold text-slate-800 flex items-center gap-2">
          Detail Kunjungan & Tagihan Medis
        </h1>
        <p class="text-slate-500 text-sm mt-1">Laporan rekam medis lengkap beserta detail biaya penanganan Anda</p>
      </div>

      <!-- Quick Action Feedback -->
      @if(strtolower($antrian->status) === 'selesai')
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold text-slate-400">Punya ulasan untuk kunjungan ini?</span>
          <button onclick="showModalFeedback({{ $antrian->id }})" class="btn-anim text-blue-900 bg-white hover:bg-blue-50 border border-blue-900/15 px-4.5 py-2.5 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
            <i class="fas fa-star text-yellow-400"></i> Beri Ulasan
          </button>
        </div>
      @endif
    </header>

    @php
      $rm = $antrian->rekamMedis;
      $billing = $rm ? $rm->billing : null;
      $dokter = $rm && $rm->dokter ? ($rm->dokter->nama ?? '—') : '—';
      
      // Diagnosa ICD-10
      $diagnosa = [];
      if ($rm && $rm->diagnosa) {
          foreach ($rm->diagnosa as $d) {
              $diagnosa[] = [
                  'kode'   => $d->icdx ? ($d->icdx->kode ?? '?') : '?',
                  'nama'   => $d->icdx ? ($d->icdx->nama ?? '—') : '—',
                  'primer' => (bool) $d->is_primer,
              ];
          }
      }
      
      // Resep Obat
      $resepObat = [];
      if ($rm && $rm->resep && $rm->resep->details) {
          foreach ($rm->resep->details as $det) {
              $resepObat[] = [
                  'nama'        => $det->obat ? ($det->obat->nama ?? '—') : '—',
                  'jumlah'      => $det->jumlah ?? null,
                  'dosis'       => $det->dosis ?? null,
                  'aturan_pakai'=> $det->aturan_pakai ?? null,
              ];
          }
      }

      $statusCls = [
          'selesai' => 'bg-green-100 text-green-700 border-green-200',
          'batal' => 'bg-red-100 text-red-700 border-red-200',
          'menunggu' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
          'dipanggil' => 'bg-blue-100 text-blue-700 border-blue-200'
      ];
      $statusLbl = [
          'selesai' => 'Selesai',
          'batal' => 'Batal',
          'menunggu' => 'Menunggu',
          'dipanggil' => 'Dipanggil'
      ];
      $currentStatus = strtolower($antrian->status);
    @endphp

    <!-- ===== MAIN CONTENT GRID ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      
      <!-- LEFT COLUMN: MEDICAL DETAILS (2/3) -->
      <div class="lg:col-span-2 space-y-8">
        
        <!-- CARD 1: RINGKASAN KUNJUNGAN -->
        <div class="bg-white rounded-3xl p-6 border border-blue-900/10 custom-shadow glass-card" data-aos="fade-right">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6 flex-wrap gap-3">
            <div class="flex items-center gap-3">
              <div class="w-14 h-14 bg-blue-900 text-white rounded-2xl flex items-center justify-center font-extrabold text-sm shadow-md">
                {{ $antrian->jenis === 'Online' ? 'O-'.str_pad($antrian->no_antrian, 3, '0', STR_PAD_LEFT) : str_pad($antrian->no_antrian, 3, '0', STR_PAD_LEFT) }}
              </div>
              <div>
                <h3 class="font-extrabold text-slate-800 text-lg">{{ $layanan }}</h3>
                <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-400">
                  <i class="fas fa-calendar-alt text-slate-300"></i>
                  <span>{{ \Carbon\Carbon::parse($antrian->tanggal)->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
              </div>
            </div>
            
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ $statusCls[$currentStatus] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                {{ $statusLbl[$currentStatus] ?? 'Menunggu' }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="flex items-start gap-3 bg-slate-50/50 border border-slate-100 rounded-2xl p-4">
              <div class="w-10 h-10 bg-blue-50 text-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-md text-base"></i>
              </div>
              <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dokter Pemeriksa</div>
                <div class="text-sm font-bold text-slate-700 mt-0.5">{{ $dokter }}</div>
                <div class="text-xs text-slate-400 mt-0.5">Spesialis / Praktisi Klinik Sahaduta</div>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-slate-50/50 border border-slate-100 rounded-2xl p-4">
              <div class="w-10 h-10 bg-blue-50 text-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-id-card text-base"></i>
              </div>
              <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Identitas Pasien</div>
                <div class="text-sm font-bold text-slate-700 mt-0.5">{{ $pasien->nama ?? '—' }}</div>
                <div class="text-xs text-slate-400 mt-0.5">NIK: {{ $pasien->nik ?? '—' }} · No. RM: {{ $pasien->no_rekam_medis ?? '—' }}</div>
              </div>
            </div>
          </div>

          @if($antrian->keluhan)
            <div class="mt-6 p-4 bg-amber-50/50 border border-amber-900/10 rounded-2xl">
              <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5 mb-1.5">
                <i class="fas fa-comment-medical text-amber-500"></i> Keluhan Awal Pasien
              </h4>
              <p class="text-sm text-slate-600 leading-relaxed italic">&ldquo;{{ $antrian->keluhan }}&rdquo;</p>
            </div>
          @endif
        </div>

        <!-- CARD 2: HASIL PEMERIKSAAN MEDIS (TTV & DIAGNOSA) -->
        <div class="bg-white rounded-3xl p-6 border border-blue-900/10 custom-shadow glass-card" data-aos="fade-right" data-aos-delay="100">
          <div class="border-b border-slate-100 pb-4 mb-6">
            <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
              <i class="fas fa-notes-medical text-blue-900"></i> Rekam Medis &amp; TTV
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Tanda-tanda vital beserta diagnosa penyakit dari dokter</p>
          </div>

          <!-- Tanda-Tanda Vital (TTV) -->
          <div class="mb-8">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
              <i class="fas fa-heartbeat text-red-500"></i> Tanda-Tanda Vital (TTV)
            </h4>
            
            @if($rm && ($rm->tekanan_darah || $rm->suhu || $rm->nadi || $rm->respirasi))
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @if($rm->tekanan_darah)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-lg font-extrabold text-blue-900">{{ $rm->tekanan_darah }}</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Tekanan Darah</div>
                    <div class="text-[10px] text-slate-300 mt-0.5">mmHg</div>
                  </div>
                @endif
                @if($rm->suhu)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-lg font-extrabold text-orange-600">{{ $rm->suhu }}°C</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Suhu Tubuh</div>
                    <div class="text-[10px] text-slate-300 mt-0.5">Normal: 36.5 - 37.5</div>
                  </div>
                @endif
                @if($rm->nadi)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-lg font-extrabold text-red-600">{{ $rm->nadi }} bpm</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Denyut Nadi</div>
                    <div class="text-[10px] text-slate-300 mt-0.5">kali / menit</div>
                  </div>
                @endif
                @if($rm->respirasi)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-lg font-extrabold text-emerald-600">{{ $rm->respirasi }} bpm</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Pernapasan</div>
                    <div class="text-[10px] text-slate-300 mt-0.5">kali / menit</div>
                  </div>
                @endif
                @if($rm->berat_badan)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-lg font-extrabold text-purple-600">{{ $rm->berat_badan }} kg</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Berat Badan</div>
                    <div class="text-[10px] text-slate-300 mt-0.5">Berat Aktual</div>
                  </div>
                @endif
                @if($rm->tinggi_badan)
                  <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                    <div class="text-lg font-extrabold text-indigo-600">{{ $rm->tinggi_badan }} cm</div>
                    <div class="text-xs text-slate-400 font-semibold mt-0.5">Tinggi Badan</div>
                    <div class="text-[10px] text-slate-300 mt-0.5">Tinggi Aktual</div>
                  </div>
                @endif
              </div>
            @else
              <div class="text-sm text-slate-400 bg-slate-50 border border-slate-100 rounded-2xl p-5 text-center italic">
                Tanda-Tanda Vital tidak tercatat dalam kunjungan ini
              </div>
            @endif
          </div>

          <!-- Diagnosa & ICD-10 -->
          <div class="mb-8">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
              <i class="fas fa-virus text-red-500"></i> Diagnosa Medis (ICD-10)
            </h4>
            
            @if(count($diagnosa) > 0)
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($diagnosa as $d)
                  <div class="flex items-start gap-3 p-3.5 bg-white border border-slate-100 rounded-2xl shadow-sm">
                    <span class="text-[10px] font-bold px-2 py-1.5 rounded-lg flex-shrink-0 {{ $d['primer'] ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                      {{ $d['primer'] ? 'Primer' : 'Sekunder' }}
                    </span>
                    <div>
                      <div class="text-xs font-extrabold text-slate-800">{{ $d['kode'] }}</div>
                      <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $d['nama'] }}</div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-sm text-slate-400 bg-slate-50 border border-slate-100 rounded-2xl p-5 text-center italic">
                Belum ada diagnosa penyakit tercatat dalam kunjungan ini
              </div>
            @endif
          </div>

          <!-- Catatan / Anamnesis Dokter -->
          <div class="space-y-4">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
              <i class="fas fa-clipboard-list text-blue-900"></i> Catatan &amp; Tindakan Medis
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              @if($rm && $rm->anamnesis)
                <div class="bg-blue-50/30 border border-blue-900/5 rounded-2xl p-4">
                  <div class="text-[10px] font-bold text-blue-900/60 uppercase tracking-wider mb-1">Hasil Anamnesis Dokter</div>
                  <p class="text-xs text-slate-600 leading-relaxed">{{ $rm->anamnesis }}</p>
                </div>
              @endif

              @if($rm && $rm->tindakan)
                <div class="bg-blue-50/30 border border-blue-900/5 rounded-2xl p-4">
                  <div class="text-[10px] font-bold text-blue-900/60 uppercase tracking-wider mb-1">Tindakan Medis</div>
                  <p class="text-xs text-slate-600 leading-relaxed">{{ $rm->tindakan }}</p>
                </div>
              @endif

              @if($rm && $rm->pengobatan)
                <div class="bg-blue-50/30 border border-blue-900/5 rounded-2xl p-4">
                  <div class="text-[10px] font-bold text-blue-900/60 uppercase tracking-wider mb-1">Terapi / Pengobatan</div>
                  <p class="text-xs text-slate-600 leading-relaxed">{{ $rm->pengobatan }}</p>
                </div>
              @endif

              @if($rm && $rm->prognosa)
                <div class="bg-blue-50/30 border border-blue-900/5 rounded-2xl p-4">
                  <div class="text-[10px] font-bold text-blue-900/60 uppercase tracking-wider mb-1">Prognosa</div>
                  <p class="text-xs text-slate-600 leading-relaxed">{{ $rm->prognosa }}</p>
                </div>
              @endif
            </div>

            @if(!$rm || (!$rm->anamnesis && !$rm->tindakan && !$rm->pengobatan && !$rm->prognosa))
              <div class="text-sm text-slate-400 bg-slate-50 border border-slate-100 rounded-2xl p-5 text-center italic">
                Belum ada rincian catatan pemeriksaan dokter
              </div>
            @endif
          </div>
        </div>

        <!-- CARD 3: RESEP OBAT -->
        <div class="bg-white rounded-3xl p-6 border border-blue-900/10 custom-shadow glass-card" data-aos="fade-right" data-aos-delay="200">
          <div class="border-b border-slate-100 pb-4 mb-6">
            <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
              <i class="fas fa-prescription-bottle-alt text-blue-900"></i> Resep Obat &amp; Dosis
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar resep obat beserta aturan pakai dari apoteker Sahaduta</p>
          </div>

          @if(count($resepObat) > 0)
            <div class="overflow-hidden border border-slate-100 rounded-2xl">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <th class="p-4 w-12 text-center">No</th>
                    <th class="p-4">Nama Obat</th>
                    <th class="p-4 text-center">Jumlah</th>
                    <th class="p-4">Dosis &amp; Aturan Pakai</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @foreach($resepObat as $i => $o)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                      <td class="p-4 text-xs font-semibold text-slate-400 text-center">{{ $i + 1 }}</td>
                      <td class="p-4">
                        <div class="flex items-center gap-2">
                          <div class="w-7 h-7 bg-green-50 text-green-700 border border-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-pills text-xs"></i>
                          </div>
                          <span class="text-xs font-bold text-slate-700">{{ $o['nama'] }}</span>
                        </div>
                      </td>
                      <td class="p-4 text-xs font-semibold text-slate-700 text-center">{{ $o['jumlah'] }} pcs</td>
                      <td class="p-4 text-xs text-slate-500 font-medium">{{ $o['dosis'] ?? '—' }} · {{ $o['aturan_pakai'] ?? '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-sm text-slate-400 bg-slate-50 border border-slate-100 rounded-2xl p-6 text-center italic">
              Tidak ada resep obat yang dikeluarkan dalam pemeriksaan ini
            </div>
          @endif
        </div>

      </div>

      <!-- RIGHT COLUMN: INVOICE / COSTS (1/3) -->
      <div class="space-y-8" data-aos="fade-left">
        
        <!-- BILLING CARD (PAPER STYLE) -->
        <div class="bg-white rounded-3xl border border-blue-900/10 custom-shadow overflow-hidden relative glass-card">
          <!-- Top Accent Ribbon -->
          <div class="h-2 bg-gradient-to-r from-blue-900 to-indigo-900"></div>

          <!-- Paper body -->
          <div class="p-6">
            <!-- Paper Header -->
            <div class="border-b border-dashed border-slate-200 pb-5 mb-5 text-center">
              <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Klinik Pratama Sahaduta</div>
              <h3 class="text-xl font-black text-slate-800 mt-1 uppercase tracking-tight">Kwitansi Pembayaran</h3>
              
              <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-bold shadow-sm {{ $billing && (strtolower($billing->status) === 'lunas' || strtolower($billing->status) === 'paid') ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                <i class="fas {{ $billing && (strtolower($billing->status) === 'lunas' || strtolower($billing->status) === 'paid') ? 'fa-check-circle' : 'fa-clock' }}"></i>
                {{ $billing && (strtolower($billing->status) === 'lunas' || strtolower($billing->status) === 'paid') ? 'LUNAS' : 'BELUM LUNAS' }}
              </div>
            </div>

            <!-- Invoice Details -->
            <div class="space-y-3.5 text-xs border-b border-slate-100 pb-5 mb-5 font-medium text-slate-500">
              <div class="flex justify-between items-center">
                <span>No. Invoice</span>
                <span class="font-bold text-slate-700">{{ $billing->no_invoice ?? '—' }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span>Metode Pembayaran</span>
                <span class="font-bold text-slate-700 flex items-center gap-1">
                  {{ $jenisPelayanan }}
                  @if($jenisPelayanan === 'BPJS')
                    <span class="text-[9px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-extrabold">Covered</span>
                  @endif
                </span>
              </div>
              @if($billing && $billing->paid_at)
                <div class="flex justify-between items-center">
                  <span>Waktu Pembayaran</span>
                  <span class="font-bold text-slate-700">{{ $billing->paid_at->isoFormat('D MMM Y · HH:mm') }}</span>
                </div>
              @endif
            </div>

            <!-- Cost Line Items -->
            <div class="space-y-4 mb-6">
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Rincian Item Tindakan</div>
              
              @php
                $biayaRegistrasi = $billing->biaya_registrasi ?? 0;
                $biayaKamar = $billing->biaya_kamar ?? 0;
                $biayaTindakan = $billing->biaya_tindakan ?? 0;
                $biayaObat = $billing->biaya_obat ?? 0;
                $potonganBpjs = $billing->potongan_bpjs ?? 0;
                $grandTotal = $billing->grand_total ?? 0;
              @endphp

              <!-- Item 1: Registrasi -->
              <div class="flex justify-between text-xs">
                <div class="flex flex-col">
                  <span class="font-bold text-slate-700">Biaya Registrasi &amp; Administrasi</span>
                  <span class="text-[10px] text-slate-400 mt-0.5">Biaya pendaftaran loket pasien</span>
                </div>
                <span class="font-bold text-slate-700">Rp {{ number_format($biayaRegistrasi, 0, ',', '.') }}</span>
              </div>

              <!-- Item 2: Pemeriksaan/Tindakan -->
              @if($biayaTindakan > 0)
                <div class="flex justify-between text-xs">
                  <div class="flex flex-col">
                    <span class="font-bold text-slate-700">Pemeriksaan &amp; Tindakan Medis</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Jasa konsultasi dokter &amp; penanganan</span>
                  </div>
                  <span class="font-bold text-slate-700">Rp {{ number_format($biayaTindakan, 0, ',', '.') }}</span>
                </div>
              @endif

              <!-- Item 3: Farmasi/Obat -->
              @if($biayaObat > 0)
                <div class="flex justify-between text-xs">
                  <div class="flex flex-col">
                    <span class="font-bold text-slate-700">Biaya Obat &amp; Alkes</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Penyediaan resep obat apotek</span>
                  </div>
                  <span class="font-bold text-slate-700">Rp {{ number_format($biayaObat, 0, ',', '.') }}</span>
                </div>
              @endif

              <!-- Item 4: Kamar (jika ada rawat inap) -->
              @if($biayaKamar > 0)
                <div class="flex justify-between text-xs">
                  <div class="flex flex-col">
                    <span class="font-bold text-slate-700">Sewa Kamar Rawat Inap</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Akomodasi inap medis</span>
                  </div>
                  <span class="font-bold text-slate-700">Rp {{ number_format($biayaKamar, 0, ',', '.') }}</span>
                </div>
              @endif

              <!-- Subtotal divider -->
              <div class="border-t border-slate-100 pt-3 flex justify-between text-xs font-semibold text-slate-400">
                <span>Subtotal Biaya</span>
                <span>Rp {{ number_format($biayaRegistrasi + $biayaTindakan + $biayaObat + $biayaKamar, 0, ',', '.') }}</span>
              </div>

              <!-- BPJS Subsidy (Negative) -->
              @if($potonganBpjs > 0)
                <div class="flex justify-between text-xs text-blue-700 bg-blue-50/50 border border-blue-100 p-2.5 rounded-xl">
                  <div class="flex flex-col">
                    <span class="font-bold">Potongan / Subsidy BPJS</span>
                    <span class="text-[9px] text-blue-500 mt-0.5">Ditanggung penuh oleh BPJS Kesehatan</span>
                  </div>
                  <span class="font-extrabold">- Rp {{ number_format($potonganBpjs, 0, ',', '.') }}</span>
                </div>
              @endif

            </div>

            <!-- Grand Total -->
            <div class="border-t-2 border-dashed border-slate-200 pt-5 mt-5">
              <div class="flex justify-between items-center">
                <div>
                  <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Tagihan</span>
                  <span class="text-xs text-slate-400">Grand Total</span>
                </div>
                <span class="text-2xl font-black text-blue-900 tracking-tight">
                  Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </span>
              </div>
            </div>

            <!-- Disclaimer notes -->
            <div class="mt-6 bg-slate-50 border border-slate-100 rounded-2xl p-3.5 text-[10px] text-slate-400 leading-relaxed flex gap-2">
              <i class="fas fa-info-circle text-blue-900 mt-0.5 text-xs flex-shrink-0"></i>
              <div>
                Rincian ini diterbitkan secara sah oleh Klinik Sahaduta. Jika ada ketidaksesuaian mengenai klaim tarif BPJS atau tagihan obat, silakan hubungi meja administrasi klinik.
              </div>
            </div>

          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Modal Feedback (Jika diperlukan) -->
  <div id="modalFeedback" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300 relative border border-blue-900/10">
      <h3 class="text-lg font-bold text-slate-800 mb-2 flex items-center gap-2">
        <i class="fas fa-star text-yellow-400"></i> Beri Ulasan Kunjungan
      </h3>
      <p class="text-xs text-slate-400 leading-relaxed mb-4">Masukan Anda sangat berharga bagi kami untuk meningkatkan kualitas pelayanan dokter dan staf medis.</p>
      
      <form id="formFeedback" onsubmit="submitFeedback(event)">
        @csrf
        <input type="hidden" id="feedbackAntrianId" name="antrian_id">
        
        <!-- Stars -->
        <div class="flex justify-center gap-2.5 mb-5">
          @for($i=1; $i<=5; $i++)
            <i class="fas fa-star text-2xl text-slate-200 cursor-pointer transition-colors hover:text-yellow-400 rating-star" onclick="setRating({{ $i }})" data-val="{{ $i }}"></i>
          @endfor
        </div>
        <input type="hidden" id="ratingInput" name="rating" value="0">

        <!-- Comment -->
        <div class="mb-4">
          <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block mb-1">Komentar &amp; Ulasan</label>
          <textarea id="feedbackKomentar" name="komentar" rows="3" placeholder="Tuliskan ulasan Anda..." class="w-full border border-slate-200 rounded-xl px-4 py-3 text-xs focus:border-blue-900 outline-none resize-none bg-slate-50"></textarea>
        </div>

        <div class="flex justify-end gap-2.5">
          <button type="button" onclick="closeModalFeedback()" class="px-4.5 py-2.5 border border-slate-200 text-slate-500 rounded-2xl text-xs font-bold hover:bg-slate-50 transition">Batal</button>
          <button type="submit" id="btnSubmitFeedback" class="px-5 py-2.5 bg-blue-900 text-white rounded-2xl text-xs font-bold hover:bg-blue-800 transition flex items-center gap-1.5">Kirim Ulasan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Toast notification -->
  <div id="toast" class="fixed bottom-5 right-5 bg-green-700 text-white px-5 py-3 rounded-2xl shadow-xl font-semibold text-sm flex items-center gap-2 z-50 opacity-0 translate-y-4 transition-all duration-300 pointer-events-none">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg">Berhasil</span>
  </div>

  <!-- Scripts -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ once: true, duration: 700, offset: 80 });

    // ================================================================
    // MODAL FEEDBACK / REVIEW
    // ================================================================
    function showModalFeedback(id) {
      document.getElementById('feedbackAntrianId').value = id;
      setRating(0);
      document.getElementById('feedbackKomentar').value = '';
      
      const modal = document.getElementById('modalFeedback');
      modal.classList.remove('opacity-0', 'pointer-events-none');
      modal.firstElementChild.classList.remove('scale-95');
      modal.firstElementChild.classList.add('scale-100');
    }

    function closeModalFeedback() {
      const modal = document.getElementById('modalFeedback');
      modal.classList.add('opacity-0', 'pointer-events-none');
      modal.firstElementChild.classList.remove('scale-100');
      modal.firstElementChild.classList.add('scale-95');
    }

    function setRating(val) {
      document.getElementById('ratingInput').value = val;
      document.querySelectorAll('.rating-star').forEach(el => {
        const starVal = parseInt(el.getAttribute('data-val'));
        if (starVal <= val) {
          el.classList.remove('text-slate-200');
          el.classList.add('text-yellow-400');
        } else {
          el.classList.remove('text-yellow-400');
          el.classList.add('text-slate-200');
        }
      });
    }

    function showToast(msg, isErr=false) {
      const toast = document.getElementById('toast');
      const msgEl = document.getElementById('toastMsg');
      msgEl.textContent = msg;
      if (isErr) {
        toast.classList.replace('bg-green-700', 'bg-red-700');
      } else {
        toast.classList.replace('bg-red-700', 'bg-green-700');
      }
      toast.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
      setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
      }, 3000);
    }

    async function submitFeedback(e) {
      e.preventDefault();
      const id = document.getElementById('feedbackAntrianId').value;
      const rating = document.getElementById('ratingInput').value;
      const komentar = document.getElementById('feedbackKomentar').value;

      if (!rating || rating === '0') {
        showToast('Pilih bintang terlebih dahulu', true);
        return;
      }

      const btn = document.getElementById('btnSubmitFeedback');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

      try {
        const res = await fetch('{{ route("pasien.antrian.feedback") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ antrian_id: id, rating, komentar })
        });
        const data = await res.json();
        if (data.success) {
          showToast('Ulasan berhasil dikirim!');
          closeModalFeedback();
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showToast(data.message || 'Gagal mengirim ulasan.', true);
        }
      } catch (err) {
        showToast('Terjadi kesalahan koneksi.', true);
      } finally {
        btn.disabled = false;
        btn.innerHTML = 'Kirim Ulasan';
      }
    }
  </script>

</body>
</html>
