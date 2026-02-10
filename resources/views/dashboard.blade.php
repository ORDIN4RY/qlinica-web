<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Klinik & Apotek Terintegrasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clinic: {
                            primary: '#3b82f6',
                            secondary: '#1e40af',
                            accent: '#10b981',
                            light: '#f8fafc',
                            dark: '#0f172a'
                        },
                        pharmacy: {
                            primary: '#8b5cf6',
                            secondary: '#7c3aed',
                            accent: '#f59e0b'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-clinic-dark to-pharmacy-primary text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-clinic-medical"></i>
                    <span>Klinik-Apotek</span>
                </h1>
                <p class="text-gray-300 text-sm mt-1">Sehat Sentosa Terpadu</p>
            </div>
            
            <nav class="mt-6">
                <a href="#" class="flex items-center gap-3 px-6 py-3 text-white bg-blue-900/30 border-l-4 border-clinic-primary">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#patients" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-user-injured w-5"></i>
                    <span>Manajemen Pasien</span>
                </a>
                <a href="#prescriptions" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-prescription-bottle-alt w-5"></i>
                    <span>Resep & Obat</span>
                </a>
                <a href="#pharmacy" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-pills w-5"></i>
                    <span>Manajemen Apotek</span>
                </a>
                <a href="#inventory" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-warehouse w-5"></i>
                    <span>Stok & Inventori</span>
                </a>
                <a href="#billing" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-file-invoice-dollar w-5"></i>
                    <span>Penagihan & Pembayaran</span>
                </a>
                <a href="#reports" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>Laporan Terintegrasi</span>
                </a>
            </nav>
            
            <div class="absolute bottom-0 w-64 p-6 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-clinic-primary to-pharmacy-primary flex items-center justify-center">
                        <i class="fas fa-user-md text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium">Dr. Andi Wijaya</p>
                        <p class="text-sm text-gray-300">Dokter & Apoteker</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-clinic-dark">Sistem Klinik & Apotek Terintegrasi</h2>
                    <p class="text-gray-600">Manajemen terpadu perawatan dan farmasi</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Cari pasien, obat, atau resep..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-clinic-primary w-64">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-clinic-primary text-white px-3 py-1 rounded-full text-sm">Klinik</span>
                        <span class="bg-pharmacy-primary text-white px-3 py-1 rounded-full text-sm">Apotek</span>
                    </div>
                    <div class="relative">
                        <button id="notificationBtn" class="relative p-2 text-gray-600 hover:text-clinic-primary">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notificationBadge" class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-xs flex items-center justify-center text-white">3</span>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Main Dashboard -->
            <main class="p-6">
                <!-- Statistik Terintegrasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-clinic-primary">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500">Pasien Hari Ini</p>
                                <p class="text-3xl font-bold text-clinic-dark mt-2" id="todayPatients">18</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span id="withPrescription">12</span> dengan resep
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-user-injured text-2xl text-clinic-primary"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-pharmacy-primary">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500">Resep Diproses</p>
                                <p class="text-3xl font-bold text-clinic-dark mt-2" id="processedPrescriptions">24</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span id="pendingPrescriptions">3</span> menunggu
                                </p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-prescription-bottle-alt text-2xl text-pharmacy-primary"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-clinic-accent">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500">Stok Obat</p>
                                <p class="text-3xl font-bold text-clinic-dark mt-2" id="totalStock">142</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span id="lowStock" class="text-red-500">5</span> hampir habis
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-pills text-2xl text-clinic-accent"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-pharmacy-accent">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500">Pendapatan Hari Ini</p>
                                <p class="text-3xl font-bold text-clinic-dark mt-2">Rp <span id="todayRevenue">6,8</span>jt</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span id="pharmacyRevenue">Rp 2,1jt</span> dari apotek
                                </p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-wallet text-2xl text-pharmacy-accent"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sistem Terintegrasi -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Form Resep Digital -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-clinic-dark mb-6">Sistem Resep Digital Terintegrasi</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Data Pasien -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-clinic-primary">Data Pasien</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pasien</label>
                                    <select id="patientSelect" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-clinic-primary">
                                        <option value="">-- Pilih Pasien --</option>
                                        <option value="1">Ahmad Fauzi (RM-001)</option>
                                        <option value="2">Sinta Wulandari (RM-002)</option>
                                        <option value="3">Bambang Sutrisno (RM-003)</option>
                                        <option value="4">Dewi Anggraini (RM-004)</option>
                                    </select>
                                </div>
                                
                                <div id="patientInfo" class="hidden p-4 bg-blue-50 rounded-lg">
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="font-medium" id="patientName">-</p>
                                            <p class="text-sm text-gray-600" id="patientId">-</p>
                                            <p class="text-sm text-gray-600" id="patientAge">-</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600" id="patientAllergy">Alergi: -</p>
                                            <p class="text-sm text-gray-600" id="patientCondition">Kondisi: -</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Data Dokter -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-clinic-primary">Data Dokter</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dokter Penanggung Jawab</label>
                                    <select id="doctorSelect" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-clinic-primary">
                                        <option value="">-- Pilih Dokter --</option>
                                        <option value="1">Dr. Sari Dewi (Sp. Jantung)</option>
                                        <option value="2">Dr. Budi Santoso (Sp. Anak)</option>
                                        <option value="3">Dr. Rina Melati (Sp. Kulit)</option>
                                    </select>
                                </div>
                                
                                <div class="flex items-center gap-2 text-sm">
                                    <i class="fas fa-stamp text-clinic-primary"></i>
                                    <span>Resep digital akan ditandatangani elektronik</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Resep -->
                        <div class="mt-6">
                            <h4 class="font-medium text-pharmacy-primary mb-4">Resep Obat</h4>
                            <div class="space-y-4" id="prescriptionForm">
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Obat</label>
                                        <select id="medicineSelect" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-clinic-primary">
                                            <option value="">-- Pilih Obat --</option>
                                            <option value="1">Paracetamol 500mg</option>
                                            <option value="2">Amoxicillin 500mg</option>
                                            <option value="3">Cetirizine 10mg</option>
                                            <option value="4">Omeprazole 20mg</option>
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                        <input type="number" id="medicineQty" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-clinic-primary" min="1" value="1">
                                    </div>
                                    <div class="w-32">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Aturan</label>
                                        <select id="medicineInstruction" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-clinic-primary">
                                            <option value="3x1">3x1 sehari</option>
                                            <option value="2x1">2x1 sehari</option>
                                            <option value="1x1">1x1 sehari</option>
                                            <option value="Sesuai kebutuhan">Sesuai kebutuhan</option>
                                        </select>
                                    </div>
                                    <div class="pt-6">
                                        <button id="addMedicineBtn" class="px-4 py-2 bg-pharmacy-primary text-white rounded-lg hover:bg-pharmacy-secondary">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Daftar Obat yang Ditambahkan -->
                                <div id="medicineList" class="hidden">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="text-left p-3">Nama Obat</th>
                                                <th class="text-left p-3">Jumlah</th>
                                                <th class="text-left p-3">Aturan Pakai</th>
                                                <th class="text-left p-3">Subtotal</th>
                                                <th class="text-left p-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedMedicines">
                                            <!-- Data akan diisi oleh JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-between">
                            <button id="checkStockBtn" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                <i class="fas fa-search mr-2"></i> Cek Stok
                            </button>
                            <button id="createPrescriptionBtn" class="px-6 py-2 bg-clinic-primary text-white rounded-lg hover:bg-clinic-secondary">
                                <i class="fas fa-file-prescription mr-2"></i> Buat Resep Digital
                            </button>
                        </div>
                    </div>
                    
                    <!-- Manajemen Stok Apotek -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-clinic-dark">Manajemen Stok Apotek</h3>
                            <button id="refreshStockBtn" class="text-pharmacy-primary hover:text-pharmacy-secondary">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Stok Rendah -->
                            <div>
                                <h4 class="font-medium text-red-600 mb-2">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Stok Hampir Habis
                                </h4>
                                <div id="lowStockList" class="space-y-2">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Stok Cukup -->
                            <div>
                                <h4 class="font-medium text-green-600 mb-2">
                                    <i class="fas fa-check-circle mr-2"></i>Stok Tersedia
                                </h4>
                                <div id="availableStockList" class="space-y-2">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Peringatan -->
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                    <div>
                                        <p class="font-medium text-blue-800">Sinkronisasi Otomatis</p>
                                        <p class="text-sm text-blue-600">Stok apotek diperbarui otomatis saat resep diproses</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tombol Aksi -->
                            <div class="flex flex-wrap gap-2 mt-4">
                                <button id="orderStockBtn" class="flex-1 px-4 py-2 bg-pharmacy-primary text-white rounded-lg hover:bg-pharmacy-secondary text-sm">
                                    <i class="fas fa-truck mr-2"></i> Pesan Obat
                                </button>
                                <button id="inventoryReportBtn" class="flex-1 px-4 py-2 bg-clinic-accent text-white rounded-lg hover:bg-green-600 text-sm">
                                    <i class="fas fa-chart-bar mr-2"></i> Laporan Stok
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sistem Pembayaran Terintegrasi -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Tagihan Terintegrasi -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-clinic-dark mb-6">Sistem Pembayaran Terintegrasi</h3>
                        
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">Konsultasi Dokter</p>
                                        <p class="text-sm text-gray-600">Biaya pemeriksaan</p>
                                    </div>
                                    <p class="font-medium">Rp 150.000</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">Obat dari Resep</p>
                                        <p class="text-sm text-gray-600">Total biaya obat</p>
                                    </div>
                                    <p class="font-medium" id="medicineTotal">Rp 0</p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium">Diskon Asuransi</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <select id="insuranceSelect" class="border border-gray-300 rounded p-1 text-sm">
                                                <option value="0">Tidak ada asuransi</option>
                                                <option value="10">BPJS (10%)</option>
                                                <option value="20">Asuransi Swasta (20%)</option>
                                                <option value="30">Asuransi Perusahaan (30%)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <p class="font-medium text-green-600" id="insuranceDiscount">- Rp 0</p>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <p class="text-lg font-bold">Total Tagihan</p>
                                    <p class="text-2xl font-bold text-clinic-primary" id="totalBill">Rp 150.000</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <button id="printBillBtn" class="px-4 py-3 border border-clinic-primary text-clinic-primary rounded-lg hover:bg-blue-50">
                                    <i class="fas fa-print mr-2"></i> Cetak Tagihan
                                </button>
                                <button id="processPaymentBtn" class="px-4 py-3 bg-clinic-primary text-white rounded-lg hover:bg-clinic-secondary">
                                    <i class="fas fa-credit-card mr-2"></i> Proses Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monitoring Integrasi -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-clinic-dark mb-6">Monitoring Sistem Terintegrasi</h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">Status Integrasi</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-database text-clinic-primary"></i>
                                            <span>Database Pasien</span>
                                        </span>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Tersinkron</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-prescription-bottle text-pharmacy-primary"></i>
                                            <span>Sistem Resep</span>
                                        </span>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-pills text-clinic-accent"></i>
                                            <span>Stok Apotek</span>
                                        </span>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Real-time</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-file-invoice-dollar text-pharmacy-accent"></i>
                                            <span>Pembayaran</span>
                                        </span>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs">Terintegrasi</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-gray-700 mb-2">Aktivitas Terbaru</h4>
                                <div class="space-y-3">
                                    <div class="text-sm">
                                        <p class="font-medium">Resep diproses untuk Ahmad Fauzi</p>
                                        <p class="text-gray-600">3 obat, Rp 85.000 • 10:30</p>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-medium">Stok Paracetamol diperbarui</p>
                                        <p class="text-gray-600">Stok: 45 → 42 • 10:15</p>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-medium">Pembayaran Sinta Wulandari</p>
                                        <p class="text-gray-600">Rp 235.000 • 09:45</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-gradient-to-r from-clinic-primary to-pharmacy-primary rounded-lg text-white">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-sync-alt text-2xl"></i>
                                    <div>
                                        <p class="font-medium">Sistem Terintegrasi</p>
                                        <p class="text-sm opacity-90">Data mengalir otomatis antara klinik dan apotek</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Dashboard -->
                <div class="mt-8 p-4 bg-white rounded-xl shadow">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-clinic-primary" id="totalPatientsToday">0</p>
                                <p class="text-sm text-gray-600">Pasien Hari Ini</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-pharmacy-primary" id="totalPrescriptionsToday">0</p>
                                <p class="text-sm text-gray-600">Resep Diproses</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-clinic-accent" id="totalRevenueToday">Rp 0</p>
                                <p class="text-sm text-gray-600">Pendapatan</p>
                            </div>
                        </div>
                        <button id="systemResetBtn" class="mt-4 md:mt-0 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-redo mr-2"></i> Reset Data Demo
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Notifikasi Sistem</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent" class="space-y-3">
                <!-- Konten notifikasi akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Data Sistem Klinik-Apotek
        const systemData = {
            patients: [
                { id: 1, name: "Ahmad Fauzi", recordId: "RM-001", age: 35, allergy: "Paracetamol", condition: "Demam" },
                { id: 2, name: "Sinta Wulandari", recordId: "RM-002", age: 28, allergy: "Tidak ada", condition: "Flu" },
                { id: 3, name: "Bambang Sutrisno", recordId: "RM-003", age: 52, allergy: "Penicillin", condition: "Hipertensi" },
                { id: 4, name: "Dewi Anggraini", recordId: "RM-004", age: 45, allergy: "Ibuprofen", condition: "Sakit kepala" }
            ],
            
            medicines: [
                { id: 1, name: "Paracetamol 500mg", price: 5000, stock: 42, minStock: 20, category: "Analgesik" },
                { id: 2, name: "Amoxicillin 500mg", price: 12000, stock: 35, minStock: 15, category: "Antibiotik" },
                { id: 3, name: "Cetirizine 10mg", price: 8000, stock: 58, minStock: 25, category: "Antihistamin" },
                { id: 4, name: "Omeprazole 20mg", price: 15000, stock: 12, minStock: 10, category: "Antasida" },
                { id: 5, name: "Vitamin C 500mg", price: 3000, stock: 5, minStock: 20, category: "Vitamin" }
            ],
            
            doctors: [
                { id: 1, name: "Dr. Sari Dewi", specialty: "Sp. Jantung" },
                { id: 2, name: "Dr. Budi Santoso", specialty: "Sp. Anak" },
                { id: 3, name: "Dr. Rina Melati", specialty: "Sp. Kulit" }
            ],
            
            prescriptions: [],
            selectedMedicines: [],
            todayRevenue: 0,
            todayPatients: 0,
            todayPrescriptions: 0
        };

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboard();
            updateStockDisplay();
            setupEventListeners();
            updateTotals();
            
            // Simulasi data awal
            systemData.todayPatients = 18;
            systemData.todayPrescriptions = 24;
            systemData.todayRevenue = 6800000;
            updateTotals();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Pilih pasien
            document.getElementById('patientSelect').addEventListener('change', function() {
                const patientId = parseInt(this.value);
                const patient = systemData.patients.find(p => p.id === patientId);
                
                if (patient) {
                    document.getElementById('patientInfo').classList.remove('hidden');
                    document.getElementById('patientName').textContent = patient.name;
                    document.getElementById('patientId').textContent = patient.recordId;
                    document.getElementById('patientAge').textContent = `Umur: ${patient.age} tahun`;
                    document.getElementById('patientAllergy').textContent = `Alergi: ${patient.allergy}`;
                    document.getElementById('patientCondition').textContent = `Kondisi: ${patient.condition}`;
                } else {
                    document.getElementById('patientInfo').classList.add('hidden');
                }
            });

            // Tambah obat ke resep
            document.getElementById('addMedicineBtn').addEventListener('click', function() {
                const medicineId = parseInt(document.getElementById('medicineSelect').value);
                const quantity = parseInt(document.getElementById('medicineQty').value);
                const instruction = document.getElementById('medicineInstruction').value;
                
                if (!medicineId || !quantity) {
                    showNotification('Peringatan', 'Pilih obat dan masukkan jumlah', 'warning');
                    return;
                }
                
                const medicine = systemData.medicines.find(m => m.id === medicineId);
                if (!medicine) return;
                
                // Cek apakah obat sudah ada di list
                const existingIndex = systemData.selectedMedicines.findIndex(m => m.id === medicineId);
                
                if (existingIndex !== -1) {
                    // Update jumlah jika sudah ada
                    systemData.selectedMedicines[existingIndex].quantity += quantity;
                } else {
                    // Tambah baru
                    systemData.selectedMedicines.push({
                        id: medicine.id,
                        name: medicine.name,
                        price: medicine.price,
                        quantity: quantity,
                        instruction: instruction
                    });
                }
                
                // Update tampilan
                updateMedicineList();
                updateBill();
                
                // Reset form
                document.getElementById('medicineQty').value = 1;
                
                showNotification('Berhasil', `${medicine.name} ditambahkan ke resep`, 'success');
            });

            // Cek stok
            document.getElementById('checkStockBtn').addEventListener('click', function() {
                if (systemData.selectedMedicines.length === 0) {
                    showNotification('Informasi', 'Tambahkan obat terlebih dahulu', 'info');
                    return;
                }
                
                let allAvailable = true;
                let lowStockMeds = [];
                
                systemData.selectedMedicines.forEach(med => {
                    const stockMedicine = systemData.medicines.find(m => m.id === med.id);
                    if (stockMedicine) {
                        if (stockMedicine.stock < med.quantity) {
                            allAvailable = false;
                            lowStockMeds.push({
                                name: med.name,
                                needed: med.quantity,
                                available: stockMedicine.stock
                            });
                        } else if (stockMedicine.stock - med.quantity < stockMedicine.minStock) {
                            lowStockMeds.push({
                                name: med.name,
                                needed: med.quantity,
                                available: stockMedicine.stock,
                                warning: 'Akan mencapai stok minimum'
                            });
                        }
                    }
                });
                
                if (allAvailable) {
                    if (lowStockMeds.length > 0) {
                        let message = 'Stok tersedia, tetapi peringatan:\n';
                        lowStockMeds.forEach(med => {
                            message += `- ${med.name}: ${med.warning}\n`;
                        });
                        showNotification('Stok Tersedia', message, 'info');
                    } else {
                        showNotification('Stok Tersedia', 'Semua obat tersedia di apotek', 'success');
                    }
                } else {
                    let message = 'Stok tidak mencukupi:\n';
                    lowStockMeds.forEach(med => {
                        if (!med.warning) {
                            message += `- ${med.name}: Dibutuhkan ${med.needed}, tersedia ${med.available}\n`;
                        }
                    });
                    showNotification('Stok Tidak Cukup', message, 'error');
                }
            });

            // Buat resep digital
            document.getElementById('createPrescriptionBtn').addEventListener('click', function() {
                const patientId = parseInt(document.getElementById('patientSelect').value);
                const doctorId = parseInt(document.getElementById('doctorSelect').value);
                
                if (!patientId) {
                    showNotification('Peringatan', 'Pilih pasien terlebih dahulu', 'warning');
                    return;
                }
                
                if (!doctorId) {
                    showNotification('Peringatan', 'Pilih dokter penanggung jawab', 'warning');
                    return;
                }
                
                if (systemData.selectedMedicines.length === 0) {
                    showNotification('Peringatan', 'Tambahkan minimal satu obat ke resep', 'warning');
                    return;
                }
                
                // Kurangi stok
                systemData.selectedMedicines.forEach(selectedMed => {
                    const medicine = systemData.medicines.find(m => m.id === selectedMed.id);
                    if (medicine) {
                        medicine.stock -= selectedMed.quantity;
                    }
                });
                
                // Buat resep
                const prescription = {
                    id: systemData.prescriptions.length + 1,
                    patientId: patientId,
                    doctorId: doctorId,
                    medicines: [...systemData.selectedMedicines],
                    date: new Date().toLocaleString(),
                    status: 'Diproses'
                };
                
                systemData.prescriptions.push(prescription);
                
                // Update statistik
                systemData.todayPrescriptions++;
                updateTotals();
                updateStockDisplay();
                
                // Reset form
                systemData.selectedMedicines = [];
                updateMedicineList();
                document.getElementById('patientSelect').value = '';
                document.getElementById('doctorSelect').value = '';
                document.getElementById('patientInfo').classList.add('hidden');
                
                showNotification('Resep Berhasil', 'Resep digital telah dibuat dan stok apotek diperbarui', 'success');
            });

            // Proses pembayaran
            document.getElementById('processPaymentBtn').addEventListener('click', function() {
                const medicineTotal = calculateMedicineTotal();
                const consultationFee = 150000;
                const insuranceDiscount = parseInt(document.getElementById('insuranceSelect').value);
                
                let total = medicineTotal + consultationFee;
                let discount = total * (insuranceDiscount / 100);
                let finalTotal = total - discount;
                
                // Update revenue
                systemData.todayRevenue += finalTotal;
                systemData.todayPatients++;
                
                updateTotals();
                
                showNotification('Pembayaran Berhasil', 
                    `Total: Rp ${finalTotal.toLocaleString()}\nAsuransi: ${insuranceDiscount}%\nTerima kasih telah menggunakan layanan terintegrasi kami`, 
                    'success');
                
                // Reset
                document.getElementById('insuranceSelect').value = '0';
                updateBill();
            });

            // Notifikasi
            document.getElementById('notificationBtn').addEventListener('click', function() {
                showNotificationsModal();
            });

            document.getElementById('closeModalBtn').addEventListener('click', function() {
                document.getElementById('notificationModal').classList.add('hidden');
                document.getElementById('notificationBadge').textContent = '0';
            });

            // Refresh stok
            document.getElementById('refreshStockBtn').addEventListener('click', function() {
                updateStockDisplay();
                showNotification('Stok Diperbarui', 'Data stok telah diperbarui', 'info');
            });

            // Reset data demo
            document.getElementById('systemResetBtn').addEventListener('click', function() {
                // Reset data obat ke nilai awal
                systemData.medicines = [
                    { id: 1, name: "Paracetamol 500mg", price: 5000, stock: 42, minStock: 20, category: "Analgesik" },
                    { id: 2, name: "Amoxicillin 500mg", price: 12000, stock: 35, minStock: 15, category: "Antibiotik" },
                    { id: 3, name: "Cetirizine 10mg", price: 8000, stock: 58, minStock: 25, category: "Antihistamin" },
                    { id: 4, name: "Omeprazole 20mg", price: 15000, stock: 12, minStock: 10, category: "Antasida" },
                    { id: 5, name: "Vitamin C 500mg", price: 3000, stock: 5, minStock: 20, category: "Vitamin" }
                ];
                
                // Reset statistik
                systemData.todayPatients = 18;
                systemData.todayPrescriptions = 24;
                systemData.todayRevenue = 6800000;
                
                updateTotals();
                updateStockDisplay();
                showNotification('Sistem Direset', 'Data demo telah direset ke kondisi awal', 'info');
            });
        }

        // Update tampilan dashboard
        function updateDashboard() {
            // Update statistik
            const totalStock = systemData.medicines.reduce((sum, med) => sum + med.stock, 0);
            const lowStockCount = systemData.medicines.filter(med => med.stock < med.minStock).length;
            
            document.getElementById('totalStock').textContent = totalStock;
            document.getElementById('lowStock').textContent = lowStockCount;
            
            // Update dengan resep
            const withPrescription = Math.floor(systemData.todayPatients * 0.7);
            document.getElementById('withPrescription').textContent = withPrescription;
            
            // Update pending prescriptions
            const pendingPrescriptions = Math.max(0, systemData.todayPrescriptions - withPrescription);
            document.getElementById('pendingPrescriptions').textContent = pendingPrescriptions;
            
            // Update pharmacy revenue (30% dari total)
            const pharmacyRevenue = systemData.todayRevenue * 0.3;
            document.getElementById('pharmacyRevenue').textContent = `Rp ${(pharmacyRevenue / 1000000).toFixed(1)}jt`;
        }

        // Update tampilan stok
        function updateStockDisplay() {
            const lowStockList = document.getElementById('lowStockList');
            const availableStockList = document.getElementById('availableStockList');
            
            // Clear lists
            lowStockList.innerHTML = '';
            availableStockList.innerHTML = '';
            
            // Sort by stock level
            const sortedMedicines = [...systemData.medicines].sort((a, b) => a.stock - b.stock);
            
            sortedMedicines.forEach(medicine => {
                const stockItem = document.createElement('div');
                stockItem.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-lg';
                
                const stockInfo = document.createElement('div');
                stockInfo.innerHTML = `
                    <p class="font-medium">${medicine.name}</p>
                    <p class="text-sm text-gray-600">${medicine.category}</p>
                `;
                
                const stockLevel = document.createElement('div');
                stockLevel.className = 'text-right';
                
                let stockColor = 'text-green-600';
                let stockText = 'Cukup';
                
                if (medicine.stock < medicine.minStock) {
                    stockColor = 'text-red-600';
                    stockText = 'Hampir Habis';
                } else if (medicine.stock < medicine.minStock * 1.5) {
                    stockColor = 'text-yellow-600';
                    stockText = 'Perlu diisi';
                }
                
                stockLevel.innerHTML = `
                    <p class="font-medium ${stockColor}">${medicine.stock} ${stockText}</p>
                    <p class="text-sm text-gray-600">Min: ${medicine.minStock}</p>
                `;
                
                stockItem.appendChild(stockInfo);
                stockItem.appendChild(stockLevel);
                
                if (medicine.stock < medicine.minStock) {
                    lowStockList.appendChild(stockItem);
                } else {
                    availableStockList.appendChild(stockItem);
                }
            });
            
            // Update dashboard
            updateDashboard();
        }

        // Update daftar obat yang dipilih
        function updateMedicineList() {
            const container = document.getElementById('selectedMedicines');
            const medicineListDiv = document.getElementById('medicineList');
            
            if (systemData.selectedMedicines.length === 0) {
                medicineListDiv.classList.add('hidden');
                container.innerHTML = '';
                return;
            }
            
            medicineListDiv.classList.remove('hidden');
            
            let html = '';
            systemData.selectedMedicines.forEach((med, index) => {
                const subtotal = med.price * med.quantity;
                html += `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">${med.name}</td>
                        <td class="p-3">${med.quantity}</td>
                        <td class="p-3">${med.instruction}</td>
                        <td class="p-3">Rp ${subtotal.toLocaleString()}</td>
                        <td class="p-3">
                            <button onclick="removeMedicine(${index})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            container.innerHTML = html;
        }

        // Hapus obat dari daftar
        window.removeMedicine = function(index) {
            systemData.selectedMedicines.splice(index, 1);
            updateMedicineList();
            updateBill();
        };

        // Update tagihan
        function updateBill() {
            const medicineTotal = calculateMedicineTotal();
            const consultationFee = 150000;
            const insuranceDiscount = parseInt(document.getElementById('insuranceSelect').value);
            
            document.getElementById('medicineTotal').textContent = `Rp ${medicineTotal.toLocaleString()}`;
            
            let total = medicineTotal + consultationFee;
            let discount = total * (insuranceDiscount / 100);
            let finalTotal = total - discount;
            
            document.getElementById('insuranceDiscount').textContent = `- Rp ${discount.toLocaleString()}`;
            document.getElementById('totalBill').textContent = `Rp ${finalTotal.toLocaleString()}`;
        }

        // Hitung total obat
        function calculateMedicineTotal() {
            return systemData.selectedMedicines.reduce((total, med) => {
                return total + (med.price * med.quantity);
            }, 0);
        }

        // Update total statistik
        function updateTotals() {
            document.getElementById('totalPatientsToday').textContent = systemData.todayPatients;
            document.getElementById('totalPrescriptionsToday').textContent = systemData.todayPrescriptions;
            document.getElementById('totalRevenueToday').textContent = `Rp ${(systemData.todayRevenue / 1000000).toFixed(1)}jt`;
            
            document.getElementById('todayPatients').textContent = systemData.todayPatients;
            document.getElementById('processedPrescriptions').textContent = systemData.todayPrescriptions;
            document.getElementById('todayRevenue').textContent = (systemData.todayRevenue / 1000000).toFixed(1);
            
            updateDashboard();
        }

        // Tampilkan notifikasi
        function showNotification(title, message, type = 'info') {
            // Update badge
            const badge = document.getElementById('notificationBadge');
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
            
            // Warna berdasarkan tipe
            let bgColor = 'bg-blue-100';
            let textColor = 'text-blue-800';
            let icon = 'fa-info-circle';
            
            if (type === 'success') {
                bgColor = 'bg-green-100';
                textColor = 'text-green-800';
                icon = 'fa-check-circle';
            } else if (type === 'warning') {
                bgColor = 'bg-yellow-100';
                textColor = 'text-yellow-800';
                icon = 'fa-exclamation-triangle';
            } else if (type === 'error') {
                bgColor = 'bg-red-100';
                textColor = 'text-red-800';
                icon = 'fa-times-circle';
            }
            
            // Tambahkan notifikasi ke modal
            const modalContent = document.getElementById('modalContent');
            const notification = document.createElement('div');
            notification.className = `p-3 ${bgColor} ${textColor} rounded-lg`;
            notification.innerHTML = `
                <div class="flex items-start gap-3">
                    <i class="fas ${icon} mt-1"></i>
                    <div>
                        <p class="font-medium">${title}</p>
                        <p class="text-sm mt-1">${message}</p>
                        <p class="text-xs opacity-75 mt-2">${new Date().toLocaleTimeString()}</p>
                    </div>
                </div>
            `;
            
            // Tambahkan di atas
            modalContent.insertBefore(notification, modalContent.firstChild);
            
            // Tampilkan toast notifikasi (opsional)
            showToast(title, message, type);
        }

        // Tampilkan toast notifikasi
        function showToast(title, message, type) {
            // Buat elemen toast
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
            
            // Warna berdasarkan tipe
            if (type === 'success') {
                toast.classList.add('bg-green-500', 'text-white');
            } else if (type === 'warning') {
                toast.classList.add('bg-yellow-500', 'text-white');
            } else if (type === 'error') {
                toast.classList.add('bg-red-500', 'text-white');
            } else {
                toast.classList.add('bg-blue-500', 'text-white');
            }
            
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
                    <div>
                        <p class="font-medium">${title}</p>
                        <p class="text-sm opacity-90">${message}</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animasi masuk
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 10);
            
            // Animasi keluar setelah 5 detik
            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                
                // Hapus elemen setelah animasi selesai
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 5000);
        }

        // Tampilkan modal notifikasi
        function showNotificationsModal() {
            document.getElementById('notificationModal').classList.remove('hidden');
        }

        // Event listener untuk select asuransi
        document.getElementById('insuranceSelect').addEventListener('change', updateBill);
    </script>
</body>
</html>