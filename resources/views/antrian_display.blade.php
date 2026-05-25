<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Display Antrian | QLINICA</title>
  <meta name="description" content="Layar antrian digital QLINICA — tampilkan nomor antrian terkini secara real-time.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --putih:   #ffffff;
      --bg:      #f0f4f8;
      --border:  #e2e8f0;
      --shadow:  0 4px 6px -1px rgba(0,0,0,.06), 0 2px 4px -2px rgba(0,0,0,.04);
      --teks:    #1e293b;
      --terang:  #64748b;
      --abu:     #94a3b8;
      --navy:    #1e3a8a;
      --biru:    #2563eb;
      --biru-lt: #dbeafe;
      --ungu:    #7c3aed;
      --hijau:   #10b981;
      --kuning:  #f59e0b;
      --radius:  16px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--teks);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ══ HEADER ══ */
    .header-bar {
      background: var(--putih);
      border-bottom: 1px solid var(--border);
      box-shadow: var(--shadow);
      padding: 14px 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 20;
    }
    .logo-wrap {
      display: flex; align-items: center; gap: 12px;
    }
    .logo-icon {
      width: 42px; height: 42px;
      background: var(--navy);
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 14px rgba(30,58,138,.3);
      transition: transform .4s ease;
    }
    .logo-icon:hover { transform: rotate(6deg) scale(1.05); }
    .logo-text {
      font-family: 'Sora', sans-serif;
      font-size: 20px; font-weight: 800;
      color: var(--navy); letter-spacing: -0.5px;
    }
    .logo-sub {
      font-size: 10px; color: var(--abu);
      font-weight: 600; letter-spacing: 1px;
      text-transform: uppercase;
    }

    /* clock */
    .header-right { text-align: right; }
    .header-clock {
      font-family: 'Sora', sans-serif;
      font-size: 26px; font-weight: 800;
      color: var(--navy); letter-spacing: -1px;
    }
    .header-date {
      font-size: 11px; color: var(--terang); margin-top: 2px; font-weight: 500;
    }

    /* sound toggle */
    .sound-toggle {
      width: 38px; height: 38px;
      border-radius: 12px;
      background: var(--biru-lt);
      border: 1px solid #bfdbfe;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all .2s;
      color: var(--biru); font-size: 15px;
    }
    .sound-toggle:hover { background: #bfdbfe; transform: scale(1.08); }
    .sound-toggle.muted { background: #f1f5f9; border-color: var(--border); color: var(--abu); }

    /* ══ MAIN LAYOUT ══ */
    .main-grid {
      display: grid;
      grid-template-columns: 1fr 320px;
      gap: 20px;
      padding: 24px 36px 80px;
      min-height: calc(100vh - 76px);
    }

    /* ══ LEFT ══ */
    .current-section {
      display: flex; flex-direction: column; gap: 18px;
    }

    .section-label {
      font-size: 10px; font-weight: 700;
      color: var(--abu);
      text-transform: uppercase; letter-spacing: 2px;
      display: flex; align-items: center; gap: 6px;
    }
    .section-label i { color: var(--biru); }

    /* Called Card */
    .called-card {
      background: var(--putih);
      border: 1px solid var(--border);
      border-radius: 24px;
      box-shadow: var(--shadow);
      padding: 40px 48px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      flex: 1;
      min-height: 340px;
      position: relative;
      overflow: hidden;
      transition: box-shadow .3s;
    }
    .called-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--navy), var(--biru), #6366f1);
      border-radius: 24px 24px 0 0;
    }
    .called-card:hover { box-shadow: 0 12px 40px rgba(30,58,138,.1); }

    /* Flash animation saat panggil */
    .called-card.flash-animate {
      animation: flashCard .7s ease;
    }
    @keyframes flashCard {
      0%   { box-shadow: 0 0 0 0 rgba(37,99,235,.5); }
      40%  { box-shadow: 0 0 0 18px rgba(37,99,235,0); border-color: rgba(37,99,235,.6); }
      100% { box-shadow: 0 4px 6px -1px rgba(0,0,0,.06); border-color: var(--border); }
    }

    /* Live badge */
    .called-label {
      font-size: 10px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 3px;
      color: var(--terang);
      margin-bottom: 20px;
      display: flex; align-items: center; gap: 8px;
    }
    .live-dot {
      width: 8px; height: 8px;
      background: var(--hijau);
      border-radius: 50%;
      animation: blink 1.4s infinite;
      flex-shrink: 0;
    }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.2} }

    /* Number */
    .called-number {
      font-family: 'Sora', sans-serif;
      font-size: clamp(88px, 13vw, 148px);
      font-weight: 900;
      line-height: 1;
      letter-spacing: -6px;
      color: var(--navy);
      position: relative;
      transition: all .3s cubic-bezier(.4,0,.2,1);
    }
    .called-number.number-change {
      animation: numberPop .45s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes numberPop {
      0%   { transform: scale(.82); opacity: 0; }
      60%  { transform: scale(1.06); }
      100% { transform: scale(1); opacity: 1; }
    }
    /* Underline aksen biru */
    .called-number::after {
      content: '';
      display: block;
      width: 60%;
      height: 5px;
      background: linear-gradient(90deg, var(--biru), #6366f1);
      border-radius: 99px;
      margin: 14px auto 0;
    }

    .called-name {
      font-family: 'Sora', sans-serif;
      font-size: 20px; font-weight: 700;
      color: var(--teks);
      margin-top: 18px;
      min-height: 30px;
      transition: all .3s ease;
    }
    .called-layanan {
      font-size: 13px;
      color: var(--terang);
      margin-top: 6px;
      min-height: 20px;
    }

    /* Poli badge */
    .poli-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      color: var(--navy);
      border-radius: 99px;
      padding: 8px 20px;
      font-size: 13px; font-weight: 700;
      margin-top: 20px;
      letter-spacing: .3px;
    }
    .poli-badge i { color: var(--biru); font-size: 13px; }

    /* No-queue state */
    .no-queue-state {
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      gap: 14px; flex: 1;
    }
    .no-queue-icon {
      width: 80px; height: 80px;
      background: #f0f4f8;
      border-radius: 24px;
      display: flex; align-items: center; justify-content: center;
      font-size: 32px; color: var(--abu);
    }
    .no-queue-text { color: var(--terang); font-size: 15px; font-weight: 600; }
    .no-queue-sub  { color: var(--abu); font-size: 12px; }

    /* ══ STATS ROW ══ */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }
    .stat-chip {
      background: var(--putih);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 18px 16px;
      text-align: center;
      transition: transform .2s, box-shadow .2s;
      position: relative; overflow: hidden;
    }
    .stat-chip::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0; height: 3px;
    }
    .stat-chip.chip-total::before { background: linear-gradient(90deg,var(--biru),#6366f1); }
    .stat-chip.chip-wait::before  { background: linear-gradient(90deg,var(--kuning),#ef4444); }
    .stat-chip.chip-done::before  { background: linear-gradient(90deg,var(--hijau),#06b6d4); }

    .stat-chip:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(30,58,138,.1); }
    .stat-chip .val {
      font-family: 'Sora', sans-serif;
      font-size: 34px; font-weight: 900;
      line-height: 1; letter-spacing: -2px;
    }
    .stat-chip .lbl {
      font-size: 10px; font-weight: 700;
      color: var(--abu);
      text-transform: uppercase; letter-spacing: 1.5px;
      margin-top: 5px;
    }
    .val-total   { color: var(--biru); }
    .val-waiting { color: var(--kuning); }
    .val-done    { color: var(--hijau); }

    /* ══ RIGHT PANEL ══ */
    .waiting-panel {
      display: flex; flex-direction: column; gap: 14px;
    }
    .panel-card {
      background: var(--putih);
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: var(--shadow);
      overflow: hidden;
      flex: 1;
    }
    .panel-header-inner {
      padding: 16px 18px 14px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      background: #f8fafc;
    }
    .panel-title {
      font-size: 12px; font-weight: 700;
      color: var(--teks);
      text-transform: uppercase; letter-spacing: 1.5px;
      display: flex; align-items: center; gap: 6px;
    }
    .panel-title i { color: var(--biru); }
    .count-badge {
      background: #fffbeb;
      border: 1px solid #fde68a;
      color: #92400e;
      font-size: 11px; font-weight: 700;
      padding: 3px 10px; border-radius: 99px;
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
      padding: 13px 18px;
      display: flex; align-items: center; gap: 12px;
      border-bottom: 1px solid #f0f4f8;
      transition: background .18s;
      animation: slideIn .3s ease;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(10px); }
      to   { opacity: 1; transform: translateX(0); }
    }
    .queue-item:last-child { border-bottom: none; }
    .queue-item:hover { background: #f8fafc; }
    .queue-item.is-called { background: #eff6ff; border-left: 3px solid var(--biru); }

    .q-num {
      width: 40px; height: 40px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--navy), var(--biru));
      display: flex; align-items: center; justify-content: center;
      font-family: 'Sora', sans-serif;
      font-size: 13px; font-weight: 800;
      color: #fff; flex-shrink: 0;
      box-shadow: 0 3px 10px rgba(37,99,235,.25);
    }
    .q-num.called-num {
      background: linear-gradient(135deg, var(--ungu), #a78bfa);
      box-shadow: 0 3px 14px rgba(124,58,237,.35);
      animation: pulseNum 1.5s infinite;
    }
    @keyframes pulseNum {
      0%,100% { box-shadow: 0 3px 10px rgba(124,58,237,.35); }
      50%      { box-shadow: 0 3px 22px rgba(124,58,237,.6); }
    }
    .q-info { flex: 1; min-width: 0; }
    .q-name {
      font-size: 13px; font-weight: 700;
      color: var(--teks);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .q-status {
      font-size: 10px; font-weight: 600;
      margin-top: 2px; display: flex; align-items: center; gap: 4px;
    }
    .q-status.menunggu  { color: var(--kuning); }
    .q-status.dipanggil { color: var(--ungu); }

    .empty-waiting {
      text-align: center; padding: 40px 20px;
      color: var(--abu); font-size: 13px;
    }
    .empty-waiting i { display: block; font-size: 28px; margin-bottom: 8px; opacity: .35; }

    /* ══ TOAST ══ */
    .call-toast {
      position: fixed;
      top: 20px; left: 50%; transform: translateX(-50%) translateY(-90px);
      z-index: 50;
      background: var(--navy);
      border: 1px solid rgba(96,165,250,.3);
      border-radius: 20px;
      padding: 14px 26px;
      display: flex; align-items: center; gap: 14px;
      box-shadow: 0 16px 48px rgba(0,0,0,.18);
      transition: transform .45s cubic-bezier(.34,1.56,.64,1), opacity .3s;
      opacity: 0;
      min-width: 300px;
    }
    .call-toast.show {
      transform: translateX(-50%) translateY(0);
      opacity: 1;
    }
    .toast-icon {
      width: 40px; height: 40px;
      background: rgba(255,255,255,.12);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: #fff; flex-shrink: 0;
    }
    .toast-num  { font-family: 'Sora',sans-serif; font-size: 26px; font-weight: 900; color: #bfdbfe; }
    .toast-sub  { font-size: 11px; color: rgba(255,255,255,.65); font-weight: 600; margin-top: 1px; }

    /* ══ TICKER ══ */
    .ticker-bar {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: var(--navy);
      border-top: 2px solid rgba(96,165,250,.25);
      padding: 10px 0;
      z-index: 15;
      overflow: hidden;
      display: flex; align-items: center; gap: 0;
    }
    .ticker-prefix {
      background: var(--biru);
      padding: 0 18px;
      font-size: 10px; font-weight: 800;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: #fff; white-space: nowrap;
      display: flex; align-items: center; gap: 6px;
      height: 100%; flex-shrink: 0;
      align-self: stretch; justify-content: center;
    }
    .ticker-scroll { overflow: hidden; flex: 1; }
    .ticker-content {
      display: inline-flex; align-items: center; gap: 0;
      white-space: nowrap;
      animation: ticker 32s linear infinite;
    }
    @keyframes ticker {
      from { transform: translateX(100vw); }
      to   { transform: translateX(-100%); }
    }
    .ticker-item {
      font-size: 13px; font-weight: 500;
      color: rgba(255,255,255,.8);
      padding: 0 28px;
    }
    .ticker-sep { color: rgba(255,255,255,.25); padding: 0 6px; }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 900px) {
      .main-grid { grid-template-columns: 1fr; padding: 16px 20px 80px; }
      .waiting-panel { display: none; }
      .header-bar { padding: 12px 20px; }
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
        <i class="fas fa-clinic-medical text-white" style="font-size:18px;color:#fff;"></i>
      </div>
      <div>
        <div class="logo-text">QLINICA</div>
        <div class="logo-sub">Sistem Antrian Digital</div>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:16px;">
      <div class="header-right">
        <div class="header-clock" id="liveClock">--:--:--</div>
        <div class="header-date"  id="liveDate">—</div>
      </div>
      <button class="sound-toggle" id="soundToggle" title="Toggle Suara" onclick="toggleSound()">
        <i class="fas fa-volume-up" id="soundIcon"></i>
      </button>
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
   VOICE — SUARA PEREMPUAN INSTANSI
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
  'Poli Umum'    : '#eff6ff',
  'Poli Gigi'    : '#f5f3ff',
  'Poli KIA'     : '#fdf2f8',
  'UGD'          : '#fff1f2',
  'Laboratorium' : '#f0fdf4',
  'Baby Spa'     : '#fffbeb',
};
const POLI_COLOR_BORDER = {
  'Poli Umum'    : '#bfdbfe',
  'Poli Gigi'    : '#ddd6fe',
  'Poli KIA'     : '#f9a8d4',
  'UGD'          : '#fecaca',
  'Laboratorium' : '#bbf7d0',
  'Baby Spa'     : '#fde68a',
};
const POLI_COLOR_TEXT = {
  'Poli Umum'    : '#1e3a8a',
  'Poli Gigi'    : '#4c1d95',
  'Poli KIA'     : '#831843',
  'UGD'          : '#7f1d1d',
  'Laboratorium' : '#14532d',
  'Baby Spa'     : '#78350f',
};

/* ── Pilih suara perempuan Bahasa Indonesia ── */
let femaleVoice = null;

function loadVoices() {
  const voices = window.speechSynthesis.getVoices();

  // Prioritas: id-ID perempuan → ms-MY perempuan → en-US perempuan → fallback
  const candidates = [
    // Perempuan Indonesia / Melayu
    voices.find(v => v.lang === 'id-ID' && /female|wanita|perempuan|woman|f\b/i.test(v.name)),
    voices.find(v => v.lang.startsWith('id')  && /female|woman/i.test(v.name)),
    voices.find(v => v.lang === 'ms-MY' && /female|woman/i.test(v.name)),
    // Nama suara Google/Microsoft yang dikenal female
    voices.find(v => /google bahasa indonesia|google id/i.test(v.name)),
    voices.find(v => /microsoft andi|microsoft gadis|zira|hazel|susan|karen|samantha|moira|fiona|tessa/i.test(v.name)),
    // Fallback: sembarang id-ID
    voices.find(v => v.lang === 'id-ID'),
    voices.find(v => v.lang.startsWith('id')),
    voices.find(v => v.lang === 'ms-MY'),
    // Terakhir: perempuan bahasa Inggris (pitch tinggi)
    voices.find(v => /female|woman|zira|hazel|susan|karen|samantha/i.test(v.name)),
  ];

  femaleVoice = candidates.find(v => v !== undefined) || null;
}

if ('speechSynthesis' in window) {
  window.speechSynthesis.getVoices();
  window.speechSynthesis.onvoiceschanged = loadVoices;
  // Chrome membutuhkan panggilan awal
  setTimeout(loadVoices, 300);
}

/* ── Pengumuman suara perempuan instansi ── */
function speakAnnouncement(number, name, poli) {
  if (!soundEnabled) return;
  if (!('speechSynthesis' in window)) return;

  window.speechSynthesis.cancel();

  const poliText = poli ? (POLI_TTS_MAP[poli] || poli) : null;

  // Format nomor: "001" → "0 0 1" agar dieja per digit
  const digitized = number.split('').join(', ');

  // Kalimat formal ala instansi pemerintah / rumah sakit
  const text =
    'Perhatian. ' +
    'Nomor antrian, ' + digitized + '. ' +
    'Nomor antrian, ' + digitized + '. ' +
    (poliText
      ? 'Dimohon kepada pasien atas nama ' + (name || 'yang bersangkutan') + ', harap segera menuju ' + poliText + '. '
      : 'Dimohon kepada pasien atas nama ' + (name || 'yang bersangkutan') + ', harap segera menuju loket pendaftaran. ') +
    'Terima kasih.';

  setTimeout(() => {
    const utter    = new SpeechSynthesisUtterance(text);
    utter.lang     = 'id-ID';
    utter.rate     = 0.88;   // sedikit lebih lambat agar jelas
    utter.pitch    = 1.15;   // sedikit lebih tinggi → kesan suara perempuan
    utter.volume   = 1;

    if (femaleVoice) utter.voice = femaleVoice;

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
  const newNum = 'No. ' + data.dilayani.no_antrian;

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

  numEl.textContent  = newNum;
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
      ? '<span style="display:inline-flex;align-items:center;gap:4px;background:#f0f4f8;border-radius:99px;padding:2px 8px;font-size:9px;font-weight:700;color:var(--terang);margin-top:3px;">'
        + '<i class="fas ' + icon + '" style="font-size:8px;"></i> ' + poli + '</span>'
      : '';

    div.innerHTML =
      '<div class="q-num' + (isCalled ? ' called-num' : '') + '">' + item.no_antrian + '</div>' +
      '<div class="q-info">' +
        '<div class="q-name">' + (item.nama || '—') + '</div>' +
        '<div class="q-status ' + (isCalled ? 'dipanggil' : 'menunggu') + '">' +
          (isCalled
            ? '<i class="fas fa-bullhorn" style="font-size:9px;"></i> Dipanggil'
            : '<i class="fas fa-clock"   style="font-size:9px;"></i> Menunggu') +
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
