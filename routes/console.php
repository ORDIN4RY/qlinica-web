<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Tandai Alpa Otomatis ───────────────────────────────────────────────────
// Jalankan setiap hari pukul 23:59 WIB untuk menandai pegawai yang
// tidak absen sama sekali pada jadwal shiftnya hari ini sebagai Alpa.
Schedule::command('presensi:tandai-alpa')
    ->dailyAt('23:59')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/alpa-scheduler.log'));
