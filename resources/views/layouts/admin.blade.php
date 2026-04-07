<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
    </style>
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="{{ route('beranda_admin') }}" class="@yield('dashboard_active')">Dashboard</a></li>
            <li><a href="{{ route('admin.pasien') }}" class="@yield('pasien_active')">Pasien</a></li>
            <li><a href="{{ route('admin.pegawai') }}" class="@yield('pegawai_active')">Pegawai</a></li>
            <li><a href="{{ route('admin.pemesanan') }}" class="@yield('pemesanan_active')">Pemesanan</a></li>
            <!-- Tambahkan menu lain sesuai kebutuhan -->
        </ul>
    </div>
    <div class="main-content">
        <h2>@yield('title')</h2>
        @yield('content')
    </div>
</body>
</html>
