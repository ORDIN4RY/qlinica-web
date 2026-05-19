<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sahaduta | Portal Pasien</title>
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
    /* Tab active */
    .tab-btn.active { background:#1e3a8a; color:#fff; }
    /* Antrian number */
    .no-antrian { font-size:5rem; font-weight:900; line-height:1; }
    /* Status badge */
    .status-menunggu { background:#fef3c7; color:#92400e; }
    .status-selesai  { background:#d1fae5; color:#065f46; }
    .status-batal    { background:#fee2e2; color:#991b1b; }

    /* Star Rating */
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; }
    .star-rating input { display: none; }
    .star-rating label { color: #d1d5db; font-size: 2.25rem; padding: 0 0.25rem; cursor: pointer; transition: color 0.2s; }
    .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #facc15; }
  </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-700 overflow-x-hidden">

  <!-- ===== NAVBAR ===== -->
  <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-blue-900/10 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-blue-900 rounded-xl flex items-center justify-center">
            <i class="fas fa-clinic-medical text-white"></i>
          </div>
          <span class="font-semibold text-xl text-gray-800">Sahaduta</span>
        </div>

        <!-- Nav Links -->
        <nav class="hidden md:flex space-x-6 text-sm font-medium">
          <a href="#beranda"  class="text-gray-700 hover:text-blue-900 transition">Beranda</a>
          <a href="#antrian"  class="text-gray-700 hover:text-blue-900 transition">Antrian</a>
          <a href="#profil"   class="text-gray-700 hover:text-blue-900 transition">Profil</a>
          <a href="#riwayat"  class="text-gray-700 hover:text-blue-900 transition">Riwayat</a>
        </nav>

        <!-- User + Logout -->
        <div class="flex items-center gap-3">
          <div class="hidden sm:flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-full px-3 py-1.5">
            <div class="w-6 h-6 bg-blue-900 rounded-full flex items-center justify-center text-white text-xs font-bold" id="avatarInitial">{{ $pasien ? strtoupper(substr($pasien->no_rm, 0, 2)) : 'P' }}</div>
            <span class="text-sm font-semibold text-blue-900" id="namaUser">{{ $pasien->nama ?? 'Pasien' }}</span>
          </div>
          <form method="POST" action="/logout" id="logoutForm">
            @csrf
            <button type="submit" class="btn-anim bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5">
              <i class="fas fa-sign-out-alt"></i>
              <span class="hidden sm:inline">Logout</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <main>

    <!-- ===== HERO / GREETING ===== -->
    <section id="beranda" class="relative bg-gradient-to-br from-blue-50 via-white to-white overflow-hidden py-14 md:py-20">
      <div class="absolute top-10 right-0 w-80 h-80 bg-blue-900/5 rounded-full blur-3xl float-anim"></div>
      <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-900/8 rounded-full blur-2xl pulse-bg"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid md:grid-cols-2 gap-10 items-center">
          <!-- kiri -->
          <div class="space-y-5" data-aos="fade-right">
            <span class="inline-block bg-blue-100 text-blue-900 px-4 py-1.5 rounded-full text-sm font-semibold">👋 Selamat datang kembali</span>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
              Halo, <span class="text-blue-900" id="greetName">{{ $pasien->nama ?? 'Pasien' }}</span>! <br>
              Ada yang bisa kami bantu hari ini?
            </h1>
            <p class="text-gray-500 text-base max-w-md">Ambil antrian online, lihat riwayat kunjungan, atau perbarui data profil Anda dengan mudah.</p>
            <div class="flex flex-wrap gap-3 pt-2">
              <a href="#antrian" class="btn-anim bg-blue-900 hover:bg-blue-800 text-white px-6 py-3 rounded-2xl font-semibold shadow-md flex items-center gap-2">
                <i class="fas fa-ticket-alt"></i> Ambil Antrian
              </a>
              <a href="#riwayat" class="btn-anim bg-white border border-blue-900/30 hover:border-blue-900 text-blue-900 px-6 py-3 rounded-2xl font-semibold shadow-sm flex items-center gap-2">
                <i class="fas fa-history"></i> Lihat Riwayat
              </a>
            </div>
          </div>

          <!-- kanan — info cards -->
          <div class="grid grid-cols-2 gap-4" data-aos="fade-left">
            <div class="card-hover bg-white p-5 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-blue-100 text-blue-900 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-ticket-alt"></i>
              </div>
              <div class="text-2xl font-bold text-blue-900" id="noAntrianKu">{{ $antrianAktif ? str_pad($antrianAktif->no_antrian, 3, '0', STR_PAD_LEFT) : '—' }}</div>
              <div class="text-xs text-gray-500 mt-0.5">Antrian Aktif Saya</div>
            </div>
            <div class="card-hover bg-white p-5 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-users"></i>
              </div>
              <div class="text-2xl font-bold text-green-700" id="antrianSkrg">{{ $antrianDilayani ? str_pad($antrianDilayani->no_antrian, 3, '0', STR_PAD_LEFT) : '—' }}</div>
              <div class="text-xs text-gray-500 mt-0.5">Antrian Sedang Dilayani</div>
            </div>
            <div class="card-hover bg-white p-5 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-clock"></i>
              </div>
              <div class="text-2xl font-bold text-amber-700" id="estimasi">~{{ max(0, $antrianMenunggu * 5) }} mnt</div>
              <div class="text-xs text-gray-500 mt-0.5">Estimasi Tunggu</div>
            </div>
            <div class="card-hover bg-white p-5 rounded-3xl shadow-md border border-blue-900/15">
              <div class="w-11 h-11 bg-purple-100 text-purple-700 rounded-2xl flex items-center justify-center mb-3 text-xl">
                <i class="fas fa-calendar-check"></i>
              </div>
              <div class="text-2xl font-bold text-purple-700" id="totalKunjungan">5</div>
              <div class="text-xs text-gray-500 mt-0.5">Total Kunjungan</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== FITUR ANTRIAN ===== -->
    <section id="antrian" class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Online Queue</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Antrian <span class="text-blue-900">Online</span></h2>
          <p class="text-gray-500 mt-2 text-sm">Ambil nomor antrian tanpa perlu menunggu lama di klinik.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-start">
          <!-- Panel Ambil Antrian -->
          <div class="bg-white border-2 border-blue-900/15 rounded-3xl p-7 shadow-md" data-aos="fade-right">
            <h3 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
              <i class="fas fa-plus-circle text-blue-900"></i> Ambil Nomor Antrian
            </h3>

            <div id="formAntrian" class="space-y-4 {{ $antrianAktif ? 'hidden' : '' }}">
              <div>
                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Jenis Layanan</label>
                <select id="jenisLayanan" class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10 outline-none bg-gray-50">
                  <option value="">— Pilih Layanan —</option>
                  <option value="Konsultasi Umum">Konsultasi Umum</option>
                  <option value="Klinik Gigi">Klinik Gigi</option>
                  <option value="Laboratorium">Laboratorium</option>
                  <option value="Imunisasi / Vaksin">Imunisasi / Vaksin</option>
                  <option value="KIA / KB">KIA / KB</option>
                </select>
              </div>
              <div>
                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Keluhan (opsional)</label>
                <textarea id="keluhan" rows="3" placeholder="Deskripsikan keluhan Anda..." class="mt-1.5 w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-blue-900 outline-none resize-none bg-gray-50"></textarea>
              </div>
              <button onclick="ambilAntrian()" class="btn-anim w-full bg-blue-900 hover:bg-blue-800 text-white py-3 rounded-2xl font-semibold text-sm flex items-center justify-center gap-2 shadow-md">
                <i class="fas fa-ticket-alt"></i> Ambil Antrian Sekarang
              </button>
            </div>

            <!-- Hasil antrian -->
            <div id="hasilAntrian" class="{{ $antrianAktif ? '' : 'hidden' }} text-center">
              <div class="w-16 h-16 bg-green-100 text-green-700 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                <i class="fas fa-check-circle"></i>
              </div>
              <p class="text-sm text-gray-500 mb-1">Nomor Antrian Anda</p>
              <div class="no-antrian text-blue-900" id="nomorAntrian">{{ $antrianAktif ? str_pad($antrianAktif->no_antrian, 3, '0', STR_PAD_LEFT) : '—' }}</div>
              <p class="text-xs text-gray-500 mt-1 mb-1" id="layananDipilih">{{ $antrianAktif ? $antrianAktif->jenis : '' }}</p>
              <div class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200 text-blue-800 px-3 py-1.5 rounded-full text-xs font-semibold mt-1">
                <i class="fas fa-calendar-alt"></i> <span id="tanggalAntrian">{{ $antrianAktif ? \Carbon\Carbon::parse($antrianAktif->tanggal)->format('d M Y') : '' }}</span>
              </div>
              <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2 rounded-full text-sm font-semibold mt-2">
                <i class="fas fa-clock"></i> Estimasi: <span id="estimasiHasil">~15 mnt</span>
              </div>
              <div class="mt-3 inline-flex items-center gap-1.5 bg-green-50 border border-green-200 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold">
                <i class="fas fa-wifi"></i> Antrian Online
              </div>
              <button
                id="btnBatalAntrian"
                onclick="batalAntrian()"
                class="mt-4 w-full border border-red-300 text-red-600 hover:bg-red-50 py-2.5 rounded-xl text-sm font-semibold transition"
                @if($antrianAktif && strtolower($antrianAktif->status) === 'dipanggil')
                  disabled
                  title="Antrian Anda sedang dipanggil, tidak dapat dibatalkan"
                  style="opacity:0.45; cursor:not-allowed; pointer-events:none;"
                @endif
              >
                <i class="fas fa-times"></i>
                @if($antrianAktif && strtolower($antrianAktif->status) === 'dipanggil')
                  Sedang Dipanggil...
                @else
                  Batalkan Antrian
                @endif
              </button>
            </div>
          </div>

          <!-- Status Antrian Real-time -->
          <div class="bg-gradient-to-br from-blue-900 to-blue-800 rounded-3xl p-7 text-white shadow-xl" data-aos="fade-left">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2 justify-between">
              <span class="flex items-center gap-2"><i class="fas fa-broadcast-tower"></i> Status Antrian Hari Ini</span>
              <span id="liveBadge" class="flex items-center gap-1.5 text-xs font-bold bg-green-400/20 border border-green-300/40 text-green-200 px-2.5 py-1 rounded-full">
                <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse"></span> LIVE
              </span>
            </h3>
            <div class="text-center py-4">
              <p class="text-blue-200 text-sm mb-1">Sedang Dilayani</p>
              <div class="text-7xl font-black text-white leading-none" id="antrianDisplay">{{ $antrianDilayani ? str_pad($antrianDilayani->no_antrian, 3, '0', STR_PAD_LEFT) : '—' }}</div>
              <p class="text-blue-200 text-sm mt-2">{{ $antrianDilayani ? $antrianDilayani->jenis : '-' }}</p>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-4">
              <div class="bg-white/10 rounded-2xl p-3 text-center">
                <div class="text-xl font-bold" id="totalAntrianHari">{{ $totalAntrianHariIni }}</div>
                <div class="text-xs text-blue-200 mt-0.5">Total</div>
              </div>
              <div class="bg-white/10 rounded-2xl p-3 text-center">
                <div class="text-xl font-bold text-green-300" id="sudahDilayani">{{ $antrianSelesai }}</div>
                <div class="text-xs text-blue-200 mt-0.5">Selesai</div>
              </div>
              <div class="bg-white/10 rounded-2xl p-3 text-center">
                <div class="text-xl font-bold text-amber-300" id="menunggu">{{ $antrianMenunggu }}</div>
                <div class="text-xs text-blue-200 mt-0.5">Menunggu</div>
              </div>
            </div>

            <div class="mt-4 p-3 bg-white/10 rounded-2xl text-xs text-blue-200 flex items-center gap-2">
              <i class="fas fa-sync-alt" id="syncIcon"></i>
              <span id="lastUpdated">Memuat data...</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== PROFIL & RIWAYAT ===== -->
    <section id="profil" class="py-16 bg-gradient-to-b from-white to-blue-50/50">
      <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Akun Saya</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Profil & <span class="text-blue-900">Riwayat</span></h2>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-2 mb-6 bg-gray-100 p-1.5 rounded-2xl w-fit mx-auto">
          <button onclick="switchTab('profil')" id="tab-profil" class="tab-btn active px-5 py-2.5 rounded-xl text-sm font-semibold transition-all">
            <i class="fas fa-user mr-1.5"></i> Data Profil
          </button>
          <button onclick="switchTab('riwayat')" id="tab-riwayat" class="tab-btn px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 transition-all">
            <i class="fas fa-history mr-1.5"></i> Riwayat Pemesanan
          </button>
        </div>

        <!-- Tab: Profil -->
        <div id="panel-profil" data-aos="fade-up">
          <div class="bg-white rounded-3xl shadow-md border border-blue-900/10 overflow-hidden">
            <!-- Header profil -->
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 p-8 flex items-center gap-6">
              <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center text-white text-3xl font-black flex-shrink-0" id="avatarBesar">{{ $pasien ? strtoupper(substr($pasien->no_rm, 0, 2)) : 'P' }}</div>
              <div>
                <div class="text-white font-bold text-xl" id="namaLengkap">{{ $pasien->nama ?? '-' }}</div>
                <div class="text-blue-200 text-sm mt-0.5">No. RM: <span class="font-mono font-semibold text-white" id="noRm">{{ $pasien->no_rm ?? '-' }}</span></div>
                <div class="mt-2 inline-flex items-center gap-1.5 bg-green-400/20 border border-green-300/40 text-green-100 px-3 py-1 rounded-full text-xs font-semibold">
                  <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span> Pasien Aktif
                </div>
              </div>
            </div>

            <!-- Form data diri -->
            <div class="p-7">
              <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-800">Data Diri</h3>
                <button onclick="toggleEditProfil()" id="btnEdit" class="btn-anim bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-1.5">
                  <i class="fas fa-edit"></i> Edit Profil
                </button>
              </div>

              {{-- Alert profil --}}
              <div id="profilAlert" class="hidden mb-4 px-4 py-3 rounded-xl text-sm font-medium"></div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="gridProfil">

                {{-- Nama --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Lengkap <span class="text-red-400 hidden edit-required">*</span></label>
                  <input type="text" id="fNamaLengkap" value="{{ $pasien->nama ?? '' }}" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- No RM (readonly always) --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">No. Rekam Medik</label>
                  <input type="text" value="{{ $pasien->no_rm ?? '' }}" readonly
                    class="w-full border border-gray-200 bg-gray-100 rounded-xl px-4 py-3 text-sm text-gray-500 cursor-not-allowed outline-none">
                </div>

                {{-- NIK --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">NIK (16 digit)</label>
                  <input type="text" id="fNik" value="{{ $pasien->nik ?? '' }}" maxlength="16" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- Tanggal Lahir --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tanggal Lahir <span class="text-red-400 hidden edit-required">*</span></label>
                  <input type="date" id="fTglLahir" value="{{ $pasien->tgl_lahir ? $pasien->tgl_lahir->format('Y-m-d') : '' }}" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- Jenis Kelamin --}}
                <div class="space-y-1" id="wrapJenkel">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Jenis Kelamin <span class="text-red-400 hidden edit-required">*</span></label>
                  {{-- tampilan readonly --}}
                  <input type="text" id="fJenkelDisplay"
                    value="{{ $pasien->jenis_kelamin === 'L' ? 'Laki-laki' : ($pasien->jenis_kelamin === 'P' ? 'Perempuan' : '') }}"
                    readonly class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none">
                  {{-- dropdown saat edit --}}
                  <select id="fJenkel" class="profil-select hidden w-full border border-gray-200 bg-white rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                    <option value="L" {{ $pasien->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $pasien->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                  </select>
                </div>

                {{-- Golongan Darah --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Golongan Darah</label>
                  {{-- tampilan readonly --}}
                  <input type="text" id="fDarahDisplay" value="{{ $pasien->golongan_darah ?? '-' }}"
                    readonly class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 outline-none">
                  {{-- dropdown saat edit --}}
                  <select id="fDarah" class="profil-select hidden w-full border border-gray-200 bg-white rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                    <option value="">— Pilih —</option>
                    @foreach(['A','B','AB','O'] as $gol)
                      <option value="{{ $gol }}" {{ $pasien->golongan_darah === $gol ? 'selected' : '' }}>{{ $gol }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- Alamat --}}
                <div class="space-y-1 sm:col-span-2">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Alamat</label>
                  <input type="text" id="fAlamat" value="{{ $pasien->alamat ?? '' }}" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- Desa --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Desa / Kelurahan</label>
                  <input type="text" id="fDesa" value="{{ $pasien->desa ?? '' }}" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- Kota --}}
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Kota / Kabupaten</label>
                  <input type="text" id="fKota" value="{{ $pasien->kota ?? '' }}" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- Nama KK --}}
                <div class="space-y-1 sm:col-span-2">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Kepala Keluarga</label>
                  <input type="text" id="fNamaKk" value="{{ $pasien->nama_kk ?? '' }}" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition">
                </div>

                {{-- Riwayat Alergi --}}
                <div class="space-y-1 sm:col-span-2">
                  <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Riwayat Alergi</label>
                  <textarea id="fAlergi" rows="2" readonly
                    class="profil-input w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-800 focus:border-blue-900 outline-none transition resize-none">{{ $pasien->riwayat_alergi ?? '' }}</textarea>
                </div>

              </div>{{-- end grid --}}

              {{-- Tombol simpan (hidden saat readonly) --}}
              <div id="btnSaveRow" class="hidden mt-5 flex gap-3 justify-end">
                <button type="button" onclick="batalEditProfil()" class="border border-gray-300 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">Batal</button>
                <button type="button" onclick="simpanProfil()" id="btnSimpanProfil" class="btn-anim bg-blue-900 hover:bg-blue-800 text-white px-6 py-2.5 rounded-xl text-sm font-semibold shadow-md flex items-center gap-1.5">
                  <i class="fas fa-save"></i> Simpan Perubahan
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Riwayat -->
        <div id="panel-riwayat" class="hidden" data-aos="fade-up">
          <div id="riwayat" class="bg-white rounded-3xl shadow-md border border-blue-900/10 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
              <div>
                <h3 class="font-bold text-gray-800 text-lg">Riwayat Pemesanan</h3>
                <p class="text-sm text-gray-400">Semua kunjungan dan antrian yang pernah Anda buat</p>
              </div>
              <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
                <i class="fas fa-search text-gray-400 text-xs"></i>
                <input type="text" id="searchRiwayat" placeholder="Cari layanan..." class="bg-transparent text-sm outline-none w-36">
              </div>
            </div>
            <div class="divide-y divide-gray-100" id="listRiwayat"></div>
            <div class="p-4 text-center text-sm text-gray-400" id="riwayatEmpty" style="display:none">Tidak ada riwayat kunjungan</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== LAYANAN SECTION ===== -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold uppercase text-xs tracking-widest">Layanan</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Pilih <span class="text-blue-900">Layanan</span> Anda</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @php
            $layanan = [
              ['icon'=>'fa-heartbeat','warna'=>'blue','nama'=>'Konsultasi Umum','desc'=>'Dokter umum siap melayani konsultasi kesehatan Anda.'],
              ['icon'=>'fa-tooth','warna'=>'green','nama'=>'Klinik Gigi','desc'=>'Perawatan gigi modern dengan alat standar internasional.'],
              ['icon'=>'fa-flask','warna'=>'purple','nama'=>'Laboratorium','desc'=>'Pengecekan lab cepat, akurat, dan steril.'],
              ['icon'=>'fa-syringe','warna'=>'amber','nama'=>'Imunisasi / Vaksin','desc'=>'Program vaksinasi lengkap untuk semua usia.'],
              ['icon'=>'fa-baby','warna'=>'pink','nama'=>'KIA / KB','desc'=>'Layanan kesehatan ibu dan anak serta KB.'],
              ['icon'=>'fa-truck-medical','warna'=>'red','nama'=>'Gawat Darurat','desc'=>'Penanganan darurat 24 jam oleh tenaga ahli.'],
            ];
          @endphp
          @foreach($layanan as $i => $l)
          <div class="card-hover bg-white border border-blue-900/15 rounded-3xl p-7 shadow-sm cursor-pointer"
               data-aos="fade-up" data-aos-delay="{{ ($i%3+1)*100 }}"
               onclick="pilihLayanan('{{ $l['nama'] }}')">
            <div class="w-14 h-14 bg-{{ $l['warna'] }}-100 text-{{ $l['warna'] }}-700 rounded-2xl flex items-center justify-center mb-4 text-2xl">
              <i class="fas {{ $l['icon'] }}"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $l['nama'] }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed">{{ $l['desc'] }}</p>
            <div class="mt-4 flex items-center text-blue-900 text-sm font-medium gap-1 hover:gap-2 transition-all">
              Ambil Antrian <i class="fas fa-arrow-right text-xs"></i>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- ===== KRITIK & SARAN ===== -->
    {{-- <section id="feedback" class="py-16 bg-gradient-to-b from-white to-blue-50/30">
      <div class="max-w-3xl mx-auto px-4">
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="text-blue-900 font-semibold tracking-wider uppercase text-xs">Ulasan Anda</span>
          <h2 class="text-3xl font-bold text-gray-800 mt-2">Kritik & <span class="text-blue-900">Saran</span></h2>
          <p class="text-gray-500 mt-2 text-sm">Masukan Anda sangat berarti untuk peningkatan kualitas layanan kami.</p>
        </div>

        <form action="#" method="POST" class="bg-white p-8 md:p-10 rounded-[2rem] shadow-md border border-blue-900/10" data-aos="fade-up" data-aos-delay="100">
          <!-- Rating Bintang -->
          <div class="mb-8 text-center">
            <label class="block text-base font-semibold text-gray-700 mb-3">Seberapa puas Anda dengan layanan kami?</label>
            <div class="star-rating">
              <input type="radio" id="star5" name="rating" value="5" />
              <label for="star5" title="Sangat Puas"><i class="fas fa-star"></i></label>
              <input type="radio" id="star4" name="rating" value="4" />
              <label for="star4" title="Puas"><i class="fas fa-star"></i></label>
              <input type="radio" id="star3" name="rating" value="3" />
              <label for="star3" title="Cukup"><i class="fas fa-star"></i></label>
              <input type="radio" id="star2" name="rating" value="2" />
              <label for="star2" title="Kurang"><i class="fas fa-star"></i></label>
              <input type="radio" id="star1" name="rating" value="1" />
              <label for="star1" title="Sangat Kurang"><i class="fas fa-star"></i></label>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-5 mb-5">
            <div>
              <label class="block text-sm font-semibold text-gray-600 mb-2">Nama Lengkap</label>
              <input type="text" name="name" value="{{ $pasien->nama ?? '' }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition" placeholder="Masukkan nama Anda">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-600 mb-2">Email / No. HP (Opsional)</label>
              <input type="text" name="contact" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition" placeholder="Email atau No. HP">
            </div>
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-600 mb-2">Pesan, Kritik, atau Saran</label>
            <textarea name="message" rows="4" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition resize-none" placeholder="Tuliskan pengalaman Anda atau saran untuk kami..."></textarea>
          </div>

          <button type="submit" class="btn-anim w-full bg-blue-900 hover:bg-blue-800 text-white py-3.5 rounded-xl font-semibold text-base flex items-center justify-center gap-2 shadow-md">
            <i class="fas fa-paper-plane"></i> Kirim Ulasan
          </button>
        </form>
      </div>
    </section> --}}

  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="bg-white border-t border-blue-900/10 py-8">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
      <div class="flex justify-center space-x-6 mb-3">
        <a href="#" class="text-blue-900 hover:text-blue-700 transition"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-blue-900 hover:text-blue-700 transition"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-blue-900 hover:text-blue-700 transition"><i class="fab fa-whatsapp"></i></a>
      </div>
      <p>&copy; 2025 Sahaduta — Portal Pasien</p>
    </div>
  </footer>

  <!-- Modal Kritik & Saran Selesai Periksa -->
  <div id="modalFeedback" class="fixed inset-0 z-[60] hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
  <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 relative" id="modalFeedbackContent">
    
    <button onclick="closeModalFeedback()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
      <i class="fas fa-times text-xl"></i>
    </button>

    <div class="p-8">
      <div class="text-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800">Kritik & Saran</h3>
      </div>

      <form action="#" method="POST" id="formModalFeedback">
        <input type="hidden" id="feedbackAntrianId" value="">

        <!-- Rating -->
        <div class="mb-6 text-center">
          <label class="block text-base font-semibold text-gray-700 mb-3">
            Seberapa puas Anda dengan layanan kami?
          </label>

          <div class="star-rating justify-center">
            <input type="radio" id="m_star5" name="rating" value="5" />
            <label for="m_star5" title="Sangat Puas"><i class="fas fa-star"></i></label>

            <input type="radio" id="m_star4" name="rating" value="4" />
            <label for="m_star4" title="Puas"><i class="fas fa-star"></i></label>

            <input type="radio" id="m_star3" name="rating" value="3" />
            <label for="m_star3" title="Cukup"><i class="fas fa-star"></i></label>

            <input type="radio" id="m_star2" name="rating" value="2" />
            <label for="m_star2" title="Kurang"><i class="fas fa-star"></i></label>

            <input type="radio" id="m_star1" name="rating" value="1" />
            <label for="m_star1" title="Sangat Kurang"><i class="fas fa-star"></i></label>
          </div>
        </div>

        <!-- Input -->
        <div class="grid md:grid-cols-3 gap-5 mb-5">
          
          <!-- Nama lebih lebar -->
          <div class="md:col-span-5">
            <label class="block text-sm font-semibold text-gray-600 mb-2">
              Nama Lengkap
            </label>

            <input 
              type="text" 
              name="name" 
              value="{{ $pasien->nama ?? '' }}"
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition"
              placeholder="Masukkan nama Anda"
            >
          </div>
        </div>

        <!-- Kritik -->
        <div class="mb-5">
          <label class="block text-sm font-semibold text-gray-600 mb-2">
            Kritik
          </label>

          <textarea 
            name="kritik" 
            rows="3" 
            required
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition resize-none"
            placeholder="Tuliskan kritik Anda..."
          ></textarea>
        </div>

        <!-- Saran -->
        <div class="mb-6">
          <label class="block text-sm font-semibold text-gray-600 mb-2">
            Saran
          </label>

          <textarea 
            name="saran" 
            rows="3"
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-blue-900 outline-none transition resize-none"
            placeholder="Tuliskan saran Anda..."
          ></textarea>
        </div>

        <!-- Button -->
        <button 
          type="button" 
          id="btnSubmitFeedback"
          onclick="submitModalFeedback()"
          class="btn-anim w-full bg-blue-900 hover:bg-blue-800 text-white py-3.5 rounded-xl font-semibold text-base flex items-center justify-center gap-2 shadow-md"
        >
          <i class="fas fa-paper-plane"></i> Kirim Ulasan
        </button>

      </form>
    </div>
  </div>
</div>

  <!-- Toast notification -->
  <div id="toast" class="fixed bottom-5 right-5 bg-green-700 text-white px-5 py-3 rounded-2xl shadow-xl font-semibold text-sm flex items-center gap-2 z-50 opacity-0 translate-y-4 transition-all duration-300 pointer-events-none">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg">Berhasil</span>
  </div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ once: true, duration: 700, offset: 80 });

    // ================================================================
    // DATA PASIEN (dari server via Blade)
    // ================================================================
    // Data sudah dirender langsung via Blade di HTML, tidak perlu set via JS

    // ================================================================
    // DATA RIWAYAT
    // ================================================================
    const riwayatData = {!! json_encode($riwayatAntrian->map(function($r) {
        $dokter = '—';
        if ($r->rekamMedis && $r->rekamMedis->dokter) {
            $dokter = $r->rekamMedis->dokter->nama ?? '—';
        }
        return [
            'id' => $r->id,
            'tanggal' => \Carbon\Carbon::parse($r->tanggal)->format('Y-m-d'),
            'layanan' => 'Layanan Klinik', // Placeholder as backend overwrites jenis to 'Online'
            'dokter' => $dokter,
            'noAntrian' => $r->jenis === 'Online' ? 'O-'.str_pad($r->no_antrian, 3, '0', STR_PAD_LEFT) : str_pad($r->no_antrian, 3, '0', STR_PAD_LEFT),
            'status' => strtolower($r->status),
            'keluhan' => $r->keluhan ?? '—'
        ];
    })) !!};

    document.getElementById('totalKunjungan').textContent = riwayatData.filter(r=>r.status==='selesai').length;

    function renderRiwayat(filter='') {
      const list = document.getElementById('listRiwayat');
      const empty = document.getElementById('riwayatEmpty');
      const items = riwayatData.filter(r => !filter || r.layanan.toLowerCase().includes(filter.toLowerCase()));
      if (!items.length) { list.innerHTML=''; empty.style.display='block'; return; }
      empty.style.display='none';
      const statusCls = { selesai:'status-selesai', batal:'status-batal', menunggu:'status-menunggu' };
      const statusLbl = { selesai:'Selesai', batal:'Batal', menunggu:'Menunggu' };
      list.innerHTML = items.map(r => `
        <div class="p-5 flex items-center justify-between gap-4 hover:bg-gray-50 transition group">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-900 rounded-2xl flex items-center justify-center flex-shrink-0 text-sm font-bold group-hover:bg-blue-900 group-hover:text-white transition">
              ${r.noAntrian.split('-')[0]}
            </div>
            <div>
              <div class="font-semibold text-gray-800 text-sm">${r.layanan}</div>
              <div class="text-xs text-gray-400 mt-0.5">${r.tanggal} · Antrian ${r.noAntrian} · ${r.dokter}</div>
              <div class="text-xs text-gray-500 mt-0.5 italic">${r.keluhan}</div>
            </div>
          </div>
          <div class="flex flex-col items-end gap-2">
            <span class="text-xs font-bold px-3 py-1.5 rounded-full flex-shrink-0 ${statusCls[r.status]}">${statusLbl[r.status]}</span>
            ${r.status === 'selesai' ? `<button onclick="showModalFeedback(${r.id})" class="text-blue-600 hover:text-blue-800 text-xs flex items-center gap-1 font-semibold hover:underline transition"><i class="fas fa-star text-yellow-400"></i> Beri Ulasan</button>` : ''}
          </div>
        </div>`).join('');
    }
    renderRiwayat();
    document.getElementById('searchRiwayat').addEventListener('input', e => renderRiwayat(e.target.value));

    // ================================================================
    // TAB SWITCH
    // ================================================================
    function switchTab(tab) {
      ['profil','riwayat'].forEach(t => {
        document.getElementById('panel-'+t).classList.toggle('hidden', t!==tab);
        document.getElementById('tab-'+t).classList.toggle('active', t===tab);
        if(t!==tab) document.getElementById('tab-'+t).classList.add('text-gray-600');
        else document.getElementById('tab-'+t).classList.remove('text-gray-600');
      });
    }

    // ================================================================
    // MODAL FEEDBACK
    // ================================================================
    function showModalFeedback(id = null) {
      const modal = document.getElementById('modalFeedback');
      const content = document.getElementById('modalFeedbackContent');
      
      document.getElementById('feedbackAntrianId').value = id;
      document.getElementById('formModalFeedback').reset();
      
      modal.classList.remove('hidden');
      // Trigger reflow
      void modal.offsetWidth;
      modal.classList.remove('opacity-0');
      content.classList.remove('scale-95');
      content.classList.add('scale-100');
    }

    function closeModalFeedback() {
      const modal = document.getElementById('modalFeedback');
      const content = document.getElementById('modalFeedbackContent');
      modal.classList.add('opacity-0');
      content.classList.remove('scale-100');
      content.classList.add('scale-95');
      setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    async function submitModalFeedback() {
      const antrianId = document.getElementById('feedbackAntrianId').value;
      const rating = document.querySelector('#formModalFeedback input[name="rating"]:checked')?.value;
      const kritik = document.querySelector('#formModalFeedback textarea[name="kritik"]')?.value ?? '';
      const saran  = document.querySelector('#formModalFeedback textarea[name="saran"]')?.value ?? '';

      if (!antrianId) {
        showToast('Terjadi kesalahan, antrian tidak valid!', 'red');
        return;
      }
      if (!rating) {
        showToast('Silakan berikan rating (bintang) terlebih dahulu!', 'red');
        return;
      }

      const btn = document.getElementById('btnSubmitFeedback');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

      try {
        const response = await fetch("{{ route('pasien.antrian.feedback') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            antrian_id: antrianId,
            rating: parseInt(rating),
            kritik: kritik,
            saran: saran
          })
        });

        const data = await response.json();

        if (data.success) {
          showToast('Terima kasih atas ulasan Anda! 🎉', 'green');
          closeModalFeedback();
        } else {
          showToast(data.message || 'Gagal mengirim ulasan.', 'red');
        }
      } catch (err) {
        console.error(err);
        showToast('Terjadi kesalahan jaringan.', 'red');
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    }

    // ================================================================
    // ANTRIAN
    // ================================================================
    let antrianAktif = {!! json_encode($antrianAktif ? ['id' => $antrianAktif->id, 'nomor' => str_pad($antrianAktif->no_antrian, 3, '0', STR_PAD_LEFT), 'layanan' => $antrianAktif->jenis] : null) !!};

    async function ambilAntrian() {
      const layanan = document.getElementById('jenisLayanan').value;
      const keluhan = document.getElementById('keluhan').value;
      if (!layanan) { showToast('Pilih jenis layanan terlebih dahulu!', 'red'); return; }

      // Tampilkan loading state
      const btn = document.querySelector('#formAntrian button[onclick="ambilAntrian()"]');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

      try {
        const response = await fetch("{{ route('pasien.antrian.store') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ jenis: layanan, keluhan: keluhan })
        });
        const data = await response.json();

        if (data.success) {
          // Gunakan no_antrian dari server (format "001")
          antrianAktif = { id: data.id, nomor: data.no_antrian, layanan };

          document.getElementById('nomorAntrian').textContent  = data.no_antrian;
          document.getElementById('layananDipilih').textContent = data.layanan;
          document.getElementById('tanggalAntrian').textContent = data.tanggal;
          document.getElementById('noAntrianKu').textContent   = data.no_antrian;
          document.getElementById('estimasiHasil').textContent = '~15 mnt';
          document.getElementById('formAntrian').classList.add('hidden');
          document.getElementById('hasilAntrian').classList.remove('hidden');

          // Update statistik lokal sementara
          const total  = parseInt(document.getElementById('totalAntrianHari').textContent) + 1;
          const tunggu = parseInt(document.getElementById('menunggu').textContent) + 1;
          document.getElementById('totalAntrianHari').textContent = total;
          document.getElementById('menunggu').textContent = tunggu;

          // Tambah ke riwayat lokal sementara
          riwayatData.unshift({
            id: Date.now(),
            tanggal: new Date().toLocaleDateString('en-CA'),
            layanan: data.layanan,
            dokter: '—',
            noAntrian: data.no_antrian,
            status: 'menunggu',
            keluhan: keluhan || '—'
          });
          renderRiwayat(document.getElementById('searchRiwayat').value);
          document.getElementById('totalKunjungan').textContent = riwayatData.filter(r => r.status === 'selesai').length;

          showToast(data.message);
        } else {
          showToast(data.message || 'Gagal mengambil antrian', 'red');
          // Kembalikan tombol
          btn.disabled = false;
          btn.innerHTML = '<i class="fas fa-ticket-alt"></i> Ambil Antrian Sekarang';
        }
      } catch (err) {
        showToast('Terjadi kesalahan pada server', 'red');
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-ticket-alt"></i> Ambil Antrian Sekarang';
      }
    }

    async function batalAntrian() {
      if (!antrianAktif || !antrianAktif.id) {
        showToast('Tidak ada antrian aktif.', 'red');
        return;
      }
      if (!confirm('Batalkan antrian ' + antrianAktif.nomor + '?\nData antrian akan dihapus permanen.')) return;

      const btnBatal = document.querySelector('#hasilAntrian button[onclick="batalAntrian()"]');
      if (btnBatal) {
        btnBatal.disabled = true;
        btnBatal.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membatalkan...';
      }

      try {
        const response = await fetch(`/dashboard-pasien/antrian/${antrianAktif.id}/cancel`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });

        let data;
        try {
          data = await response.json();
        } catch (parseErr) {
          throw new Error('Response bukan JSON. Status: ' + response.status);
        }

        if (data.success) {
          // Hapus dari riwayat lokal
          const idx = riwayatData.findIndex(r => r.noAntrian === antrianAktif.nomor && r.status === 'menunggu');
          if (idx !== -1) riwayatData.splice(idx, 1);

          // Reset state
          const nomorBatal = antrianAktif.nomor;
          antrianAktif = null;

          // Reset UI — tampilkan form kembali
          document.getElementById('noAntrianKu').textContent = '—';
          document.getElementById('jenisLayanan').value = '';
          document.getElementById('keluhan').value = '';
          document.getElementById('formAntrian').classList.remove('hidden');
          document.getElementById('hasilAntrian').classList.add('hidden');

          // Restore tombol
          if (btnBatal) {
            btnBatal.disabled = false;
            btnBatal.innerHTML = '<i class="fas fa-times"></i> Batalkan Antrian';
          }

          // Update statistik lokal
          const tunggu = parseInt(document.getElementById('menunggu').textContent);
          document.getElementById('menunggu').textContent = Math.max(0, tunggu - 1);

          renderRiwayat(document.getElementById('searchRiwayat').value);
          showToast('Antrian ' + nomorBatal + ' berhasil dibatalkan.', 'red');

        } else {
          showToast(data.message || 'Gagal membatalkan antrian.', 'red');
          if (btnBatal) {
            btnBatal.disabled = false;
            btnBatal.innerHTML = '<i class="fas fa-times"></i> Batalkan Antrian';
          }
        }
      } catch (err) {
        console.error('batalAntrian error:', err);
        showToast('Terjadi kesalahan: ' + err.message, 'red');
        if (btnBatal) {
          btnBatal.disabled = false;
          btnBatal.innerHTML = '<i class="fas fa-times"></i> Batalkan Antrian';
        }
      }
    }

    function pilihLayanan(nama) {
      document.getElementById('jenisLayanan').value = nama;
      document.querySelector('#antrian').scrollIntoView({behavior:'smooth'});
    }

    // ================================================================
    // EDIT PROFIL
    // ================================================================
    const PROFIL_URL  = '{{ route("pasien.profil.update") }}';
    const PROFIL_CSRF = '{{ csrf_token() }}';

    function toggleEditProfil() {
      // Aktifkan semua input
      document.querySelectorAll('.profil-input').forEach(el => {
        if (el.tagName === 'TEXTAREA') el.readOnly = false;
        else el.readOnly = false;
        el.classList.add('border-blue-300', 'bg-white');
        el.classList.remove('bg-gray-50');
      });
      // Tampilkan select dropdown, sembunyikan input display
      document.getElementById('fJenkelDisplay').classList.add('hidden');
      document.getElementById('fJenkel').classList.remove('hidden');
      document.getElementById('fDarahDisplay').classList.add('hidden');
      document.getElementById('fDarah').classList.remove('hidden');
      // Tanda bintang required
      document.querySelectorAll('.edit-required').forEach(el => el.classList.remove('hidden'));
      document.getElementById('btnEdit').classList.add('hidden');
      document.getElementById('btnSaveRow').classList.remove('hidden');
      document.getElementById('profilAlert').classList.add('hidden');
    }

    function batalEditProfil() {
      document.querySelectorAll('.profil-input').forEach(el => {
        el.readOnly = true;
        el.classList.remove('border-blue-300', 'bg-white');
        el.classList.add('bg-gray-50');
      });
      document.getElementById('fJenkelDisplay').classList.remove('hidden');
      document.getElementById('fJenkel').classList.add('hidden');
      document.getElementById('fDarahDisplay').classList.remove('hidden');
      document.getElementById('fDarah').classList.add('hidden');
      document.querySelectorAll('.edit-required').forEach(el => el.classList.add('hidden'));
      document.getElementById('btnEdit').classList.remove('hidden');
      document.getElementById('btnSaveRow').classList.add('hidden');
      document.getElementById('profilAlert').classList.add('hidden');
    }

    async function simpanProfil() {
      const nama    = document.getElementById('fNamaLengkap').value.trim();
      const tgl     = document.getElementById('fTglLahir').value;
      const jenkel  = document.getElementById('fJenkel').value;

      if (!nama || !tgl || !jenkel) {
        showProfilAlert('Nama, tanggal lahir, dan jenis kelamin wajib diisi.', 'red');
        return;
      }

      const btn = document.getElementById('btnSimpanProfil');
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

      const payload = {
        _method:        'PUT',
        nama,
        nik:            document.getElementById('fNik').value.trim() || null,
        tgl_lahir:      tgl,
        jenis_kelamin:  jenkel,
        golongan_darah: document.getElementById('fDarah').value || null,
        alamat:         document.getElementById('fAlamat').value.trim() || null,
        desa:           document.getElementById('fDesa').value.trim() || null,
        kota:           document.getElementById('fKota').value.trim() || null,
        nama_kk:        document.getElementById('fNamaKk').value.trim() || null,
        riwayat_alergi: document.getElementById('fAlergi').value.trim() || null,
      };

      try {
        const res  = await fetch(PROFIL_URL, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': PROFIL_CSRF, 'Accept': 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
          const p = data.pasien;
          // Update display values
          document.getElementById('namaLengkap').textContent = p.nama;
          document.getElementById('namaUser').textContent    = p.nama;
          document.getElementById('greetName').textContent   = p.nama;
          // Update display-only fields
          document.getElementById('fJenkelDisplay').value  = p.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
          document.getElementById('fDarahDisplay').value   = p.golongan_darah || '-';
          batalEditProfil();
          showToast('Profil berhasil diperbarui!');
        } else {
          const errors = data.errors
            ? Object.values(data.errors).flat().join(' | ')
            : (data.message || 'Gagal menyimpan.');
          showProfilAlert(errors, 'red');
        }
      } catch (err) {
        showProfilAlert('Terjadi kesalahan koneksi.', 'red');
        console.error(err);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
      }
    }

    function showProfilAlert(msg, type) {
      const box = document.getElementById('profilAlert');
      const cls = { red: 'bg-red-50 border border-red-300 text-red-700', green: 'bg-green-50 border border-green-300 text-green-700' };
      box.className = `mb-4 px-4 py-3 rounded-xl text-sm font-medium ${cls[type]}`;
      box.textContent = msg;
      box.classList.remove('hidden');
      setTimeout(() => box.classList.add('hidden'), 5000);
    }

    // ================================================================
    // TOAST
    // ================================================================
    function showToast(msg, color='green') {
      const t = document.getElementById('toast');
      const colorMap = { green:'bg-green-700', red:'bg-red-700', amber:'bg-amber-600' };
      t.className = `fixed bottom-5 right-5 ${colorMap[color]||'bg-green-700'} text-white px-5 py-3 rounded-2xl shadow-xl font-semibold text-sm flex items-center gap-2 z-50 transition-all duration-300 pointer-events-none opacity-100 translate-y-0`;
      document.getElementById('toastMsg').textContent = msg;
      setTimeout(() => { t.classList.add('opacity-0','translate-y-4'); t.classList.remove('opacity-100','translate-y-0'); }, 3000);
    }

    // ================================================================
    // POLLING REALTIME — Status Antrian (setiap 5 detik)
    // ================================================================
    const POLL_URL  = '{{ route("pasien.antrian.status") }}';
    const POLL_CSRF = '{{ csrf_token() }}';
    let   pollTimer = null;

    function formatTime(date) {
      return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }

    function animateChange(el, newVal) {
      if (el.textContent === String(newVal)) return;
      el.style.transform = 'scale(1.2)';
      el.style.transition = 'transform 0.2s ease';
      el.textContent = newVal;
      setTimeout(() => { el.style.transform = 'scale(1)'; }, 200);
    }

    async function pollAntrianStatus() {
      const syncIcon = document.getElementById('syncIcon');
      const lastUpdated = document.getElementById('lastUpdated');

      try {
        syncIcon.classList.add('fa-spin');

        const res  = await fetch(POLL_URL, {
          headers: { 'X-CSRF-TOKEN': POLL_CSRF, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        // — Nomor sedang dilayani —
        const displayEl = document.getElementById('antrianDisplay');
        const skrgEl    = document.getElementById('antrianSkrg');
        const newDisplay = data.dilayani ? data.dilayani.no_antrian : '—';
        if (displayEl.textContent !== newDisplay) {
          displayEl.style.transition = 'opacity 0.3s';
          displayEl.style.opacity    = '0';
          setTimeout(() => {
            displayEl.textContent  = newDisplay;
            displayEl.style.opacity = '1';
          }, 150);
          // Notif jika antrian aktif pasien dipanggil
          if (data.antrian_aktif && data.antrian_aktif.status === 'Dipanggil' &&
              antrianAktif && data.antrian_aktif.no_antrian === antrianAktif.nomor) {
            showToast('🔔 Antrian Anda ' + antrianAktif.nomor + ' sedang dipanggil!', 'amber');
          }
        }

        // — Disable/enable tombol Batalkan Antrian berdasarkan status —
        const btnBatal = document.getElementById('btnBatalAntrian');
        if (btnBatal) {
          const statusAktif = data.antrian_aktif ? data.antrian_aktif.status : null;
          const isDipanggil = statusAktif && statusAktif.toLowerCase() === 'dipanggil';
          if (isDipanggil) {
            btnBatal.disabled = true;
            btnBatal.style.opacity = '0.45';
            btnBatal.style.cursor  = 'not-allowed';
            btnBatal.style.pointerEvents = 'none';
            btnBatal.title = 'Antrian Anda sedang dipanggil, tidak dapat dibatalkan';
            btnBatal.innerHTML = '<i class="fas fa-bell fa-shake"></i> Sedang Dipanggil...';
          } else {
            btnBatal.disabled = false;
            btnBatal.style.opacity = '';
            btnBatal.style.cursor  = '';
            btnBatal.style.pointerEvents = '';
            btnBatal.title = '';
            btnBatal.innerHTML = '<i class="fas fa-times"></i> Batalkan Antrian';
            }
        }
        if (skrgEl) animateChange(skrgEl, newDisplay);

        // — Statistik —
        animateChange(document.getElementById('totalAntrianHari'), data.total);
        animateChange(document.getElementById('sudahDilayani'), data.selesai);
        animateChange(document.getElementById('menunggu'), data.menunggu);

        // — Estimasi tunggu —
        const estimasiEl = document.getElementById('estimasi');
        if (estimasiEl) animateChange(estimasiEl, '~' + Math.max(0, data.menunggu * 5) + ' mnt');

        // — Daftar pasien menunggu —
        const listEl = document.querySelector('.space-y-2.max-h-40');
        if (listEl) {
          if (!data.daftar_menunggu.length) {
            listEl.innerHTML = '<div class="text-center text-xs text-blue-200 py-2">Belum ada pasien yang mengantri.</div>';
          } else {
            listEl.innerHTML = data.daftar_menunggu.map(a => `
              <div class="bg-white/10 rounded-xl p-3 flex justify-between items-center ${
                antrianAktif && a.no_antrian === antrianAktif.nomor ? 'ring-2 ring-green-400/60' : ''
              }">
                <div>
                  <div class="font-bold text-sm">${a.nama}</div>
                  <div class="text-xs text-blue-200">${a.jenis}</div>
                </div>
                <div class="text-lg font-black text-white">${a.no_antrian}</div>
              </div>`).join('');
          }
        }

        // — Antrian aktif pasien (sinkron jika berubah dari luar, misal dibatalkan admin) —
        if (!data.antrian_aktif && antrianAktif) {
          // Antrian dihapus/dibatalkan dari sisi admin
          antrianAktif = null;
          document.getElementById('noAntrianKu').textContent = '—';
          document.getElementById('formAntrian').classList.remove('hidden');
          document.getElementById('hasilAntrian').classList.add('hidden');
          showToast('Antrian Anda telah berakhir atau dibatalkan.', 'red');
        } else if (data.antrian_aktif && !antrianAktif) {
          // Antrian baru terdeteksi (misal dibuat dari tab lain)
          antrianAktif = { id: data.antrian_aktif.id, nomor: data.antrian_aktif.no_antrian, layanan: data.antrian_aktif.jenis };
          document.getElementById('noAntrianKu').textContent = antrianAktif.nomor;
          document.getElementById('nomorAntrian').textContent = antrianAktif.nomor;
          document.getElementById('formAntrian').classList.add('hidden');
          document.getElementById('hasilAntrian').classList.remove('hidden');
        }

        lastUpdated.textContent = 'Update: ' + formatTime(new Date());
        document.getElementById('liveBadge').style.opacity = '1';

      } catch (err) {
        console.warn('Polling error:', err);
        lastUpdated.textContent = 'Gagal terhubung...';
        document.getElementById('liveBadge').style.opacity = '0.4';
      } finally {
        syncIcon.classList.remove('fa-spin');
      }
    }

    // Jalankan polling pertama kali langsung, lalu tiap 5 detik
    pollAntrianStatus();
    pollTimer = setInterval(pollAntrianStatus, 5000);

    // Hentikan polling saat tab tidak aktif (hemat resource), mulai lagi saat aktif
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        clearInterval(pollTimer);
      } else {
        pollAntrianStatus();
        pollTimer = setInterval(pollAntrianStatus, 5000);
      }
    });
  </script>
</body>
</html>
