<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pegawai - Sahaduta Klinik</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --dark: #0f1729; --accent: #4f6ef7; --green: #38c9a0;
  --txt: #1a2035; --sub: #6b7a99; --bg: #f0f2f8;
  --card: #fff; --border: #e4e8f0; --red: #e05252;
}
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); display: flex; font-size: 12px; min-height: 100vh; }

.sidebar { width: 200px; min-height: 100vh; background: var(--dark); position: fixed; top:0; left:0; bottom:0; }
.logo { display: flex; align-items: center; gap: 8px; padding: 14px 12px; border-bottom: 1px solid rgba(255,255,255,.06); }
.logo-icon { width: 28px; height: 28px; background: var(--accent); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 13px; flex-shrink:0; }
.logo h2 { color: #fff; font-size: 11.5px; font-weight: 700; line-height:1.2; }
.logo span { color: rgba(255,255,255,.4); font-size: 9px; }
.user-sb { margin: 8px 8px; background: rgba(255,255,255,.06); border-radius: 8px; padding: 8px 10px; display: flex; align-items: center; gap: 7px; }
.avatar { width: 26px; height: 26px; background: var(--accent); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 11px; flex-shrink: 0; }
.user-sb .welcome { font-size: 8px; color: rgba(255,255,255,.4); text-transform: uppercase; }
.user-sb .name { font-size: 11px; color: #fff; font-weight: 600; }
.nav-label { font-size: 8px; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: .8px; padding: 10px 12px 3px; }
.nav-item { display: flex; align-items: center; gap: 7px; padding: 8px 12px; color: rgba(255,255,255,.5); font-size: 11.5px; font-weight: 500; cursor: pointer; transition: all .15s; }
.nav-item:hover { background: rgba(255,255,255,.05); color: rgba(255,255,255,.8); }
.nav-item.active { background: var(--accent); color: #fff; border-radius: 0 14px 14px 0; margin-right: 10px; }
.nav-item svg { width: 13px; height: 13px; flex-shrink: 0; }
.badge { margin-left: auto; background: var(--red); color: #fff; font-size: 8px; font-weight: 700; padding: 1px 5px; border-radius: 20px; }

.main { margin-left: 200px; flex: 1; }
.topbar { background: #fff; border-bottom: 1px solid var(--border); padding: 10px 18px; display: flex; align-items: center; justify-content: space-between; }
.topbar h1 { font-size: 14px; font-weight: 700; }
.topbar .bc { font-size: 10px; color: var(--sub); }
.topbar .bc span { color: var(--accent); font-weight: 600; }

.content { padding: 14px 18px; }

.bar { background: var(--card); border-radius: 10px; padding: 10px 14px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 6px rgba(0,0,0,.04); }
.bar-left { display: flex; align-items: center; gap: 8px; }
.btn-add { display: flex; align-items: center; gap: 5px; background: var(--accent); color: #fff; border: none; padding: 7px 13px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-add:hover { background: #3a58e0; }
.show { display: flex; align-items: center; gap: 5px; color: var(--sub); }
.show select { border: 1px solid var(--border); border-radius: 5px; padding: 4px 6px; font-size: 11px; font-family: inherit; outline: none; }
.search { display: flex; align-items: center; gap: 5px; background: var(--bg); border: 1px solid var(--border); border-radius: 7px; padding: 6px 10px; }
.search input { border: none; background: transparent; font-size: 11.5px; font-family: inherit; outline: none; width: 150px; color: var(--txt); }

.table-wrap { background: var(--card); border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.04); overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #f7f8fc; border-bottom: 2px solid var(--border); }
th { padding: 9px 10px; text-align: left; font-size: 9.5px; font-weight: 700; color: var(--sub); text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
td { padding: 9px 10px; color: var(--txt); border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 11.5px; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #f9fbff; }

.foto-img { width: 34px; height: 34px; border-radius: 6px; object-fit: cover; border: 1.5px solid var(--border); }
.foto-ph { width: 34px; height: 34px; border-radius: 6px; background: var(--bg); border: 1.5px dashed var(--border); display: flex; align-items: center; justify-content: center; font-size: 8.5px; color: var(--sub); font-weight: 600; text-align:center; }

.status-badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; white-space: nowrap; }
.status-badge.super-admin { background: #e8ecff; color: var(--accent); }
.status-badge.admin { background: #e6f9f4; color: #1a9e7a; }

.action-btns { display: flex; gap: 4px; }
.btn-edit { background: var(--accent); color: #fff; border: none; width: 26px; height: 26px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-edit:hover { background: #3a58e0; }
.btn-del { background: var(--red); color: #fff; border: none; width: 26px; height: 26px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-del:hover { background: #c43a3a; }

.empty { display: flex; flex-direction: column; align-items: center; padding: 36px 0; gap: 8px; color: var(--sub); }
.tfoot { padding: 9px 14px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f7f8fc; }
.tfoot span { font-size: 11px; color: var(--sub); }
.pgn { display: flex; gap: 4px; }
.pgn button { padding: 4px 10px; border-radius: 5px; font-size: 11px; font-family: inherit; font-weight: 600; cursor: pointer; border: 1px solid var(--border); background: #fff; color: var(--sub); }
.pgn button:not(:disabled):hover, .pgn button.active-pg { background: var(--accent); color: #fff; border-color: var(--accent); }
.pgn button:disabled { opacity: .4; cursor: not-allowed; }

/* MODAL */
.overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; align-items: center; justify-content: center; }
.overlay.show { display: flex; }
.modal { background: #fff; border-radius: 12px; width: 520px; max-width: 95vw; max-height: 90vh; overflow-y: auto; padding: 20px 24px; box-shadow: 0 16px 50px rgba(0,0,0,.18); animation: fu .2s ease; }
@keyframes fu { from { opacity:0; transform: translateY(12px); } to { opacity:1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
.modal-header h3 { font-size: 14px; font-weight: 700; }
.modal-close { background: none; border: none; font-size: 16px; color: var(--sub); cursor: pointer; padding: 2px 6px; border-radius: 5px; }
.modal-close:hover { background: var(--bg); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 10px; font-weight: 700; color: var(--sub); text-transform: uppercase; }
.req { color: var(--red); }
.form-group input, .form-group select { border: 1.5px solid var(--border); border-radius: 7px; padding: 8px 10px; font-size: 12px; font-family: inherit; color: var(--txt); outline: none; background: var(--bg); }
.form-group input:focus, .form-group select:focus { border-color: var(--accent); background: #fff; box-shadow: 0 0 0 2px rgba(79,110,247,.1); }
.pw-wrap { position: relative; }
.pw-wrap input { width: 100%; padding-right: 30px; }
.pw-toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 12px; }
.pw-strength { font-size: 9.5px; font-weight: 600; }
.pw-strength.weak { color: var(--red); }
.pw-strength.medium { color: #e09a52; }
.pw-strength.strong { color: var(--green); }
.modal-footer { display: flex; justify-content: flex-end; gap: 7px; margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border); }
.btn-cancel { background: var(--bg); color: var(--sub); border: 1px solid var(--border); padding: 7px 16px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-simpan { background: var(--accent); color: #fff; border: none; padding: 7px 18px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-simpan:hover { background: #3a58e0; }

.modal-del { background: #fff; border-radius: 12px; width: 320px; max-width: 95vw; padding: 22px; box-shadow: 0 16px 50px rgba(0,0,0,.18); animation: fu .2s ease; text-align: center; }
.del-icon { width: 46px; height: 46px; background: #fde8e8; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
.modal-del h3 { font-size: 13px; font-weight: 700; margin-bottom: 5px; }
.modal-del p { font-size: 11.5px; color: var(--sub); margin-bottom: 16px; }
.del-btns { display: flex; justify-content: center; gap: 8px; }
.btn-batal { background: var(--bg); color: var(--sub); border: 1px solid var(--border); padding: 7px 16px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-hapus { background: var(--red); color: #fff; border: none; padding: 7px 16px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-hapus:hover { background: #c43a3a; }

.toast { position: fixed; bottom: 20px; right: 20px; background: var(--txt); color: #fff; padding: 10px 18px; border-radius: 8px; font-size: 12px; font-weight: 500; z-index: 999; opacity: 0; transform: translateY(8px); transition: all .3s; pointer-events: none; }
.toast.show { opacity: 1; transform: translateY(0); }
.toast.success { background: var(--green); }
.toast.error { background: var(--red); }
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
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Pasien <span class="badge">12</span>
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>Pemesanan
  </div>
  <div class="nav-item active">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Pegawai
  </div>
  <div class="nav-label">Laporan</div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Komentar
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>Laporan
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>Lap. Penanganan
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10z"/></svg>ICD X
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div>
      <h1>Pegawai</h1>
      <div class="bc">Sahaduta Klinik › <span>Data Pegawai</span></div>
    </div>
    <span style="font-size:12px;font-weight:600;color:var(--sub)">Prodevis Team ▾</span>
  </div>

  <div class="content">
    <div class="bar">
      <div class="bar-left">
        <button class="btn-add" id="btnTambah">＋ Tambah Pegawai</button>
        <div class="show">Tampilkan <select id="perPage"><option value="10">10</option><option value="25">25</option><option value="50">50</option></select> entri</div>
      </div>
      <div class="search">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#6b7a99" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari NIK, nama, username..." id="searchInput">
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th><th>NIK</th><th>Nama Pegawai</th><th>Username</th>
            <th>No HP</th><th>Foto</th><th>Status</th><th>Terakhir Update</th><th>Action</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
      <div class="tfoot">
        <span id="tfootInfo">Menampilkan 0 dari 0 entri</span>
        <div class="pgn" id="pgn"></div>
      </div>
    </div>
  </div>
</main>

<!-- MODAL FORM -->
<div class="overlay" id="overlayForm">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modalTitle">Tambah Pegawai</h3>
      <button class="modal-close" id="btnModalClose">✕</button>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label>NIK <span class="req">*</span></label>
        <input type="text" id="fNik" placeholder="16 digit NIK" maxlength="16">
      </div>
      <div class="form-group">
        <label>Password <span class="req" id="pwLabel">*</span></label>
        <div class="pw-wrap">
          <input type="password" id="fPassword" placeholder="Password">
          <button type="button" class="pw-toggle" id="pwToggle">👁</button>
        </div>
        <div class="pw-strength" id="pwStrength"></div>
      </div>
      <div class="form-group">
        <label>Nama Pegawai <span class="req">*</span></label>
        <input type="text" id="fNama" placeholder="Nama lengkap">
      </div>
      <div class="form-group">
        <label>Status <span class="req">*</span></label>
        <select id="fStatus"><option value="Super Admin">Super Admin</option><option value="Admin">Admin</option></select>
      </div>
      <div class="form-group">
        <label>Nomor HP <span class="req">*</span></label>
        <input type="text" id="fHp" placeholder="08xxxxxxxxxx" maxlength="15">
      </div>
      <div class="form-group">
        <label>Username <span class="req">*</span></label>
        <input type="text" id="fUsername" placeholder="Username unik">
      </div>
      <div class="form-group full">
        <label>Alamat <span class="req">*</span></label>
        <input type="text" id="fAlamat" placeholder="Alamat lengkap">
      </div>
      <div class="form-group full">
        <label>Foto</label>
        <input type="file" id="fFoto" accept="image/*">
        <div style="display:flex;align-items:center;gap:8px;margin-top:5px">
          <img id="fotoPreview" style="width:40px;height:40px;border-radius:6px;object-fit:cover;border:1.5px solid var(--border);display:none" alt="">
          <span id="fotoPreviewTxt" style="font-size:11px;color:var(--sub)">Belum ada foto dipilih</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="btnCancelForm">Batal</button>
      <button class="btn-simpan" id="btnSimpan">Simpan</button>
    </div>
  </div>
</div>

<!-- MODAL HAPUS -->
<div class="overlay" id="overlayDel">
  <div class="modal-del">
    <div class="del-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e05252" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
    </div>
    <h3>Hapus Pegawai?</h3>
    <p id="delMsg">Data pegawai akan dihapus secara permanen.</p>
    <div class="del-btns">
      <button class="btn-batal" id="btnBatalHapus">Batal</button>
      <button class="btn-hapus" id="btnKonfirmHapus">Ya, Hapus</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
var pegawais = [
  { id:1, nik:'3509066503740002', nama:'Megawati dr', username:'mega74', alamat:'Curah Bamban Tanggul Wetan', hp:'081336744966', foto:'', status:'Super Admin', update:'2020-02-14 23:28:27' },
  { id:2, nik:'3509077010840007', nama:'Nety Pristiyowati', username:'nety', alamat:'Sidomekar', hp:'082338116730', foto:'', status:'Admin', update:'2025-11-11 15:01:51' },
  { id:3, nik:'3509096606930001', nama:'Masyitah', username:'masyitah', alamat:'Gambirono', hp:'082330438106', foto:'', status:'Admin', update:'2025-04-16 17:39:45' },
  { id:4, nik:'3508172005890003', nama:'KRISTIAN', username:'tian', alamat:'Jatiroto', hp:'081360411795', foto:'', status:'Super Admin', update:'2020-12-31 20:46:12' },
  { id:5, nik:'3509276212870005', nama:'UMI KULSUM', username:'Umi', alamat:'Paleran', hp:'085231422773', foto:'', status:'Super Admin', update:'2020-08-22 13:58:54' },
];
var nextId=6, editId=null, delId=null, currentPage=1, perPage=10;

function getFiltered() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  return pegawais.filter(function(p){ return p.nik.toLowerCase().includes(q)||p.nama.toLowerCase().includes(q)||p.username.toLowerCase().includes(q); });
}

function renderTable() {
  var filtered = getFiltered();
  var total = filtered.length;
  var totalPage = Math.max(1, Math.ceil(total/perPage));
  if (currentPage > totalPage) currentPage = totalPage;
  var start = (currentPage-1)*perPage;
  var slice = filtered.slice(start, start+perPage);
  var tbody = document.getElementById('tableBody');
  if (!slice.length) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="empty"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#4f6ef7" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg><p>Tidak ada data pegawai</p></div></td></tr>';
  } else {
    var html = '';
    for (var i=0; i<slice.length; i++) {
      var p=slice[i], no=start+i+1;
      var foto = p.foto ? '<img class="foto-img" src="'+p.foto+'" alt="">' : '<div class="foto-ph">No<br>Foto</div>';
      var sc = p.status==='Super Admin'?'super-admin':'admin';
      var act = (p.status!=='Super Admin')
        ? '<div class="action-btns"><button class="btn-edit" onclick="openEdit('+p.id+')" title="Edit"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button><button class="btn-del" onclick="openDel('+p.id+')" title="Hapus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/></svg></button></div>'
        : '<span style="color:var(--sub)">—</span>';
      html += '<tr><td>'+no+'</td><td style="font-family:monospace;font-size:11px">'+p.nik+'</td><td><strong>'+p.nama+'</strong></td><td>'+p.username+'</td><td>'+p.hp+'</td><td>'+foto+'</td><td><span class="status-badge '+sc+'">'+p.status+'</span></td><td style="color:var(--sub);font-size:11px">'+p.update+'</td><td>'+act+'</td></tr>';
    }
    tbody.innerHTML = html;
  }
  var showEnd = start+slice.length;
  document.getElementById('tfootInfo').textContent = 'Menampilkan '+(total===0?0:start+1)+' sampai '+showEnd+' dari '+total+' entri';
  var pgn='';
  pgn += '<button '+(currentPage<=1?'disabled':'')+' onclick="goPage('+(currentPage-1)+')">← Prev</button>';
  for (var x=1; x<=totalPage; x++) pgn += '<button class="'+(x===currentPage?'active-pg':'')+'" onclick="goPage('+x+')">'+x+'</button>';
  pgn += '<button '+(currentPage>=totalPage?'disabled':'')+' onclick="goPage('+(currentPage+1)+')">Next →</button>';
  document.getElementById('pgn').innerHTML = pgn;
}
function goPage(n){ currentPage=n; renderTable(); }

function resetForm(){
  ['fNik','fPassword','fNama','fAlamat','fHp','fUsername'].forEach(function(id){ document.getElementById(id).value=''; });
  document.getElementById('fStatus').value='Super Admin';
  document.getElementById('fFoto').value='';
  document.getElementById('fotoPreview').style.display='none';
  document.getElementById('fotoPreviewTxt').textContent='Belum ada foto dipilih';
  document.getElementById('pwStrength').textContent='';
  document.getElementById('pwStrength').className='pw-strength';
}
function openTambah(){ editId=null; resetForm(); document.getElementById('modalTitle').textContent='Tambah Pegawai'; document.getElementById('pwLabel').textContent='*'; document.getElementById('overlayForm').classList.add('show'); document.getElementById('fNik').focus(); }
function openEdit(id){ editId=id; var p=pegawais.find(function(x){return x.id===id;}); if(!p)return; resetForm(); document.getElementById('modalTitle').textContent='Edit Pegawai'; document.getElementById('pwLabel').textContent='(opsional)'; document.getElementById('fNik').value=p.nik; document.getElementById('fNama').value=p.nama; document.getElementById('fStatus').value=p.status; document.getElementById('fAlamat').value=p.alamat; document.getElementById('fHp').value=p.hp; document.getElementById('fUsername').value=p.username; document.getElementById('overlayForm').classList.add('show'); }
function closeForm(){ document.getElementById('overlayForm').classList.remove('show'); }

function simpan(){
  var nik=document.getElementById('fNik').value.trim(), nama=document.getElementById('fNama').value.trim(), pw=document.getElementById('fPassword').value, stat=document.getElementById('fStatus').value, almt=document.getElementById('fAlamat').value.trim(), hp=document.getElementById('fHp').value.trim(), user=document.getElementById('fUsername').value.trim();
  if(!nik||!nama||!almt||!hp||!user){showToast('Semua field wajib diisi!','error');return;}
  if(!editId&&!pw){showToast('Password wajib diisi!','error');return;}
  var now=new Date(), nowStr=now.toLocaleDateString('id-ID')+' '+now.toLocaleTimeString('id-ID');
  if(editId){ var p=pegawais.find(function(x){return x.id===editId;}); if(p){p.nik=nik;p.nama=nama;p.status=stat;p.alamat=almt;p.hp=hp;p.username=user;p.update=nowStr;} showToast('Data berhasil diperbarui','success'); }
  else { pegawais.push({id:nextId++,nik:nik,nama:nama,username:user,alamat:almt,hp:hp,foto:'',status:stat,update:nowStr}); showToast('Pegawai berhasil ditambahkan','success'); }
  closeForm(); renderTable();
}

function openDel(id){ delId=id; var p=pegawais.find(function(x){return x.id===id;}); document.getElementById('delMsg').textContent='Pegawai "'+(p?p.nama:'')+'" akan dihapus permanen.'; document.getElementById('overlayDel').classList.add('show'); }
function closeDel(){ document.getElementById('overlayDel').classList.remove('show'); delId=null; }
function konfirmHapus(){ pegawais=pegawais.filter(function(p){return p.id!==delId;}); closeDel(); showToast('Pegawai berhasil dihapus','success'); renderTable(); }

document.getElementById('fPassword').addEventListener('input',function(){ var v=this.value,el=document.getElementById('pwStrength'),s=0; if(!v){el.textContent='';el.className='pw-strength';return;} if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++; el.textContent='Password: '+(s<=1?'Lemah':s<=2?'Sedang':'Kuat'); el.className='pw-strength '+(s<=1?'weak':s<=2?'medium':'strong'); });
document.getElementById('pwToggle').addEventListener('click',function(){ var inp=document.getElementById('fPassword'); inp.type=inp.type==='password'?'text':'password'; this.textContent=inp.type==='password'?'👁':'🙈'; });
document.getElementById('fFoto').addEventListener('change',function(){ var f=this.files[0]; if(!f)return; var r=new FileReader(); r.onload=function(e){ var img=document.getElementById('fotoPreview'); img.src=e.target.result; img.style.display='block'; document.getElementById('fotoPreviewTxt').textContent=f.name; }; r.readAsDataURL(f); });
document.getElementById('searchInput').addEventListener('input',function(){currentPage=1;renderTable();});
document.getElementById('perPage').addEventListener('change',function(){perPage=parseInt(this.value);currentPage=1;renderTable();});

function showToast(msg,type){ var t=document.getElementById('toast'); t.textContent=msg; t.className='toast show '+(type||''); setTimeout(function(){t.className='toast';},3000); }

document.getElementById('btnTambah').addEventListener('click',openTambah);
document.getElementById('btnModalClose').addEventListener('click',closeForm);
document.getElementById('btnCancelForm').addEventListener('click',closeForm);
document.getElementById('btnSimpan').addEventListener('click',simpan);
document.getElementById('btnBatalHapus').addEventListener('click',closeDel);
document.getElementById('btnKonfirmHapus').addEventListener('click',konfirmHapus);
document.getElementById('overlayForm').addEventListener('click',function(e){if(e.target===this)closeForm();});
document.getElementById('overlayDel').addEventListener('click',function(e){if(e.target===this)closeDel();});

renderTable();
</script>
</body>
</html>