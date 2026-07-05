@extends('layouts.app')

@section('title', isset($store) ? 'Edit Toko' : 'Tambah Toko')
@section('subtitle', isset($store) ? 'Perbarui informasi toko cabang' : 'Daftarkan toko cabang baru')

@section('content')

@php
    $districtId = old('district_id', $store->district_id ?? '');
@endphp

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

<div class="form-container">
    <div class="form-card">
        <form action="{{ isset($store) ? route('stores.update', $store->id) : route('stores.store') }}" method="POST">
            @csrf
            @if(isset($store)) @method('PUT') @endif

            <div class="form-row-2col">
                <div class="form-group">
                    <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $store->name ?? '') }}" required placeholder="Contoh: Toko FAA Pusat">
                </div>

                <div class="form-group">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-input" value="{{ old('slug', $store->slug ?? '') }}" placeholder="Kosongkan untuk generate otomatis">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Lokasi Toko (Kecamatan) <span class="text-danger">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;">
                    <select id="store_province" class="form-input" data-prefix="store" data-level="province">
                        <option value="">Provinsi...</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                        @endforeach
                    </select>
                    <select id="store_regency" class="form-input" data-prefix="store" data-level="regency" disabled>
                        <option value="">Kab/Kota...</option>
                    </select>
                    <select id="store_district" class="form-input" data-prefix="store" data-level="district" disabled>
                        <option value="">Kecamatan...</option>
                    </select>
                </div>
                <input type="hidden" name="district_id" id="store_district_id" value="{{ $districtId }}">
                <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Pilih provinsi &rarr; kabupaten &rarr; kecamatan lokasi toko.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="address" class="form-input" rows="2" placeholder="Alamat fisik toko...">{{ old('address', $store->address ?? '') }}</textarea>
            </div>

            <div class="form-row-2col">
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $store->phone ?? '') }}" placeholder="Contoh: 08123456789">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $store->email ?? '') }}" placeholder="toko@example.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status Toko</label>
                <div style="display: flex; gap: 2rem; margin-top: 0.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #1e293b; cursor: pointer;">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $store->is_active ?? '1') == '1' ? 'checked' : '' }} style="accent-color: #f97316;"> Aktif
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; color: #1e293b; cursor: pointer;">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $store->is_active ?? '1') == '0' ? 'checked' : '' }} style="accent-color: #f97316;"> Nonaktif
                    </label>
                </div>
            </div>

            <div class="form-actions-bar">
                <a href="{{ route('stores.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> {{ isset($store) ? 'Perbarui Toko' : 'Simpan Toko' }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-container { max-width: 700px; margin: 0 auto; }
    .form-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .form-group { display: flex; flex-direction: column; gap: .5rem; margin-bottom: 1.25rem; }
    .form-label { font-size: .8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .05em; }
    .form-input { width: 100%; background: #fff; border: 1px solid #cbd5e1; border-radius: .5rem; padding: .625rem .875rem; font-size: .9rem; color: #1e293b; transition: all .2s; box-sizing: border-box; }
    .form-input:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
    .form-row-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-actions-bar { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .btn-cancel { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: .625rem 1.25rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; transition: all .2s; text-decoration:none; }
    .btn-cancel:hover { background: #e2e8f0; }
    .btn-save { background: #f97316; color: #fff; border: none; padding: .625rem 1.5rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-save:hover { background: #ea580c; }
    .text-danger { color: #ef4444; }
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    @media (max-width: 700px) { .form-row-2col { grid-template-columns: 1fr; } }
</style>

<script>
function loadChildren(parent, level, prefix) {
    const sel = document.getElementById(prefix + '_' + level);
    if (!sel) return;
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled = true;

    const url = level === 'regency'
        ? `/api/wilayah/regencies/${parent.value}`
        : `/api/wilayah/districts/${parent.value}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">Pilih ' + (level === 'regency' ? 'Kab/Kota' : 'Kecamatan') + '...</option>';
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(() => { sel.innerHTML = '<option value="">Gagal</option>'; });
}

const prefix = 'store';
const provSel = document.getElementById('store_province');
const regSel = document.getElementById('store_regency');
const disSel = document.getElementById('store_district');
const hidden = document.getElementById('store_district_id');

provSel.addEventListener('change', function() {
    regSel.innerHTML = '<option value="">Kab/Kota...</option>';
    regSel.disabled = true;
    disSel.innerHTML = '<option value="">Kecamatan...</option>';
    disSel.disabled = true;
    hidden.value = '';
    if (this.value) loadChildren(this, 'regency', prefix);
});

regSel.addEventListener('change', function() {
    disSel.innerHTML = '<option value="">Kecamatan...</option>';
    disSel.disabled = true;
    hidden.value = '';
    if (this.value) loadChildren(this, 'district', prefix);
});

disSel.addEventListener('change', function() {
    hidden.value = this.value || '';
});

// Pre-populate on edit
const selectedId = '{{ $districtId }}';
if (selectedId && selectedId.length >= 7) {
    const provId = selectedId.substring(0, 2);
    const regId = selectedId.substring(0, 4);
    provSel.value = provId;

    fetch(`/api/wilayah/regencies/${provId}`)
        .then(r => r.json())
        .then(regencies => {
            regSel.innerHTML = '<option value="">Kab/Kota...</option>';
            regencies.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                if (r.id === regId) opt.selected = true;
                regSel.appendChild(opt);
            });
            regSel.disabled = false;
            return fetch(`/api/wilayah/districts/${regId}`);
        })
        .then(r => r.json())
        .then(districts => {
            disSel.innerHTML = '<option value="">Kecamatan...</option>';
            districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.name;
                if (d.id === selectedId) opt.selected = true;
                disSel.appendChild(opt);
            });
            disSel.disabled = false;
        })
        .catch(() => {});
}
</script>

@endsection
