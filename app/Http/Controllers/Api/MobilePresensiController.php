<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MobilePresensiController extends Controller
{
    // Menghapus konstanta jam statis karena sekarang menggunakan sistem shift dinamis


    /**
     * Ambil riwayat presensi pegawai yang login.
     * Menyisipkan record Alpha untuk hari kerja tanpa presensi.
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan.',
            ], 404);
        }

        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        // ★ Batas awal data: 16 Mei 2026 (data sebelumnya dihapus)
        $batasAwal = Carbon::parse('2026-05-16');

        // Ambil data presensi dari database (mulai 16 Mei)
        $presensis = Presensi::where('pegawai_id', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('tanggal', '>=', $batasAwal->toDateString())
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil record hari ini khusus untuk info status clock-in/out
        $todayRecord = Presensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // Map tanggal yang sudah ada presensi
        $presensiMap = [];
        foreach ($presensis as $p) {
            $presensiMap[$p->tanggal] = $this->formatPresensi($p);
        }

        // Ambil tanggal cuti/izin/sakit yang disetujui
        $cutiDates = $this->getCutiDates($pegawai->id, $bulan, $tahun);

        // Hari libur pegawai: 0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu
        $hariLibur = (int) ($pegawai->hari_libur ?? 0);

        // Generate semua hari di bulan ini (mulai dari 16 Mei atau awal bulan)
        $startOfMonth = Carbon::create($tahun, $bulan, 1);
        // Jangan mulai sebelum batas awal 16 Mei
        if ($startOfMonth->lt($batasAwal)) {
            $startOfMonth = $batasAwal->copy();
        }

        $endOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        $today      = Carbon::today();
        // ★ Kembalikan Riwayat hanya sampai hari ini saja (tidak tampil jadwal masa depan di list riwayat)
        $lastDate   = $endOfMonth->lt($today) ? $endOfMonth : $today; 
        
        $jadwalMap = \App\Models\JadwalShift::with('shift')
            ->where('pegawai_id', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy('tanggal');

        $result = [];

        for ($date = $startOfMonth->copy(); $date->lte($lastDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $shift   = $jadwalMap[$dateStr] ?? null;

            if (isset($presensiMap[$dateStr])) {
                // Ada record presensi (Hadir, Cuti, Izin, Sakit, dll)
                $result[] = $presensiMap[$dateStr];
            } elseif (!$shift) {
                // MASA LALU: Tidak ada jadwal shift = Hari Libur / Off
                $result[] = [
                    'id'              => null,
                    'tanggal'         => $dateStr,
                    'jam_masuk'       => null,
                    'jam_keluar'      => null,
                    'telat_menit'     => 0,
                    'status'          => 'Libur',
                    'approval_status' => null,
                    'keterangan'      => 'Hari libur (tidak ada jadwal)',
                ];
            } elseif (in_array($dateStr, $cutiDates)) {
                // Ada cuti/izin yang disetujui — skip (sudah ada record di presensiMap)
                continue;
            } else {
                // MASA LALU: Ada jadwal tapi belum absen — Alpha (hanya setelah jam pulang shift)
                $jamPulangShift = Carbon::parse($dateStr . ' ' . $shift->shift->jam_pulang);
                
                // Jika shift malam (pulang pagi), tambahkan 1 hari ke jam pulang untuk pengecekan alpha
                if (Carbon::parse($shift->shift->jam_pulang)->lt(Carbon::parse($shift->shift->jam_masuk))) {
                    $jamPulangShift->addDay();
                }

                if (Carbon::now()->gte($jamPulangShift)) {
                    $result[] = [
                        'id'              => null,
                        'tanggal'         => $dateStr,
                        'jam_masuk'       => null,
                        'jam_keluar'      => null,
                        'telat_menit'     => 0,
                        'status'          => 'Alpa',
                        'approval_status' => null,
                        'keterangan'      => "Alpa - Tidak masuk {$shift->shift->nama_shift}",
                    ];
                }
            }
        }

        // Sort descending by tanggal
        usort($result, fn($a, $b) => strcmp($b['tanggal'], $a['tanggal']));

        // Hitung ringkasan (excludes Libur from counts)
        $hadir = 0;
        $telat = 0;
        $alpha = 0;
        $cuti  = 0;
        $izin  = 0;
        $sakit = 0;
        
        foreach ($result as $r) {
            if ($r['status'] === 'Alpa') {
                $alpha++;
            } elseif ($r['status'] === 'Cuti') {
                $cuti++;
            } elseif ($r['status'] === 'Izin') {
                $izin++;
            } elseif ($r['status'] === 'Sakit') {
                $sakit++;
            } elseif ($r['status'] === 'Libur') {
                // Libur tidak dihitung
            } elseif ($r['status'] === 'Hadir') {
                if (($r['telat_menit'] ?? 0) > 0) {
                    $telat++;
                }
                $hadir++;
            }
        }

        // Info jadwal hari ini
        $jadwalHariIni = \App\Models\JadwalShift::with('shift')
            ->where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // Info jadwal mendatang (7 hari ke depan)
        $jadwalMendatang = \App\Models\JadwalShift::with('shift')
            ->where('pegawai_id', $pegawai->id)
            ->where('tanggal', '>', now()->toDateString())
            ->where('tanggal', '<=', now()->addDays(7)->toDateString())
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function($j) {
                return [
                    'tanggal' => $j->tanggal,
                    'hari'    => \Carbon\Carbon::parse($j->tanggal)->translatedFormat('l'),
                    'shift'   => $j->shift->nama_shift,
                    'jam'     => substr($j->shift->jam_masuk, 0, 5) . ' - ' . substr($j->shift->jam_pulang, 0, 5),
                ];
            });

        return response()->json([
            'success'         => true,
            'presensi'        => $result,
            'today'           => $todayRecord ? $this->formatPresensi($todayRecord) : null,
            'jadwal_today'    => $jadwalHariIni ? [
                'nama'   => $jadwalHariIni->shift->nama_shift,
                'masuk'  => substr($jadwalHariIni->shift->jam_masuk, 0, 5),
                'pulang' => substr($jadwalHariIni->shift->jam_pulang, 0, 5),
            ] : null,
            'jadwal_upcoming' => $jadwalMendatang,
            'has_clocked_in'  => $todayRecord?->jam_masuk !== null,
            'has_clocked_out' => $todayRecord?->jam_keluar !== null,
            'ringkasan'       => [
                'hadir' => $hadir,
                'telat' => $telat,
                'alpha' => $alpha,
                'cuti'  => $cuti,
                'izin'  => $izin,
                'sakit' => $sakit,
            ],
        ]);
    }

    /**
     * Clock In: catat jam masuk + hitung telat.
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'is_location_valid' => 'required|boolean',
            'foto'              => 'nullable|string',
        ]);

        // ★ Enforce lokasi: tolak jika di luar radius
        if (!$request->is_location_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area klinik. Absen hanya bisa dilakukan di dalam radius.',
            ], 403);
        }

        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $today = now()->toDateString();

        $existing = Presensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini.',
            ], 409);
        }

        // ★ Cek Jadwal Shift hari ini
        $jadwal = \App\Models\JadwalShift::with('shift')
            ->where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal kerja hari ini (Hari Libur).',
            ], 403);
        }

        // ★ Hitung keterlambatan berdasarkan shift
        $jamMasukShift = Carbon::parse($today . ' ' . $jadwal->shift->jam_masuk);
        $sekarang      = Carbon::now();
        $telatMenit    = 0;
        $keterangan    = 'Hadir tepat waktu (' . $jadwal->shift->nama_shift . ')';

        if ($sekarang->gt($jamMasukShift)) {
            $telatMenit = $sekarang->diffInMinutes($jamMasukShift);
            $keterangan = "Telat {$telatMenit} menit pada {$jadwal->shift->nama_shift}";
        }

        $presensi = Presensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $today],
            [
                'jadwal_shift_id'   => $jadwal->id,
                'jam_masuk'         => now()->toTimeString(),
                'telat_menit'       => $telatMenit,
                'status'            => 'Hadir',
                'approval_status'   => 'Pending',
                'keterangan'        => $keterangan,
            ]
        );

        return response()->json([
            'success'  => true,
            'message'  => $telatMenit > 0
                ? "Absen masuk dicatat. Anda telat {$telatMenit} menit."
                : 'Absen masuk berhasil dicatat.',
            'presensi' => $this->formatPresensi($presensi),
        ]);
    }

    /**
     * Clock Out: catat jam keluar.
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'is_location_valid' => 'required|boolean',
        ]);

        // ★ Enforce lokasi
        if (!$request->is_location_valid) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area klinik. Absen pulang hanya bisa dilakukan di dalam radius.',
            ], 403);
        }

        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $today    = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Cari presensi hari ini atau kemarin yang belum ada jam_keluar
        $presensi = Presensi::where('pegawai_id', $pegawai->id)
            ->whereIn('tanggal', [$today, $yesterday])
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->orderBy('tanggal', 'desc')
            ->first();

        if (!$presensi || !$presensi->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 409);
        }

        if ($presensi->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
            ], 409);
        }

        $presensi->update([
            'jam_keluar'      => now()->toTimeString(),
            'approval_status' => 'Pending',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Absen pulang berhasil dicatat.',
            'presensi' => $this->formatPresensi($presensi->fresh()),
        ]);
    }

    /**
     * Helper: ambil tanggal-tanggal cuti/izin/sakit yang sudah disetujui.
     * Data disimpan di tabel presensis dengan status Cuti/Izin/Sakit.
     */
    private function getCutiDates(int $pegawaiId, int $bulan, int $tahun): array
    {
        $records = Presensi::where('pegawai_id', $pegawaiId)
            ->whereIn('status', ['Cuti', 'Izin', 'Sakit'])
            ->where('approval_status', 'Approved')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->pluck('tanggal')
            ->toArray();

        return $records;
    }

    /**
     * Helper: format data presensi untuk response JSON.
     */
    private function formatPresensi(Presensi $p): array
    {
        return [
            'id'              => $p->id,
            'tanggal'         => $p->tanggal,
            'jam_masuk'       => $p->jam_masuk,
            'jam_keluar'      => $p->jam_keluar,
            'telat_menit'     => $p->telat_menit ?? 0,
            'status'          => $p->status,
            'approval_status' => $p->approval_status,
            'keterangan'      => $p->keterangan,
        ];
    }
}
