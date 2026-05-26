<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>403 Akses Ditolak | Sahaduta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; background: #f8fafc; }
    h1, h2 { font-family: 'Sora', sans-serif; }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
  <div class="text-center max-w-lg">
    <div class="relative w-48 h-48 mx-auto mb-8">
      <div class="absolute inset-0 bg-red-100 rounded-full blur-2xl opacity-60"></div>
      <div class="relative flex items-center justify-center w-full h-full bg-white border border-red-50 rounded-full shadow-xl">
        <i class="fas fa-lock text-6xl text-red-500"></i>
      </div>
    </div>
    <h1 class="text-7xl font-extrabold text-slate-800 mb-4 tracking-tighter">403</h1>
    <h2 class="text-2xl font-bold text-slate-700 mb-3">Akses Ditolak</h2>
    <p class="text-slate-500 mb-8 leading-relaxed">
      Maaf, Anda tidak memiliki izin atau sub-akses yang diperlukan untuk melihat halaman ini atau melakukan aksi tersebut. Hubungi Administrator jika terjadi kesalahan.
    </p>
    <div class="flex items-center justify-center gap-3">
      <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30 hover:shadow-red-500/50 hover:-translate-y-0.5">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
    </div>
  </div>
</body>
</html>
