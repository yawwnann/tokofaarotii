@extends('layouts.app')

@section('title', 'Panel Kendali')
@section('subtitle', 'Pusat kendali Toko Faa Frozen & Bakery')

@section('content')

{{-- ── WELCOME BANNER ── --}}
<div class="welcome-banner">
    <div class="welcome-content">
        <h2 class="welcome-title">Halo, {{ Auth::user()->name ?? 'Admin' }}! 👋</h2>
        <p class="welcome-text">Selamat datang kembali di panel manajemen. Hari ini ada <strong>{{ $totalTransactions }}</strong> transaksi tercatat.</p>
    </div>
    <div class="welcome-actions">
        <a href="{{ route('sales.create') }}" class="btn-welcome">
            <i class="fas fa-cart-plus"></i> Kasir Baru
        </a>
    </div>
</div>

{{-- ── QUICK STATS ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-wallet"></i></div>
        <div class="stat-info">
            <span class="stat-label">Pendapatan Hari Ini</span>
            <span class="stat-value">Rp {{ number_format($todaySales, 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-shopping-basket"></i></div>
        <div class="stat-info">
            <span class="stat-label">Item Terjual</span>
            <span class="stat-value">{{ number_format($totalItemSold) }} <small>Unit</small></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i class="fas fa-boxes"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Stok</span>
            <span class="stat-value">{{ number_format($totalStock) }} <small>Item</small></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-purple"><i class="fas fa-box"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Produk</span>
            <span class="stat-value">{{ number_format($totalProducts) }}</span>
        </div>
    </div>
</div>

<div class="dashboard-main-grid">
    
    {{-- ── LEFT COLUMN: CHARTS & TOP PRODUCTS ── --}}
    <div class="dashboard-col">
        
        {{-- Penjualan Chart --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line text-orange-500"></i> Tren Penjualan</h3>
                <span class="card-subtitle">6 Bulan Terakhir</span>
            </div>
            <div class="card-body chart-container">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-crown text-amber-500"></i> Produk Terlaris</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-wrap">
                    <table class="simple-table">
                        <thead>
                            <tr>
                                <th>PRODUK</th>
                                <th style="text-align:center;">TERJUAL</th>
                                <th style="text-align:right;">RANK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $item)
                            <tr>
                                <td>
                                    <div class="td-product">
                                        <span class="p-name">{{ $item->product->name ?? 'N/A' }}</span>
                                        <span class="p-cat">{{ $item->product->category->name ?? 'Umum' }}</span>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge-qty">{{ number_format($item->total_sold) }}</span>
                                </td>
                                <td style="text-align:right;">
                                    @if($index == 0) <i class="fas fa-trophy text-amber-400"></i> @else #{{ $index + 1 }} @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-gray-400">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT COLUMN: ALERTS & CATEGORIES ── --}}
    <div class="dashboard-col">
        
        {{-- Quick Shortcuts --}}
        <div class="shortcut-grid">
            <a href="{{ route('products.index') }}" class="shortcut-item">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>
            <a href="{{ route('stock-entries.index') }}" class="shortcut-item">
                <i class="fas fa-warehouse"></i>
                <span>Stok</span>
            </a>
            <a href="{{ route('reports.index') }}" class="shortcut-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Laporan</span>
            </a>
        </div>

        {{-- Stok Alerts --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell text-red-500"></i> Stok Perlu Perhatian</h3>
                <span class="badge-count {{ ($lowStock->count() + $outOfStock->count()) > 0 ? 'bg-red' : 'bg-green' }}">
                    {{ $lowStock->count() + $outOfStock->count() }}
                </span>
            </div>
            <div class="card-body p-0 max-h-80 overflow-y-auto custom-scrollbar">
                @forelse($outOfStock as $item)
                <div class="alert-item item-red">
                    <div class="alert-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="alert-info">
                        <p class="alert-name">{{ $item->name }}</p>
                        <p class="alert-status">Stok Habis (0)</p>
                    </div>
                    <a href="{{ route('stock-entries.create', ['product_id' => $item->id]) }}" class="btn-refill">Isi</a>
                </div>
                @empty @endforelse

                @forelse($lowStock as $item)
                <div class="alert-item item-amber">
                    <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="alert-info">
                        <p class="alert-name">{{ $item->name }}</p>
                        <p class="alert-status">Stok Menipis ({{ $item->total_stok }})</p>
                    </div>
                    <a href="{{ route('stock-entries.create', ['product_id' => $item->id]) }}" class="btn-refill">Isi</a>
                </div>
                @empty @endforelse

                @if($lowStock->isEmpty() && $outOfStock->isEmpty())
                <div class="empty-alert">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <p>Semua stok aman</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Categories Performance --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie text-blue-500"></i> Performa Kategori</h3>
            </div>
            <div class="card-body">
                <div class="chart-mini">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    /* ── Welcome Banner (Clean & Professional) ── */
    .welcome-banner { 
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 1.25rem; 
        padding: 2.25rem 2.5rem; 
        color: #0f172a; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 2rem; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .welcome-content { position: relative; z-index: 2; }
    .welcome-title { font-size: 1.5rem; font-weight: 700; margin: 0 0 .5rem; letter-spacing: -0.01em; color: #0f172a; }
    .welcome-text { font-size: 1rem; color: #475569; margin: 0; font-weight: 500; }
    .btn-welcome { 
        background: #f97316;
        color: #ffffff; 
        padding: 0.75rem 1.5rem; 
        border-radius: 0.75rem; 
        font-size: 0.9rem; 
        font-weight: 600; 
        text-decoration: none; 
        display: flex; 
        align-items: center; 
        gap: .75rem; 
        transition: background-color 0.2s ease; 
    }
    .btn-welcome:hover { 
        background: #ea580c; 
    }

    /* ── Stats (Crisp & Flat) ── */
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 1.5rem; 
        margin-bottom: 2rem; 
    }
    .stat-card { 
        background: #ffffff; 
        border-radius: 1rem; 
        border: 1px solid #e2e8f0; 
        padding: 1.5rem; 
        display: flex; 
        align-items: center; 
        gap: 1.25rem; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02); 
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; 
    }
    .stat-card:hover { 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }
    .stat-icon { 
        width: 52px; 
        height: 52px; 
        border-radius: 0.85rem; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 1.2rem; 
    }
    
    .icon-blue { background: #eff6ff; color: #2563eb; }
    .icon-green { background: #f0fdf4; color: #16a34a; }
    .icon-orange { background: #fff7ed; color: #ea580c; }
    .icon-purple { background: #f5f3ff; color: #7c3aed; }
    
    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: .75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 0.25rem; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #0f172a; line-height: 1.2; letter-spacing: -0.01em; }
    .stat-value small { font-size: .85rem; color: #94a3b8; font-weight: 500; }

    /* ── Main Layout ── */
    .dashboard-main-grid { display: grid; grid-template-columns: 1fr 400px; gap: 1.5rem; }
    .dashboard-col { display: flex; flex-direction: column; gap: 1.5rem; }

    /* ── Card (Professional) ── */
    .dashboard-card { 
        background: #ffffff; 
        border-radius: 1rem; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02); 
        overflow: hidden; 
        display: flex;
        flex-direction: column;
    }
    .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #ffffff; }
    .card-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: .75rem; }
    .card-title i { font-size: 1.1rem; }
    .card-subtitle { font-size: .75rem; color: #64748b; font-weight: 500; background: #f1f5f9; padding: 0.25rem 0.75rem; border-radius: 0.5rem; }
    .card-body { padding: 1.5rem; flex: 1; }

    /* ── Table (Structured) ── */
    .simple-table { width: 100%; border-collapse: collapse; }
    .simple-table th { background: #f8fafc; padding: 0.75rem 1.25rem; text-align: left; font-size: .7rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
    .simple-table td { padding: 1rem 1.25rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .simple-table tbody tr:hover td { background: #f8fafc; }
    
    .td-product { display: flex; flex-direction: column; gap: 0.2rem; }
    .p-name { font-size: .85rem; font-weight: 600; color: #0f172a; }
    .p-cat { font-size: .75rem; color: #64748b; font-weight: 500; }
    .badge-qty { background: #f1f5f9; color: #475569; padding: .25rem .75rem; border-radius: 0.5rem; font-size: .75rem; font-weight: 600; display: inline-block; }

    /* ── Alerts (Clean Alerts) ── */
    .badge-count { padding: .25rem .75rem; border-radius: 0.5rem; font-size: .75rem; font-weight: 600; color: #ffffff; }
    .bg-red { background: #ef4444; }
    .bg-green { background: #22c55e; }
    
    .alert-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 0.75rem; margin-bottom: 0.75rem; transition: border-color 0.2s ease; }
    .alert-item:last-child { margin-bottom: 0; }
    .alert-item:hover { border-color: #e2e8f0; }
    
    .alert-icon { width: 36px; height: 36px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .item-red .alert-icon { background: #fef2f2; color: #ef4444; }
    .item-amber .alert-icon { background: #fff7ed; color: #f59e0b; }
    
    .alert-info { flex: 1; min-width: 0; }
    .alert-name { font-size: .85rem; font-weight: 600; color: #0f172a; margin: 0 0 0.1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .alert-status { font-size: .75rem; font-weight: 500; color: #64748b; margin: 0; }
    
    .btn-refill { background: #f97316; color: #ffffff; padding: .35rem .85rem; border-radius: 0.5rem; font-size: .75rem; font-weight: 600; text-decoration: none; transition: background-color 0.2s; }
    .btn-refill:hover { background: #ea580c; }
    
    .empty-alert { padding: 3rem 1.5rem; text-align: center; color: #94a3b8; display: flex; flex-direction: column; align-items: center; }
    .empty-alert i { font-size: 2.5rem; margin-bottom: 1rem; color: #cbd5e1; }
    .empty-alert p { font-size: .9rem; font-weight: 500; color: #64748b; }

    /* ── Charts ── */
    .chart-container { height: 300px; position: relative; }
    .chart-mini { height: 240px; position: relative; }

    /* ── Shortcuts (Solid Minimalist) ── */
    .shortcut-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .shortcut-item { 
        background: #ffffff; 
        border-radius: 1rem; 
        padding: 1.25rem 1rem; 
        border: 1px solid rgba(226, 232, 240, 0.8); 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        gap: .75rem; 
        text-decoration: none; 
        transition: border-color 0.2s ease, background-color 0.2s ease; 
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
    }
    .shortcut-item:hover { 
        background: #f8fafc; 
        border-color: #cbd5e1; 
    }
    .shortcut-item i { font-size: 1.25rem; color: #64748b; transition: color 0.2s ease; }
    .shortcut-item:hover i { color: #f97316; }
    .shortcut-item span { font-size: .8rem; font-weight: 600; color: #475569; }

    /* ── Utilities ── */
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    @media (max-width: 1024px) {
        .dashboard-main-grid { grid-template-columns: 1fr; }
        .welcome-banner { flex-direction: column; text-align: center; gap: 1.5rem; }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Sales Chart
    const monthlyCtx = document.getElementById('monthlySalesChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [{
                    label: 'Penjualan',
                    data: @json($chartData ?? []),
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.08)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f97316',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Penjualan: Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#f1f5f9' }, 
                        ticks: { 
                            font: { size: 10 }, 
                            callback: v => 'Rp' + v.toLocaleString('id-ID') 
                        } 
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const labels = @json($categoryLabels ?? []);
        const colors = labels.map(label => {
            const l = label.toLowerCase();
            if (l.includes('frozen')) return '#ef4444'; // Red
            if (l.includes('bakery')) return '#f59e0b'; // Amber/Orange
            return '#f97316'; // Default Orange
        });

        new Chart(categoryCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: @json($categoryData ?? []),
                    backgroundColor: colors,
                    borderWidth: 4, borderColor: '#fff'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15, font: { size: 10, weight: 'bold' } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
@endpush

@endsection
