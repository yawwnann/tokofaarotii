@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)
@section('subtitle', 'Informasi lengkap produk')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

{{-- ── BREADCRUMB ── --}}
<div class="breadcrumb-wrap">
    <a href="{{ route('products.index') }}" class="bc-link">
        <i class="fas fa-box"></i> Produk
    </a>
    <i class="fas fa-chevron-right bc-sep"></i>
    <span class="bc-current">{{ Str::limit($product->name, 40) }}</span>
</div>

{{-- ── MAIN LAYOUT ── --}}
<div class="detail-layout">

    {{-- ── SIDEBAR ── --}}
    <div class="detail-sidebar">

        {{-- Gambar Produk --}}
        <div class="detail-card img-card">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                     style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:.625rem;border:1px solid #f1f5f9;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div class="img-placeholder-lg" style="display:none;">
                    <div class="img-placeholder-lg-inner">
                        <i class="fas fa-image"></i>
                        <p>Gambar tidak ditemukan</p>
                    </div>
                </div>
            @else
                <div class="img-placeholder-lg">
                    <div class="img-placeholder-lg-inner">
                        <i class="fas fa-image"></i>
                        <p>Belum ada gambar</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Stok Realtime --}}
        @php
            $currentStock = $product->stockEntries->sum('quantity') - $product->sales->sum('quantity_sold');
        @endphp
        <div class="stock-dark-card">
            <div class="stock-header">
                <span class="stock-label-top">REAL-TIME INVENTORI</span>
                <i class="fas fa-cube" style="color:#6366f1;"></i>
            </div>
            <div class="stock-number-wrap">
                <span class="stock-big-num">{{ $currentStock }}</span>
                <span class="stock-unit">{{ $product->unit ?? 'pcs' }}</span>
            </div>
            @if($currentStock <= 0)
                <div class="stock-status-badge status-red">
                    <span class="status-dot dot-red"></span> STOK HABIS
                </div>
            @elseif($currentStock < 10)
                <div class="stock-status-badge status-yellow">
                    <span class="status-dot dot-yellow dot-pulse"></span> STOK RENDAH
                </div>
            @else
                <div class="stock-status-badge status-green">
                    <span class="status-dot dot-green"></span> STOK AMAN
                </div>
            @endif

            <a href="{{ route('products.index', ['open_stock' => $product->id]) }}" class="stock-refill-btn">
                <i class="fas fa-plus"></i> ISI ULANG STOK
            </a>
        </div>

        {{-- Aksi --}}
        <div class="detail-card" style="display:flex;flex-direction:column;gap:.625rem;">
            <p style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin:0;">Aksi Produk</p>
            <a href="{{ route('products.index', ['edit_id' => $product->id]) }}" class="action-btn-full btn-edit-full">
                <i class="fas fa-pencil-alt"></i> Edit Produk
            </a>
            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                  onsubmit="return confirm('Hapus produk ini secara permanen?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn-full btn-delete-full">
                    <i class="fas fa-trash-alt"></i> Hapus Produk
                </button>
            </form>
        </div>

    </div>

    {{-- ── CONTENT ── --}}
    <div class="detail-content">

        {{-- Header Info --}}
        <div class="detail-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <div>
                    @if($product->category)
                        <span class="cat-badge">{{ $product->category->name }}</span>
                    @endif
                    <h1 class="prod-title">{{ $product->name }}</h1>
                    <div class="prod-meta">
                        @if($product->sku)
                            <span><i class="fas fa-barcode"></i> SKU: <strong>{{ $product->sku }}</strong></span>
                            <span class="meta-sep">·</span>
                        @endif
                        <span><i class="far fa-calendar-alt"></i> {{ $product->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Price & Unit --}}
        <div class="info-grid">
            <div class="info-tile">
                <div class="tile-icon" style="background:#eff6ff;">
                    <i class="fas fa-tag" style="color:#3b82f6;"></i>
                </div>
                <div>
                    <p class="tile-label">Harga Jual</p>
                    <p class="tile-value">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="info-tile">
                <div class="tile-icon" style="background:#f0fdf4;">
                    <i class="fas fa-cube" style="color:#22c55e;"></i>
                </div>
                <div>
                    <p class="tile-label">Satuan</p>
                    <p class="tile-value">{{ $product->unit ?? 'pcs' }}</p>
                </div>
            </div>
            <div class="info-tile">
                <div class="tile-icon" style="background:#fff7ed;">
                    <i class="fas fa-layer-group" style="color:#f97316;"></i>
                </div>
                <div>
                    <p class="tile-label">Total Masuk</p>
                    <p class="tile-value">{{ $product->stockEntries->sum('quantity') }}</p>
                </div>
            </div>
            <div class="info-tile">
                <div class="tile-icon" style="background:#fef2f2;">
                    <i class="fas fa-shopping-cart" style="color:#ef4444;"></i>
                </div>
                <div>
                    <p class="tile-label">Total Terjual</p>
                    <p class="tile-value">{{ $product->sales->sum('quantity_sold') }}</p>
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="detail-card">
            <div class="section-title">
                <div class="section-icon" style="background:var(--color-primary,#ea580c);">
                    <i class="fas fa-align-left"></i>
                </div>
                Deskripsi Produk
            </div>
            <p class="desc-text">
                {{ $product->description ?: 'Belum ada deskripsi untuk produk ini.' }}
            </p>
        </div>

        {{-- Log Stok --}}
        <div class="detail-card" style="padding:0;overflow:hidden;">
            <div class="log-header">
                <div class="section-title" style="margin:0;">
                    <div class="section-icon" style="background:#4f46e5;">
                        <i class="fas fa-history"></i>
                    </div>
                    Log Aktivitas Stok
                </div>
                <span style="font-size:.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;">5 Terakhir</span>
            </div>

            <div class="table-wrap">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>DETAIL</th>
                            <th style="text-align:center;width:80px;">QTY</th>
                            <th style="text-align:right;width:100px;">SALDO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $runningBalance = $currentStock;
                            $entries = $product->stockEntries()->latest()->take(5)->get();
                        @endphp
                        @forelse($entries as $entry)
                        <tr class="log-row">
                            <td>
                                <div style="display:flex;align-items:center;gap:.75rem;">
                                    <div class="log-icon {{ $entry->type == 'in' ? 'log-in' : 'log-out' }}">
                                        <i class="fas {{ $entry->type == 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                    </div>
                                    <div>
                                        <p class="log-type">{{ $entry->type == 'in' ? 'Barang Masuk' : 'Barang Keluar' }}</p>
                                        <p class="log-date">{{ $entry->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="log-qty {{ $entry->type == 'in' ? 'qty-in' : 'qty-out' }}">
                                    {{ $entry->type == 'in' ? '+' : '-' }}{{ $entry->quantity }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <span class="log-balance">{{ number_format($runningBalance) }}</span>
                                {{-- 🛠️ PERBAIKAN LOGIKA DISINI: Menghitung mundur saldo awal sebelum transaksi ini terjadi --}}
                                @php 
                                    $runningBalance -= ($entry->type == 'in' ? $entry->quantity : -$entry->quantity); 
                                @endphp
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="log-empty">
                                <i class="fas fa-stream"></i>
                                <p>Belum ada riwayat stok</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<style>
    /* ─ Alert ─ */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.625rem;margin-bottom:1.25rem;font-size:.85rem;font-weight:500;animation:slideDown .3s ease; }
    @keyframes slideDown { from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)} }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6;padding:2px; }

    /* ─ Breadcrumb ─ */
    .breadcrumb-wrap { display:flex;align-items:center;gap:.5rem;margin-bottom:1.25rem;font-size:.8rem; }
    .bc-link { color:#f97316;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:.3rem; }
    .bc-link:hover { text-decoration:underline; }
    .bc-sep { font-size:.6rem;color:#cbd5e1; }
    .bc-current { color:#64748b;font-weight:500; }

    /* ─ Layout ─ */
    .detail-layout { display:grid;grid-template-columns:280px 1fr;gap:1.5rem;align-items:start; }
    .detail-sidebar { display:flex;flex-direction:column;gap:1rem; }
    .detail-content { display:flex;flex-direction:column;gap:1rem; }

    /* ─ Card Base ─ */
    .detail-card { background:#fff;border-radius:.875rem;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(15,23,42,.06);padding:1.25rem; }

    /* ─ Image Card ─ */
    .img-card { padding:.75rem; }
    .img-placeholder-lg { position:relative; width:100%; height:300px; background:linear-gradient(135deg, #fff7ed 0%, #ffedd5 50%, #fed7aa 100%); border-radius:.625rem; border:1px solid #fed7aa; overflow:hidden; flex-shrink:0; }
    .img-placeholder-lg-inner { position:absolute; top:0; left:0; width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#f97316; }
    .img-placeholder-lg-inner i { font-size:3rem; margin-bottom:.5rem; opacity:.5; }
    .img-placeholder-lg-inner p { font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#ea580c; opacity:.5; margin:0; }

    /* ─ Stock Dark Card ─ */
    .stock-dark-card { background:#0f172a;border-radius:.875rem;padding:1.5rem;color:#fff;position:relative;overflow:hidden; }
    .stock-dark-card::before { content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:rgba(99,102,241,.12);border-radius:50%;pointer-events:none; }
    .stock-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem; }
    .stock-label-top { font-size:.6rem;font-weight:800;letter-spacing:.12em;color:#475569;text-transform:uppercase; }
    .stock-number-wrap { display:flex;align-items:baseline;gap:.5rem;margin-bottom:.75rem; }
    .stock-big-num { font-size:3.5rem;font-weight:900;line-height:1;letter-spacing:-2px; }
    .stock-unit { font-size:.8rem;font-weight:700;color:#6366f1;text-transform:uppercase; }
    .stock-status-badge { display:inline-flex;align-items:center;gap:.4rem;padding:.25rem .75rem;border-radius:9999px;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:1.25rem; }
    .status-green { background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);color:#4ade80; }
    .status-yellow { background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);color:#fbbf24; }
    .status-red { background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171; }
    .status-dot { display:inline-block;width:6px;height:6px;border-radius:50%; }
    .dot-green { background:#22c55e; }
    .dot-yellow { background:#f59e0b; }
    .dot-red { background:#ef4444; }
    .dot-pulse { animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.4} }
    .stock-refill-btn { display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;background:#6366f1;color:#fff;border-radius:.625rem;font-size:.775rem;font-weight:700;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;transition:background .15s,transform .1s; }
    .stock-refill-btn:hover { background:#4f46e5;transform:translateY(-1px);color:#fff; }

    /* ─ Aksi Sidebar ─ */
    .action-btn-full { display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.6rem 1rem;border-radius:.5rem;font-size:.8rem;font-weight:600;cursor:pointer;transition:filter .15s,transform .1s;text-decoration:none;border:none;box-sizing:border-box; }
    .action-btn-full:hover { filter:brightness(1.08);transform:translateY(-1px); }
    .btn-edit-full { background:#f59e0b;color:#fff; }
    .btn-delete-full { background:#ef4444;color:#fff; }

    /* ─ Header Info ─ */
    .cat-badge { display:inline-block;padding:.2rem .65rem;background:#dbeafe;color:#1d4ed8;border-radius:9999px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem; }
    .prod-title { font-size:1.5rem;font-weight:800;color:#1e293b;margin:0 0 .5rem;line-height:1.2; }
    .prod-meta { display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;font-size:.775rem;color:#64748b; }
    .prod-meta i { color:#f97316;font-size:.65rem; }
    .meta-sep { color:#cbd5e1; }

    /* ─ Info Grid ─ */
    .info-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:.875rem; }
    .info-tile { background:#fff;border-radius:.875rem;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(15,23,42,.06);padding:1rem 1.125rem;display:flex;align-items:center;gap:.875rem; }
    .tile-icon { width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
    .tile-label { font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin:0 0 2px; }
    .tile-value { font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;line-height:1; }

    /* ─ Deskripsi ─ */
    .section-title { display:flex;align-items:center;gap:.625rem;font-size:.875rem;font-weight:700;color:#1e293b;margin-bottom:.875rem; }
    .section-icon { width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.65rem;flex-shrink:0; }
    .desc-text { font-size:.85rem;color:#475569;line-height:1.7;margin:0; }

    /* ─ Log Table ─ */
    .log-header { display:flex;align-items:center;justify-content:space-between;padding:1.125rem 1.25rem;border-bottom:1px solid #f1f5f9; }
    .table-wrap { overflow-x:auto; }
    .log-table { width:100%;border-collapse:collapse;font-size:.8rem; }
    .log-table thead tr { background:#f8fafc; }
    .log-table thead th { padding:.625rem 1.25rem;text-align:left;font-size:.65rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:#94a3b8; }
    .log-row { border-bottom:1px solid #f8fafc;transition:background .12s; }
    .log-row:hover { background:#fffbf7; }
    .log-row td { padding:.875rem 1.25rem;vertical-align:middle; }
    .log-icon { width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.65rem;flex-shrink:0; }
    .log-in { background:#dcfce7;color:#16a34a; }
    .log-out { background:#fee2e2;color:#dc2626; }
    .log-type { font-size:.8rem;font-weight:600;color:#1e293b;margin:0; }
    .log-date { font-size:.68rem;color:#94a3b8;margin:2px 0 0; }
    .log-qty { font-size:.8rem;font-weight:800; }
    .qty-in { color:#16a34a; }
    .qty-out { color:#dc2626; }
    .log-balance { font-size:.8rem;font-weight:700;color:#1e293b;font-family:monospace; }
    .log-empty { text-align:center;padding:2.5rem 1rem !important;color:#94a3b8; }
    .log-empty i { font-size:1.75rem;display:block;margin-bottom:.5rem; }
    .log-empty p { font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin:0; }

    /* ─ Responsive ─ */
    @media(max-width:900px) {
        .detail-layout { grid-template-columns:1fr; }
        .info-grid { grid-template-columns:repeat(2,1fr); }
    }
    @media(max-width:480px) {
        .info-grid { grid-template-columns:1fr; }
    }
</style>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>

@endsection