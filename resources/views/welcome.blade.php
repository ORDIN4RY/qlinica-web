<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
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
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .auth-modal.show {
      opacity: 1;
      visibility: visible;
    }
    .auth-modal .modal-content {
      transition: transform 0.4s cubic-bezier(0.15, 0.75, 0.45, 1);
      transform: scale(0.95);
    }
    .auth-modal.show .modal-content {
      transform: scale(1);
    }
    
    /* Form input focus */
    .input-focus {
      transition: all 0.2s;
    }
    .input-focus:focus {
      border-color: #1e3a8a;
      box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
      outline: none;
    }

    /* Responsive fixes */
    @media (max-width: 768px) {
      .hero-content {
        text-align: center;
      }
      .hero-stats {
        justify-content: center;
      }
    }
  </style>
</head>
<body class="bg-white font-sans antialiased text-gray-700 overflow-x-hidden">

  <!-- ===== NAVBAR ===== dengan tombol Login saja ===== -->
  <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-blue-900/10 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- logo dengan nama Sahaduta -->
        <div class="flex items-center space-x-2 group">
          <div class="w-8 h-8 bg-blue-900 rounded-xl flex items-center justify-center transition-transform duration-500 group-hover:rotate-3">
            <i class="fas fa-clinic-medical text-white text-lg"></i>
          </div>
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
            <div class="flex items-center gap-8 pt-8 text-gray-600 hero-stats justify-center md:justify-start">
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

    <!-- ===== TESTIMONI ===== dengan grid responsive hingga 10 kartu ===== -->
    @if($testimoni->isNotEmpty())
    <section class="py-16 bg-white" id="testimoni">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-blue-900 font-semibold tracking-wider uppercase text-sm" data-aos="fade-up">Ulasan Pasien</span>
        <h2 class="text-3xl font-bold text-gray-800 mt-2" data-aos="fade-down">Apa kata <span class="text-blue-900">pasien</span>?</h2>
        <p class="text-gray-500 mt-3 max-w-xl mx-auto" data-aos="fade-up" data-aos-delay="100">{{ $testimoni->count() }} ulasan terbaru dari pasien kami</p>

        <!-- Grid: 1 kolom mobile, 2 tablet, 3 desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
          @foreach($testimoni as $i => $testi)
          <div class="testimonial-card bg-blue-50/60 p-6 rounded-3xl border border-blue-900/20 text-left relative flex flex-col"
               data-aos="fade-up" data-aos-delay="{{ min($i * 60, 400) }}">

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

            {{-- Teks ulasan — opsional, hanya tampil jika ada --}}
            @if($testi['ulasan'])
              <i class="fas fa-quote-left text-blue-900/20 text-3xl absolute top-4 right-5"></i>
              <p class="text-gray-700 text-sm leading-relaxed relative z-10 flex-1">"{{ $testi['ulasan'] }}"</p>
            @else
              <p class="text-gray-400 text-sm italic flex-1">Pasien memberikan penilaian tanpa komentar.</p>
            @endif

            {{-- Profil pasien --}}
            <div class="flex items-center gap-3 mt-5 pt-4 border-t border-blue-900/10">
              <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center text-blue-900 font-bold text-sm flex-shrink-0">
                {{ $testi['inisial'] }}
              </div>
              <div>
                <h5 class="font-semibold text-gray-800 text-sm">{{ $testi['nama'] }}</h5>
                <span class="text-xs text-gray-500">{{ $testi['tipe'] }}</span>
              </div>
            </div>
          </div>
          @endforeach
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

  <!-- ===== MODAL LOGIN (Register dihapus) ===== -->
  <div id="loginModal" class="auth-modal fixed inset-0 bg-black/50 flex items-center justify-center z-50 opacity-0 invisible transition-all duration-300">
    <div class="modal-content bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 sm:p-8 m-4">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800">SIGN IN</h3>
        <button class="close-modal text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
      </div>

      @if($errors->has('session'))
        <div class="mb-4 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl text-sm">
          <i class="fas fa-clock mr-2"></i>{{ $errors->first('session') }}
        </div>
      @elseif($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
          <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
        </div>
      @endif

      <form id="loginForm" method="POST" action="/login" class="space-y-5">
        @csrf
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekam Medik</label>
          <input type="text" name="login_id" value="{{ old('login_id') }}" required
            class="input-focus w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-900"
            placeholder="RM-YYYYMMDD-XXXX">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" required
            class="input-focus w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-900"
            placeholder="••••••••">
        </div>
        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-gray-300"> Ingat saya
          </label>
        </div>

        <p class="text-sm text-gray-500 text-center">
          Belum punya No. Rekam Medik? Silahkan datang langsung ke Klinik Qlinica.
        </p>

        <button type="submit" class="w-full btn-subtle bg-blue-900 hover:bg-blue-800 text-white py-3 rounded-xl font-semibold text-lg mt-2">
          Login
        </button>
      </form>

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
  </script>
</body>
</html>