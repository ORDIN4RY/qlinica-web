@extends('layouts.app')

@section('title', 'Manajemen Presensi & Shift')
@section('page-title', 'Manajemen Presensi & Shift')
@section('page-subtitle', 'Pantau absensi, persetujuan cuti, dan atur jadwal shift libur pegawai')

@push('styles')
<style>
    .tab-btn { transition: all 0.2s; border-bottom: 2px solid transparent; }
    .tab-btn.active { border-bottom-color: #1e3a8a; color: #1e3a8a; font-weight: 700; background: #f8fafc; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    .status-badge { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 12px; border-radius: 99px; }
    .badge-hadir { background: #f0fdf4; color: #166534; }
    .badge-sakit { background: #fffbeb; color: #92400e; }
    .badge-izin { background: #fdf4ff; color: #86198f; }
    .badge-cuti { background: #f0fdfa; color: #115e59; }
    .badge-alpha { background: #fef2f2; color: #991b1b; }
    .badge-libur { background: #f1f5f9; color: #475569; }
    
    .table-auto-hover tr:hover { background-color: #f8fafc; }
</style>
@endpush

@section('content')

{{-- KPI Summary --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    @php
        $stats = [
            ['label' => 'Hadir', 'val' => $kpi['hadir'], 'icon' => 'fa-check-circle', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
            ['label' => 'Sakit', 'val' => $kpi['sakit'], 'icon' => 'fa-heart-pulse', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
            ['label' => 'Izin',  'val' => $kpi['izin'],  'icon' => 'fa-envelope-open-text', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
            ['label' => 'Cuti',  'val' => $kpi['cuti'],  'icon' => 'fa-plane-departure', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
            ['label' => 'Alpha', 'val' => $kpi['alpa'], 'icon' => 'fa-times-circle', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex items-center gap-4 transition hover:shadow-md">
        <div class="w-12 h-12 rounded-xl {{ $s['bg'] }} flex items-center justify-center {{ $s['text'] }}">
            <i class="fas {{ $s['icon'] }} text-xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ $s['label'] }}</p>
            <p class="text-2xl font-black text-gray-800">{{ $s['val'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabs Navigation --}}
<div class="bg-white rounded-t-2xl border-x border-t border-gray-100 shadow-sm overflow-hidden">
    <div class="flex border-b border-gray-100">
        <button onclick="switchTab('log')" id="tab-log" class="tab-btn active px-6 py-4 text-sm font-medium text-gray-500 hover:text-blue-900">
            <i class="fas fa-list-ul mr-2"></i> Log Presensi
        </button>
        <button onclick="switchTab('persetujuan')" id="tab-persetujuan" class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-blue-900 flex items-center">
            <i class="fas fa-clipboard-check mr-2"></i> Persetujuan 
            @if($pengajuans->count() > 0)
                <span class="ml-2 px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $pengajuans->count() }}</span>
            @endif
        </button>
        <button onclick="switchTab('shift')" id="tab-shift" class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-blue-900">
            <i class="fas fa-calendar-day mr-2"></i> Pengaturan Shift
        </button>
    </div>
</div>

<div class="bg-white rounded-b-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
    
    {{-- TAB 1: LOG PRESENSI --}}
    <div id="content-log" class="tab-content active">
        <div class="p-6 border-b border-gray-50 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-gray-800">Riwayat Kehadiran Karyawan</h3>
                @if(request('status'))
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-md uppercase">Filter: {{ request('status') }}</span>
                @endif
            </div>
            <form method="GET" action="{{ route('admin.presensi') }}" class="flex items-center flex-wrap gap-2">
                <input type="hidden" name="tab" value="log">
                
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    @foreach(['Hadir', 'Sakit', 'Izin', 'Cuti', 'Alpha', 'Libur'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>

                <select name="bulan" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500">
                    @for($i=date('Y')-1; $i<=date('Y')+1; $i++)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request('status') || request('bulan') != date('m') || request('tahun') != date('Y'))
                    <a href="{{ route('admin.presensi', ['tab' => 'log']) }}" class="p-2 text-gray-400 hover:text-red-500" title="Reset Filter">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left table-auto-hover">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Pegawai</th>
                        <th class="px-6 py-3 text-center">Masuk / Pulang</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50">
                    @forelse($presensis as $p)
                    @php
                        $accentColor = match($p->status) {
                            'Hadir' => 'border-l-4 border-green-500',
                            'Sakit' => 'border-l-4 border-amber-500',
                            'Izin' => 'border-l-4 border-purple-500',
                            'Cuti' => 'border-l-4 border-emerald-500',
                            'Alpha' => 'border-l-4 border-red-500',
                            'Libur' => 'border-l-4 border-gray-300',
                            default => '',
                        };
                    @endphp
                    <tr class="{{ $accentColor }}">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</div>
                            <div class="text-[10px] text-gray-400 capitalize">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-bold">
                                    {{ strtoupper(substr($p->pegawai->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 leading-tight">{{ $p->pegawai->nama }}</div>
                                    <div class="text-[10px] text-blue-600 font-bold uppercase">{{ $p->pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($p->jam_masuk)
                                <div class="font-mono font-bold text-blue-700 text-sm">{{ substr($p->jam_masuk, 0, 5) }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $p->jam_keluar ? substr($p->jam_keluar, 0, 5) : '--:--' }}</div>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClass = match($p->status) {
                                    'Hadir' => 'badge-hadir',
                                    'Sakit' => 'badge-sakit',
                                    'Izin' => 'badge-izin',
                                    'Cuti' => 'badge-cuti',
                                    'Alpha' => 'badge-alpha',
                                    'Libur' => 'badge-libur',
                                    default => 'badge-libur',
                                };
                            @endphp
                            <div class="flex items-center gap-1">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $p->status }}
                                </span>
                                @if($p->batch_id)
                                    <i class="fas fa-layer-group text-[10px] text-gray-300" title="Bagian dari pengajuan grup"></i>
                                @endif
                            </div>
                            @if($p->telat_menit > 0)
                                <div class="text-[10px] text-orange-600 font-bold mt-1"><i class="fas fa-clock mr-1"></i>Telat {{ $p->telat_menit }}m</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs italic">
                            <div class="truncate max-w-[150px]" title="{{ $p->keterangan }}">
                                {{ $p->keterangan ?: '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.presensi.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-300 hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-10 text-center text-gray-400">Tidak ada data untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 2: PERSETUJUAN --}}
    <div id="content-persetujuan" class="tab-content">
        <div class="p-6 border-b border-gray-50">
            <h3 class="font-bold text-gray-800">Menunggu Persetujuan (Cuti/Izin/Sakit)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Rentang Tanggal</th>
                        <th class="px-6 py-3 text-center">Durasi</th>
                        <th class="px-6 py-3">Pegawai</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Keterangan / Dokumen</th>
                        <th class="px-6 py-3 text-right">Keputusan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50">
                    @forelse($pengajuans as $p)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M') }} 
                                - 
                                {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y') }}
                            </div>
                            <div class="text-[10px] text-gray-400">Diajukan pada {{ \Carbon\Carbon::parse($p->tanggal_mulai)->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-gray-100 rounded-lg font-bold text-gray-700 text-xs">
                                {{ $p->durasi }} Hari
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $p->pegawai->nama }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $p->pegawai->nik }}</div>
                        </td>
                        <td class="px-6 py-4">
                             <span class="status-badge {{ match($p->status){'Sakit'=>'badge-sakit','Izin'=>'badge-izin','Cuti'=>'badge-cuti',default=>''} }}">
                                 {{ $p->status }}
                             </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-600 text-xs italic">{{ $p->keterangan }}</div>
                            @if($p->surat_dokter)
                                <a href="{{ asset('storage/'.$p->surat_dokter) }}" target="_blank" class="mt-2 inline-flex items-center text-blue-600 hover:underline font-bold text-[10px]">
                                    @if($p->status === 'Sakit')
                                        <i class="fas fa-file-medical mr-1"></i> Lihat Surat Dokter
                                    @else
                                        <i class="fas fa-paperclip mr-1"></i> Lihat Lampiran Izin
                                    @endif
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex justify-end gap-2">
                                <form action="{{ route('admin.presensi.update', $p->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="approval_status" value="Approved">
                                    <button class="px-4 py-1.5 bg-green-600 text-white text-[11px] font-bold rounded-lg shadow-sm hover:bg-green-700">Setujui</button>
                                </form>
                                <form action="{{ route('admin.presensi.update', $p->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="approval_status" value="Rejected">
                                    <button class="px-4 py-1.5 bg-white border border-red-200 text-red-600 text-[11px] font-bold rounded-lg hover:bg-red-50">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-10 text-center text-gray-400">Semua pengajuan sudah diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 3: PENGATURAN SHIFT (ROSTER SIMPLE) --}}
    <div id="content-shift" class="tab-content">
        {{-- ROSTER CONTROLS --}}
        <div class="p-6 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4 bg-white sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-800">Penjadwalan Roster</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5 font-bold uppercase tracking-widest">
                        Periode: {{ date('F Y', mktime(0,0,0,$bulan,10,$tahun)) }}
                    </p>
                </div>
                
                {{-- SIMPLE MONTH NAV --}}
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 ml-4">
                    @php
                        $prevMonth = $bulan == 1 ? 12 : $bulan - 1;
                        $prevYear  = $bulan == 1 ? $tahun - 1 : $tahun;
                        $nextMonth = $bulan == 12 ? 1 : $bulan + 1;
                        $nextYear  = $bulan == 12 ? $tahun + 1 : $tahun;
                    @endphp
                    <a href="{{ route('admin.presensi', ['tab' => 'shift', 'bulan' => $prevMonth, 'tahun' => $prevYear]) }}" 
                       class="w-8 h-8 flex items-center justify-center rounded hover:bg-white hover:shadow-sm text-gray-400 hover:text-blue-600 transition-all">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </a>
                    <span class="px-4 text-[11px] font-bold text-gray-600">{{ date('M Y', mktime(0,0,0,$bulan,10,$tahun)) }}</span>
                    <a href="{{ route('admin.presensi', ['tab' => 'shift', 'bulan' => $nextMonth, 'tahun' => $nextYear]) }}" 
                       class="w-8 h-8 flex items-center justify-center rounded hover:bg-white hover:shadow-sm text-gray-400 hover:text-blue-600 transition-all">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
                    <input type="text" id="searchPegawai" onkeyup="filterRoster()" placeholder="Cari nama pegawai..." 
                        class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-[11px] focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none w-56 transition-all">
                </div>
                <div class="flex gap-2 ml-2">
                    <button onclick="openBulkModal()" class="px-3 py-2 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200 hover:bg-blue-100 transition shadow-sm">
                        <i class="fas fa-users mr-1"></i> Atur Massal
                    </button>
                    <button onclick="openPatternModal()" class="px-3 py-2 bg-purple-50 text-purple-700 text-[10px] font-bold rounded-lg border border-purple-200 hover:bg-purple-100 transition shadow-sm">
                        <i class="fas fa-sync-alt mr-1"></i> Pola Berulang
                    </button>
                    <form id="copyShiftForm" action="{{ route('admin.presensi.shift.copy') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <button type="button" onclick="openCopyConfirmModal()" class="px-3 py-2 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-200 hover:bg-emerald-100 transition shadow-sm">
                            <i class="fas fa-copy mr-1"></i> Salin Bulan Lalu
                        </button>
                    </form>
                </div>
                <!-- <div class="flex gap-1.5 ml-2">
                    @foreach($shifts as $sh)
                        <span class="px-2 py-1 rounded text-[9px] font-bold border uppercase
                            @if($sh->nama_shift == 'Shift Pagi') bg-green-50 text-green-700 border-green-200
                            @elseif($sh->nama_shift == 'Shift Sore') bg-blue-50 text-blue-700 border-blue-200
                            @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                            {{ substr($sh->nama_shift, 6, 1) }}
                        </span>
                    @endforeach
                </div> -->
            </div>
        </div>
        
        <div class="w-full overflow-auto max-h-[600px]">
            <table class="w-full min-w-[1500px] text-left border-collapse">
                <thead class="sticky top-0 z-40">
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 sticky left-0 bg-gray-50 z-50 border-r border-gray-100 shadow-[2px_0_5px_rgba(0,0,0,0.03)]" style="width: 180px;">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pegawai</span>
                        </th>
                        @php
                            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
                            $todayDate = date('Y-m-d');
                        @endphp
                        @for($d=1; $d<=$daysInMonth; $d++)
                            @php 
                                $dateStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                $isWeekend = in_array(date('N', strtotime($dateStr)), [6, 7]);
                                $isToday = $dateStr === $todayDate;
                            @endphp
                            <th class="p-0 border-r border-gray-100 {{ $isWeekend ? 'bg-red-50/30' : '' }} {{ $isToday ? 'bg-blue-50' : '' }}" style="min-width: 40px;">
                                <div class="py-2 text-center {{ $isToday ? 'text-blue-600' : ($isWeekend ? 'text-red-400' : 'text-gray-500') }}">
                                    <div class="text-[11px] font-bold">{{ $d }}</div>
                                    <div class="text-[8px] uppercase opacity-50">{{ date('D', strtotime($dateStr)) }}</div>
                                </div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="roster-body">
                    @foreach($pegawais as $peg)
                    <tr class="roster-row hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 sticky left-0 bg-white z-20 border-r border-gray-100 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                            <div class="font-bold text-gray-700 text-xs truncate pegawai-name">{{ $peg->nama }}</div>
                            <div class="text-[9px] text-gray-400 uppercase">{{ $peg->jabatan->nama_jabatan ?? '-' }}</div>
                        </td>
                        @for($d=1; $d<=$daysInMonth; $d++)
                            @php 
                                $cellDate = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                                // PERBAIKAN: Pastikan akses data jadwal sangat spesifik per pegawai dan tanggal
                                $assignedShift = null;
                                if (isset($jadwalShifts[$peg->id]) && isset($jadwalShifts[$peg->id][$cellDate])) {
                                    $assignedShift = $jadwalShifts[$peg->id][$cellDate];
                                }
                            @endphp
                            <td class="p-0.5 border-r border-gray-50 text-center">
                                <button type="button" 
                                    onclick="openShiftModal('{{ $peg->id }}', '{{ $peg->nama }}', '{{ $cellDate }}', '{{ $assignedShift?->shift_id }}')"
                                    class="w-full h-8 rounded transition-all flex items-center justify-center border
                                    @if(!$assignedShift) 
                                        border-transparent hover:border-gray-200 hover:bg-gray-100
                                    @else
                                        @if($assignedShift->shift->nama_shift == 'Shift Pagi') bg-green-500 border-green-600 text-white
                                        @elseif($assignedShift->shift->nama_shift == 'Shift Sore') bg-blue-500 border-blue-600 text-white
                                        @else bg-gray-700 border-gray-800 text-white @endif
                                    @endif">
                                    
                                    @if($assignedShift)
                                        <span class="font-black text-[10px]">{{ substr($assignedShift->shift->nama_shift, 6, 1) }}</span>
                                    @else
                                        <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                                    @endif
                                </button>
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center gap-4 text-[9px] text-gray-400 font-bold uppercase tracking-widest">
            <span class="mr-2">Legenda:</span>
            @foreach($shifts as $sh)
                <div class="flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-sm
                        @if($sh->nama_shift == 'Shift Pagi') bg-green-500
                        @elseif($sh->nama_shift == 'Shift Sore') bg-blue-500
                        @else bg-gray-700 @endif"></span>
                    {{ $sh->nama_shift }} ({{ substr($sh->jam_masuk,0,5) }}-{{ substr($sh->jam_pulang,0,5) }})
                </div>
            @endforeach
            <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-gray-200 rounded-sm"></span> Off / Libur</div>
        </div>
    </div>

    {{-- MODAL ATUR SHIFT --}}
    <div id="shiftModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden scale-95 transition-transform duration-300" id="modalContent">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-blue-900 text-white">
                <div>
                    <h4 class="font-black text-lg">Atur Jadwal Kerja</h4>
                    <p class="text-xs text-blue-200" id="modalSubTitle"></p>
                </div>
                <button onclick="closeShiftModal()" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form id="shiftForm" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="tanggal" id="modalInputTanggal">
                
                <div class="grid grid-cols-1 gap-3">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pilih Shift Kerja</label>
                    
                    {{-- Opsi Libur --}}
                    <label class="relative flex items-center p-4 border border-red-100 rounded-xl cursor-pointer hover:bg-red-50 transition group bg-red-50/30">
                        <input type="radio" name="shift_id" value="" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                        <div class="ml-4">
                            <div class="font-bold text-red-800">Hari Libur / OFF</div>
                            <div class="text-[10px] text-red-500">Hapus jadwal kerja untuk hari ini</div>
                        </div>
                    </label>

                    <div class="h-px bg-gray-100 my-1"></div>

                    @foreach($shifts as $sh)
                    <label class="relative flex items-center p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition group">
                        <input type="radio" name="shift_id" value="{{ $sh->id }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <div class="ml-4">
                            <div class="font-bold text-gray-800">{{ $sh->nama_shift }}</div>
                            <div class="text-[10px] text-gray-500">{{ substr($sh->jam_masuk,0,5) }} - {{ substr($sh->jam_pulang,0,5) }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeShiftModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-2 px-6 py-2 bg-blue-900 text-white rounded-xl font-bold text-sm hover:bg-blue-800 shadow-lg shadow-blue-900/20">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL BULK SHIFT --}}
    <div id="bulkModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden scale-95 transition-transform duration-300" id="bulkModalContent">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-blue-900 text-white">
                <div>
                    <h4 class="font-black text-lg">Atur Jadwal Massal</h4>
                    <p class="text-xs text-blue-200">Terapkan shift untuk banyak pegawai sekaligus</p>
                </div>
                <button onclick="closeBulkModal()" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.presensi.shift.bulk') }}" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                @csrf
                
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Pilih Pegawai</label>
                    <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50 space-y-2">
                        @foreach($pegawais as $peg)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="pegawai_ids[]" value="{{ $peg->id }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <div>
                                <div class="font-bold text-gray-800 text-sm">{{ $peg->nama }}</div>
                                <div class="text-[10px] text-gray-500">{{ $peg->jabatan->nama_jabatan ?? '-' }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Dari Tanggal</label>
                        <input type="date" name="tanggal_mulai" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Pilih Shift</label>
                    <select name="shift_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Hari Libur / OFF --</option>
                        @foreach($shifts as $sh)
                            <option value="{{ $sh->id }}">{{ $sh->nama_shift }} ({{ substr($sh->jam_masuk,0,5) }}-{{ substr($sh->jam_pulang,0,5) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <input type="checkbox" name="skip_minggu" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-bold text-gray-700">Abaikan Hari Minggu (Libur)</span>
                    </label>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeBulkModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-2 px-6 py-2 bg-blue-900 text-white rounded-xl font-bold text-sm hover:bg-blue-800 shadow-lg shadow-blue-900/20">Terapkan Massal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PATTERN SHIFT --}}
    <div id="patternModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden scale-95 transition-transform duration-300" id="patternModalContent">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-purple-900 text-white">
                <div>
                    <h4 class="font-black text-lg">Pola Berulang</h4>
                    <p class="text-xs text-purple-200">Generate jadwal berdasarkan pola shift</p>
                </div>
                <button onclick="closePatternModal()" class="text-white/50 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.presensi.shift.pattern') }}" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
                @csrf
                
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Pilih Pegawai</label>
                    <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50 space-y-2">
                        @foreach($pegawais as $peg)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="pegawai_ids[]" value="{{ $peg->id }}" class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                            <div>
                                <div class="font-bold text-gray-800 text-sm">{{ $peg->nama }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Periode Mulai</label>
                        <input type="date" name="tanggal_mulai" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-2">Periode Selesai</label>
                        <input type="date" name="tanggal_selesai" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Urutan Pola Shift</label>
                        <button type="button" onclick="addPatternRow()" class="text-xs font-bold text-purple-600 hover:text-purple-800"><i class="fas fa-plus"></i> Tambah Hari</button>
                    </div>
                    <div id="patternContainer" class="space-y-2">
                        <!-- Pattern rows will be generated by JS -->
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <input type="checkbox" name="skip_minggu" value="1" class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                        <span class="text-sm font-bold text-gray-700">Abaikan Hari Minggu (Libur)</span>
                    </label>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closePatternModal()" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-2 px-6 py-2 bg-purple-900 text-white rounded-xl font-bold text-sm hover:bg-purple-800 shadow-lg shadow-purple-900/20">Generate Pola</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL KONFIRMASI SALIN BULAN LALU --}}
    <div id="copyConfirmModal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl scale-95 transition-transform duration-300" id="copyConfirmModalContent">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-copy text-lg"></i>
            </div>
            <h4 class="font-black text-lg text-gray-800 mb-2">Salin Jadwal Bulan Lalu?</h4>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                Apakah Anda yakin ingin menyalin semua jadwal dari bulan sebelumnya ke bulan ini? Jadwal hari yang sama pada bulan ini akan ditimpa.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeCopyConfirmModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-50 transition">Batal</button>
                <button type="button" onclick="submitCopyForm()" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/20 transition">Ya, Salin</button>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        // Deactivate all buttons
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        // Show selected
        document.getElementById('content-' + tab).classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
        
        // Save to URL to persist on refresh
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    }

    function openShiftModal(pegawaiId, nama, tanggal, currentShiftId) {
        const modal = document.getElementById('shiftModal');
        const content = document.getElementById('modalContent');
        const title = document.getElementById('modalSubTitle');
        const inputTanggal = document.getElementById('modalInputTanggal');
        const form = document.getElementById('shiftForm');

        title.innerText = `${nama} • ${tanggal}`;
        inputTanggal.value = tanggal;
        form.action = `/admin/presensi/${pegawaiId}/shift`;

        // Reset and Set radio
        document.querySelectorAll('input[name="shift_id"]').forEach(radio => {
            radio.checked = (radio.value == currentShiftId);
        });

        modal.classList.remove('hidden');
        setTimeout(() => content.classList.remove('scale-95'), 10);
    }

    function closeShiftModal() {
        const modal = document.getElementById('shiftModal');
        const content = document.getElementById('modalContent');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function filterRoster() {
        const query = document.getElementById('searchPegawai').value.toLowerCase();
        const rows = document.querySelectorAll('.roster-row');
        
        rows.forEach(row => {
            const name = row.querySelector('.pegawai-name').innerText.toLowerCase();
            if (name.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    let patternDayCount = 0;
    const shiftsOptions = `
        <option value="0">Libur / OFF</option>
        @foreach($shifts as $sh)
            <option value="{{ $sh->id }}">{{ $sh->nama_shift }}</option>
        @endforeach
    `;

    function addPatternRow() {
        patternDayCount++;
        const container = document.getElementById('patternContainer');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 bg-gray-50 p-2 border border-gray-200 rounded-lg';
        row.innerHTML = `
            <div class="w-16 text-center text-xs font-bold text-gray-500 uppercase">Hari ${patternDayCount}</div>
            <select name="pola[]" required class="flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                ${shiftsOptions}
            </select>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 px-2"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(row);
    }

    function openBulkModal() {
        const modal = document.getElementById('bulkModal');
        const content = document.getElementById('bulkModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => content.classList.remove('scale-95'), 10);
    }

    function closeBulkModal() {
        const modal = document.getElementById('bulkModal');
        const content = document.getElementById('bulkModalContent');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function openPatternModal() {
        const modal = document.getElementById('patternModal');
        const content = document.getElementById('patternModalContent');
        if(patternDayCount === 0) {
            addPatternRow();
            addPatternRow();
        }
        modal.classList.remove('hidden');
        setTimeout(() => content.classList.remove('scale-95'), 10);
    }

    function closePatternModal() {
        const modal = document.getElementById('patternModal');
        const content = document.getElementById('patternModalContent');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    /* ── CONFIRM COPY MODAL ── */
    function openCopyConfirmModal() {
        const modal = document.getElementById('copyConfirmModal');
        const content = document.getElementById('copyConfirmModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => content.classList.remove('scale-95'), 10);
    }

    function closeCopyConfirmModal() {
        const modal = document.getElementById('copyConfirmModal');
        const content = document.getElementById('copyConfirmModalContent');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function submitCopyForm() {
        document.getElementById('copyShiftForm').submit();
    }

    // Auto switch tab based on URL param
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab && document.getElementById('tab-' + activeTab)) {
            switchTab(activeTab);
        }
    }
</script>
@endpush

