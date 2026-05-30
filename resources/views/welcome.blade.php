<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QLINICA | Klinik Sehat</title>
  <!-- Tailwind via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- AOS (Animate On Scroll) Library -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    html { scroll-behavior: smooth; }
    
    /* Animasi halus yang sudah disepakati */
    .service-card {
      transition: all 0.4s cubic-bezier(0.15, 0.75, 0.45, 1);
    }
    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 35px -12px rgba(30, 58, 138, 0.25);
    }
    
    .float-subtle {
      animation: floatSubtle 7s ease-in-out infinite;
    }
    @keyframes floatSubtle {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-12px); }
      100% { transform: translateY(0px); }
    }
    
    .pulse-soft {
      animation: pulseSoft 5s infinite;
    }
    @keyframes pulseSoft {
      0% { opacity: 0.25; }
      50% { opacity: 0.45; }
      100% { opacity: 0.25; }
    }
    
    .hover-scale-subtle {
      transition: transform 0.3s ease;
    }
    .hover-scale-subtle:hover {
      transform: scale(1.02);
    }
    
    .btn-subtle {
      transition: all 0.3s ease;
    }
    .btn-subtle:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px -8px rgba(30, 58, 138, 0.4);
    }
    
    .testimonial-card {
      transition: all 0.3s ease;
    }
    .testimonial-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 25px -10px rgba(30, 58, 138, 0.15);
    }

    /* Testimonial Slider */
    .testi-slider-wrapper {
      overflow: hidden;
      position: relative;
    }
    .testi-slider-track {
      display: flex;
      transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      will-change: transform;
    }
    .testi-slide {
      min-width: calc(100% / 3);
      padding: 0 10px;
      box-sizing: border-box;
    }
    @media (max-width: 1024px) {
      .testi-slide { min-width: 50%; }
    }
    @media (max-width: 640px) {
      .testi-slide { min-width: 100%; }
    }
    .testi-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: #cbd5e1;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      padding: 0;
    }
    .testi-dot.active {
      background-color: #1e3a8a;
      width: 24px;
      border-radius: 4px;
    }
    .testi-nav-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 2px solid rgba(30, 58, 138, 0.2);
      background: white;
      color: #1e3a8a;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }
    .testi-nav-btn:hover {
      background: #1e3a8a;
      color: white;
      border-color: #1e3a8a;
    }
    
    .nav-link {
      position: relative;
    }
    .nav-link:after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      right: 0;
      height: 2px;
      background-color: #1e3a8a;
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }
    .nav-link:hover:after {
      transform: scaleX(1);
    }
    
    /* Style untuk modal/login overlay */
    .auth-modal {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      z-index: 100;
      opacity: 0;
      visibility: hidden;
      /* Mobile: sheet dari bawah */
      display: flex;
      align-items: flex-end;
      justify-content: center;
      transition: opacity 0.3s ease, visibility 0.3s ease;
      box-sizing: border-box;
    }
    .auth-modal.show {
      opacity: 1;
      visibility: visible;
    }

    /* Modal content — mobile default: bottom sheet */
    .auth-modal .modal-content {
      width: 100%;
      max-width: 100%;
      margin: 0;
      border-radius: 24px 24px 0 0;
      max-height: 96dvh;
      max-height: 96vh;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      padding-bottom: env(safe-area-inset-bottom, 0);
      transform: translateY(40px);
      transition: transform 0.4s cubic-bezier(0.15, 0.75, 0.45, 1);
      box-sizing: border-box;
    }
    .auth-modal.show .modal-content {
      transform: translateY(0);
    }

    .auth-modal *, .auth-modal *::before, .auth-modal *::after {
      box-sizing: border-box;
    }

    /* Desktop: tengah layar, rounded semua sisi */
    @media (min-width: 640px) {
      .auth-modal {
        align-items: center;
        padding: 16px;
      }
      .auth-modal .modal-content {
        max-width: 448px;
        border-radius: 24px;
        max-height: 90vh;
        padding-bottom: 0;
        transform: scale(0.95);
      }
      .auth-modal.show .modal-content {
        transform: scale(1);
      }
    }

    /* Cegah auto-zoom iOS saat tap input */
    .input-focus {
      transition: all 0.2s;
      font-size: max(16px, 0.875rem);
    }
    .input-focus:focus {
      border-color: #1e3a8a;
      box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
      outline: none;
    }

    /* Responsive fixes */
    @media (max-width: 768px) {
      .hero-content { text-align: center; }
      .hero-stats   { justify-content: center; }
    }
  </style>
</head>
<body class="bg-white font-sans antialiased text-gray-700 overflow-x-hidden">

  <!-- ===== NAVBAR ===== dengan tombol Login saja ===== -->
  <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-blue-900/10 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-2 group">
          <img src="{{ asset('favicon.png') }}" alt="QLINICA" class="w-8 h-8 transition-transform duration-500 group-hover:rotate-3">
          <span class="font-semibold text-xl text-gray-800">QLINICA</span>
        </div>
        
        <!-- nav links - hidden on mobile, visible on md -->
        <nav class="hidden md:flex space-x-8 text-sm font-medium">
          <a href="#home" class="nav-link text-gray-700 hover:text-blue-900 transition">Beranda</a>
          <a href="#layanan" class="nav-link text-gray-700 hover:text-blue-900 transition">Layanan</a>
          <a href="#tentang" class="nav-link text-gray-700 hover:text-blue-900 transition">Tentang</a>
          <a href="#kontak" class="nav-link text-gray-700 hover:text-blue-900 transition">Kontak</a>
        </nav>
        
        <!-- Tombol Login saja (Register dihapus) & Mobile menu button -->
        <div class="flex items-center gap-3">
          <button id="loginBtn" class="btn-subtle bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-xl font-semibold text-sm flex items-center gap-2">
            <i class="fas fa-sign-in-alt"></i> <span class="hidden sm:inline">Login</span>
          </button>
          
          <!-- Mobile menu button (hamburger) - untuk tampilan mobile -->
          <button id="mobileMenuBtn" class="md:hidden text-gray-600 hover:text-blue-900 focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
          </button>
        </div>
      </div>
      
      <!-- Mobile menu dropdown (hidden by default) -->
      <div id="mobileMenu" class="hidden md:hidden pb-4 pt-2 border-t border-gray-200">
        <a href="#home" class="block py-2 text-gray-700 hover:text-blue-900 transition">Beranda</a>
        <a href="#layanan" class="block py-2 text-gray-700 hover:text-blue-900 transition">Layanan</a>
        <a href="#tentang" class="block py-2 text-gray-700 hover:text-blue-900 transition">Tentang</a>
        <a href="#kontak" class="block py-2 text-gray-700 hover:text-blue-900 transition">Kontak</a>
      </div>
    </div>
  </header>

  <main>
    <!-- ===== HERO SECTION ===== dengan responsive layout ===== -->
    <section id="home" class="relative bg-linear-to-br from-blue-50 via-white to-white overflow-hidden">
      <!-- background elements -->
      <div class="absolute top-20 right-0 w-96 h-96 bg-blue-900/5 rounded-full blur-3xl float-subtle"></div>
      <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-900/10 rounded-full blur-2xl pulse-soft"></div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 md:pt-20 md:pb-32 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <!-- left content - responsive text center on mobile -->
          <div class="space-y-6 hero-content" data-aos="fade-right" data-aos-duration="1000">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">Kesehatan Anda <br><span class="text-blue-900">Prioritas Utama</span> Kami</h1>
            <p class="text-lg text-gray-600 max-w-lg mx-auto md:mx-0" data-aos="fade-up" data-aos-delay="200">Dilengkapi tenaga medis profesional dan fasilitas modern. Suasana nyaman, putih bersih, dengan sentuhan biru tua yang menenangkan.</p>
            
            <!-- tombol CTA - stack on mobile -->
            <div class="flex flex-wrap gap-4 pt-4 justify-center md:justify-start" data-aos="fade-up" data-aos-delay="400">
              <a href="#layanan" class="btn-subtle bg-blue-900 hover:bg-blue-800 text-white px-8 py-3.5 rounded-2xl font-semibold shadow-md flex items-center gap-2 text-base">
                <i class="fas fa-stethoscope"></i> Lihat Layanan
              </a>
              <a href="#tentang" class="btn-subtle bg-white border border-blue-900/30 hover:border-blue-900 text-blue-900 px-8 py-3.5 rounded-2xl font-semibold shadow-sm flex items-center gap-2 text-base">
                <i class="fas fa-calendar-alt"></i> Tentang Kami
              </a>
            </div>
            
            <!-- quick stat - center on mobile -->
            <div class="flex flex-wrap items-center gap-x-8 gap-y-4 pt-8 text-gray-600 hero-stats justify-center md:justify-start">
              <div class="flex items-center gap-2"><i class="fas fa-user-md text-blue-900 text-xl"></i> <span>{{ $jumlahDokter }}+ dokter ahli</span></div>
              <div class="flex items-center gap-2"><i class="fas fa-hand-holding-heart text-blue-900 text-xl"></i> <span>{{ $jumlahPasien }}+ pasien</span></div>
            </div>
          </div>
          
          <!-- right image - hidden on mobile, visible on md -->
          <div class="relative hidden md:block" data-aos="fade-left" data-aos-duration="1200">
            <div class="relative w-full h-96 bg-blue-50 rounded-3xl shadow-xl overflow-hidden border-8 border-white/80 transition-all duration-700 hover:shadow-2xl hover-scale-subtle">
              <img src="{{ asset('bg-right.jpg') }}" alt="Klinik Qlinica" class="absolute inset-0 w-full h-full object-cover">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== LAYANAN SECTION ===== dengan grid responsive ===== -->
    <section id="layanan" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16" data-aos="fade-up">
          <span class="text-blue-900 font-semibold tracking-wider uppercase text-sm">Layanan</span>
          <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">Fasilitas & Layanan <span class="text-blue-900">QLINICA</span></h2>
          <p class="text-gray-500 mt-4">Suasana putih bersih dengan sentuhan biru tua yang profesional dan menenangkan.</p>
        </div>

        <!-- Grid 1 kolom di mobile, 2 di tablet, 3 di desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          @foreach($layanan as $i => $l)
          <div class="service-card bg-white border border-blue-900/20 rounded-3xl p-8 shadow-sm" data-aos="fade-up" data-aos-delay="{{ ($i%3+1)*100 }}">
            <div class="w-16 h-16 bg-{{ $l['warna'] }}-100 text-{{ $l['warna'] }}-{{ $l['warna'] === 'blue' ? 900 : ($l['warna'] === 'green' ? 700 : ($l['warna'] === 'pink' ? 600 : ($l['warna'] === 'red' ? 600 : ($l['warna'] === 'purple' ? 700 : 700)))) }} rounded-2xl flex items-center justify-center mb-6">
              <i class="fas {{ $l['icon'] }} text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $l['nama'] }}</h3>
            <p class="text-gray-600 leading-relaxed">{{ $l['desc'] }}</p>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- ===== TENTANG SECTION ===== dengan layout responsive ===== -->
    <section id="tentang" class="py-20 bg-linear-to-b from-white to-blue-50/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
          <!-- kiri - grid icon (responsive) -->
          <div class="order-2 md:order-1 relative" data-aos="fade-right">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-4">
                <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-900/20 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                  <i class="fas fa-user-nurse text-blue-900 text-3xl mb-3"></i>
                  <h5 class="font-semibold">Perawat ramah</h5>
                  <p class="text-sm text-gray-500">{{ $jumlahPegawai }}+ tenaga profesional</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-900/20 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                  <i class="fas fa-syringe text-blue-900 text-3xl mb-3"></i>
                  <h5 class="font-semibold">Vaksinasi</h5>
                  <p class="text-sm text-gray-500">Lengkap & terjadwal</p>
                </div>
              </div>
              <div class="space-y-4 mt-8">
                <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-900/20 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                  <i class="fas fa-procedures text-blue-900 text-3xl mb-3"></i>
                  <h5 class="font-semibold">Ruang rawat</h5>
                  <p class="text-sm text-gray-500">Kelas 1, VIP, putih bersih</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-md border border-blue-900/20 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                  <i class="fas fa-truck-medical text-blue-900 text-3xl mb-3"></i>
                  <h5 class="font-semibold">Ambulans 24/7</h5>
                  <p class="text-sm text-gray-500">Siap antar-jemput</p>
                </div>
              </div>
            </div>
            <div class="absolute -z-10 -top-10 -left-10 w-40 h-40 bg-blue-900/10 rounded-full blur-3xl float-subtle"></div>
          </div>
          
          <!-- kanan - teks tentang -->
          <div class="order-1 md:order-2 text-center md:text-left" data-aos="fade-left">
            <span class="text-blue-900 font-semibold tracking-wider uppercase text-sm">Tentang Kami</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">Lebih dari sekadar <span class="text-blue-900">klinik</span></h2>
            <p class="text-gray-600 mt-6 text-lg leading-relaxed">
              QLINICA hadir dengan konsep ruangan didominasi warna putih yang memberikan kesan bersih, luas, dan terang. 
              Sentuhan <span class="text-blue-900 font-medium">biru tua</span> dipilih untuk menciptakan rasa tenang, profesional, dan terpercaya.
            </p>
            <div class="flex gap-5 mt-8 justify-center md:justify-start">
              <div><span class="font-bold text-3xl text-blue-900">8+</span> <span class="text-gray-500">tahun</span></div>
              <div><span class="font-bold text-3xl text-blue-900">{{ $jumlahPegawai }}+</span> <span class="text-gray-500">tenaga medis</span></div>
              <div><span class="font-bold text-3xl text-blue-900">{{ $ratingRatata }}</span> <span class="text-gray-500">rating</span></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== TESTIMONI ===== Slider 3-per-halaman, auto-scroll ===== -->
    @if($testimoni->isNotEmpty())
    <section class="py-16 bg-white" id="testimoni">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-blue-900 font-semibold tracking-wider uppercase text-sm" data-aos="fade-up">Ulasan Pasien</span>
        <h2 class="text-3xl font-bold text-gray-800 mt-2" data-aos="fade-down">Apa kata <span class="text-blue-900">pasien</span>?</h2>
        <p class="text-gray-500 mt-3 max-w-xl mx-auto" data-aos="fade-up" data-aos-delay="100">{{ $testimoni->count() }} ulasan terbaru dari pasien kami</p>

        <!-- Slider Container -->
        <div class="mt-12 relative" data-aos="fade-up" data-aos-delay="150">

          <!-- Slider Wrapper: overflow hidden, 3 kartu tampil bersamaan -->
          <div class="testi-slider-wrapper" id="testiWrapper">
            <div class="testi-slider-track" id="testiTrack">

              @foreach($testimoni as $i => $testi)
              <div class="testi-slide">
                <div class="testimonial-card bg-blue-50/60 p-6 rounded-3xl border border-blue-900/20 text-left relative flex flex-col h-full" style="min-height:210px">

                  {{-- Icon quote --}}
                  <i class="fas fa-quote-left text-blue-900/15 text-4xl absolute top-4 right-5"></i>

                  {{-- Bintang penilaian --}}
                  <div class="flex items-center gap-1 mb-3">
                    @for($s = 1; $s <= 5; $s++)
                      @if($s <= $testi['penilaian'])
                        <i class="fas fa-star text-amber-400 text-sm"></i>
                      @else
                        <i class="far fa-star text-gray-300 text-sm"></i>
                      @endif
                    @endfor
                    <span class="text-xs text-gray-400 ml-1">{{ $testi['penilaian'] }}/5</span>
                  </div>

                  {{-- Teks ulasan --}}
                  @if($testi['ulasan'])
                    <p class="text-gray-700 text-sm leading-relaxed relative z-10 flex-1">"{{ $testi['ulasan'] }}"</p>
                  @else
                    <p class="text-gray-400 text-sm italic flex-1">Pasien memberikan penilaian tanpa komentar.</p>
                  @endif

                  {{-- Profil pasien --}}
                  <div class="flex items-center gap-3 mt-4 pt-4 border-t border-blue-900/10">
                    <div class="w-10 h-10 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                      {{ $testi['inisial'] }}
                    </div>
                    <div>
                      <h5 class="font-semibold text-gray-800 text-sm">{{ $testi['nama'] }}</h5>
                      <span class="text-xs text-gray-500">{{ $testi['tipe'] }}</span>
                    </div>
                  </div>

                </div>
              </div>
              @endforeach

            </div>
          </div>

          <!-- Navigasi Prev / Next + Dots (dibuat oleh JS) -->
          <div class="flex items-center justify-center gap-4 mt-8">
            <button class="testi-nav-btn" id="testiPrev" aria-label="Sebelumnya">
              <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <div class="flex items-center gap-2" id="testiDots"></div>
            <button class="testi-nav-btn" id="testiNext" aria-label="Berikutnya">
              <i class="fas fa-chevron-right text-sm"></i>
            </button>
          </div>

        </div>
      </div>
    </section>
    @endif


    <!-- ===== KONTAK ===== dengan padding responsive ===== -->
    <section id="kontak" class="py-20 bg-blue-50/80">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="bg-white p-6 md:p-10 lg:p-14 rounded-[3rem] shadow-xl border border-blue-900/20 transition-all duration-500 hover:shadow-2xl hover-scale-subtle" data-aos="zoom-in-up">
          <span class="text-blue-900 font-semibold text-sm">Jadwalkan kunjungan</span>
          <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 mt-2">Siap memberikan yang <span class="text-blue-900">terbaik</span> untuk Anda</h2>
          <p class="text-gray-600 max-w-xl mx-auto mt-4 px-2">Klinik dengan nuansa putih yang menenangkan dan tenaga medis berpengalaman.</p>
          
          <!-- tombol stack di mobile, row di desktop -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
            <a href="#" id="buatJanjiBtn" class="btn-subtle bg-blue-900 hover:bg-blue-800 text-white px-6 sm:px-8 py-4 rounded-2xl font-semibold shadow-md flex items-center justify-center gap-3 text-base sm:text-lg">
              <i class="fas fa-calendar-week"></i> Buat antrian online
            </a>
            <a href="https://wa.me/6282131927337" target="_blank" rel="noopener noreferrer" class="btn-subtle bg-white border-2 border-blue-900/30 hover:border-blue-900 text-blue-900 px-6 sm:px-8 py-4 rounded-2xl font-semibold flex items-center justify-center gap-3 text-base sm:text-lg">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          </div>
          
          <!-- kontak info - wrap di mobile -->
          <div class="flex flex-wrap justify-center gap-4 sm:gap-8 mt-12 text-gray-600">
            <div class="flex items-center gap-2"><i class="fas fa-map-pin text-blue-900"></i> Jl. Ahmad Yani No. 23</div>
            <div class="flex items-center gap-2"><i class="fas fa-phone-alt text-blue-900"></i> (0361) 123-4567</div>
            <div class="flex items-center gap-2"><i class="fas fa-envelope text-blue-900"></i> info@qlinica.co</div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="bg-white border-t border-blue-900/20 py-8">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
      <div class="flex justify-center space-x-6 mb-4">
        <a href="#" class="text-blue-900 hover:text-blue-700 transition-all duration-300 hover:scale-110"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="text-blue-900 hover:text-blue-700 transition-all duration-300 hover:scale-110"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-blue-900 hover:text-blue-700 transition-all duration-300 hover:scale-110"><i class="fab fa-twitter"></i></a>
      </div>
      <p>&copy; 2026 QLINICA.</p>
    </div>
  </footer>

  <!-- ===== MODAL LOGIN ===== -->
  <div id="loginModal" class="auth-modal fixed inset-0 bg-black/50 z-50">
    <div class="modal-content bg-white shadow-2xl">

      <!-- Handle bar (mobile only) -->
      <div class="flex justify-center pt-3 pb-1 sm:hidden">
        <div style="width:40px;height:4px;border-radius:99px;background:#e2e8f0;"></div>
      </div>

      <!-- Header -->
      <div class="flex justify-between items-center px-6 pt-5 pb-4 border-b border-gray-100">
        <div>
          <h3 class="text-xl font-bold text-gray-800">Masuk ke QLINICA</h3>
          <p class="text-xs text-gray-400 mt-0.5">Gunakan No. Rekam Medik Anda</p>
        </div>
        <button class="close-modal w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-400 text-xl transition">&times;</button>
      </div>

      <!-- Body -->
      <div class="px-6 py-5">

        @if($errors->has('session'))
          <div class="mb-4 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-clock mr-2"></i>{{ $errors->first('session') }}
          </div>
        @elseif($errors->any())
          <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
          </div>
        @endif

        <form id="loginForm" method="POST" action="/login" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Rekam Medik</label>
            <input type="text" name="login_id" value="{{ old('login_id') }}" required
              autocomplete="username"
              class="input-focus w-full px-4 py-3 border border-gray-200 rounded-xl"
              placeholder="RM-YYYYMMDD-XXXX">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <input type="password" name="password" required
              autocomplete="current-password"
              class="input-focus w-full px-4 py-3 border border-gray-200 rounded-xl"
              placeholder="••••••••">
          </div>
          <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
              <input type="checkbox" name="remember" class="rounded border-gray-300"> Ingat saya
            </label>
          </div>

          <button type="submit"
            class="w-full btn-subtle bg-blue-900 hover:bg-blue-800 text-white py-3.5 rounded-xl font-semibold text-base mt-1 flex items-center justify-center gap-2">
            <i class="fas fa-sign-in-alt"></i> Login
          </button>

          <p class="text-xs text-gray-400 text-center pt-1">
            Belum punya akun? Silahkan datang langsung ke klinik.
          </p>
        </form>

      </div>
    </div>
  </div>

  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    // Initialize AOS
    AOS.init({
      once: true,
      duration: 800,
      offset: 100,
      easing: 'ease-out-cubic'
    });

    // ===== MODAL LOGIN ONLY =====
    const loginModal = document.getElementById('loginModal');
    const loginBtn = document.getElementById('loginBtn');
    const buatJanjiBtn = document.getElementById('buatJanjiBtn');
    const closeButtons = document.querySelectorAll('.close-modal');

    // Open Login Modal
    const openLogin = (e) => {
      e.preventDefault();
      loginModal.classList.add('show');
    };

    if (loginBtn) loginBtn.addEventListener('click', openLogin);
    if (buatJanjiBtn) buatJanjiBtn.addEventListener('click', openLogin);


    // Close modals with close button
    closeButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        loginModal.classList.remove('show');
      });
    });

    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
      if (e.target === loginModal) {
        loginModal.classList.remove('show');
      }
    });

    // Auto-open login modal if there are server-side errors
    @if($errors->any())
      loginModal.classList.add('show');
    @endif

    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }

    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== "#" && href.startsWith("#")) {
          e.preventDefault();
          const target = document.querySelector(href);
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
            // Tutup mobile menu jika terbuka
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
              mobileMenu.classList.add('hidden');
            }
          }
        }
      });
    });

    // ===== TESTIMONIAL SLIDER (3 per halaman) =====
    (function () {
      const track    = document.getElementById('testiTrack');
      const dotsBox  = document.getElementById('testiDots');
      const btnPrev  = document.getElementById('testiPrev');
      const btnNext  = document.getElementById('testiNext');
      if (!track) return;

      const slides     = track.querySelectorAll('.testi-slide');
      const totalCards = slides.length;
      let currentPage  = 0;
      let autoTimer    = null;
      let visibleCount = 3;
      let totalPages   = 1;

      // Hitung visibleCount sesuai lebar layar
      function getVisible() {
        if (window.innerWidth < 640)  return 1;
        if (window.innerWidth < 1024) return 2;
        return 3;
      }

      // Bangun ulang dot indicator
      function buildDots() {
        dotsBox.innerHTML = '';
        for (let p = 0; p < totalPages; p++) {
          const btn = document.createElement('button');
          btn.className = 'testi-dot' + (p === currentPage ? ' active' : '');
          btn.setAttribute('aria-label', 'Halaman ' + (p + 1));
          btn.addEventListener('click', () => { goToPage(p); startAuto(); });
          dotsBox.appendChild(btn);
        }
      }

      // Perbarui dot aktif
      function updateDots() {
        dotsBox.querySelectorAll('.testi-dot').forEach((d, i) =>
          d.classList.toggle('active', i === currentPage)
        );
      }

      // Perbarui lebar setiap slide via inline style agar presisi
      function updateSlideWidths() {
        slides.forEach(s => { s.style.minWidth = `calc(100% / ${visibleCount})`; });
      }

      // Navigasi ke halaman tertentu
      function goToPage(page) {
        totalPages   = Math.ceil(totalCards / visibleCount);
        currentPage  = (page + totalPages) % totalPages;
        // Geser track: setiap halaman = visibleCount kartu = 100% lebar wrapper
        const offsetPct = currentPage * (100 / visibleCount) * visibleCount;
        track.style.transform = `translateX(-${offsetPct}%)`;
        updateDots();
      }

      // Inisialisasi / re-inisialisasi saat resize
      function init() {
        visibleCount = getVisible();
        totalPages   = Math.ceil(totalCards / visibleCount);
        // Pastikan currentPage masih valid
        if (currentPage >= totalPages) currentPage = 0;
        updateSlideWidths();
        buildDots();
        goToPage(currentPage);
      }

      function startAuto() {
        stopAuto();
        autoTimer = setInterval(() => goToPage(currentPage + 1), 3000);
      }
      function stopAuto() {
        if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
      }

      // Tombol prev / next
      btnPrev.addEventListener('click', () => { goToPage(currentPage - 1); startAuto(); });
      btnNext.addEventListener('click', () => { goToPage(currentPage + 1); startAuto(); });

      // Pause saat hover kartu
      track.closest('.testi-slider-wrapper').addEventListener('mouseenter', stopAuto);
      track.closest('.testi-slider-wrapper').addEventListener('mouseleave', startAuto);

      // Touch / swipe
      let touchStartX = 0;
      track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; stopAuto(); }, { passive: true });
      track.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) goToPage(diff > 0 ? currentPage + 1 : currentPage - 1);
        startAuto();
      }, { passive: true });

      // Re-init saat resize layar
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => { stopAuto(); init(); startAuto(); }, 200);
      });

      // Mulai
      init();
      startAuto();
    })();
  </script>
</body>
</html>