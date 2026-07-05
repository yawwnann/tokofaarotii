@extends('layouts.app')

@section('title', 'Riwayat Penjualan')
@section('subtitle', 'Pantau semua transaksi masuk dari Frozen & Bakery')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert-banner alert-danger">
    <div class="alert-inner">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:1rem;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

{{-- ── SUMMARY STATS ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-wallet"></i></div>
        <div class="stat-info">
            <span class="stat-label">Total Pendapatan</span>
            <span class="stat-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i class="fas fa-box-open"></i></div>
        <div class="stat-info">
            <span class="stat-label">Item Terjual</span>
            <span class="stat-value">{{ $totalItems }} <small>Unit</small></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-receipt"></i></div>
        <div class="stat-info">
            <span class="stat-label">Transaksi</span>
            <span class="stat-value">{{ $sales->total() }}</span>
        </div>
    </div>
</div>

{{-- ── TOOLBAR ── --}}
<div class="toolbar-wrap">
    <div class="toolbar-left">
        <h3 class="toolbar-title">Daftar Penjualan</h3>
    </div>
    <div class="toolbar-right">
        <a href="{{ route('sales.create') }}" class="btn-primary">
            <i class="fas fa-cart-plus"></i> Tambah Transaksi
        </a>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="table-container">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">NO</th>
                    <th>TANGGAL & PELANGGAN</th>
                    <th>PRODUK & KATEGORI</th>
                    <th>METODE BAYAR</th>
                    <th style="text-align:center;">TOTAL QTY</th>
                    <th style="text-align:right;">TOTAL HARGA</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @php $no = ($sales->currentPage() - 1) * $sales->perPage() + 1; @endphp
                @forelse($sales as $sale)
                <tr>
                    <td class="td-no">{{ $no++ }}</td>
                    <td>
                        <div class="td-flex">
                            <div class="td-date">{{ \Carbon\Carbon::parse($sale->sale_date)->translatedFormat('d M Y') }}</div>
                            <div class="td-sub-info text-indigo customer-name">{{ $sale->customer_name ?? 'Umum' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="td-product-group">
                            <div class="p-name-group">{{ $sale->product_names }}</div>
                            <div class="p-cat-group"><i class="fas fa-tags"></i> {{ $sale->category_names }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="td-payment">
                            @if(strtolower($sale->payment_method ?? 'tunai') == 'tunai')
                                <span class="badge-tunai"><i class="fas fa-money-bill-wave"></i> Tunai</span>
                            @else
                                <span class="badge-transfer"><i class="fas fa-credit-card"></i> {{ ucfirst($sale->payment_method ?? 'Online') }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <span class="qty-badge sale-qty">{{ $sale->total_items }}</span>
                    </td>
                    <td style="text-align:right;">
                        <span class="total-price sale-total">Rp {{ number_format($sale->total_revenue, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button onclick="reprintStruk('{{ $sale->transaction_group }}', '{{ addslashes($sale->customer_name ?? 'Umum') }}', '{{ \Carbon\Carbon::parse($sale->sale_date)->translatedFormat('d M Y') }}', '{{ addslashes($sale->product_names) }}', '{{ $sale->total_items }}', '{{ number_format($sale->total_revenue, 0, ',', '.') }}', '{{ $sale->payment_method ?? 'tunai' }}')" class="btn-action btn-view" title="Cetak Struk">
                                <i class="fas fa-print"></i> Struk
                            </button>

                            <form action="{{ route('sales.destroy', $sale->transaction_group) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Hapus">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <p>Belum ada data penjualan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sales->hasPages())
    <div class="pagination-wrap">
        {{ $sales->links() }}
    </div>
    @endif
</div>

<style>
    /* ── Stats ── */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
    .stat-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(15,23,42,.05); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; }
    .stat-icon { width: 52px; height: 52px; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-orange { background: #fff7ed; color: #f97316; }
    .icon-green { background: #f0fdf4; color: #22c55e; }
    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    .stat-value { font-size: 1.25rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .stat-value small { font-size: .8rem; color: #94a3b8; font-weight: 600; }

    /* ── Toolbar ── */
    .toolbar-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .toolbar-title { font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-primary { background: #f97316; color: #fff; padding: .625rem 1.25rem; border-radius: .75rem; font-size: .85rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-primary:hover { background: #ea580c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99, 102, 241, .2); }

    /* ── Table ── */
    .table-container { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(15,23,42,.05); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 1rem 1.25rem; text-align: left; font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    td { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .td-flex { display: flex; flex-direction: column; gap: 2px; }
    .td-date { font-size: .85rem; font-weight: 700; color: #1e293b; }
    .td-no { font-size: .75rem; font-weight: 800; color: #94a3b8; text-align: center; }
    .td-sub-info { font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
    .text-indigo { color: #f97316; }

    .td-product-group { display: flex; flex-direction: column; gap: 4px; }
    .p-name-group { font-size: .8rem; font-weight: 700; color: #1e293b; line-height: 1.4; }
    .p-cat-group { font-size: .65rem; font-weight: 600; color: #94a3b8; }

    .badge-tunai { background: #f0fdf4; color: #166534; padding: .25rem .6rem; border-radius: .5rem; font-size: .65rem; font-weight: 800; display: inline-flex; align-items: center; gap: .3rem; }
    .badge-transfer { background: #f0fdf4; color: #166534; padding: .25rem .6rem; border-radius: .5rem; font-size: .65rem; font-weight: 800; display: inline-flex; align-items: center; gap: .3rem; border: 1px solid #bbf7d0; }
    .td-payment { display: flex; flex-direction: column; gap: 4px; }

    .qty-badge { display: inline-block; padding: .2rem .6rem; background: #f1f5f9; border-radius: .5rem; font-size: .75rem; font-weight: 800; color: #475569; }
    .total-price { font-size: .9rem; font-weight: 800; color: #f97316; }

    /* Action buttons standard */
    .action-btns { display: flex; flex-direction: column; gap: 5px; align-items: center; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; padding: 0.3rem 0.75rem; border: none; border-radius: 0.35rem; font-size: 0.72rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; width: 85px; text-decoration: none; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .btn-view { background: #2563eb; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    .empty-state { text-align: center; padding: 4rem 1rem !important; color: #cbd5e1; }
    .empty-state i { font-size: 2.5rem; margin-bottom: 1rem; display: block; }
    .empty-state p { font-size: .85rem; font-weight: 600; color: #94a3b8; }

    .pagination-wrap { padding: 1.25rem; background: #f8fafc; border-top: 1px solid #f1f5f9; }

    /* ── Alert ── */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-danger { background:#fef2f2;border:1px solid #fee2e2;color:#991b1b; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6; }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .toolbar-wrap { flex-direction: column; align-items: stretch; gap: 1rem; }
        .btn-primary { justify-content: center; }
    }
</style>

<script>
const storeName = "{{ addslashes(DB::table('settings')->value('store_name') ?? 'TOKO FAA') }}";
const storeAddress = "{{ addslashes(DB::table('settings')->value('store_address') ?? '') }}";
const storePhone = "{{ addslashes(DB::table('settings')->value('store_whatsapp') ?? '') }}";

function reprintStruk(trxId, customer, date, products, qty, total, payment) {
    let strukWindow = window.open('', '', 'width=400,height=600');
    
    // Pecah nama produk jika banyak
    let productList = products.split(', ').map(p => `<div>- ${p}</div>`).join('');
    
    // Validasi string payment aman dari null / kosong
    let paymentMethodText = (payment || 'TUNAI').toUpperCase();

    strukWindow.document.write(`
        <html>
        <body style="font-family: 'Courier New', Courier, monospace; width: 300px; padding: 10px; color: #333;">
            <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 18px;">${storeName}</h2>
                ${storeAddress ? `<p style="margin: 2px 0; font-size: 10px;">${storeAddress}</p>` : ''}
                ${storePhone ? `<p style="margin: 2px 0; font-size: 10px;">${storePhone}</p>` : ''}
                <p style="margin: 5px 0 0; font-size: 9px; font-weight: bold;">(COPY STRUK)</p>
            </div>
            <div style="font-size: 10px; margin-bottom: 10px; line-height: 1.4;">
                <div style="display: flex; justify-content: space-between;"><span>No:</span> <span>${trxId}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>Tgl:</span> <span>${date}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>Plg:</span> <span>${customer}</span></div>
                <div style="display: flex; justify-content: space-between;"><span>Byr:</span> <span>${paymentMethodText}</span></div>
            </div>
            <div style="border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; font-size: 11px;">
                <div style="font-weight: bold; margin-bottom: 3px;">Item:</div>
                ${productList}
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                <span>TOTAL (${qty} item)</span>
                <span>Rp ${total}</span>
            </div>
            <div style="text-align: center; font-size: 10px; margin-top: 20px;">
                *** TERIMA KASIH ***<br>
                Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan
            </div>
        </body>
        </html>
    `);

    strukWindow.document.close();
    setTimeout(() => {
        strukWindow.print();
        strukWindow.close();
    }, 500);
}

// Auto-dismiss alerts
setTimeout(() => {
    document.querySelectorAll('.alert-banner').forEach(el => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 4000);
</script>

@endsection
