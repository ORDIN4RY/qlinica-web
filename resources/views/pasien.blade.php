<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sahaduta Klinik — Pasien</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy:    #0b1f3a;
      --blue:    #2563eb;
      --blue-l:  #eff6ff;
      --blue-b:  #dbeafe;
      --purple:  #7c3aed;
      --surface: #ffffff;
      --bg:      #f0f4fc;
      --border:  #e4e9f2;
      --t1:      #0b1f3a;
      --t2:      #5a6a85;
      --t3:      #a0aec0;
      --green:   #10b981;
      --green-l: #ecfdf5;
      --red:     #ef4444;
      --red-l:   #fff1f2;
      --amber:   #f59e0b;
      --r:       14px;
      --sw:      240px;
      --sh:      0 1px 2px rgba(11,31,58,.05), 0 4px 16px rgba(11,31,58,.07);
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{font-size:14px;-webkit-font-smoothing:antialiased;overflow-x:hidden}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--t1);display:flex;min-height:100vh;overflow-x:hidden}

    /* ── SIDEBAR ── */
    .sb{
      width:var(--sw);background:var(--navy);
      position:fixed;inset:0 auto 0 0;
      display:flex;flex-direction:column;
      z-index:200;overflow-y:auto;
      transition:transform .3s cubic-bezier(.4,0,.2,1);
    }
    .sb::before{
      content:'';position:absolute;inset:0;pointer-events:none;
      background:radial-gradient(ellipse at 90% 5%,rgba(37,99,235,.18) 0%,transparent 55%),
                 radial-gradient(ellipse at 10% 95%,rgba(124,58,237,.14) 0%,transparent 50%);
    }
    .sb-logo{padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.06);position:relative;z-index:1}
    .sb-logo-inner{display:flex;align-items:center;gap:11px}
    .logo-mark{
      width:38px;height:38px;border-radius:11px;flex-shrink:0;
      background:linear-gradient(135deg,#3b82f6,#1d4ed8);
      display:flex;align-items:center;justify-content:center;
      font-family:'Sora',sans-serif;font-weight:800;font-size:16px;color:#fff;
      box-shadow:0 4px 16px rgba(37,99,235,.4);
    }
    .logo-text{font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#fff;line-height:1.2}
    .logo-text small{display:block;font-family:'Inter',sans-serif;font-weight:400;font-size:10.5px;color:rgba(255,255,255,.3);margin-top:1px}
    .sb-user{
      margin:12px 14px;padding:11px 13px;
      background:rgba(255,255,255,.05);border-radius:10px;
      border:1px solid rgba(255,255,255,.07);
      display:flex;align-items:center;gap:10px;position:relative;z-index:1;
    }
    .sb-avatar{
      width:34px;height:34px;border-radius:9px;flex-shrink:0;
      background:linear-gradient(135deg,#3b82f6,#7c3aed);
      display:flex;align-items:center;justify-content:center;
      font-weight:700;font-size:13px;color:#fff;
    }
    .sb-user-info small{display:block;font-size:10px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.9px}
    .sb-user-info span{font-size:13px;font-weight:600;color:#fff}
    .sb-nav{padding:14px 12px;flex:1;position:relative;z-index:1}
    .sb-section{margin-bottom:22px}
    .sb-lbl{font-size:9.5px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.2);padding:0 10px;margin-bottom:5px}
    .sb-item{
      display:flex;align-items:center;gap:9px;
      padding:9px 11px;border-radius:9px;
      color:rgba(255,255,255,.4);font-size:13px;font-weight:500;
      cursor:pointer;transition:all .16s;margin-bottom:2px;
    }
    .sb-item:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.8)}
    .sb-item.active{background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;box-shadow:0 4px 14px rgba(37,99,235,.35)}
    .sb-icon{width:15px;height:15px;flex-shrink:0}
    .sb-badge{margin-left:auto;background:var(--red);color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:99px}

    .ham{display:none;position:fixed;top:13px;left:13px;z-index:300;width:38px;height:38px;border-radius:9px;background:var(--navy);border:none;color:#fff;align-items:center;justify-content:center;cursor:pointer;box-shadow:var(--sh)}
    .ovl{display:none;position:fixed;inset:0;background:rgba(11,31,58,.5);z-index:150;backdrop-filter:blur(3px)}
    .ovl.on{display:block}

    /* ── MAIN ── */
    .main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh;overflow-x:hidden;width:0;min-width:0}

    .top{
      background:var(--surface);border-bottom:1px solid var(--border);
      padding:0 26px;height:60px;
      display:flex;align-items:center;justify-content:space-between;
      position:sticky;top:0;z-index:100;
      box-shadow:0 1px 0 var(--border),0 2px 12px rgba(11,31,58,.04);
    }
    .top-title{font-family:'Sora',sans-serif;font-size:15px;font-weight:700;color:var(--navy)}
    .top-bc{font-size:11.5px;color:var(--t3);margin-top:1px}
    .top-bc b{color:var(--blue);font-weight:600}
    .top-right{display:flex;align-items:center;gap:8px}
    .icon-btn{
      width:38px;height:38px;border-radius:9px;background:#fff;border:1px solid var(--border);
      display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--t2);
      transition:all .15s;position:relative;
    }
    .icon-btn:hover{background:var(--blue-l);color:var(--blue);border-color:var(--blue-b)}
    .ndot{position:absolute;top:8px;right:8px;width:7px;height:7px;background:var(--red);border-radius:50%;border:2px solid #fff}

    /* ── CONTENT ── */
    .content{padding:24px;flex:1;overflow-x:hidden;width:100%}

    /* TOOLBAR */
    .toolbar{
      display:flex;align-items:center;justify-content:space-between;
      gap:12px;margin-bottom:18px;flex-wrap:wrap;
    }
    .toolbar-left{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .toolbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

    .btn-primary{
      display:flex;align-items:center;gap:7px;
      padding:9px 18px;border-radius:10px;
      background:var(--navy);color:#fff;
      font-size:13px;font-weight:600;border:none;cursor:pointer;
      font-family:'Inter',sans-serif;transition:all .15s;
      box-shadow:0 4px 14px rgba(11,31,58,.2);
      white-space:nowrap;
    }
    .btn-primary:hover{background:#1a3460}
    .btn-primary svg{width:14px;height:14px}

    /* Search */
    .search-wrap{
      position:relative;display:flex;align-items:center;
    }
    .search-wrap svg{
      position:absolute;left:11px;
      width:15px;height:15px;color:var(--t3);pointer-events:none;
    }
    .search-input{
      padding:8px 12px 8px 34px;
      border-radius:10px;border:1px solid var(--border);
      background:var(--surface);font-size:13px;
      font-family:'Inter',sans-serif;color:var(--t1);
      width:220px;transition:all .18s;
      outline:none;
    }
    .search-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.1);width:260px}
    .search-input::placeholder{color:var(--t3)}

    /* Show per page */
    .show-select-wrap{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t2)}
    .show-select{
      padding:7px 10px;border-radius:9px;border:1px solid var(--border);
      background:var(--surface);font-size:12.5px;color:var(--t1);
      font-family:'Inter',sans-serif;outline:none;cursor:pointer;
    }
    .show-select:focus{border-color:var(--blue)}

    /* Filter chip */
    .filter-chip{
      display:flex;align-items:center;gap:6px;
      padding:7px 13px;border-radius:99px;
      border:1px solid var(--border);background:var(--surface);
      font-size:12.5px;font-weight:500;color:var(--t2);
      cursor:pointer;transition:all .15s;
    }
    .filter-chip:hover{border-color:var(--blue-b);background:var(--blue-l);color:var(--blue)}
    .filter-chip svg{width:13px;height:13px}

    /* STATS ROW */
    .stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px}
    .stat{
      background:var(--surface);border-radius:var(--r);
      padding:14px 17px;border:1px solid var(--border);box-shadow:var(--sh);
      animation:up .4s cubic-bezier(.4,0,.2,1) both;
    }
    .stat:nth-child(2){animation-delay:.05s}
    .stat:nth-child(3){animation-delay:.1s}
    .stat:nth-child(4){animation-delay:.15s}
    .stat-lbl{font-size:11px;color:var(--t3);font-weight:500;margin-bottom:4px}
    .stat-val{font-family:'Sora',sans-serif;font-size:22px;font-weight:800;color:var(--navy);letter-spacing:-.5px}
    .stat-delta{font-size:11px;font-weight:600;margin-top:3px}
    .cu{color:var(--green)}.cd{color:var(--red)}.cn{color:var(--t3)}

    @keyframes up{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}

    /* TABLE CARD */
    .table-card{
      background:var(--surface);border-radius:var(--r);
      border:1px solid var(--border);box-shadow:var(--sh);
      overflow:hidden;animation:up .4s .2s cubic-bezier(.4,0,.2,1) both;
      width:100%;
    }
    .table-wrap{
      overflow-x:auto;
      overflow-y:visible;
      -webkit-overflow-scrolling:touch;
      width:100%;
    }
    .table-wrap::-webkit-scrollbar{height:6px}
    .table-wrap::-webkit-scrollbar-track{background:#f0f4fc;border-radius:99px}
    .table-wrap::-webkit-scrollbar-thumb{background:#c7d2e8;border-radius:99px}
    .table-wrap::-webkit-scrollbar-thumb:hover{background:#a0aec0}

    table{width:100%;border-collapse:collapse;min-width:900px}
    thead tr{background:#f8faff;border-bottom:2px solid var(--border)}
    thead th{
      padding:11px 14px;
      text-align:left;
      font-size:11px;font-weight:700;
      color:var(--t2);text-transform:uppercase;letter-spacing:.9px;
      white-space:nowrap;
      position:relative;
    }
    thead th.sortable{cursor:pointer;user-select:none}
    thead th.sortable:hover{color:var(--blue)}
    thead th .sort-icon{
      display:inline-flex;flex-direction:column;gap:1px;
      margin-left:5px;vertical-align:middle;opacity:.4;
    }
    thead th.sort-asc .sort-icon,
    thead th.sort-desc .sort-icon{opacity:1;color:var(--blue)}
    .sort-icon svg{width:8px;height:8px}

    tbody tr{border-bottom:1px solid #f0f4fb;transition:background .12s}
    tbody tr:last-child{border-bottom:none}
    tbody tr:hover{background:#f8faff}

    td{padding:13px 14px;font-size:13px;color:var(--t1);vertical-align:middle}
    td.muted{color:var(--t3);font-size:12px}

    .no-cell{font-weight:700;color:var(--t3);font-size:12px}
    .rm-cell{
      font-family:'Sora',sans-serif;font-weight:700;font-size:13px;
      color:var(--blue);
    }
    .name-cell{font-weight:600;color:var(--navy)}
    .age-cell .age-num{font-weight:700;font-size:13px;color:var(--navy)}
    .age-cell .age-sub{font-size:11px;color:var(--t3)}

    /* Blood type badge */
    .blood{
      display:inline-flex;align-items:center;justify-content:center;
      width:26px;height:26px;border-radius:7px;
      font-size:11px;font-weight:700;
    }
    .blood-a{background:#fef2f2;color:#dc2626}
    .blood-b{background:#eff6ff;color:#2563eb}
    .blood-ab{background:#f5f3ff;color:#7c3aed}
    .blood-o{background:#ecfdf5;color:#059669}
    .blood-empty{background:#f1f5f9;color:#94a3b8}

    /* Religion badge */
    .rel-badge{
      display:inline-block;padding:2px 8px;border-radius:99px;
      font-size:11px;font-weight:600;
      background:#f1f5f9;color:var(--t2);
    }

    /* Edu badge */
    .edu-badge{
      display:inline-block;padding:2px 8px;border-radius:99px;
      font-size:11px;font-weight:600;
      border:1px solid var(--border);color:var(--t2);
    }

    /* Jenis kelamin badge */
    .jk-badge{
      display:inline-flex;align-items:center;gap:4px;
      padding:3px 9px;border-radius:99px;
      font-size:11px;font-weight:600;
    }
    .jk-l{background:var(--blue-l);color:var(--blue)}
    .jk-p{background:#f5f3ff;color:#7c3aed}

    /* Pekerjaan */
    .pekerjaan-badge{
      display:inline-block;padding:2px 8px;border-radius:99px;
      font-size:11px;font-weight:500;
      background:#f8faff;border:1px solid var(--border);color:var(--t2);
      white-space:nowrap;
    }

    /* Row actions */
    .row-actions{display:flex;align-items:center;gap:5px}
    .act-btn{
      width:30px;height:30px;border-radius:7px;border:none;
      display:flex;align-items:center;justify-content:center;
      cursor:pointer;transition:all .14s;
    }
    .act-btn svg{width:14px;height:14px}
    .act-edit{background:var(--blue-l);color:var(--blue)}
    .act-edit:hover{background:var(--blue-b)}
    .act-del{background:#fff1f2;color:var(--red)}
    .act-del:hover{background:#fee2e2}
    .act-view{background:#f0fdf4;color:var(--green)}
    .act-view:hover{background:#dcfce7}

    /* PAGINATION */
    .table-footer{
      display:flex;align-items:center;justify-content:space-between;
      padding:14px 18px;border-top:1px solid var(--border);
      flex-wrap:wrap;gap:10px;
    }
    .pagination-info{font-size:12.5px;color:var(--t3)}
    .pagination-info b{color:var(--t2);font-weight:600}
    .pagination{display:flex;align-items:center;gap:4px}
    .page-btn{
      width:32px;height:32px;border-radius:8px;border:1px solid var(--border);
      background:var(--surface);color:var(--t2);
      font-size:12.5px;font-weight:600;
      display:flex;align-items:center;justify-content:center;
      cursor:pointer;transition:all .14s;
    }
    .page-btn:hover{border-color:var(--blue-b);background:var(--blue-l);color:var(--blue)}
    .page-btn.active{background:var(--blue);color:#fff;border-color:var(--blue);box-shadow:0 2px 8px rgba(37,99,235,.3)}
    .page-btn.disabled{opacity:.4;cursor:not-allowed;pointer-events:none}
    .page-dots{color:var(--t3);font-size:13px;padding:0 2px}

    /* EMPTY STATE */
    .empty-state{
      text-align:center;padding:48px 20px;color:var(--t3);
    }
    .empty-state svg{width:48px;height:48px;margin:0 auto 12px;display:block;opacity:.3}
    .empty-state p{font-size:13.5px;font-weight:500}

    /* MODAL */
    .modal-bg{
      display:none;position:fixed;inset:0;
      background:rgba(11,31,58,.5);z-index:500;
      backdrop-filter:blur(4px);
      align-items:center;justify-content:center;
    }
    .modal-bg.open{display:flex}
    .modal{
      background:var(--surface);border-radius:18px;
      width:100%;max-width:560px;
      box-shadow:0 24px 64px rgba(11,31,58,.22);
      animation:modalIn .22s cubic-bezier(.4,0,.2,1);
      overflow:hidden;
      margin:16px;
    }
    @keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(8px)}to{opacity:1;transform:none}}
    .modal-head{
      padding:20px 24px;border-bottom:1px solid var(--border);
      display:flex;align-items:center;justify-content:space-between;
    }
    .modal-title{font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:var(--navy)}
    .modal-close{
      width:32px;height:32px;border-radius:8px;border:none;
      background:var(--bg);color:var(--t2);
      display:flex;align-items:center;justify-content:center;
      cursor:pointer;transition:all .14s;
    }
    .modal-close:hover{background:var(--red-l);color:var(--red)}
    .modal-body{padding:20px 24px;max-height:70vh;overflow-y:auto}
    .modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end}

    /* Form */
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .form-grid.full{grid-template-columns:1fr}
    .form-group{display:flex;flex-direction:column;gap:5px}
    .form-group.span2{grid-column:1/-1}
    .form-label{font-size:12px;font-weight:600;color:var(--t2)}
    .form-input,.form-select{
      padding:9px 12px;border-radius:9px;border:1px solid var(--border);
      font-size:13px;font-family:'Inter',sans-serif;color:var(--t1);
      background:var(--surface);outline:none;transition:all .16s;
    }
    .form-input:focus,.form-select:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
    .form-input::placeholder{color:var(--t3)}

    .btn-cancel{
      padding:9px 18px;border-radius:9px;border:1px solid var(--border);
      background:var(--surface);color:var(--t2);font-size:13px;font-weight:600;
      cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s;
    }
    .btn-cancel:hover{background:var(--bg)}
    .btn-save{
      padding:9px 22px;border-radius:9px;border:none;
      background:var(--navy);color:#fff;font-size:13px;font-weight:600;
      cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s;
      box-shadow:0 4px 12px rgba(11,31,58,.2);
    }
    .btn-save:hover{background:#1a3460}

    footer{text-align:center;padding:16px;font-size:11.5px;color:var(--t3);border-top:1px solid var(--border);background:var(--surface)}
    footer strong{color:var(--navy)}

    /* RESPONSIVE */
    @media(max-width:900px){.stat-row{grid-template-columns:1fr 1fr}}
    @media(max-width:700px){
      .ham{display:flex}.sb{transform:translateX(-100%)}.sb.open{transform:translateX(0)}
      .main{margin-left:0}.top{padding:0 14px 0 56px}
      .content{padding:14px}
      .toolbar{gap:8px}
      .search-input{width:160px}
      .search-input:focus{width:180px}
    }
    @media(max-width:500px){
      .stat-row{grid-template-columns:1fr 1fr;gap:10px}
      .form-grid{grid-template-columns:1fr}
      .form-group.span2{grid-column:auto}
    }
  </style>
</head>
<body>

<button class="ham" id="ham">
  <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
  </svg>
</button>
<div class="ovl" id="ovl"></div>

<!-- SIDEBAR -->
<aside class="sb" id="sb">
  <div class="sb-logo">
    <div class="sb-logo-inner">
      <div class="logo-mark">S</div>
      <div class="logo-text">Sahaduta Klinik <small>Management System</small></div>
    </div>
  </div>
  <div class="sb-user">
    <div class="sb-avatar">P</div>
    <div class="sb-user-info"><small>Welcome back</small><span>Prodevis Team</span></div>
  </div>
  <nav class="sb-nav">
    <div class="sb-section">
      <div class="sb-lbl">Menu Utama</div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
          <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>Dashboard
      </div>
      <div class="sb-item active">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>Pasien<span class="sb-badge" id="sbBadge">12</span>
      </div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>Pemesanan
      </div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>Pegawai
      </div>
    </div>
    <div class="sb-section">
      <div class="sb-lbl">Laporan</div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>Komentar
      </div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        </svg>Laporan
      </div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
        </svg>Lap. Penanganan
      </div>
      <div class="sb-item">
        <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>ICD X
      </div>
    </div>
  </nav>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="top">
    <div>
      <div class="top-title">Data Pasien</div>
      <div class="top-bc">Sahaduta Klinik &rsaquo; <b>Pasien</b></div>
    </div>
    <div class="top-right">
      <div class="icon-btn" title="Notifikasi">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg><span class="ndot"></span>
      </div>
      <div class="icon-btn" title="Pengaturan">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </div>
    </div>
  </header>

  <div class="content">

    <!-- STAT ROW -->
    <div class="stat-row">
      <div class="stat">
        <div class="stat-lbl">Total Pasien</div>
        <div class="stat-val" id="sTot">12</div>
        <div class="stat-delta cu">↑ Terdaftar</div>
      </div>
      <div class="stat">
        <div class="stat-lbl">Laki-laki</div>
        <div class="stat-val" id="sMale">5</div>
        <div class="stat-delta cn">41.7%</div>
      </div>
      <div class="stat">
        <div class="stat-lbl">Perempuan</div>
        <div class="stat-val" id="sFemale">7</div>
        <div class="stat-delta cn">58.3%</div>
      </div>
      <div class="stat">
        <div class="stat-lbl">Rata-rata Usia</div>
        <div class="stat-val" id="sAge">38</div>
        <div class="stat-delta cn">tahun</div>
      </div>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div class="toolbar-left">
        <button class="btn-primary" id="btnTambah">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Tambah Pasien
        </button>
        <div class="filter-chip" id="filterBtn">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
          </svg>
          Filter
        </div>
      </div>
      <div class="toolbar-right">
        <div class="show-select-wrap">
          Tampilkan
          <select class="show-select" id="perPageSel">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          data
        </div>
        <div class="search-wrap">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" class="search-input" id="searchInput" placeholder="Cari nama, RM, NIK...">
        </div>
      </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
      <div class="table-wrap">
        <table id="tbl">
          <thead>
            <tr>
              <th>No</th>
              <th class="sortable" data-col="rm">No RM <span class="sort-icon"><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 0L8 5H0L4 0Z"/></svg><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 12L0 7H8L4 12Z"/></svg></span></th>
              <th>NIK</th>
              <th class="sortable" data-col="nama">Nama <span class="sort-icon"><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 0L8 5H0L4 0Z"/></svg><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 12L0 7H8L4 12Z"/></svg></span></th>
              <th>Nama KK</th>
              <th class="sortable" data-col="tgl">Tgl Lahir <span class="sort-icon"><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 0L8 5H0L4 0Z"/></svg><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 12L0 7H8L4 12Z"/></svg></span></th>
              <th class="sortable" data-col="umur">Umur <span class="sort-icon"><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 0L8 5H0L4 0Z"/></svg><svg viewBox="0 0 8 12" fill="currentColor"><path d="M4 12L0 7H8L4 12Z"/></svg></span></th>
              <th>Alamat</th>
              <th>No HP</th>
              <th>Desa</th>
              <th>Kota</th>
              <th>Darah</th>
              <th>Agama</th>
              <th>Pendidikan</th>
              <th>Pekerjaan</th>
              <th>Jenis Kelamin</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div class="table-footer">
        <div class="pagination-info" id="pageInfo">Menampilkan <b>1–10</b> dari <b>12</b> data</div>
        <div class="pagination" id="pagination"></div>
      </div>
    </div>

  </div>
  <footer>&copy; 2025 <strong>Sahaduta Klinik</strong> &middot; Management Information System</footer>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal-bg" id="modalBg">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-title" id="modalTitle">Tambah Pasien</div>
      <button class="modal-close" id="modalClose">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">No RM</label>
          <input type="text" class="form-input" id="fRm" placeholder="Contoh: 02180">
        </div>
        <div class="form-group">
          <label class="form-label">NIK</label>
          <input type="text" class="form-input" id="fNik" placeholder="16 digit NIK">
        </div>
        <div class="form-group span2">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-input" id="fNama" placeholder="Nama lengkap pasien">
        </div>
        <div class="form-group">
          <label class="form-label">Nama Kepala Keluarga</label>
          <input type="text" class="form-input" id="fKk" placeholder="Nama KK">
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Lahir</label>
          <input type="date" class="form-input" id="fTgl">
        </div>
        <div class="form-group">
          <label class="form-label">No HP</label>
          <input type="text" class="form-input" id="fHp" placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-group">
          <label class="form-label">Alamat</label>
          <input type="text" class="form-input" id="fAlamat" placeholder="Alamat lengkap">
        </div>
        <div class="form-group">
          <label class="form-label">Desa</label>
          <input type="text" class="form-input" id="fDesa" placeholder="Desa">
        </div>
        <div class="form-group">
          <label class="form-label">Kota</label>
          <input type="text" class="form-input" id="fKota" placeholder="Kota / Kabupaten">
        </div>
        <div class="form-group">
          <label class="form-label">Golongan Darah</label>
          <select class="form-select" id="fDarah">
            <option value="">— Pilih —</option>
            <option value="A">A</option><option value="B">B</option>
            <option value="AB">AB</option><option value="O">O</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Agama</label>
          <select class="form-select" id="fAgama">
            <option value="">— Pilih —</option>
            <option>Islam</option><option>Kristen</option>
            <option>Katolik</option><option>Hindu</option>
            <option>Buddha</option><option>Konghucu</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Pendidikan</label>
          <select class="form-select" id="fEdu">
            <option value="">— Pilih —</option>
            <option>Belum Sekolah</option><option>SD / Sederajat</option>
            <option>SMP / Sederajat</option><option>SMA / Sederajat</option>
            <option>Diploma</option><option>Strata 1 (S1)</option>
            <option>Strata 2 (S2)</option><option>Strata 3 (S3)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Pekerjaan</label>
          <select class="form-select" id="fPekerjaan">
            <option value="">— Pilih —</option>
            <option>Belum/tidak bekerja</option>
            <option>Pelajar/Mahasiswa</option>
            <option>PNS</option>
            <option>TNI/Polri</option>
            <option>Pegawai Swasta</option>
            <option>Buruh/pegawai</option>
            <option>Wiraswasta</option>
            <option>Petani/Pekebun</option>
            <option>Nelayan</option>
            <option>Pensiunan</option>
            <option>Ibu Rumah Tangga</option>
            <option>Lain-Lain</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Jenis Kelamin</label>
          <select class="form-select" id="fJenkel">
            <option value="">— Pilih —</option>
            <option>Laki-Laki</option>
            <option>Perempuan</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="modalCancel">Batal</button>
      <button class="btn-save" id="modalSave">Simpan Pasien</button>
    </div>
  </div>
</div>

<script>
  /* ── SIDEBAR ── */
  document.getElementById('ham').addEventListener('click', () => {
    document.getElementById('sb').classList.toggle('open');
    document.getElementById('ovl').classList.toggle('on');
  });
  document.getElementById('ovl').addEventListener('click', () => {
    document.getElementById('sb').classList.remove('open');
    document.getElementById('ovl').classList.remove('on');
  });

  /* ── DATA ── */
  let patients = [
    { id:1,  rm:'02180',  nik:'',                 nama:'REVARINA PUTRI AULIA D', kk:'KUSIONO',      tgl:'2007-07-11', hp:'082335998818', alamat:'BAGON',       desa:'PUGER',        kota:'PUGER',      darah:'',   agama:'Islam',   edu:'Belum Sekolah',   pekerjaan:'Belum/tidak bekerja', jenkel:'Perempuan' },
    { id:2,  rm:'02296',  nik:'',                 nama:'SUWARNI',                kk:'SUWARNI',      tgl:'1940-01-01', hp:'082316309811', alamat:'SEMBORO',     desa:'SEMBORO',      kota:'SEMBORO',    darah:'',   agama:'Islam',   edu:'SD / Sederajat',  pekerjaan:'Lain-Lain',           jenkel:'Perempuan' },
    { id:3,  rm:'02366',  nik:'',                 nama:'HARIZ HAKIM AQWA',       kk:'ABDUL MUFTI',  tgl:'2003-11-23', hp:'085655905668', alamat:'UMBULSARI',   desa:'UMBULSARI',    kota:'JEMBER',     darah:'',   agama:'Islam',   edu:'SMA / Sederajat', pekerjaan:'Lain-Lain',           jenkel:'Laki-Laki' },
    { id:4,  rm:'02391',  nik:'',                 nama:'HARTINI',                kk:'LENG',         tgl:'1983-01-01', hp:'081554520276', alamat:'PONDOK JOYO', desa:'SEMBORO',      kota:'JEMBER',     darah:'',   agama:'Islam',   edu:'SMA / Sederajat', pekerjaan:'Pensiunan',           jenkel:'Perempuan' },
    { id:5,  rm:'02815',  nik:'',                 nama:'NIMON',                  kk:'NIMON',        tgl:'1968-07-01', hp:'',             alamat:'SUMBERBAR',   desa:'SUMBERBARU',   kota:'JEMBER',     darah:'',   agama:'Islam',   edu:'SD / Sederajat',  pekerjaan:'Lain-Lain',           jenkel:'Laki-Laki' },
    { id:6,  rm:'.02460', nik:'3509066609730003', nama:'Indah heawati',          kk:'Maman',        tgl:'1973-09-26', hp:'081259551165', alamat:'Tekoan',      desa:'Tanggul kulon',kota:'Tanggul',    darah:'',   agama:'Islam',   edu:'Strata 1 (S1)',   pekerjaan:'Buruh/pegawai',       jenkel:'Perempuan' },
    { id:7,  rm:'000001', nik:'',                 nama:'M. RAFA',                kk:'ILYAS',        tgl:'2017-11-17', hp:'',             alamat:'SIDOMULYO',   desa:'SIDOMULYO',    kota:'JEMBER',     darah:'O',  agama:'Islam',   edu:'Belum Sekolah',   pekerjaan:'Belum/tidak bekerja', jenkel:'Laki-Laki' },
    { id:8,  rm:'000002', nik:'',                 nama:'ADAM',                   kk:'ANANG RUDAYA', tgl:'2017-06-22', hp:'',             alamat:'SELODAKON',   desa:'SELODAKON',    kota:'SELODAKON',  darah:'B',  agama:'Islam',   edu:'',                pekerjaan:'',                    jenkel:'Laki-Laki' },
    { id:9,  rm:'000003', nik:'',                 nama:'SUWARDI',                kk:'SUWARDI',      tgl:'1972-01-01', hp:'',             alamat:'SEMBORO',     desa:'SEMBORO',      kota:'JEMBER',     darah:'O',  agama:'Islam',   edu:'SD / Sederajat',  pekerjaan:'Belum/tidak bekerja', jenkel:'Laki-Laki' },
    { id:10, rm:'000004', nik:'',                 nama:'SRI WAHYUNI',            kk:'SUTRISNO',     tgl:'1985-03-15', hp:'081234567890', alamat:'REJOAGUNG',   desa:'REJOAGUNG',    kota:'JEMBER',     darah:'A',  agama:'Kristen', edu:'SMA / Sederajat', pekerjaan:'Lain-Lain',           jenkel:'Perempuan' },
    { id:11, rm:'000005', nik:'3509012345670001', nama:'BUDI SANTOSO',           kk:'SANTOSO',      tgl:'1990-08-20', hp:'087654321098', alamat:'KENCONG',     desa:'KENCONG',      kota:'JEMBER',     darah:'AB', agama:'Islam',   edu:'Strata 1 (S1)',   pekerjaan:'Buruh/pegawai',       jenkel:'Laki-Laki' },
    { id:12, rm:'000006', nik:'',                 nama:'DEWI RAHAYU',            kk:'WAHYU',        tgl:'1995-12-05', hp:'089876543210', alamat:'PUGER',       desa:'PUGER',        kota:'PUGER',      darah:'A',  agama:'Islam',   edu:'Diploma',         pekerjaan:'Wiraswasta',          jenkel:'Perempuan' },
  ];

  let currentPage = 1;
  let perPage = 10;
  let searchQ = '';
  let sortCol = '';
  let sortDir = 1;
  let editingId = null;

  function calcAge(tgl) {
    if (!tgl) return { y:0, m:0 };
    const b = new Date(tgl), n = new Date();
    let y = n.getFullYear() - b.getFullYear();
    let m = n.getMonth() - b.getMonth();
    if (m < 0) { y--; m += 12; }
    return { y, m };
  }

  function fmtDate(tgl) {
    if (!tgl) return '—';
    const d = new Date(tgl);
    return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
  }

  function bloodClass(d) {
    if (!d) return 'blood-empty';
    const m = { A:'blood-a', B:'blood-b', AB:'blood-ab', O:'blood-o' };
    return m[d] || 'blood-empty';
  }

  function getFiltered() {
    let data = [...patients];
    if (searchQ) {
      const q = searchQ.toLowerCase();
      data = data.filter(p =>
        p.nama.toLowerCase().includes(q) ||
        p.rm.toLowerCase().includes(q) ||
        p.nik.toLowerCase().includes(q) ||
        p.kota.toLowerCase().includes(q) ||
        p.desa.toLowerCase().includes(q)
      );
    }
    if (sortCol) {
      data.sort((a, b) => {
        let va = a[sortCol] ?? '', vb = b[sortCol] ?? '';
        if (sortCol === 'umur') { va = calcAge(a.tgl).y; vb = calcAge(b.tgl).y; }
        if (sortCol === 'tgl') { va = new Date(a.tgl); vb = new Date(b.tgl); }
        return va < vb ? -sortDir : va > vb ? sortDir : 0;
      });
    }
    return data;
  }

  function updateStats() {
    const all = patients;
    const males = all.filter(p => p.jenkel === 'Laki-Laki').length;
    const females = all.filter(p => p.jenkel === 'Perempuan').length;
    document.getElementById('sTot').textContent    = all.length;
    document.getElementById('sMale').textContent   = males;
    document.getElementById('sFemale').textContent = females;
    const avgAge = Math.round(all.reduce((s, p) => s + calcAge(p.tgl).y, 0) / (all.length || 1));
    document.getElementById('sAge').textContent    = avgAge;
    document.getElementById('sbBadge').textContent = all.length;
  }

  function render() {
    const data = getFiltered();
    const total = data.length;
    const pages = Math.max(1, Math.ceil(total / perPage));
    if (currentPage > pages) currentPage = pages;

    const start = (currentPage - 1) * perPage;
    const slice = data.slice(start, start + perPage);

    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '';

    if (!slice.length) {
      tbody.innerHTML = `<tr><td colspan="17"><div class="empty-state">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
        <p>Tidak ada data pasien ditemukan</p>
      </div></td></tr>`;
      document.getElementById('pageInfo').innerHTML = 'Tidak ada data';
      document.getElementById('pagination').innerHTML = '';
      return;
    }

    slice.forEach((p, i) => {
      const age = calcAge(p.tgl);
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="no-cell">${start + i + 1}</td>
        <td class="rm-cell">${p.rm}</td>
        <td class="muted">${p.nik || '—'}</td>
        <td class="name-cell">${p.nama}</td>
        <td class="muted">${p.kk}</td>
        <td class="muted">${fmtDate(p.tgl)}</td>
        <td class="age-cell"><span class="age-num">${age.y}</span> <span class="age-sub">Thn ${age.m} Bln</span></td>
        <td class="muted">${p.alamat}</td>
        <td class="muted">${p.hp || '—'}</td>
        <td class="muted">${p.desa}</td>
        <td class="muted">${p.kota}</td>
        <td><span class="blood ${bloodClass(p.darah)}">${p.darah || '—'}</span></td>
        <td><span class="rel-badge">${p.agama || '—'}</span></td>
        <td><span class="edu-badge">${p.edu || '—'}</span></td>
        <td><span class="pekerjaan-badge">${p.pekerjaan || '—'}</span></td>
        <td><span class="jk-badge ${p.jenkel === 'Laki-Laki' ? 'jk-l' : 'jk-p'}">${p.jenkel === 'Laki-Laki' ? '♂' : '♀'} ${p.jenkel || '—'}</span></td>
        <td>
          <div class="row-actions">
            <button class="act-btn act-view" title="Detail" onclick="viewPasien(${p.id})">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
            <button class="act-btn act-edit" title="Edit" onclick="editPasien(${p.id})">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button class="act-btn act-del" title="Hapus" onclick="delPasien(${p.id})">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/><path d="M10,11v6"/><path d="M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/></svg>
            </button>
          </div>
        </td>`;
      tbody.appendChild(tr);
    });

    // Pagination info
    document.getElementById('pageInfo').innerHTML =
      `Menampilkan <b>${start+1}–${Math.min(start+perPage, total)}</b> dari <b>${total}</b> data`;

    // Pagination buttons
    buildPagination(pages);
  }

  function buildPagination(pages) {
    const pg = document.getElementById('pagination');
    pg.innerHTML = '';

    const addBtn = (label, page, active, disabled, isIcon) => {
      const b = document.createElement('button');
      b.className = 'page-btn' + (active ? ' active' : '') + (disabled ? ' disabled' : '');
      b.innerHTML = isIcon ? label : label;
      if (!disabled && !active) b.addEventListener('click', () => { currentPage = page; render(); });
      pg.appendChild(b);
    };

    const prevSVG = `<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15,18 9,12 15,6"/></svg>`;
    const nextSVG = `<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="9,18 15,12 9,6"/></svg>`;

    addBtn(prevSVG, currentPage - 1, false, currentPage === 1, true);

    let start = Math.max(1, currentPage - 2);
    let end   = Math.min(pages, currentPage + 2);
    if (start > 1) { addBtn('1', 1, false, false); if (start > 2) pg.innerHTML += `<span class="page-dots">…</span>`; }
    for (let i = start; i <= end; i++) addBtn(i, i, i === currentPage, false);
    if (end < pages) { if (end < pages - 1) pg.innerHTML += `<span class="page-dots">…</span>`; addBtn(pages, pages, false, false); }

    addBtn(nextSVG, currentPage + 1, false, currentPage === pages, true);
  }

  /* ── SORT ── */
  document.querySelectorAll('thead th.sortable').forEach(th => {
    th.addEventListener('click', () => {
      const col = th.dataset.col;
      if (sortCol === col) sortDir *= -1;
      else { sortCol = col; sortDir = 1; }
      document.querySelectorAll('thead th').forEach(h => h.classList.remove('sort-asc','sort-desc'));
      th.classList.add(sortDir === 1 ? 'sort-asc' : 'sort-desc');
      currentPage = 1;
      render();
    });
  });

  /* ── SEARCH ── */
  document.getElementById('searchInput').addEventListener('input', e => {
    searchQ = e.target.value.trim();
    currentPage = 1;
    render();
  });

  /* ── PER PAGE ── */
  document.getElementById('perPageSel').addEventListener('change', e => {
    perPage = parseInt(e.target.value);
    currentPage = 1;
    render();
  });

  /* ── MODAL ── */
  function openModal(title, id = null) {
    editingId = id;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBg').classList.add('open');
    if (!id) {
      ['fRm','fNik','fNama','fKk','fHp','fAlamat','fDesa','fKota'].forEach(f => document.getElementById(f).value = '');
      ['fDarah','fAgama','fEdu','fPekerjaan','fJenkel'].forEach(f => document.getElementById(f).value = '');
      document.getElementById('fTgl').value = '';
    }
  }
  function closeModal() {
    document.getElementById('modalBg').classList.remove('open');
    editingId = null;
  }

  document.getElementById('btnTambah').addEventListener('click', () => openModal('Tambah Pasien'));
  document.getElementById('modalClose').addEventListener('click', closeModal);
  document.getElementById('modalCancel').addEventListener('click', closeModal);
  document.getElementById('modalBg').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });

  document.getElementById('modalSave').addEventListener('click', () => {
    const nama = document.getElementById('fNama').value.trim();
    if (!nama) { document.getElementById('fNama').focus(); return; }
    const obj = {
      rm:        document.getElementById('fRm').value.trim(),
      nik:       document.getElementById('fNik').value.trim(),
      nama,
      kk:        document.getElementById('fKk').value.trim(),
      tgl:       document.getElementById('fTgl').value,
      hp:        document.getElementById('fHp').value.trim(),
      alamat:    document.getElementById('fAlamat').value.trim(),
      desa:      document.getElementById('fDesa').value.trim(),
      kota:      document.getElementById('fKota').value.trim(),
      darah:     document.getElementById('fDarah').value,
      agama:     document.getElementById('fAgama').value,
      edu:       document.getElementById('fEdu').value,
      pekerjaan: document.getElementById('fPekerjaan').value,
      jenkel:    document.getElementById('fJenkel').value,
    };
    if (editingId) {
      const idx = patients.findIndex(p => p.id === editingId);
      if (idx !== -1) patients[idx] = { ...patients[idx], ...obj };
    } else {
      obj.id = Date.now();
      patients.push(obj);
    }
    closeModal();
    updateStats();
    render();
  });

  /* ── ACTIONS ── */
  window.editPasien = id => {
    const p = patients.find(x => x.id === id);
    if (!p) return;
    openModal('Edit Pasien', id);
    document.getElementById('fRm').value    = p.rm;
    document.getElementById('fNik').value   = p.nik;
    document.getElementById('fNama').value  = p.nama;
    document.getElementById('fKk').value    = p.kk;
    document.getElementById('fTgl').value   = p.tgl;
    document.getElementById('fHp').value    = p.hp;
    document.getElementById('fAlamat').value= p.alamat;
    document.getElementById('fDesa').value  = p.desa;
    document.getElementById('fKota').value  = p.kota;
    document.getElementById('fDarah').value    = p.darah;
    document.getElementById('fAgama').value    = p.agama;
    document.getElementById('fEdu').value      = p.edu;
    document.getElementById('fPekerjaan').value= p.pekerjaan;
    document.getElementById('fJenkel').value   = p.jenkel;
  };

  window.delPasien = id => {
    if (!confirm('Hapus data pasien ini?')) return;
    patients = patients.filter(p => p.id !== id);
    updateStats();
    render();
  };

  window.viewPasien = id => {
    const p = patients.find(x => x.id === id);
    if (!p) return;
    const age = calcAge(p.tgl);
    alert(`📋 Detail Pasien\n\nNama : ${p.nama}\nRM   : ${p.rm}\nUsia : ${age.y} tahun ${age.m} bulan\nAlamat: ${p.alamat}, ${p.desa}, ${p.kota}\nHP   : ${p.hp || '—'}\nDarah: ${p.darah || '—'}\nAgama: ${p.agama}\nEdu  : ${p.edu}`);
  };

  // Init
  updateStats();
  render();
</script>
</body>
</html>