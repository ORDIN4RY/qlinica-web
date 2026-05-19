<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QLINICA | Pemesanan Antrian</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    html { scroll-behavior: smooth; }
    .card-hover { transition: all 0.3s cubic-bezier(0.15,0.75,0.45,1); }
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(30,58,138,0.2); }
    .btn-anim { transition: all 0.25s ease; }
    .btn-anim:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -8px rgba(30,58,138,0.4); }
    .float-anim { animation: floatY 6s ease-in-out infinite; }
    @keyframes floatY { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
    .pulse-bg { animation: pulseBg 5s infinite; }
    @keyframes pulseBg { 0%,100%{opacity:.15} 50%{opacity:.3} }
    .nav-link { position:relative; }
    .nav-link::after { content:''; position:absolute; bottom:-2px; left:0; right:0; height:2px; background:#1e3a8a; transform:scaleX(0); transition:transform 0.3s ease; }
    .nav-link:hover::after { transform:scaleX(1); }
    /* Status badge */
    .badge-menunggu  { background:#fef3c7; color:#92400e; }
    .badge-terpanggil{ background:#d1fae5; color:#065f46; }
    .badge-batal     { background:#fee2e2; color:#991b1b; }
    /* Modal */
    .modal-overlay { transition: opacity 0.3s, visibility 0.3s; }
    .modal-overlay.open { opacity:1; visibility:visible; }
    .modal-box { transition: transform 0.35s cubic-bezier(0.15,0.75,0.45,1); transform:scale(0.95); }
    .modal-overlay.open .modal-box { transform:scale(1); }
    /* Antrian number big */
    .no-antrian { font-size:5rem; font-weight:900; line-height:1; }
    /* Ticker */
    .ticker-wrap { white-space:nowrap; }
    @keyframes ticker { 0%{transform:translateX(100%)} 100%{transform:translateX(-100%)} }
    .ticker-text { animation: ticker 18s linear infinite; display:inline-block; }
  </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-700 overflow-x-hidden">

  <!-- ===== NAVBAR ===== -->
  <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-blue-900/10 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <a href="/" class="flex items-center space-x-2 group">
          <div class="w-8 h-8 bg-blue-900 rounded-xl flex items-center justify-center transition-transform duration-500 group-hover:rotate-3">
            <i class="fas fa-clinic-medical text-white"></i>
          </div>
          <span class="font-semibold text-xl text-gray-800">QLINICA</span>
        </a>

        <!-- Nav Links -->
        <nav class="hidden md:flex space-x-8 text-sm font-medium">
          <a href="/" class="nav-link text-gray-700 hover:text-blue-900 transition">Beranda</a>
          <a href="#antrian" class="nav-link text-blue-900 font-semibold transition">Antrian</a>
          <a href="#status" class="nav-link text-gray-700 hover:text-blue-900 transition">Status</a>
          <a href="#daftar" class="nav-link text-gray-700 hover:text-blue-900 transition">Daftar</a>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-3">
          <button id="btnAmbil"
            class="btn-anim bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md">
            <i class="fas fa-ticket-alt"></i>
            <span class="hidden sm:inline">Ambil Antrian</span>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- ===== TICKER INFO ===== -->
  <div class="bg-blue-900 py-2 overflow-hidden">
    <div class="ticker-wrap">
      <span class="ticker-text text-white text-xs font-medium px-8">
        📢 &nbsp; Bawa Kartu Identitas &nbsp;·&nbsp; Datang 15 menit sebelum nomor antrian dipanggil &nbsp;·&nbsp; Pastikan No. RM terdaftar &nbsp;·&nbsp; Layanan dibuka Senin–Sabtu pukul 08.00–17.00 &nbsp;·&nbsp; 📢
      </span>
    </div>
  </div>

  <main>

    <!-- ===== HERO ===== -->
    <section id="antrian" class="relative bg-gradient-to-br from-blue-50 via-white to-white overflow-hidden py-16 md:py-24">
      <div class="absolute top-10 right-0 w-96 h-96 bg-blue-900/5 rounded-full blur-3xl float-anim"></div>
      <div class="absolute bottom-0 left-0 w-72 h-72 bg-blue-900/8 rounded-full blur-2xl pulse-bg"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">

          <!-- kiri -->
          <div class="space-y-6" data-aos="fade-right">
            <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-900 px-4 py-1.5 rounded-full text-sm font-semibold">
              <i class="fas fa-ticket-alt"></i> Antrian Online Klinik QLINICA
            </span>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
              Pesan Antrian <br><span class="text-blue-900">Tanpa Antre</span> Panjang
            </h1>
            <p class="text-gray-500 text-base max-w-md leading-relaxed">
              Ambil nomor antrian dari rumah, pantau status secara realtime, dan datang tepat waktu. Layanan klinik lebih mudah dan efisien.
            </p>
            <div class="flex flex-wrap gap-3 pt-2">
              <button onclick="openModal()" class="btn-anim bg-blue-900 hover:bg-blue-800 text-white px-7 py-3.5 rounded-2xl font-semibold shadow-md flex items-center gap-2">
                <i class="fas fa-ticket-alt"></i> Ambil Antrian Sekarang
              </button>
              <a href="#status" class="btn-anim bg-white border border-blue-900/25 hover:border-blue-900 text-blue-900 px-7 py-3.5 rounded-2xl font-semibold shadow-sm flex items-center gap-2">
                <i class="fas fa-broadcast-tower"></i> Lihat Status
              </a>
            </div>
          </div>

          <!-- kanan — stat cards -->
          <div class="grid grid-cols-2 gap-4" data-aos="fade-left">
            <div class="card-hover bg-white p-6 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-blue-100 text-blue-900 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-users"></i>
              </div>
              <div class="text-3xl font-bold text-blue-900" id="statTotal">0</div>
              <div class="text-xs text-gray-500 mt-0.5">Total Antrian Hari Ini</div>
            </div>
            <div class="card-hover bg-white p-6 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-stethoscope"></i>
              </div>
              <div class="text-3xl font-bold text-green-700" id="statSkrg">—</div>
              <div class="text-xs text-gray-500 mt-0.5">Sedang Dilayani</div>
            </div>
            <div class="card-hover bg-white p-6 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-clock"></i>
              </div>
              <div class="text-3xl font-bold text-amber-700" id="statMenunggu">0</div>
              <div class="text-xs text-gray-500 mt-0.5">Masih Menunggu</div>
            </div>
            <div class="card-hover bg-white p-6 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-purple-100 text-purple-700 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="text-3xl font-bold text-purple-700" id="statSelesai">0</div>
              <div class="text-xs text-gray-500 mt-0.5">Sudah Terpanggil</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== STATUS REALTIME ===== -->
    <section id="status" class="py-16 bg-white">
      <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Realtime</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Status <span class="text-blue-900">Antrian</span> Sekarang</h2>
          <p class="text-gray-500 mt-2 text-sm">Antrian diperbarui setiap 30 detik secara otomatis.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6" data-aos="fade-up" data-aos-delay="100">
          <!-- Panel kiri — nomor dipanggil -->
          <div class="md:col-span-2 bg-gradient-to-br from-blue-900 to-blue-700 rounded-3xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-bold text-lg flex items-center gap-2">
                <i class="fas fa-broadcast-tower animate-pulse"></i> Nomor Dipanggil
              </h3>
              <span class="text-xs text-blue-300 bg-blue-800/50 px-3 py-1 rounded-full">LIVE</span>
            </div>
            <div class="text-center py-6">
              <p class="text-blue-200 text-sm mb-2">Saat ini melayani nomor</p>
              <div class="no-antrian text-white" id="nomorDisplay">—</div>
              <p class="text-blue-300 text-sm mt-3" id="layananDisplay">Belum ada antrian aktif</p>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-2">
              <div class="bg-white/10 rounded-2xl p-3 text-center">
                <div class="text-xl font-bold" id="displayTotal">0</div>
                <div class="text-xs text-blue-200 mt-0.5">Total</div>
              </div>
              <div class="bg-white/10 rounded-2xl p-3 text-center">
                <div class="text-xl font-bold text-green-300" id="displaySelesai">0</div>
                <div class="text-xs text-blue-200 mt-0.5">Selesai</div>
              </div>
              <div class="bg-white/10 rounded-2xl p-3 text-center">
                <div class="text-xl font-bold text-amber-300" id="displayMenunggu">0</div>
                <div class="text-xs text-blue-200 mt-0.5">Menunggu</div>
              </div>
            </div>
          </div>

          <!-- Panel kanan — ambil antrian cepat -->
          <div class="bg-white border-2 border-blue-900/15 rounded-3xl p-6 shadow-md flex flex-col justify-between">
            <div>
              <h3 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-900"></i> Antrian Saya
              </h3>
              <p class="text-xs text-gray-400 mb-4">Nomor antrian Anda saat ini.</p>
              <div class="text-center py-4">
                <div class="text-5xl font-black text-blue-900" id="antrianSaya">—</div>
                <p class="text-xs text-gray-400 mt-1" id="layananSaya">Belum mengambil antrian</p>
              </div>
            </div>
            <button onclick="openModal()" class="btn-anim w-full bg-blue-900 hover:bg-blue-800 text-white py-3 rounded-2xl font-semibold text-sm flex items-center justify-center gap-2 shadow-md mt-4">
              <i class="fas fa-ticket-alt"></i> Ambil Antrian
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CARA PESAN ===== -->
    <section class="py-16 bg-gradient-to-b from-white to-blue-50/50">
      <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Panduan</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Cara <span class="text-blue-900">Pesan Antrian</span></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          @php
            $steps = [
              ['no'=>'01','icon'=>'fa-id-card','title'=>'Siapkan No. RM','desc'=>'Pastikan Anda memiliki No. Rekam Medik yang terdaftar di Klinik QLINICA.'],
              ['no'=>'02','icon'=>'fa-ticket-alt','title'=>'Ambil Nomor','desc'=>'Klik tombol "Ambil Antrian", isi form singkat, dan nomor antrian akan diberikan.'],
              ['no'=>'03','icon'=>'fa-bell','title'=>'Pantau & Datang','desc'=>'Pantau nomor yang sedang dipanggil di halaman ini, lalu datang tepat waktu.'],
            ];
          @endphp
          @foreach($steps as $i => $s)
          <div class="card-hover bg-white rounded-3xl p-7 shadow-md border border-blue-900/10 relative overflow-hidden"
               data-aos="fade-up" data-aos-delay="{{ ($i+1)*100 }}">
            <div class="absolute top-4 right-4 text-5xl font-black text-blue-900/5 select-none">{{ $s['no'] }}</div>
            <div class="w-13 h-13 bg-blue-100 text-blue-900 rounded-2xl flex items-center justify-center mb-4 text-2xl w-12 h-12">
              <i class="fas {{ $s['icon'] }}"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-2">{{ $s['title'] }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed">{{ $s['desc'] }}</p>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- ===== DAFTAR ANTRIAN ===== -->
    <section id="daftar" class="py-16 bg-white">
      <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Daftar</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Daftar <span class="text-blue-900">Antrian</span> Hari Ini</h2>
        </div>

        <!-- Bar: search + tambah -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5" data-aos="fade-up">
          <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-4 py-2.5">
            <i class="fas fa-search text-gray-400 text-xs"></i>
            <input type="text" id="searchInput" placeholder="Cari nama atau No. RM..."
              class="bg-transparent text-sm outline-none w-44 text-gray-700">
          </div>
          <button onclick="openModal()"
            class="btn-anim bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md">
            <i class="fas fa-plus"></i> Ambil Antrian
          </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-md border border-blue-900/10 overflow-hidden" data-aos="fade-up">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]">
              <thead>
                <tr class="bg-blue-50 border-b border-blue-900/10">
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">No.</th>
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">No. RM</th>
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Nama</th>
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Layanan</th>
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Waktu</th>
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Status</th>
                  <th class="px-5 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody id="tbody">
                <tr>
                  <td colspan="7" class="px-5 py-14 text-center text-gray-400">
                    <i class="fas fa-ticket-alt text-3xl text-blue-900/20 mb-3 block"></i>
                    Belum ada antrian hari ini
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-400" id="pageInfo">0 antrian</span>
            <span class="text-xs text-gray-400">Periode: hari ini</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== LAYANAN TERSEDIA ===== -->
    <section class="py-16 bg-gradient-to-b from-blue-50/50 to-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Pilih Layanan</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Layanan yang <span class="text-blue-900">Tersedia</span></h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
          @php
            $layanan = [
              ['icon'=>'fa-heartbeat','warna'=>'blue','nama'=>'Umum'],
              ['icon'=>'fa-tooth','warna'=>'green','nama'=>'Gigi'],
              ['icon'=>'fa-flask','warna'=>'purple','nama'=>'Lab'],
              ['icon'=>'fa-syringe','warna'=>'amber','nama'=>'Vaksin'],
              ['icon'=>'fa-baby','warna'=>'pink','nama'=>'KIA/KB'],
              ['icon'=>'fa-truck-medical','warna'=>'red','nama'=>'Darurat'],
            ];
          @endphp
          @foreach($layanan as $i => $l)
          <div class="card-hover bg-white border border-blue-900/10 rounded-3xl p-5 shadow-sm text-center cursor-pointer"
               data-aos="fade-up" data-aos-delay="{{ ($i%3+1)*80 }}"
               onclick="pilihLayanan('{{ $l['nama'] }}')">
            <div class="w-12 h-12 bg-{{ $l['warna'] }}-100 text-{{ $l['warna'] }}-700 rounded-2xl flex items-center justify-center mx-auto mb-3 text-xl">
              <i class="fas {{ $l['icon'] }}"></i>
            </div>
            <p class="text-sm font-semibold text-gray-800">{{ $l['nama'] }}</p>
            <p class="text-xs text-blue-900 mt-1 font-medium flex items-center justify-center gap-1">
              Pilih <i class="fas fa-arrow-right text-xs"></i>
            </p>
          </div>
          @endforeach
        </div>
      </div>
    </section>

  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="bg-white border-t border-blue-900/10 py-8">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
      <div class="flex justify-center space-x-6 mb-3">
        <a href="/" class="text-blue-900 hover:text-blue-700 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
        <a href="#antrian" class="text-blue-900 hover:text-blue-700 transition"><i class="fas fa-ticket-alt mr-1"></i> Antrian</a>
      </div>
      <p>&copy; 2025 QLINICA — Pemesanan Antrian Online</p>
    </div>
  </footer>

  <!-- ===== MODAL AMBIL ANTRIAN ===== -->
  <div id="modalBg"
    class="modal-overlay fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 invisible">
    <div class="modal-box bg-white rounded-3xl shadow-2xl w-full max-w-md p-7 m-4">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-800">Ambil Nomor Antrian</h3>
          <p class="text-sm text-gray-400 mt-0.5">Isi form di bawah untuk mengambil antrian</p>
        </div>
        <button onclick="closeModal()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-600 flex items-center justify-center text-gray-500 transition text-lg">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="space-y-4">
        <div>
          <label class="text-xs font-bold text-gray-500 uppercase tracking-wide block mb-1.5">No. Rekam Medik</label>
          <input type="text" id="inNoRm" placeholder="Contoh: 02180"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 outline-none bg-gray-50">
        </div>
        <div>
          <label class="text-xs font-bold text-gray-500 uppercase tracking-wide block mb-1.5">Nama Lengkap</label>
          <input type="text" id="inNama" placeholder="Nama sesuai kartu identitas"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 outline-none bg-gray-50">
        </div>
        <div>
          <label class="text-xs font-bold text-gray-500 uppercase tracking-wide block mb-1.5">Jenis Layanan</label>
          <select id="inLayanan"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 outline-none bg-gray-50">
            <option value="">— Pilih Layanan —</option>
            <option value="Konsultasi Umum">Konsultasi Umum</option>
            <option value="Klinik Gigi">Klinik Gigi</option>
            <option value="Laboratorium">Laboratorium</option>
            <option value="Imunisasi / Vaksin">Imunisasi / Vaksin</option>
            <option value="KIA / KB">KIA / KB</option>
            <option value="Gawat Darurat">Gawat Darurat</option>
          </select>
        </div>
        <div>
          <label class="text-xs font-bold text-gray-500 uppercase tracking-wide block mb-1.5">No. Antrian</label>
          <input type="text" id="inNoAntrian" readonly
            class="w-full border border-gray-200 bg-blue-50 rounded-xl px-4 py-3 text-sm text-blue-900 font-bold outline-none cursor-not-allowed">
        </div>
      </div>

      <div class="flex gap-3 mt-6">
        <button onclick="closeModal()"
          class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
          Batal
        </button>
        <button onclick="simpanAntrian()"
          class="btn-anim flex-1 bg-blue-900 hover:bg-blue-800 text-white py-3 rounded-xl text-sm font-semibold shadow-md flex items-center justify-center gap-2">
          <i class="fas fa-ticket-alt"></i> Konfirmasi
        </button>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div id="toast"
    class="fixed bottom-5 right-5 bg-green-700 text-white px-5 py-3 rounded-2xl shadow-xl font-semibold text-sm flex items-center gap-2 z-50 opacity-0 translate-y-4 transition-all duration-300 pointer-events-none">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg">Berhasil</span>
  </div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ once: true, duration: 700, offset: 80 });

    // =============================================
    // STATE
    // =============================================
    let antrian    = [];
    let noCounter  = 1;
    let nomorAktif = null;

    // =============================================
    // MODAL
    // =============================================
    function openModal(layananDefault = '') {
      document.getElementById('inNoAntrian').value = noCounter;
      if (layananDefault) document.getElementById('inLayanan').value = layananDefault;
      document.getElementById('modalBg').classList.add('open');
    }
    function closeModal() {
      document.getElementById('modalBg').classList.remove('open');
    }
    document.getElementById('modalBg').addEventListener('click', e => {
      if (e.target === e.currentTarget) closeModal();
    });
    document.getElementById('btnAmbil').addEventListener('click', () => openModal());

    window.pilihLayanan = (nama) => {
      const map = { 'Umum':'Konsultasi Umum','Gigi':'Klinik Gigi','Lab':'Laboratorium','Vaksin':'Imunisasi / Vaksin','KIA/KB':'KIA / KB','Darurat':'Gawat Darurat' };
      openModal(map[nama] || nama);
      document.querySelector('#antrian').scrollIntoView({ behavior:'smooth' });
    };

    // =============================================
    // SIMPAN ANTRIAN
    // =============================================
    function simpanAntrian() {
      const rm      = document.getElementById('inNoRm').value.trim();
      const nama    = document.getElementById('inNama').value.trim();
      const layanan = document.getElementById('inLayanan').value;
      if (!rm)      { showToast('No. RM wajib diisi!','red'); return; }
      if (!nama)    { showToast('Nama wajib diisi!','red'); return; }
      if (!layanan) { showToast('Pilih jenis layanan!','red'); return; }

      const item = {
        no: noCounter++,
        rm, nama, layanan,
        waktu: new Date().toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }),
        status: 'Menunggu'
      };
      antrian.push(item);

      // Set nomor antrian saya
      document.getElementById('antrianSaya').textContent = String(item.no).padStart(3, '0');
      document.getElementById('layananSaya').textContent = item.layanan;

      // Set nomor display jika pertama
      if (!nomorAktif) {
        nomorAktif = item.no;
        document.getElementById('nomorDisplay').textContent = String(nomorAktif).padStart(3,'0');
        document.getElementById('layananDisplay').textContent = item.layanan;
        item.status = 'Terpanggil';
      }

      // Reset form
      document.getElementById('inNoRm').value    = '';
      document.getElementById('inNama').value    = '';
      document.getElementById('inLayanan').value = '';
      document.getElementById('inNoAntrian').value = noCounter;

      closeModal();
      render();
      showToast('Antrian berhasil diambil! No. ' + String(item.no).padStart(3,'0'));

      // scroll ke daftar
      setTimeout(() => document.getElementById('daftar').scrollIntoView({ behavior:'smooth' }), 400);
    }

    // =============================================
    // PANGGIL
    // =============================================
    window.panggil = (no) => {
      const item = antrian.find(a => a.no === no);
      if (!item || item.status === 'Terpanggil') return;
      item.status = 'Terpanggil';
      nomorAktif = no;
      document.getElementById('nomorDisplay').textContent = String(no).padStart(3,'0');
      document.getElementById('layananDisplay').textContent = item.layanan;
      render();
      showToast('Nomor ' + String(no).padStart(3,'0') + ' dipanggil!', 'green');
    };

    // =============================================
    // BATAL
    // =============================================
    window.batalAntrian = (no) => {
      const item = antrian.find(a => a.no === no);
      if (!item) return;
      item.status = 'Batal';
      render();
      showToast('Antrian ' + String(no).padStart(3,'0') + ' dibatalkan.', 'red');
    };

    // =============================================
    // RENDER TABLE
    // =============================================
    function render() {
      const q    = document.getElementById('searchInput').value.toLowerCase();
      const data = antrian.filter(a =>
        !q || a.nama.toLowerCase().includes(q) || a.rm.toLowerCase().includes(q)
      );

      const tunggu  = antrian.filter(a => a.status === 'Menunggu').length;
      const selesai = antrian.filter(a => a.status === 'Terpanggil').length;

      document.getElementById('statTotal').textContent    = antrian.length;
      document.getElementById('statSkrg').textContent     = nomorAktif ? String(nomorAktif).padStart(3,'0') : '—';
      document.getElementById('statMenunggu').textContent = tunggu;
      document.getElementById('statSelesai').textContent  = selesai;
      document.getElementById('displayTotal').textContent   = antrian.length;
      document.getElementById('displaySelesai').textContent = selesai;
      document.getElementById('displayMenunggu').textContent = tunggu;
      document.getElementById('pageInfo').textContent = data.length + ' antrian ditampilkan';

      const tbody = document.getElementById('tbody');
      if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-14 text-center text-gray-400">
          <i class="fas fa-ticket-alt text-3xl text-blue-900/20 mb-3 block"></i>
          Belum ada antrian hari ini</td></tr>`;
        return;
      }

      const clsBadge = { 'Menunggu':'badge-menunggu','Terpanggil':'badge-terpanggil','Batal':'badge-batal' };
      tbody.innerHTML = data.map(a => `
        <tr class="border-b border-gray-100 hover:bg-blue-50/40 transition">
          <td class="px-5 py-4 font-black text-blue-900 text-lg">${String(a.no).padStart(3,'0')}</td>
          <td class="px-5 py-4 text-sm font-mono text-gray-600">${a.rm}</td>
          <td class="px-5 py-4 font-semibold text-gray-800 text-sm">${a.nama}</td>
          <td class="px-5 py-4 text-sm text-gray-600">${a.layanan}</td>
          <td class="px-5 py-4 text-sm text-gray-500">${a.waktu}</td>
          <td class="px-5 py-4">
            <span class="text-xs font-bold px-3 py-1.5 rounded-full ${clsBadge[a.status]}">${a.status}</span>
          </td>
          <td class="px-5 py-4">
            <div class="flex items-center gap-2">
              ${a.status === 'Menunggu'
                ? `<button onclick="panggil(${a.no})"
                    class="bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                    <i class="fas fa-bell"></i> Panggil</button>
                   <button onclick="batalAntrian(${a.no})"
                    class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                    <i class="fas fa-times"></i> Batal</button>`
                : `<span class="text-xs text-gray-400">—</span>`
              }
            </div>
          </td>
        </tr>`).join('');
    }

    document.getElementById('searchInput').addEventListener('input', render);
    render();

    // =============================================
    // TOAST
    // =============================================
    function showToast(msg, color='green') {
      const colorMap = { green:'bg-green-700', red:'bg-red-600', amber:'bg-amber-600' };
      const t = document.getElementById('toast');
      t.className = `fixed bottom-5 right-5 ${colorMap[color]||'bg-green-700'} text-white px-5 py-3 rounded-2xl shadow-xl font-semibold text-sm flex items-center gap-2 z-50 transition-all duration-300 pointer-events-none opacity-100 translate-y-0`;
      document.getElementById('toastMsg').textContent = msg;
      setTimeout(() => {
        t.classList.add('opacity-0','translate-y-4');
        t.classList.remove('opacity-100','translate-y-0');
      }, 3500);
    }
  </script>
</body>
</html>