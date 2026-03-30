@extends('layouts.app')

@section('title', 'Data Pasien')
@section('page-title', 'Data Pasien')
@section('page-subtitle', 'Kelola seluruh data pasien klinik')

@section('content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  {{-- Search --}}
  <form method="GET" action="{{ route('patients.index') }}" class="flex gap-2 w-full sm:w-auto">
    <div class="relative flex-1 sm:w-72">
      <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
      <input type="text" name="search" value="{{ $search }}"
        placeholder="Cari nama, NIK, atau penyakit..."
        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-900/10">
    </div>
    <button type="submit" class="px-4 py-2.5 bg-blue-900 text-white rounded-xl text-sm font-medium hover:bg-blue-800 transition">Cari</button>
    @if($search)
      <a href="{{ route('patients.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition">Reset</a>
    @endif
  </form>

  <a href="{{ route('patients.create') }}"
     class="flex items-center gap-2 px-5 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl text-sm font-semibold shadow transition whitespace-nowrap">
    <i class="fas fa-plus"></i> Tambah Pasien
  </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
        <tr>
          <th class="px-5 py-3 text-left">#</th>
          <th class="px-5 py-3 text-left">Nama</th>
          <th class="px-5 py-3 text-left">NIK</th>
          <th class="px-5 py-3 text-left">Usia</th>
          <th class="px-5 py-3 text-left">Gender</th>
          <th class="px-5 py-3 text-left">Penyakit</th>
          <th class="px-5 py-3 text-left">Kunjungan</th>
          <th class="px-5 py-3 text-left">No. Telp</th>
          <th class="px-5 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($patients as $p)
        <tr class="hover:bg-blue-50/40 transition">
          <td class="px-5 py-4 text-gray-400">{{ $patients->firstItem() + $loop->index }}</td>
          <td class="px-5 py-4 font-semibold text-gray-800">{{ $p->name }}</td>
          <td class="px-5 py-4 font-mono text-gray-500 text-xs">{{ $p->nik }}</td>
          <td class="px-5 py-4">{{ $p->age }} thn</td>
          <td class="px-5 py-4">
            @if($p->gender === 'L')
              <span class="px-2.5 py-1 bg-sky-100 text-sky-700 rounded-full text-xs font-medium">Laki-laki</span>
            @else
              <span class="px-2.5 py-1 bg-pink-100 text-pink-700 rounded-full text-xs font-medium">Perempuan</span>
            @endif
          </td>
          <td class="px-5 py-4">
            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">{{ $p->disease }}</span>
          </td>
          <td class="px-5 py-4 text-gray-500">{{ $p->visit_date->format('d M Y') }}</td>
          <td class="px-5 py-4 text-gray-500">{{ $p->phone }}</td>
          <td class="px-5 py-4">
            <div class="flex items-center justify-center gap-2">
              <a href="{{ route('patients.edit', $p) }}"
                 class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition" title="Edit">
                <i class="fas fa-pen text-xs"></i>
              </a>
              <form method="POST" action="{{ route('patients.destroy', $p) }}"
                    onsubmit="return confirm('Hapus pasien {{ $p->name }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                  class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Hapus">
                  <i class="fas fa-trash text-xs"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="px-6 py-16 text-center text-gray-400">
            <i class="fas fa-inbox text-3xl mb-3 block"></i>
            Belum ada data pasien.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($patients->hasPages())
  <div class="px-6 py-4 border-t border-gray-100">
    {{ $patients->links() }}
  </div>
  @endif
</div>

@endsection
