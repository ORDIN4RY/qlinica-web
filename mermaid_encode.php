<?php
$code = "graph TD\n    classDef startEnd fill:#10b981,stroke:#047857,stroke-width:2px,color:#fff,font-weight:bold;\n    classDef admin fill:#3b82f6,stroke:#1d4ed8,stroke-width:2px,color:#fff;\n    classDef dokter fill:#8b5cf6,stroke:#6d28d9,stroke-width:2px,color:#fff;\n    classDef apotek fill:#f59e0b,stroke:#b45309,stroke-width:2px,color:#fff;\n    classDef kasir fill:#ef4444,stroke:#b91c1c,stroke-width:2px,color:#fff;\n    classDef decision fill:#f3f4f6,stroke:#4b5563,stroke-width:2px,color:#1f2937,stroke-dasharray: 5 5;\n\n    A([Pasien Datang]):::startEnd\n    B[Pendaftaran Poli/IGD<br/>Admin]:::admin\n    C[Pemeriksaan & Diagnosa<br/>Dokter]:::dokter\n    D{Perlu Rawat Inap?}:::decision\n    E[Skrining & Proses Resep<br/>Apoteker]:::apotek\n    F[Pembayaran Rawat Jalan<br/>Kasir]:::kasir\n    G[Penyerahan Obat<br/>Apoteker]:::apotek\n    H[Check-In & Pilih Kamar<br/>Admin Admisi]:::admin\n    I[Perawatan & Visite<br/>Dokter]:::dokter\n    J[Penyerahan Obat Inap<br/>Apoteker]:::apotek\n    K{Pasien Pulang}:::decision\n    L[Pembayaran Rawat Inap<br/>Kasir]:::kasir\n    M([Selesai / Pasien Pulang]):::startEnd\n\n    A --> B\n    B -->|Masuk Antrian| C\n    C -->|Input Resep & Tindakan| D\n    D -->|Tidak| E\n    E -->|Status: Menunggu Pembayaran| F\n    F -->|Status: Sudah Dibayar| G\n    G --> M\n    D -->|Ya| H\n    H -->|Pasien Masuk Kamar| I\n    H -.->|Resep dialihkan ke Inap| J\n    I -->|Resep Tambahan| J\n    J -->|Serahkan langsung| I\n    I --> K\n    K -->|Checkout| L\n    L --> M\n";

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
