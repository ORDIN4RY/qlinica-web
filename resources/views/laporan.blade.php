@extends('layouts.admin')

@section('page_title', 'Laporan Kunjungan')
@section('breadcrumb', 'Laporan › Semua')
@section('nav_laporan', 'aktif')

@section('extra_styles')
<style>
    /* FILTER */
    .filter-card { background: var(--putih); border-radius: var(--radius); padding: 16px 20px; margin-bottom: 14px; box-shadow: var(--shadow); border: 1px solid var(--border); }
    .filter-card h3 { font-size: 13px; font-weight: 700; margin-bottom: 12px; color: var(--teks); }
    .filter-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .filter-group { display: flex; align-items: center; gap: 8px; }
    .filter-group label { font-size: 12px; font-weight: 600; color: var(--terang); white-space: nowrap; }
    .filter-group input[type="date"] { border: 1.5px solid var(--border); border-radius: 9px; padding: 7px 11px; font-size: 12px; font-family: 'Inter', sans-serif; color: var(--teks); outline: none; background: #f8faff; }
    .filter-group input[type="date"]:focus { border-color: var(--biru); background: #fff; }
    .btn-cari { background: var(--navy); color: #fff; border: none; padding: 8px 20px; border-radius: 9px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .btn-cari:hover { background: #1a3460; }

    /* ACTION BAR */
    .action-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; flex-wrap: wrap; gap: 8px; }
    .action-left { display: flex; align-items: center; gap: 6px; }
    .btn-action { border: 1px solid var(--border); background: #fff; color: var(--teks); padding: 6px 13px; border-radius: 7px; font-size: 11.5px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; }
    .btn-action:hover { background: #f8faff; }
    .tampilkan { display: flex; align-items: center; gap: 6px; color: var(--terang); font-size: 12px; }
    .tampilkan select { border: 1px solid var(--border); border-radius: 7px; padding: 5px 8px; font-size: 12px; font-family: 'Inter', sans-serif; outline: none; }
    .search-wrap { display: flex; align-items: center; gap: 6px; background: #f8faff; border: 1px solid var(--border); border-radius: 9px; padding: 7px 12px; }
    .search-wrap input { border: none; background: transparent; font-size: 12px; font-family: 'Inter', sans-serif; outline: none; width: 160px; color: var(--teks); }
    .search-wrap svg { width: 14px; height: 14px; color: var(--terang); flex-shrink: 0; }

    /* TABLE */
    .table-card { background: var(--putih); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    thead tr { background: #f8faff; border-bottom: 2px solid var(--border); }
    th { padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 700; color: var(--biru); white-space: nowrap; border-right: 1px solid var(--border); }
    th:last-child { border-right: none; }
    td { padding: 10px 12px; color: var(--teks); border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); vertical-align: top; font-size: 11.5px; line-height: 1.5; }
    td:last-child { border-right: none; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #f8faff; }
    tbody tr:nth-child(even) td { background: #fafbff; }
    tbody tr:nth-child(even):hover td { background: #f0f4ff; }

    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 600; }
    .status-lama { background: #ecfdf5; color: #059669; }
    .status-baru { background: #eff6ff; color: var(--biru); }

    .tfoot-bar { padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); background: #f8faff; }
    .tfoot-bar span { font-size: 11.5px; color: var(--terang); }
    .pagination { display: flex; gap: 4px; }
    .page-btn { padding: 5px 12px; border-radius: 7px; border: 1px solid var(--border); background: #fff; color: var(--abu); font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .page-btn.active { background: var(--biru); color: #fff; border-color: var(--biru); }
    .page-btn:disabled { opacity: .4; cursor: not-allowed; }

    @media print {
        @page { size: landscape; margin: 8mm 12mm; }
        body { background: #fff !important; color: #000 !important; }
        .sidebar, .filter-card, .action-bar, .tfoot-bar, h2 { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        .table-card { border: none !important; box-shadow: none !important; background: #fff !important; }
        .table-wrap { overflow: visible !important; width: 100% !important; }
        table { width: 100% !important; min-width: 100% !important; border: 1.5px solid #000 !important; font-size: 7.5pt !important; }
        thead { display: table-header-group !important; }
        tr { page-break-inside: avoid !important; }
        th { background: #f3f4f6 !important; color: #000 !important; border: 1px solid #000 !important; font-size: 7.5pt !important; padding: 6px 4px !important; font-weight: bold !important; text-transform: uppercase !important; }
        td { border: 1px solid #000 !important; font-size: 7.5pt !important; padding: 6px 4px !important; color: #000 !important; vertical-align: middle !important; white-space: normal !important; word-break: break-word !important; }
        .status-badge { border: 1px solid #000 !important; background: transparent !important; color: #000 !important; padding: 1px 4px !important; }
        .print-header { display: block !important; }
    }
</style>
@endsection

@section('content')

  {{-- PRINT HEADER --}}
  <div class="print-header" style="display: none;">
      <h1 style="font-size: 20px; font-weight: 800; margin-bottom: 4px; color: #1e3a8a; text-align: center; font-family: 'Inter', sans-serif;">QLINICA</h1>
      <p style="font-size: 12px; text-align: center; margin-bottom: 20px; color: #4b5563; font-weight: 600; font-family: 'Inter', sans-serif;">LAPORAN KUNJUNGAN PASIEN</p>
  </div>

  {{-- FILTER --}}
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

  {{-- ACTION BAR --}}
  <div class="action-bar">
    <div class="action-left">
      <button class="btn-action" onclick="copyTable()"><i class="fas fa-copy mr-1.5 text-gray-500"></i> Copy</button>
      <button class="btn-action" onclick="exportExcel()"><i class="fas fa-file-excel mr-1.5 text-emerald-600"></i> Excel</button>
      <button class="btn-action" onclick="window.print()"><i class="fas fa-print mr-1.5 text-blue-600"></i> Print</button>
      <div class="tampilkan">
        Tampilkan
        <select id="perPageSel">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
        baris
      </div>
    </div>
    <div class="search-wrap">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="searchInput" placeholder="Search...">
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-card">
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
        <tbody id="tbody"></tbody>
      </table>
    </div>
    <div class="tfoot-bar">
      <span id="tfootInfo"></span>
      <div class="pagination" id="pagination"></div>
    </div>
  </div>

@endsection

@push('scripts')
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

var displayed = allData.slice(), currentPage = 1, perPage = 10;

function getFiltered() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  return displayed.filter(d => d.nama.toLowerCase().includes(q) || d.rm.includes(q) || d.alamat.toLowerCase().includes(q));
}

function render() {
  var filtered = getFiltered(), total = filtered.length;
  var totalPage = Math.max(1, Math.ceil(total/perPage));
  if (currentPage > totalPage) currentPage = totalPage;
  var start = (currentPage-1)*perPage, slice = filtered.slice(start, start+perPage);
  var tbody = document.getElementById('tbody');
  if (!slice.length) {
    tbody.innerHTML = '<tr><td colspan="13" style="text-align:center;padding:40px;color:var(--terang)">Tidak ada data kunjungan</td></tr>';
  } else {
    tbody.innerHTML = slice.map((d, i) => {
      var spHtml = d.sp ? `<span class="status-badge ${d.sp==='Lama'?'status-lama':'status-baru'}">${d.sp}</span>` : '';
      var spktHtml = d.spkt ? `<span class="status-badge ${d.spkt==='Lama'?'status-lama':'status-baru'}">${d.spkt}</span>` : '';
      return `<tr><td>${start+i+1}</td><td>${d.rm}</td><td>${d.nama}</td><td style="white-space:nowrap">${d.tgl}</td><td style="white-space:nowrap">${d.umur}</td><td>${spHtml}</td><td>${spktHtml}</td><td style="font-size:10.5px">${d.alamat}</td><td>${d.kk}</td><td>${d.agama}</td><td style="white-space:nowrap">${d.pend}</td><td style="white-space:nowrap">${d.kerja}</td><td>${d.jk}</td></tr>`;
    }).join('');
  }
  document.getElementById('tfootInfo').textContent = 'Showing ' + (total===0?0:start+1) + ' to ' + (start+slice.length) + ' of ' + total + ' entries';
  var pgn = document.getElementById('pagination');
  pgn.innerHTML = '';
  var addB = (lbl, pg, cls, dis) => { var b=document.createElement('button'); b.className='page-btn'+(cls?' '+cls:''); b.textContent=lbl; b.disabled=dis; if(!dis&&cls!=='active')b.addEventListener('click',()=>{currentPage=pg;render();}); pgn.appendChild(b); };
  addB('Previous', currentPage-1, '', currentPage<=1);
  for (var i=1;i<=totalPage;i++) addB(i, i, i===currentPage?'active':'', false);
  addB('Next', currentPage+1, '', currentPage>=totalPage);
}

document.getElementById('btnCari').addEventListener('click', () => {
  var awal = document.getElementById('tglAwal').value, akhir = document.getElementById('tglAkhir').value;
  if (!awal && !akhir) { displayed = allData.slice(); render(); return; }
  displayed = allData.filter(d => {
    if (awal && d.tglKunjungan < awal) return false;
    if (akhir && d.tglKunjungan > akhir) return false;
    return true;
  });
  currentPage = 1; render();
});
document.getElementById('searchInput').addEventListener('input', () => { currentPage=1; render(); });
document.getElementById('perPageSel').addEventListener('change', e => { perPage=parseInt(e.target.value); currentPage=1; render(); });

function copyTable() {
  var rows = Array.from(document.querySelectorAll('#mainTable tbody tr')).map(tr => Array.from(tr.querySelectorAll('td')).map(td=>td.textContent.trim()).join('\t'));
  navigator.clipboard.writeText(rows.join('\n')).then(() => alert('Data berhasil disalin!'));
}
function exportExcel() {
  var filtered = getFiltered();
  var html = `
  <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
      <!--[if gte mso 9]>
      <xml>
          <x:ExcelWorkbook>
              <x:ExcelWorksheets>
                  <x:ExcelWorksheet>
                      <x:Name>Laporan Kunjungan</x:Name>
                      <x:WorksheetOptions>
                          <x:DisplayGridlines/>
                      </x:WorksheetOptions>
                  </x:ExcelWorksheet>
              </x:ExcelWorksheets>
          </x:ExcelWorkbook>
      </xml>
      <![endif]-->
      <style>
          table { border-collapse: collapse; }
          th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1.5px solid #cbd5e1; padding: 8px; text-align: left; }
          td { border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; }
      </style>
  </head>
  <body>
      <table>
          <thead>
              <tr>
                  <th>No</th>
                  <th>No.RM</th>
                  <th>Nama Pasien</th>
                  <th>Tanggal Lahir</th>
                  <th>Umur</th>
                  <th>Status Pasien</th>
                  <th>Status Penyakit</th>
                  <th>Alamat</th>
                  <th>Nama KK</th>
                  <th>Agama</th>
                  <th>Pendidikan</th>
                  <th>Pekerjaan</th>
                  <th>Jenis Kelamin</th>
              </tr>
          </thead>
          <tbody>
  `;
  
  filtered.forEach((d, i) => {
      html += `
          <tr>
              <td>${i + 1}</td>
              <td style="mso-number-format:'@';">${d.rm}</td>
              <td>${d.nama}</td>
              <td>${d.tgl}</td>
              <td>${d.umur || '-'}</td>
              <td>${d.sp || '-'}</td>
              <td>${d.spkt || '-'}</td>
              <td>${d.alamat || '-'}</td>
              <td>${d.kk || '-'}</td>
              <td>${d.agama || '-'}</td>
              <td>${d.pend || '-'}</td>
              <td>${d.kerja || '-'}</td>
              <td>${d.jk || '-'}</td>
          </tr>
      `;
  });
  
  html += `
          </tbody>
      </table>
  </body>
  </html>
  `;
  
  var blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
  var a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'laporan_kunjungan.xls';
  a.click();
}

render();
</script>
@endpush