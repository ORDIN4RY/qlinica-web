@extends('layouts.app')

@section('page_title', 'Komentar Pasien')
@section('breadcrumb', 'Komentar')
@section('nav_komentar', 'aktif')

@section('extra_styles')
    /* BAR */
    .bar { background: var(--putih); border-radius: var(--radius); padding: 12px 16px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow); gap: 12px; border: 1px solid var(--border); }
    .tampilkan { display: flex; align-items: center; gap: 6px; color: var(--terang); font-size: 12.5px; }
    .tampilkan select { border: 1px solid var(--border); border-radius: 7px; padding: 5px 8px; font-size: 12px; font-family: 'Inter', sans-serif; outline: none; }
    .search-wrap { display: flex; align-items: center; gap: 6px; background: #f8faff; border: 1px solid var(--border); border-radius: 9px; padding: 7px 12px; }
    .search-wrap input { border: none; background: transparent; font-size: 12px; font-family: 'Inter', sans-serif; outline: none; width: 200px; color: var(--teks); }
    .search-wrap svg { width: 14px; height: 14px; color: var(--terang); flex-shrink: 0; }

    /* TABLE */
    .table-card { background: var(--putih); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f8faff; border-bottom: 2px solid var(--border); }
    th { padding: 11px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--abu); text-transform: uppercase; letter-spacing: .6px; white-space: nowrap; }
    td { padding: 12px 14px; color: var(--teks); border-bottom: 1px solid var(--border); vertical-align: middle; font-size: 12.5px; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #f8faff; }
    .star { color: #f59e0b; }
    .star-e { color: #e2e8f0; }

    .btn-hapus { display: flex; align-items: center; gap: 5px; background: #fee2e2; color: #b91c1c; border: none; padding: 6px 12px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-hapus:hover { background: #fecaca; }
    .btn-hapus svg { width: 12px; height: 12px; }

    .tfoot-bar { padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f8faff; }
    .tfoot-bar span { font-size: 11.5px; color: var(--terang); }
    .pagination { display: flex; gap: 4px; }
    .page-btn { padding: 5px 12px; border-radius: 7px; border: 1px solid var(--border); background: #fff; color: var(--abu); font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .page-btn.active { background: var(--biru); color: #fff; border-color: var(--biru); }
    .page-btn:disabled { opacity: .4; cursor: not-allowed; }

    /* MODAL DEL */
    .modal-bg { display: none; position: fixed; inset: 0; background: rgba(15,33,68,.5); z-index: 500; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .modal-bg.open { display: flex; }
    .modal-del { background: #fff; border-radius: 16px; width: 320px; max-width: 95vw; padding: 24px; box-shadow: 0 24px 64px rgba(15,33,68,.2); animation: modalIn .2s ease; text-align: center; }
    @keyframes modalIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:none; } }
    .del-icon { width: 48px; height: 48px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
    .del-btns { display: flex; justify-content: center; gap: 8px; margin-top: 16px; }
    .btn-batal { background: #f1f5f9; color: var(--abu); border: 1px solid var(--border); padding: 8px 18px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-ya { background: #dc2626; color: #fff; border: none; padding: 8px 18px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-ya:hover { background: #b91c1c; }

    .toast { position: fixed; bottom: 20px; right: 20px; background: #dc2626; color: #fff; padding: 11px 20px; border-radius: 10px; font-size: 12.5px; font-weight: 500; z-index: 999; opacity: 0; transform: translateY(8px); transition: all .3s; pointer-events: none; }
    .toast.show { opacity: 1; transform: translateY(0); }
@endsection

@section('content')

  {{-- BAR --}}
  <div class="bar">
    <div class="tampilkan">
      Tampilkan
      <select id="perPageSel">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
      entri
    </div>
    <div class="search-wrap">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Cari nama pasien, No RM...">
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th><th>No RM</th><th>Nama Pasien</th><th>Penilaian</th>
            <th>Kritik</th><th>Saran</th><th>Tanggal Komentar</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>
    <div class="tfoot-bar">
      <span id="tfootInfo"></span>
      <div class="pagination" id="pagination"></div>
    </div>
  </div>

  {{-- MODAL HAPUS --}}
  <div class="modal-bg" id="modalBg">
    <div class="modal-del">
      <div class="del-icon">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
          <polyline points="3,6 5,6 21,6"/>
          <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
          <path d="M10,11v6"/><path d="M14,11v6"/>
          <path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
        </svg>
      </div>
      <h3 style="font-size:14px;font-weight:700;margin-bottom:6px">Hapus Komentar?</h3>
      <p id="delMsg" style="font-size:12px;color:var(--terang)">Komentar ini akan dihapus secara permanen.</p>
      <div class="del-btns">
        <button class="btn-batal" id="btnBatal">Batal</button>
        <button class="btn-ya" id="btnYaHapus">Ya, Hapus</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast">Komentar berhasil dihapus</div>

@endsection

@push('scripts')
<script>
  var data = [
    {id:1,  rm:'031539', nama:'AQILA JUWITA DZAHIN',       nilai:4, kritik:'', saran:'', tgl:'2023-10-16'},
    {id:2,  rm:'007698', nama:'M.ALI WAFA',                 nilai:5, kritik:'', saran:'', tgl:'2022-06-02'},
    {id:3,  rm:'007825', nama:'M. AZRIL ALI ZAFLAN',        nilai:5, kritik:'', saran:'', tgl:'2022-03-17'},
    {id:4,  rm:'021794', nama:'KARINA MAYCHILLA AZAHRA',    nilai:4, kritik:'', saran:'', tgl:'2021-12-22'},
    {id:5,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS', nilai:5, kritik:'', saran:'', tgl:'2021-11-25'},
    {id:6,  rm:'007825', nama:'M. AZRIL ALI ZAFLAN',        nilai:5, kritik:'', saran:'', tgl:'2021-11-25'},
    {id:7,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS', nilai:5, kritik:'', saran:'', tgl:'2021-08-12'},
    {id:8,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS', nilai:5, kritik:'', saran:'', tgl:'2021-06-22'},
    {id:9,  rm:'007499', nama:'CATUR INAYAH WIDIANINGTYAS', nilai:5, kritik:'', saran:'', tgl:'2021-06-18'},
    {id:10, rm:'017484', nama:'SUTAMI',                     nilai:4, kritik:'', saran:'', tgl:'2021-05-03'},
    {id:11, rm:'009123', nama:'BUDI SANTOSO',               nilai:3, kritik:'Antri lama', saran:'Tambah dokter', tgl:'2021-04-15'},
    {id:12, rm:'011234', nama:'SITI RAHAYU',                nilai:5, kritik:'', saran:'Pertahankan pelayanan', tgl:'2021-03-20'},
    {id:13, rm:'013456', nama:'AHMAD FAUZI',                nilai:4, kritik:'', saran:'', tgl:'2021-02-10'},
  ];
  var delId = null, currentPage = 1, perPage = 10;

  function stars(n) {
    return Array.from({length:5}, (_,i) => `<span class="${i<n?'star':'star-e'}">★</span>`).join('');
  }

  function getFiltered() {
    var q = document.getElementById('searchInput').value.toLowerCase();
    return data.filter(d => d.nama.toLowerCase().includes(q) || d.rm.includes(q));
  }

  function render() {
    var filtered = getFiltered(), total = filtered.length;
    var totalPage = Math.max(1, Math.ceil(total/perPage));
    if (currentPage > totalPage) currentPage = totalPage;
    var start = (currentPage-1)*perPage, slice = filtered.slice(start, start+perPage);
    var tbody = document.getElementById('tbody');
    tbody.innerHTML = slice.length
      ? slice.map((d, i) => `<tr>
          <td>${start+i+1}</td>
          <td style="font-weight:600;color:var(--biru)">${d.rm}</td>
          <td><strong>${d.nama}</strong></td>
          <td>${stars(d.nilai)}</td>
          <td style="color:var(--terang);font-size:12px">${d.kritik||'—'}</td>
          <td style="color:var(--terang);font-size:12px">${d.saran||'—'}</td>
          <td style="color:var(--terang)">${d.tgl}</td>
          <td><button class="btn-hapus" onclick="openDel(${d.id})">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/></svg>
            Hapus
          </button></td>
        </tr>`).join('')
      : '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--terang)">Tidak ada data komentar</td></tr>';
    document.getElementById('tfootInfo').textContent = 'Showing ' + (total===0?0:start+1) + ' to ' + (start+slice.length) + ' of ' + total + ' entries';
    var pgn = document.getElementById('pagination');
    pgn.innerHTML = '';
    var addB = (lbl, pg, cls, dis) => { var b=document.createElement('button'); b.className='page-btn'+(cls?' '+cls:''); b.textContent=lbl; b.disabled=dis; if(!dis&&cls!=='active')b.addEventListener('click',()=>{currentPage=pg;render();}); pgn.appendChild(b); };
    addB('Previous', currentPage-1, '', currentPage<=1);
    for (var i=1;i<=totalPage;i++) addB(i, i, i===currentPage?'active':'', false);
    addB('Next', currentPage+1, '', currentPage>=totalPage);
  }

  function openDel(id) { delId=id; var d=data.find(x=>x.id===id); document.getElementById('delMsg').textContent='Komentar dari "' + (d?d.nama:'') + '" akan dihapus permanen.'; document.getElementById('modalBg').classList.add('open'); }
  function closeDel() { document.getElementById('modalBg').classList.remove('open'); delId=null; }

  document.getElementById('btnBatal').addEventListener('click', closeDel);
  document.getElementById('btnYaHapus').addEventListener('click', () => {
    data = data.filter(d => d.id !== delId);
    closeDel();
    var t = document.getElementById('toast');
    t.className = 'toast show';
    setTimeout(() => t.className = 'toast', 3000);
    render();
  });
  document.getElementById('modalBg').addEventListener('click', e => { if(e.target===e.currentTarget)closeDel(); });
  document.getElementById('searchInput').addEventListener('input', () => { currentPage=1; render(); });
  document.getElementById('perPageSel').addEventListener('change', e => { perPage=parseInt(e.target.value); currentPage=1; render(); });

  window.openDel = openDel;
  render();
</script>
@endpush
