<?php

use App\Models\Menu;
use App\Models\HakAkses;

$menus = [
    'Kamar',
    'Rawat Inap',
];

foreach ($menus as $nama) {
    Menu::updateOrCreate(
        ['nama_menu' => $nama],
        ['nama_menu' => $nama]
    );
}

$menusDb = Menu::pluck('id', 'nama_menu');

$akses = [
    [
        'jabatan_id' => 1,
        'menu_id' => $menusDb['Rawat Inap'],
        'sub_akses' => ['view' => true],
    ],
    [
        'jabatan_id' => 1,
        'menu_id' => $menusDb['Kamar'],
        'sub_akses' => ['view' => true],
    ],
    [
        'jabatan_id' => 3,
        'menu_id' => $menusDb['Kamar'],
        'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
    ],
    [
        'jabatan_id' => 3,
        'menu_id' => $menusDb['Rawat Inap'],
        'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
    ],
];

foreach ($akses as $data) {
    HakAkses::firstOrCreate(
        ['jabatan_id' => $data['jabatan_id'], 'menu_id' => $data['menu_id']],
        ['sub_akses' => $data['sub_akses']]
    );
}

echo "Seeding completed.\n";
