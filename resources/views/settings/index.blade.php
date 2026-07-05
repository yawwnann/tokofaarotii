@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('subtitle', 'Konfigurasi identitas toko dan preferensi sistem operasional Toko FAA')

@section('content')

{{-- ── ALERT NOTIFIKASI ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@if($errors->any())
<div class="alert-banner" style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;">
    <div class="alert-inner">
        <i class="fas fa-exclamation-circle" style="margin-top: 3px;"></i>
        <ul style="list-style: disc; padding-left: 1rem; margin: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="settings-container">
    <div class="settings-grid">
        
        {{-- ── NAVIGATION SIDEBAR (TAB SWITCHER) ── --}}
        <div class="settings-nav">
            <div class="nav-card">
                <button type="button" onclick="switchSettingsTab('store-info')" id="btn-store-info" class="nav-item-btn active">
                    <i class="fas fa-store"></i>
                    <span>Informasi Toko</span>
                </button>
                <button type="button" onclick="switchSettingsTab('branding-info')" id="btn-branding-info" class="nav-item-btn">
                    <i class="fas fa-palette"></i>
                    <span>Tampilan & Branding</span>
                </button>
                <button type="button" onclick="switchSettingsTab('system-info')" id="btn-system-info" class="nav-item-btn">
                    <i class="fas fa-server"></i>
                    <span>Sistem & Operasional</span>
                </button>
            </div>
            
            <div class="nav-card" style="margin-top: 1rem;">
                <a href="{{ route('settings.password') }}" class="nav-item-btn" style="text-decoration: none; display: flex; border-left-color: transparent;">
                    <i class="fas fa-key" style="color: #ef4444;"></i>
                    <span style="color: #ef4444; font-weight: 700;">Ubah Password</span>
                    <i class="fas fa-chevron-right" style="margin-left: auto; font-size: 0.7rem; color: #cbd5e1;"></i>
                </a>
            </div>
            
            <div class="nav-info-card">
                <i class="fas fa-info-circle"></i>
                <p>Perubahan pada pengaturan ini akan berdampak langsung pada identitas struk belanja dan informasi kontak WhatsApp utama sistem.</p>
            </div>
        </div>

        {{-- ── CONTENT FORM PENGATURAN TOKO ── --}}
        <div class="settings-content">
            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- SECTION 1: INFORMASI TOKO --}}
                <div id="store-info" class="content-section">
                    <div class="section-header">
                        <h3 class="section-title">Informasi Toko</h3>
                        <p class="section-desc">Atur nama bisnis, nomor kontak customer service, dan alamat fisik Toko FAA.</p>
                    </div>

                    <div class="section-card">
                        <div class="form-group">
                            <label class="form-label">Nama Toko / Bisnis</label>
                            <input type="text" name="store_name" class="form-input" 
                                   value="{{ old('store_name', $settings->store_name ?? 'TOKO FAA') }}" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">No. WhatsApp CS Toko</label>
                                <div class="input-with-icon">
                                    <i class="fab fa-whatsapp" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                                    <input type="text" name="store_whatsapp" class="form-input" style="padding-left: 2.5rem;"
                                           value="{{ old('store_whatsapp', $settings->store_whatsapp ?? '') }}" placeholder="Contoh: 0812345678">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Kontak Bisnis</label>
                                <div class="input-with-icon">
                                    <i class="far fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                                    <input type="email" name="store_email" class="form-input" style="padding-left: 2.5rem;"
                                           value="{{ old('store_email', $settings->store_email ?? '') }}" placeholder="admin@tokofaa.com">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Alamat Fisik Toko</label>
                            <textarea name="store_address" class="form-input" rows="3" 
                                      placeholder="Alamat lengkap Toko FAA untuk footer/struk...">{{ old('store_address', $settings->store_address ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Lokasi Toko (Asal Pengiriman)</label>
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem;margin-bottom:.5rem;">
                                <select id="settings_province" class="form-input" data-level="province">
                                    <option value="">Provinsi...</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                    @endforeach
                                </select>
                                <select id="settings_regency" class="form-input" data-level="regency" disabled>
                                    <option value="">Kabupaten/Kota...</option>
                                </select>
                                <select id="settings_district" class="form-input" data-level="district" disabled>
                                    <option value="">Kecamatan...</option>
                                </select>
                            </div>
                            <input type="hidden" name="store_district_id" id="settings_district_id" value="{{ old('store_district_id', $settings->store_district_id ?? '') }}">
                            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Pilih provinsi → kabupaten → kecamatan lokasi toko. Digunakan sebagai titik asal perhitungan ongkos kirim zonasi.</p>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: TAMPILAN & BRANDING --}}
                <div id="branding-info" class="content-section d-none">
                    <div class="section-header">
                        <h3 class="section-title">Tampilan & Branding</h3>
                        <p class="section-desc">Kelola aset visual seperti tagline e-commerce dan logo resmi Toko FAA.</p>
                    </div>

                    <div class="section-card">
                        <div class="form-group">
                            <label class="form-label">Tagline Toko</label>
                            <input type="text" name="store_tagline" class="form-input" 
                                   value="{{ old('store_tagline', $settings->store_tagline ?? 'Frozen Food & Bakery') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Logo Resmi Struk / Web</label>
                            <div style="display: flex; align-items: center; gap: 1.5rem; margin-top: 0.5rem;">
                                @if(!empty($settings->store_logo))
                                    <img src="{{ asset('storage/' . $settings->store_logo) }}" alt="Logo Toko" style="max-height: 60px; object-fit: contain; border-radius: 4px; border: 1px solid #e2e8f0; padding: 4px;">
                                @else
                                    <div style="height: 60px; width: 60px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border-radius: 4px; border: 1px solid #e2e8f0; color: #94a3b8;"><i class="fas fa-image"></i></div>
                                @endif
                                <div style="flex: 1;">
                                    <input type="file" name="store_logo" accept="image/*" style="font-size: 0.85rem; color: #64748b;">
                                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Rekomendasi format .PNG transparan maks 2MB.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: SISTEM & OPERASIONAL --}}
                <div id="system-info" class="content-section d-none">
                    <div class="section-header">
                        <h3 class="section-title">Sistem & Operasional</h3>
                        <p class="section-desc">Atur teks footer hak cipta bawah halaman dan status maintenance web.</p>
                    </div>

                    <div class="section-card">
                        <div class="form-group">
                            <label class="form-label">Teks Hak Cipta (Footer)</label>
                            <input type="text" name="footer_text" class="form-input" 
                                   value="{{ old('footer_text', $settings->footer_text ?? '© 2026 TOKO FAA. All Rights Reserved.') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mode Pemeliharaan (Maintenance)</label>
                            <div style="display: flex; gap: 2rem; margin-top: 0.5rem;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #1e293b; cursor: pointer;">
                                    <input type="radio" name="maintenance_mode" value="0" {{ old('maintenance_mode', $settings->maintenance_mode ?? '0') == '0' ? 'checked' : '' }} style="accent-color: #f97316;"> Aktif (Normal)
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #1e293b; cursor: pointer;">
                                    <input type="radio" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings->maintenance_mode ?? '0') == '1' ? 'checked' : '' }} style="accent-color: #f97316;"> Perbaikan (Maintenance)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAR ACTION BUTTONS --}}
                <div class="form-actions-bar">
                    <button type="reset" class="btn-reset">Reset Form</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Simpan Pengaturan Toko
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- ── STYLING UTAMA CSS SETTINGS ── --}}
<style>
    .settings-container { max-width: 1200px; margin: 0 auto; }
    .settings-grid { display: grid; grid-template-columns: 280px 1fr; gap: 2rem; align-items: start; }
    .nav-card { background: #fff; border-radius: .75rem; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    
    .nav-item-btn { 
        display: flex; align-items: center; gap: .875rem; padding: 1rem 1.25rem; 
        color: #64748b; font-size: .85rem; font-weight: 700; transition: all .2s; 
        border: none; background: none; width: 100%; text-align: left; cursor: pointer;
        border-left: 3px solid transparent; 
    }
    .nav-item-btn i { font-size: 1rem; width: 20px; text-align: center; }
    .nav-item-btn:hover { background: #f8fafc; color: #f97316; }
    .nav-item-btn.active { background: #fff7ed; color: #f97316; border-left-color: #f97316; }

    .nav-info-card { margin-top: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1rem; display: flex; gap: .75rem; color: #64748b; font-size: .75rem; line-height: 1.4; }
    .nav-info-card i { color: #3b82f6; font-size: 1rem; margin-top: 2px; }

    .section-header { margin-bottom: 1.5rem; }
    .section-title { font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 .25rem 0; }
    .section-desc { font-size: .825rem; color: #64748b; margin: 0; }

    .section-card { background: #fff; border-radius: .75rem; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 1.25rem; }
    .form-group { display: flex; flex-direction: column; gap: .5rem; }
    .form-label { font-size: .8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .05em; }
    .form-input { width: 100%; background: #fff; border: 1px solid #cbd5e1; border-radius: .5rem; padding: .625rem .875rem; font-size: .9rem; color: #1e293b; transition: all .2s; box-sizing: border-box; }
    .form-input:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .input-with-icon { position: relative; width: 100%; }

    .form-actions-bar { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 1rem; }
    .btn-reset { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: .625rem 1.25rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; transition: all .2s; }
    .btn-reset:hover { background: #e2e8f0; }
    .btn-save { background: #f97316; color: #fff; border: none; padding: .625rem 1.5rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-save:hover { background: #ea580c; }

    .alert-banner { display: flex; align-items: center; justify-content: space-between; padding: .875rem 1.125rem; border-radius: .75rem; margin-bottom: 2rem; font-size: .85rem; font-weight: 600; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .alert-inner { display: flex; align-items: center; gap: .5rem; }
    .alert-banner button { background: none; border: none; cursor: pointer; color: inherit; opacity: .6; }

    .d-none { display: none !important; }
    @media (max-width: 900px) { .settings-grid { grid-template-columns: 1fr; } .settings-nav { display: none; } .form-row { grid-template-columns: 1fr; } }
</style>

{{-- ── JAVASCRIPT CASCADING WILAYAH ── --}}
<script>
function switchSettingsTab(tabId) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.add('d-none');
    });
    document.getElementById(tabId).classList.remove('d-none');
    document.querySelectorAll('.nav-item-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById('btn-' + tabId).classList.add('active');
}

function loadChildren(parent, level) {
    const sel = parent.closest('.form-group')?.querySelector(`[data-level="${level}"]`);
    if (!sel) return;
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled = true;

    const url = level === 'regency'
        ? `/api/wilayah/regencies/${parent.value}`
        : `/api/wilayah/districts/${parent.value}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">Pilih ' + (level === 'regency' ? 'Kabupaten/Kota' : 'Kecamatan') + '...</option>';
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(() => {
            sel.innerHTML = '<option value="">Gagal memuat</option>';
        });
}

document.querySelectorAll('[data-level="province"], [data-level="regency"]').forEach(el => {
    el.addEventListener('change', function() {
        const next = this.dataset.level === 'province' ? 'regency' : 'district';
        const nextSel = this.closest('.form-group')?.querySelector(`[data-level="${next}"]`);
        if (nextSel) {
            nextSel.innerHTML = '<option value="">Pilih ' + (next === 'regency' ? 'Kabupaten/Kota' : 'Kecamatan') + '...</option>';
            nextSel.disabled = true;
            if (next === 'district') {
                document.getElementById('settings_district_id').value = '';
            }
        }
        if (this.value) loadChildren(this, next);
    });
});

document.querySelectorAll('[data-level="district"]').forEach(el => {
    el.addEventListener('change', function() {
        document.getElementById('settings_district_id').value = this.value || '';
    });
});

// Pre-populate on page load if editing
(function() {
    const districtId = document.getElementById('settings_district_id').value;
    if (!districtId) return;

    const provId = districtId.substring(0, 2);
    const regId = districtId.substring(0, 4);

    const provinceSel = document.getElementById('settings_province');
    provinceSel.value = provId;

    // Load regencies then districts
    fetch(`/api/wilayah/regencies/${provId}`)
        .then(r => r.json())
        .then(regencies => {
            const regSel = document.getElementById('settings_regency');
            regSel.innerHTML = '<option value="">Kabupaten/Kota...</option>';
            regencies.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                if (r.id === regId) opt.selected = true;
                regSel.appendChild(opt);
            });
            regSel.disabled = false;

            // Now load districts
            return fetch(`/api/wilayah/districts/${regId}`);
        })
        .then(r => r.json())
        .then(districts => {
            const disSel = document.getElementById('settings_district');
            disSel.innerHTML = '<option value="">Kecamatan...</option>';
            districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.name;
                if (d.id === districtId) opt.selected = true;
                disSel.appendChild(opt);
            });
            disSel.disabled = false;
        })
        .catch(() => {});
})();
</script>

@endsection
