@extends('layouts.dokter')

@section('title', 'Data Pasien')
@section('page-title', 'Data Pasien')
@section('page-subtitle', 'Daftar seluruh pasien terdaftar (hanya baca)')

@section('content')

{{-- Search Bar --}}
<div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <p class="text-sm text-gray-500">Total <span class="font-bold text-gray-800">{{ $pasiens->total() }}</span> pasien terdaftar</p>
  <form method="GET" action="{{ route('dokter.pasien') }}" class="flex items-center gap-2">
    <div class="relative">
      <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
      <input type="text" name="q" value="{{ request('q') }}"
        placeholder="Cari nama atau No. RM..."
        class="pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-64">
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">Cari</button>
    @if(request('q'))
      <a href="{{ route('dokter.pasien') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Reset</a>
    @endif
  </form>
</div>

{{-- Info badge read-only --}}
<div class="flex items-center gap-2 mb-4 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5">
  <i class="fas fa-eye"></i>
  <span>Mode <strong>hanya lihat</strong> — Dokter tidak dapat menambah atau mengubah data pasien.</span>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm" style="min-width:800px">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-100">
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No. RM</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pasien</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">NIK</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Umur</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Alamat</th>
          <th class="text-left px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Detail</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($pasiens as $pasien)
          <tr class="hover:bg-blue-50/40 transition-colors">
            <td class="px-5 py-3.5">
              <span class="text-blue-700 font-bold font-mono text-xs tracking-wide">{{ $pasien->no_rm ?? '—' }}</span>
            </td>
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                  {{ strtoupper(substr($pasien->nama ?? '?', 0, 1)) }}
                </div>
                <span class="font-semibold text-gray-800">{{ $pasien->nama ?? '—' }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5 text-gray-500 text-xs font-mono">{{ $pasien->nik ?? '—' }}</td>
            <td class="px-5 py-3.5">
              @if(($pasien->jenis_kelamin ?? '') === 'L')
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">♂ Laki-laki</span>
              @elseif(($pasien->jenis_kelamin ?? '') === 'P')
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700">♀ Perempuan</span>
              @else
                <span class="text-gray-400 text-xs">—</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-gray-700 text-sm">
              {{ $pasien->umur ?? '—' }} <span class="text-gray-400 text-xs">thn</span>
            </td>
            <td class="px-5 py-3.5 text-gray-500 text-xs max-w-xs truncate">{{ $pasien->alamat ?? '—' }}</td>
            <td class="px-5 py-3.5">
              @php
                $tglFmt    = $pasien->tgl_lahir ? \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d M Y') : '-';
                $agamaTxt  = $pasien->agama?->agama ?? '-';
                $pdkTxt    = $pasien->pendidikan?->pendidikan ?? '-';
                $pkjTxt    = $pasien->pekerjaan?->pekerjaan ?? '-';
                $golTxt    = $pasien->golongan_darah ?? '-';
                $nikTxt    = $pasien->nik ?? '-';
                $umurTxt   = $pasien->umur ?? '-';
              @endphp
              <button type="button"
                data-nama="{{ addslashes($pasien->nama) }}"
                data-nrm="{{ $pasien->no_rm }}"
                data-nik="{{ $nikTxt }}"
                data-jk="{{ $pasien->jenis_kelamin }}"
                data-umur="{{ $umurTxt }}"
                data-tgl="{{ $tglFmt }}"
                data-gol="{{ $golTxt }}"
                data-alamat="{{ addslashes($pasien->alamat ?? '-') }}"
                data-desa="{{ addslashes($pasien->desa ?? '-') }}"
                data-kota="{{ addslashes($pasien->kota ?? '-') }}"
                data-agama="{{ $agamaTxt }}"
                data-pdk="{{ $pdkTxt }}"
                data-pkj="{{ $pkjTxt }}"
                onclick="openDetailFromData(this)"
                class="px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition flex items-center gap-1.5">
                <i class="fas fa-eye text-xs"></i> Lihat
              </button>
            </td>
          </tr>


        @empty
          <tr>
            <td colspan="7" class="text-center py-16 text-gray-400">
              <i class="fas fa-inbox text-4xl mb-3 block opacity-25"></i>
              <p class="font-semibold text-sm">Tidak ada data pasien ditemukan</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($pasiens->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
      {{ $pasiens->appends(request()->query())->links() }}
    </div>
  @endif
</div>

{{-- Modal Detail Pasien --}}
<div id="detailModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden items-center justify-center">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden" style="animation: modalIn .22s ease">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-slate-800">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
          <i class="fas fa-user-circle text-white"></i>
        </div>
        <div>
          <h3 class="font-bold text-white text-base" id="detailNama">—</h3>
          <p class="text-white/60 text-xs" id="detailNoRm">—</p>
        </div>
      </div>
      <button onclick="closeDetailModal()" class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center text-white transition">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
    <div class="p-6 space-y-4">
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">NIK</p><p class="font-medium text-gray-800" id="detailNik">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Jenis Kelamin</p><p class="font-medium text-gray-800" id="detailJk">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Tanggal Lahir</p><p class="font-medium text-gray-800" id="detailTglLahir">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Umur</p><p class="font-medium text-gray-800" id="detailUmur">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Gol. Darah</p><p class="font-medium text-gray-800" id="detailGolDarah">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Agama</p><p class="font-medium text-gray-800" id="detailAgama">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Pendidikan</p><p class="font-medium text-gray-800" id="detailPendidikan">—</p></div>
        <div><p class="text-xs text-gray-400 font-semibold uppercase mb-0.5">Pekerjaan</p><p class="font-medium text-gray-800" id="detailPekerjaan">—</p></div>
      </div>
      <div class="border-t pt-4">
        <p class="text-xs text-gray-400 font-semibold uppercase mb-1">Alamat Lengkap</p>
        <p class="text-sm text-gray-700" id="detailAlamat">—</p>
      </div>
    </div>
    <div class="px-6 pb-5">
      <div class="flex items-center gap-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
        <i class="fas fa-lock text-amber-500"></i>
        <span>Data hanya dapat dilihat oleh dokter. Untuk perubahan, hubungi admin.</span>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes modalIn { from{opacity:0;transform:scale(.96)} to{opacity:1;transform:none} }
</style>

<script>
function openDetailFromData(btn) {
  var d = btn.dataset;
  var jkLabel = (d.jk === 'L') ? '♂ Laki-laki' : (d.jk === 'P') ? '♀ Perempuan' : d.jk;
  document.getElementById('detailNama').textContent = d.nama;
  document.getElementById('detailNoRm').textContent = 'No. RM: ' + d.nrm;
  document.getElementById('detailNik').textContent = d.nik;
  document.getElementById('detailJk').textContent = jkLabel;
  document.getElementById('detailUmur').textContent = d.umur + ' tahun';
  document.getElementById('detailTglLahir').textContent = d.tgl;
  document.getElementById('detailGolDarah').textContent = d.gol;
  document.getElementById('detailAgama').textContent = d.agama;
  document.getElementById('detailPendidikan').textContent = d.pdk;
  document.getElementById('detailPekerjaan').textContent = d.pkj;

  var alamat = d.alamat || '-';
  if (d.desa && d.desa !== '-') alamat += ', Desa ' + d.desa;
  if (d.kota && d.kota !== '-') alamat += ', ' + d.kota;
  document.getElementById('detailAlamat').textContent = alamat;

  var modal = document.getElementById('detailModal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}


function closeDetailModal() {
  var modal = document.getElementById('detailModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
  if (e.target === this) closeDetailModal();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeDetailModal();
});
</script>

@endsection
