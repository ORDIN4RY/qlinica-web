<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') | QLINICA</title>
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

    .sidebar-group-btn {
      transition: all 0.2s ease;
    }
    .sidebar-group-btn:hover {
      background: rgba(255,255,255,0.08);
    }
    .sidebar-group-chevron {
      transition: transform 0.2s ease;
    }
    .rotate-180 {
      transform: rotate(180deg);
    }
    @media print {
      .no-print, aside, #sidebar, header, nav, footer, #sidebarToggleBtn { display: none !important; }
      [class*="md:ml-64"] { margin-left: 0 !important; }
      main { padding: 0 !important; overflow: visible !important; }
      body, html { background: white !important; color: black !important; overflow: visible !important; height: auto !important; }
    }

    /* ===== TOAST SYSTEM ===== */
    #toast-container { pointer-events: none; }
    .toast-item {
      pointer-events: auto;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      background: #fff;
      border-radius: 14px;
      padding: 14px 16px;
      min-width: 300px;
      max-width: 400px;
      box-shadow: 0 8px 32px rgba(0,0,0,.14), 0 2px 8px rgba(0,0,0,.08);
      border-left: 4px solid #10b981;
      position: relative;
      overflow: hidden;
      transform: translateX(calc(100% + 32px));
      opacity: 0;
      transition: transform .4s cubic-bezier(.34,1.56,.64,1), opacity .35s;
    }
    .toast-item.toast-show {
      transform: translateX(0);
      opacity: 1;
    }
    .toast-item.toast-hide {
      transform: translateX(calc(100% + 32px));
      opacity: 0;
      transition: transform .3s ease-in, opacity .25s;
    }
    .toast-item.toast-success { border-left-color: #10b981; }
    .toast-item.toast-error   { border-left-color: #ef4444; }
    .toast-item.toast-warning { border-left-color: #f59e0b; }
    .toast-icon {
      width: 36px; height: 36px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }
    .toast-success .toast-icon { background: #ecfdf5; color: #10b981; }
    .toast-error   .toast-icon { background: #fef2f2; color: #ef4444; }
    .toast-warning .toast-icon { background: #fffbeb; color: #f59e0b; }
    .toast-body { flex: 1; min-width: 0; }
    .toast-title {
      font-size: 13px; font-weight: 700;
      color: #1e293b;
      margin-bottom: 2px;
      line-height: 1.3;
    }
    .toast-close {
      background: none; border: none;
      color: #9ca3af; cursor: pointer;
      padding: 2px 4px; border-radius: 6px;
      font-size: 14px; line-height: 1;
      transition: color .15s;
      flex-shrink: 0;
    }
    .toast-close:hover { color: #ef4444; }
    .toast-progress {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 3px;
    }
    .toast-progress-bar {
      height: 100%;
      border-radius: 0 0 14px 14px;
      animation: toastBarShrink 5s linear forwards;
    }
    .toast-success .toast-progress-bar { background: #10b981; }
    .toast-error   .toast-progress-bar { background: #ef4444; }
    .toast-warning .toast-progress-bar { background: #f59e0b; }
    @keyframes toastBarShrink {
      from { width: 100%; }
      to   { width: 0%; }
    }
    @media (max-width: 480px) {
      #toast-container {
        left: 12px;
        right: 12px;
        top: auto;
        bottom: 16px;
      }
      .toast-item {
        min-width: 0;
        max-width: 100%;
        width: 100%;
      }
    }
  </style>
  @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-700">

<div class="flex min-h-screen">

  <!-- ===== SIDEBAR ===== -->
  <aside id="sidebar" class="w-64 bg-blue-900 text-white flex flex-col fixed inset-y-0 left-0 z-40 shadow-2xl transition-transform duration-300 transform -translate-x-full md:translate-x-0">
    <!-- Logo (Fixed Top) -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10 shrink-0">
      <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center shadow-sm p-1">
        <img src="{{ asset('favicon.png') }}" alt="QLINICA" class="w-full h-full object-contain">
      </div>
      <span class="font-bold text-xl tracking-tight">QLINICA</span>
    </div>

    <!-- Nav (Scrollable Middle) -->
    <nav id="sidebar-nav" class="flex-1 px-4 py-6 space-y-1 overflow-y-auto sidebar-scroll">
      @php
        $u = auth()->user();

        // 1. Check Route Active States
        $isDashboardActive = request()->routeIs('beranda_admin*') || request()->routeIs('apoteker.dashboard*') || request()->routeIs('dokter.dashboard*');

        $isMedisActive = request()->routeIs('admin.pemesanan*') || request()->routeIs('dokter.antrian*') || request()->routeIs('dokter.pasien*') || request()->routeIs('admin.pasien*') || request()->routeIs('admin.rawat_inap*') || request()->routeIs('admin.billing*');

        $isApotekActive = request()->routeIs('apoteker.resep*') || request()->routeIs('apoteker.obat*');

        $isMasterActive = request()->routeIs('admin.kamar*') || request()->routeIs('admin.icdx*');

        $isSdmActive = request()->routeIs('admin.pegawai*') || request()->routeIs('admin.presensi*') || request()->routeIs('admin.jabatan*');

        $isLaporanActive = request()->routeIs('admin.laporan.penanganan*') || request()->routeIs('admin.laporan.keuangan*') || request()->routeIs('apoteker.laporan*');

        // 2. Check Access Permissions
        $hasMedisAccess = $u && ($u->hasMenuAccess('Antrian Pemesanan') || $u->hasMenuAccess('Antrian Pemeriksaan') || $u->hasMenuAccess('Rekam Medis') || $u->hasMenuAccess('Pasien') || $u->hasMenuAccess('Rawat Inap') || $u->hasMenuAccess('Billing'));

        $hasApotekAccess = $u && ($u->hasMenuAccess('Resep') || $u->hasMenuAccess('Obat'));

        $hasMasterAccess = $u && ($u->hasMenuAccess('Kamar') || $u->hasMenuAccess('ICDX'));

        $hasSdmAccess = $u && ($u->hasMenuAccess('Pegawai') || $u->hasMenuAccess('Presensi') || $u->hasMenuAccess('Jabatan'));

        $hasLaporanAccess = $u && $u->hasMenuAccess('Laporan') && (
            $u->hasMenuAccess('Laporan', 'penanganan') ||
            $u->hasMenuAccess('Laporan', 'keuangan') ||
            $u->hasMenuAccess('Laporan', 'apotek')
        );
      @endphp

      <!-- ================= UTAMA ================= -->
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
          @php
             $dashboardRoute = $u->hasMenuAccess('Dashboard', 'admin_dashboard')
                 ? route('beranda_admin')
                 : ($u->hasMenuAccess('Dashboard', 'dokter_dashboard')
                     ? route('dokter.dashboard')
                     : ($u->hasMenuAccess('Dashboard', 'apoteker_dashboard')
                         ? route('apoteker.dashboard')
                         : route('beranda_admin')));
             $isDashboardActive = request()->routeIs('beranda_admin') || request()->routeIs('dokter.dashboard') || request()->routeIs('apoteker.dashboard');
          @endphp
          <a href="{{ $dashboardRoute }}"
             class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ $isDashboardActive ? 'active' : '' }}">
            <i class="fas fa-chart-pie w-5 text-center"></i> Dashboard
          </a>
        @endif
      @endif

      <!-- ================= LAYANAN MEDIS ================= -->
      @if($hasMedisAccess)
      <div class="space-y-1 pt-2">
        <button type="button"
                onclick="toggleSidebarGroup('medis-group')"
                class="w-full sidebar-group-btn flex items-center justify-between px-4 py-3 rounded-xl text-white/90 transition font-medium">
          <div class="flex items-center gap-3">
            <i class="fas fa-briefcase-medical w-5 text-center text-white/60"></i>
            <span class="text-sm">Layanan Medis</span>
          </div>
          <i class="fas fa-chevron-down sidebar-group-chevron text-[10px] text-white/40 {{ $isMedisActive ? 'rotate-180' : '' }}" id="medis-group-chevron"></i>
        </button>
        <div id="medis-group" class="{{ $isMedisActive ? '' : 'hidden' }} pl-4 space-y-1 border-l border-white/10 ml-6 mt-1 transition-all duration-300">
          @if($u->hasMenuAccess('Antrian Pemesanan'))
          <a href="{{ route('admin.pemesanan') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.pemesanan*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check w-4 text-center text-white/50"></i> Antrian Pendaftaran
          </a>
          @endif

          @if($u->hasMenuAccess('Antrian Pemeriksaan'))
          <a href="{{ route('dokter.antrian') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('dokter.antrian*') ? 'active' : '' }}">
            <i class="fas fa-stethoscope w-4 text-center text-white/50"></i> Antrian Pemeriksaan
          </a>
          @endif

          @if($u->hasMenuAccess('Rekam Medis'))
          <a href="{{ route('dokter.pasien') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('dokter.pasien*') ? 'active' : '' }}">
            <i class="fas fa-file-prescription w-4 text-center text-white/50"></i> Riwayat Medis Pasien
          </a>
          @endif

          @if($u->hasMenuAccess('Pasien'))
          <a href="{{ route('admin.pasien') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.pasien*') ? 'active' : '' }}">
            <i class="fas fa-user-injured w-4 text-center text-white/50"></i> Data Pasien
          </a>
          @endif

          @if($u->hasMenuAccess('Rawat Inap'))
          <a href="{{ route('admin.rawat_inap') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.rawat_inap*') ? 'active' : '' }}">
            <i class="fas fa-procedures w-4 text-center text-white/50"></i> Rawat Inap
          </a>
          @endif

          @if($u->hasMenuAccess('Billing'))
          <a href="{{ route('admin.billing') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.billing*') ? 'active' : '' }}">
            <i class="fas fa-wallet w-4 text-center text-white/50"></i> Billing & Kasir
          </a>
          @endif
        </div>
      </div>
      @endif

      <!-- ================= FARMASI & OBAT ================= -->
      @if($hasApotekAccess)
      <div class="space-y-1 pt-1">
        <button type="button"
                onclick="toggleSidebarGroup('apotek-group')"
                class="w-full sidebar-group-btn flex items-center justify-between px-4 py-3 rounded-xl text-white/90 transition font-medium">
          <div class="flex items-center gap-3">
            <i class="fas fa-prescription-bottle-alt w-5 text-center text-white/60"></i>
            <span class="text-sm">Farmasi & Obat</span>
          </div>
          <i class="fas fa-chevron-down sidebar-group-chevron text-[10px] text-white/40 {{ $isApotekActive ? 'rotate-180' : '' }}" id="apotek-group-chevron"></i>
        </button>
        <div id="apotek-group" class="{{ $isApotekActive ? '' : 'hidden' }} pl-4 space-y-1 border-l border-white/10 ml-6 mt-1 transition-all duration-300">
          @if($u->hasMenuAccess('Resep'))
          <a href="{{ route('apoteker.resep') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('apoteker.resep*') ? 'active' : '' }}">
            <i class="fas fa-file-medical w-4 text-center text-white/50"></i> Resep Medis
          </a>
          @endif

          @if($u->hasMenuAccess('Obat'))
          <a href="{{ route('apoteker.obat') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('apoteker.obat*') ? 'active' : '' }}">
            <i class="fas fa-pills w-4 text-center text-white/50"></i> Stok Obat
          </a>
          @endif
        </div>
      </div>
      @endif

      <!-- ================= MASTER DATA ================= -->
      @if($hasMasterAccess)
      <div class="space-y-1 pt-1">
        <button type="button"
                onclick="toggleSidebarGroup('master-group')"
                class="w-full sidebar-group-btn flex items-center justify-between px-4 py-3 rounded-xl text-white/90 transition font-medium">
          <div class="flex items-center gap-3">
            <i class="fas fa-database w-5 text-center text-white/60"></i>
            <span class="text-sm">Master Data</span>
          </div>
          <i class="fas fa-chevron-down sidebar-group-chevron text-[10px] text-white/40 {{ $isMasterActive ? 'rotate-180' : '' }}" id="master-group-chevron"></i>
        </button>
        <div id="master-group" class="{{ $isMasterActive ? '' : 'hidden' }} pl-4 space-y-1 border-l border-white/10 ml-6 mt-1 transition-all duration-300">
          @if($u->hasMenuAccess('Kamar'))
          <a href="{{ route('admin.kamar') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.kamar*') ? 'active' : '' }}">
            <i class="fas fa-bed w-4 text-center text-white/50"></i> Data Kamar
          </a>
          @endif

          @if($u->hasMenuAccess('ICDX'))
          <a href="{{ route('admin.icdx') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.icdx*') ? 'active' : '' }}">
            <i class="fas fa-file-medical-alt w-4 text-center text-white/50"></i> ICD-X
          </a>
          @endif
        </div>
      </div>
      @endif

      <!-- ================= KEPEGAWAIAN & SDM ================= -->
      @if($hasSdmAccess)
      <div class="space-y-1 pt-1">
        <button type="button"
                onclick="toggleSidebarGroup('sdm-group')"
                class="w-full sidebar-group-btn flex items-center justify-between px-4 py-3 rounded-xl text-white/90 transition font-medium">
          <div class="flex items-center gap-3">
            <i class="fas fa-users w-5 text-center text-white/60"></i>
            <span class="text-sm">Kepegawaian & SDM</span>
          </div>
          <i class="fas fa-chevron-down sidebar-group-chevron text-[10px] text-white/40 {{ $isSdmActive ? 'rotate-180' : '' }}" id="sdm-group-chevron"></i>
        </button>
        <div id="sdm-group" class="{{ $isSdmActive ? '' : 'hidden' }} pl-4 space-y-1 border-l border-white/10 ml-6 mt-1 transition-all duration-300">
          @if($u->hasMenuAccess('Pegawai'))
          <a href="{{ route('admin.pegawai') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.pegawai*') ? 'active' : '' }}">
            <i class="fas fa-user-doctor w-4 text-center text-white/50"></i> Pegawai
          </a>
          @endif

          @if($u->hasMenuAccess('Presensi'))
          <a href="{{ route('admin.presensi') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.presensi*') ? 'active' : '' }}">
            <i class="fas fa-user-clock w-4 text-center text-white/50"></i> Presensi
          </a>
          @endif

          @if($u->hasMenuAccess('Jabatan'))
          <a href="{{ route('admin.jabatan') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.jabatan*') ? 'active' : '' }}">
            <i class="fas fa-shield-alt w-4 text-center text-white/50"></i> Jabatan & Akses
          </a>
          @endif
        </div>
      </div>
      @endif

      <!-- ================= LAPORAN & ANALISIS ================= -->
      @if($hasLaporanAccess)
      <div class="space-y-1 pt-1">
        <button type="button"
                onclick="toggleSidebarGroup('laporan-group')"
                class="w-full sidebar-group-btn flex items-center justify-between px-4 py-3 rounded-xl text-white/90 transition font-medium">
          <div class="flex items-center gap-3">
            <i class="fas fa-chart-bar w-5 text-center text-white/60"></i>
            <span class="text-sm">Laporan & Analisis</span>
          </div>
          <i class="fas fa-chevron-down sidebar-group-chevron text-[10px] text-white/40 {{ $isLaporanActive ? 'rotate-180' : '' }}" id="laporan-group-chevron"></i>
        </button>
        <div id="laporan-group" class="{{ $isLaporanActive ? '' : 'hidden' }} pl-4 space-y-1 border-l border-white/10 ml-6 mt-1 transition-all duration-300">
          @if($u->hasMenuAccess('Laporan', 'penanganan'))
          <a href="{{ route('admin.laporan.penanganan') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.laporan.penanganan') ? 'active' : '' }}">
            <i class="fas fa-notes-medical w-4 text-center text-white/50"></i> Laporan Penanganan
          </a>
          @endif

          @if($u->hasMenuAccess('Laporan', 'keuangan'))
          <a href="{{ route('admin.laporan.keuangan') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('admin.laporan.keuangan') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar w-4 text-center text-white/50"></i> Laporan Keuangan
          </a>
          @endif

          @if($u->hasMenuAccess('Laporan', 'apotek'))
          <a href="{{ route('apoteker.laporan') }}"
             class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-white/80 hover:text-white text-sm font-medium {{ request()->routeIs('apoteker.laporan*') ? 'active' : '' }}">
            <i class="fas fa-prescription-bottle-alt w-4 text-center text-white/50"></i> Laporan Apotek
          </a>
          @endif
        </div>
      </div>
      @endif

      <!-- ================= LAINNYA ================= -->
      @if($u && $u->hasMenuAccess('Komentar'))
      <div class="pt-2">
        <a href="{{ route('admin.komentar') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('admin.komentar*') ? 'active' : '' }}">
          <i class="fas fa-comment w-5 text-center text-white/60"></i> Komentar
        </a>
      </div>
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
          <p class="text-white/60 text-xs truncate">{{ Auth::user()?->email ?? 'admin@qlinica.com' }}</p>
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
      <div class="flex items-center gap-3 shrink-0">
        <span class="text-xs sm:text-sm text-gray-500">
          <i class="fas fa-calendar-alt text-blue-900 mr-1"></i>
          <span class="hidden sm:inline">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
          <span class="inline sm:hidden">{{ now()->translatedFormat('d M Y') }}</span>
        </span>
      </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto overflow-x-hidden">
      <div class="w-full max-w-6xl mx-auto">

      @yield('content')
      </div>
    </main>
  </div>

</div>

<!-- ===== TOAST CONTAINER ===== -->
<div id="toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-3 no-print" style="min-width:0;"></div>

<script>
// ===== TOAST SYSTEM =====
window.showToast = function(type, message, duration) {
  duration = duration || 5000;
  var container = document.getElementById('toast-container');
  if (!container) return;

  var icons = {
    success: 'fa-check-circle',
    error: 'fa-exclamation-circle',
    warning: 'fa-triangle-exclamation'
  };

  var toast = document.createElement('div');
  toast.className = 'toast-item toast-' + type;
  toast.innerHTML =
    '<div class="toast-icon"><i class="fas ' + (icons[type] || 'fa-info-circle') + '"></i></div>' +
    '<div class="toast-body"><div class="toast-title">' + message + '</div></div>' +
    '<button class="toast-close" onclick="this.closest(\'.toast-item\').remove()">&times;</button>' +
    '<div class="toast-progress"><div class="toast-progress-bar"></div></div>';

  container.appendChild(toast);

  // Trigger animation
  requestAnimationFrame(function() {
    requestAnimationFrame(function() {
      toast.classList.add('toast-show');
    });
  });

  // Auto dismiss
  var timer = setTimeout(function() {
    toast.classList.add('toast-hide');
    setTimeout(function() {
      if (toast.parentNode) toast.remove();
    }, 350);
  }, duration);

  // Cancel auto dismiss on hover
  toast.addEventListener('mouseenter', function() { clearTimeout(timer); });
  toast.addEventListener('mouseleave', function() {
    timer = setTimeout(function() {
      toast.classList.add('toast-hide');
      setTimeout(function() { if (toast.parentNode) toast.remove(); }, 350);
    }, 2000);
  });
};

// Auto-show toasts dari session data yang di-render PHP
document.addEventListener('DOMContentLoaded', function() {
  @if(session('success'))
    window.showToast('success', {{ Js::from(session('success')) }});
  @endif
  @if(session('error'))
    window.showToast('error', {{ Js::from(session('error')) }});
  @endif
  @if(session('warning'))
    window.showToast('warning', {{ Js::from(session('warning')) }});
  @endif
});
</script>

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

  // Toggle Sidebar Dropdowns with persistence
  function toggleSidebarGroup(groupId) {
    const groupEl = document.getElementById(groupId);
    const chevronEl = document.getElementById(groupId + '-chevron');
    if (groupEl && chevronEl) {
      const isHidden = groupEl.classList.contains('hidden');
      if (isHidden) {
        groupEl.classList.remove('hidden');
        chevronEl.classList.add('rotate-180');
        sessionStorage.setItem('sidebar_group_' + groupId, 'open');
      } else {
        groupEl.classList.add('hidden');
        chevronEl.classList.remove('rotate-180');
        sessionStorage.setItem('sidebar_group_' + groupId, 'closed');
      }
    }
  }

  // Restore Sidebar States (Groups & Scroll)
  document.addEventListener("DOMContentLoaded", function() {
    // Restore collapsible groups from sessionStorage
    const groups = ['medis-group', 'apotek-group', 'master-group', 'sdm-group', 'laporan-group'];
    groups.forEach(function(groupId) {
      const groupEl = document.getElementById(groupId);
      const chevronEl = document.getElementById(groupId + '-chevron');
      if (groupEl && chevronEl) {
        const savedState = sessionStorage.getItem('sidebar_group_' + groupId);
        if (savedState === 'open') {
          groupEl.classList.remove('hidden');
          chevronEl.classList.add('rotate-180');
        } else if (savedState === 'closed') {
          groupEl.classList.add('hidden');
          chevronEl.classList.remove('rotate-180');
        }
      }
    });

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

  // Portal semua .modal-overlay ke body agar position:fixed bekerja benar
  // (mengatasi bug containment karena overflow-y:auto pada elemen main)
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal-overlay').forEach(function(el) {
      document.body.appendChild(el);
    });
  });
</script>
@stack('scripts')
</body>
</html>
