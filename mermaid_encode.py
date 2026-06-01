import base64
import json

code = """graph TD
    classDef startEnd fill:#10b981,stroke:#047857,stroke-width:2px,color:#fff,font-weight:bold;
    classDef admin fill:#3b82f6,stroke:#1d4ed8,stroke-width:2px,color:#fff;
    classDef dokter fill:#8b5cf6,stroke:#6d28d9,stroke-width:2px,color:#fff;
    classDef apotek fill:#f59e0b,stroke:#b45309,stroke-width:2px,color:#fff;
    classDef kasir fill:#ef4444,stroke:#b91c1c,stroke-width:2px,color:#fff;
    classDef decision fill:#f3f4f6,stroke:#4b5563,stroke-width:2px,color:#1f2937,stroke-dasharray: 5 5;

    A([Pasien Datang]):::startEnd
    B[Pendaftaran Poli/IGD<br/>Admin]:::admin
    C[Pemeriksaan & Diagnosa<br/>Dokter]:::dokter
    D{Perlu Rawat Inap?}:::decision
    E[Skrining & Proses Resep<br/>Apoteker]:::apotek
    F[Pembayaran Rawat Jalan<br/>Kasir]:::kasir
    G[Penyerahan Obat<br/>Apoteker]:::apotek
    H[Check-In & Pilih Kamar<br/>Admin Admisi]:::admin
    I[Perawatan & Visite<br/>Dokter]:::dokter
    J[Penyerahan Obat Inap<br/>Apoteker]:::apotek
    K{Pasien Pulang}:::decision
    L[Pembayaran Rawat Inap<br/>Kasir]:::kasir
    M([Selesai / Pasien Pulang]):::startEnd

    A --> B
    B -->|Masuk Antrian| C
    C -->|Input Resep & Tindakan| D
    
    D -->|Tidak| E
    E -->|Status: Menunggu Pembayaran| F
    F -->|Status: Sudah Dibayar| G
    G --> M
    
    D -->|Ya| H
    H -->|Pasien Masuk Kamar| I
    H -.->|Resep dialihkan ke Inap| J
    I -->|Resep Tambahan| J
    J -->|Serahkan langsung| I
    
    I --> K
    K -->|Checkout| L
    L --> M
"""

state = {
    "code": code,
    "mermaid": '{"theme":"default"}',
    "autoSync": True,
    "updateDiagram": True
}

encoded = base64.urlsafe_b64encode(json.dumps(state).encode('utf-8')).decode('utf-8')
print('https://mermaid.live/view#base64:' + encoded)
