<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\RekamMedis;
use App\Models\User;
use App\Models\Antrian;
use App\Models\Jabatan;
use App\Models\Icdx;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class LaporanPenangananFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_laporan_penanganan_filtering()
    {
        // 1. Setup Admin User (to bypass menu/role auth checks)
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Dokter Jabatan
        $jabatanDokter = Jabatan::create([
            'nama_jabatan' => 'Dokter'
        ]);

        // 2. Setup Doctor and Patient
        $dokterUser = User::create([
            'name' => 'dr. Andi Sp.PD',
            'email' => 'dokter.andi@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        $dokterPegawai = Pegawai::create([
            'user_id' => $dokterUser->id,
            'nama' => 'dr. Andi Sp.PD',
            'nik' => '1234567890123456',
            'jabatan_id' => $jabatanDokter->id,
        ]);

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

        // 3. Setup ICDX Code
        $icdx = Icdx::create([
            'kode' => 'A00',
            'nama' => 'Cholera',
        ]);

        // 4. Create Antrian & Rekam Medis
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
            'tanggal_periksa' => Carbon::now(),
            'anamnesis' => 'Demam sejak 2 hari yang lalu',
            'pemeriksaan_fisik' => 'Suhu tubuh tinggi',
            'tekanan_darah' => '120/80',
            'suhu' => 38.5,
            'berat_badan' => 70,
            'tinggi_badan' => 170,
            'nadi' => 80,
            'respirasi' => 20,
            'kasus_penyakit' => 'Baru',
            'keadaan_keluar' => 'Membaik',
            'tindakan' => 'Rawat Luka Ringan',
            'pengobatan' => 'Paracetamol 500mg',
        ]);

        $rekamMedis->diagnosa()->create([
            'icdx_id' => $icdx->id,
            'is_primer' => true,
        ]);

        // 5. Test Access & Filtering
        $this->actingAs($adminUser);

        // A. Filter Dokter yang Tepat
        $response = $this->get(route('admin.laporan.penanganan', [
            'dokter_id' => $dokterPegawai->id
        ]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');

        // B. Filter Dokter yang Salah
        $response = $this->get(route('admin.laporan.penanganan', [
            'dokter_id' => 999
        ]));
        $response->assertStatus(200);
        $response->assertDontSee('Budi Santoso');

        // C. Filter Kasus Penyakit Baru
        $response = $this->get(route('admin.laporan.penanganan', [
            'kasus_penyakit' => 'Baru'
        ]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');

        // D. Filter Kasus Penyakit Lama
        $response = $this->get(route('admin.laporan.penanganan', [
            'kasus_penyakit' => 'Lama'
        ]));
        $response->assertStatus(200);
        $response->assertDontSee('Budi Santoso');

        // E. Filter Custom Tanggal Hari Ini (00:00:00 - 23:59:59)
        $today = Carbon::today()->toDateString();
        $response = $this->get(route('admin.laporan.penanganan', [
            'periode' => 'custom',
            'tgl_awal' => $today,
            'tgl_akhir' => $today,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');

        // F. Search by ICDX Code
        $response = $this->get(route('admin.laporan.penanganan', [
            'search' => 'A00'
        ]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');

        // G. Search by ICDX Name
        $response = $this->get(route('admin.laporan.penanganan', [
            'search' => 'Cholera'
        ]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');

        // H. Search by Tindakan
        $response = $this->get(route('admin.laporan.penanganan', [
            'search' => 'Luka'
        ]));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');

        // I. Verify Doctor Dropdown List is populated
        $response = $this->get(route('admin.laporan.penanganan'));
        $response->assertStatus(200);
        $response->assertViewHas('dokters', function ($dokters) use ($dokterPegawai) {
            return $dokters->contains($dokterPegawai);
        });
    }
}
