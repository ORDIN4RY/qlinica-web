<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') | Sahaduta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    html { scroll-behavior: smooth; }
    .sidebar-link { transition: all 0.2s ease; border-left: 3px solid transparent; }
    .sidebar-link:hover {
      background: rgba(255,255,255,0.10);
      border-left-color: rgba(255,255,255,0.4);
    }
    .sidebar-link.active {
      background: rgba(255,255,255,0.18);
      border-left-color: #ffffff;
      font-weight: 700;
    }
    .card-hover { transition: all 0.3s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(30,58,138,0.15); }
    @keyframes fadeInUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .fade-in-up { animation: fadeInUp 0.5s ease forwards; }
    
    /* Custom Scrollbar untuk Navigasi Sidebar */
    .sidebar-scroll::-webkit-scrollbar { width: 5px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
  </style>
  @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-700">

<div class="flex min-h-screen">

  <!-- ===== SIDEBAR ===== -->
  <aside id="sidebar" class="w-64 bg-blue-900 text-white flex flex-col fixed inset-y-0 left-0 z-40 shadow-2xl transition-transform duration-300 transform -translate-x-full md:translate-x-0">
    <!-- Logo (Fixed Top) -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10 shrink-0">
      <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-clinic-medical text-white text-lg"></i>
      </div>
      <span class="font-bold text-xl tracking-tight">Sahaduta</span>
    </div>

    <!-- Nav (Scrollable Middle) -->
    <nav id="sidebar-nav" class="flex-1 px-4 py-6 space-y-1 overflow-y-auto sidebar-scroll">
      @php $u = auth()->user(); @endphp

      <!-- ================= UTAMA ================= -->
      @if(($u && $u->hasMenuAccess('Dashboard')) || ($u && $u->hasMenuAccess('Laporan')))
      <div class="px-2 mt-2 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-wider">Utama</div>
      @endif

      @if($u && $u->hasMenuAccess('Dashboard'))
        @if($u->role === 'admin')
          @php
             $currentView = session('bypass_view', 'admin');
             if ($currentView === 'admin') {
                 $nextView = 'dokter';
                 $icon = 'fa-chart-bar';
                 $label = 'Admin';
             } elseif ($currentView === 'dokter') {
                 $nextView = 'apoteker';
                 $icon = 'fa-stethoscope';
                 $label = 'Dokter';
             } else {
                 $nextView = 'admin';
                 $icon = 'fa-pills';
                 $label = 'Apoteker';
             }
             $isOnDashboard = request()->routeIs('beranda_admin');
          @endphp
          <div class="flex items-center justify-between mb-1 group">
             <a href="{{ route('beranda_admin') }}" class="sidebar-link flex-1 flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ $isOnDashboard ? 'active' : '' }}">
               <i class="fas fa-chart-pie w-5 text-center"></i> Dashboard
             </a>
             @if($isOnDashboard)
             <a href="{{ route('beranda_admin', ['view' => $nextView]) }}" title="Cycle Mode Dashboard" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/20 text-white/50 hover:text-white transition cursor-pointer ml-1">
               <i class="fas fa-sync-alt text-xs"></i>
             </a>
             @endif
          </div>
          @if($isOnDashboard)
          <div class="px-4 pb-2 mb-2 border-b border-white/10">
             <div class="flex items-center gap-2 text-[10px] font-bold text-emerald-400 uppercase tracking-wider">
               <i class="fas {{ $icon }}"></i> Mode {{ $label }}
             </div>
          </div>
          @endif
        @else
          <a href="{{ route('beranda_admin') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('beranda_admin') ? 'active' : '' }}">
            <i class="fas fa-chart-pie w-5 text-center"></i> Dashboard
          </a>
        @endif
      @endif

      @if($u && $u->hasMenuAccess('Laporan'))
      <a href="{{ route('admin.laporan.penanganan') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
        <i class="fas fa-chart-line w-5 text-center"></i> Laporan Penanganan
      </a>
      
      <a href="{{ route('apoteker.laporan') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('apoteker.laporan*')  ? 'active' : '' }}">
        <i class="fas fa-chart-line w-5 text-center"></i> Laporan Apotek
      </a>
      @endif

      <!-- ================= PELAYANAN MEDIS ================= -->
      @if(($u && $u->hasMenuAccess('Antrian Pemesanan')) || ($u && $u->hasMenuAccess('Antrian Pemeriksaan')) || ($u && $u->hasMenuAccess('Pasien')) || ($u && $u->hasMenuAccess('Resep')) || ($u && $u->hasMenuAccess('Obat')) || ($u && $u->hasMenuAccess('Rekam Medis')))
      <div class="px-2 mt-6 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-wider">Pelayanan Medis</div>
      @endif

      @if($u && $u->hasMenuAccess('Antrian Pemesanan'))
      <a href="{{ route('admin.pemesanan') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.pemesanan*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check w-5 text-center"></i> Antrian Pendaftaran
      </a>
      @endif

      
      @if($u && $u->hasMenuAccess('Antrian Pemeriksaan'))
      <a href="{{ route('dokter.antrian') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('dokter.antrian*') ? 'active' : '' }}">
        <i class="fas fa-stethoscope w-5 text-center"></i> Antrian Pemeriksaan
      </a>
      @endif
      
      @if($u && $u->hasMenuAccess('Rekam Medis'))
      <a href="{{ route('dokter.pasien') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('dokter.pasien*') ? 'active' : '' }}">
        <i class="fas fa-notes-medical w-5 text-center"></i> Riwayat Medis Pasien
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Pasien'))
      <a href="{{ route('admin.pasien') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.pasien*') ? 'active' : '' }}">
        <i class="fas fa-user-injured w-5 text-center"></i> Data Pasien
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Resep'))
      <a href="{{ route('apoteker.resep') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('apoteker.resep*') ? 'active' : '' }}">
        <i class="fas fa-prescription-bottle-alt w-5 text-center"></i> Resep Medis
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Obat'))
      <a href="{{ route('apoteker.obat') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('apoteker.obat*') ? 'active' : '' }}">
        <i class="fas fa-pills w-5 text-center"></i> Stok Obat
      </a>
      @endif

      <!-- ================= MASTER DATA & SDM ================= -->
      @if(($u && $u->hasMenuAccess('ICDX')) || ($u && $u->hasMenuAccess('Pegawai')) || ($u && $u->hasMenuAccess('Presensi')) || ($u && $u->hasMenuAccess('Jabatan')))
      <div class="px-2 mt-6 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-wider">Master Data & SDM</div>
      @endif

      @if($u && $u->hasMenuAccess('ICDX'))
      <a href="{{ route('admin.icdx') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.icdx*') ? 'active' : '' }}">
        <i class="fas fa-file-medical-alt w-5 text-center"></i> ICD-X
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Pegawai'))
      <a href="{{ route('admin.pegawai') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.pegawai*') ? 'active' : '' }}">
        <i class="fas fa-user-doctor w-5 text-center"></i> Pegawai
      </a>
      @endif

      @if(auth()->user()->hasMenuAccess('Presensi'))
      <a href="{{ route('admin.presensi') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.presensi*') ? 'active' : '' }}">
        <i class="fas fa-user-clock w-5 text-center"></i> Presensi
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Jabatan'))
      <a href="{{ route('admin.jabatan') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.jabatan*') ? 'active' : '' }}">
        <i class="fas fa-shield-alt w-5 text-center"></i> Jabatan & Akses
      </a>
      @endif

      <!-- ================= LAINNYA ================= -->
      @if($u && $u->hasMenuAccess('Komentar'))
      <div class="px-2 mt-6 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-wider">Lainnya</div>
      <a href="{{ route('admin.komentar') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.komentar*') ? 'active' : '' }}">
        <i class="fas fa-comment w-5 text-center"></i> Komentar
      </a>
      @endif
    </nav>

    <!-- User + Logout -->
    <div class="px-4 py-5 border-t border-white/10 shrink-0">
      <a href="{{ route('admin.profil') }}"
         class="flex items-center gap-3 mb-4 hover:bg-white/10 rounded-xl px-2 py-2 transition cursor-pointer">
        <div class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center bg-white/20 font-bold text-sm flex-shrink-0">
          @if(Auth::user()?->foto)
            <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover">
          @else
            {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'A' }}
          @endif
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-sm truncate">{{ Auth::user()?->name ?? 'Administrator' }}</p>
          <p class="text-white/60 text-xs truncate">{{ Auth::user()?->email ?? 'admin@sahaduta.com' }}</p>
        </div>
        <i class="fas fa-chevron-right text-white/40 text-xs"></i>
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
          class="w-full flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-medium transition">
          <i class="fas fa-sign-out-alt"></i> Keluar
        </button>
      </form>
    </div>

  </aside>

  <!-- Mobile Overlay -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-gray-900/50 z-30 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

  <!-- ===== MAIN ===== -->
  <div class="md:ml-64 flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300">
    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-20 px-4 md:px-8 py-4 flex items-center justify-between shadow-sm">
      <div class="flex items-center gap-3">
        <button id="sidebarToggleBtn" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none p-1 rounded-md hover:bg-gray-100">
          <i class="fas fa-bars text-xl"></i>
        </button>
        <div>
          <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
          <p class="text-sm text-gray-500 hidden sm:block">@yield('page-subtitle', 'Selamat datang kembali, ' . (Auth::user()?->name ?? 'Admin') . '!')</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500"><i class="fas fa-calendar-alt text-blue-900 mr-1"></i>{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
      </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto overflow-x-hidden">
      <div class="w-full max-w-6xl mx-auto">
      @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl shadow-sm">
          <i class="fas fa-check-circle text-green-500 text-lg"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif
      @if(session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-sm">
          <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif
      @if(session('warning'))
        <div class="mb-6 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl shadow-sm">
          <i class="fas fa-triangle-exclamation text-amber-500 text-lg"></i>
          <span>{{ session('warning') }}</span>
        </div>
      @endif
      @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl shadow-sm">
          <div class="flex items-center gap-3 mb-2">
            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
            <span class="font-bold">Terjadi Kesalahan:</span>
          </div>
          <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @yield('content')
      </div>
    </main>
  </div>

</div>

@stack('scripts')
<script>
  // Mobile Sidebar Toggle
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggleBtn = document.getElementById('sidebarToggleBtn');

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
  }

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('opacity-0');
    setTimeout(() => overlay.classList.add('hidden'), 300);
  }

  if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
  if (overlay) overlay.addEventListener('click', closeSidebar);

  // Persist Sidebar Scroll Position
  document.addEventListener("DOMContentLoaded", function() {
    const sidebarNav = document.getElementById('sidebar-nav');
    if (sidebarNav) {
      const savedScroll = sessionStorage.getItem('sidebarScrollPosition');
      if (savedScroll !== null) {
        sidebarNav.scrollTop = parseInt(savedScroll, 10);
      }
      sidebarNav.addEventListener('scroll', function() {
        sessionStorage.setItem('sidebarScrollPosition', sidebarNav.scrollTop);
      });
    }
  });
</script>
</body>
</html>
