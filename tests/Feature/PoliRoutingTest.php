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
use App\Models\Menu;
use App\Models\HakAkses;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PoliRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_crud_with_poli()
    {
        // 1. Setup Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $jabatanDokter = Jabatan::create([
            'nama_jabatan' => 'Dokter'
        ]);

        $this->actingAs($adminUser);

        // A. Store Employee with Poli
        $response = $this->post(route('admin.pegawai.store'), [
            'nama' => 'dr. Gigi Indah',
            'nik' => '1234567890123456',
            'email' => 'indah@sahaduta.com',
            'password' => 'password123',
            'jabatan_id' => $jabatanDokter->id,
            'no_hp' => '08123456789',
            'alamat' => 'Jl. Gigi Cantik',
            'no_sip' => 'SIP-GIGI-123',
            'poli' => 'Poli Gigi',
        ]);

        $response->assertRedirect(route('admin.pegawai'));
        $this->assertDatabaseHas('pegawais', [
            'nama' => 'dr. Gigi Indah',
            'poli' => 'Poli Gigi',
        ]);

        $pegawai = Pegawai::where('nama', 'dr. Gigi Indah')->first();

        // B. Update Employee Poli
        $response = $this->put(route('admin.pegawai.update', $pegawai->id), [
            'nama' => 'dr. Gigi Indah',
            'nik' => '1234567890123456',
            'email' => 'indah@sahaduta.com',
            'role' => 'dokter',
            'jabatan_id' => $jabatanDokter->id,
            'no_hp' => '08123456789',
            'alamat' => 'Jl. Gigi Cantik',
            'no_sip' => 'SIP-GIGI-123',
            'poli' => 'Poli Umum', // change to Poli Umum
        ]);

        $response->assertRedirect(route('admin.pegawai'));
        $this->assertDatabaseHas('pegawais', [
            'id' => $pegawai->id,
            'poli' => 'Poli Umum',
        ]);
    }

    public function test_ttv_checkin_and_poli_based_routing()
    {
        // 1. Setup Jabatan & Users
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $jabatanDokter = Jabatan::create([
            'nama_jabatan' => 'Dokter'
        ]);

        // Create 2 Doctors with different Poli
        $dokterGigiUser = User::create([
            'name' => 'drg. Rian',
            'email' => 'rian@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);
        $dokterGigiPegawai = Pegawai::create([
            'user_id' => $dokterGigiUser->id,
            'nama' => 'drg. Rian',
            'nik' => '1111111111111111',
            'jabatan_id' => $jabatanDokter->id,
            'poli' => 'Poli Gigi',
        ]);

        $dokterUmumUser = User::create([
            'name' => 'dr. Rudi',
            'email' => 'rudi@sahaduta.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);
        $dokterUmumPegawai = Pegawai::create([
            'user_id' => $dokterUmumUser->id,
            'nama' => 'dr. Rudi',
            'nik' => '2222222222222222',
            'jabatan_id' => $jabatanDokter->id,
            'poli' => 'Poli Umum',
        ]);

        // Seeding permissions for Antrian Pemeriksaan
        $menuAntrianPemeriksaan = Menu::create([
            'nama_menu' => 'Antrian Pemeriksaan',
            'url' => '/dokter/antrian',
        ]);
        HakAkses::create([
            'jabatan_id' => $jabatanDokter->id,
            'menu_id' => $menuAntrianPemeriksaan->id,
            'sub_akses' => [
                'view' => true,
            ],
        ]);

        // Create a Patient
        $pasien = Pasien::create([
            'nama' => 'Siti Aminah',
            'no_rm' => 'RM-002',
            'tgl_lahir' => '1995-05-05',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Gigi Sehat',
            'no_telepon' => '0822222222',
        ]);

        // Create initial queue (Antrian Pemesanan)
        $antrian = Antrian::create([
            'no_antrian' => 1,
            'pasien_id' => $pasien->id,
            'jenis' => 'Offline',
            'keluhan' => 'Sakit gigi geraham belakang',
            'status' => 'Menunggu TTV', // Initial state
            'tanggal' => now()->toDateString(),
        ]);

        // 2. Perform Checkin TTV via Receptionist (Admin User)
        $this->actingAs($adminUser);
        $response = $this->post(route('admin.antrian.panggil', $antrian->id), [
            'dokter_id' => '', // Omitted, nullable
            'jenis_pelayanan' => 'Umum',
            'pelayanan_kesehatan' => 'Poli Gigi', // Routed to Poli Gigi
            'tekanan_darah' => '120/80',
            'suhu' => 36.5,
            'berat_badan' => 55.0,
            'tinggi_badan' => 160.0,
            'nadi' => 80,
            'respirasi' => 20,
        ]);

        $response->assertRedirect(route('admin.pemesanan'));
        $this->assertDatabaseHas('rekam_medis', [
            'antrian_id' => $antrian->id,
            'dokter_id' => null, // Omitted
            'pelayanan_kesehatan' => 'Poli Gigi',
        ]);

        // 3. Verify Doctor Queue Visibility
        // Doctor Gigi (assigned to Poli Gigi) should see this patient in their queue index
        $this->actingAs($dokterGigiUser);
        $response = $this->get(route('dokter.antrian'));
        $response->assertStatus(200);
        $response->assertSee('Siti Aminah');

        // Doctor Umum (assigned to Poli Umum) should NOT see this patient in their queue index
        $this->actingAs($dokterUmumUser);
        $response = $this->get(route('dokter.antrian'));
        $response->assertStatus(200);
        $response->assertDontSee('Siti Aminah');

        // 4. Verify Doctor Examination & Claim (simpanDiagnosa)
        // Let's change queue status to 'Dipanggil' (simulating doctor calling patient)
        $antrian->update(['status' => 'Dipanggil']);

        // Doctor Gigi performs examination
        $this->actingAs($dokterGigiUser);
        $response = $this->get(route('dokter.antrian.periksa', $antrian->id));
        $response->assertStatus(200);

        // Doctor Gigi submits diagnoses
        $icdx = Icdx::create([
            'kode' => 'K02',
            'nama' => 'Dental Caries',
        ]);

        $response = $this->post(route('dokter.antrian.diagnosa', $antrian->id), [
            'anamnesis' => 'Gigi berlubang',
            'pemeriksaan_fisik' => 'Caries media',
            'tekanan_darah' => '120/80',
            'suhu' => 36.5,
            'berat_badan' => 55,
            'tinggi_badan' => 160,
            'nadi' => 80,
            'respirasi' => 20,
            'kasus_penyakit' => 'Baru',
            'status_pasien' => 'Selesai',
            'diagnosa' => [$icdx->id],
            'diagnosa_primer' => $icdx->id,
            'pakai_resep' => 'Tidak',
        ]);

        $response->assertRedirect(route('dokter.antrian'));

        // Check that doctor_id is now populated with Doctor Gigi's ID in the RekamMedis table
        $this->assertDatabaseHas('rekam_medis', [
            'antrian_id' => $antrian->id,
            'dokter_id' => $dokterGigiPegawai->id,
        ]);
    }
}
