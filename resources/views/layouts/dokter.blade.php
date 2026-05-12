<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') | Sahaduta Dokter</title>
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
  </style>
  @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-700">

<div class="flex min-h-screen">

  <aside class="w-48 md:w-64 bg-slate-800 text-white flex flex-col fixed inset-y-0 left-0 z-30 shadow-2xl">
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
      <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-user-doctor text-white text-lg"></i>
      </div>
      <span class="font-bold text-xl tracking-tight">Dokter</span>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1">
      @php $u = auth()->user(); @endphp

      @if($u && $u->hasMenuAccess('Dashboard'))
      <a href="{{ route('dokter.dashboard') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home w-5 text-center"></i> Dashboard
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Antrian'))
      <a href="{{ route('dokter.antrian') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('dokter.antrian*') ? 'active' : '' }}">
        <i class="fas fa-users w-5 text-center"></i> Antrian Pasien
      </a>
      @endif

      @if($u && $u->hasMenuAccess('Pasien'))
      <a href="{{ route('dokter.pasien') }}"
         class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 font-medium {{ request()->routeIs('dokter.pasien*') ? 'active' : '' }}">
        <i class="fas fa-notes-medical w-5 text-center"></i> Data Pasien
      </a>
      @endif
    </nav>

    <div class="px-4 py-5 border-t border-white/10">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center font-bold text-sm">
          {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'D' }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-sm truncate">{{ Auth::user()?->name ?? 'Dokter' }}</p>
          <p class="text-white/60 text-xs truncate">{{ Auth::user()?->email ?? 'dokter@sahaduta.com' }}</p>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
          class="w-full flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-medium transition">
          <i class="fas fa-sign-out-alt"></i> Keluar
        </button>
      </form>
    </div>
  </aside>

  <div class="ml-48 md:ml-64 flex-1 flex flex-col min-h-screen">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-20 px-4 md:px-8 py-4 flex items-center justify-between shadow-sm">
      <div>
        <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
        <p class="text-sm text-gray-500">@yield('page-subtitle', 'Selamat datang kembali, ' . (Auth::user()?->name ?? 'Dokter') . '!')</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500"><i class="fas fa-calendar-alt text-slate-800 mr-1"></i>{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
      </div>
    </header>

    <main class="flex-1 p-4 md:p-8 overflow-y-auto">
      <div class="max-w-6xl mx-auto w-full min-w-0">
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
      @yield('content')
      </div>
    </main>
  </div>

</div>

@stack('scripts')
</body>
</html>
