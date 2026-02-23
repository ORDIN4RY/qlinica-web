<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Komentar - Sahaduta Klinik</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root { --dark:#0f1729; --accent:#4f6ef7; --txt:#1a2035; --sub:#6b7a99; --bg:#f0f2f8; --card:#fff; --border:#e4e8f0; --red:#e05252; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); display: flex; font-size: 12px; min-height: 100vh; }

.sidebar { width: 200px; min-height: 100vh; background: var(--dark); position: fixed; top:0; left:0; bottom:0; }
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
.nav-item.active { background: var(--accent); color: #fff; border-radius: 0 14px 14px 0; margin-right: 10px; }
.nav-item svg { width: 13px; height: 13px; flex-shrink:0; }
.badge { margin-left: auto; background: var(--red); color: #fff; font-size: 8px; font-weight: 700; padding: 1px 5px; border-radius: 20px; }

.main { margin-left: 200px; flex: 1; }
.topbar { background: #fff; border-bottom: 1px solid var(--border); padding: 10px 18px; display: flex; align-items: center; justify-content: space-between; }
.topbar h1 { font-size: 14px; font-weight: 700; }
.topbar .bc { font-size: 10px; color: var(--sub); }
.topbar .bc span { color: var(--accent); font-weight: 600; }

.content { padding: 14px 18px; }

.bar { background: var(--card); border-radius: 10px; padding: 10px 14px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 6px rgba(0,0,0,.04); }
.show { display: flex; align-items: center; gap: 5px; color: var(--sub); }
.show select { border: 1px solid var(--border); border-radius: 5px; padding: 4px 6px; font-size: 11px; font-family: inherit; outline: none; }
.search { display: flex; align-items: center; gap: 5px; background: var(--bg); border: 1px solid var(--border); border-radius: 7px; padding: 6px 10px; }
.search input { border: none; background: transparent; font-size: 11.5px; font-family: inherit; outline: none; width: 160px; color: var(--txt); }

.table-wrap { background: var(--card); border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.04); overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #f7f8fc; border-bottom: 2px solid var(--border); }
th { padding: 9px 12px; text-align: left; font-size: 10px; font-weight: 700; color: var(--sub); text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
td { padding: 10px 12px; color: var(--txt); border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 11.5px; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #f9fbff; }

.star { color: #f59e0b; font-size: 13px; }
.star-empty { color: #e4e8f0; font-size: 13px; }

.btn-hapus { display: flex; align-items: center; gap: 4px; background: var(--red); color: #fff; border: none; padding: 5px 11px; border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-hapus:hover { background: #c43a3a; }

.tfoot { padding: 9px 14px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f7f8fc; }
.tfoot span { font-size: 11px; color: var(--sub); }
.pgn { display: flex; gap: 4px; }
.pgn button { padding: 4px 10px; border-radius: 5px; font-size: 11px; font-family: inherit; font-weight: 600; cursor: pointer; border: 1px solid var(--border); background: #fff; color: var(--sub); }
.pgn button:not(:disabled):hover, .pgn button.active-pg { background: var(--accent); color: #fff; border-color: var(--accent); }
.pgn button:disabled { opacity: .4; cursor: not-allowed; }

/* MODAL HAPUS */
.overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; align-items: center; justify-content: center; }
.overlay.show { display: flex; }
.modal-del { background: #fff; border-radius: 12px; width: 320px; max-width: 95vw; padding: 22px; box-shadow: 0 16px 50px rgba(0,0,0,.18); text-align: center; animation: fu .2s ease; }
@keyframes fu { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
.del-icon { width: 46px; height: 46px; background: #fde8e8; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
.modal-del h3 { font-size: 13px; font-weight: 700; margin-bottom: 5px; }
.modal-del p { font-size: 11.5px; color: var(--sub); margin-bottom: 16px; }
.del-btns { display: flex; justify-content: center; gap: 8px; }
.btn-batal { background: var(--bg); color: var(--sub); border: 1px solid var(--border); padding: 7px 16px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-ya { background: var(--red); color: #fff; border: none; padding: 7px 16px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-ya:hover { background: #c43a3a; }

.toast { position: fixed; bottom: 20px; right: 20px; background: var(--red); color: #fff; padding: 10px 18px; border-radius: 8px; font-size: 12px; font-weight: 500; z-index: 999; opacity: 0; transform: translateY(8px); transition: all .3s; pointer-events: none; }
.toast.show { opacity: 1; transform: translateY(0); }
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
  <div class="nav-label">Laporan</div>
  <div class="nav-item active"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Komentar</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>Laporan</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>Lap. Penanganan</div>
  <div class="nav-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10z"/></svg>ICD X</div>
</aside>

<main class="main">
  <div class="topbar">
    <div>
      <h1>Komentar</h1>
      <div class="bc">Sahaduta Klinik › <span>Komentar Pasien</span></div>
    </div>
    <span style="font-size:12px;font-weight:600;color:var(--sub)">Prodevis Team ▾</span>
  </div>

  <div class="content">
    <div class="bar">
      <div class="show">Tampilkan <select id="perPage"><option value="10">10</option><option value="25">25</option><option value="50">50</option></select> entri</div>
      <div class="search">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#6b7a99" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari nama pasien, No RM..." id="searchInput">
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th><th>No RM</th><th>Nama Pasien</th><th>Penilaian</th>
            <th>Kritik</th><th>Saran</th><th>Tanggal Komentar</th><th>Aksi</th>
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

<!-- MODAL HAPUS -->
<div class="overlay" id="overlayDel">
  <div class="modal-del">
    <div class="del-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e05252" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
    </div>
    <h3>Hapus Komentar?</h3>
    <p id="delMsg">Komentar ini akan dihapus secara permanen.</p>
    <div class="del-btns">
      <button class="btn-batal" id="btnBatal">Batal</button>
      <button class="btn-ya" id="btnYaHapus">Ya, Hapus</button>
    </div>
  </div>
</div>

<div class="toast" id="toast">Komentar berhasil dihapus</div>

<script>
var data = [
  {id:1,  rm:'031539', nama:'AQILA JUWITA DZAHIN',      nilai:4, kritik:'', saran:'', tgl:'2023-10-16'},
  {id:2,  rm:'007698', nama:'M.ALI WAFA',                nilai:5, kritik:'', saran:'', tgl:'2022-06-02'},
  {id:3,  rm:'007825', nama:'M. AZRIL ALI ZAFLAN',       nilai:5, kritik:'', saran:'', tgl:'2022-03-17'},
  {id:4,  rm:'021794', nama:'KARINA MAYCHILLA AZAHRA',   nilai:4, kritik:'', saran:'', tgl:'2021-12-22'},
  {id:5,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS',nilai:5, kritik:'', saran:'', tgl:'2021-11-25'},
  {id:6,  rm:'007825', nama:'M. AZRIL ALI ZAFLAN',       nilai:5, kritik:'', saran:'', tgl:'2021-11-25'},
  {id:7,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS',nilai:5, kritik:'', saran:'', tgl:'2021-08-12'},
  {id:8,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS',nilai:5, kritik:'', saran:'', tgl:'2021-06-22'},
  {id:9,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS',nilai:5, kritik:'', saran:'', tgl:'2021-06-18'},
  {id:10, rm:'017484', nama:'SUTAMI',                    nilai:4, kritik:'', saran:'', tgl:'2021-05-03'},
  {id:11, rm:'009123', nama:'BUDI SANTOSO',              nilai:3, kritik:'Antri lama', saran:'Tambah dokter', tgl:'2021-04-15'},
  {id:12, rm:'011234', nama:'SITI RAHAYU',               nilai:5, kritik:'', saran:'Pertahankan pelayanan', tgl:'2021-03-20'},
  {id:13, rm:'013456', nama:'AHMAD FAUZI',               nilai:4, kritik:'', saran:'', tgl:'2021-02-10'},
];

var delId = null, currentPage = 1, perPage = 10;

function stars(n) {
  var s = '';
  for (var i=1;i<=5;i++) s += '<span class="'+(i<=n?'star':'star-empty')+'">★</span>';
  return s;
}

function getFiltered() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  return data.filter(function(d){ return d.nama.toLowerCase().includes(q) || d.rm.includes(q); });
}

function render() {
  var filtered = getFiltered();
  var total = filtered.length;
  var totalPage = Math.max(1, Math.ceil(total/perPage));
  if (currentPage > totalPage) currentPage = totalPage;
  var start = (currentPage-1)*perPage;
  var slice = filtered.slice(start, start+perPage);
  var tbody = document.getElementById('tableBody');
  var html = '';
  for (var i=0; i<slice.length; i++) {
    var d = slice[i], no = start+i+1;
    html += '<tr><td>'+no+'</td><td>'+d.rm+'</td><td>'+d.nama+'</td><td>'+stars(d.nilai)+'</td><td style="color:var(--sub);font-size:11px">'+d.kritik+'</td><td style="color:var(--sub);font-size:11px">'+d.saran+'</td><td style="color:var(--sub)">'+d.tgl+'</td><td><button class="btn-hapus" onclick="openDel('+d.id+')"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/></svg> Hapus</button></td></tr>';
  }
  tbody.innerHTML = html || '<tr><td colspan="8" style="text-align:center;padding:36px;color:var(--sub)">Tidak ada data komentar</td></tr>';
  document.getElementById('tfootInfo').textContent = 'Showing '+(total===0?0:start+1)+' to '+(start+slice.length)+' of '+total+' entries';
  var pgn = '';
  pgn += '<button '+(currentPage<=1?'disabled':'')+' onclick="goPage('+(currentPage-1)+')">Previous</button>';
  for (var x=1; x<=totalPage; x++) pgn += '<button class="'+(x===currentPage?'active-pg':'')+'" onclick="goPage('+x+')">'+x+'</button>';
  pgn += '<button '+(currentPage>=totalPage?'disabled':'')+' onclick="goPage('+(currentPage+1)+')">Next</button>';
  document.getElementById('pgn').innerHTML = pgn;
}

function goPage(n) { currentPage=n; render(); }

function openDel(id) {
  delId = id;
  var d = data.find(function(x){return x.id===id;});
  document.getElementById('delMsg').textContent = 'Komentar dari "' + (d?d.nama:'') + '" akan dihapus permanen.';
  document.getElementById('overlayDel').classList.add('show');
}
function closeDel() { document.getElementById('overlayDel').classList.remove('show'); delId=null; }

document.getElementById('btnBatal').addEventListener('click', closeDel);
document.getElementById('btnYaHapus').addEventListener('click', function(){
  data = data.filter(function(d){return d.id!==delId;});
  closeDel();
  var t = document.getElementById('toast');
  t.className = 'toast show';
  setTimeout(function(){t.className='toast';}, 3000);
  render();
});
document.getElementById('overlayDel').addEventListener('click', function(e){if(e.target===this)closeDel();});
document.getElementById('searchInput').addEventListener('input', function(){currentPage=1;render();});
document.getElementById('perPage').addEventListener('change', function(){perPage=parseInt(this.value);currentPage=1;render();});

render();
</script>
</body>
</html>