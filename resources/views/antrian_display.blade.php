<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Display Antrian | QLINICA</title>
  <meta name="description" content="Layar antrian digital QLINICA — tampilkan nomor antrian terkini secara real-time per loket dan poli.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Sora:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg:        #e8eef8;
      --putih:     #ffffff;
      --border:    #d4ddf0;
      --shadow:    0 4px 20px rgba(15,33,68,.09);
      --shadow-lg: 0 16px 48px rgba(15,33,68,.18);
      --teks:      #1e293b;
      --terang:    #64748b;
      --abu:       #94a3b8;
      --navy:      #0f172a;
      --navy2:     #1e3a8a;
      --biru:      #2563eb;
      --biru-lt:   #dbeafe;
      --hijau:     #10b981;
      --kuning:    #f59e0b;
      --pink:      #ec4899;
      --pink2:     #be185d;
      --ungu:      #7c3aed;
      --ungu2:     #5b21b6;
      --r:         20px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      height: 100vh;
      overflow: hidden;
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--teks);
      background-image: radial-gradient(circle, #bfcfe8 1px, transparent 1px);
      background-size: 26px 26px;
      display: flex;
      flex-direction: column;
    }

    /* ══════════════════════════════════
       HEADER
    ══════════════════════════════════ */
    .header-bar {
      background: var(--putih);
      border-bottom: 2px solid var(--border);
      box-shadow: 0 2px 20px rgba(15,33,68,.08);
      padding: 0 32px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
      z-index: 20;
    }
    .logo-wrap { display:flex; align-items:center; gap:12px; }
    .logo-icon  { width:44px; height:44px; border-radius:12px; overflow:hidden; flex-shrink:0; }
    .logo-icon img { width:100%; height:100%; object-fit:contain; }
    .logo-text  { font-family:'Sora',sans-serif; font-size:20px; font-weight:900; color:var(--navy2); letter-spacing:-.5px; }
    .logo-sub   { font-size:9.5px; color:var(--abu); font-weight:600; letter-spacing:1.4px; text-transform:uppercase; margin-top:1px; }

    .header-right { text-align:right; }
    .header-clock { font-family:'Sora',sans-serif; font-size:26px; font-weight:900; color:var(--navy2); letter-spacing:-1.5px; line-height:1; }
    .header-date  { font-size:11px; color:var(--terang); margin-top:2px; font-weight:500; }

    .sound-toggle {
      width:38px; height:38px; border-radius:11px;
      background:var(--biru-lt); border:1.5px solid #bfdbfe;
      display:flex; align-items:center; justify-content:center;
      cursor:pointer; transition:all .2s; color:var(--biru); font-size:15px;
    }
    .sound-toggle:hover { background:#bfdbfe; transform:scale(1.08); }
    .sound-toggle.muted { background:#f1f5f9; border-color:var(--border); color:var(--abu); }

    /* ══════════════════════════════════
       MAIN GRID — 2-column layout
       Left: Resepsionis (full height)
       Right: 3 Poli cards stacked
    ══════════════════════════════════ */
    .main-grid {
      flex: 1 1 0;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding: 12px 18px 48px; /* bottom for ticker */
    }

    /* Cards area: side-by-side columns */
    .cards-area {
      flex: 1 1 0;
      min-height: 0;
      display: grid;
      grid-template-columns: 55fr 45fr; /* resepsionis dominan */
      gap: 10px;
    }

    /* Resepsionis column — fills full height of cards-area */
    .resepsionis-wrap {
      min-height: 0;
      height: 100%;
    }
    .resepsionis-wrap .station-card {
      height: 100%;
    }

    /* Poli column — 3 cards stacked equally */
    .poli-col {
      display: flex;
      flex-direction: column;
      gap: 10px;
      min-height: 0;
      height: 100%;
    }
    .poli-col .station-card {
      flex: 1 1 0;
      min-height: 0;
    }

    /* Stats row — compact, fixed height */
    .stats-row {
      flex: 0 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
    }

    /* ══════════════════════════════════
       STATION CARD — shared base
    ══════════════════════════════════ */
    .station-card {
      border-radius: var(--r);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      transition: box-shadow .3s;
    }

    /* ── RESEPSIONIS: dark navy hero card ── */
    .st-resepsionis {
      background: linear-gradient(150deg, #0a1628 0%, #0f2251 45%, #1a3a8f 100%);
      border: 1px solid rgba(96,165,250,.18);
      box-shadow: 0 8px 40px rgba(15,33,68,.30), inset 0 1px 0 rgba(255,255,255,.06);
      padding: 32px 40px;
    }

    /* Radial glow behind number */
    .st-resepsionis::before {
      content: '';
      position: absolute;
      width: 55%;
      aspect-ratio: 1;
      background: radial-gradient(circle, rgba(59,130,246,.22) 0%, transparent 70%);
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }

    /* Top gradient accent */
    .st-resepsionis::after {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; height: 4px;
      background: linear-gradient(90deg, #3b82f6, #6366f1, #8b5cf6);
    }

    /* ── POLI CARDS: light with color tints ── */
    .poli-card {
      padding: 16px 22px;
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
    }

    .st-poli-umum {
      background: linear-gradient(145deg, #ffffff 0%, #f0f7ff 100%);
    }
    .st-poli-kia {
      background: linear-gradient(145deg, #ffffff 0%, #fdf2f8 100%);
    }
    .st-poli-gigi {
      background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%);
    }

    /* Top accent bars on poli cards */
    .poli-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; height: 5px;
    }
    .st-poli-umum::before  { background: linear-gradient(90deg, #1d4ed8, #60a5fa); }
    .st-poli-kia::before   { background: linear-gradient(90deg, var(--pink2), var(--pink), #f9a8d4); }
    .st-poli-gigi::before  { background: linear-gradient(90deg, var(--ungu2), var(--ungu), #a78bfa); }

    /* Subtle watermark letter */
    .station-card [data-wm]::after {
      content: attr(data-wm);
    }
    .wm {
      position: absolute;
      right: -12px; bottom: -20px;
      font-size: 160px; font-weight: 900;
      font-family: 'Sora', sans-serif;
      line-height: 1;
      pointer-events: none; user-select: none;
    }
    .st-resepsionis .wm { color: rgba(96,165,250,.07); font-size: 280px; right: -24px; bottom: -48px; }
    .st-poli-umum   .wm { color: rgba(37,99,235,.05); }
    .st-poli-kia    .wm { color: rgba(236,72,153,.05); }
    .st-poli-gigi   .wm { color: rgba(124,58,237,.05); }

    /* ── Flash animations ── */
    .st-resepsionis.flash-animate { animation: flashNavy  .85s ease; }
    .st-poli-umum.flash-animate   { animation: flashBiru  .75s ease; }
    .st-poli-kia.flash-animate    { animation: flashPink  .75s ease; }
    .st-poli-gigi.flash-animate   { animation: flashUngu  .75s ease; }

    @keyframes flashNavy { 0%{box-shadow:0 0 0 0 rgba(59,130,246,.7);}   55%{box-shadow:0 0 0 28px rgba(59,130,246,0);}   100%{box-shadow:0 8px 40px rgba(15,33,68,.30);} }
    @keyframes flashBiru { 0%{box-shadow:0 0 0 0 rgba(37,99,235,.55);}   55%{box-shadow:0 0 0 18px rgba(37,99,235,0);}   100%{box-shadow:var(--shadow-lg);} }
    @keyframes flashPink { 0%{box-shadow:0 0 0 0 rgba(236,72,153,.55);}  55%{box-shadow:0 0 0 18px rgba(236,72,153,0);}  100%{box-shadow:var(--shadow-lg);} }
    @keyframes flashUngu { 0%{box-shadow:0 0 0 0 rgba(124,58,237,.55);}  55%{box-shadow:0 0 0 18px rgba(124,58,237,0);}  100%{box-shadow:var(--shadow-lg);} }

    /* ══════════════════════════════════
       LIVE DOT
    ══════════════════════════════════ */
    .live-dot {
      width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
      animation: blink 1.6s infinite;
    }
    .st-resepsionis .live-dot { background:#34d399; box-shadow:0 0 0 3px rgba(52,211,153,.2); }
    .st-poli-umum   .live-dot { background:var(--biru); box-shadow:0 0 0 3px rgba(37,99,235,.15); }
    .st-poli-kia    .live-dot { background:var(--pink);  box-shadow:0 0 0 3px rgba(236,72,153,.15); }
    .st-poli-gigi   .live-dot { background:var(--ungu);  box-shadow:0 0 0 3px rgba(124,58,237,.15); }
    @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.2;} }

    /* ══════════════════════════════════
       STATION LABEL BADGE
    ══════════════════════════════════ */
    .station-label {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 7px 16px; border-radius: 99px;
      font-size: 10px; font-weight: 800;
      letter-spacing: 2px; text-transform: uppercase;
      margin-bottom: 0; flex-shrink: 0;
    }
    .st-resepsionis .station-label {
      background: rgba(255,255,255,.1);
      color: rgba(255,255,255,.85);
      border: 1px solid rgba(255,255,255,.15);
      font-size: 11px; letter-spacing: 2.5px;
      margin-bottom: 20px;
    }
    .st-poli-umum   .station-label { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; margin-bottom:8px; }
    .st-poli-kia    .station-label { background:#fdf2f8; color:var(--pink2); border:1px solid #f9a8d4; margin-bottom:8px; }
    .st-poli-gigi   .station-label { background:#f5f3ff; color:var(--ungu2); border:1px solid #ddd6fe; margin-bottom:8px; }

    /* ══════════════════════════════════
       STATION NUMBER — big & bold
    ══════════════════════════════════ */
    .station-number {
      font-family: 'Sora', sans-serif; font-weight: 900; line-height: 1;
      transition: all .3s cubic-bezier(.4,0,.2,1);
      position: relative; z-index: 1;
    }
    /* Resepsionis: sangat besar, putih */
    .st-resepsionis .station-number {
      font-size: clamp(96px, 13vw, 200px);
      letter-spacing: -8px;
      color: #ffffff;
      text-shadow: 0 0 60px rgba(96,165,250,.35);
    }
    /* Poli: sedang, warna accent */
    .poli-card .station-number {
      font-size: clamp(46px, 6vw, 82px);
      letter-spacing: -4px;
    }
    .st-poli-umum .station-number { color: var(--biru); }
    .st-poli-kia  .station-number { color: var(--pink); }
    .st-poli-gigi .station-number { color: var(--ungu); }

    .station-number.number-change { animation: numPop .55s cubic-bezier(.34,1.56,.64,1); }
    @keyframes numPop {
      0%   { transform: scale(.68); opacity: 0; }
      65%  { transform: scale(1.09); }
      100% { transform: scale(1); opacity: 1; }
    }

    /* ── Number underline ── */
    .num-underline {
      height: 4px; border-radius: 99px;
      margin: 10px auto 0;
      position: relative; z-index: 1;
    }
    .st-resepsionis .num-underline { width: 45%; background: linear-gradient(90deg, #3b82f6, #a78bfa, #3b82f6); opacity:.7; margin: 14px auto 0; }
    .st-poli-umum   .num-underline { width: 55%; background: linear-gradient(90deg, #1d4ed8, #93c5fd); }
    .st-poli-kia    .num-underline { width: 55%; background: linear-gradient(90deg, var(--pink2), #f9a8d4); }
    .st-poli-gigi   .num-underline { width: 55%; background: linear-gradient(90deg, var(--ungu2), #c4b5fd); }

    /* ── Patient name ── */
    .station-name {
      font-family: 'Sora', sans-serif; font-weight: 700;
      transition: all .3s ease;
      position: relative; z-index: 1;
    }
    .st-resepsionis .station-name { font-size: clamp(16px, 2vw, 26px); color: rgba(255,255,255,.92); margin-top: 16px; }
    .poli-card      .station-name { font-size: clamp(12px, 1.3vw, 17px); color: var(--teks); margin-top: 8px; }

    /* ── Instruction ── */
    .station-instruction {
      font-weight: 500; position: relative; z-index: 1; margin-top: 8px;
    }
    .st-resepsionis .station-instruction { font-size: 14px; color: rgba(255,255,255,.55); }
    .poli-card      .station-instruction { font-size: 11px; color: var(--terang); }

    /* ── No-call state ── */
    .no-call-state {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center; gap: 10px;
      position: relative; z-index: 1;
    }
    .no-call-icon {
      border-radius: 20px;
      display: flex; align-items: center; justify-content: center;
    }
    .st-resepsionis .no-call-icon {
      width: 80px; height: 80px;
      background: rgba(255,255,255,.08);
      border: 1.5px solid rgba(255,255,255,.12);
      font-size: 32px; color: rgba(255,255,255,.4);
      margin-bottom: 4px;
    }
    .poli-card .no-call-icon {
      width: 48px; height: 48px; font-size: 20px;
    }
    .st-poli-umum .no-call-icon { background:#eff6ff; color:var(--biru); }
    .st-poli-kia  .no-call-icon { background:#fdf2f8; color:var(--pink); }
    .st-poli-gigi .no-call-icon { background:#f5f3ff; color:var(--ungu); }

    .no-call-text { font-weight: 700; }
    .st-resepsionis .no-call-text { font-size: 18px; color: rgba(255,255,255,.7); }
    .poli-card      .no-call-text { font-size: 13px; color: var(--terang); }
    .no-call-sub { font-weight: 500; }
    .st-resepsionis .no-call-sub { font-size: 13px; color: rgba(255,255,255,.38); }
    .poli-card      .no-call-sub { font-size: 11px; color: var(--abu); }

    /* Poli name shown in empty state */
    .poli-name-empty {
      font-weight: 800; font-size: 14px; letter-spacing: .3px;
    }
    .st-poli-umum .poli-name-empty { color: var(--biru); }
    .st-poli-kia  .poli-name-empty { color: var(--pink); }
    .st-poli-gigi .poli-name-empty { color: var(--ungu); }

    /* Step indicator (resepsionis bottom-right) */
    .step-indicator {
      position: absolute; bottom: 14px; right: 18px;
      font-size: 9.5px; font-weight: 700;
      color: rgba(255,255,255,.28);
      letter-spacing: .5px;
      display: flex; align-items: center; gap: 5px;
    }
    .step-indicator i { font-size: 8px; }

    /* Divider line on poli cards (between label and number) */
    .poli-divider {
      width: 32px; height: 2px; border-radius: 99px; margin: 0 auto 4px;
      flex-shrink: 0;
    }
    .st-poli-umum .poli-divider { background: linear-gradient(90deg, #bfdbfe, #3b82f6); }
    .st-poli-kia  .poli-divider { background: linear-gradient(90deg, #f9a8d4, var(--pink)); }
    .st-poli-gigi .poli-divider { background: linear-gradient(90deg, #ddd6fe, var(--ungu)); }

    /* ══════════════════════════════════
       STATS CHIPS
    ══════════════════════════════════ */
    .stat-chip {
      background: var(--putih);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 10px 16px;
      text-align: center;
      position: relative; overflow: hidden;
      transition: transform .22s, box-shadow .22s;
      display: flex; align-items: center; justify-content: center; gap: 14px;
    }
    .stat-chip::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
    .stat-chip.chip-total::before { background: linear-gradient(90deg, var(--biru), #6366f1); }
    .stat-chip.chip-wait::before  { background: linear-gradient(90deg, var(--kuning), #ef4444); }
    .stat-chip.chip-done::before  { background: linear-gradient(90deg, var(--hijau), #06b6d4); }
    .stat-chip:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(30,58,138,.11); }

    .stat-icon {
      width: 36px; height: 36px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; flex-shrink: 0;
    }
    .chip-total .stat-icon { background: #eff6ff; color: var(--biru); }
    .chip-wait  .stat-icon { background: #fffbeb; color: var(--kuning); }
    .chip-done  .stat-icon { background: #ecfdf5; color: var(--hijau); }

    .stat-info { text-align: left; }
    .stat-chip .val { font-family:'Sora',sans-serif; font-size:28px; font-weight:900; line-height:1; letter-spacing:-1.5px; }
    .stat-chip .lbl { font-size:9.5px; font-weight:700; color:var(--abu); text-transform:uppercase; letter-spacing:1.2px; margin-top:2px; white-space:nowrap; }
    .val-total   { color: var(--biru); }
    .val-waiting { color: var(--kuning); }
    .val-done    { color: var(--hijau); }

    /* ══════════════════════════════════
       CALL TOAST
    ══════════════════════════════════ */
    .call-toast {
      position: fixed; top: 78px; left: 50%;
      transform: translateX(-50%) translateY(-130px);
      z-index: 50;
      background: linear-gradient(135deg, #0a1628, #0f2251);
      border: 1.5px solid rgba(96,165,250,.25);
      border-radius: 18px; padding: 13px 26px;
      display: flex; align-items: center; gap: 14px;
      box-shadow: 0 20px 60px rgba(0,0,0,.28), 0 0 0 1px rgba(96,165,250,.08);
      transition: transform .5s cubic-bezier(.34,1.56,.64,1), opacity .3s;
      opacity: 0; min-width: 280px;
    }
    .call-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast-icon { width:40px;height:40px;background:rgba(255,255,255,.1);border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;color:#93c5fd;flex-shrink:0; }
    .toast-num  { font-family:'Sora',sans-serif;font-size:24px;font-weight:900;color:#bfdbfe;line-height:1; }
    .toast-sub  { font-size:11px;color:rgba(255,255,255,.6);font-weight:600;margin-top:2px; }

    /* ══════════════════════════════════
       TICKER
    ══════════════════════════════════ */
    .ticker-bar {
      position: fixed; bottom:0; left:0; right:0;
      background: linear-gradient(90deg, #060e1e 0%, #0f172a 40%, #1e3a8a 100%);
      border-top: 1.5px solid rgba(96,165,250,.15);
      z-index: 15; overflow: hidden;
      display: flex; align-items: center; height: 40px;
    }
    .ticker-prefix {
      background: linear-gradient(135deg, var(--biru), #6366f1);
      padding: 0 18px; height: 100%;
      font-size: 9.5px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;
      color: #fff; white-space: nowrap;
      display: flex; align-items: center; gap: 7px; flex-shrink: 0;
    }
    .ticker-scroll { overflow: hidden; flex: 1; }
    .ticker-content { display: inline-flex; align-items: center; white-space: nowrap; animation: ticker 42s linear infinite; }
    @keyframes ticker { from{transform:translateX(100vw);} to{transform:translateX(-100%);} }
    .ticker-item { font-size: 12.5px; font-weight: 500; color: rgba(255,255,255,.78); padding: 0 28px; }
    .ticker-sep  { color: rgba(255,255,255,.22); font-size: 13px; }

    /* ══════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════ */
    @media (max-width: 1024px) {
      .cards-area { grid-template-columns: 1fr; grid-template-rows: 1fr auto; }
      .poli-col { flex-direction: row; }
      .poli-col .station-card { min-width: 0; }
      .st-resepsionis .station-number { font-size: clamp(72px, 12vw, 140px); }
    }
    @media (max-width: 768px) {
      .main-grid { padding: 8px 10px 46px; gap: 8px; }
      .header-bar { padding: 0 14px; height: 56px; }
      .poli-col { flex-direction: column; }
      .stat-chip { flex-direction: column; gap: 4px; padding: 8px 10px; }
      .stat-icon { display: none; }
    }
  </style>
</head>
<body>

  <!-- ══ CALL TOAST ══ -->
  <div class="call-toast" id="callToast">
    <div class="toast-icon"><i class="fas fa-bullhorn"></i></div>
    <div>
      <div class="toast-num" id="toastNumber">—</div>
      <div class="toast-sub" id="toastSub">Silakan menuju loket</div>
    </div>
  </div>

  <!-- ══ HEADER ══ -->
  <header class="header-bar">
    <div class="logo-wrap">
      <div class="logo-icon">
        <img src="{{ asset('favicon.png') }}" alt="QLINICA">
      </div>
      <div>
        <div class="logo-text">QLINICA</div>
        <div class="logo-sub">Sistem Antrian Digital</div>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:16px;position:relative;">
      <div class="header-right">
        <div class="header-clock" id="liveClock">--:--:--</div>
        <div class="header-date"  id="liveDate">—</div>
      </div>
      <div style="position:relative;">
        <button class="sound-toggle" id="soundToggle" title="Toggle Suara" onclick="toggleSound(); toggleVoicePanel();">
          <i class="fas fa-volume-up" id="soundIcon"></i>
        </button>
        <div id="voicePanelWrap" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:300px;
             background:#0a1628;border:1px solid rgba(96,165,250,.2);border-radius:14px;
             padding:12px;z-index:999;box-shadow:0 16px 48px rgba(0,0,0,.45);max-height:250px;overflow-y:auto;">
          <div id="voiceStatus" style="font-size:11px;font-weight:700;margin-bottom:8px;padding-bottom:8px;
               border-bottom:1px solid rgba(255,255,255,.08);color:#94a3b8;">Memuat voice...</div>
          <div id="voicePanel" style="color:rgba(255,255,255,.7);"></div>
          <div style="font-size:10px;color:#475569;margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,.07);">
            🟢 = voice Indonesia/Melayu &nbsp;|&nbsp; Abu = bahasa lain
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ══ MAIN GRID ══ -->
  <div class="main-grid">

    <!-- ──────────────────────────────────────────
         CARDS AREA: Resepsionis (kiri) + Poli (kanan)
    ─────────────────────────────────────────────── -->
    <div class="cards-area">

      <!-- ═══════════════════════════
           RESEPSIONIS — hero card (kiri, penuh)
      ═══════════════════════════ -->
      <div class="resepsionis-wrap">
        <div class="station-card st-resepsionis" id="cardResepsionis">
          <span class="wm">R</span>

          <!-- No-call state -->
          <div id="noCallResepsionis" class="no-call-state">
            <div class="no-call-icon"><i class="fas fa-user-clock"></i></div>
            <div class="no-call-text">Belum ada antrian dipanggil</div>
            <div class="no-call-sub">Menunggu petugas memanggil nomor ke Resepsionis...</div>
          </div>

          <!-- Active call state -->
          <div id="callResepsionis" style="display:none;flex-direction:column;align-items:center;text-align:center;position:relative;z-index:1;">
            <div class="station-label">
              <span class="live-dot"></span>
              <span>SEDANG DIPANGGIL &nbsp;·&nbsp; LOKET RESEPSIONIS</span>
            </div>
            <div class="station-number" id="numResepsionis">—</div>
            <div class="num-underline"></div>
            <div class="station-name" id="nameResepsionis">—</div>
            <div class="station-instruction">Silakan menuju <strong style="color:rgba(255,255,255,.8);">Meja Resepsionis</strong> untuk pendaftaran</div>
          </div>

          <div class="step-indicator">
            <i class="fas fa-circle-info"></i> Langkah 1 dari 2 &nbsp;·&nbsp; Pendaftaran &amp; Pemeriksaan TTV
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════
           POLI COLUMN — 3 cards stacked (kanan)
      ═══════════════════════════ -->
      <div class="poli-col">

        <!-- Poli Umum -->
        <div class="station-card poli-card st-poli-umum" id="cardPoliUmum">
          <span class="wm">U</span>
          <!-- No-call -->
          <div id="noCallPoliUmum" class="no-call-state">
            <div class="no-call-icon"><i class="fas fa-stethoscope"></i></div>
            <div class="poli-name-empty">Poli Umum</div>
            <div class="no-call-sub">Menunggu panggilan...</div>
          </div>
          <!-- Active -->
          <div id="callPoliUmum" style="display:none;flex-direction:column;align-items:center;text-align:center;width:100%;position:relative;z-index:1;">
            <div class="station-label">
              <span class="live-dot"></span>
              <i class="fas fa-stethoscope" style="font-size:9px;"></i>
              <span>POLI UMUM</span>
            </div>
            <div class="poli-divider"></div>
            <div class="station-number" id="numPoliUmum">—</div>
            <div class="num-underline"></div>
            <div class="station-name" id="namePoliUmum">—</div>
            <div class="station-instruction">Harap menuju <strong>Poli Umum</strong></div>
          </div>
        </div>

        <!-- Poli KIA -->
        <div class="station-card poli-card st-poli-kia" id="cardPoliKia">
          <span class="wm">K</span>
          <!-- No-call -->
          <div id="noCallPoliKia" class="no-call-state">
            <div class="no-call-icon"><i class="fas fa-baby"></i></div>
            <div class="poli-name-empty">Poli KIA</div>
            <div class="no-call-sub">Menunggu panggilan...</div>
          </div>
          <!-- Active -->
          <div id="callPoliKia" style="display:none;flex-direction:column;align-items:center;text-align:center;width:100%;position:relative;z-index:1;">
            <div class="station-label">
              <span class="live-dot"></span>
              <i class="fas fa-baby" style="font-size:9px;"></i>
              <span>POLI KIA</span>
            </div>
            <div class="poli-divider"></div>
            <div class="station-number" id="numPoliKia">—</div>
            <div class="num-underline"></div>
            <div class="station-name" id="namePoliKia">—</div>
            <div class="station-instruction">Harap menuju <strong>Poli KIA</strong></div>
          </div>
        </div>

        <!-- Poli Gigi -->
        <div class="station-card poli-card st-poli-gigi" id="cardPoliGigi">
          <span class="wm">G</span>
          <!-- No-call -->
          <div id="noCallPoliGigi" class="no-call-state">
            <div class="no-call-icon"><i class="fas fa-tooth"></i></div>
            <div class="poli-name-empty">Poli Gigi</div>
            <div class="no-call-sub">Menunggu panggilan...</div>
          </div>
          <!-- Active -->
          <div id="callPoliGigi" style="display:none;flex-direction:column;align-items:center;text-align:center;width:100%;position:relative;z-index:1;">
            <div class="station-label">
              <span class="live-dot"></span>
              <i class="fas fa-tooth" style="font-size:9px;"></i>
              <span>POLI GIGI</span>
            </div>
            <div class="poli-divider"></div>
            <div class="station-number" id="numPoliGigi">—</div>
            <div class="num-underline"></div>
            <div class="station-name" id="namePoliGigi">—</div>
            <div class="station-instruction">Harap menuju <strong>Poli Gigi</strong></div>
          </div>
        </div>

      </div><!-- /poli-col -->
    </div><!-- /cards-area -->

    <!-- ──────────────────────────────────────────
         STATS ROW
    ─────────────────────────────────────────────── -->
    <!-- <div class="stats-row">
      <div class="stat-chip chip-total">
        <div class="stat-icon"><i class="fas fa-list-ol"></i></div>
        <div class="stat-info">
          <div class="val val-total" id="statTotal">0</div>
          <div class="lbl">Total Antrian</div>
        </div>
      </div>
      <div class="stat-chip chip-wait">
        <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="stat-info">
          <div class="val val-waiting" id="statWaiting">0</div>
          <div class="lbl">Menunggu</div>
        </div>
      </div>
      <div class="stat-chip chip-done">
        <div class="stat-icon"><i class="fas fa-circle-check"></i></div>
        <div class="stat-info">
          <div class="val val-done" id="statDone">0</div>
          <div class="lbl">Selesai</div>
        </div>
      </div>
    </div> -->

  </div><!-- /main-grid -->

  <!-- ══ TICKER ══ -->
  <div class="ticker-bar">
    <div class="ticker-prefix">
      <i class="fas fa-broadcast-tower"></i> INFO
    </div>
    <div class="ticker-scroll">
      <div class="ticker-content">
        <span class="ticker-item">
          Selamat datang di QLINICA &nbsp;·&nbsp; Alur antrian: (1) Daftar di <strong style="color:#fff;">Resepsionis</strong> → (2) Menuju <strong style="color:#fff;">Poli</strong> tujuan Anda
        </span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-item">
          Poli tersedia: <strong style="color:#93c5fd;">Poli Umum</strong> &nbsp;|&nbsp; <strong style="color:#f9a8d4;">Poli KIA</strong> (Kesehatan Ibu &amp; Anak) &nbsp;|&nbsp; <strong style="color:#c4b5fd;">Poli Gigi</strong>
        </span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-item">
          Jam Pelayanan: Senin – Jumat 07.30 – 16.00 &nbsp;|&nbsp; Sabtu 07.30 – 12.00 &nbsp;·&nbsp; Pastikan membawa kartu identitas dan kartu BPJS
        </span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-item">
          Perhatikan layar antrian secara berkala &nbsp;·&nbsp; Terima kasih telah mempercayakan kesehatan Anda kepada kami
        </span>
      </div>
    </div>
  </div>


<script>
/* ══════════════════════════════════
   CLOCK
══════════════════════════════════ */
function updateClock() {
  const now = new Date();
  document.getElementById('liveClock').textContent =
    String(now.getHours()).padStart(2,'0') + ':' +
    String(now.getMinutes()).padStart(2,'0') + ':' +
    String(now.getSeconds()).padStart(2,'0');
  const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  document.getElementById('liveDate').textContent =
    days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
}
updateClock(); setInterval(updateClock, 1000);

/* ══════════════════════════════════
   SOUND TOGGLE
══════════════════════════════════ */
let soundEnabled = true;
let isFirstLoad  = true;

function toggleSound() {
  soundEnabled = !soundEnabled;
  document.getElementById('soundIcon').className = soundEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
  document.getElementById('soundToggle').classList.toggle('muted', !soundEnabled);
}

/* ══════════════════════════════════
   TTS — BAHASA INDONESIA
══════════════════════════════════ */
let ttsVoice = null, voiceLocked = false, hasNativeID = false;
const DIGIT_ID = ['nol','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan'];

function digitToIndo(s) {
  return s.toString().split('').map(d => DIGIT_ID[+d] || d).join(', ');
}

function loadVoice() {
  if (voiceLocked) return;
  const voices = window.speechSynthesis.getVoices();
  if (!voices?.length) return;
  updateVoicePanel(voices);
  const cands = [
    voices.find(v => v.lang==='id-ID' && /female|wanita|perempuan|woman/i.test(v.name)),
    voices.find(v => /google bahasa indonesia/i.test(v.name)),
    voices.find(v => /microsoft.*indonesian|indonesian.*microsoft/i.test(v.name)),
    voices.find(v => v.lang==='id-ID'),
    voices.find(v => v.lang.startsWith('id-')),
    voices.find(v => v.lang.startsWith('id')),
    voices.find(v => v.lang==='ms-MY'),
    voices.find(v => v.lang.startsWith('ms')),
  ];
  const picked = cands.find(v => v) || null;
  if (picked) {
    ttsVoice = picked; hasNativeID = true; voiceLocked = true;
    updateVoiceStatus('✅ ' + picked.name + ' (' + picked.lang + ')', '#10b981');
  } else {
    ttsVoice = null; hasNativeID = false; voiceLocked = true;
    const fb = voices.find(v => v.default) || voices[0];
    updateVoiceStatus('⚠️ Fallback: ' + (fb?.name||'default'), '#f59e0b');
  }
}

function updateVoicePanel(voices) {
  const p = document.getElementById('voicePanel');
  if (!p) return;
  p.innerHTML = '<div style="font-size:10px;font-weight:800;letter-spacing:1px;color:#475569;margin-bottom:5px;">VOICES ('+ voices.length +')</div>' +
    voices.map(v =>
      '<div style="padding:3px 0;border-bottom:1px solid rgba(255,255,255,.06);">' +
      '<span style="color:'+(v.lang.startsWith('id')||v.lang.startsWith('ms')?'#34d399':'#475569')+';font-size:10px;">['+v.lang+'] </span>' +
      '<span style="font-size:11px;">'+v.name+(v.default?' ★':'')+'</span></div>'
    ).join('');
}
function updateVoiceStatus(msg, color) {
  const el = document.getElementById('voiceStatus');
  if (el) { el.textContent = msg; el.style.color = color; }
}

let voicePanelOpen = false;
function toggleVoicePanel() {
  voicePanelOpen = !voicePanelOpen;
  const w = document.getElementById('voicePanelWrap');
  if (w) w.style.display = voicePanelOpen ? 'block' : 'none';
  if (voicePanelOpen) updateVoicePanel(window.speechSynthesis.getVoices());
}

if ('speechSynthesis' in window) {
  window.speechSynthesis.getVoices();
  window.speechSynthesis.onvoiceschanged = () => { if (!voiceLocked) loadVoice(); };
  setTimeout(loadVoice, 400);
  setTimeout(loadVoice, 1600);
  setTimeout(loadVoice, 3200);
}

/* ══════════════════════════════════
   ANNOUNCEMENT QUEUE
   (satu per satu, tidak tumpang tindih)
══════════════════════════════════ */
const annQueue = [];
let isAnnouncing = false;

function queueAnnouncement(no, nama, tts) {
  annQueue.push({ no, nama, tts });
  if (!isAnnouncing) nextAnnouncement();
}

function nextAnnouncement() {
  if (!annQueue.length) { isAnnouncing = false; return; }
  isAnnouncing = true;
  const { no, nama, tts } = annQueue.shift();
  speakAnnouncement(no, nama, tts);
}

function speakAnnouncement(no, nama, destination) {
  if (!soundEnabled || !('speechSynthesis' in window)) { setTimeout(nextAnnouncement, 300); return; }
  window.speechSynthesis.cancel();
  const dig = hasNativeID ? no.split('').join(', ') : digitToIndo(no);
  const txt =
    'Perhatian. Nomor antrian, ' + dig + '. Nomor antrian, ' + dig + '. ' +
    'Dimohon kepada pasien atas nama ' + (nama||'yang bersangkutan') +
    ', harap segera menuju ' + destination + '. Terima kasih.';
  setTimeout(() => {
    const u = new SpeechSynthesisUtterance(txt);
    u.rate = 0.88; u.pitch = 1.1; u.volume = 1;
    if (hasNativeID && ttsVoice) { u.voice = ttsVoice; u.lang = ttsVoice.lang; }
    u.onend   = () => setTimeout(nextAnnouncement, 900);
    u.onerror = () => setTimeout(nextAnnouncement, 400);
    window.speechSynthesis.speak(u);
  }, 260);
}

function showCallToast(no, dest) {
  document.getElementById('toastNumber').textContent = no;
  document.getElementById('toastSub').textContent    = 'Harap menuju ' + dest;
  const t = document.getElementById('callToast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 6000);
}

/* ══════════════════════════════════
   PER-STATION CONFIG
══════════════════════════════════ */
const STATIONS = {
  resepsionis: { cardId:'cardResepsionis', noCallId:'noCallResepsionis', callId:'callResepsionis', numId:'numResepsionis', nameId:'nameResepsionis', displayDest:'Loket Resepsionis', ttsDest:'loket pendaftaran' },
  poli_umum:   { cardId:'cardPoliUmum',    noCallId:'noCallPoliUmum',    callId:'callPoliUmum',    numId:'numPoliUmum',    nameId:'namePoliUmum',    displayDest:'Poli Umum',          ttsDest:'Poli Umum' },
  poli_kia:    { cardId:'cardPoliKia',     noCallId:'noCallPoliKia',     callId:'callPoliKia',     numId:'numPoliKia',     nameId:'namePoliKia',     displayDest:'Poli KIA',           ttsDest:'Poli K. I. A.' },
  poli_gigi:   { cardId:'cardPoliGigi',    noCallId:'noCallPoliGigi',    callId:'callPoliGigi',    numId:'numPoliGigi',    nameId:'namePoliGigi',    displayDest:'Poli Gigi',          ttsDest:'Poli Gigi' },
};

const stKeys = { resepsionis:null, poli_umum:null, poli_kia:null, poli_gigi:null };

function renderStation(stId, data) {
  const cfg    = STATIONS[stId];
  const card   = document.getElementById(cfg.cardId);
  const noCall = document.getElementById(cfg.noCallId);
  const call   = document.getElementById(cfg.callId);
  const numEl  = document.getElementById(cfg.numId);
  const nameEl = document.getElementById(cfg.nameId);

  if (!data) {
    if (noCall) noCall.style.display = '';
    if (call)   call.style.display   = 'none';
    stKeys[stId] = null;
    return;
  }

  const key = data.no_antrian + '|' + (data.updated_at || '0');
  if (stKeys[stId] !== key) {
    if (!isFirstLoad) {
      if (numEl)  { numEl.classList.remove('number-change'); void numEl.offsetWidth; numEl.classList.add('number-change'); }
      if (card)   { card.classList.remove('flash-animate');  void card.offsetWidth;  card.classList.add('flash-animate');  }
      queueAnnouncement(data.no_antrian, data.nama, cfg.ttsDest);
      showCallToast(data.no_antrian, cfg.displayDest);
    }
    stKeys[stId] = key;
  }

  if (numEl)  numEl.textContent  = data.no_antrian;
  if (nameEl) nameEl.textContent = data.nama || '—';
  if (noCall) noCall.style.display = 'none';
  if (call)   call.style.display   = 'flex';
}

function renderStats(data) {
  const g = id => document.getElementById(id);
  if (g('statTotal'))   g('statTotal').textContent   = data.total   ?? 0;
  if (g('statWaiting')) g('statWaiting').textContent = data.menunggu ?? 0;
  if (g('statDone'))    g('statDone').textContent    = data.selesai  ?? 0;
}

/* ══════════════════════════════════
   POLLING
══════════════════════════════════ */
function fetchDisplayData() {
  fetch('{{ route("antrian.display.data") }}', {
    headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    const s = data.stations || {};
    renderStation('resepsionis', s.resepsionis || null);
    renderStation('poli_umum',   s.poli_umum   || null);
    renderStation('poli_kia',    s.poli_kia    || null);
    renderStation('poli_gigi',   s.poli_gigi   || null);
    renderStats(data);
    if (isFirstLoad) { isFirstLoad = false; console.log('[Display] Siap — polling aktif.'); }
  })
  .catch(e => console.warn('[Display] Polling error:', e));
}

fetchDisplayData();
setInterval(fetchDisplayData, 3000);
</script>
</body>
</html>
