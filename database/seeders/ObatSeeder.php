<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $obat = [
            [
                'kode' => 'OBT001',
                'nama' => 'Paracetamol 500mg',
                'satuan' => 'Tablet',
                'kategori' => 'Obat Bebas',
                'stok' => 100,
                'stok_minimum' => 20,
                'harga' => 5000,
                'keterangan' => 'Obat penurun panas dan pereda nyeri',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT002',
                'nama' => 'Amoxicillin 500mg',
                'satuan' => 'Kapsul',
                'kategori' => 'Antibiotik',
                'stok' => 50,
                'stok_minimum' => 10,
                'harga' => 15000,
                'keterangan' => 'Antibiotik untuk infeksi bakteri',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT003',
                'nama' => 'Ibuprofen 400mg',
                'satuan' => 'Tablet',
                'kategori' => 'Analgesik',
                'stok' => 80,
                'stok_minimum' => 15,
                'harga' => 8000,
                'keterangan' => 'Obat anti inflamasi non-steroid',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT004',
                'nama' => 'Cetirizine 10mg',
                'satuan' => 'Tablet',
                'kategori' => 'Antihistamin',
                'stok' => 60,
                'stok_minimum' => 10,
                'harga' => 6000,
                'keterangan' => 'Obat alergi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT005',
                'nama' => 'Omeprazole 20mg',
                'satuan' => 'Kapsul',
                'kategori' => 'Antasida',
                'stok' => 40,
                'stok_minimum' => 10,
                'harga' => 12000,
                'keterangan' => 'Obat maag dan asam lambung',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT006',
                'nama' => 'Vitamin C 500mg',
                'satuan' => 'Tablet',
                'kategori' => 'Vitamin',
                'stok' => 150,
                'stok_minimum' => 30,
                'harga' => 20000,
                'keterangan' => 'Suplemen vitamin C',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT007',
                'nama' => 'Antasida Doen',
                'satuan' => 'Tablet',
                'kategori' => 'Antasida',
                'stok' => 100,
                'stok_minimum' => 20,
                'harga' => 4000,
                'keterangan' => 'Obat maag ringan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT008',
                'nama' => 'Salep Hidrokortison 2.5%',
                'satuan' => 'Tube',
                'kategori' => 'Kortikosteroid',
                'stok' => 25,
                'stok_minimum' => 5,
                'harga' => 15000,
                'keterangan' => 'Obat gatal alergi kulit',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT009',
                'nama' => 'Sirup Obat Batuk Hitam (OBH)',
                'satuan' => 'Botol',
                'kategori' => 'Obat Batuk',
                'stok' => 30,
                'stok_minimum' => 5,
                'harga' => 18000,
                'keterangan' => 'Obat batuk berdahak',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'OBT010',
                'nama' => 'Loperamide 2mg',
                'satuan' => 'Tablet',
                'kategori' => 'Antidiare',
                'stok' => 40,
                'stok_minimum' => 10,
                'harga' => 5000,
                'keterangan' => 'Obat diare',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('obat')->insert($obat);
    }
}
