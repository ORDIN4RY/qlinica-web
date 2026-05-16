<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Pegawai;
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

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('admin.presensi')->with('success', 'Data presensi berhasil dihapus.');
    }
}
