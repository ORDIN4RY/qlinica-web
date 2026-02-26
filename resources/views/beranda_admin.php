<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sahaduta Klinik — Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

  <style>
    /* ============================================================
       CSS VARIABLES — Ganti warna di sini jika perlu
    ============================================================ */
    :root {
      --navy:   #0f2144;
      --navy2:  #162d5a;
      --biru:   #2563eb;
      --ungu:   #7c3aed;
      --putih:  #ffffff;
      --bg:     #f0f4fc;
      --border: #e2e8f4;
      --teks:   #0f2144;
      --abu:    #64748b;
      --terang: #94a3b8;
      --hijau:  #10b981;
      --merah:  #ef4444;
      --kuning: #f59e0b;

      /* Lebar sidebar */
      --sb-lebar: 200px;   /* saat terbuka penuh */
      --sb-kecil: 95px;    /* saat terlipat (ikon + teks kecil) */

      --radius: 15px;
      --shadow: 0 2px 8px rgba(15,33,68,.06), 0 8px 24px rgba(15,33,68,.08);
    }

    /* ============================================================
       RESET & BASE
    ============================================================ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--teks);
      display: flex;
      min-height: 100vh;
    }

    /* ============================================================
       SIDEBAR
    ============================================================ */
    .sidebar {
      width: var(--sb-lebar);
      background: var(--navy);
      position: fixed;
      top: 0; left: 0; bottom: 0;
      display: flex;
      flex-direction: column;
      z-index: 300;
      transition: width 0.25s cubic-bezier(.4,0,.2,1);
      overflow: hidden;
    }

    /* Dekorasi gradient di belakang sidebar */
    .sidebar::after {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      background:
        radial-gradient(ellipse 120% 40% at 110% 10%, rgba(37,99,235,.22) 0%, transparent 60%),
        radial-gradient(ellipse 80% 40% at -10% 90%, rgba(124,58,237,.18) 0%, transparent 60%);
    }

    /* ---- Header sidebar: logo + nama klinik ---- */
    .sb-header {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 20px 16px;
      border-bottom: 1px solid rgba(255,255,255,.06);
      position: relative;
      z-index: 1;
      flex-shrink: 0;
    }

    .sb-logo {
      width: 40px; height: 40px;
      border-radius: 12px;
      background: linear-gradient(135deg, #3b82f6, #1e40af);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Sora', sans-serif;
      font-weight: 800; font-size: 18px;
      color: #fff;
      box-shadow: 0 4px 16px rgba(37,99,235,.5);
      flex-shrink: 0;
    }

    .sb-nama {
      overflow: hidden;
      white-space: nowrap;
    }

    .sb-nama-utama {
      font-family: 'Sora', sans-serif;
      font-weight: 700; font-size: 14px;
      color: #fff;
    }

    .sb-nama-sub {
      font-size: 10.5px;
      color: rgba(255,255,255,.3);
      margin-top: 1px;
    }

    /* ---- Navigasi ---- */
    .sb-nav {
      padding: 8px 10px;
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      position: relative;
      z-index: 1;
    }

    /* Scrollbar tipis */
    .sb-nav::-webkit-scrollbar { width: 3px; }
    .sb-nav::-webkit-scrollbar-track { background: transparent; }
    .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

    .nav-grup-label {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 1.8px;
      text-transform: uppercase;
      color: rgba(255,255,255,.2);
      padding: 0 10px;
      margin: 16px 0 4px;
      white-space: nowrap;
      overflow: hidden;
      transition: opacity .2s;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 10px;
      border-radius: 10px;
      color: rgba(255,255,255,.45);
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: background .15s, color .15s;
      margin-bottom: 1px;
      white-space: nowrap;
      overflow: hidden;
      position: relative;
    }

    .nav-item:hover {
      background: rgba(255,255,255,.07);
      color: rgba(255,255,255,.9);
    }

    .nav-item.aktif {
      background: linear-gradient(135deg, rgba(37,99,235,.8), rgba(79,70,229,.7));
      color: #fff;
      font-weight: 600;
      box-shadow: 0 4px 14px rgba(37,99,235,.3);
    }

    /* Ikon navigasi */
    .nav-ikon {
      width: 18px; height: 18px;
      flex-shrink: 0;
      opacity: .7;
      transition: opacity .15s;
    }

    .nav-item:hover .nav-ikon,
    .nav-item.aktif .nav-ikon { opacity: 1; }

    /* Teks label menu (horizontal, saat sidebar terbuka) */
    .nav-label {
      flex: 1;
      overflow: hidden;
      white-space: nowrap;
    }

    /* Teks kecil di bawah ikon (hanya muncul saat terlipat) */
    .nav-mini-label {
      display: none;
      font-size: 9px;
      font-weight: 600;
      text-align: center;
      color: rgba(255,255,255,.45);
      line-height: 1;
      margin-top: 3px;
      letter-spacing: .2px;
      max-width: 60px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .nav-item.aktif .nav-mini-label { color: rgba(255,255,255,.9); }

    /* Badge angka */
    .nav-badge {
      background: var(--merah);
      color: #fff;
      font-size: 9px;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 99px;
      flex-shrink: 0;
    }

    /* Tooltip saat sidebar terlipat (hover muncul nama) */
    .nav-tooltip {
      display: none;
      position: absolute;
      left: calc(var(--sb-kecil) + 4px);
      top: 50%;
      transform: translateY(-50%);
      background: var(--navy2);
      color: #fff;
      font-size: 12px;
      font-weight: 600;
      padding: 5px 12px;
      border-radius: 8px;
      white-space: nowrap;
      box-shadow: 0 6px 20px rgba(0,0,0,.35);
      z-index: 999;
      pointer-events: none;
      border: 1px solid rgba(255,255,255,.08);
    }

    /* ---- Mode terlipat (sidebar kecil) ---- */
    body.sb-kecil .sidebar         { width: var(--sb-kecil); }
    body.sb-kecil .sb-nama         { display: none; }
    body.sb-kecil .nav-grup-label  { opacity: 0; height: 0; margin: 4px 0 0; }
    body.sb-kecil .nav-badge       { display: none; }
    body.sb-kecil .nav-label       { display: none; }
    body.sb-kecil .nav-mini-label  { display: block; }  /* tampilkan label kecil */
    body.sb-kecil .nav-tooltip     { display: none !important; } /* tidak perlu tooltip lagi */

    /* Item menu saat terlipat: susun ikon + teks secara vertikal */
    body.sb-kecil .nav-item {
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 0;
      padding: 8px 4px;
      text-align: center;
    }

    body.sb-kecil .nav-ikon {
      width: 20px; height: 20px;
    }

    /* Konten utama ikut geser */
    .konten-utama {
      margin-left: var(--sb-lebar);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      transition: margin-left 0.25s cubic-bezier(.4,0,.2,1);
    }

    body.sb-kecil .konten-utama { margin-left: var(--sb-kecil); }

    /* ============================================================
       OVERLAY MOBILE
    ============================================================ */
    .overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(15,33,68,.55);
      z-index: 200;
      backdrop-filter: blur(4px);
    }
    .overlay.aktif { display: block; }

    /* ============================================================
       TOPBAR
    ============================================================ */
    .topbar {
      height: 62px;
      background: var(--putih);
      border-bottom: 1px solid var(--border);
      padding: 0 22px;
      display: flex;
      align-items: center;
      gap: 14px;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 1px 0 var(--border), 0 4px 16px rgba(15,33,68,.04);
    }

    /* Tombol toggle hamburger */
    .btn-toggle {
      width: 38px; height: 38px;
      border-radius: 10px;
      background: var(--bg);
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      color: var(--abu);
      flex-shrink: 0;
      transition: all .15s;
    }
    .btn-toggle:hover { background: #dbeafe; color: var(--biru); border-color: #bfdbfe; }

    .topbar-info { flex: 1; }

    .topbar-judul {
      font-family: 'Sora', sans-serif;
      font-size: 15px; font-weight: 700;
      color: var(--teks);
    }

    .topbar-sub {
      font-size: 11.5px;
      color: var(--terang);
      margin-top: 1px;
    }

    .topbar-sub b { color: var(--biru); font-weight: 600; }

    .topbar-aksi { display: flex; align-items: center; gap: 8px; }

    /* Date picker button */
    .date-btn {
      display: flex; align-items: center; gap: 7px;
      background: #eff6ff;
      border: 1px solid #dbeafe;
      padding: 7px 14px;
      border-radius: 99px;
      font-size: 12.5px; font-weight: 700;
      color: var(--biru);
      cursor: pointer;
      white-space: nowrap;
      transition: background .15s;
      user-select: none;
    }
    .date-btn:hover { background: #dbeafe; }
    .date-btn svg { width: 13px; height: 13px; flex-shrink: 0; transition: transform .2s; }
    .date-btn.open svg.chevron { transform: rotate(180deg); }

    /* Dropdown kalender */
    .date-picker { position: relative; }

    .date-drop {
      position: absolute;
      top: calc(100% + 8px); right: 0;
      width: 260px;
      background: var(--putih);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: 0 16px 48px rgba(15,33,68,.14);
      padding: 16px;
      z-index: 600;
      display: none;
    }
    .date-drop.open { display: block; animation: fadeDown .18s ease; }

    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-6px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .dd-tahun-row {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 12px;
    }

    .dd-tahun-angka {
      font-family: 'Sora', sans-serif;
      font-weight: 700; font-size: 16px;
      color: var(--teks);
    }

    .dd-tahun-btn {
      width: 28px; height: 28px;
      border-radius: 7px;
      border: 1px solid var(--border);
      background: var(--bg);
      color: var(--abu);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      transition: all .14s;
    }
    .dd-tahun-btn:hover { background: #eff6ff; color: var(--biru); border-color: #bfdbfe; }

    .dd-bulan-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 5px; }

    .dd-bulan-item {
      padding: 7px 2px;
      border-radius: 8px;
      font-size: 12px; font-weight: 500;
      color: var(--abu);
      text-align: center;
      cursor: pointer;
      transition: all .14s;
      border: 1px solid transparent;
      position: relative;
    }
    .dd-bulan-item:hover { background: #eff6ff; color: var(--biru); }
    .dd-bulan-item.aktif  { background: var(--biru); color: #fff; font-weight: 700; }
    .dd-bulan-item.ada-data::after {
      content: '';
      position: absolute;
      bottom: 3px; left: 50%;
      transform: translateX(-50%);
      width: 4px; height: 4px;
      border-radius: 50%;
      background: var(--hijau);
    }
    .dd-bulan-item.aktif::after { background: rgba(255,255,255,.7); }

    /* Ikon button kecil (notif, setting) */
    .btn-ikon {
      width: 38px; height: 38px;
      border-radius: 10px;
      background: var(--putih);
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
      color: var(--abu);
      position: relative;
      transition: all .15s;
    }
    .btn-ikon:hover { background: #eff6ff; color: var(--biru); border-color: #bfdbfe; }

    .notif-dot {
      position: absolute;
      top: 8px; right: 8px;
      width: 7px; height: 7px;
      background: var(--merah);
      border-radius: 50%;
      border: 2px solid var(--putih);
    }

    /* ---- User Profile Dropdown (topbar kanan) ---- */
    .user-dropdown { position: relative; }

    .user-btn {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 5px 12px 5px 5px;
      border-radius: 99px;
      border: 1px solid var(--border);
      background: var(--putih);
      cursor: pointer;
      transition: all .15s;
      user-select: none;
    }
    .user-btn:hover { background: #f8faff; border-color: #c7d7f5; }

    .user-btn-avatar {
      width: 30px; height: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, #3b82f6, #7c3aed);
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 13px;
      color: #fff;
      flex-shrink: 0;
    }

    .user-btn-name {
      font-size: 13px;
      font-weight: 600;
      color: var(--teks);
      white-space: nowrap;
    }

    .user-btn-chevron {
      width: 14px; height: 14px;
      color: var(--terang);
      transition: transform .2s;
      flex-shrink: 0;
    }

    .user-btn.open .user-btn-chevron { transform: rotate(180deg); }

    /* Panel dropdown user */
    .user-drop {
      position: absolute;
      top: calc(100% + 8px);
      right: 0;
      width: 200px;
      background: var(--putih);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: 0 12px 40px rgba(15,33,68,.14);
      overflow: hidden;
      z-index: 600;
      display: none;
    }
    .user-drop.open { display: block; animation: fadeDown .18s ease; }

    /* Info user di dalam dropdown */
    .user-drop-head {
      padding: 14px 16px 10px;
      border-bottom: 1px solid var(--border);
    }
    .user-drop-head-name {
      font-size: 13px; font-weight: 700;
      color: var(--teks);
    }
    .user-drop-head-role {
      font-size: 11px;
      color: var(--terang);
      margin-top: 1px;
    }

    /* Item menu dropdown */
    .user-drop-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 11px 16px;
      font-size: 13px;
      font-weight: 500;
      color: var(--abu);
      cursor: pointer;
      transition: background .13s, color .13s;
    }
    .user-drop-item:hover { background: #f8faff; color: var(--teks); }
    .user-drop-item.logout { color: var(--merah); }
    .user-drop-item.logout:hover { background: #fff1f1; color: #c00; }
    .user-drop-item svg { width: 15px; height: 15px; flex-shrink: 0; opacity: .7; }

    /* ============================================================
       HALAMAN ISI
    ============================================================ */
    .halaman { padding: 22px; flex: 1; }

    /* ============================================================
       KPI CARDS — 3 kartu besar
    ============================================================ */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
      margin-bottom: 16px;
    }

    .kpi-card {
      background: var(--putih);
      border-radius: var(--radius);
      padding: 20px 22px;
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      transition: transform .2s, box-shadow .2s;
      animation: riseUp .4s ease both;
    }

    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(15,33,68,.12); }
    .kpi-card:nth-child(2) { animation-delay: .07s; }
    .kpi-card:nth-child(3) { animation-delay: .14s; }

    /* Garis aksen warna di atas kartu */
    .kpi-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
    }
    .kpi-card.biru::before   { background: linear-gradient(90deg,#2563eb,#6366f1); }
    .kpi-card.hijau::before  { background: linear-gradient(90deg,#10b981,#06b6d4); }
    .kpi-card.kuning::before { background: linear-gradient(90deg,#f59e0b,#ef4444); }

    .kpi-top {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 14px;
    }

    .kpi-icon {
      width: 42px; height: 42px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
    }
    .kpi-icon.bg-biru   { background: #eff6ff; }
    .kpi-icon.bg-hijau  { background: #ecfdf5; }
    .kpi-icon.bg-kuning { background: #fffbeb; }

    .badge {
      font-size: 11px; font-weight: 700;
      padding: 3px 9px; border-radius: 99px;
    }
    .badge.naik { background: #ecfdf5; color: var(--hijau); }
    .badge.diam { background: #f1f5f9; color: var(--terang); border: 1px solid var(--border); }

    .kpi-label {
      font-size: 10px; font-weight: 700;
      color: var(--terang);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 5px;
    }

    .kpi-angka {
      font-family: 'Sora', sans-serif;
      font-size: 34px; font-weight: 800;
      color: var(--teks);
      letter-spacing: -1.5px;
      line-height: 1;
      margin-bottom: 14px;
    }

    .kpi-pills { display: flex; gap: 7px; flex-wrap: wrap; }

    .pill {
      font-size: 11.5px;
      padding: 4px 10px;
      border-radius: 99px;
      border: 1px solid var(--border);
      color: var(--abu);
      background: #f8faff;
    }
    .pill strong { font-weight: 700; color: var(--teks); }

    @keyframes riseUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ============================================================
       GRID 2 KOLOM — penyakit + chart kanan
    ============================================================ */
    .dua-kolom {
      display: grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 14px;
      margin-bottom: 14px;
      align-items: stretch;
    }

    /* Kartu penyakit isi penuh tinggi */
    .card-penuh { display: flex; flex-direction: column; }
    .card-penuh .card-body { flex: 1; display: flex; flex-direction: column; }
    .card-penuh .penyakit-list { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }

    /* ============================================================
       KARTU GENERIK
    ============================================================ */
    .card {
      background: var(--putih);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      overflow: hidden;
      animation: riseUp .4s ease both;
      transition: box-shadow .2s;
    }
    .card:hover { box-shadow: 0 12px 32px rgba(15,33,68,.1); }

    .card-header {
      padding: 18px 20px 0;
      display: flex; align-items: flex-start; justify-content: space-between;
      gap: 10px;
    }

    .card-judul { font-size: 13.5px; font-weight: 700; color: var(--teks); margin-bottom: 2px; }
    .card-sub   { font-size: 11px; color: var(--terang); }
    .card-body  { padding: 14px 20px 20px; }

    .btn-lihat {
      font-size: 11px; font-weight: 600;
      padding: 4px 12px; border-radius: 99px;
      background: #eff6ff;
      border: 1px solid #dbeafe;
      color: var(--biru);
      cursor: pointer;
      white-space: nowrap; flex-shrink: 0;
      transition: background .15s;
    }
    .btn-lihat:hover { background: #dbeafe; }

    /* ============================================================
       DAFTAR TOP 10 PENYAKIT
    ============================================================ */
    .penyakit-list { list-style: none; }

    .penyakit-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 0;
      border-bottom: 1px solid #f0f4fb;
    }
    .penyakit-item:last-child { border: none; }

    .urut {
      width: 26px; height: 26px;
      border-radius: 8px;
      flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700;
    }
    .urut.top { background: var(--navy); color: #fff; }
    .urut.biasa { background: #f1f5f9; color: var(--abu); border: 1px solid var(--border); }

    .penyakit-nama { flex: 1; font-size: 12.5px; font-weight: 500; color: var(--teks); line-height: 1.3; }

    .bar-wrap { width: 52px; height: 5px; background: #f1f5f9; border-radius: 99px; flex-shrink: 0; }
    .bar-fill { height: 5px; border-radius: 99px; background: linear-gradient(90deg,var(--biru),#6366f1); }

    .penyakit-angka { font-size: 12.5px; font-weight: 700; color: var(--teks); min-width: 46px; text-align: right; }

    /* ============================================================
       GRAFIK
    ============================================================ */
    .grafik-kecil { position: relative; height: 150px; }
    .grafik-besar { position: relative; height: 220px; }

    /* ============================================================
       KEPUASAN — legenda
    ============================================================ */
    .kepuasan-legenda {
      display: flex; flex-wrap: wrap; gap: 5px 10px;
      margin-bottom: 10px;
    }
    .leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--abu); }
    .leg-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ============================================================
       GENDER
    ============================================================ */
    .gender-row { display: flex; gap: 9px; margin-bottom: 11px; }

    .gender-box {
      flex: 1;
      padding: 11px 13px;
      border-radius: 11px;
      border: 1px solid var(--border);
      display: flex; align-items: center; gap: 9px;
    }

    .gender-icon {
      width: 33px; height: 33px;
      border-radius: 9px;
      flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
    }

    .gender-label { font-size: 10px; color: var(--terang); font-weight: 500; }
    .gender-angka { font-family: 'Sora',sans-serif; font-size: 18px; font-weight: 800; color: var(--teks); }
    .gender-pct   { font-size: 10px; font-weight: 700; }

    .gender-bar-wrap {
      height: 5px;
      background: #f1f5f9;
      border-radius: 99px;
      overflow: hidden;
      margin-bottom: 12px;
    }
    .gender-bar-fill {
      height: 100%;
      border-radius: 99px;
      background: linear-gradient(90deg, var(--biru) 41.3%, var(--ungu) 41.3%);
    }

    /* ============================================================
       FOOTER
    ============================================================ */
    footer {
      text-align: center;
      padding: 16px;
      font-size: 11.5px;
      color: var(--terang);
      border-top: 1px solid var(--border);
      background: var(--putih);
    }
    footer strong { color: var(--teks); }

    /* ============================================================
       RESPONSIVE
    ============================================================ */
    @media (max-width: 1100px) {
      .dua-kolom { grid-template-columns: 1fr; }
    }

    @media (max-width: 900px) {
      .kpi-grid { grid-template-columns: 1fr 1fr; }
      .kpi-grid .kpi-card:nth-child(3) { grid-column: 1/-1; }
    }

    @media (max-width: 700px) {
      /* Mobile: sidebar tersembunyi, toggle pakai overlay */
      .sidebar {
        width: var(--sb-lebar) !important;
        transform: translateX(-100%);
        transition: transform .28s ease;
      }
      .sidebar.mobile-open { transform: translateX(0); }
      .konten-utama { margin-left: 0 !important; }
      body.sb-kecil .konten-utama { margin-left: 0 !important; }
      .halaman { padding: 14px; }
      .kpi-grid { grid-template-columns: 1fr; }
      .kpi-grid .kpi-card:nth-child(3) { grid-column: auto; }
      .date-drop { right: -10px; width: 240px; }
    }

    @media (max-width: 480px) {
      .gender-row { flex-direction: column; }
      .halaman { padding: 10px; }
    }
  </style>
</head>

<body>
  <!-- Overlay gelap (mobile) -->
  <div class="overlay" id="overlay"></div>

  <!-- ======================== SIDEBAR ======================== -->
  <aside class="sidebar" id="sidebar">

    <!-- Logo klinik -->
    <div class="sb-header">
      <div class="sb-logo">S</div>
      <div class="sb-nama">
        <div class="sb-nama-utama">Sahaduta Klinik</div>
        <div class="sb-nama-sub">Management System</div>
      </div>
    </div>



    <!-- Navigasi -->
    <nav class="sb-nav">

      <div class="nav-grup-label">Menu Utama</div>

      <div class="nav-item aktif">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
          <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>
        <span class="nav-label">Dashboard</span>
        <span class="nav-mini-label">Dashboard</span>
        <span class="nav-tooltip">Dashboard</span>
      </div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
        <span class="nav-label">Pasien</span>
        <span class="nav-badge">12</span>
        <span class="nav-mini-label">Pasien</span>
        <span class="nav-tooltip">Pasien</span>
      </div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="nav-label">Pemesanan</span>
        <span class="nav-mini-label">Pesan</span>
        <span class="nav-tooltip">Pemesanan</span>
      </div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <span class="nav-label">Pegawai</span>
        <span class="nav-mini-label">Pegawai</span>
        <span class="nav-tooltip">Pegawai</span>
      </div>

      <div class="nav-grup-label">Laporan</div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span class="nav-label">Komentar</span>
        <span class="nav-mini-label">Komentar</span>
        <span class="nav-tooltip">Komentar</span>
      </div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14,2 14,8 20,8"/>
          <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        <span class="nav-label">Laporan</span>
        <span class="nav-mini-label">Laporan</span>
        <span class="nav-tooltip">Laporan</span>
      </div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
        </svg>
        <span class="nav-label">Lap. Penanganan</span>
        <span class="nav-mini-label">Penanganan</span>
        <span class="nav-tooltip">Lap. Penanganan</span>
      </div>

      <div class="nav-item">
        <svg class="nav-ikon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
        <span class="nav-label">ICD X</span>
        <span class="nav-mini-label">ICD X</span>
        <span class="nav-tooltip">ICD X</span>
      </div>

    </nav>
  </aside>

  <!-- ======================== KONTEN ======================== -->
  <div class="konten-utama">

    <!-- TOPBAR -->
    <header class="topbar">
      <!-- Tombol buka/tutup sidebar -->
      <button class="btn-toggle" id="btnToggle" title="Buka/Tutup Sidebar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>

      <div class="topbar-info">
        <div class="topbar-judul">Dashboard</div>
        <div class="topbar-sub">Sahaduta Klinik &rsaquo; <b>Overview</b></div>
      </div>

      <div class="topbar-aksi">
        <!-- Date Picker -->
        <div class="date-picker" id="datePicker">
          <div class="date-btn" id="dateBtn">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
              <rect x="3" y="4" width="18" height="18" rx="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="labelBulan">Februari 2025</span>
            <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <polyline points="6,9 12,15 18,9"/>
            </svg>
          </div>

          <div class="date-drop" id="dateDrop">
            <div class="dd-tahun-row">
              <button class="dd-tahun-btn" id="btnTahunPrev">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <polyline points="15,18 9,12 15,6"/>
                </svg>
              </button>
              <span class="dd-tahun-angka" id="tahunLabel">2025</span>
              <button class="dd-tahun-btn" id="btnTahunNext">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <polyline points="9,18 15,12 9,6"/>
                </svg>
              </button>
            </div>
            <div class="dd-bulan-grid" id="bulanGrid"></div>
          </div>
        </div>

        <!-- Notifikasi -->
        <div class="btn-ikon" title="Notifikasi">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <span class="notif-dot"></span>
        </div>

        <!-- Tombol Pengaturan -->
        <div class="btn-ikon" title="Pengaturan">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
          </svg>
        </div>

        <!-- Dropdown User Profile -->
        <div class="user-dropdown" id="userDropdown">
          <div class="user-btn" id="userBtn">
            <div class="user-btn-avatar">P</div>
            <span class="user-btn-name">Prodevis Team</span>
            <svg class="user-btn-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <polyline points="6,9 12,15 18,9"/>
            </svg>
          </div>

          <div class="user-drop" id="userDrop">
            <!-- Info singkat user -->
            <div class="user-drop-head">
              <div class="user-drop-head-name">Prodevis Team</div>
              <div class="user-drop-head-role">Administrator</div>
            </div>

            <!-- Menu: Ganti Password -->
            <div class="user-drop-item">
              <span>Ganti Password</span>
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </div>

            <!-- Menu: Log Out -->
            <div class="user-drop-item logout">
              <span>Log Out</span>
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16,17 21,12 16,7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- ISI HALAMAN -->
    <div class="halaman">

      <!-- 3 KPI Cards -->
      <div class="kpi-grid">

        <div class="kpi-card biru">
          <div class="kpi-top">
            <div class="kpi-icon bg-biru">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
              </svg>
            </div>
            <span class="badge naik">↑ 8.2%</span>
          </div>
          <div class="kpi-label">Total Tahun Ini</div>
          <div class="kpi-angka" id="kpiTotalTahun">1.442</div>
          <div class="kpi-pills">
            <div class="pill">♂ <strong id="kpiLakiTahun">595</strong></div>
            <div class="pill">♀ <strong id="kpiPerempuanTahun">847</strong></div>
          </div>
        </div>

        <div class="kpi-card hijau">
          <div class="kpi-top">
            <div class="kpi-icon bg-hijau">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="1.8">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <span class="badge naik" id="badgeBulan">↑ 4.1%</span>
          </div>
          <div class="kpi-label" id="labelBulanKPI">Februari</div>
          <div class="kpi-angka" id="kpiBulan">404</div>
          <div class="kpi-pills">
            <div class="pill">♂ <strong id="kpiLakiBulan">153</strong></div>
            <div class="pill">♀ <strong id="kpiPerempuanBulan">251</strong></div>
          </div>
        </div>

        <div class="kpi-card kuning">
          <div class="kpi-top">
            <div class="kpi-icon bg-kuning">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.8">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12,6 12,12 16,14"/>
              </svg>
            </div>
            <span class="badge diam">Minggu ini</span>
          </div>
          <div class="kpi-label">Minggu Ini</div>
          <div class="kpi-angka">0</div>
          <div class="kpi-pills">
            <div class="pill">♂ <strong>0</strong></div>
            <div class="pill">♀ <strong>0</strong></div>
          </div>
        </div>

      </div>

      <!-- Grid 2 kolom: penyakit + chart -->
      <div class="dua-kolom">

        <!-- Top 10 Penyakit -->
        <div class="card card-penuh" style="animation-delay:.1s">
          <div class="card-header">
            <div>
              <div class="card-judul">Top 10 Penyakit</div>
              <div class="card-sub">Berdasarkan jumlah kunjungan</div>
            </div>
            <span class="btn-lihat">Lihat Semua</span>
          </div>
          <div class="card-body">
            <ul class="penyakit-list" id="penyakitList"></ul>
          </div>
        </div>

        <!-- Kolom kanan: Kepuasan + Gender -->
        <div style="display:flex; flex-direction:column; gap:14px;">

          <!-- Kepuasan Pasien -->
          <div class="card" style="animation-delay:.16s">
            <div class="card-header">
              <div>
                <div class="card-judul">Kepuasan Pasien</div>
                <div class="card-sub">Rating dari pasien</div>
              </div>
            </div>
            <div class="card-body">
              <div class="kepuasan-legenda" id="kepuasanLeg"></div>
              <div class="grafik-kecil"><canvas id="grafikKepuasan"></canvas></div>
            </div>
          </div>

          <!-- Distribusi Gender -->
          <div class="card" style="animation-delay:.22s">
            <div class="card-header">
              <div>
                <div class="card-judul">Distribusi Gender</div>
                <div class="card-sub">Total tahun 2025</div>
              </div>
            </div>
            <div class="card-body">
              <div class="gender-row">
                <div class="gender-box">
                  <div class="gender-icon" style="background:#eff6ff">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2.2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                  </div>
                  <div>
                    <div class="gender-label">Laki-laki</div>
                    <div class="gender-angka">595</div>
                    <div class="gender-pct" style="color:#2563eb">41.3%</div>
                  </div>
                </div>
                <div class="gender-box">
                  <div class="gender-icon" style="background:#f5f3ff">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2.2">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <circle cx="12" cy="7" r="4"/>
                    </svg>
                  </div>
                  <div>
                    <div class="gender-label">Perempuan</div>
                    <div class="gender-angka" style="color:#7c3aed">847</div>
                    <div class="gender-pct" style="color:#7c3aed">58.7%</div>
                  </div>
                </div>
              </div>
              <div class="gender-bar-wrap">
                <div class="gender-bar-fill" style="width:100%"></div>
              </div>
              <div class="grafik-kecil"><canvas id="grafikGender"></canvas></div>
            </div>
          </div>

        </div>
      </div>

      <!-- Grafik Bulanan -->
      <div class="card" style="animation-delay:.28s; margin-bottom:16px">
        <div class="card-header">
          <div>
            <div class="card-judul">Grafik Penanganan per Bulan — <span id="labelTahunGrafik">2025</span></div>
            <div class="card-sub" id="labelSubGrafik">Data kumulatif s.d. Februari 2025</div>
          </div>
        </div>
        <div class="card-body">
          <div class="grafik-besar"><canvas id="grafikBulanan"></canvas></div>
        </div>
      </div>

    </div><!-- /halaman -->

    <footer>&copy; 2025 <strong>Sahaduta Klinik</strong> &middot; Management Information System</footer>

  </div><!-- /konten-utama -->


  <script>
    // ==============================================================
    // 0. USER PROFILE DROPDOWN
    // ==============================================================
    var userBtn  = document.getElementById('userBtn');
    var userDrop = document.getElementById('userDrop');

    userBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = userDrop.classList.contains('open');
      userDrop.classList.toggle('open', !isOpen);
      userBtn.classList.toggle('open', !isOpen);
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function () {
      userDrop.classList.remove('open');
      userBtn.classList.remove('open');
    });

    userDrop.addEventListener('click', function (e) { e.stopPropagation(); });

    // ==============================================================
    // 1. SIDEBAR TOGGLE
    // ==============================================================
    var btnToggle = document.getElementById('btnToggle');
    var sidebar   = document.getElementById('sidebar');
    var overlay   = document.getElementById('overlay');

    btnToggle.addEventListener('click', function () {
      if (window.innerWidth <= 700) {
        // Mobile: slide in/out dengan overlay
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('aktif');
      } else {
        // Desktop: perkecil/perbesar sidebar dengan animasi halus
        document.body.classList.toggle('sb-kecil');
      }
    });

    // Tutup sidebar mobile saat klik overlay
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('mobile-open');
      overlay.classList.remove('aktif');
    });


    // ==============================================================
    // 2. NAMA BULAN
    // ==============================================================
    var BULAN_PANJANG = ['Januari','Februari','Maret','April','Mei','Juni',
                         'Juli','Agustus','September','Oktober','November','Desember'];

    var BULAN_PENDEK  = ['Jan','Feb','Mar','Apr','Mei','Jun',
                         'Jul','Agt','Sep','Okt','Nov','Des'];

    var bulanAdaData   = [0, 1];   // index bulan yang punya data
    var bulanTerpilih  = 1;        // Februari (0-based)
    var tahunTerpilih  = 2025;
    var tahunDropdown  = 2025;


    // ==============================================================
    // 3. DATE PICKER
    // ==============================================================
    var dateBtn    = document.getElementById('dateBtn');
    var dateDrop   = document.getElementById('dateDrop');
    var labelBulan = document.getElementById('labelBulan');
    var tahunLabel = document.getElementById('tahunLabel');
    var bulanGrid  = document.getElementById('bulanGrid');

    function renderBulanGrid() {
      bulanGrid.innerHTML = '';
      tahunLabel.textContent = tahunDropdown;

      BULAN_PENDEK.forEach(function (nama, i) {
        var el = document.createElement('div');
        el.className = 'dd-bulan-item';
        if (i === bulanTerpilih && tahunDropdown === tahunTerpilih) el.classList.add('aktif');
        if (bulanAdaData.includes(i)) el.classList.add('ada-data');
        el.textContent = nama;

        el.addEventListener('click', function () {
          bulanTerpilih = i;
          tahunTerpilih = tahunDropdown;
          labelBulan.textContent = BULAN_PANJANG[i] + ' ' + tahunDropdown;
          tutupDrop();
          renderBulanGrid();
          updateDashboard();
        });

        bulanGrid.appendChild(el);
      });
    }

    function bukaDrop()  { dateDrop.classList.add('open'); dateBtn.classList.add('open'); renderBulanGrid(); }
    function tutupDrop() { dateDrop.classList.remove('open'); dateBtn.classList.remove('open'); }

    dateBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      dateDrop.classList.contains('open') ? tutupDrop() : bukaDrop();
    });

    document.addEventListener('click', tutupDrop);
    dateDrop.addEventListener('click', function (e) { e.stopPropagation(); });

    document.getElementById('btnTahunPrev').addEventListener('click', function () { tahunDropdown--; renderBulanGrid(); });
    document.getElementById('btnTahunNext').addEventListener('click', function () { tahunDropdown++; renderBulanGrid(); });


    // ==============================================================
    // 4. DATA KUNJUNGAN PER BULAN
    // ==============================================================
    var dataLaki      = [414, 153, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var dataPerempuan = [624, 251, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


    // ==============================================================
    // 5. TOP 10 PENYAKIT
    // ==============================================================
    var dataPenyakit = [
      { nama: 'Streptococcal pharyngitis',                                   n: 4859 },
      { nama: 'General medical examination',                                 n: 4601 },
      { nama: 'Typhoid fever',                                               n: 4044 },
      { nama: 'Acute upper respiratory infection, unspecified',              n: 3536 },
      { nama: 'Gastritis, unspecified',                                      n: 3004 },
      { nama: 'Unspecified acute lower respiratory infection',               n: 2866 },
      { nama: 'Essential (primary) hypertension',                            n: 2860 },
      { nama: 'Diarrhoea and gastroenteritis of presumed infectious origin', n: 1797 },
      { nama: 'Fever, unspecified',                                          n: 1521 },
      { nama: 'Ulcerative colitis, unspecified',                             n: 1488 }
    ];

    var maxPenyakit = dataPenyakit[0].n;
    var elList = document.getElementById('penyakitList');

    dataPenyakit.forEach(function (p, i) {
      var nomor = i + 1;
      var bar   = ((p.n / maxPenyakit) * 100).toFixed(1) + '%';
      var kelas = nomor <= 3 ? 'top' : 'biasa';

      var li = document.createElement('li');
      li.className = 'penyakit-item';
      li.innerHTML =
        '<div class="urut ' + kelas + '">' + nomor + '</div>' +
        '<div class="penyakit-nama">' + p.nama + '</div>' +
        '<div class="bar-wrap"><div class="bar-fill" style="width:' + bar + '"></div></div>' +
        '<div class="penyakit-angka">' + p.n.toLocaleString('id') + '</div>';
      elList.appendChild(li);
    });


    // ==============================================================
    // 6. GRAFIK KEPUASAN (Donut)
    // ==============================================================
    var kepuasan = {
      labels: ['Sangat Puas', 'Puas', 'Cukup', 'Buruk', 'Sangat Buruk'],
      data:   [78, 14, 5, 2, 1],
      warna:  ['#0f2144', '#2563eb', '#60a5fa', '#f59e0b', '#ef4444']
    };

    // Buat legenda
    var legEl = document.getElementById('kepuasanLeg');
    kepuasan.labels.forEach(function (teks, i) {
      var d = document.createElement('div');
      d.className = 'leg-item';
      d.innerHTML = '<span class="leg-dot" style="background:' + kepuasan.warna[i] + '"></span>' +
                    teks + ' (' + kepuasan.data[i] + '%)';
      legEl.appendChild(d);
    });

    Chart.defaults.font.family = 'Inter';
    Chart.defaults.color = '#94a3b8';

    new Chart(document.getElementById('grafikKepuasan'), {
      type: 'doughnut',
      data: {
        labels: kepuasan.labels,
        datasets: [{ data: kepuasan.data, backgroundColor: kepuasan.warna, borderWidth: 3, borderColor: '#fff' }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '72%',
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: function (c) { return ' ' + c.label + ': ' + c.parsed + '%'; } } }
        }
      }
    });


    // ==============================================================
    // 7. GRAFIK GENDER (Donut)
    // ==============================================================
    new Chart(document.getElementById('grafikGender'), {
      type: 'doughnut',
      data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{ data: [595, 847], backgroundColor: ['#2563eb', '#7c3aed'], borderWidth: 3, borderColor: '#fff' }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '68%',
        plugins: { legend: { display: false } }
      }
    });


    // ==============================================================
    // 8. GRAFIK BULANAN (Bar Chart)
    // ==============================================================
    var grafikBulanan = new Chart(document.getElementById('grafikBulanan'), {
      type: 'bar',
      data: {
        labels: BULAN_PENDEK,
        datasets: [
          {
            label: 'Laki-laki',
            data: dataLaki.slice(),
            backgroundColor: dataLaki.map(function () { return 'rgba(37,99,235,.7)'; }),
            borderRadius: 6, borderSkipped: false
          },
          {
            label: 'Perempuan',
            data: dataPerempuan.slice(),
            backgroundColor: dataPerempuan.map(function () { return 'rgba(124,58,237,.7)'; }),
            borderRadius: 6, borderSkipped: false
          }
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { font: { family: 'Inter', size: 12, weight: '600' }, boxWidth: 9, usePointStyle: true, pointStyle: 'circle', padding: 16 }
          },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          x: { stacked: true, grid: { display: false }, border: { display: false } },
          y: { stacked: true, grid: { color: '#f0f4fb' }, border: { display: false } }
        }
      }
    });


    // ==============================================================
    // 9. FORMAT ANGKA (1442 → "1.442")
    // ==============================================================
    function fmt(n) { return n.toLocaleString('id'); }


    // ==============================================================
    // 10. UPDATE DASHBOARD saat bulan berubah
    // ==============================================================
    function updateDashboard() {
      var totalL = 0, totalP = 0;
      for (var i = 0; i <= bulanTerpilih; i++) {
        totalL += dataLaki[i];
        totalP += dataPerempuan[i];
      }
      var total = totalL + totalP;
      var lakiBln     = dataLaki[bulanTerpilih];
      var perempuanBln = dataPerempuan[bulanTerpilih];

      // KPI total tahun
      document.getElementById('kpiTotalTahun').textContent     = fmt(total);
      document.getElementById('kpiLakiTahun').textContent      = fmt(totalL);
      document.getElementById('kpiPerempuanTahun').textContent  = fmt(totalP);

      // KPI bulan ini
      document.getElementById('labelBulanKPI').textContent     = BULAN_PANJANG[bulanTerpilih];
      document.getElementById('kpiBulan').textContent          = fmt(lakiBln + perempuanBln);
      document.getElementById('kpiLakiBulan').textContent      = fmt(lakiBln);
      document.getElementById('kpiPerempuanBulan').textContent  = fmt(perempuanBln);

      // Label grafik
      document.getElementById('labelTahunGrafik').textContent  = tahunTerpilih;
      document.getElementById('labelSubGrafik').textContent    =
        'Data kumulatif s.d. ' + BULAN_PANJANG[bulanTerpilih] + ' ' + tahunTerpilih;

      // Warna bar chart
      var wL = [], wP = [];
      for (var k = 0; k < 12; k++) {
        if (k === bulanTerpilih)     { wL.push('#2563eb');              wP.push('#7c3aed'); }
        else if (k < bulanTerpilih)  { wL.push('rgba(37,99,235,.35)'); wP.push('rgba(124,58,237,.35)'); }
        else                         { wL.push('rgba(37,99,235,.1)');  wP.push('rgba(124,58,237,.1)'); }
      }
      grafikBulanan.data.datasets[0].backgroundColor = wL;
      grafikBulanan.data.datasets[1].backgroundColor = wP;
      grafikBulanan.update('none');
    }


    // ==============================================================
    // 11. INISIALISASI
    // ==============================================================
    renderBulanGrid();
    updateDashboard();
  </script>
</body>
</html>