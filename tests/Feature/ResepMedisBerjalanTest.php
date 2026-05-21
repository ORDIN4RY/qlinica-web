<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pasien;
use App\Models\Kamar;
use App\Models\Pegawai;
use App\Models\RawatInap;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\Billing;
use App\Models\User;
use App\Models\Antrian;
use App\Models\RekamMedis;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResepMedisBerjalanTest extends TestCase
{
    use RefreshDatabase;

    public function test_inpatient_prescription_workflow()
    {
        // 1. Setup users
        $pasienUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $pasien = Pasien::create([
            'user_id' => $pasienUser->id,
            'nama' => 'Budi Santoso',
            'no_rm' => 'RM-001',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka',
            'no_telepon' => '08123456789',
        ]);

        $dokterUser = User::create([
            'name' => 'dr. Andi',
            'email' => 'dokter@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $dokterPegawai = Pegawai::create([
            'user_id' => $dokterUser->id,
            'nama' => 'dr. Andi',
            'nik' => '1234567890123456',
            'jabatan_id' => null,
        ]);

        $apotekerUser = User::create([
            'name' => 'Apoteker Rina',
            'email' => 'apoteker@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $apotekerPegawai = Pegawai::create([
            'user_id' => $apotekerUser->id,
            'nama' => 'Apoteker Rina',
            'nik' => '1234567890123457',
            'jabatan_id' => null,
        ]);

        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kamar = Kamar::create([
            'kode_kamar' => 'K-VIP-01',
            'nama_kamar' => 'Kamar Melati 1',
            'kelas' => 'VIP',
            'tarif_per_malam' => 500000,
            'kapasitas' => 2,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        // Obat
        $obat = Obat::create([
            'nama' => 'Paracetamol 500mg',
            'kategori' => 'Analgesik',
            'harga' => 10000,
            'stok' => 100,
            'satuan' => 'Tablet',
        ]);

        // 2. Simulasikan Check-In Rawat Inap
        $antrian = Antrian::create([
            'no_antrian' => 1,
            'pasien_id' => $pasien->id,
            'jenis' => 'Offline',
            'keluhan' => 'Demam tinggi',
            'status' => 'Selesai',
            'tanggal' => now()->toDateString(),
        ]);

        $rekamMedis = RekamMedis::create([
            'antrian_id' => $antrian->id,
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokterPegawai->id,
            'tanggal_periksa' => now(),
            'anamnesis' => 'Demam sejak 2 hari yang lalu',
            'pemeriksaan_fisik' => 'Suhu tubuh tinggi',
            'tekanan_darah' => '120/80',
            'suhu' => 38.5,
            'berat_badan' => 70,
            'tinggi_badan' => 170,
            'nadi' => 80,
            'respirasi' => 20,
        ]);

        $rawatInap = RawatInap::create([
            'pasien_id' => $pasien->id,
            'kamar_id' => $kamar->id,
            'dokter_id' => $dokterPegawai->id,
            'jenis_penjamin' => 'Umum',
            'tgl_masuk' => now()->subDays(1),
            'status' => 'Aktif',
        ]);

        // Buat billing awal
        $billing = Billing::create([
            'rekam_medis_id' => $rekamMedis->id,
            'rawat_inap_id' => $rawatInap->id,
            'pasien_id' => $pasien->id,
            'no_invoice' => 'INV-RI-TEST-001',
            'biaya_registrasi' => 50000,
            'biaya_kamar' => 500000,
            'biaya_obat' => 0,
            'biaya_tindakan' => 0,
            'grand_total' => 550000,
            'status' => 'Belum Bayar',
        ]);

        // Buat riwayat kamar awal
        $rawatInap->kamarHistories()->create([
            'kamar_id' => $kamar->id,
            'tarif_per_malam' => $kamar->tarif_per_malam,
            'tgl_mulai' => $rawatInap->tgl_masuk,
        ]);

        // 3. Simulasikan penulisan Resep Rawat Inap
        $this->actingAs($adminUser);
        $resepData = [
            'catatan_dokter' => 'Resep Harian Rawat Inap',
            'obat_id' => [$obat->id],
            'jumlah' => [5],
            'dosis' => ['3x1'],
            'aturan_pakai' => ['Sesudah makan'],
        ];

        $response = $this->post("/admin/rawat_inap/{$rawatInap->id}/resep", $resepData);
        $response->assertRedirect();

        // Ambil resep yang baru dibuat
        $resep = Resep::where('rawat_inap_id', $rawatInap->id)->first();
        $this->assertNotNull($resep);
        $this->assertEquals('Menunggu', $resep->status);
        $this->assertCount(1, $resep->details);

        // 4. Simulasikan Apoteker memproses resep (Skrining & RACIK)
        $this->actingAs($apotekerUser);

        $responseProses = $this->patch("/apoteker/resep/{$resep->id}", [
            'action' => 'proses',
            'catatan_apoteker' => 'Resep valid',
        ]);
        $responseProses->assertRedirect();

        $resep->refresh();
        $this->assertEquals('Diproses', $resep->status);

        // Verifikasi item ditambahkan ke BillingDetail dengan suffix [Resep #ID]
        $billing->refresh();
        $this->assertEquals(50000, $billing->biaya_obat); // 5 * 10000
        $this->assertEquals(600000, $billing->grand_total); // 50000 (reg) + 500000 (kamar) + 50000 (obat)

        $detailObat = $billing->details()->where('kategori', 'Obat')->first();
        $this->assertNotNull($detailObat);
        $this->assertStringContainsString("[Resep #{$resep->id}]", $detailObat->nama_item);

        // 5. Simulasikan Apoteker menyerahkan obat (Penyerahan / SELESAI)
        $responseSelesai = $this->patch("/apoteker/resep/{$resep->id}", [
            'action' => 'selesai',
            'catatan_apoteker' => 'Sudah diserahkan dengan SOP 5 Benar',
        ]);
        $responseSelesai->assertRedirect();

        $resep->refresh();
        $this->assertEquals('Selesai', $resep->status);

        // Verifikasi stok berkurang
        $obat->refresh();
        $this->assertEquals(95, $obat->stok);
    }
}
