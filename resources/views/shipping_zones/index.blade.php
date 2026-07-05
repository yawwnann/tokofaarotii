@extends('layouts.app')

@section('title', 'Tarif Ongkir Zonasi')
@section('subtitle', 'Atur tarif ongkos kirim berdasarkan zona wilayah')

@section('content')

@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@if($errors->any())
<div class="alert-banner alert-danger">
    <div class="alert-inner"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

<div class="toolbar-wrap">
    <div class="toolbar-left">
        <h3 class="toolbar-title">Tarif Ongkir Zonasi</h3>
        <p class="toolbar-sub">Sistem otomatis menentukan zona berdasarkan perbandingan wilayah asal &rarr; tujuan pelanggan.</p>
    </div>
</div>

<form action="{{ route('shipping-zones.update') }}" method="POST">
    @csrf
    @method('PUT')

    @forelse($stores as $store)
    <div class="store-card">
        <div class="store-card-header">
            <div class="store-icon-circle"><i class="fas fa-store"></i></div>
            <div class="store-meta">
                <h4>{{ $store->name }}</h4>
                <span>{{ $store->district->name ?? '-' }}, {{ $store->district->regency->name ?? '-' }}, {{ $store->district->regency->province->name ?? '-' }}</span>
            </div>
        </div>
        <div class="card-body">
            @foreach($levels as $key => $label)
            @php $zone = $store->zones->get($key); @endphp
            <div class="zone-row">
                <div class="zone-info">
                    <span class="zone-dot zone-dot-{{ $key }}"></span>
                    <div>
                        <span class="zone-name">{{ $label }}</span>
                        <span class="zone-hint">{{ $key }}</span>
                    </div>
                </div>
                <div class="rp-input-wrap">
                    <span class="rp-prefix">Rp</span>
                    <input type="text" name="rates[{{ $store->id }}][{{ $key }}]"
                           class="zone-input"
                           placeholder="0"
                           oninput="formatRupiah(this)"
                           value="{{ $zone ? number_format($zone->rate, 0) : '' }}">
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-store-alt"></i>
        <p>Belum ada toko aktif. Tambah toko terlebih dahulu.</p>
        <a href="{{ route('stores.create') }}" class="btn-primary" style="display:inline-flex;margin-top:1rem;">
            <i class="fas fa-plus"></i> Tambah Toko
        </a>
    </div>
    @endforelse

    @if($stores->isNotEmpty())
    <div class="form-actions">
        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Simpan Semua Tarif
        </button>
    </div>
    @endif
</form>

<style>
    .toolbar-wrap { margin-bottom: 2rem; }
    .toolbar-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0 0 .25rem; }
    .toolbar-sub { font-size: .8rem; color: #64748b; margin: 0; }

    .store-card {
        background: #fff;
        border-radius: 1.25rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .store-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
    .store-icon-circle {
        width: 40px;
        height: 40px;
        background: #fff7ed;
        color: #f97316;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .store-meta h4 {
        font-size: .95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .store-meta span {
        font-size: .75rem;
        color: #64748b;
        margin-top: 2px;
        display: block;
    }
    .card-body {
        padding: .25rem 0;
    }

    .zone-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .75rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        gap: 1.5rem;
    }
    .zone-row:last-child { border-bottom: none; }

    .zone-info {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }
    .zone-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .zone-dot-same_district { background: #15803d; }
    .zone-dot-same_regency { background: #0284c7; }
    .zone-dot-same_province { background: #7c3aed; }
    .zone-dot-same_island { background: #b45309; }
    .zone-dot-different_island { background: #dc2626; }

    .zone-name {
        display: block;
        font-size: .875rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.3;
    }
    .zone-hint {
        display: block;
        font-size: .7rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .rp-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        width: 180px;
        flex-shrink: 0;
    }
    .rp-prefix {
        position: absolute;
        left: .75rem;
        font-size: .75rem;
        font-weight: 700;
        color: #94a3b8;
        pointer-events: none;
        z-index: 1;
    }
    .zone-input {
        width: 100%;
        padding: .5rem .75rem .5rem 2rem;
        border: 1.5px solid #e2e8f0;
        border-radius: .5rem;
        font-size: .875rem;
        font-weight: 600;
        color: #1e293b;
        text-align: right;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .zone-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249,115,22,.12);
    }
    .zone-input::placeholder {
        color: #cbd5e1;
        font-weight: 400;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        padding: 0 0 1.5rem;
    }
    .btn-save {
        background: #f97316;
        color: #fff;
        border: none;
        padding: .75rem 2.5rem;
        border-radius: .75rem;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: all .2s;
    }
    .btn-save:hover {
        background: #ea580c;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249,115,22,.2);
    }

    .btn-primary {
        background: #f97316;
        color: #fff;
        border: none;
        padding: .625rem 1.25rem;
        border-radius: .75rem;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: all .2s;
        text-decoration: none;
    }
    .btn-primary:hover {
        background: #ea580c;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249,115,22,.2);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #94a3b8;
    }
    .empty-state i {
        font-size: 3rem;
        display: block;
        margin-bottom: .75rem;
    }
    .empty-state p {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
    }

    .alert-banner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
        padding: .875rem 1.125rem;
        border-radius: .75rem;
        margin-bottom: 1.5rem;
        font-size: .85rem;
        font-weight: 600;
    }
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .alert-inner {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
    }
    .alert-banner button {
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: .6;
    }
</style>

<script>
    function formatRupiah(input) {
        let val = input.value.replace(/[^\d]/g, '');
        if (val) {
            val = parseInt(val, 10).toLocaleString('en-US');
            input.value = val;
        } else {
            input.value = '';
        }
    }

    document.querySelector('form').addEventListener('submit', function() {
        this.querySelectorAll('.zone-input').forEach(inp => {
            inp.value = inp.value.replace(/,/g, '');
        });
    });

    document.querySelectorAll('.zone-input').forEach(inp => {
        if (inp.value) formatRupiah(inp);
    });

    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>
@endsection
