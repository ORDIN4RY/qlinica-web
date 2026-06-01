<?php
$code = "flowchart TD\n    classDef startEnd fill:#111827,stroke:#374151,stroke-width:2px,color:#fff,font-weight:bold\n    classDef process fill:#1e3a8a,stroke:#3b82f6,stroke-width:2px,color:#fff\n    classDef io fill:#064e3b,stroke:#10b981,stroke-width:2px,color:#fff\n    classDef decision fill:#78350f,stroke:#f59e0b,stroke-width:2px,color:#fff\n\n    Start([Mulai Buka Aplikasi]):::startEnd\n    End([Selesai / Kembali ke Dashboard]):::startEnd\n\n    subgraph Auth [1. AUTENTIKASI]\n        direction TB\n        A_Cek{Session Login<br/>Aktif?}:::decision\n        A_Login[Tampil Layar Login]:::process\n        A_Input[/Input NIP/Email<br/>& Password/]:::io\n        A_Validasi{Kredensial<br/>Valid?}:::decision\n    end\n\n    subgraph Dash [2. DASHBOARD UTAMA]\n        direction TB\n        B_Menu{Pilih Menu<br/>Navigasi?}:::decision\n    end\n\n    subgraph Absen [3. PROSES PRESENSI]\n        direction TB\n        C_Tampil[Buka Kamera &<br/>Akses GPS Location]:::process\n        C_Input[/Ambil Foto Selfie<br/>& Titik Lokasi/]:::io\n        C_Proses[Sistem Mencocokkan<br/>Radius Lokasi Klinik]:::process\n        C_Simpan[/Simpan Data Presensi<br/>Masuk / Pulang/]:::io\n    end\n\n    subgraph Cuti [4. CUTI & IZIN]\n        direction TB\n        D_Tampil[Tampil Daftar<br/>Riwayat Pengajuan]:::process\n        D_Pilih{Buat<br/>Pengajuan Baru?}:::decision\n        D_Form[/Input Tanggal, Jenis,<br/>& Alasan Cuti/Izin/]:::io\n        D_Simpan[Kirim Pengajuan ke<br/>Server/Admin Web]:::process\n    end\n\n    subgraph Riwayat [5. RIWAYAT & PROFIL]\n        direction TB\n        E_Riwayat[Tampil Histori<br/>Kehadiran Bulanan]:::process\n        E_Profil[Tampil Data Diri<br/>& Tombol Logout]:::process\n    end\n\n    Start --> A_Cek\n    A_Cek -->|Tidak| A_Login\n    A_Login --> A_Input\n    A_Input --> A_Validasi\n    A_Validasi -->|T| A_Login\n    A_Validasi -->|Y| B_Menu\n    A_Cek -->|Ya| B_Menu\n\n    B_Menu -->|Tombol Presensi| C_Tampil\n    B_Menu -->|Menu Cuti & Izin| D_Tampil\n    B_Menu -->|Menu Riwayat| E_Riwayat\n    B_Menu -->|Menu Profil| E_Profil\n\n    C_Tampil --> C_Input\n    C_Input --> C_Proses\n    C_Proses --> C_Simpan\n    C_Simpan --> End\n\n    D_Tampil --> D_Pilih\n    D_Pilih -->|Y| D_Form\n    D_Form --> D_Simpan\n    D_Simpan --> End\n    D_Pilih -->|T| End\n\n    E_Riwayat --> End\n    E_Profil --> End\n";

$state = [
    "code" => $code,
    "mermaid" => '{"theme":"default"}',
    "autoSync" => true,
    "updateDiagram" => true
];

$json = json_encode($state);
$base64 = base64_encode($json);
$base64url = str_replace(['+', '/', '='], ['-', '_', ''], $base64);

echo "https://mermaid.live/view#base64:" . $base64url . "\n";
