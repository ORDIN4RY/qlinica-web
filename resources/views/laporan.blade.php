<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan - Sahaduta Klinik</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root { --dark:#0f1729; --accent:#4f6ef7; --txt:#1a2035; --sub:#6b7a99; --bg:#f0f2f8; --card:#fff; --border:#e4e8f0; --red:#e05252; --green:#38c9a0; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); display: flex; font-size: 12px; min-height: 100vh; }

/* SIDEBAR */
.sidebar { width: 200px; min-height: 100vh; background: var(--dark); position: fixed; top:0; left:0; bottom:0; overflow-y:auto; }
.logo { display: flex; align-items: center; gap: 8px; padding: 14px 12px; border-bottom: 1px solid rgba(255,255,255,.06); }
.logo-icon { width: 28px; height: 28px; background: var(--accent); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 13px; flex-shrink:0; }
.logo h2 { color: #fff; font-size: 11.5px; font-weight: 700; line-height:1.2; }
.logo span { color: rgba(255,255,255,.4); font-size: 9px; }
.user-sb { margin: 8px; background: rgba(255,255,255,.06); border-radius: 8px; padding: 8px 10px; display: flex; align-items: center; gap: 7px; }
.avatar { width: 26px; height: 26px; background: var(--accent); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 11px; flex-shrink:0; }
.user-sb .welcome { font-size: 8px; color: rgba(255,255,255,.4); text-transform: uppercase; }
.user-sb .name { font-size: 11px; color: #fff; font-weight: 600; }
.nav-label { font-size: 8px; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: .8px; padding: 10px 12px 3px; }
.nav-item { display: flex; align-items: center; gap: 7px; padding: 8px 12px; color: rgba(255,255,255,.5); font-size: 11.5px; font-weight: 500; cursor: pointer; transition: all .15s; }
.nav-item:hover { background: rgba(255,255,255,.05); color: rgba(255,255,255,.8); }
.nav-item.active, .nav-item.active-parent { background: var(--accent); color: #fff; border-radius: 0 14px 14px 0; margin-right: 10px; }
.nav-item svg { width: 13px; height: 13px; flex-shrink:0; }
.badge { margin-left: auto; background: var(--red); color: #fff; font-size: 8px; font-weight: 700; padding: 1px 5px; border-radius: 20px; }
.sub-nav { background: rgba(255,255,255,.03); }
.sub-nav .nav-item { padding-left: 28px; font-size: 11px; }
.sub-nav .nav-item.active { background: rgba(79,110,247,.4); border-radius: 0 14px 14px 0; margin-right: 10px; }
.chevron { margin-left: auto; transition: transform .2s; }

/* MAIN */
.main { margin-left: 200px; flex: 1; }
.topbar { background: #fff; border-bottom: 1px solid var(--border); padding: 10px 18px; display: flex; align-items: center; justify-content: space-between; }
.topbar h1 { font-size: 14px; font-weight: 700; }
.topbar .bc { font-size: 10px; color: var(--sub); }
.topbar .bc span { color: var(--accent); font-weight: 600; }

.content { padding: 14px 18px; }

/* FILTER */
.filter-card { background: var(--card); border-radius: 10px; padding: 14px 18px; margin-bottom: 14px; box-shadow: 0 2px 6px rgba(0,0,0,.04); }
.filter-card h3 { font-size: 13px; font-weight: 700; margin-bottom: 12px; color: var(--txt); }
.filter-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.filter-group { display: flex; align-items: center; gap: 7px; }
.filter-group label { font-size: 11.5px; font-weight: 600; color: var(--sub); white-space: nowrap; }
.filter-group input[type="date"] { border: 1.5px solid var(--border); border-radius: 7px; padding: 6px 10px; font-size: 11.5px; font-family: inherit; color: var(--txt); outline: none; background: var(--bg); }
.filter-group input[type="date"]:focus { border-color: var(--accent); background: #fff; }
.btn-cari { background: var(--accent); color: #fff; border: none; padding: 7px 18px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-cari:hover { background: #3a58e0; }

/* ACTION BAR */
.action-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; flex-wrap: wrap; gap: 8px; }
.action-left { display: flex; align-items: center; gap: 6px; }
.btn-action { border: 1px solid var(--border); background: #fff; color: var(--txt); padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-action:hover { background: var(--bg); }
.show { display: flex; align-items: center; gap: 5px; color: var(--sub); }
.show select { border: 1px solid var(--border); border-radius: 5px; padding: 4px 6px; font-size: 11px; font-family: inherit; outline: none; }
.search { display: flex; align-items: center; gap: 5px; background: var(--bg); border: 1px solid var(--border); border-radius: 7px; padding: 6px 10px; }
.search input { border: none; background: transparent; font-size: 11.5px; font-family: inherit; outline: none; width: 150px; color: var(--txt); }

/* TABLE */
.table-wrap { background: var(--card); border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.04); overflow-x: auto; }
table { width: 100%; border-collapse: collapse; min-width: 1000px; }
thead tr { background: #f7f8fc; border-bottom: 2px solid var(--border); }
th { padding: 9px 10px; text-align: left; font-size: 10px; font-weight: 700; color: var(--accent); text-transform: none; white-space: nowrap; border-right: 1px solid var(--border); }
th:last-child { border-right: none; }
td { padding: 9px 10px; color: var(--txt); border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); vertical-align: top; font-size: 11px; line-height: 1.5; }
td:last-child { border-right: none; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #f9fbff; }
tbody tr:nth-child(even) td { background: #fafbff; }
tbody tr:nth-child(even):hover td { background: #f0f4ff; }

.status-badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 10px; font-weight: 600; }
.status-lama { background: #e6f9f4; color: #1a9e7a; }
.status-baru { background: #e8ecff; color: var(--accent); }

.tfoot { padding: 9px 14px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f7f8fc; }
.tfoot span { font-size: 11px; color: var(--sub); }
.pgn { display: flex; gap: 4px; }
.pgn button { padding: 4px 10px; border-radius: 5px; font-size: 11px; font-family: inherit; font-weight: 600; cursor: pointer; border: 1px solid var(--border); background: #fff; color: var(--sub); }
.pgn button:not(:disabled):hover, .pgn button.active-pg { background: var(--accent); color: #fff; border-color: var(--accent); }
.pgn button:disabled { opacity: .4; cursor: not-allowed; }

.empty-row { text-align: center; padding: 40px; color: var(--sub); font-size: 12px; }
</style>
</head>
<body>

<aside class="sidebar">
  <div class="logo">
    <div class="logo-icon">S</div>
    <div><h2>Sahaduta Klinik</h2><span>Management System</span></div>
  </div>
  <div class="user-sb">
    <div class="avatar">P</div>
    <div><div class="welcome">Welcome Back</div><div class="name">Prodevis Team</div></div>
  </div>
  <div class="nav-label">Menu Utama</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Pasien <span class="badge">12</span></div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>Pemesanan</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Pegawai</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Komentar</div>
  <div class="nav-label">Laporan</div>
  <div class="nav-item active-parent"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>Laporan <span class="chevron">▾</span></div>
  <div class="sub-nav">
    <div class="nav-item active">Laporan Semua</div>
    <div class="nav-item">Laporan Harian</div>
    <div class="nav-item">Laporan Mingguan</div>
    <div class="nav-item">Laporan Bulanan</div>
    <div class="nav-item">Laporan Tahunan</div>
  </div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>Laporan Penanganan <span class="chevron">▾</span></div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10z"/></svg>ICD X</div>
</aside>

<main class="main">
  <div class="topbar">
    <div>
      <h1>Laporan Kunjungan</h1>
      <div class="bc">Sahaduta Klinik › Laporan › <span>Laporan Semua</span></div>
    </div>
    <span style="font-size:12px;font-weight:600;color:var(--sub)">Prodevis Team ▾</span>
  </div>

  <div class="content">
    <!-- FILTER -->
    <div class="filter-card">
      <h3>Data Laporan Kunjungan Semua</h3>
      <div class="filter-row">
        <div class="filter-group">
          <label>Tanggal Awal</label>
          <input type="date" id="tglAwal">
        </div>
        <div class="filter-group">
          <label>Tanggal Akhir</label>
          <input type="date" id="tglAkhir">
        </div>
        <button class="btn-cari" id="btnCari">Cari</button>
      </div>
    </div>

    <!-- ACTION BAR -->
    <div class="action-bar">
      <div class="action-left">
        <button class="btn-action" onclick="copyTable()">Copy</button>
        <button class="btn-action" onclick="exportCSV()">CSV</button>
        <button class="btn-action" onclick="window.print()">Print</button>
        <div class="show">Tampilkan <select id="perPage"><option value="10">10</option><option value="25">25</option><option value="50">50</option></select> baris</div>
      </div>
      <div class="search">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#6b7a99" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search..." id="searchInput">
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-wrap">
      <table id="mainTable">
        <thead>
          <tr>
            <th>No ⇅</th><th>No.RM ⇅</th><th>Nama Pasien ⇅</th><th>Tanggal Lahir ⇅</th>
            <th>Umur ⇅</th><th>Status Pasien ⇅</th><th>Status Penyakit ⇅</th>
            <th>Alamat ⇅</th><th>Nama KK ⇅</th><th>Agama ⇅</th>
            <th>Pendidikan ⇅</th><th>Pekerjaan ⇅</th><th>Jenis Kelamin ⇅</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
      <div class="tfoot">
        <span id="tfootInfo"></span>
        <div class="pgn" id="pgn"></div>
      </div>
    </div>
  </div>
</main>

<script>
var allData = [
  {rm:'029040',nama:'MUHAMMAD NURHADI GUNAWAN',tgl:'1995-11-05',umur:'26 Tahun 11 Bulan',sp:'',spkt:'',alamat:'BALUNG',kk:'MUHAMMAD NURHADI GUNAWAN',agama:'Islam',pend:'SMA / Sederajat',kerja:'Wiraswasta',jk:'Laki-Laki',tglKunjungan:'2023-01-10'},
  {rm:'039652',nama:'ADIVA NUR SYARIFAH PRADANA',tgl:'2022-12-06',umur:'',sp:'',spkt:'',alamat:'DUSUN ROWOTENGU 003/005',kk:'HENDI PRADANA',agama:'Islam',pend:'Belum Sekolah',kerja:'Belum/tidak bekerja',jk:'Perempuan',tglKunjungan:'2023-02-14'},
  {rm:'036519',nama:'SANIYATI',tgl:'1975-10-15',umur:'49 Tahun 2 Bulan',sp:'',spkt:'',alamat:'DUSUN KRAJAN II 007/003',kk:'SANIYATI',agama:'Islam',pend:'SD / Sederajat',kerja:'Lain-Lain',jk:'Perempuan',tglKunjungan:'2023-03-05'},
  {rm:'038993',nama:'SUPATMI',tgl:'1955-07-01',umur:'70 Tahun 7 Bulan',sp:'',spkt:'',alamat:'DUSUN KRAJAN 011/006',kk:'SUPATMI',agama:'Islam',pend:'SD / Sederajat',kerja:'Lain-Lain',jk:'Perempuan',tglKunjungan:'2023-03-20'},
  {rm:'031716',nama:'SHANUM RIZKANAYA AZZAHRA',tgl:'2022-02-28',umur:'1 Tahun 7 Bulan',sp:'',spkt:'',alamat:'UMBULREJO',kk:'HADI CAHYO WICAKSONO',agama:'Islam',pend:'Belum Sekolah',kerja:'Belum/tidak bekerja',jk:'Perempuan',tglKunjungan:'2023-04-11'},
  {rm:'039606',nama:'AMILINA NORMA YUNITA',tgl:'1998-08-09',umur:'27 Tahun 3 Bulan',sp:'Lama',spkt:'Lama',alamat:'PERUM PONDOK TANGGUL ASRI B.8A RT/RW: 001/019',kk:'EKO SUBIYANTO',agama:'Islam',pend:'Strata 1 (S1)',kerja:'PNS',jk:'Perempuan',tglKunjungan:'2023-04-22'},
  {rm:'014605',nama:'EVY NURHAYATI',tgl:'1982-03-07',umur:'43 Tahun 3 Bulan',sp:'Lama',spkt:'',alamat:'DSN KRAJAN LOR, 002/014',kk:'SIGIT PAMBUDI R',agama:'Islam',pend:'SD / Sederajat',kerja:'Belum/tidak bekerja',jk:'Perempuan',tglKunjungan:'2023-05-08'},
  {rm:'022341',nama:'BUDI HARTONO',tgl:'1980-04-12',umur:'44 Tahun 5 Bulan',sp:'Lama',spkt:'Lama',alamat:'JL. MAWAR NO. 5',kk:'BUDI HARTONO',agama:'Islam',pend:'SMA / Sederajat',kerja:'Wiraswasta',jk:'Laki-Laki',tglKunjungan:'2023-06-01'},
  {rm:'015678',nama:'SITI AMINAH',tgl:'1990-07-20',umur:'34 Tahun',sp:'Baru',spkt:'Baru',alamat:'DUSUN KRAJAN 003/001',kk:'AHMAD FAUZI',agama:'Islam',pend:'SMP / Sederajat',kerja:'Ibu Rumah Tangga',jk:'Perempuan',tglKunjungan:'2023-06-15'},
  {rm:'033456',nama:'AHMAD FAUZI',tgl:'1988-09-14',umur:'36 Tahun 1 Bulan',sp:'Baru',spkt:'',alamat:'JL. KENANGA RT 002/003',kk:'AHMAD FAUZI',agama:'Islam',pend:'SMA / Sederajat',kerja:'Swasta',jk:'Laki-Laki',tglKunjungan:'2023-07-03'},
  {rm:'041234',nama:'DEWI RAHAYU',tgl:'2000-03-15',umur:'24 Tahun 5 Bulan',sp:'Baru',spkt:'Baru',alamat:'PERUM GRIYA ASRI BLOK C',kk:'SLAMET RIYADI',agama:'Islam',pend:'Diploma III',kerja:'Swasta',jk:'Perempuan',tglKunjungan:'2023-07-18'},
  {rm:'028765',nama:'HENDRA SAPUTRA',tgl:'1979-11-02',umur:'45 Tahun',sp:'Lama',spkt:'Lama',alamat:'DUSUN WONOSARI 001/002',kk:'HENDRA SAPUTRA',agama:'Islam',pend:'SMA / Sederajat',kerja:'Petani',jk:'Laki-Laki',tglKunjungan:'2023-08-05'},
  {rm:'037890',nama:'NURUL HIDAYAH',tgl:'1995-06-25',umur:'29 Tahun 3 Bulan',sp:'Lama',spkt:'',alamat:'JL. MELATI NO. 12',kk:'WAHYU PRIYONO',agama:'Islam',pend:'Strata 1 (S1)',kerja:'Guru',jk:'Perempuan',tglKunjungan:'2023-08-20'},
  {rm:'019023',nama:'SURYONO',tgl:'1965-08-10',umur:'59 Tahun 2 Bulan',sp:'Lama',spkt:'Lama',alamat:'DUSUN SUMBER 005/004',kk:'SURYONO',agama:'Islam',pend:'SD / Sederajat',kerja:'Petani',jk:'Laki-Laki',tglKunjungan:'2023-09-12'},
  {rm:'043210',nama:'PUTRI WULANDARI',tgl:'2003-12-01',umur:'21 Tahun',sp:'Baru',spkt:'Baru',alamat:'JL. DAHLIA NO. 8',kk:'DONI PRASETYO',agama:'Islam',pend:'SMA / Sederajat',kerja:'Pelajar / Mahasiswa',jk:'Perempuan',tglKunjungan:'2023-09-28'},
];

var displayed = allData.slice();
var currentPage = 1, perPage = 10;

function getFiltered() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  return displayed.filter(function(d){
    return d.nama.toLowerCase().includes(q) || d.rm.includes(q) || d.alamat.toLowerCase().includes(q);
  });
}

function render() {
  var filtered = getFiltered();
  var total = filtered.length;
  var totalPage = Math.max(1, Math.ceil(total/perPage));
  if (currentPage > totalPage) currentPage = totalPage;
  var start = (currentPage-1)*perPage;
  var slice = filtered.slice(start, start+perPage);
  var tbody = document.getElementById('tableBody');
  if (!slice.length) {
    tbody.innerHTML = '<tr><td colspan="13" class="empty-row">Tidak ada data kunjungan</td></tr>';
  } else {
    var html = '';
    for (var i=0; i<slice.length; i++) {
      var d=slice[i], no=start+i+1;
      var spHtml = d.sp ? '<span class="status-badge '+(d.sp==='Lama'?'status-lama':'status-baru')+'">'+d.sp+'</span>' : '';
      var spktHtml = d.spkt ? '<span class="status-badge '+(d.spkt==='Lama'?'status-lama':'status-baru')+'">'+d.spkt+'</span>' : '';
      html += '<tr><td>'+no+'</td><td>'+d.rm+'</td><td>'+d.nama+'</td><td style="white-space:nowrap">'+d.tgl+'</td><td style="white-space:nowrap">'+d.umur+'</td><td>'+spHtml+'</td><td>'+spktHtml+'</td><td style="font-size:10.5px">'+d.alamat+'</td><td>'+d.kk+'</td><td>'+d.agama+'</td><td style="white-space:nowrap">'+d.pend+'</td><td style="white-space:nowrap">'+d.kerja+'</td><td>'+d.jk+'</td></tr>';
    }
    tbody.innerHTML = html;
  }
  document.getElementById('tfootInfo').textContent = 'Showing '+(total===0?0:start+1)+' to '+(start+slice.length)+' of '+total+' entries';
  var pgn = '';
  pgn += '<button '+(currentPage<=1?'disabled':'')+' onclick="goPage('+(currentPage-1)+')">Previous</button>';
  for (var x=1; x<=totalPage; x++) pgn += '<button class="'+(x===currentPage?'active-pg':'')+'" onclick="goPage('+x+')">'+x+'</button>';
  pgn += '<button '+(currentPage>=totalPage?'disabled':'')+' onclick="goPage('+(currentPage+1)+')">Next</button>';
  document.getElementById('pgn').innerHTML = pgn;
}

function goPage(n) { currentPage=n; render(); }

// Filter tanggal
document.getElementById('btnCari').addEventListener('click', function(){
  var awal = document.getElementById('tglAwal').value;
  var akhir = document.getElementById('tglAkhir').value;
  if (!awal && !akhir) { displayed = allData.slice(); render(); return; }
  displayed = allData.filter(function(d){
    if (awal && d.tglKunjungan < awal) return false;
    if (akhir && d.tglKunjungan > akhir) return false;
    return true;
  });
  currentPage = 1;
  render();
});

document.getElementById('searchInput').addEventListener('input', function(){currentPage=1;render();});
document.getElementById('perPage').addEventListener('change', function(){perPage=parseInt(this.value);currentPage=1;render();});

function copyTable() {
  var rows = [];
  var trs = document.querySelectorAll('#mainTable tbody tr');
  trs.forEach(function(tr){ rows.push(Array.from(tr.querySelectorAll('td')).map(function(td){return td.textContent.trim();}).join('\t')); });
  navigator.clipboard.writeText(rows.join('\n')).then(function(){ alert('Data berhasil disalin!'); });
}

function exportCSV() {
  var headers = ['No','No.RM','Nama Pasien','Tanggal Lahir','Umur','Status Pasien','Status Penyakit','Alamat','Nama KK','Agama','Pendidikan','Pekerjaan','Jenis Kelamin'];
  var filtered = getFiltered();
  var rows = [headers.join(',')];
  filtered.forEach(function(d, i){ rows.push([i+1,d.rm,d.nama,d.tgl,d.umur,d.sp,d.spkt,'"'+d.alamat+'"',d.kk,d.agama,d.pend,d.kerja,d.jk].join(',')); });
  var blob = new Blob([rows.join('\n')], {type:'text/csv'});
  var a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'laporan_kunjungan.csv'; a.click();
}

render();
</script>
</body>
</html>