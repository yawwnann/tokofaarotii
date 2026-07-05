@extends('layouts.app')

@section('title', isset($shippingRate) ? 'Edit Tarif Ongkir' : 'Tambah Tarif Ongkir')
@section('subtitle', isset($shippingRate) ? 'Perbarui tarif ongkos kirim zonasi' : 'Buat tarif ongkos kirim baru berdasarkan kecamatan')

@section('content')

@php
    $originId = old('origin_district_id', $shippingRate->origin_district_id ?? '');
    $destId   = old('destination_district_id', $shippingRate->destination_district_id ?? '');
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
        <form action="{{ isset($shippingRate) ? route('shipping-rates.update', $shippingRate->id) : route('shipping-rates.store') }}" method="POST">
            @csrf
            @if(isset($shippingRate)) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Asal (Toko)</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;">
                        <select id="origin_province" class="form-input" data-prefix="origin" data-level="province">
                            <option value="">Provinsi...</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                            @endforeach
                        </select>
                        <select id="origin_regency" class="form-input" data-prefix="origin" data-level="regency" disabled>
                            <option value="">Kab/Kota...</option>
                        </select>
                        <select id="origin_district" class="form-input" data-prefix="origin" data-level="district" disabled>
                            <option value="">Kecamatan...</option>
                        </select>
                    </div>
                    <input type="hidden" name="origin_district_id" id="origin_district_id" value="{{ old('origin_district_id', $shippingRate->origin_district_id ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Tujuan</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;">
                        <select id="dest_province" class="form-input" data-prefix="dest" data-level="province">
                            <option value="">Provinsi...</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                            @endforeach
                        </select>
                        <select id="dest_regency" class="form-input" data-prefix="dest" data-level="regency" disabled>
                            <option value="">Kab/Kota...</option>
                        </select>
                        <select id="dest_district" class="form-input" data-prefix="dest" data-level="district" disabled>
                            <option value="">Kecamatan...</option>
                        </select>
                    </div>
                    <input type="hidden" name="destination_district_id" id="dest_district_id" value="{{ old('destination_district_id', $shippingRate->destination_district_id ?? '') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tarif Ongkir (Rp)</label>
                <input type="number" name="rate" class="form-input" value="{{ old('rate', $shippingRate->rate ?? '') }}" min="0" step="500" required placeholder="Contoh: 15000">
            </div>

            <div class="form-actions-bar">
                <a href="{{ route('shipping-rates.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> {{ isset($shippingRate) ? 'Perbarui Tarif' : 'Simpan Tarif' }}
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
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-actions-bar { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .btn-cancel { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: .625rem 1.25rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; transition: all .2s; }
    .btn-cancel:hover { background: #e2e8f0; }
    .btn-save { background: #f97316; color: #fff; border: none; padding: .625rem 1.5rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-save:hover { background: #ea580c; }
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    @media (max-width: 700px) { .form-row { grid-template-columns: 1fr; } }
</style>

<script>
function setupCascading(prefix, selectedId) {
    const provSel = document.getElementById(prefix + '_province');
    const regSel  = document.getElementById(prefix + '_regency');
    const disSel  = document.getElementById(prefix + '_district');
    const hidden  = document.getElementById(prefix + '_district_id');

    function loadChildren(parent, level) {
        const sel = level === 'regency' ? regSel : disSel;
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

    provSel.addEventListener('change', function() {
        regSel.innerHTML = '<option value="">Kab/Kota...</option>';
        regSel.disabled = true;
        disSel.innerHTML = '<option value="">Kecamatan...</option>';
        disSel.disabled = true;
        hidden.value = '';
        if (this.value) loadChildren(this, 'regency');
    });

    regSel.addEventListener('change', function() {
        disSel.innerHTML = '<option value="">Kecamatan...</option>';
        disSel.disabled = true;
        hidden.value = '';
        if (this.value) loadChildren(this, 'district');
    });

    disSel.addEventListener('change', function() {
        hidden.value = this.value || '';
    });

    // Pre-populate on edit
    if (selectedId && selectedId.length >= 7) {
        const provId = selectedId.substring(0, 2);
        const regId  = selectedId.substring(0, 4);
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
}

setupCascading('origin', '{{ $originId }}');
setupCascading('dest', '{{ $destId }}');
</script>
@endsection
