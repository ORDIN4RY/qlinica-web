<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Petugas | Sahaduta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    html { scroll-behavior: smooth; }

    .input-focus { transition: all 0.2s; }
    .input-focus:focus {
      border-color: #1e3a8a;
      box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
      outline: none;
    }

    .btn-primary {
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px -8px rgba(30, 58, 138, 0.4);
    }

    .float-subtle {
      animation: floatSubtle 7s ease-in-out infinite;
    }
    @keyframes floatSubtle {
      0%   { transform: translateY(0px); }
      50%  { transform: translateY(-12px); }
      100% { transform: translateY(0px); }
    }

    .pulse-soft {
      animation: pulseSoft 5s infinite;
    }
    @keyframes pulseSoft {
      0%   { opacity: 0.2; }
      50%  { opacity: 0.4; }
      100% { opacity: 0.2; }
    }
  </style>
</head>
<body class="bg-white font-sans antialiased text-gray-700 min-h-screen flex flex-col">

  <!-- Navbar -->
  <header class="bg-white/90 backdrop-blur-sm sticky top-0 z-30 border-b border-blue-900/10 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
          <div class="w-8 h-8 bg-blue-900 rounded-xl flex items-center justify-center transition-transform duration-500 group-hover:rotate-3">
            <i class="fas fa-clinic-medical text-white text-lg"></i>
          </div>
          <span class="font-semibold text-xl text-gray-800">Sahaduta</span>
        </a>
        <a href="{{ route('home') }}" class="text-sm text-blue-900 hover:text-blue-700 font-medium transition flex items-center gap-2">
          <i class="fas fa-arrow-left text-xs"></i> Kembali ke Beranda
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-1 flex items-center justify-center py-16 px-4 relative overflow-hidden">

    <!-- Background decorations -->
    <div class="absolute top-10 right-10 w-72 h-72 bg-blue-900/5 rounded-full blur-3xl float-subtle pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-64 h-64 bg-blue-900/8 rounded-full blur-2xl pulse-soft pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

      <!-- Card -->
      <div class="bg-white rounded-3xl shadow-xl border border-blue-900/10 p-8 sm:p-10">

        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-blue-900 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fas fa-user-shield text-white text-2xl"></i>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">Login Admin</h1>
        </div>

        <!-- Error Alert -->
        @if($errors->has('session'))
          <div class="mb-6 bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-clock mr-2"></i>{{ $errors->first('session') }}
          </div>
        @elseif($errors->any())
          <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
          </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login.petugas.submit') }}" class="space-y-5">
          @csrf

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Petugas</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                <i class="fas fa-envelope text-sm"></i>
              </span>
              <input type="email" name="login_id" value="{{ old('login_id') }}" required
                class="input-focus w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl"
                placeholder="Masukkan email petugas">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                <i class="fas fa-lock text-sm"></i>
              </span>
              <input type="password" name="password" required id="passwordInput"
                class="input-focus w-full pl-11 pr-12 py-3 border border-gray-300 rounded-xl"
                placeholder="••••••••">
              <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-eye text-sm" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="remember" class="rounded border-gray-300"> Ingat saya
            </label>
          </div>

          <button type="submit"
            class="btn-primary w-full bg-blue-900 hover:bg-blue-800 text-white py-3 rounded-xl font-semibold text-base mt-2 flex items-center justify-center gap-2">
            <i class="fas fa-sign-in-alt"></i> Masuk 
          </button>
        </form>

      </div>

      <!-- Info note -->
      <p class="text-center text-xs text-gray-400 mt-6">
        Halaman ini hanya untuk petugas dan admin Klinik Sahaduta yang terdaftar.
      </p>
    </div>
  </main>

  <!-- Footer -->
  <footer class="py-4 text-center text-xs text-gray-400 border-t border-gray-100">
    &copy; 2025 Sahaduta. All rights reserved.
  </footer>

  <script>
    function togglePassword() {
      const input = document.getElementById('passwordInput');
      const icon  = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }
  </script>
</body>
</html>
