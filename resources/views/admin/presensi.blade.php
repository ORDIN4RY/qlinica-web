@extends('layouts.app')

@section('title', 'Presensi Pegawai')
@section('page-title', 'Presensi Pegawai')
@section('page-subtitle', 'Pantau performa absensi dan persetujuan cuti/izin pegawai')

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Hadir</p>
            <p class="text-2xl font-bold text-gray-800">{{ $kpi['hadir'] }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600">
            <i class="fas fa-procedures text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Sakit</p>
            <p class="text-2xl font-bold text-gray-800">{{ $kpi['sakit'] }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
            <i class="fas fa-envelope-open-text text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Izin</p>
            <p class="text-2xl font-bold text-gray-800">{{ $kpi['izin'] }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
            <i class="fas fa-plane-departure text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Cuti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $kpi['cuti'] }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
            <i class="fas fa-times-circle text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Alpa</p>
            <p class="text-2xl font-bold text-gray-800">{{ $kpi['alpa'] }}</p>
        </div>
    </div>
</div>

{{-- Top Actions --}}
<div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('admin.presensi') }}" class="flex items-center gap-2">
        <select name="bulan" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            @for($i=1; $i<=12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                </option>
            @endfor
        </select>
        <select name="tahun" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
            @for($i=date('Y')-2; $i<=date('Y'); $i++)
                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl text-sm font-semibold transition">
            Filter
        </button>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-sm">
                    <th class="px-6 py-4 font-bold text-gray-700">Tanggal</th>
                    <th class="px-6 py-4 font-bold text-gray-700">Pegawai</th>
                    <th class="px-6 py-4 font-bold text-gray-700">Jabatan</th>
                    <th class="px-6 py-4 font-bold text-gray-700">Status Absen</th>
                    <th class="px-6 py-4 font-bold text-gray-700">Keterangan</th>
                    <th class="px-6 py-4 font-bold text-gray-700">Persetujuan</th>
                    <th class="px-6 py-4 font-bold text-gray-700 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($presensis as $p)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ \Carbon\Carbon::parse($p->tanggal)->isoFormat('D MMMM YYYY') }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $p->pegawai->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $p->pegawai->nik }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $p->pegawai->jabatan }}</td>
                    <td class="px-6 py-4">
                        @if($p->status == 'Hadir')
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">Hadir</span>
                        @elseif($p->status == 'Sakit')
                            <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-xs font-bold">Sakit</span>
                        @elseif($p->status == 'Izin')
                            <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-bold">Izin</span>
                        @elseif($p->status == 'Cuti')
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold">Cuti</span>
                        @else
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold">Alpa</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate" title="{{ $p->keterangan }}">
                        {{ $p->keterangan ?: '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($p->approval_status == 'Approved')
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold"><i class="fas fa-check mr-1"></i> Disetujui</span>
                        @elseif($p->approval_status == 'Rejected')
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold"><i class="fas fa-times mr-1"></i> Ditolak</span>
                        @else
                            <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-xs font-bold"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        @if($p->approval_status == 'Pending' && in_array($p->status, ['Izin', 'Cuti', 'Sakit']))
                            <form action="{{ route('admin.presensi.update', $p->id) }}" method="POST" class="inline-block">
                                @csrf @method('PUT')
                                <input type="hidden" name="approval_status" value="Approved">
                                <button type="submit" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition shadow-sm mx-0.5" title="Setujui">
                                    <i class="fas fa-check"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('admin.presensi.update', $p->id) }}" method="POST" class="inline-block">
                                @csrf @method('PUT')
                                <input type="hidden" name="approval_status" value="Rejected">
                                <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition shadow-sm mx-0.5" title="Tolak">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.presensi.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 mx-1 ml-2 transition" title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-folder-open text-4xl mb-3 opacity-50 block"></i>
                        <p>Belum ada data presensi bulan ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
