@extends('layouts.app')

@section('title', 'Kelola Jabatan & Hak Akses')
@section('page-title', 'Kelola Jabatan & Hak Akses')
@section('page-subtitle', 'Atur hak akses secara detail untuk setiap jabatan (Role-based Access Control)')

@push('styles')
<style>
  /* ── Sidebar & Layout ── */
  .role-sidebar {
    min-width: 280px;
    width: 280px;
  }
  .role-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 8px;
    transition: all 0.2s;
    border: 1px solid transparent;
    color: #4b5563;
    background: transparent;
  }
  .role-btn:hover {
    background: #f3f4f6;
  }
  .role-btn.active {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
    font-weight: 700;
  }
  .role-btn .badge {
    background: #e5e7eb;
    color: #6b7280;
    padding: 2px 8px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
  }
  .role-btn.active .badge {
    background: #bfdbfe;
    color: #1d4ed8;
  }
  
  /* ── Panels ── */
  .akses-panel { display: none; animation: fadeIn 0.2s ease; }
  .akses-panel.active { display: block; }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ── Toggle Switch ── */
  .switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
  }
  .switch input { opacity: 0; width: 0; height: 0; }
  .slider {
    position: absolute; cursor: pointer; inset: 0;
    background-color: #cbd5e1;
    transition: .3s; border-radius: 34px;
  }
  .slider:before {
    position: absolute; content: "";
    height: 18px; width: 18px; left: 3px; bottom: 3px;
    background-color: white; transition: .3s; border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  input:checked + .slider { background-color: #2563eb; }
  input:checked + .slider:before { transform: translateX(20px); }
  input:disabled + .slider { opacity: 0.5; cursor: not-allowed; }

  /* ── Menu Block ── */
  .menu-block {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    margin-bottom: 16px;
    background: #fff;
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .menu-block:hover {
    border-color: #bfdbfe;
  }
  .menu-block.active {
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37,99,235,0.08);
  }
  .menu-header {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    background: #fff;
  }
  .sub-panel {
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
    padding: 20px;
    display: none;
  }
  .menu-block.expanded .sub-panel {
    display: block;
  }
  .caret-icon { transition: transform 0.3s; }
  .menu-block.expanded .caret-icon { transform: rotate(180deg); }

  /* ── Checkbox custom for subs ── */
  .sub-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
  }
  .sub-card:hover { border-color: #93c5fd; }
  .sub-card.checked { border-color: #2563eb; background: #eff6ff; }
  .sub-card input[type="checkbox"] { width: 18px; height: 18px; accent-color: #2563eb; cursor: pointer; margin-top: 2px; }

  /* Toast */
  #toast {
    position: fixed; bottom: 24px; right: 24px;
    background: #1f2937; color: white; padding: 12px 24px;
    border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: flex; items-center; gap: 12px; font-weight: 500; font-size: 14px;
    transform: translateY(100px); opacity: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 9999;
  }
  #toast.show { transform: translateY(0); opacity: 1; }
  #toast.success { background: #166534; border: 1px solid #22c55e; }
  #toast.error { background: #991b1b; border: 1px solid #ef4444; }

  /* Modals */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 9999;
    align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
  }
  .modal-overlay.open { display: flex; }
  .modal-box {
    background: #fff; border-radius: 20px; padding: 28px;
    width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: fadeIn .2s ease;
  }
</style>
@endpush

@section('content')

{{-- Info Card --}}
<div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex items-start gap-4">
  <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 text-blue-600">
    <i class="fas fa-shield-alt text-lg"></i>
  </div>
  <div>
    <h4 class="font-bold text-blue-900 mb-1">Pengaturan Role & Hak Akses (Extension Style)</h4>
    <p class="text-sm text-blue-700 leading-relaxed max-w-4xl">
      Pilih jabatan di sebelah kiri. Aktifkan menu menggunakan toggle. Jika aktif, klik kotak menu untuk melihat sub-akses spesifik (seperti jenis dashboard atau izin operasi). 
      <br><span class="font-bold mt-1 inline-block">Catatan:</span> Role Admin akan mem-bypass semua hak akses di bawah ini.
    </p>
  </div>
</div>

<div class="flex flex-col lg:flex-row gap-6 items-start relative">

  {{-- ── SIDEBAR JABATAN ── --}}
  <div class="role-sidebar flex-shrink-0 w-full lg:w-auto bg-white border border-gray-200 rounded-2xl shadow-sm p-4 sticky top-24">
    <div class="flex justify-between items-center mb-4 px-2">
      <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Daftar Jabatan</h3>
      <button type="button" onclick="document.getElementById('modalTambah').classList.add('open')"
        class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition" title="Tambah Jabatan">
        <i class="fas fa-plus text-sm"></i>
      </button>
    </div>
    
    <div class="space-y-1" id="jabatanTabs">
      @forelse($jabatans as $i => $jabatan)
        <button
          type="button"
          class="role-btn {{ $i === 0 ? 'active' : '' }}"
          onclick="switchTab({{ $jabatan->id }})"
          id="tab-btn-{{ $jabatan->id }}"
        >
          <div class="flex items-center gap-3">
            <i class="fas fa-user-tag w-4 text-center opacity-70"></i>
            <span class="font-semibold text-sm">{{ $jabatan->nama_jabatan }}</span>
          </div>
          <span class="badge">{{ $jabatan->pegawais_count }}</span>
        </button>
      @empty
        <div class="text-center p-4 text-gray-400 text-sm">Belum ada jabatan</div>
      @endforelse
    </div>
  </div>

  {{-- ── MAIN PANEL ── --}}
  <div class="flex-1 w-full min-w-0">
    @foreach($jabatans as $i => $jabatan)
      @php
        $formId = 'form-jabatan-' . $jabatan->id;
        // Kita akses $hakAkses yang di-pass dari Controller, key-nya menu_id
        $jabatanAkses = $hakAkses[$jabatan->id] ?? collect();
      @endphp

      <div class="akses-panel {{ $i === 0 ? 'active' : '' }}" id="panel-{{ $jabatan->id }}">
        
        {{-- Header Panel --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2">
              {{ $jabatan->nama_jabatan }}
            </h2>
            <p class="text-sm text-gray-500">Kelola akses modul dan ekstensi untuk jabatan ini.</p>
          </div>
          <div class="flex items-center gap-3">
            @if($jabatan->pegawais_count === 0)
              <button type="button" onclick="confirmDelete({{ $jabatan->id }}, '{{ addslashes($jabatan->nama_jabatan) }}')"
                class="px-4 py-2.5 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-sm font-bold transition flex items-center gap-2">
                <i class="fas fa-trash-alt"></i> Hapus
              </button>
            @endif
            <button type="button" onclick="saveAkses({{ $jabatan->id }})" id="btnSave-{{ $jabatan->id }}"
              class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition flex items-center gap-2 shadow-sm">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </div>

        {{-- Pencarian --}}
        <div class="mb-5 relative">
          <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input type="text" placeholder="Cari modul atau sub-akses..." 
            class="w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-blue-400 shadow-sm transition" 
            onkeyup="searchMenu(this, {{ $jabatan->id }})">
        </div>

        {{-- Form Sub Akses --}}
        <form id="{{ $formId }}" action="{{ route('admin.jabatan.akses', $jabatan->id) }}" method="POST" onsubmit="event.preventDefault(); saveAkses({{ $jabatan->id }})">
          @csrf
          @method('PUT')
          
          <div class="flex flex-col gap-4" id="menu-list-{{ $jabatan->id }}">
            @foreach($menus as $menu)
              @php
                $subs = $menuSubAkses[$menu->nama_menu] ?? [];
                $viewConf = $subs['view'] ?? ['label' => 'Akses ' . $menu->nama_menu, 'icon' => 'fa-cube', 'desc' => 'Izinkan akses'];
                
                // Cek akses saat ini via sub_akses JSON (atau object stdClass dari DB)
                $ha = $jabatanAkses[$menu->id] ?? null;
                $currentSub = [];
                if ($ha) {
                   $currentSub = is_array($ha->sub_akses) ? $ha->sub_akses : json_decode(json_encode($ha->sub_akses), true) ?? [];
                }
                
                $isView = !empty($currentSub['view']);
                $hasSubs = count($subs) > 1;
              @endphp

              <div class="menu-block {{ $isView ? 'active expanded' : '' }}" id="menu-block-{{$jabatan->id}}-{{$menu->id}}">
                
                {{-- Menu Header (Toggle) --}}
                <div class="menu-header" onclick="toggleMenuPanel(this, {{$hasSubs ? 'true' : 'false'}})">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-50 border border-gray-100 text-gray-600 flex items-center justify-center rounded-xl text-lg transition-colors icon-container">
                      <i class="fas {{ $viewConf['icon'] ?? 'fa-cube' }}"></i>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-800 text-base mb-0.5">{{ $menu->nama_menu }}</h4>
                      <p class="text-xs text-gray-500">{{ $viewConf['desc'] ?? 'Kelola akses untuk menu ini' }}</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-5">
                    <label class="switch" onclick="event.stopPropagation()">
                      <input type="checkbox" name="akses[{{$menu->id}}][view]" value="1" class="view-cb" 
                        {{ $isView ? 'checked' : '' }} onchange="handleViewToggle(this, {{$jabatan->id}}, {{$menu->id}}, {{$hasSubs ? 'true' : 'false'}})">
                      <span class="slider"></span>
                    </label>
                    @if($hasSubs)
                      <div class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors text-gray-400">
                        <i class="fas fa-chevron-down caret-icon"></i>
                      </div>
                    @endif
                  </div>
                </div>

                {{-- Sub Akses Panel --}}
                @if($hasSubs)
                  <div class="sub-panel">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sub-Akses & Ekstensi</p>
                    <div class="flex flex-col gap-3">
                      @foreach($subs as $key => $subConf)
                        @if($key !== 'view')
                          @php
                            $isSubChecked = !empty($currentSub[$key]);
                          @endphp
                          <label class="sub-card {{ $isSubChecked ? 'checked' : '' }}" onclick="toggleSubCard(this)">
                            <input type="checkbox" name="akses[{{$menu->id}}][{{$key}}]" value="1" 
                              class="sub-cb" {{ $isSubChecked ? 'checked' : '' }} onclick="event.stopPropagation()">
                            <div class="flex-1">
                              <div class="font-bold text-sm text-gray-800 flex items-center gap-2 mb-1">
                                <i class="fas {{ $subConf['icon'] ?? 'fa-check' }} text-blue-500 text-xs w-4"></i>
                                {{ $subConf['label'] }}
                              </div>
                              <p class="text-xs text-gray-500 leading-snug">{{ $subConf['desc'] }}</p>
                              
                              @if(isset($subConf['preview']))
                                <div class="mt-2 text-[10px] font-bold uppercase tracking-wide text-blue-600 bg-blue-50 px-2 py-1 rounded inline-flex items-center gap-1 border border-blue-100">
                                  <i class="fas fa-eye"></i> Layout: {{ $subConf['preview'] }}
                                </div>
                              @endif
                            </div>
                          </label>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @endif

              </div>
            @endforeach
          </div>
        </form>

      </div>
    @endforeach
  </div>

</div>

{{-- Toast Notification --}}
<div id="toast">
  <i class="fas fa-check-circle text-xl"></i>
  <span id="toast-msg">Tersimpan</span>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-overlay" id="modalTambah">
  <div class="modal-box">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
        <i class="fas fa-plus-circle text-emerald-600"></i> Tambah Jabatan Baru
      </h3>
      <button type="button" onclick="document.getElementById('modalTambah').classList.remove('open')"
        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition text-lg">
        &times;
      </button>
    </div>

    <form method="POST" action="{{ route('admin.jabatan.store') }}">
      @csrf
      <div class="mb-5">
        <label class="block text-xs font-bold text-gray-600 mb-1.5" for="nama_jabatan">Nama Jabatan</label>
        <input type="text" id="nama_jabatan" name="nama_jabatan" value="{{ old('nama_jabatan') }}"
          class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm"
          placeholder="contoh: Kepala Bagian..." autofocus>
        @error('nama_jabatan')
          <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
        @enderror
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="document.getElementById('modalTambah').classList.remove('open')"
          class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">Batal</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold transition shadow-sm">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div class="modal-overlay" id="modalHapus">
  <div class="modal-box text-center p-8">
    <div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-2xl mx-auto mb-4">
      <i class="fas fa-trash-alt"></i>
    </div>
    <h3 class="font-bold text-gray-800 text-lg mb-2">Hapus Jabatan?</h3>
    <p class="text-sm text-gray-500 mb-1">Anda yakin ingin menghapus jabatan:</p>
    <p class="font-bold text-red-600 text-base mb-6" id="hapusNamaJabatan"></p>
    <form method="POST" id="formHapusJabatan">
      @csrf @method('DELETE')
      <div class="flex justify-center gap-3">
        <button type="button" onclick="document.getElementById('modalHapus').classList.remove('open')"
          class="px-6 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">Batal</button>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  /* ── Switch Tab (Discord Style) ── */
  function switchTab(jabatanId) {
    document.querySelectorAll('.akses-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
    
    document.getElementById('panel-' + jabatanId).classList.add('active');
    document.getElementById('tab-btn-' + jabatanId).classList.add('active');
  }

  /* ── Collapsible Menu Panel ── */
  function toggleMenuPanel(headerEl, hasSubs) {
    if (!hasSubs) return; // Jika tidak ada subs, gak usah expand
    const block = headerEl.closest('.menu-block');
    // Hanya bisa expand jika toggle utama nyala
    const viewCb = block.querySelector('.view-cb');
    if (!viewCb.checked) return;
    
    block.classList.toggle('expanded');
  }

  /* ── View Toggle Change ── */
  function handleViewToggle(cb, jabatanId, menuId, hasSubs) {
    const block = cb.closest('.menu-block');
    const iconContainer = block.querySelector('.icon-container');
    
    if (cb.checked) {
      block.classList.add('active');
      iconContainer.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-100');
      iconContainer.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-100');
      if (hasSubs) block.classList.add('expanded');
    } else {
      block.classList.remove('active', 'expanded');
      iconContainer.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-100');
      iconContainer.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-100');
      
      // Uncheck all subs
      const subCbs = block.querySelectorAll('.sub-cb');
      subCbs.forEach(sub => {
        sub.checked = false;
        sub.closest('.sub-card').classList.remove('checked');
      });
    }
  }

  /* ── Sub Card Select ── */
  function toggleSubCard(cardEl) {
    const cb = cardEl.querySelector('.sub-cb');
    // checkbox is already toggled by the browser because of label wrapping,
    // so we just read its state and style the card.
    setTimeout(() => {
      if (cb.checked) {
        cardEl.classList.add('checked');
      } else {
        cardEl.classList.remove('checked');
      }
    }, 10);
  }

  /* ── AJAX Save ── */
  async function saveAkses(jabatanId) {
    const form = document.getElementById('form-jabatan-' + jabatanId);
    const btn = document.getElementById('btnSave-' + jabatanId);
    const originalText = btn.innerHTML;
    
    // UI Loading state
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
    btn.disabled = true;

    try {
      const formData = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      
      if (data.success) {
        showToast('Berhasil disimpan', 'success');
      } else {
        showToast('Gagal menyimpan', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('Terjadi kesalahan jaringan', 'error');
    } finally {
      // Revert UI
      btn.innerHTML = originalText;
      btn.disabled = false;
    }
  }

  function showToast(msg, type) {
    const toast = document.getElementById('toast');
    const msgEl = document.getElementById('toast-msg');
    const icon = toast.querySelector('i');
    
    toast.className = 'show ' + type;
    msgEl.textContent = msg;
    
    if(type === 'success') {
      icon.className = 'fas fa-check-circle text-green-400 text-xl';
    } else {
      icon.className = 'fas fa-exclamation-triangle text-red-400 text-xl';
    }
    
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
  }

  /* ── Konfirmasi Hapus ── */
  function confirmDelete(jabatanId, namaJabatan) {
    document.getElementById('hapusNamaJabatan').textContent = namaJabatan;
    document.getElementById('formHapusJabatan').action = '/admin/jabatan/' + jabatanId;
    document.getElementById('modalHapus').classList.add('open');
  }

  /* ── Tutup modal jika klik overlay ── */
  ['modalTambah','modalHapus'].forEach(id => {
    const el = document.getElementById(id);
    if(el) {
      el.addEventListener('click', function(e) {
        if (e.target === el) el.classList.remove('open');
      });
    }
  });

  /* ── Buka modal tambah otomatis jika ada validasi error ── */
  @if($errors->has('nama_jabatan'))
    document.getElementById('modalTambah').classList.add('open');
  @endif

  /* ── Search Menu ── */
  function searchMenu(input, jabatanId) {
    const filter = input.value.toLowerCase();
    const menuList = document.getElementById('menu-list-' + jabatanId);
    if (!menuList) return;
    
    const blocks = menuList.querySelectorAll('.menu-block');
    blocks.forEach(block => {
      // Dapatkan semua teks di dalam block tersebut untuk dicocokkan
      const textContent = block.innerText.toLowerCase();
      if (textContent.includes(filter)) {
        block.style.display = '';
      } else {
        block.style.display = 'none';
      }
    });
  }

  // Init icon colors on load for checked items
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.view-cb:checked').forEach(cb => {
      const block = cb.closest('.menu-block');
      const iconContainer = block.querySelector('.icon-container');
      if (iconContainer) {
        iconContainer.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-100');
        iconContainer.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-100');
      }
    });
  });
</script>
@endpush
