@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview')

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
@endpush

@push('styles')
<style>
    /* ============================================================
       KPI CARDS
    ============================================================ */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
      margin-bottom: 16px;
    }

    .kpi-card {
      background: var(--putih);
      border-radius: var(--radius);
      padding: 20px 22px;
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      transition: transform .2s, box-shadow .2s;
      animation: riseUp .4s ease both;
    }

    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(15,33,68,.12); }
    .kpi-card:nth-child(2) { animation-delay: .07s; }
    .kpi-card:nth-child(3) { animation-delay: .14s; }

    .kpi-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
    }
    .kpi-card.biru::before   { background: linear-gradient(90deg,#2563eb,#6366f1); }
    .kpi-card.hijau::before  { background: linear-gradient(90deg,#10b981,#06b6d4); }
    .kpi-card.kuning::before { background: linear-gradient(90deg,#f59e0b,#ef4444); }

    .kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }

    .kpi-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .kpi-icon.bg-biru   { background: #eff6ff; }
    .kpi-icon.bg-hijau  { background: #ecfdf5; }
    .kpi-icon.bg-kuning { background: #fffbeb; }

    .badge { font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 99px; }
    .badge.naik { background: #ecfdf5; color: var(--hijau); }
    .badge.diam { background: #f1f5f9; color: var(--terang); border: 1px solid var(--border); }

    .kpi-label { font-size: 10px; font-weight: 700; color: var(--terang); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }

    .kpi-angka { font-family: 'Sora', sans-serif; font-size: 34px; font-weight: 800; color: var(--teks); letter-spacing: -1.5px; line-height: 1; margin-bottom: 14px; }

    .kpi-pills { display: flex; gap: 7px; flex-wrap: wrap; }

    .pill { font-size: 11.5px; padding: 4px 10px; border-radius: 99px; border: 1px solid var(--border); color: var(--abu); background: #f8faff; }
    .pill strong { font-weight: 700; color: var(--teks); }

    @keyframes riseUp {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ============================================================
       GRID 2 KOLOM
    ============================================================ */
    .dua-kolom { display: grid; grid-template-columns: 1.2fr .8fr; gap: 14px; margin-bottom: 14px; align-items: stretch; }
    .card-penuh { display: flex; flex-direction: column; }
    .card-penuh .card-body { flex: 1; display: flex; flex-direction: column; }
    .card-penuh .penyakit-list { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }

    /* ============================================================
       KARTU GENERIK
    ============================================================ */
    .card { background: var(--putih); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; animation: riseUp .4s ease both; transition: box-shadow .2s; }
    .card:hover { box-shadow: 0 12px 32px rgba(15,33,68,.1); }
    .card-header { padding: 18px 20px 0; display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
    .card-judul { font-size: 13.5px; font-weight: 700; color: var(--teks); margin-bottom: 2px; }
    .card-sub   { font-size: 11px; color: var(--terang); }
    .card-body  { padding: 14px 20px 20px; }

    .btn-lihat { font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 99px; background: #eff6ff; border: 1px solid #dbeafe; color: var(--biru); cursor: pointer; white-space: nowrap; flex-shrink: 0; transition: background .15s; }
    .btn-lihat:hover { background: #dbeafe; }

    /* ============================================================
       TOP 10 PENYAKIT
    ============================================================ */
    .penyakit-list { list-style: none; }
    .penyakit-item { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f0f4fb; }
    .penyakit-item:last-child { border: none; }
    .urut { width: 26px; height: 26px; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }
    .urut.top { background: var(--navy); color: #fff; }
    .urut.biasa { background: #f1f5f9; color: var(--abu); border: 1px solid var(--border); }
    .penyakit-nama { flex: 1; font-size: 12.5px; font-weight: 500; color: var(--teks); line-height: 1.3; }
    .bar-wrap { width: 52px; height: 5px; background: #f1f5f9; border-radius: 99px; flex-shrink: 0; }
    .bar-fill { height: 5px; border-radius: 99px; background: linear-gradient(90deg,var(--biru),#6366f1); }
    .penyakit-angka { font-size: 12.5px; font-weight: 700; color: var(--teks); min-width: 46px; text-align: right; }

    /* ============================================================
       GRAFIK
    ============================================================ */
    .grafik-kecil { position: relative; height: 150px; }
    .grafik-besar { position: relative; height: 220px; }

    /* ============================================================
       KEPUASAN — legenda
    ============================================================ */
    .kepuasan-legenda { display: flex; flex-wrap: wrap; gap: 5px 10px; margin-bottom: 10px; }
    .leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--abu); }
    .leg-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ============================================================
       GENDER
    ============================================================ */
    .gender-row { display: flex; gap: 9px; margin-bottom: 11px; }
    .gender-box { flex: 1; padding: 11px 13px; border-radius: 11px; border: 1px solid var(--border); display: flex; align-items: center; gap: 9px; }
    .gender-icon { width: 33px; height: 33px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .gender-label { font-size: 10px; color: var(--terang); font-weight: 500; }
    .gender-angka { font-family: 'Sora',sans-serif; font-size: 18px; font-weight: 800; color: var(--teks); }
    .gender-pct   { font-size: 10px; font-weight: 700; }
    .gender-bar-wrap { height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin-bottom: 12px; }
    .gender-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--biru) 41.3%, var(--ungu) 41.3%); }

    /* ============================================================
       DATE PICKER
    ============================================================ */
    .date-btn { display: flex; align-items: center; gap: 7px; background: #eff6ff; border: 1px solid #dbeafe; padding: 7px 14px; border-radius: 99px; font-size: 12.5px; font-weight: 700; color: var(--biru); cursor: pointer; white-space: nowrap; transition: background .15s; user-select: none; }
    .date-btn:hover { background: #dbeafe; }
    .date-btn svg { width: 13px; height: 13px; flex-shrink: 0; transition: transform .2s; }
    .date-btn.open svg.chevron { transform: rotate(180deg); }
    .date-picker { position: relative; }
    .date-drop { position: absolute; top: calc(100% + 8px); right: 0; width: 260px; background: var(--putih); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: 0 16px 48px rgba(15,33,68,.14); padding: 16px; z-index: 600; display: none; }
    .date-drop.open { display: block; animation: fadeDown .18s ease; }
    @keyframes fadeDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .dd-tahun-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .dd-tahun-angka { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 16px; color: var(--teks); }
    .dd-tahun-btn { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border); background: var(--bg); color: var(--abu); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .14s; }
    .dd-tahun-btn:hover { background: #eff6ff; color: var(--biru); border-color: #bfdbfe; }
    .dd-bulan-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 5px; }
    .dd-bulan-item { padding: 7px 2px; border-radius: 8px; font-size: 12px; font-weight: 500; color: var(--abu); text-align: center; cursor: pointer; transition: all .14s; border: 1px solid transparent; position: relative; }
    .dd-bulan-item:hover { background: #eff6ff; color: var(--biru); }
    .dd-bulan-item.aktif { background: var(--biru); color: #fff; font-weight: 700; }
    .dd-bulan-item.ada-data::after { content: ''; position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); width: 4px; height: 4px; border-radius: 50%; background: var(--hijau); }
    .dd-bulan-item.aktif::after { background: rgba(255,255,255,.7); }

    /* ============================================================
       RESPONSIVE
    ============================================================ */
    @media (max-width: 1100px) { .dua-kolom { grid-template-columns: 1fr; } }
    @media (max-width: 900px) { .kpi-grid { grid-template-columns: 1fr 1fr; } .kpi-grid .kpi-card:nth-child(3) { grid-column: 1/-1; } }
    @media (max-width: 700px) { .kpi-grid { grid-template-columns: 1fr; } .kpi-grid .kpi-card:nth-child(3) { grid-column: auto; } .date-drop { right: -10px; width: 240px; } }
    @media (max-width: 480px) { .gender-row { flex-direction: column; } }
    @media (max-width: 480px) { .gender-row { flex-direction: column; } }
</style>
@endpush

@section('content')

  <!-- Date Picker di topbar (inject lewat JS, atau taruh di atas KPI) -->
  <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <div class="date-picker" id="datePicker">
      <div class="date-btn" id="dateBtn">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span id="labelBulan">{{ ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$month] }} {{ $year }}</span>
        <svg class="chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <polyline points="6,9 12,15 18,9"/>
        </svg>
      </div>

      <div class="date-drop" id="dateDrop">
        <div class="dd-tahun-row">
          <button class="dd-tahun-btn" id="btnTahunPrev">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15,18 9,12 15,6"/></svg>
          </button>
          <span class="dd-tahun-angka" id="tahunLabel">{{ $year }}</span>
          <button class="dd-tahun-btn" id="btnTahunNext">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="9,18 15,12 9,6"/></svg>
          </button>
        </div>
        <div class="dd-bulan-grid" id="bulanGrid"></div>
      </div>
    </div>
  </div>

  <!-- 3 KPI Cards -->
  <div class="kpi-grid">

    <div class="kpi-card biru">
      <div class="kpi-top">
        <div class="kpi-icon bg-biru">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9,22 9,12 15,12 15,22"/>
          </svg>
        </div>
        <span class="badge naik">↑ 8.2%</span>
      </div>
      <div class="kpi-label">Total Tahun Ini</div>
      <div class="kpi-angka" id="kpiTotalTahun">{{ number_format($totalTahun) }}</div>
      <div class="kpi-pills">
        <div class="pill">♂ <strong id="kpiLakiTahun">{{ number_format($lakiTahun) }}</strong></div>
        <div class="pill">♀ <strong id="kpiPerempuanTahun">{{ number_format($perempuanTahun) }}</strong></div>
      </div>
    </div>

    <div class="kpi-card hijau">
      <div class="kpi-top">
        <div class="kpi-icon bg-hijau">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="1.8">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <span class="badge naik" id="badgeBulan">↑ 4.1%</span>
      </div>
      <div class="kpi-label" id="labelBulanKPI">Februari</div>
      <div class="kpi-angka" id="kpiBulan">404</div>
      <div class="kpi-pills">
        <div class="pill">♂ <strong id="kpiLakiBulan">153</strong></div>
        <div class="pill">♀ <strong id="kpiPerempuanBulan">251</strong></div>
      </div>
    </div>

    <div class="kpi-card kuning">
      <div class="kpi-top">
        <div class="kpi-icon bg-kuning">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.8">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12,6 12,12 16,14"/>
          </svg>
        </div>
        <span class="badge diam">Minggu ini</span>
      </div>
      <div class="kpi-label">Minggu Ini</div>
      <div class="kpi-angka">0</div>
      <div class="kpi-pills">
        <div class="pill">♂ <strong>0</strong></div>
        <div class="pill">♀ <strong>0</strong></div>
      </div>
    </div>

  </div>

  <!-- Grid 2 kolom: penyakit + chart -->
  <div class="dua-kolom">

    <!-- Top 10 Penyakit -->
    <div class="card card-penuh" style="animation-delay:.1s">
      <div class="card-header">
        <div>
          <div class="card-judul">Top 10 Penyakit</div>
          <div class="card-sub">Berdasarkan jumlah kunjungan</div>
        </div>
        <span class="btn-lihat">Lihat Semua</span>
      </div>
      <div class="card-body">
        <ul class="penyakit-list" id="penyakitList"></ul>
      </div>
    </div>

    <!-- Kolom kanan: Kepuasan + Gender -->
    <div style="display:flex; flex-direction:column; gap:14px;">

      <!-- Kepuasan Pasien -->
      <div class="card" style="animation-delay:.16s">
        <div class="card-header">
          <div>
            <div class="card-judul">Kepuasan Pasien</div>
            <div class="card-sub">Rating dari pasien</div>
          </div>
        </div>
        <div class="card-body">
          <div class="kepuasan-legenda" id="kepuasanLeg"></div>
          <div class="grafik-kecil"><canvas id="grafikKepuasan"></canvas></div>
        </div>
      </div>

      <!-- Distribusi Gender -->
      <div class="card" style="animation-delay:.22s">
        <div class="card-header">
          <div>
            <div class="card-judul">Distribusi Gender</div>
            <div class="card-sub">Total terdaftar ({{ $year }})</div>
          </div>
        </div>
        <div class="card-body">
          <div class="gender-row">
            <div class="gender-box">
              <div class="gender-icon" style="background:#eff6ff">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2.2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <div>
                <div class="gender-label">Laki-laki</div>
                <div class="gender-angka">{{ number_format($lakiTotal) }}</div>
                <div class="gender-pct" style="color:#2563eb">{{ $lakiPct }}%</div>
              </div>
            </div>
            <div class="gender-box">
              <div class="gender-icon" style="background:#f5f3ff">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2.2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <div>
                <div class="gender-label">Perempuan</div>
                <div class="gender-angka" style="color:#7c3aed">{{ number_format($perempuanTotal) }}</div>
                <div class="gender-pct" style="color:#7c3aed">{{ $perempuanPct }}%</div>
              </div>
            </div>
          </div>
          <div class="gender-bar-wrap">
            <div class="gender-bar-fill" style="width:100%; background: linear-gradient(90deg, #2563eb {{ $lakiPct }}%, #7c3aed {{ $lakiPct }}%)"></div>
          </div>
          <div class="grafik-kecil"><canvas id="grafikGender"></canvas></div>
        </div>
      </div>

    </div>
  </div>

  <!-- Grafik Bulanan -->
  <div class="card" style="animation-delay:.28s; margin-bottom:16px">
    <div class="card-header">
      <div>
        <div class="card-judul">Grafik Penanganan per Bulan — <span id="labelTahunGrafik">{{ $year }}</span></div>
        <div class="card-sub" id="labelSubGrafik">Data kumulatif s.d. Februari 2025</div>
      </div>
    </div>
    <div class="card-body">
      <div class="grafik-besar"><canvas id="grafikBulanan"></canvas></div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    // ==============================================================
    // 1. DATE PICKER
    // ==============================================================
    var BULAN_PANJANG = ['Januari','Februari','Maret','April','Mei','Juni',
                         'Juli','Agustus','September','Oktober','November','Desember'];
    var BULAN_PENDEK  = ['Jan','Feb','Mar','Apr','Mei','Jun',
                         'Jul','Agt','Sep','Okt','Nov','Des'];

    var bulanAdaData   = @json($bulanAdaData);
    var bulanTerpilih  = {{ $month }};
    var tahunTerpilih  = {{ $year }};
    var tahunDropdown  = {{ $year }};
    var tahunList      = @json($tahunList);

    var dateBtn    = document.getElementById('dateBtn');
    var dateDrop   = document.getElementById('dateDrop');
    var labelBulan = document.getElementById('labelBulan');
    var tahunLabel = document.getElementById('tahunLabel');
    var bulanGrid  = document.getElementById('bulanGrid');

    function renderBulanGrid() {
      bulanGrid.innerHTML = '';
      tahunLabel.textContent = tahunDropdown;
      BULAN_PENDEK.forEach(function (nama, i) {
        var el = document.createElement('div');
        el.className = 'dd-bulan-item';
        if (i === bulanTerpilih && tahunDropdown === tahunTerpilih) el.classList.add('aktif');
        if (bulanAdaData.includes(i)) el.classList.add('ada-data');
        el.textContent = nama;
        el.addEventListener('click', function () {
          bulanTerpilih = i;
          tahunTerpilih = tahunDropdown;
          labelBulan.textContent = BULAN_PANJANG[i] + ' ' + tahunDropdown;
          tutupDrop();
          renderBulanGrid();
          updateDashboard();
        });
        bulanGrid.appendChild(el);
      });
    }

    function bukaDrop()  { dateDrop.classList.add('open'); dateBtn.classList.add('open'); renderBulanGrid(); }
    function tutupDrop() { dateDrop.classList.remove('open'); dateBtn.classList.remove('open'); }

    dateBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      dateDrop.classList.contains('open') ? tutupDrop() : bukaDrop();
    });
    document.addEventListener('click', tutupDrop);
    dateDrop.addEventListener('click', function (e) { e.stopPropagation(); });
    document.getElementById('btnTahunPrev').addEventListener('click', function () { tahunDropdown--; renderBulanGrid(); });
    document.getElementById('btnTahunNext').addEventListener('click', function () { tahunDropdown++; renderBulanGrid(); });

    // ==============================================================
    // 2. DATA KUNJUNGAN
    // ==============================================================
    var dataLaki      = @json($dataLaki);
    var dataPerempuan = @json($dataPerempuan);

    // ==============================================================
    // 3. TOP 10 PENYAKIT
    // ==============================================================
    var dataPenyakit = @json($topPenyakit);

    var maxPenyakit = dataPenyakit.length > 0 ? dataPenyakit[0].n : 1;
    var elList = document.getElementById('penyakitList');
    dataPenyakit.forEach(function (p, i) {
      var nomor = i + 1;
      var bar   = ((p.n / maxPenyakit) * 100).toFixed(1) + '%';
      var kelas = nomor <= 3 ? 'top' : 'biasa';
      var li = document.createElement('li');
      li.className = 'penyakit-item';
      li.innerHTML =
        '<div class="urut ' + kelas + '">' + nomor + '</div>' +
        '<div class="penyakit-nama">' + p.nama + '</div>' +
        '<div class="bar-wrap"><div class="bar-fill" style="width:' + bar + '"></div></div>' +
        '<div class="penyakit-angka">' + p.n.toLocaleString('id') + '</div>';
      elList.appendChild(li);
    });

    // ==============================================================
    // 4. GRAFIK KEPUASAN (Donut)
    // ==============================================================
    var kepuasan = {
      labels: ['Sangat Puas', 'Puas', 'Cukup', 'Buruk', 'Sangat Buruk'],
      data:   [78, 14, 5, 2, 1],
      warna:  ['#0f2144', '#2563eb', '#60a5fa', '#f59e0b', '#ef4444']
    };
    var legEl = document.getElementById('kepuasanLeg');
    kepuasan.labels.forEach(function (teks, i) {
      var d = document.createElement('div');
      d.className = 'leg-item';
      d.innerHTML = '<span class="leg-dot" style="background:' + kepuasan.warna[i] + '"></span>' + teks + ' (' + kepuasan.data[i] + '%)';
      legEl.appendChild(d);
    });

    Chart.defaults.font.family = 'Inter';
    Chart.defaults.color = '#94a3b8';

    new Chart(document.getElementById('grafikKepuasan'), {
      type: 'doughnut',
      data: { labels: kepuasan.labels, datasets: [{ data: kepuasan.data, backgroundColor: kepuasan.warna, borderWidth: 3, borderColor: '#fff' }] },
      options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (c) { return ' ' + c.label + ': ' + c.parsed + '%'; } } } } }
    });

    // ==============================================================
    // 5. GRAFIK GENDER (Donut)
    // ==============================================================
    new Chart(document.getElementById('grafikGender'), {
      type: 'doughnut',
      data: { labels: ['Laki-laki', 'Perempuan'], datasets: [{ data: [{{ $lakiTotal }}, {{ $perempuanTotal }}], backgroundColor: ['#2563eb', '#7c3aed'], borderWidth: 3, borderColor: '#fff' }] },
      options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } }
    });

    // ==============================================================
    // 6. GRAFIK BULANAN (Bar Chart)
    // ==============================================================
    var grafikBulanan = new Chart(document.getElementById('grafikBulanan'), {
      type: 'bar',
      data: {
        labels: BULAN_PENDEK,
        datasets: [
          { label: 'Laki-laki', data: dataLaki.slice(), backgroundColor: dataLaki.map(function () { return 'rgba(37,99,235,.7)'; }), borderRadius: 6, borderSkipped: false },
          { label: 'Perempuan', data: dataPerempuan.slice(), backgroundColor: dataPerempuan.map(function () { return 'rgba(124,58,237,.7)'; }), borderRadius: 6, borderSkipped: false }
        ]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { font: { family: 'Inter', size: 12, weight: '600' }, boxWidth: 9, usePointStyle: true, pointStyle: 'circle', padding: 16 } }, tooltip: { mode: 'index', intersect: false } },
        scales: { x: { stacked: true, grid: { display: false }, border: { display: false } }, y: { stacked: true, grid: { color: '#f0f4fb' }, border: { display: false } } }
      }
    });

    // ==============================================================
    // 7. UPDATE DASHBOARD saat bulan berubah
    // ==============================================================
    function fmt(n) { return n.toLocaleString('id'); }

    function updateDashboard() {
      var totalL = 0, totalP = 0;
      for (var i = 0; i <= bulanTerpilih; i++) { totalL += dataLaki[i]; totalP += dataPerempuan[i]; }
      var total = totalL + totalP;
      var lakiBln      = dataLaki[bulanTerpilih];
      var perempuanBln = dataPerempuan[bulanTerpilih];

      document.getElementById('kpiTotalTahun').textContent    = fmt(total);
      document.getElementById('kpiLakiTahun').textContent     = fmt(totalL);
      document.getElementById('kpiPerempuanTahun').textContent = fmt(totalP);
      document.getElementById('labelBulanKPI').textContent    = BULAN_PANJANG[bulanTerpilih];
      document.getElementById('kpiBulan').textContent         = fmt(lakiBln + perempuanBln);
      document.getElementById('kpiLakiBulan').textContent     = fmt(lakiBln);
      document.getElementById('kpiPerempuanBulan').textContent = fmt(perempuanBln);
      document.getElementById('labelTahunGrafik').textContent = tahunTerpilih;
      document.getElementById('labelSubGrafik').textContent   = 'Data kumulatif s.d. ' + BULAN_PANJANG[bulanTerpilih] + ' ' + tahunTerpilih;

      var wL = [], wP = [];
      for (var k = 0; k < 12; k++) {
        if (k === bulanTerpilih)    { wL.push('#2563eb');              wP.push('#7c3aed'); }
        else if (k < bulanTerpilih) { wL.push('rgba(37,99,235,.35)'); wP.push('rgba(124,58,237,.35)'); }
        else                        { wL.push('rgba(37,99,235,.1)');  wP.push('rgba(124,58,237,.1)'); }
      }
      grafikBulanan.data.datasets[0].backgroundColor = wL;
      grafikBulanan.data.datasets[1].backgroundColor = wP;
      grafikBulanan.update('none');
    }

    // ==============================================================
    // 8. INISIALISASI
    // ==============================================================
    renderBulanGrid();
    updateDashboard();
  </script>
@endpush