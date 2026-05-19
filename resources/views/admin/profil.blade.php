@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- Kartu Foto Profil --}}
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
    <div class="w-28 h-28 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center text-blue-900 text-4xl font-bold mb-4">
      @if($user->foto)
        <img src="{{ asset('storage/' . $user->foto) }}" class="w-full h-full object-cover" id="preview-foto">
      @else
        <span id="avatar-initials">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        <img src="" class="w-full h-full object-cover hidden" id="preview-foto">
      @endif
    </div>
    <h2 class="font-bold text-lg text-gray-800">{{ $user->name }}</h2>
    <p class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full inline-block mt-1 mb-2">
      {{ $user->pegawai->jabatan->nama_jabatan ?? 'Pegawai' }}
    </p>
    <p class="text-sm text-gray-500 mb-1">{{ $user->email }}</p>
    <p class="text-xs text-gray-400 mb-6 font-mono">{{ $user->pegawai->nik ?? '-' }}</p>

    {{-- Form Upload Foto --}}
    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="w-full">
      @csrf @method('PUT')
      <input type="hidden" name="name"  value="{{ $user->name }}">
      <input type="hidden" name="phone" value="{{ $user->phone }}">
      <label class="cursor-pointer w-full flex flex-col items-center gap-2 border-2 border-dashed border-blue-200 rounded-xl p-4 hover:border-blue-400 transition">
        <i class="fas fa-camera text-blue-400 text-xl"></i>
        <span class="text-sm text-blue-600 font-medium">Ganti Foto</span>
        <span class="text-xs text-gray-400">JPG, PNG, WEBP maks. 2MB</span>
        <input type="file" name="foto" class="hidden" accept="image/*" onchange="previewFoto(this)">
      </label>
      <button type="submit" id="btn-upload-foto"
        class="hidden mt-3 w-full bg-blue-900 text-white py-2 rounded-xl text-sm font-medium hover:bg-blue-800 transition">
        <i class="fas fa-upload mr-1"></i> Upload Foto
      </button>
    </form>

    {{-- Hapus Foto --}}
    @if($user->foto)
    <form method="POST" action="{{ route('admin.profil.foto.delete') }}" class="w-full mt-2"
          onsubmit="return confirm('Hapus foto profil?')">
      @csrf @method('DELETE')
      <button type="submit" class="w-full py-2 text-sm text-red-500 hover:text-red-700 font-medium transition">
        <i class="fas fa-trash mr-1"></i> Hapus Foto
      </button>
    </form>
    @endif
  </div>

  {{-- Kolom Kanan --}}
  <div class="lg:col-span-2 flex flex-col gap-6">

    {{-- Form Info Pribadi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
        <i class="fas fa-user text-blue-900"></i> Informasi Pribadi
      </h3>
      <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm outline-none transition">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">NIK</label>
            <input type="text" value="{{ $user->pegawai->nik ?? '-' }}" disabled
              class="w-full px-4 py-2.5 border border-gray-100 rounded-xl bg-gray-50 text-sm text-gray-400 cursor-not-allowed font-mono">
            <p class="text-[10px] text-gray-400 mt-1">NIK tidak dapat diubah</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan</label>
            <input type="text" value="{{ $user->pegawai->jabatan->nama_jabatan ?? 'Pegawai' }}" disabled
              class="w-full px-4 py-2.5 border border-gray-100 rounded-xl bg-gray-50 text-sm text-gray-400 cursor-not-allowed">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <input type="email" value="{{ $user->email }}" disabled
              class="w-full px-4 py-2.5 border border-gray-100 rounded-xl bg-gray-50 text-sm text-gray-400 cursor-not-allowed">
            <p class="text-[10px] text-gray-400 mt-1">Email tidak dapat diubah</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
              placeholder="08xxxxxxxxxx"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm outline-none transition">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 mb-1">Alamat</label>
            <textarea name="alamat" rows="2"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm outline-none transition">{{ old('alamat', $user->pegawai->alamat ?? '') }}</textarea>
          </div>
        </div>
        <div class="mt-5 flex justify-end">
          <button type="submit"
            class="px-6 py-2.5 bg-blue-900 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition flex items-center gap-2">
            <i class="fas fa-save"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>

    {{-- Form Ganti Password --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
      <h3 class="font-bold text-gray-800 text-base mb-5 flex items-center gap-2">
        <i class="fas fa-lock text-blue-900"></i> Ganti Password
      </h3>
      <form method="POST" action="{{ route('admin.profil.password') }}">
        @csrf @method('PUT')
        <div class="flex flex-col gap-4">

          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Password Lama</label>
            <div class="relative">
              <input type="password" name="password_lama" id="pw-lama" required
                class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm outline-none transition @error('password_lama') border-red-400 @enderror">
              <button type="button" onclick="togglePw('pw-lama','eye-lama')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye" id="eye-lama"></i>
              </button>
            </div>
            @error('password_lama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Password Baru</label>
            <div class="relative">
              <input type="password" name="password" id="pw-baru" required
                class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm outline-none transition @error('password') border-red-400 @enderror">
              <button type="button" onclick="togglePw('pw-baru','eye-baru')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye" id="eye-baru"></i>
              </button>
            </div>
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Konfirmasi Password Baru</label>
            <div class="relative">
              <input type="password" name="password_confirmation" id="pw-konfirm" required
                class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm outline-none transition">
              <button type="button" onclick="togglePw('pw-konfirm','eye-konfirm')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i class="fas fa-eye" id="eye-konfirm"></i>
              </button>
            </div>
          </div>

        </div>
        <div class="mt-5 flex justify-end">
          <button type="submit"
            class="px-6 py-2.5 bg-blue-900 text-white rounded-xl text-sm font-semibold hover:bg-blue-800 transition flex items-center gap-2">
            <i class="fas fa-key"></i> Ubah Password
          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
function togglePw(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon  = document.getElementById(iconId);
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}

function previewFoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const img     = document.getElementById('preview-foto');
      const initial = document.getElementById('avatar-initials');
      img.src = e.target.result;
      img.classList.remove('hidden');
      if (initial) initial.classList.add('hidden');
      document.getElementById('btn-upload-foto').classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endpush
