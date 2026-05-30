<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Display Antrian | QLINICA</title>
  <meta name="description" content="Layar antrian digital QLINICA — tampilkan nomor antrian terkini secara real-time.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Sora:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root {
      --putih:   #ffffff;
      --bg:      #eef2f7;
      --border:  #dde3ee;
      --shadow:  0 4px 24px rgba(15,33,68,.08);
      --shadow-lg: 0 16px 48px rgba(15,33,68,.14);
      --teks:    #1e293b;
      --terang:  #64748b;
      --abu:     #94a3b8;
      --navy:    #1e3a8a;
      --biru:    #2563eb;
      --biru-lt: #dbeafe;
      --ungu:    #7c3aed;
      --hijau:   #10b981;
      --kuning:  #f59e0b;
      --radius:  20px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      height: 100%;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--teks);
      min-height: 100vh;
      overflow-x: hidden;
      /* Subtle dot grid background */
      background-image: radial-gradient(circle, #c8d5ee 1px, transparent 1px);
      background-size: 28px 28px;
    }

    /* ══ HEADER ══ */
    .header-bar {
      background: var(--putih);
      border-bottom: 2px solid var(--border);
      box-shadow: 0 2px 20px rgba(30,58,138,.07);
      padding: 0 40px;
      height: 72px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 20;
    }
    .logo-wrap {
      display: flex; align-items: center; gap: 14px;
    }
    .logo-icon {
      width: 46px; height: 46px;
      background: linear-gradient(135deg, var(--navy), var(--biru));
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 6px 18px rgba(30,58,138,.35);
    }
    .logo-text {
      font-family: 'Sora', sans-serif;
      font-size: 22px; font-weight: 900;
      color: var(--navy); letter-spacing: -0.5px;
    }
    .logo-sub {
      font-size: 10px; color: var(--abu);
      font-weight: 600; letter-spacing: 1.2px;
      text-transform: uppercase; margin-top: 2px;
    }

    /* clock */
    .header-right { text-align: right; }
    .header-clock {
      font-family: 'Sora', sans-serif;
      font-size: 30px; font-weight: 900;
      color: var(--navy); letter-spacing: -1.5px;
      line-height: 1;
    }
    .header-date {
      font-size: 12px; color: var(--terang); margin-top: 3px; font-weight: 500;
    }

    /* sound toggle */
    .sound-toggle {
      width: 42px; height: 42px;
      border-radius: 13px;
      background: var(--biru-lt);
      border: 1.5px solid #bfdbfe;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all .2s;
      color: var(--biru); font-size: 16px;
    }
    .sound-toggle:hover { background: #bfdbfe; transform: scale(1.08); }
    .sound-toggle.muted { background: #f1f5f9; border-color: var(--border); color: var(--abu); }

    /* ══ MAIN LAYOUT ══ */
    .main-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 22px;
      padding: 22px 40px 90px;
      min-height: calc(100vh - 72px);
      align-items: start;
    }

    /* ══ LEFT ══ */
    .current-section {
      display: flex; flex-direction: column; gap: 18px;
    }

    .section-label {
      font-size: 11px; font-weight: 700;
      color: var(--abu);
      text-transform: uppercase; letter-spacing: 2.5px;
      display: flex; align-items: center; gap: 8px;
    }
    .section-label i { color: var(--biru); }

    /* ══ Called Card — BESAR untuk TV ══ */
    .called-card {
      background: var(--putih);
      border: 1.5px solid var(--border);
      border-radius: 28px;
      box-shadow: var(--shadow-lg);
      padding: 52px 64px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      min-height: 420px;
      position: relative;
      overflow: hidden;
      transition: box-shadow .3s;
    }

    /* Accent top bar gradient */
    .called-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 6px;
      background: linear-gradient(90deg, var(--navy) 0%, var(--biru) 50%, #6366f1 100%);
    }

    /* Watermark latar */
    .called-card::after {
      content: '+';
      position: absolute;
      right: -24px; bottom: -24px;
      font-size: 260px; font-weight: 900;
      color: rgba(37,99,235,.04);
      font-family: 'Sora', sans-serif;
      line-height: 1;
      pointer-events: none;
      user-select: none;
    }

    .called-card.flash-animate {
      animation: flashCard .75s ease;
    }
    @keyframes flashCard {
      0%   { box-shadow: 0 0 0 0 rgba(37,99,235,.55); }
      45%  { box-shadow: 0 0 0 22px rgba(37,99,235,0); border-color: rgba(37,99,235,.5); }
      100% { box-shadow: var(--shadow-lg); border-color: var(--border); }
    }

    /* Live badge */
    .called-label {
      font-size: 11px; font-weight: 800;
      text-transform: uppercase; letter-spacing: 3.5px;
      color: var(--terang);
      margin-bottom: 24px;
      display: flex; align-items: center; gap: 10px;
    }
    .live-dot {
      width: 9px; height: 9px;
      background: var(--hijau);
      border-radius: 50%;
      animation: blink 1.4s infinite;
      flex-shrink: 0;
      box-shadow: 0 0 0 3px rgba(16,185,129,.2);
    }
    @keyframes blink { 0%,100%{opacity:1;box-shadow:0 0 0 3px rgba(16,185,129,.2);} 50%{opacity:.3;box-shadow:0 0 0 6px rgba(16,185,129,.05);} }

    /* ══ Nomor besar TV-friendly ══ */
    .called-number {
      font-family: 'Sora', sans-serif;
      font-size: clamp(100px, 16vw, 176px);
      font-weight: 900;
      line-height: 1;
      letter-spacing: -8px;
      color: var(--navy);
      position: relative;
      transition: all .3s cubic-bezier(.4,0,.2,1);
    }
    .called-number.number-change {
      animation: numberPop .5s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes numberPop {
      0%   { transform: scale(.78); opacity: 0; }
      60%  { transform: scale(1.07); }
      100% { transform: scale(1); opacity: 1; }
    }
    /* Garis aksen bawah nomor */
    .number-underline {
      width: 55%; height: 6px;
      background: linear-gradient(90deg, var(--biru), #6366f1);
      border-radius: 99px;
      margin: 16px auto 0;
    }

    .called-name {
      font-family: 'Sora', sans-serif;
      font-size: 26px; font-weight: 700;
      color: var(--teks);
      margin-top: 22px;
      min-height: 36px;
      transition: all .3s ease;
    }
    .called-layanan {
      font-size: 15px;
      color: var(--terang);
      margin-top: 8px;
      min-height: 24px;
      font-weight: 500;
    }

    /* Poli badge */
    .poli-badge {
      display: inline-flex; align-items: center; gap: 10px;
      background: #eff6ff;
      border: 1.5px solid #bfdbfe;
      color: var(--navy);
      border-radius: 99px;
      padding: 10px 24px;
      font-size: 15px; font-weight: 700;
      margin-top: 22px;
    }
    .poli-badge i { font-size: 14px; }

    /* No-queue state */
    .no-queue-state {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      gap: 16px;
    }
    .no-queue-icon {
      width: 96px; height: 96px;
      background: linear-gradient(135deg, #f0f4f8, #e2e8f0);
      border-radius: 28px;
      display: flex; align-items: center; justify-content: center;
      font-size: 40px; color: var(--abu);
      box-shadow: 0 4px 16px rgba(0,0,0,.06);
    }
    .no-queue-text { color: var(--terang); font-size: 18px; font-weight: 700; }
    .no-queue-sub  { color: var(--abu); font-size: 14px; font-weight: 500; }

    /* ══ STATS ROW ══ */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
    }
    .stat-chip {
      background: var(--putih);
      border: 1.5px solid var(--border);
      border-radius: 20px;
      box-shadow: var(--shadow);
      padding: 24px 18px 20px;
      text-align: center;
      transition: transform .22s, box-shadow .22s;
      position: relative; overflow: hidden;
    }
    .stat-chip::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0; height: 4px;
    }
    .stat-chip.chip-total::before { background: linear-gradient(90deg,var(--biru),#6366f1); }
    .stat-chip.chip-wait::before  { background: linear-gradient(90deg,var(--kuning),#ef4444); }
    .stat-chip.chip-done::before  { background: linear-gradient(90deg,var(--hijau),#06b6d4); }
    .stat-chip:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(30,58,138,.12); }

    .stat-chip .val {
      font-family: 'Sora', sans-serif;
      font-size: 48px; font-weight: 900;
      line-height: 1; letter-spacing: -3px;
    }
    .stat-chip .lbl {
      font-size: 11px; font-weight: 700;
      color: var(--abu);
      text-transform: uppercase; letter-spacing: 1.5px;
      margin-top: 7px;
    }
    .val-total   { color: var(--biru); }
    .val-waiting { color: var(--kuning); }
    .val-done    { color: var(--hijau); }

    /* ══ RIGHT PANEL ══ */
    .waiting-panel {
      display: flex; flex-direction: column; gap: 14px;
      /* Sticky panel agar tetap kelihatan saat scroll */
      position: sticky;
      top: 90px;
    }
    .panel-card {
      background: var(--putih);
      border: 1.5px solid var(--border);
      border-radius: 22px;
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    .panel-header-inner {
      padding: 18px 20px 16px;
      border-bottom: 1.5px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    }
    .panel-title {
      font-size: 12px; font-weight: 800;
      color: var(--teks);
      text-transform: uppercase; letter-spacing: 1.8px;
      display: flex; align-items: center; gap: 8px;
    }
    .panel-title i { color: var(--biru); }
    .count-badge {
      background: #fffbeb;
      border: 1.5px solid #fde68a;
      color: #92400e;
      font-size: 12px; font-weight: 800;
      padding: 4px 12px; border-radius: 99px;
      min-width: 32px; text-align: center;
    }

    .waiting-list {
      display: flex; flex-direction: column; gap: 0;
      max-height: calc(100vh - 280px);
      overflow-y: auto;
    }
    .waiting-list::-webkit-scrollbar { width: 4px; }
    .waiting-list::-webkit-scrollbar-track { background: transparent; }
    .waiting-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .queue-item {
      padding: 14px 20px;
      display: flex; align-items: center; gap: 14px;
      border-bottom: 1px solid #f0f4f8;
      transition: background .18s;
      animation: slideIn .3s ease;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(12px); }
      to   { opacity: 1; transform: translateX(0); }
    }
    .queue-item:last-child { border-bottom: none; }
    .queue-item:hover { background: #f8fafc; }
    .queue-item.is-called {
      background: linear-gradient(135deg, #eff6ff, #f5f3ff);
      border-left: 4px solid var(--biru);
    }

    .q-num {
      width: 44px; height: 44px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--navy), var(--biru));
      display: flex; align-items: center; justify-content: center;
      font-family: 'Sora', sans-serif;
      font-size: 14px; font-weight: 800;
      color: #fff; flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(37,99,235,.28);
    }
    .q-num.called-num {
      background: linear-gradient(135deg, var(--ungu), #a78bfa);
      box-shadow: 0 4px 16px rgba(124,58,237,.38);
      animation: pulseNum 1.6s infinite;
    }
    @keyframes pulseNum {
      0%,100% { box-shadow: 0 4px 12px rgba(124,58,237,.35); }
      50%      { box-shadow: 0 4px 24px rgba(124,58,237,.65); }
    }
    .q-info { flex: 1; min-width: 0; }
    .q-name {
      font-size: 14px; font-weight: 700;
      color: var(--teks);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .q-status {
      font-size: 11px; font-weight: 600;
      margin-top: 3px; display: flex; align-items: center; gap: 5px;
    }
    .q-status.menunggu  { color: var(--kuning); }
    .q-status.dipanggil { color: var(--ungu); }

    .empty-waiting {
      text-align: center; padding: 48px 20px;
      color: var(--abu); font-size: 14px;
    }
    .empty-waiting i { display: block; font-size: 32px; margin-bottom: 10px; opacity: .3; }

    /* ══ TOAST ══ */
    .call-toast {
      position: fixed;
      top: 88px; left: 50%; transform: translateX(-50%) translateY(-120px);
      z-index: 50;
      background: linear-gradient(135deg, var(--navy), #1e40af);
      border: 1.5px solid rgba(96,165,250,.3);
      border-radius: 22px;
      padding: 16px 30px;
      display: flex; align-items: center; gap: 16px;
      box-shadow: 0 20px 60px rgba(0,0,0,.22);
      transition: transform .48s cubic-bezier(.34,1.56,.64,1), opacity .3s;
      opacity: 0;
      min-width: 320px;
    }
    .call-toast.show {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }
    .toast-icon {
      width: 44px; height: 44px;
      background: rgba(255,255,255,.14);
      border-radius: 13px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #fff; flex-shrink: 0;
    }
    .toast-num  { font-family: 'Sora',sans-serif; font-size: 28px; font-weight: 900; color: #bfdbfe; line-height:1; }
    .toast-sub  { font-size: 12px; color: rgba(255,255,255,.65); font-weight: 600; margin-top: 3px; }

    /* ══ TICKER ══ */
    .ticker-bar {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: linear-gradient(90deg, #0f172a 0%, var(--navy) 100%);
      border-top: 2px solid rgba(96,165,250,.2);
      padding: 11px 0;
      z-index: 15;
      overflow: hidden;
      display: flex; align-items: center;
      height: 44px;
    }
    .ticker-prefix {
      background: var(--biru);
      padding: 0 20px;
      font-size: 10px; font-weight: 800;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: #fff; white-space: nowrap;
      display: flex; align-items: center; gap: 7px;
      height: 100%; flex-shrink: 0;
      align-self: stretch; justify-content: center;
    }
    .ticker-scroll { overflow: hidden; flex: 1; }
    .ticker-content {
      display: inline-flex; align-items: center;
      white-space: nowrap;
      animation: ticker 36s linear infinite;
    }
    @keyframes ticker {
      from { transform: translateX(100vw); }
      to   { transform: translateX(-100%); }
    }
    .ticker-item {
      font-size: 13px; font-weight: 500;
      color: rgba(255,255,255,.82);
      padding: 0 32px;
    }
    .ticker-sep { color: rgba(255,255,255,.28); font-size: 14px; }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 960px) {
      .main-grid { grid-template-columns: 1fr; padding: 16px 20px 90px; }
      .waiting-panel { display: none; }
      .header-bar { padding: 0 20px; }
    }
  </style>
</head>
<body>

  <!-- ══ CALL TOAST ══ -->
  <div class="call-toast" id="callToast">
    <div class="toast-icon"><i class="fas fa-bullhorn"></i></div>
    <div>
      <div class="toast-num" id="toastNumber">—</div>
      <div class="toast-sub" id="toastSub">Silakan menuju loket pendaftaran</div>
    </div>
  </div>

  <!-- ══ HEADER ══ -->
  <header class="header-bar">
    <div class="logo-wrap">
      <div class="logo-icon">
        <i class="fas fa-clinic-medical" style="font-size:20px;color:#fff;"></i>
      </div>
      <div>
        <div class="logo-text">QLINICA</div>
        <div class="logo-sub">Sistem Antrian Digital</div>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:18px;position:relative;">
      <div class="header-right">
        <div class="header-clock" id="liveClock">--:--:--</div>
        <div class="header-date"  id="liveDate">—</div>
      </div>

      <!-- Voice status + debug panel -->
      <div style="position:relative;">
        <button class="sound-toggle" id="soundToggle" title="Toggle Suara / Lihat Voice" onclick="toggleSound(); toggleVoicePanel();">
          <i class="fas fa-volume-up" id="soundIcon"></i>
        </button>
        <!-- Voice panel dropdown -->
        <div id="voicePanelWrap" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;
             background:#0f172a;border:1px solid rgba(96,165,250,.2);border-radius:14px;
             padding:12px 14px;z-index:999;box-shadow:0 16px 48px rgba(0,0,0,.4);max-height:260px;overflow-y:auto;">
          <div id="voiceStatus" style="font-size:11px;font-weight:700;margin-bottom:8px;padding-bottom:8px;
               border-bottom:1px solid rgba(255,255,255,.1);color:#94a3b8;">Memuat voice...</div>
          <div id="voicePanel" style="color:rgba(255,255,255,.75);"></div>
          <div style="font-size:10px;color:#475569;margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,.08);">
            🟢 = voice Indonesia/Melayu &nbsp;|&nbsp; Abu = bahasa lain
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ══ MAIN GRID ══ -->
  <div class="main-grid">

    <!-- ── LEFT ── -->
    <div class="current-section">
      <div class="section-label">
        <i class="fas fa-bullhorn"></i> Nomor Yang Sedang Dipanggil
      </div>

      <!-- Called Card -->
      <div class="called-card" id="calledCard">
        <!-- No queue state -->
        <div id="noQueueState" class="no-queue-state" style="display:none;">
          <div class="no-queue-icon"><i class="fas fa-hourglass-start"></i></div>
          <div class="no-queue-text">Belum ada antrian dipanggil</div>
          <div class="no-queue-sub">Menunggu petugas memanggil nomor antrian...</div>
        </div>

        <!-- Content -->
        <div id="calledContent">
          <div class="called-label">
            <span class="live-dot"></span>SEDANG DIPANGGIL
          </div>
          <div class="called-number" id="calledNumber">—</div>
          <div class="number-underline" id="numberUnderline"></div>
          <div class="called-name"    id="calledName">—</div>
          <div class="called-layanan" id="calledLayanan">Silakan menuju loket pendaftaran</div>
          <div id="calledPoliWrap" style="display:none;">
            <span class="poli-badge" id="calledPoliBadge">
              <i id="calledPoliIcon" class="fas fa-hospital-alt"></i>
              <span id="calledPoliName">—</span>
            </span>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-chip chip-total">
          <div class="val val-total" id="statTotal">0</div>
          <div class="lbl">Total Antrian</div>
        </div>
        <div class="stat-chip chip-wait">
          <div class="val val-waiting" id="statWaiting">0</div>
          <div class="lbl">Menunggu</div>
        </div>
        <div class="stat-chip chip-done">
          <div class="val val-done" id="statDone">0</div>
          <div class="lbl">Selesai</div>
        </div>
      </div>
    </div>

    <!-- ── RIGHT PANEL ── -->
    <div class="waiting-panel">
      <div class="panel-card">
        <div class="panel-header-inner">
          <div class="panel-title">
            <i class="fas fa-list-ol"></i> Antrian Menunggu
          </div>
          <div class="count-badge" id="waitingBadge">0</div>
        </div>
        <div class="waiting-list" id="waitingList">
          <div class="empty-waiting" id="emptyWaiting">
            <i class="fas fa-inbox"></i>
            Belum ada antrian hari ini
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ══ TICKER ══ -->
  <div class="ticker-bar">
    <div class="ticker-prefix">
      <i class="fas fa-info-circle"></i> INFO
    </div>
    <div class="ticker-scroll">
      <div class="ticker-content" id="tickerContent">
        <span class="ticker-item">
          Selamat datang di QLINICA &nbsp;•&nbsp; Mohon perhatikan layar antrian &nbsp;•&nbsp; Pastikan Anda membawa kartu identitas dan kartu BPJS jika ada &nbsp;•&nbsp; Terima kasih telah mempercayakan kesehatan Anda kepada kami
        </span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-item">
          Jam Pelayanan: Senin – Jumat 07.30 – 16.00 &nbsp;|&nbsp; Sabtu 07.30 – 12.00 &nbsp;|&nbsp; Hubungi kami: (021) xxx-xxxx
        </span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-item">
          Dengarkan pengumuman dan perhatikan layar antrian digital ini
        </span>
      </div>
    </div>
  </div>

<script>
/* ══════════════════════════════════
   CLOCK
══════════════════════════════════ */
function updateClock() {
  const now  = new Date();
  const hh   = String(now.getHours()).padStart(2,'0');
  const mm   = String(now.getMinutes()).padStart(2,'0');
  const ss   = String(now.getSeconds()).padStart(2,'0');
  document.getElementById('liveClock').textContent = hh + ':' + mm + ':' + ss;

  const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  document.getElementById('liveDate').textContent =
    days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
}
updateClock();
setInterval(updateClock, 1000);

/* ══════════════════════════════════
   SOUND TOGGLE
══════════════════════════════════ */
let soundEnabled = true;
let lastCalledNumber = null;

function toggleSound() {
  soundEnabled = !soundEnabled;
  const icon = document.getElementById('soundIcon');
  const btn  = document.getElementById('soundToggle');
  if (soundEnabled) {
    icon.className = 'fas fa-volume-up';
    btn.classList.remove('muted');
  } else {
    icon.className = 'fas fa-volume-mute';
    btn.classList.add('muted');
  }
}

/* ══════════════════════════════════
   POLI MAPS
══════════════════════════════════ */
const POLI_TTS_MAP = {
  'Poli Umum'    : 'Poli Umum',
  'Poli Gigi'    : 'Poli Gigi',
  'Poli KIA'     : 'Poli K. I. A.',
  'UGD'          : 'Unit Gawat Darurat',
  'Laboratorium' : 'Laboratorium',
  'Baby Spa'     : 'Baby Spa',
};

const POLI_ICON_MAP = {
  'Poli Umum'    : 'fa-stethoscope',
  'Poli Gigi'    : 'fa-tooth',
  'Poli KIA'     : 'fa-baby',
  'UGD'          : 'fa-ambulance',
  'Laboratorium' : 'fa-flask',
  'Baby Spa'     : 'fa-spa',
};

const POLI_COLOR_BG = {
  'Poli Umum'    : '#eff6ff', 'Poli Gigi'    : '#f5f3ff',
  'Poli KIA'     : '#fdf2f8', 'UGD'          : '#fff1f2',
  'Laboratorium' : '#f0fdf4', 'Baby Spa'     : '#fffbeb',
};
const POLI_COLOR_BORDER = {
  'Poli Umum'    : '#bfdbfe', 'Poli Gigi'    : '#ddd6fe',
  'Poli KIA'     : '#f9a8d4', 'UGD'          : '#fecaca',
  'Laboratorium' : '#bbf7d0', 'Baby Spa'     : '#fde68a',
};
const POLI_COLOR_TEXT = {
  'Poli Umum'    : '#1e3a8a', 'Poli Gigi'    : '#4c1d95',
  'Poli KIA'     : '#831843', 'UGD'          : '#7f1d1d',
  'Laboratorium' : '#14532d', 'Baby Spa'     : '#78350f',
};

/* ══════════════════════════════════
   TTS — BAHASA INDONESIA
   Strategi berlapis:
   1. Cari voice id-ID / ms-MY
   2. Jika tidak ada → pakai voice manapun,
      tapi ganti TEKS ke angka Indonesia
      (nol, satu, dua...) agar tetap
      terdengar Indonesia meski logat Inggris
══════════════════════════════════ */
let ttsVoice    = null;
let voiceLocked = false;
let hasNativeID = false; // true jika ada voice id-ID / ms asli

// Angka → kata Indonesia (untuk fallback voice non-ID)
const DIGIT_ID = ['nol','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan'];

function digitToIndo(numStr) {
  return numStr.toString().split('').map(d => DIGIT_ID[parseInt(d)] || d).join(', ');
}

function loadVoice() {
  if (voiceLocked) return;

  const voices = window.speechSynthesis.getVoices();
  if (!voices || voices.length === 0) return;

  // Tampilkan semua voice di panel debug
  updateVoicePanel(voices);

  // ── Cari voice Indonesia/Melayu ──
  const idCandidates = [
    voices.find(v => v.lang === 'id-ID' && /female|wanita|perempuan|woman/i.test(v.name)),
    voices.find(v => /google bahasa indonesia/i.test(v.name)),
    voices.find(v => /microsoft.*indonesian|indonesian.*microsoft/i.test(v.name)),
    voices.find(v => v.lang === 'id-ID'),
    voices.find(v => v.lang.startsWith('id-')),
    voices.find(v => v.lang.startsWith('id')),
    voices.find(v => v.lang === 'ms-MY'),
    voices.find(v => v.lang.startsWith('ms')),
  ];

  const picked = idCandidates.find(v => v !== undefined) || null;

  if (picked) {
    ttsVoice    = picked;
    hasNativeID = true;
    voiceLocked = true;
    console.log('[TTS] ✅ Voice Indonesia ditemukan:', picked.name, picked.lang);
    updateVoiceStatus('✅ ' + picked.name + ' (' + picked.lang + ')', '#10b981');
  } else {
    // Tidak ada voice Indonesia — pakai voice default apa saja
    // tapi ubah teks ke kata Indonesia
    ttsVoice    = null; // biarkan browser pilih default
    hasNativeID = false;
    voiceLocked = true;
    const fallback = voices.find(v => v.default) || voices[0];
    console.warn('[TTS] ⚠️ Tidak ada voice id-ID. Fallback:', fallback?.name);
    updateVoiceStatus('⚠️ Fallback: ' + (fallback?.name || 'default') + ' — teks diubah ke kata Indonesia', '#f59e0b');
  }
}

// ── Panel info voice (klik ikon suara untuk toggle) ──
function updateVoicePanel(voices) {
  const panel = document.getElementById('voicePanel');
  if (!panel) return;
  const list = voices.map(v =>
    '<div style="padding:3px 0;border-bottom:1px solid rgba(255,255,255,.08);">' +
    '<span style="color:' + (v.lang.startsWith('id') || v.lang.startsWith('ms') ? '#34d399' : '#94a3b8') + ';font-size:10px;">' +
    '[' + v.lang + ']</span> ' +
    '<span style="font-size:11px;">' + v.name + (v.default ? ' ★' : '') + '</span>' +
    '</div>'
  ).join('');
  panel.innerHTML = '<div style="font-size:10px;font-weight:800;letter-spacing:1px;color:#64748b;margin-bottom:6px;">VOICES TERSEDIA (' + voices.length + ')</div>' + list;
}

function updateVoiceStatus(msg, color) {
  const el = document.getElementById('voiceStatus');
  if (el) { el.textContent = msg; el.style.color = color; }
}

let voicePanelOpen = false;
function toggleVoicePanel() {
  voicePanelOpen = !voicePanelOpen;
  const wrap = document.getElementById('voicePanelWrap');
  if (wrap) wrap.style.display = voicePanelOpen ? 'block' : 'none';
  // Refresh voices setiap buka panel
  if (voicePanelOpen) updateVoicePanel(window.speechSynthesis.getVoices());
}

if ('speechSynthesis' in window) {
  window.speechSynthesis.getVoices();
  window.speechSynthesis.onvoiceschanged = function() {
    if (!voiceLocked) loadVoice();
  };
  setTimeout(loadVoice, 400);
  setTimeout(loadVoice, 1500);
  setTimeout(loadVoice, 3000);
}

/* ── Pengumuman suara Bahasa Indonesia ── */
function speakAnnouncement(number, name, poli) {
  if (!soundEnabled) return;
  if (!('speechSynthesis' in window)) return;

  window.speechSynthesis.cancel();

  const poliText = poli ? (POLI_TTS_MAP[poli] || poli) : null;
  const numStr   = number.toString();

  let digitized;
  if (hasNativeID) {
    // Voice Indonesia asli → eja digit biasa: "001" → "0, 0, 1"
    // (voice Indonesia bisa baca angka dengan benar)
    digitized = numStr.split('').join(', ');
  } else {
    // Tidak ada voice Indonesia → eja pakai KATA INDONESIA
    // agar voice Inggris pun membaca: "nol, nol, satu"
    digitized = digitToIndo(numStr);
  }

  const text =
    'Perhatian. ' +
    'Nomor antrian, ' + digitized + '. ' +
    'Nomor antrian, ' + digitized + '. ' +
    (poliText
      ? 'Dimohon kepada pasien atas nama ' + (name || 'yang bersangkutan') +
        ', harap segera menuju ' + poliText + '. '
      : 'Dimohon kepada pasien atas nama ' + (name || 'yang bersangkutan') +
        ', harap segera menuju loket pendaftaran. ') +
    'Terima kasih.';

  setTimeout(() => {
    const utter  = new SpeechSynthesisUtterance(text);
    utter.rate   = 0.88;
    utter.pitch  = 1.1;
    utter.volume = 1;

    if (hasNativeID && ttsVoice) {
      // Ada voice Indonesia → pakai dan set lang id-ID
      utter.voice = ttsVoice;
      utter.lang  = ttsVoice.lang;
    }
    // Jika tidak ada native voice: JANGAN set utter.lang
    // Biarkan browser pakai voice default-nya untuk membaca
    // kata-kata Indonesia yang sudah kita tulis

    utter.onstart = () => console.log('[TTS] Speaking dengan voice:', utter.voice?.name || 'default');
    window.speechSynthesis.speak(utter);
  }, 250);
}

function showCallToast(number, name, poli) {
  const toast  = document.getElementById('callToast');
  const subEl  = document.getElementById('toastSub');
  document.getElementById('toastNumber').textContent = 'No. ' + number;
  subEl.textContent = poli
    ? 'Harap menuju ' + poli
    : 'Silakan menuju loket pendaftaran';
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 6500);
}

/* ══════════════════════════════════
   RENDER UI
══════════════════════════════════ */
function renderCalledCard(data) {
  const card      = document.getElementById('calledCard');
  const content   = document.getElementById('calledContent');
  const noState   = document.getElementById('noQueueState');
  const numEl     = document.getElementById('calledNumber');
  const nameEl    = document.getElementById('calledName');
  const layananEl = document.getElementById('calledLayanan');
  const poliWrap  = document.getElementById('calledPoliWrap');
  const poliBadge = document.getElementById('calledPoliBadge');
  const poliIcon  = document.getElementById('calledPoliIcon');
  const poliName  = document.getElementById('calledPoliName');

  if (!data.dilayani) {
    content.style.display = 'none';
    noState.style.display  = 'flex';
    return;
  }

  content.style.display = '';
  noState.style.display  = 'none';

  const poli   = data.dilayani.poli || null;
  const nama   = data.dilayani.nama || '';

  if (lastCalledNumber !== data.dilayani.no_antrian) {
    // Animasi nomor
    numEl.classList.remove('number-change');
    void numEl.offsetWidth;
    numEl.classList.add('number-change');

    // Flash card
    card.classList.remove('flash-animate');
    void card.offsetWidth;
    card.classList.add('flash-animate');

    speakAnnouncement(data.dilayani.no_antrian, nama, poli);
    showCallToast(data.dilayani.no_antrian, nama, poli);

    lastCalledNumber = data.dilayani.no_antrian;
  }

  numEl.textContent  = 'No. ' + data.dilayani.no_antrian;
  nameEl.textContent = nama || '—';

  if (poli) {
    layananEl.textContent = 'Harap menuju ' + poli;
    poliName.textContent  = poli;
    poliIcon.className    = 'fas ' + (POLI_ICON_MAP[poli] || 'fa-hospital-alt');
    poliBadge.style.background   = POLI_COLOR_BG[poli]     || '#eff6ff';
    poliBadge.style.borderColor  = POLI_COLOR_BORDER[poli]  || '#bfdbfe';
    poliBadge.style.color        = POLI_COLOR_TEXT[poli]    || '#1e3a8a';
    poliIcon.style.color         = POLI_COLOR_TEXT[poli]    || '#2563eb';
    poliWrap.style.display = 'block';
  } else {
    layananEl.textContent  = 'Silakan menuju loket pendaftaran';
    poliWrap.style.display = 'none';
  }
}

function renderStats(data) {
  document.getElementById('statTotal').textContent   = data.total   ?? 0;
  document.getElementById('statWaiting').textContent = data.menunggu ?? 0;
  document.getElementById('statDone').textContent    = data.selesai  ?? 0;
  document.getElementById('waitingBadge').textContent = (data.daftar_menunggu ?? []).length;
}

function renderWaitingList(items) {
  const list    = document.getElementById('waitingList');
  const emptyEl = document.getElementById('emptyWaiting');

  if (!items || items.length === 0) {
    emptyEl.style.display = '';
    list.innerHTML = '';
    list.appendChild(emptyEl);
    return;
  }

  emptyEl.style.display = 'none';
  list.innerHTML = '';

  items.forEach(function(item) {
    const isCalled = (item.status ?? '').toLowerCase() === 'dipanggil';
    const poli     = item.poli || null;
    const icon     = poli ? (POLI_ICON_MAP[poli] || 'fa-hospital-alt') : null;

    const div = document.createElement('div');
    div.className = 'queue-item' + (isCalled ? ' is-called' : '');

    const poliBadgeHtml = poli
      ? '<span style="display:inline-flex;align-items:center;gap:4px;background:#f0f4f8;border-radius:99px;padding:2px 9px;font-size:10px;font-weight:700;color:var(--terang);margin-top:4px;">'
        + '<i class="fas ' + icon + '" style="font-size:9px;"></i> ' + poli + '</span>'
      : '';

    div.innerHTML =
      '<div class="q-num' + (isCalled ? ' called-num' : '') + '">' + item.no_antrian + '</div>' +
      '<div class="q-info">' +
        '<div class="q-name">' + (item.nama || '—') + '</div>' +
        '<div class="q-status ' + (isCalled ? 'dipanggil' : 'menunggu') + '">' +
          (isCalled
            ? '<i class="fas fa-bullhorn" style="font-size:10px;"></i> Dipanggil'
            : '<i class="fas fa-clock"   style="font-size:10px;"></i> Menunggu') +
        '</div>' +
        poliBadgeHtml +
      '</div>';
    list.appendChild(div);
  });
}

/* ══════════════════════════════════
   POLLING
══════════════════════════════════ */
const POLL_INTERVAL = 4000;

function fetchDisplayData() {
  fetch('{{ route("antrian.display.data") }}', {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    renderCalledCard(data);
    renderStats(data);
    renderWaitingList(data.daftar_menunggu ?? []);
  })
  .catch(function(err) {
    console.warn('Polling error:', err);
  });
}

fetchDisplayData();
setInterval(fetchDisplayData, POLL_INTERVAL);
</script>
</body>
</html>
