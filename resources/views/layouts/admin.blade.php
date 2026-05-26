<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; }
        .sidebar {
            width: 220px;
            background: #2d3748;
            color: #fff;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
        }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar li { margin: 20px 0; }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 30px;
            display: block;
            border-radius: 4px;
        }
        .sidebar a.active, .sidebar a:hover {
            background: #4a5568;
        }
        .main-content {
            margin-left: 220px;
            padding: 30px;
            width: 100%;
        }

        @media print {
            .no-print, .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 10px !important; width: 100% !important; }
        }
    </style>
    @yield('extra_styles')
    @stack('styles')
</head>
<body>
    <div class="sidebar" style="overflow-y: scroll;">
        <ul>
            <li><a href="{{ route('beranda_admin') }}" class="@yield('dashboard_active')">Dashboard</a></li>
            <li><a href="{{ route('admin.pasien') }}" class="@yield('pasien_active')">Pasien</a></li>
            <li><a href="{{ route('admin.pegawai') }}" class="@yield('pegawai_active')">Pegawai</a></li>
            <li><a href="{{ route('admin.komentar') }}" class="@yield('komentar_active')">Komentar</a></li>
            <!-- Tambahkan menu lain sesuai kebutuhan -->
        </ul>
    </div>
    <div class="main-content">
        <h2>@yield('title')</h2>
        @yield('content')
    </div>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
