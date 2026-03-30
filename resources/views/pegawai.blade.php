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

    .foto-ph { width: 34px; height: 34px; border-radius: 8px; background: #f1f5f9; border: 1.5px dashed var(--border); display: flex; align-items: center; justify-content: center; font-size: 8px; color: var(--terang); font-weight: 600; }
    .status-badge { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .status-badge.super-admin { background: #eff6ff; color: var(--biru); }
    .status-badge.admin { background: #ecfdf5; color: #059669; }

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
    .modal { background: #fff; border-radius: 16px; width: 540px; max-width: 95vw; max-height: 90vh; overflow-y: auto; padding: 22px 26px; box-shadow: 0 24px 64px rgba(15,33,68,.2); animation: modalIn .2s ease; }
    @keyframes modalIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:none; } }
    .modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--border); }
    .modal-head h3 { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 15px; color: var(--navy); }
    .modal-close-btn { background: none; border: none; font-size: 16px; color: var(--terang); cursor: pointer; padding: 2px 6px; border-radius: 6px; }
    .modal-close-btn:hover { background: #fee2e2; color: #991b1b; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group.full { grid-column: 1/-1; }
    .form-label { font-size: 11px; font-weight: 700; color: var(--abu); text-transform: uppercase; letter-spacing: .5px; }
    .form-input, .form-select { padding: 9px 12px; border-radius: 9px; border: 1px solid var(--border); font-size: 12.5px; font-family: 'Inter', sans-serif; color: var(--teks); background: #f8faff; outline: none; transition: all .15s; }
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
@endsection

@section('content')

  {{-- BAR --}}
  <div class="bar">
    <div class="bar-left">
      <button class="btn-add" id="btnTambah">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Pegawai
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
      <input type="text" id="searchInput" placeholder="Cari NIK, nama, username...">
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th><th>NIK</th><th>Nama Pegawai</th><th>Username</th>
            <th>No HP</th><th>Foto</th><th>Status</th><th>Terakhir Update</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
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
        <h3 id="modalTitle">Tambah Pegawai</h3>
        <button class="modal-close-btn" id="btnModalClose">✕</button>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">NIK <span style="color:#dc2626">*</span></label>
          <input type="text" class="form-input" id="fNik" placeholder="16 digit NIK" maxlength="16">
        </div>
        <div class="form-group">
          <label class="form-label">Password <span style="color:#dc2626" id="pwLabel">*</span></label>
          <div class="pw-wrap">
            <input type="password" class="form-input" id="fPassword" placeholder="Password" style="width:100%;padding-right:36px">
            <button type="button" class="pw-toggle" id="pwToggle">👁</button>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Pegawai <span style="color:#dc2626">*</span></label>
          <input type="text" class="form-input" id="fNama" placeholder="Nama lengkap">
        </div>
        <div class="form-group">
          <label class="form-label">Status <span style="color:#dc2626">*</span></label>
          <select class="form-select" id="fStatus">
            <option value="Super Admin">Super Admin</option>
            <option value="Admin">Admin</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Nomor HP <span style="color:#dc2626">*</span></label>
          <input type="text" class="form-input" id="fHp" placeholder="08xxxxxxxxxx" maxlength="15">
        </div>
        <div class="form-group">
          <label class="form-label">Username <span style="color:#dc2626">*</span></label>
          <input type="text" class="form-input" id="fUsername" placeholder="Username unik">
        </div>
        <div class="form-group full">
          <label class="form-label">Alamat <span style="color:#dc2626">*</span></label>
          <input type="text" class="form-input" id="fAlamat" placeholder="Alamat lengkap">
        </div>
      </div>
      <div class="modal-foot">
        <button class="btn-cancel" id="btnCancelForm">Batal</button>
        <button class="btn-save" id="btnSimpan">Simpan</button>
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
      <h3 style="font-size:14px;font-weight:700;margin-bottom:6px">Hapus Pegawai?</h3>
      <p id="delMsg" style="font-size:12px;color:var(--terang);margin-bottom:18px">Data pegawai akan dihapus permanen.</p>
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
    return pegawais.filter(p => p.nik.toLowerCase().includes(q) || p.nama.toLowerCase().includes(q) || p.username.toLowerCase().includes(q));
  }

  function renderTable() {
    var filtered = getFiltered(), total = filtered.length;
    var totalPage = Math.max(1, Math.ceil(total/perPage));
    if (currentPage > totalPage) currentPage = totalPage;
    var start = (currentPage-1)*perPage, slice = filtered.slice(start, start+perPage);
    var tbody = document.getElementById('tbody');
    if (!slice.length) {
      tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:40px;color:var(--terang)">Tidak ada data pegawai</td></tr>';
    } else {
      tbody.innerHTML = slice.map((p, i) => {
        var foto = '<div class="foto-ph">No<br>Foto</div>';
        var sc = p.status === 'Super Admin' ? 'super-admin' : 'admin';
        var act = p.status !== 'Super Admin'
          ? `<div class="action-btns"><button class="act-btn act-edit" onclick="openEdit(${p.id})"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button><button class="act-btn act-del" onclick="openDel(${p.id})"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/></svg></button></div>`
          : '<span style="color:var(--terang)">—</span>';
        return `<tr><td>${start+i+1}</td><td style="font-size:11px;font-family:monospace">${p.nik}</td><td><strong>${p.nama}</strong></td><td>${p.username}</td><td>${p.hp}</td><td>${foto}</td><td><span class="status-badge ${sc}">${p.status}</span></td><td style="font-size:11px;color:var(--terang)">${p.update}</td><td>${act}</td></tr>`;
      }).join('');
    }
    document.getElementById('tfootInfo').textContent = 'Menampilkan ' + (total===0?0:start+1) + ' sampai ' + (start+slice.length) + ' dari ' + total + ' entri';
    var pgn = document.getElementById('pagination');
    pgn.innerHTML = '';
    var addB = (lbl, pg, active, dis) => {
      var b = document.createElement('button');
      b.className = 'page-btn' + (active?' active':'');
      b.textContent = lbl; b.disabled = dis;
      if (!dis && !active) b.addEventListener('click', () => { currentPage=pg; renderTable(); });
      pgn.appendChild(b);
    };
    addB('←', currentPage-1, false, currentPage<=1);
    for (var i=1;i<=totalPage;i++) addB(i, i, i===currentPage, false);
    addB('→', currentPage+1, false, currentPage>=totalPage);
  }

  function resetForm() {
    ['fNik','fPassword','fNama','fAlamat','fHp','fUsername'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('fStatus').value='Super Admin';
  }
  function openTambah() { editId=null; resetForm(); document.getElementById('modalTitle').textContent='Tambah Pegawai'; document.getElementById('pwLabel').textContent='*'; document.getElementById('modalForm').classList.add('open'); }
  function openEdit(id) { editId=id; var p=pegawais.find(x=>x.id===id); if(!p)return; resetForm(); document.getElementById('modalTitle').textContent='Edit Pegawai'; document.getElementById('pwLabel').textContent='(opsional)'; document.getElementById('fNik').value=p.nik; document.getElementById('fNama').value=p.nama; document.getElementById('fStatus').value=p.status; document.getElementById('fAlamat').value=p.alamat; document.getElementById('fHp').value=p.hp; document.getElementById('fUsername').value=p.username; document.getElementById('modalForm').classList.add('open'); }
  function closeForm() { document.getElementById('modalForm').classList.remove('open'); }
  function openDel(id) { delId=id; var p=pegawais.find(x=>x.id===id); document.getElementById('delMsg').textContent='Pegawai "' + (p?p.nama:'') + '" akan dihapus permanen.'; document.getElementById('modalDel').classList.add('open'); }
  function closeDel() { document.getElementById('modalDel').classList.remove('open'); delId=null; }
  function showToast(msg, type) { var t=document.getElementById('toast'); t.textContent=msg; t.className='toast show '+(type||''); setTimeout(()=>t.className='toast',3000); }

  document.getElementById('btnTambah').addEventListener('click', openTambah);
  document.getElementById('btnModalClose').addEventListener('click', closeForm);
  document.getElementById('btnCancelForm').addEventListener('click', closeForm);
  document.getElementById('btnSimpan').addEventListener('click', () => {
    var nik=document.getElementById('fNik').value.trim(), nama=document.getElementById('fNama').value.trim(), pw=document.getElementById('fPassword').value, stat=document.getElementById('fStatus').value, almt=document.getElementById('fAlamat').value.trim(), hp=document.getElementById('fHp').value.trim(), user=document.getElementById('fUsername').value.trim();
    if (!nik||!nama||!almt||!hp||!user) { showToast('Semua field wajib diisi!','error'); return; }
    if (!editId&&!pw) { showToast('Password wajib diisi!','error'); return; }
    var now = new Date().toLocaleString('id-ID');
    if (editId) { var p=pegawais.find(x=>x.id===editId); if(p){p.nik=nik;p.nama=nama;p.status=stat;p.alamat=almt;p.hp=hp;p.username=user;p.update=now;} showToast('Data berhasil diperbarui','success'); }
    else { pegawais.push({id:nextId++,nik,nama,username:user,alamat:almt,hp,foto:'',status:stat,update:now}); showToast('Pegawai berhasil ditambahkan','success'); }
    closeForm(); renderTable();
  });
  document.getElementById('btnBatalHapus').addEventListener('click', closeDel);
  document.getElementById('btnKonfirmHapus').addEventListener('click', () => { pegawais=pegawais.filter(p=>p.id!==delId); closeDel(); showToast('Pegawai berhasil dihapus','success'); renderTable(); });
  document.getElementById('modalForm').addEventListener('click', e => { if(e.target===e.currentTarget)closeForm(); });
  document.getElementById('modalDel').addEventListener('click', e => { if(e.target===e.currentTarget)closeDel(); });
  document.getElementById('pwToggle').addEventListener('click', function() { var inp=document.getElementById('fPassword'); inp.type=inp.type==='password'?'text':'password'; this.textContent=inp.type==='password'?'👁':'🙈'; });
  document.getElementById('searchInput').addEventListener('input', () => { currentPage=1; renderTable(); });
  document.getElementById('perPageSel').addEventListener('change', e => { perPage=parseInt(e.target.value); currentPage=1; renderTable(); });

  window.openEdit = openEdit;
  window.openDel = openDel;
  renderTable();
</script>
@endpush