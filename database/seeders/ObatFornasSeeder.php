<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Obat;

class ObatFornasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Obat::insert([
            ['kode'=>'OB001','nama'=>'Paracetamol 500mg','satuan'=>'Tablet','kategori'=>'Obat Dalam','stok'=>100,'stok_minimum'=>10,'harga_beli'=>200,'harga'=>500,'is_fornas'=>1,'kode_fornas'=>'F001','created_at'=>now(),'updated_at'=>now()],
            ['kode'=>'OB002','nama'=>'Amoxicillin 500mg','satuan'=>'Kapsul','kategori'=>'Antibiotik','stok'=>150,'stok_minimum'=>20,'harga_beli'=>500,'harga'=>1000,'is_fornas'=>1,'kode_fornas'=>'F002','created_at'=>now(),'updated_at'=>now()],
            ['kode'=>'OB003','nama'=>'Omeprazole 20mg','satuan'=>'Kapsul','kategori'=>'Obat Dalam','stok'=>80,'stok_minimum'=>15,'harga_beli'=>400,'harga'=>800,'is_fornas'=>1,'kode_fornas'=>'F003','created_at'=>now(),'updated_at'=>now()]
        ]);
    }
}
