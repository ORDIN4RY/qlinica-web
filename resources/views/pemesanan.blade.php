<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pemesanan - Sahaduta Klinik</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --dark: #0f1729; --accent: #4f6ef7; --green: #38c9a0;
  --txt: #1a2035; --sub: #6b7a99; --bg: #f0f2f8;
  --card: #fff; --border: #e4e8f0; --red: #e05252;
}
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); display: flex; font-size: 13px; min-height: 100vh; }

/* SIDEBAR */
.sidebar { width: 240px; min-height: 100vh; background: var(--dark); position: fixed; top:0; left:0; bottom:0; }
.logo { display: flex; align-items: center; gap: 10px; padding: 18px 16px; border-bottom: 1px solid rgba(255,255,255,.06); }
.logo-icon { width: 34px; height: 34px; background: var(--accent); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 15px; }
.logo h2 { color: #fff; font-size: 13px; font-weight: 700; }
.logo span { color: rgba(255,255,255,.4); font-size: 10px; }
.user { margin: 12px 10px; background: rgba(255,255,255,.06); border-radius: 10px; padding: 10px 12px; display: flex; align-items: center; gap: 8px; }
.avatar { width: 30px; height: 30px; background: var(--accent); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 12px; flex-shrink: 0; }
.user .welcome { font-size: 9px; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: .5px; }
.user .name { font-size: 12px; color: #fff; font-weight: 600; }
.nav-label { font-size: 9px; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: 1px; padding: 12px 16px 4px; }
.nav-item { display: flex; align-items: center; gap: 8px; padding: 9px 16px; color: rgba(255,255,255,.5); font-size: 12.5px; font-weight: 500; cursor: pointer; transition: all .2s; }
.nav-item:hover { background: rgba(255,255,255,.05); color: rgba(255,255,255,.8); }
.nav-item.active { background: var(--accent); color: #fff; border-radius: 0 18px 18px 0; margin-right: 14px; }
.nav-item svg { width: 15px; height: 15px; flex-shrink: 0; }
.badge { margin-left: auto; background: var(--red); color: #fff; font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 20px; }

/* MAIN */
.main { margin-left: 240px; flex: 1; }
.topbar { background: #fff; border-bottom: 1px solid var(--border); padding: 12px 22px; display: flex; align-items: center; justify-content: space-between; }
.topbar h1 { font-size: 16px; font-weight: 700; }
.topbar .bc { font-size: 11px; color: var(--sub); }
.topbar .bc span { color: var(--accent); font-weight: 600; }
.btn-month { display: flex; align-items: center; gap: 5px; background: #eef1fc; color: var(--accent); border: none; padding: 7px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; }

.content { padding: 20px 22px; }

/* STAT CARDS */
.stats { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.stat { background: var(--card); border-radius: 14px; border-top: 3px solid var(--accent); padding: 22px 28px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
.stat:last-child { border-top-color: var(--green); }
.stat label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--sub); }
.stat .val { font-size: 42px; font-weight: 800; color: var(--accent); line-height: 1.1; margin-top: 4px; }
.stat:last-child .val { color: var(--green); }

/* ACTION BAR */
.bar { background: var(--card); border-radius: 12px; padding: 14px 18px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,.04); gap: 12px; }
.bar-left { display: flex; align-items: center; gap: 10px; }
.btn-add { display: flex; align-items: center; gap: 6px; background: var(--accent); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: inherit; box-shadow: 0 3px 10px rgba(79,110,247,.3); }
.show { display: flex; align-items: center; gap: 6px; color: var(--sub); }
.show select { border: 1px solid var(--border); border-radius: 6px; padding: 5px 8px; font-size: 12px; font-family: inherit; outline: none; }
.search { display: flex; align-items: center; gap: 6px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 7px 12px; }
.search input { border: none; background: transparent; font-size: 12px; font-family: inherit; outline: none; width: 180px; color: var(--txt); }

/* TABLE */
.table-wrap { background: var(--card); border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,.04); overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #f7f8fc; border-bottom: 2px solid var(--border); }
th { padding: 11px 14px; text-align: left; font-size: 10.5px; font-weight: 700; color: var(--sub); text-transform: uppercase; letter-spacing: .6px; white-space: nowrap; }
td { padding: 13px 14px; color: var(--txt); border-bottom: 1px solid var(--border); vertical-align: middle; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #f9fbff; }
.empty { display: flex; flex-direction: column; align-items: center; padding: 48px 0; gap: 10px; color: var(--sub); }
.empty svg { opacity: .2; }
.tfoot { padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f7f8fc; }
.tfoot span { font-size: 11.5px; color: var(--sub); }
.pgn { display: flex; gap: 5px; }
.pgn button { padding: 5px 12px; border-radius: 7px; font-size: 11.5px; font-family: inherit; font-weight: 600; cursor: pointer; border: 1px solid var(--border); background: #fff; color: var(--sub); }
.pgn button:disabled { opacity: .4; cursor: not-allowed; }

/* MODAL */
.overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; align-items: center; justify-content: center; }
.overlay.show { display: flex; }
.modal { background: #fff; border-radius: 14px; width: 460px; max-width: 95vw; padding: 24px 28px; box-shadow: 0 20px 60px rgba(0,0,0,.15); animation: fadeUp .2s ease; }
@keyframes fadeUp { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }
.modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.modal-header h3 { font-size: 15px; font-weight: 700; color: var(--txt); }
.modal-close { background: none; border: none; font-size: 18px; color: var(--sub); cursor: pointer; line-height: 1; padding: 2px 6px; border-radius: 6px; }
.modal-close:hover { background: var(--bg); }
.form-group { margin-bottom: 14px; }
.form-group label { display: block; font-size: 11.5px; font-weight: 600; color: var(--sub); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
.form-group input { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; font-size: 13px; font-family: inherit; color: var(--txt); outline: none; background: var(--bg); transition: border .2s; }
.form-group input:focus { border-color: var(--accent); background: #fff; box-shadow: 0 0 0 3px rgba(79,110,247,.1); }
.form-group input[readonly] { color: var(--sub); cursor: not-allowed; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 22px; }
.btn-close { background: var(--bg); color: var(--sub); border: 1px solid var(--border); padding: 8px 18px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: inherit; }
.btn-close:hover { background: var(--border); }
.btn-save { background: var(--accent); color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: inherit; box-shadow: 0 3px 10px rgba(79,110,247,.3); }
.btn-save:hover { background: #3a58e0; }
</style>
</head>
<body>

<aside class="sidebar">
  <div class="logo">
    <div class="logo-icon">S</div>
    <div><h2>Sahaduta Klinik</h2><span>Management System</span></div>
  </div>
  <div class="user">
    <div class="avatar">P</div>
    <div><div class="welcome">Welcome Back</div><div class="name">Prodevis Team</div></div>
  </div>
  <div class="nav-label">Menu Utama</div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    Dashboard
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    Pasien <span class="badge">12</span>
  </div>
  <div class="nav-item active">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    Pemesanan
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
    Pegawai
  </div>
  <div class="nav-label">Laporan</div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    Komentar
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
    Laporan
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
    Lap. Penanganan
  </div>
  <div class="nav-item">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10z"/></svg>
    ICD X
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div>
      <h1>Pemesanan</h1>
      <div class="bc">Sahaduta Klinik › <span>Antrian</span></div>
    </div>
    <button class="btn-month">📅 Februari 2025 ▾</button>
  </div>

  <div class="content">
    <div class="stats">
      <div class="stat"><label>Jumlah Antrian</label><div class="val">0</div></div>
      <div class="stat"><label>Yang Terpanggil</label><div class="val">0</div></div>
    </div>

    <div class="bar">
      <div class="bar-left">
        <button class="btn-add">＋ Ambil Antrian</button>
        <div class="show">Tampilkan <select><option>10</option><option>25</option><option>50</option></select> entri</div>
      </div>
      <div class="search">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6b7a99" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Cari pasien, nomor RM...">
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No. Antrian ⇅</th><th>Nomor RM ⇅</th><th>Nama Pasien ⇅</th>
            <th>Jenis Kelamin ⇅</th><th>Waktu Pemesanan ⇅</th><th>Jenis Pemesan ⇅</th>
            <th>Status ⇅</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="8">
            <div class="empty">
              <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#4f6ef7" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              <p>Tidak ada data antrian tersedia</p>
            </div>
          </td></tr>
        </tbody>
      </table>
      <div class="tfoot">
        <span>Menampilkan 0 dari 0 entri</span>
        <div class="pgn">
          <button disabled>← Sebelumnya</button>
          <button disabled>Berikutnya →</button>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- MODAL -->
<div class="overlay" id="overlay">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Antrian</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="form-group">
      <label>No. RM</label>
      <input type="text" placeholder="Nomor RM" id="noRm">
    </div>
    <div class="form-group">
      <label>Nama Pasien</label>
      <input type="text" placeholder="Nama Pasien" id="namaPasien">
    </div>
    <div class="form-group">
      <label>No Antrian</label>
      <input type="text" value="1" id="noAntrian" readonly>
    </div>
    <div class="modal-footer">
      <button class="btn-close" onclick="closeModal()">Close</button>
      <button class="btn-save">Simpan</button>
    </div>
  </div>
</div>

<script>
  document.querySelector('.btn-add').onclick = () => document.getElementById('overlay').classList.add('show');
  function closeModal() { document.getElementById('overlay').classList.remove('show'); }
  document.getElementById('overlay').onclick = e => { if(e.target === e.currentTarget) closeModal(); }
</script>
</body>
</html>