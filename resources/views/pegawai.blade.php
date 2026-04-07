@extends('layouts.admin')

@section('page_title', 'Data Pegawai')
@section('breadcrumb', 'Pegawai')
@section('nav_pegawai', 'aktif')

@section('extra_styles')
    /* BAR */
    .bar { background: var(--putih); border-radius: var(--radius); padding: 12px 16px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow); gap: 12px; border: 1px solid var(--border); }
    .bar-left { display: flex; align-items: center; gap: 8px; }
    .btn-add { display: flex; align-items: center; gap: 6px; background: var(--navy); color: #fff; border: none; padding: 9px 16px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-add:hover { background: #1a3460; }
    .tampilkan { display: flex; align-items: center; gap: 6px; color: var(--terang); font-size: 12.5px; }
    .tampilkan select { border: 1px solid var(--border); border-radius: 7px; padding: 5px 8px; font-size: 12px; font-family: 'Inter', sans-serif; outline: none; }
    .search-wrap { display: flex; align-items: center; gap: 6px; background: #f8faff; border: 1px solid var(--border); border-radius: 9px; padding: 7px 12px; }
    .search-wrap input { border: none; background: transparent; font-size: 12px; font-family: 'Inter', sans-serif; outline: none; width: 180px; color: var(--teks); }
    .search-wrap svg { width: 14px; height: 14px; color: var(--terang); flex-shrink: 0; }

    /* TABLE */
    .table-card { background: var(--putih); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 800px; }
    thead tr { background: #f8faff; border-bottom: 2px solid var(--border); }
    th { padding: 11px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--abu); text-transform: uppercase; letter-spacing: .6px; white-space: nowrap; }
    td { padding: 12px 14px; color: var(--teks); border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 12.5px; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #f8faff; }

    .status-badge { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .status-badge.admin    { background: #ecfdf5; color: #059669; }
    .status-badge.dokter   { background: #eff6ff; color: #2563eb; }
    .status-badge.perawat  { background: #fdf4ff; color: #9333ea; }
    .status-badge.apoteker { background: #fff7ed; color: #ea580c; }

    .action-btns { display: flex; gap: 5px; }
    .act-btn { width: 28px; height: 28px; border-radius: 7px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .act-btn svg { width: 13px; height: 13px; }
    .act-edit { background: #fef3c7; color: #b45309; }
    .act-edit:hover { background: #fde68a; }
    .act-del { background: #fee2e2; color: #b91c1c; }
    .act-del:hover { background: #fecaca; }

    .tfoot-bar { padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f8faff; }
    .tfoot-bar span { font-size: 11.5px; color: var(--terang); }
    .pagination { display: flex; gap: 4px; }
    .page-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border); background: #fff; color: var(--abu); font-size: 12.5px; font-weight: 600; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .page-btn.active { background: var(--biru); color: #fff; border-color: var(--biru); }
    .page-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* MODAL */
    .modal-bg { display: none; position: fixed; inset: 0; background: rgba(15,33,68,.5); z-index: 500; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .modal-bg.open { display: flex; }
    .modal { background: #fff; border-radius: 16px; width: 560px; max-width: 95vw; max-height: 90vh; overflow-y: auto; padding: 22px 26px; box-shadow: 0 24px 64px rgba(15,33,68,.2); animation: modalIn .2s ease; }
    @keyframes modalIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:none; } }
    .modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
    .modal-head h3 { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 15px; color: var(--navy); }
    .modal-close-btn { background: none; border: none; font-size: 16px; color: var(--terang); cursor: pointer; padding: 2px 6px; border-radius: 6px; }
    .modal-close-btn:hover { background: #fee2e2; color: #991b1b; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group.full { grid-column: 1/-1; }
    .form-label { font-size: 11px; font-weight: 700; color: var(--abu); text-transform: uppercase; letter-spacing: .5px; }
    .form-input, .form-select { padding: 9px 12px; border-radius: 9px; border: 1px solid var(--border); font-size: 12.5px; font-family: 'Inter', sans-serif; color: var(--teks); background: #f8faff; outline: none; transition: all .15s; width: 100%; box-sizing: border-box; }
    .form-input:focus, .form-select:focus { border-color: var(--biru); background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .modal-foot { display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border); }
    .btn-cancel { background: #f1f5f9; color: var(--abu); border: 1px solid var(--border); padding: 9px 18px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-save { background: var(--navy); color: #fff; border: none; padding: 9px 20px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-save:hover { background: #1a3460; }

    .pw-wrap { position: relative; }
    .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 13px; }

    .toast { position: fixed; bottom: 20px; right: 20px; padding: 11px 20px; border-radius: 10px; font-size: 12.5px; font-weight: 500; z-index: 999; opacity: 0; transform: translateY(8px); transition: all .3s; pointer-events: none; color: #fff; }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.success { background: var(--hijau); }
    .toast.error { background: #dc2626; }

    .err-msg { font-size: 11px; color: #dc2626; margin-top: 3px; display: none; }
    .field-error .form-input, .field-error .form-select { border-color: #dc2626; }
    .field-error .err-msg { display: block; }
@endsection

@section('content')

  {{-- BAR --}}
  <div class="bar">
    <div class="bar-left">
      <button class="btn-add" id="btnTambah">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Petugas
      </button>
      <div class="tampilkan">
        Tampilkan
        <select id="perPageSel">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
        entri
      </div>
    </div>
    <div class="search-wrap">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Cari NIK, nama, email...">
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th><th>NIK</th><th>Nama Pegawai</th><th>Email</th>
            <th>Role</th><th>No HP</th><th>Terakhir Update</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tbody"><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--terang)">Memuat data...</td></tr></tbody>
      </table>
    </div>
    <div class="tfoot-bar">
      <span id="tfootInfo">Menampilkan 0 dari 0 entri</span>
      <div class="pagination" id="pagination"></div>
    </div>
  </div>

  {{-- MODAL FORM --}}
  <div class="modal-bg" id="modalForm">
    <div class="modal">
      <div class="modal-head">
        <h3 id="modalTitle">Tambah Petugas Baru</h3>
        <button class="modal-close-btn" id="btnModalClose">✕</button>
      </div>
      <div class="form-grid">
        {{-- Nama --}}
        <div class="form-group full" id="grpNama">
          <label class="form-label">Nama Lengkap <span style="color:#dc2626">*</span></label>
          <input type="text" class="form-input" id="fNama" placeholder="Nama lengkap petugas">
          <span class="err-msg" id="errNama"></span>
        </div>
        {{-- Email --}}
        <div class="form-group" id="grpEmail">
          <label class="form-label">Email <span style="color:#dc2626">*</span></label>
          <input type="email" class="form-input" id="fEmail" placeholder="email@sahaduta.com">
          <span class="err-msg" id="errEmail"></span>
        </div>
        {{-- Role --}}
        <div class="form-group" id="grpRole">
          <label class="form-label">Role <span style="color:#dc2626">*</span></label>
          <select class="form-select" id="fRole">
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="dokter">Dokter</option>
            <option value="perawat">Perawat</option>
            <option value="apoteker">Apoteker</option>
          </select>
          <span class="err-msg" id="errRole"></span>
        </div>
        {{-- Password --}}
        <div class="form-group" id="grpPassword">
          <label class="form-label">Password <span style="color:#dc2626" id="pwLabel">*</span></label>
          <div class="pw-wrap">
            <input type="password" class="form-input" id="fPassword" placeholder="Min. 6 karakter" style="padding-right:36px">
            <button type="button" class="pw-toggle" id="pwToggle">👁</button>
          </div>
          <span class="err-msg" id="errPassword"></span>
        </div>
        {{-- NIK --}}
        <div class="form-group" id="grpNik">
          <label class="form-label">NIK</label>
          <input type="text" class="form-input" id="fNik" placeholder="16 digit NIK" maxlength="20">
          <span class="err-msg" id="errNik"></span>
        </div>
        {{-- No HP --}}
        <div class="form-group" id="grpHp">
          <label class="form-label">No HP</label>
          <input type="text" class="form-input" id="fHp" placeholder="08xxxxxxxxxx" maxlength="15">
        </div>
        {{-- Spesialisasi --}}
        <div class="form-group" id="grpSpesialisasi">
          <label class="form-label">Spesialisasi <span style="color:var(--terang);font-weight:400">(khusus dokter)</span></label>
          <input type="text" class="form-input" id="fSpesialisasi" placeholder="Contoh: Umum, Gigi, dll">
        </div>
        {{-- No SIP --}}
        <div class="form-group" id="grpNoSip">
          <label class="form-label">No. SIP <span style="color:var(--terang);font-weight:400">(khusus dokter)</span></label>
          <input type="text" class="form-input" id="fNoSip" placeholder="Nomor Surat Izin Praktik">
        </div>
        {{-- Alamat --}}
        <div class="form-group full" id="grpAlamat">
          <label class="form-label">Alamat</label>
          <input type="text" class="form-input" id="fAlamat" placeholder="Alamat lengkap">
        </div>
      </div>
      <div class="modal-foot">
        <button class="btn-cancel" id="btnCancelForm">Batal</button>
        <button class="btn-save" id="btnSimpan">
          <span id="btnSimpanText">Simpan</span>
        </button>
      </div>
    </div>
  </div>

  {{-- MODAL HAPUS --}}
  <div class="modal-bg" id="modalDel">
    <div class="modal" style="width:320px;text-align:center">
      <div style="width:46px;height:46px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
          <polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
          <path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
        </svg>
      </div>
      <h3 style="font-size:14px;font-weight:700;margin-bottom:6px">Hapus Petugas?</h3>
      <p id="delMsg" style="font-size:12px;color:var(--terang);margin-bottom:18px">Data petugas akan dihapus permanen.</p>
      <div style="display:flex;justify-content:center;gap:8px">
        <button class="btn-cancel" id="btnBatalHapus">Batal</button>
        <button style="background:#dc2626;color:#fff;border:none;padding:9px 18px;border-radius:9px;font-size:12.5px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif" id="btnKonfirmHapus">Ya, Hapus</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

@endsection

@push('scripts')
<script>
  var pegawais = [], currentPage = 1, perPage = 10, editId = null, delId = null;
  var CSRF = '{{ csrf_token() }}';
  var URL_DATA   = '{{ route("admin.pegawai.data") }}';
  var URL_STORE  = '{{ route("admin.pegawai.store") }}';
  var URL_BASE   = '{{ url("/admin/pegawai") }}';

  /* ── Fetch data dari server ── */
  function loadData() {
    fetch(URL_DATA)
      .then(r => r.json())
      .then(data => { pegawais = data; renderTable(); })
      .catch(() => showToast('Gagal memuat data.', 'error'));
  }

  /* ── Filter & render tabel ── */
  function getFiltered() {
    var q = document.getElementById('searchInput').value.toLowerCase();
    return pegawais.filter(p =>
      p.nik.toLowerCase().includes(q) ||
      p.nama.toLowerCase().includes(q) ||
      p.email.toLowerCase().includes(q)
    );
  }

  var roleLabel = { admin:'Admin', dokter:'Dokter', perawat:'Perawat', apoteker:'Apoteker' };

  function renderTable() {
    var filtered = getFiltered(), total = filtered.length;
    var totalPage = Math.max(1, Math.ceil(total / perPage));
    if (currentPage > totalPage) currentPage = totalPage;
    var start = (currentPage - 1) * perPage, slice = filtered.slice(start, start + perPage);
    var tbody = document.getElementById('tbody');
    if (!slice.length) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--terang)">Tidak ada data petugas</td></tr>';
    } else {
      tbody.innerHTML = slice.map((p, i) => {
        var rl = roleLabel[p.role] || p.role;
        var badge = `<span class="status-badge ${p.role}">${rl}</span>`;
        var act = `<div class="action-btns">
          <button class="act-btn act-edit" onclick="openEdit(${p.id})" title="Edit">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
          </button>
          <button class="act-btn act-del" onclick="openDel(${p.id}, '${p.nama.replace(/'/g,"\\'")}');" title="Hapus">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/></svg>
          </button>
        </div>`;
        return `<tr>
          <td>${start + i + 1}</td>
          <td style="font-size:11px;font-family:monospace">${p.nik}</td>
          <td><strong>${p.nama}</strong></td>
          <td>${p.email}</td>
          <td>${badge}</td>
          <td>${p.no_hp}</td>
          <td style="font-size:11px;color:var(--terang)">${p.updated_at}</td>
          <td>${act}</td>
        </tr>`;
      }).join('');
    }
    document.getElementById('tfootInfo').textContent =
      'Menampilkan ' + (total === 0 ? 0 : start + 1) + ' sampai ' + (start + slice.length) + ' dari ' + total + ' entri';

    var pgn = document.getElementById('pagination');
    pgn.innerHTML = '';
    var addB = (lbl, pg, active, dis) => {
      var b = document.createElement('button');
      b.className = 'page-btn' + (active ? ' active' : '');
      b.textContent = lbl; b.disabled = dis;
      if (!dis && !active) b.addEventListener('click', () => { currentPage = pg; renderTable(); });
      pgn.appendChild(b);
    };
    addB('←', currentPage - 1, false, currentPage <= 1);
    for (var i = 1; i <= totalPage; i++) addB(i, i, i === currentPage, false);
    addB('→', currentPage + 1, false, currentPage >= totalPage);
  }

  /* ── Reset & buka modal ── */
  function resetForm() {
    ['fNama','fEmail','fRole','fPassword','fNik','fHp','fSpesialisasi','fNoSip','fAlamat']
      .forEach(id => { var el = document.getElementById(id); if(el) el.value = ''; });
    clearErrors();
  }

  function clearErrors() {
    ['grpNama','grpEmail','grpRole','grpPassword','grpNik'].forEach(id => {
      var g = document.getElementById(id); if(g) g.classList.remove('field-error');
    });
    ['errNama','errEmail','errRole','errPassword','errNik'].forEach(id => {
      var e = document.getElementById(id); if(e) e.textContent = '';
    });
  }

  function openTambah() {
    editId = null;
    resetForm();
    document.getElementById('modalTitle').textContent = 'Tambah Petugas Baru';
    document.getElementById('pwLabel').textContent = '*';
    document.getElementById('fPassword').placeholder = 'Min. 6 karakter';
    document.getElementById('modalForm').classList.add('open');
  }

  function openEdit(id) {
    editId = id;
    var p = pegawais.find(x => x.id === id);
    if (!p) return;
    resetForm();
    document.getElementById('modalTitle').textContent = 'Edit Petugas';
    document.getElementById('pwLabel').textContent = '(opsional)';
    document.getElementById('fPassword').placeholder = 'Kosongkan jika tidak diubah';
    document.getElementById('fNama').value = p.nama;
    document.getElementById('fEmail').value = p.email;
    document.getElementById('fRole').value = p.role;
    document.getElementById('fNik').value = p.nik !== '-' ? p.nik : '';
    document.getElementById('fHp').value  = p.no_hp !== '-' ? p.no_hp : '';
    document.getElementById('fSpesialisasi').value = p.spesialisasi !== '-' ? p.spesialisasi : '';
    document.getElementById('fNoSip').value = p.no_sip !== '-' ? p.no_sip : '';
    document.getElementById('fAlamat').value = p.alamat !== '-' ? p.alamat : '';
    document.getElementById('modalForm').classList.add('open');
  }

  function closeForm() { document.getElementById('modalForm').classList.remove('open'); }

  function openDel(id, nama) {
    delId = id;
    document.getElementById('delMsg').textContent = `Petugas "${nama}" akan dihapus (dinonaktifkan).`;
    document.getElementById('modalDel').classList.add('open');
  }
  function closeDel() { document.getElementById('modalDel').classList.remove('open'); delId = null; }

  function showToast(msg, type) {
    var t = document.getElementById('toast');
    t.textContent = msg; t.className = 'toast show ' + (type || '');
    setTimeout(() => t.className = 'toast', 3000);
  }

  function setLoading(on) {
    var btn = document.getElementById('btnSimpan');
    document.getElementById('btnSimpanText').textContent = on ? 'Menyimpan...' : 'Simpan';
    btn.disabled = on;
  }

  /* ── Submit form ── */
  document.getElementById('btnSimpan').addEventListener('click', () => {
    clearErrors();
    var nama  = document.getElementById('fNama').value.trim();
    var email = document.getElementById('fEmail').value.trim();
    var role  = document.getElementById('fRole').value;
    var pw    = document.getElementById('fPassword').value;
    var nik   = document.getElementById('fNik').value.trim();
    var hp    = document.getElementById('fHp').value.trim();
    var spes  = document.getElementById('fSpesialisasi').value.trim();
    var sip   = document.getElementById('fNoSip').value.trim();
    var almt  = document.getElementById('fAlamat').value.trim();

    var hasErr = false;
    if (!nama)  { setFieldError('grpNama','errNama','Nama wajib diisi.'); hasErr=true; }
    if (!email) { setFieldError('grpEmail','errEmail','Email wajib diisi.'); hasErr=true; }
    if (!role)  { setFieldError('grpRole','errRole','Role wajib dipilih.'); hasErr=true; }
    if (!editId && !pw) { setFieldError('grpPassword','errPassword','Password wajib diisi.'); hasErr=true; }
    if (hasErr) return;

    var url    = editId ? `${URL_BASE}/${editId}` : URL_STORE;
    var method = editId ? 'PUT' : 'POST';

    var body = { nama, email, role, nik, no_hp: hp, spesialisasi: spes, no_sip: sip, alamat: almt, _token: CSRF };
    if (pw) body.password = pw;
    if (editId) body._method = 'PUT';

    setLoading(true);
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(body)
    })
    .then(async r => {
      var data = await r.json();
      if (!r.ok) {
        // Tampilkan error validasi dari Laravel
        if (data.errors) {
          var e = data.errors;
          if (e.nama)     setFieldError('grpNama','errNama', e.nama[0]);
          if (e.email)    setFieldError('grpEmail','errEmail', e.email[0]);
          if (e.role)     setFieldError('grpRole','errRole', e.role[0]);
          if (e.password) setFieldError('grpPassword','errPassword', e.password[0]);
          if (e.nik)      setFieldError('grpNik','errNik', e.nik[0]);
        } else {
          showToast(data.message || 'Terjadi kesalahan.', 'error');
        }
        return;
      }
      showToast(data.message, 'success');
      closeForm();
      loadData();
    })
    .catch(() => showToast('Koneksi gagal.', 'error'))
    .finally(() => setLoading(false));
  });

  function setFieldError(groupId, errId, msg) {
    document.getElementById(groupId).classList.add('field-error');
    document.getElementById(errId).textContent = msg;
  }

  /* ── Hapus ── */
  document.getElementById('btnKonfirmHapus').addEventListener('click', () => {
    if (!delId) return;
    fetch(`${URL_BASE}/${delId}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ _method: 'DELETE', _token: CSRF })
    })
    .then(r => r.json())
    .then(data => { showToast(data.message, 'success'); closeDel(); loadData(); })
    .catch(() => showToast('Gagal menghapus.', 'error'));
  });

  /* ── Event listeners ── */
  document.getElementById('btnTambah').addEventListener('click', openTambah);
  document.getElementById('btnModalClose').addEventListener('click', closeForm);
  document.getElementById('btnCancelForm').addEventListener('click', closeForm);
  document.getElementById('btnBatalHapus').addEventListener('click', closeDel);
  document.getElementById('modalForm').addEventListener('click', e => { if(e.target===e.currentTarget) closeForm(); });
  document.getElementById('modalDel').addEventListener('click', e => { if(e.target===e.currentTarget) closeDel(); });
  document.getElementById('pwToggle').addEventListener('click', function() {
    var inp = document.getElementById('fPassword');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    this.textContent = inp.type === 'password' ? '👁' : '🙈';
  });
  document.getElementById('searchInput').addEventListener('input', () => { currentPage=1; renderTable(); });
  document.getElementById('perPageSel').addEventListener('change', e => { perPage=parseInt(e.target.value); currentPage=1; renderTable(); });

  window.openEdit = openEdit;
  window.openDel  = openDel;

  // Muat data saat halaman siap
  loadData();
</script>
@endpush