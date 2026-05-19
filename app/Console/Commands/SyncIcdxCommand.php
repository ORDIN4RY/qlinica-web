<?php

namespace App\Console\Commands;

use App\Models\Icdx;
use App\Services\IcdService;
use Illuminate\Console\Command;

class SyncIcdxCommand extends Command
{
    protected $signature = 'icdx:sync
                            {--depth=5 : Kedalaman maksimal traversal hierarki}
                            {--delay=200 : Jeda antar request (ms) agar tidak rate-limit}';

    protected $description = 'Sync semua data ICD-X dari WHO ICD-11 API (traversal hierarki penuh)';

    private int $synced  = 0;
    private int $skipped = 0;
    private int $maxDepth;
    private int $delayMs;
    private IcdService $icd;

    public function handle(IcdService $icd): int
    {
        $this->icd      = $icd;
        $this->maxDepth = (int) $this->option('depth');
        $this->delayMs  = (int) $this->option('delay');

        $this->info('Memulai sync ICD-X dari WHO ICD-11 API...');
        $this->info("Max depth: {$this->maxDepth} | Delay: {$this->delayMs}ms per request");
        $this->newLine();

        try {
            $root = $this->icd->browse(); // root linearization

            if (empty($root)) {
                $this->error('Gagal mengambil root dari WHO API. Periksa koneksi dan credentials.');
                return self::FAILURE;
            }

            $children = $root['child'] ?? [];
            $this->info('Root chapter ditemukan: ' . count($children) . ' chapter');
            $this->newLine();

            $bar = $this->output->createProgressBar(count($children));
            $bar->start();

            foreach ($children as $chapterUri) {
                $this->traverse($chapterUri, 1);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("✅ Sync selesai!");
        $this->table(['Keterangan', 'Jumlah'], [
            ['Disimpan / Diperbarui', $this->synced],
            ['Dilewati (tanpa kode)', $this->skipped],
        ]);

        return self::SUCCESS;
    }

    /**
     * Traversal rekursif dari satu URI entity.
     */
    private function traverse(string $uri, int $depth): void
    {
        if ($depth > $this->maxDepth) {
            return;
        }

        // Jeda agar tidak kena rate-limit WHO API
        if ($this->delayMs > 0) {
            usleep($this->delayMs * 1000);
        }

        try {
            $data = $this->icd->browse($uri);
        } catch (\Exception $e) {
            $this->warn("  ⚠ Gagal fetch URI: {$uri} — " . $e->getMessage());
            return;
        }

        if (empty($data)) {
            return;
        }

        // Simpan jika punya kode ICD
        $kode = trim($data['theCode'] ?? $data['code'] ?? '');
        $nama = trim(strip_tags($data['title']['@value'] ?? $data['title'] ?? $data['fullySpecifiedName']['@value'] ?? ''));

        if ($kode && $nama) {
            Icdx::updateOrCreate(
                ['kode' => $kode],
                ['nama' => $nama]
            );
            $this->synced++;

            if ($this->synced % 50 === 0) {
                $this->line("  → {$this->synced} kode tersimpan...");
            }
        } else {
            $this->skipped++;
        }

        // Traversal ke child
        foreach ($data['child'] ?? [] as $childUri) {
            $this->traverse($childUri, $depth + 1);
        }
    }
}
