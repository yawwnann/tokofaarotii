@extends('layouts.app')

@section('title', isset($shippingRate) ? 'Edit Tarif Ongkir' : 'Tambah Tarif Ongkir')
@section('subtitle', isset($shippingRate) ? 'Perbarui tarif ongkos kirim zonasi' : 'Buat tarif ongkos kirim baru berdasarkan kecamatan')

@section('content')

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
                    <label class="form-label">Kecamatan Asal (Toko)</label>
                    <select name="origin_district_id" class="form-input" required>
                        <option value="">Pilih Kecamatan Asal...</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('origin_district_id', $shippingRate->origin_district_id ?? '') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kecamatan Tujuan</label>
                    <select name="destination_district_id" class="form-input" required>
                        <option value="">Pilih Kecamatan Tujuan...</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('destination_district_id', $shippingRate->destination_district_id ?? '') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
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
@endsection
