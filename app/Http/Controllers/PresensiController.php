<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Pegawai;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $status = $request->get('status');
        
        $query = Presensi::with('pegawai.jabatan')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($status) {
            $query->where('status', $status);
        }
        
        $presensis = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
            
        // Data untuk tab Pengaturan Shift
        $pegawais = Pegawai::with('jabatan')->orderBy('nama')->get();
        $shifts = \App\Models\Shift::all();
        
        // Ambil jadwal shift bulan ini
        $jadwalShifts = \App\Models\JadwalShift::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->groupBy('pegawai_id')
            ->map(function($items) {
                return $items->keyBy('tanggal');
            });
        
        // Data untuk tab Persetujuan (Status Pending - Dikelompokkan per Pengajuan)
        $pengajuansRaw = Presensi::with('pegawai')
            ->where('approval_status', 'Pending')
            ->whereIn('status', ['Cuti', 'Izin', 'Sakit'])
            ->orderBy('tanggal', 'asc')
            ->get();
            
        $pengajuans = $pengajuansRaw->groupBy(function($p) {
            return $p->batch_id ?? ('manual_' . $p->id);
        })->map(function($group) {
            $first = $group->first();
            $last = $group->last();
            return (object) [
                'id'              => $first->id,
                'batch_id'        => $first->batch_id,
                'pegawai'         => $first->pegawai,
                'status'          => $first->status,
                'keterangan'      => $first->keterangan,
                'surat_dokter'    => $first->surat_dokter,
                'tanggal_mulai'   => $first->tanggal,
                'tanggal_selesai' => $last->tanggal,
                'durasi'          => $group->count(),
            ];
        });
            
        // KPI Summary (Akurasi: Bandingkan Jadwal vs Kehadiran)
        $baseKpiQuery = Presensi::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        
        $kpi = [
            'hadir' => (clone $baseKpiQuery)->where('status', 'Hadir')->count(),
            'sakit' => (clone $baseKpiQuery)->where('status', 'Sakit')->count(),
            'izin'  => (clone $baseKpiQuery)->where('status', 'Izin')->count(),
            'cuti'  => (clone $baseKpiQuery)->where('status', 'Cuti')->count(),
            'alpa'  => 0, // Akan dihitung di bawah
        ];

        // Hitung Alpa: Ada Jadwal tapi tidak ada record Presensi (sampai hari ini)
        $today = now();
        $allJadwal = \App\Models\JadwalShift::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->get();
        $allPresence = (clone $baseKpiQuery)->get()->groupBy('pegawai_id')->map(function($p) {
            return $p->keyBy('tanggal');
        });

        foreach ($allJadwal as $j) {
            // Hanya hitung alpa untuk tanggal yang sudah lewat atau hari ini (setelah jam pulang shift)
            $shiftEnd = \Carbon\Carbon::parse($j->tanggal . ' ' . $j->shift->jam_pulang);
            if ($shiftEnd->isPast()) {
                if (!isset($allPresence[$j->pegawai_id][$j->tanggal])) {
                    $kpi['alpa']++;
                }
            }
        }

        return view('admin.presensi', compact('presensis', 'pegawais', 'pengajuans', 'shifts', 'jadwalShifts', 'bulan', 'tahun', 'kpi'));
    }

    /** Update status persetujuan cuti/izin. */
    public function update(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);
        
        $request->validate([
            'approval_status' => 'required|in:Approved,Rejected',
        ]);

        if ($presensi->batch_id) {
            // Jika disetujui dan statusnya Cuti, potong jatah cuti pegawai
            if ($request->approval_status === 'Approved' && $presensi->status === 'Cuti' && $presensi->approval_status === 'Pending') {
                $durasi = Presensi::where('batch_id', $presensi->batch_id)->count();
                $presensi->pegawai->decrement('jatah_cuti', $durasi);
            }

            Presensi::where('batch_id', $presensi->batch_id)->update([
                'approval_status' => $request->approval_status
            ]);
        } else {
            // Jika disetujui dan statusnya Cuti, potong jatah cuti pegawai
            if ($request->approval_status === 'Approved' && $presensi->status === 'Cuti' && $presensi->approval_status === 'Pending') {
                $presensi->pegawai->decrement('jatah_cuti', 1);
            }

            $presensi->update([
                'approval_status' => $request->approval_status
            ]);
        }

        // Kirim Push Notification FCM ke pegawai
        try {
            $pegawai = $presensi->pegawai;
            if ($pegawai && $pegawai->user && $pegawai->user->fcm_token) {
                $fcmToken = $pegawai->user->fcm_token;
                $statusText = $request->approval_status === 'Approved' ? 'DISETUJUI' : 'DITOLAK';
                $icon = $request->approval_status === 'Approved' ? '✅' : '❌';
                
                $title = "{$icon} Pengajuan {$presensi->status} {$statusText}";
                
                $tanggalText = $presensi->tanggal;
                if ($presensi->batch_id) {
                    $dates = Presensi::where('batch_id', $presensi->batch_id)->orderBy('tanggal', 'asc')->pluck('tanggal');
                    if ($dates->count() > 1) {
                        $tanggalText = $dates->first() . ' s/d ' . $dates->last();
                    }
                }
                
                $body = "Pengajuan {$presensi->status} Anda untuk tanggal {$tanggalText} telah {$statusText} oleh Admin.";
                
                FirebaseService::sendPushNotification($fcmToken, $title, $body, [
                    'type'     => 'cuti_status',
                    'status'   => $request->approval_status,
                    'batch_id' => $presensi->batch_id ?? '',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Gagal mengirim FCM notifikasi: " . $e->getMessage());
        }

        return redirect()->route('admin.presensi', ['tab' => 'persetujuan'])->with('success', 'Status pengajuan presensi berhasil diperbarui.');
    }

    /** Update hari libur shift pegawai. */
    public function updateShift(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        $request->validate([
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        if (!$request->shift_id) {
            // Jika tidak ada shift_id, berarti diset Libur (hapus jadwal)
            \App\Models\JadwalShift::where('pegawai_id', $pegawai->id)
                ->where('tanggal', $request->tanggal)
                ->delete();
                
            return redirect()->route('admin.presensi', ['tab' => 'shift'])->with('success', 'Jadwal ' . $pegawai->nama . ' pada ' . $request->tanggal . ' telah diset Libur.');
        }

        \App\Models\JadwalShift::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $request->tanggal],
            ['shift_id' => $request->shift_id]
        );

        return redirect()->route('admin.presensi', ['tab' => 'shift'])->with('success', 'Jadwal shift ' . $pegawai->nama . ' berhasil diperbarui.');
    }

    public function bulkShift(Request $request)
    {
        $request->validate([
            'pegawai_ids' => 'required|array',
            'pegawai_ids.*' => 'exists:pegawai,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'shift_id' => 'nullable|exists:shifts,id',
            'skip_minggu' => 'nullable|boolean',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

        foreach ($request->pegawai_ids as $pegawai_id) {
            $currentDate = $tanggalMulai->copy();
            while ($currentDate->lte($tanggalSelesai)) {
                if ($request->skip_minggu && $currentDate->isSunday()) {
                    $currentDate->addDay();
                    continue;
                }

                if (!$request->shift_id) {
                    \App\Models\JadwalShift::where('pegawai_id', $pegawai_id)
                        ->where('tanggal', $currentDate->toDateString())
                        ->delete();
                } else {
                    \App\Models\JadwalShift::updateOrCreate(
                        ['pegawai_id' => $pegawai_id, 'tanggal' => $currentDate->toDateString()],
                        ['shift_id' => $request->shift_id]
                    );
                }
                
                $currentDate->addDay();
            }
        }

        return redirect()->route('admin.presensi', ['tab' => 'shift'])->with('success', 'Jadwal shift massal berhasil disimpan.');
    }

    public function patternShift(Request $request)
    {
        $request->validate([
            'pegawai_ids' => 'required|array',
            'pegawai_ids.*' => 'exists:pegawai,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'pola' => 'required|array',
            'skip_minggu' => 'nullable|boolean',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        $pola = $request->pola;
        $polaLength = count($pola);

        if ($polaLength === 0) {
            return back()->withErrors(['pola' => 'Pola tidak boleh kosong']);
        }

        foreach ($request->pegawai_ids as $pegawai_id) {
            $currentDate = $tanggalMulai->copy();
            $patternIndex = 0;

            while ($currentDate->lte($tanggalSelesai)) {
                if ($request->skip_minggu && $currentDate->isSunday()) {
                    $currentDate->addDay();
                    continue;
                }

                $shiftId = $pola[$patternIndex % $polaLength];

                if (!$shiftId || $shiftId == '0') {
                    \App\Models\JadwalShift::where('pegawai_id', $pegawai_id)
                        ->where('tanggal', $currentDate->toDateString())
                        ->delete();
                } else {
                    \App\Models\JadwalShift::updateOrCreate(
                        ['pegawai_id' => $pegawai_id, 'tanggal' => $currentDate->toDateString()],
                        ['shift_id' => $shiftId]
                    );
                }

                $currentDate->addDay();
                $patternIndex++;
            }
        }

        return redirect()->route('admin.presensi', ['tab' => 'shift'])->with('success', 'Pola shift berhasil diterapkan.');
    }

    public function copyShift(Request $request)
    {
        $request->validate([
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|numeric',
        ]);

        $targetBulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
        $targetTahun = $request->tahun;

        // Hitung bulan sebelumnya
        $sourceCarbon = Carbon::createFromDate($targetTahun, $targetBulan, 1)->subMonth();
        $sourceBulan = $sourceCarbon->format('m');
        $sourceTahun = $sourceCarbon->format('Y');

        // Ambil semua jadwal dari bulan sebelumnya
        $sourceJadwals = \App\Models\JadwalShift::whereMonth('tanggal', $sourceBulan)
            ->whereYear('tanggal', $sourceTahun)
            ->get();

        if ($sourceJadwals->isEmpty()) {
            return back()->with('error', 'Tidak ada data jadwal pada bulan sebelumnya untuk disalin.');
        }

        $targetDaysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$targetBulan, (int)$targetTahun);

        foreach ($sourceJadwals as $sj) {
            $day = Carbon::parse($sj->tanggal)->format('d');
            
            // Lewati jika hari tidak ada di bulan target (misal tanggal 31 di bulan yang hanya sampai 30 hari)
            if ((int)$day > $targetDaysInMonth) {
                continue;
            }

            $targetTanggal = sprintf('%04d-%02d-%02d', $targetTahun, $targetBulan, $day);

            \App\Models\JadwalShift::updateOrCreate(
                ['pegawai_id' => $sj->pegawai_id, 'tanggal' => $targetTanggal],
                ['shift_id' => $sj->shift_id]
            );
        }

        return redirect()->route('admin.presensi', [
            'tab' => 'shift',
            'bulan' => $targetBulan,
            'tahun' => $targetTahun
        ])->with('success', 'Jadwal shift berhasil disalin dari bulan sebelumnya.');
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('admin.presensi')->with('success', 'Data presensi berhasil dihapus.');
    }
}
