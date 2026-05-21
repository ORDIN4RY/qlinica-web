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

    public function test_bpjs_kesehatan_checkin_auto_generates_sep_and_updates_billing()
    {
        // 1. Setup users and basic models
        $pasienUser = User::create([
            'name' => 'John Doe',
            'email' => 'john@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $pasien = Pasien::create([
            'user_id' => $pasienUser->id,
            'nama' => 'John Doe',
            'no_rm' => 'RM-002',
            'tgl_lahir' => '1985-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Kebon Jeruk',
            'no_telepon' => '0877777777',
        ]);

        $dokterUser = User::create([
            'name' => 'dr. Sarah',
            'email' => 'sarah@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $dokterPegawai = Pegawai::create([
            'user_id' => $dokterUser->id,
            'nama' => 'dr. Sarah',
            'nik' => '9876543210123456',
            'jabatan_id' => null,
        ]);

        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kamar = Kamar::create([
            'kode_kamar' => 'K-CL1-01',
            'nama_kamar' => 'Kamar Dahlia 1',
            'kelas' => 'Kelas 1',
            'tarif_per_malam' => 350000,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $antrian = Antrian::create([
            'no_antrian' => 2,
            'pasien_id' => $pasien->id,
            'jenis' => 'Offline',
            'keluhan' => 'Lemas dan pusing',
            'status' => 'Selesai',
            'tanggal' => now()->toDateString(),
        ]);

        $rekamMedis = RekamMedis::create([
            'antrian_id' => $antrian->id,
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokterPegawai->id,
            'tanggal_periksa' => now(),
            'anamnesis' => 'Pasien memerlukan rawat inap',
            'is_rekomendasi_rawat_inap' => true,
        ]);

        // Create the pre-existing Poli billing for the first patient (as generated during doctor exam)
        Billing::create([
            'rekam_medis_id' => $rekamMedis->id,
            'pasien_id' => $pasien->id,
            'no_invoice' => 'INV-TEST-002',
            'biaya_registrasi' => 50000.00,
            'biaya_tindakan' => 0.00,
            'biaya_obat' => 0.00,
            'grand_total' => 50000.00,
            'status' => 'Belum Bayar',
        ]);

        // 2. Perform Check-In with BPJS KESEHATAN and EMPTY no_sep (should auto-generate SEP)
        $this->actingAs($adminUser);

        $checkInData = [
            'pasien_id' => $pasien->id,
            'kamar_id' => $kamar->id,
            'dokter_id' => $dokterPegawai->id,
            'jenis_penjamin' => 'BPJS KESEHATAN',
            'no_sep' => '',
            'tgl_masuk' => now()->format('Y-m-d\TH:i'),
        ];

        $response = $this->post('/admin/rawat_inap', $checkInData);
        $response->assertRedirect();

        // 3. Assert RawatInap record was created with an auto-generated SEP number
        $rawatInap = RawatInap::where('pasien_id', $pasien->id)->first();
        $this->assertNotNull($rawatInap);
        $this->assertEquals('BPJS KESEHATAN', $rawatInap->jenis_penjamin);
        $this->assertNotEmpty($rawatInap->no_sep);
        $this->assertStringStartsWith('SEP-', $rawatInap->no_sep);

        // 4. Assert Billing was linked and properly updated
        $billing = Billing::where('rawat_inap_id', $rawatInap->id)->first();
        $this->assertNotNull($billing);
        $this->assertEquals('Belum Bayar', $billing->status);
        $this->assertEquals($rawatInap->no_sep, $billing->no_bpjs);

        // 5. Assert the recommendation flag has been consumed/cleared
        $rekamMedis->refresh();
        $this->assertFalse((bool)$rekamMedis->is_rekomendasi_rawat_inap);

        // 6. Setup second patient to test check-in with MANUAL no_sep
        $pasienUser2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $pasien2 = Pasien::create([
            'user_id' => $pasienUser2->id,
            'nama' => 'Jane Smith',
            'no_rm' => 'RM-003',
            'tgl_lahir' => '1992-08-20',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Kebon Kacang',
            'no_telepon' => '0877777778',
        ]);

        $antrian2 = Antrian::create([
            'no_antrian' => 3,
            'pasien_id' => $pasien2->id,
            'jenis' => 'Offline',
            'keluhan' => 'Lemas dan pusing 2',
            'status' => 'Selesai',
            'tanggal' => now()->toDateString(),
        ]);

        $rekamMedis2 = RekamMedis::create([
            'antrian_id' => $antrian2->id,
            'pasien_id' => $pasien2->id,
            'dokter_id' => $dokterPegawai->id,
            'tanggal_periksa' => now(),
            'anamnesis' => 'Pasien memerlukan rawat inap segera',
            'is_rekomendasi_rawat_inap' => true,
        ]);

        // Create pre-existing Poli billing for the second patient
        Billing::create([
            'rekam_medis_id' => $rekamMedis2->id,
            'pasien_id' => $pasien2->id,
            'no_invoice' => 'INV-TEST-003',
            'biaya_registrasi' => 50000.00,
            'biaya_tindakan' => 0.00,
            'biaya_obat' => 0.00,
            'grand_total' => 50000.00,
            'status' => 'Belum Bayar',
        ]);

        $checkInDataManual = [
            'pasien_id' => $pasien2->id,
            'kamar_id' => $kamar->id,
            'dokter_id' => $dokterPegawai->id,
            'jenis_penjamin' => 'BPJS KESEHATAN',
            'no_sep' => 'SEP-MANUAL-778899',
            'tgl_masuk' => now()->format('Y-m-d\TH:i'),
        ];

        $response2 = $this->post('/admin/rawat_inap', $checkInDataManual);
        $response2->assertRedirect();

        $rawatInap2 = RawatInap::where('pasien_id', $pasien2->id)->first();
        $this->assertNotNull($rawatInap2);
        $this->assertEquals('SEP-MANUAL-778899', $rawatInap2->no_sep);

        $billing2 = Billing::where('rawat_inap_id', $rawatInap2->id)->first();
        $this->assertNotNull($billing2);
        $this->assertEquals('SEP-MANUAL-778899', $billing2->no_bpjs);
    }

    public function test_bpjs_mode_sandbox_always_generates_simulated_sep()
    {
        // Verifies that BPJS_MODE=sandbox (default) forces simulation
        // even if BPJS credentials are present — preventing "Anomali Akses" errors.
        $pasienUser = User::create([
            'name' => 'BPJS Mode Test Patient',
            'email' => 'bpjsmode@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $pasien = Pasien::create([
            'user_id' => $pasienUser->id,
            'nama' => 'BPJS Mode Test Patient',
            'no_rm' => 'RM-BPJS-MODE',
            'tgl_lahir' => '1990-06-10',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Test',
            'no_telepon' => '08123456780',
        ]);

        $dokterUser = User::create([
            'name' => 'dr. Mode',
            'email' => 'drmode@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $dokterPegawai = Pegawai::create([
            'user_id' => $dokterUser->id,
            'nama' => 'dr. Mode',
            'nik' => '1111222233334444',
            'jabatan_id' => null,
        ]);

        $adminUser = User::create([
            'name' => 'Admin Mode',
            'email' => 'adminmode@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kamar = Kamar::create([
            'kode_kamar' => 'K-MODE-01',
            'nama_kamar' => 'Kamar Mode',
            'kelas' => 'Kelas 2',
            'tarif_per_malam' => 200000,
            'kapasitas' => 4,
            'terisi' => 0,
            'status' => 'Tersedia',
        ]);

        $antrian = Antrian::create([
            'no_antrian' => 9,
            'pasien_id' => $pasien->id,
            'jenis' => 'Offline',
            'keluhan' => 'Test',
            'status' => 'Selesai',
            'tanggal' => now()->toDateString(),
        ]);

        $rekamMedis = RekamMedis::create([
            'antrian_id' => $antrian->id,
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokterPegawai->id,
            'tanggal_periksa' => now(),
            'anamnesis' => 'Test BPJS Mode',
            'is_rekomendasi_rawat_inap' => true,
        ]);

        Billing::create([
            'rekam_medis_id' => $rekamMedis->id,
            'pasien_id' => $pasien->id,
            'no_invoice' => 'INV-TEST-BPJSMODE',
            'biaya_registrasi' => 50000.00,
            'biaya_tindakan' => 0.00,
            'biaya_obat' => 0.00,
            'grand_total' => 50000.00,
            'status' => 'Belum Bayar',
        ]);

        // Simulate BPJS_MODE=sandbox even with credentials present — must NOT hit real API
        putenv('BPJS_MODE=sandbox');
        putenv('BPJS_VCLAIM_CONSID=DUMMY_CONSID_SHOULD_BE_IGNORED');

        $this->actingAs($adminUser);

        $response = $this->post('/admin/rawat_inap', [
            'pasien_id' => $pasien->id,
            'kamar_id' => $kamar->id,
            'dokter_id' => $dokterPegawai->id,
            'jenis_penjamin' => 'BPJS KESEHATAN',
            'no_sep' => '',
            'tgl_masuk' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();

        $rawatInap = RawatInap::where('pasien_id', $pasien->id)->first();
        $this->assertNotNull($rawatInap);
        // In sandbox mode: SEP must be auto-generated (starts with SEP-)
        // and must NOT have triggered real BPJS API ("Anomali Akses" will not occur)
        $this->assertNotEmpty($rawatInap->no_sep);
        $this->assertStringStartsWith('SEP-', $rawatInap->no_sep);

        // Cleanup
        putenv('BPJS_MODE');
        putenv('BPJS_VCLAIM_CONSID');
    }
}
