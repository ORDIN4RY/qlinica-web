<?php

namespace App\Console\Commands;

use App\Models\JadwalShift;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TandaiAlpaOtomatis extends Command
{
    protected $signature   = 'presensi:tandai-alpa {--tanggal= : Tanggal spesifik (Y-m-d), default hari ini}';
    protected $description = 'Tandai pegawai yang tidak absen sama sekali pada jadwal shiftnya sebagai Alpa';

    public function handle(): int
    {
        $tanggal  = $this->option('tanggal') ? Carbon::parse($this->option('tanggal')) : Carbon::today();
        $sekarang = Carbon::now();

        $this->info("Menjalankan penandaan Alpa untuk tanggal: {$tanggal->toDateString()}");

        // Ambil semua jadwal shift pada tanggal tersebut beserta relasi shift & pegawai
        $jadwals = JadwalShift::with(['shift', 'pegawai'])
            ->whereDate('tanggal', $tanggal->toDateString())
            ->get();

        if ($jadwals->isEmpty()) {
            $this->warn('Tidak ada jadwal shift pada tanggal ini.');
            return self::SUCCESS;
        }

        $jumlahAlpa   = 0;
        $jumlahSkip   = 0;

        foreach ($jadwals as $jadwal) {
            // Pastikan shift & pegawai ada
            if (!$jadwal->shift || !$jadwal->pegawai) {
                continue;
            }

            $pegawaiId = $jadwal->pegawai_id;
            $namaShift = $jadwal->shift->nama_shift;

            // Hitung jam pulang shift (handle shift malam: pulang < masuk)
            $jamPulang = Carbon::parse($tanggal->toDateString() . ' ' . $jadwal->shift->jam_pulang);
            if ($jadwal->shift->jam_pulang < $jadwal->shift->jam_masuk) {
                // Shift malam — jam pulang adalah keesokan harinya
                $jamPulang->addDay();
            }

            // Hanya tandai Alpa jika jam pulang shift sudah berlalu
            if ($sekarang->lt($jamPulang)) {
                $jumlahSkip++;
                continue; // Shift belum selesai, skip
            }

            // Cek apakah sudah ada record presensi (Hadir/Alpa/Cuti/Izin/Sakit)
            $sudahAda = Presensi::where('pegawai_id', $pegawaiId)
                ->whereDate('tanggal', $tanggal->toDateString())
                ->exists();

            if ($sudahAda) {
                $jumlahSkip++;
                continue; // Sudah ada record, tidak perlu buat Alpa
            }

            // Buat record Alpa di database
            Presensi::create([
                'pegawai_id'      => $pegawaiId,
                'jadwal_shift_id' => $jadwal->id,
                'tanggal'         => $tanggal->toDateString(),
                'jam_masuk'       => null,
                'jam_keluar'      => null,
                'telat_menit'     => 0,
                'status'          => 'Alpa',
                'approval_status' => 'Approved',
                'keterangan'      => "Alpa - Tidak masuk {$namaShift}",
            ]);

            $jumlahAlpa++;
            $this->line("  ✓ Alpa: Pegawai ID {$pegawaiId} ({$namaShift})");
        }

        $this->info("Selesai: {$jumlahAlpa} pegawai ditandai Alpa, {$jumlahSkip} dilewati.");
        return self::SUCCESS;
    }
}
