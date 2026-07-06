@extends('layouts.app')

@section('title', 'Laporan Inventaris')
@section('subtitle', 'Pantau ketersediaan stok produk Frozen & Bakery secara real-time')

@section('content')

{{-- ── SUMMARY STATS ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-boxes"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Produk</span>
            <span class="stat-value">{{ $products->count() }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <span class="stat-label">Stok Menipis</span>
            <span class="stat-value">{{ $lowStock->count() }} <small>Item</small></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <span class="stat-label">Stok Habis</span>
            <span class="stat-value">{{ $outOfStock->count() }} <small>Item</small></span>
        </div>
    </div>
</div>

{{-- ── STOCK CHART ── --}}
<div class="dashboard-card mb-6">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-bar text-blue-500"></i> Visualisasi Stok Produk</h3>
        <span class="card-subtitle">Level stok per produk</span>
    </div>
    <div class="card-body">
        <div class="chart-container" style="height: 350px;">
            <canvas id="stockBarChart"></canvas>
        </div>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="table-container">
    <div class="table-header">
        <h3 class="table-title">Status Stok Produk</h3>
        <button onclick="window.print()" class="btn-print-outline">
            <i class="fas fa-print"></i> Cetak Laporan
        </button>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>PRODUK</th>
                    <th>KATEGORI</th>
                    <th style="text-align:center;">MASUK</th>
                    <th style="text-align:center;">TERJUAL</th>
                    <th style="text-align:center;">SISA STOK</th>
                    <th style="text-align:center;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="td-product">
                            <span class="p-name">{{ $product->name }}</span>
                            <span class="p-sku">SKU: {{ $product->sku ?? '-' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="cat-badge">{{ $product->category->name ?? '-' }}</span>
                    </td>
                    <td style="text-align:center;">
                        {{-- stock_in_total di-set oleh controller via withStockData() --}}
                        <span class="stock-num text-blue">{{ number_format($product->stock_in_total ?? 0) }}</span>
                    </td>
                    <td style="text-align:center;">
                        {{-- sold_total = offline + online, di-set controller via injectOnlineSold() --}}
                        <span class="stock-num text-orange">{{ number_format(($product->offline_sold_total ?? 0) + ($product->online_sold_total ?? 0)) }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="stock-num-bold {{ $product->total_stok < 10 ? 'text-red' : 'text-green' }}">
                            {{ number_format($product->total_stok) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($product->total_stok <= 0)
                            <span class="status-pill pill-red">Habis</span>
                        @elseif($product->total_stok < 10)
                            <span class="status-pill pill-amber">Menipis</span>
                        @else
                            <span class="status-pill pill-green">Aman</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>Belum ada data produk</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* ── Stats ── */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
    .stat-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(15,23,42,.05); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; }
    .stat-icon { width: 52px; height: 52px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-amber { background: #fffbeb; color: #d97706; }
    .icon-red { background: #fef2f2; color: #ef4444; }
    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    .stat-value { font-size: 1.25rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .stat-value small { font-size: .8rem; color: #94a3b8; font-weight: 600; }

    /* ── Table ── */
    .table-container { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(15,23,42,.05); overflow: hidden; }
    .table-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; justify-content: space-between; }
    .table-title { font-size: .95rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-print-outline { background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; padding: .5rem 1rem; border-radius: .625rem; font-size: .75rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .4rem; transition: all .2s; }
    .btn-print-outline:hover { background: #f8fafc; border-color: #cbd5e1; color: #475569; }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .td-product { display: flex; flex-direction: column; gap: 2px; }
    .p-name { font-size: .85rem; font-weight: 700; color: #1e293b; }
    .p-sku { font-size: .65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; }

    .cat-badge { display: inline-block; padding: .2rem .6rem; background: #f1f5f9; border-radius: .5rem; font-size: .7rem; font-weight: 700; color: #64748b; }
    .stock-num { font-size: .85rem; font-weight: 600; }
    .stock-num-bold { font-size: .95rem; font-weight: 900; }
    .text-blue { color: #3b82f6; }
    .text-orange { color: #f97316; }
    .text-red { color: #ef4444; }
    .text-green { color: #22c55e; }

    .status-pill { display: inline-block; padding: .25rem .75rem; border-radius: 9999px; font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
    .pill-green { background: #f0fdf4; color: #16a34a; }
    .pill-amber { background: #fffbeb; color: #d97706; }
    .pill-red { background: #fef2f2; color: #dc2626; }

    .mb-6 { margin-bottom: 1.5rem; }
    .dashboard-card { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(15,23,42,.05); overflow: hidden; }
    .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; justify-content: space-between; }
    .card-title { font-size: .95rem; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: .6rem; }
    .card-subtitle { font-size: .75rem; color: #94a3b8; font-weight: 600; }
    .card-body { padding: 1.5rem; }
    .chart-container { position: relative; width: 100%; }

    .empty-state { text-align: center; padding: 4rem 1rem !important; color: #cbd5e1; }
    .empty-state i { font-size: 2.5rem; margin-bottom: 1rem; display: block; }
    .empty-state p { font-size: .85rem; font-weight: 600; color: #94a3b8; }

    @media print {
        .btn-print-outline, .dashboard-card { display: none; }
        .table-container { border: none; box-shadow: none; }
        body { background: #fff; }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stockBarChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($products->pluck('name')),
                datasets: [{
                    label: 'Sisa Stok',
                    data: @json($products->map(fn($p) => $p->total_stok)),
                    backgroundColor: @json($products->map(fn($p) => $p->total_stok <= 0 ? '#ef4444' : ($p->total_stok < 10 ? '#f59e0b' : '#3b82f6'))),
                    {{-- total_stok menggunakan fast path jika controller memanggil withStockData + injectOnlineSold --}}
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush

@endsection
