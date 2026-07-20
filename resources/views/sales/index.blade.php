@extends('layouts.app')

@section('title', 'Penjualan Offline')
@section('subtitle', 'Pantau semua transaksi penjualan offline Toko Faa')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success" id="alertBanner">
    <div class="alert-inner">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button onclick="this.closest('.alert-banner').remove()">
        <i class="fas fa-times"></i>
    </button>
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
<div class="sale-stats-grid">
    <div class="sale-stat-card">
        <div class="sale-stat-icon icon-blue"><i class="fas fa-wallet"></i></div>
        <div class="sale-stat-info">
            <span class="sale-stat-label">Total Pendapatan</span>
            <span class="sale-stat-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="sale-stat-card">
        <div class="sale-stat-icon icon-orange"><i class="fas fa-box-open"></i></div>
        <div class="sale-stat-info">
            <span class="sale-stat-label">Item Terjual</span>
            <span class="sale-stat-value">{{ number_format($totalItems) }} <small>Unit</small></span>
        </div>
    </div>
    <div class="sale-stat-card">
        <div class="sale-stat-icon icon-green"><i class="fas fa-receipt"></i></div>
        <div class="sale-stat-info">
            <span class="sale-stat-label">Total Transaksi</span>
            <span class="sale-stat-value">{{ $sales->total() }}</span>
        </div>
    </div>
</div>

{{-- ── MAIN CARD ── --}}
<div class="sale-card">

    {{-- Card Header --}}
    <div class="sale-card-header">
        <div class="sale-card-title">
            <i class="fas fa-shopping-cart"></i>
            <div>
                <h2>Riwayat Penjualan Offline</h2>
                <p>Daftar semua transaksi penjualan langsung di toko</p>
            </div>
        </div>
        <a href="{{ route('sales.create') }}" class="btn-primary-sale">
            <i class="fas fa-cart-plus"></i> Tambah Transaksi
        </a>
    </div>

    {{-- Toolbar: Search --}}
    <div class="sale-toolbar">
        <div class="sale-toolbar-left">
            <span class="sale-toolbar-info">
                <i class="fas fa-database"></i>
                Menampilkan {{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }} dari {{ $sales->total() }} transaksi
            </span>
        </div>
        <div class="sale-toolbar-right">
            <div class="sale-search-wrap" id="searchWrap">
                <i class="fas fa-search sale-search-icon"></i>
                <input type="text"
                       id="searchInput"
                       placeholder="Cari pelanggan, produk..."
                       class="sale-search-input"
                       autocomplete="off">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="sale-table-wrap">
        <table class="sale-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th>TANGGAL & PELANGGAN</th>
                    <th>PRODUK & KATEGORI</th>
                    <th>METODE BAYAR</th>
                    <th style="text-align:center;">QTY</th>
                    <th style="text-align:right;">TOTAL HARGA</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody id="salesTableBody">
                @php $no = ($sales->currentPage() - 1) * $sales->perPage() + 1; @endphp
                @forelse($sales as $sale)
                <tr class="sale-row" data-search="{{ strtolower(($sale->customer_name ?? 'Umum') . ' ' . $sale->product_names . ' ' . $sale->category_names) }}">
                    {{-- NO --}}
                    <td class="col-no">
                        <span class="row-num">{{ $no++ }}</span>
                    </td>

                    {{-- TANGGAL & PELANGGAN --}}
                    <td>
                        <div class="td-flex">
                            <div class="td-date">{{ \Carbon\Carbon::parse($sale->sale_date)->translatedFormat('d M Y') }}</div>
                            <div class="td-customer"><i class="fas fa-user"></i> {{ $sale->customer_name ?? 'Umum' }}</div>
                        </div>
                    </td>

                    {{-- PRODUK & KATEGORI --}}
                    <td>
                        <div class="td-product-group">
                            <div class="p-name-group">{{ $sale->product_names }}</div>
                            <div class="p-cat-group"><i class="fas fa-tags"></i> {{ $sale->category_names }}</div>
                        </div>
                    </td>

                    {{-- METODE BAYAR --}}
                    <td>
                        @if(strtolower($sale->payment_method ?? 'tunai') == 'tunai')
                            <span class="badge-pay badge-tunai"><i class="fas fa-money-bill-wave"></i> Tunai</span>
                        @elseif(strtolower($sale->payment_method ?? '') == 'transfer')
                            <span class="badge-pay badge-transfer"><i class="fas fa-university"></i> Transfer</span>
                        @else
                            <span class="badge-pay badge-other"><i class="fas fa-credit-card"></i> {{ ucfirst($sale->payment_method ?? 'Lainnya') }}</span>
                        @endif
                    </td>

                    {{-- QTY --}}
                    <td style="text-align:center;">
                        <span class="qty-badge">{{ $sale->total_items }}</span>
                    </td>

                    {{-- TOTAL HARGA --}}
                    <td style="text-align:right;">
                        <span class="total-price">Rp {{ number_format($sale->total_revenue, 0, ',', '.') }}</span>
                    </td>

                    {{-- AKSI --}}
                    <td class="col-action">
                        <div class="sale-action-btns">
                            <button onclick="reprintStruk('{{ $sale->transaction_group }}', '{{ addslashes($sale->customer_name ?? 'Umum') }}', '{{ \Carbon\Carbon::parse($sale->sale_date)->translatedFormat('d M Y') }}', '{{ addslashes($sale->product_names) }}', '{{ $sale->total_items }}', '{{ number_format($sale->total_revenue, 0, ',', '.') }}', '{{ $sale->payment_method ?? 'tunai' }}')" class="btn-action btn-print" title="Cetak Struk">
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
                        <div class="empty-state-inner">
                            <i class="fas fa-receipt"></i>
                            <p>Belum ada data penjualan</p>
                            <a href="{{ route('sales.create') }}" class="btn-primary-sale" style="margin-top:.75rem;font-size:.8rem;">
                                <i class="fas fa-cart-plus"></i> Tambah Transaksi Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- No search results message --}}
    <div id="noSearchResults" class="empty-state" style="display:none;padding:2rem 1rem;">
        <div class="empty-state-inner">
            <i class="fas fa-search"></i>
            <p>Tidak ada transaksi yang cocok</p>
        </div>
    </div>

    {{-- Pagination --}}
    @if($sales->hasPages())
    <div class="sale-pagination">
        <div class="pagination-info">
            Menampilkan {{ $sales->firstItem() }}–{{ $sales->lastItem() }} dari {{ $sales->total() }} transaksi
        </div>
        <div>{!! $sales->links() !!}</div>
    </div>
    @endif

</div>

{{-- ── STYLES ── --}}
<style>
    /* ─ Alert ─ */
    .alert-banner { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; padding:.875rem 1.125rem; border-radius:.625rem; margin-bottom:1.25rem; font-size:.85rem; font-weight:500; animation:saleSlideDown .3s ease; }
    @keyframes saleSlideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .alert-danger  { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
    .alert-inner   { display:flex; align-items:flex-start; gap:.5rem; }
    .alert-banner button { background:none; border:none; cursor:pointer; color:inherit; opacity:.6; padding:2px; }
    .alert-banner button:hover { opacity:1; }

    /* ─ Stats ─ */
    .sale-stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1.25rem; margin-bottom:1.5rem; }
    .sale-stat-card { background:#fff; border-radius:1.25rem; border:1px solid #f1f5f9; box-shadow:0 1px 3px rgba(15,23,42,.05); padding:1.25rem; display:flex; align-items:center; gap:1rem; transition:transform .2s, box-shadow .2s; }
    .sale-stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(15,23,42,.08); }
    .sale-stat-icon { width:52px; height:52px; border-radius:1rem; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
    .sale-stat-icon.icon-blue   { background:#eff6ff; color:#3b82f6; }
    .sale-stat-icon.icon-orange { background:#fff7ed; color:#f97316; }
    .sale-stat-icon.icon-green  { background:#f0fdf4; color:#22c55e; }
    .sale-stat-info { display:flex; flex-direction:column; }
    .sale-stat-label { font-size:.7rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; }
    .sale-stat-value { font-size:1.25rem; font-weight:800; color:#1e293b; line-height:1.2; }
    .sale-stat-value small { font-size:.8rem; color:#94a3b8; font-weight:600; }

    /* ─ Card ─ */
    .sale-card { background:#fff; border-radius:.875rem; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(15,23,42,.06); overflow:hidden; }

    /* ─ Card Header ─ */
    .sale-card-header { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid #f1f5f9; gap:1rem; flex-wrap:wrap; }
    .sale-card-title  { display:flex; align-items:center; gap:.75rem; }
    .sale-card-title > i { font-size:1.25rem; color:#f97316; }
    .sale-card-title h2 { font-size:.95rem; font-weight:700; color:#1e293b; margin:0; }
    .sale-card-title p  { font-size:.75rem; color:#94a3b8; margin:2px 0 0; }

    /* ─ Primary Button ─ */
    .btn-primary-sale { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1rem; background:var(--color-primary,#f97316); color:#fff; border:none; border-radius:.5rem; font-size:.825rem; font-weight:600; cursor:pointer; text-decoration:none; box-shadow:0 3px 10px rgba(249,115,22,.35); transition:box-shadow .2s,transform .15s; white-space:nowrap; }
    .btn-primary-sale:hover { box-shadow:0 5px 16px rgba(249,115,22,.45); transform:translateY(-1px); color:#fff; }

    /* ─ Toolbar ─ */
    .sale-toolbar { display:flex; align-items:center; justify-content:space-between; padding:.875rem 1.5rem; border-bottom:1px solid #f8fafc; gap:1rem; flex-wrap:wrap; background:#fafafa; }
    .sale-toolbar-left  { display:flex; align-items:center; gap:.5rem; }
    .sale-toolbar-right { display:flex; align-items:center; gap:.75rem; }
    .sale-toolbar-info { font-size:.78rem; color:#94a3b8; font-weight:500; display:flex; align-items:center; gap:.4rem; }
    .sale-toolbar-info i { font-size:.65rem; }

    /* ─ Search ─ */
    .sale-search-wrap { position:relative; display:flex; align-items:center; }
    .sale-search-icon { position:absolute; left:.625rem; font-size:.7rem; color:#94a3b8; pointer-events:none; }
    .sale-search-input { padding:.4rem .75rem .4rem 1.875rem; border:1px solid #e2e8f0; border-radius:.4rem; font-size:.8rem; color:#1e293b; background:#fff; outline:none; width:220px; transition:border-color .18s,box-shadow .18s,width .25s; font-family:inherit; }
    .sale-search-input:focus { border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,.12); width:260px; }

    /* ─ Table ─ */
    .sale-table-wrap { overflow-x:auto; }
    .sale-table { width:100%; border-collapse:collapse; font-size:.825rem; }
    .sale-table thead tr { background:#f8fafc; border-bottom:2px solid #f1f5f9; }
    .sale-table thead th { padding:.75rem 1.25rem; text-align:left; font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
    .col-no { width:50px; text-align:center !important; }
    .col-action { width:120px; text-align:center !important; }
    .sale-row { border-bottom:1px solid #f1f5f9; transition:background .15s; }
    .sale-row:hover { background:#fffbf7; }
    .sale-row td { padding:.875rem 1.25rem; vertical-align:middle; }

    /* ─ Row Elements ─ */
    .row-num { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; background:#fff7ed; color:#f97316; font-size:.7rem; font-weight:700; }

    .td-flex { display:flex; flex-direction:column; gap:3px; }
    .td-date { font-size:.85rem; font-weight:700; color:#1e293b; }
    .td-customer { font-size:.68rem; font-weight:700; color:#f97316; text-transform:uppercase; letter-spacing:.03em; display:flex; align-items:center; gap:.3rem; }
    .td-customer i { font-size:.55rem; opacity:.7; }

    .td-product-group { display:flex; flex-direction:column; gap:4px; }
    .p-name-group { font-size:.8rem; font-weight:700; color:#1e293b; line-height:1.4; }
    .p-cat-group { font-size:.65rem; font-weight:600; color:#94a3b8; display:flex; align-items:center; gap:.3rem; }
    .p-cat-group i { font-size:.55rem; }

    /* ─ Payment Badges ─ */
    .badge-pay { padding:.25rem .6rem; border-radius:.5rem; font-size:.65rem; font-weight:800; display:inline-flex; align-items:center; gap:.3rem; }
    .badge-tunai { background:#f0fdf4; color:#166534; }
    .badge-transfer { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
    .badge-other { background:#f5f3ff; color:#6d28d9; border:1px solid #ddd6fe; }

    .qty-badge { display:inline-block; padding:.2rem .6rem; background:#f1f5f9; border-radius:.5rem; font-size:.75rem; font-weight:800; color:#475569; }
    .total-price { font-size:.9rem; font-weight:800; color:#f97316; white-space:nowrap; }

    /* ─ Action Buttons ─ */
    .sale-action-btns { display:flex; flex-direction:column; gap:5px; align-items:center; }
    .btn-action { display:inline-flex; align-items:center; justify-content:center; gap:.3rem; padding:.3rem .75rem; border:none; border-radius:.35rem; font-size:.72rem; font-weight:600; cursor:pointer; transition:filter .15s,transform .1s; white-space:nowrap; width:85px; text-decoration:none; }
    .btn-action:hover { filter:brightness(1.1); transform:translateY(-1px); }
    .btn-print  { background:#2563eb; color:#fff; }
    .btn-delete { background:#ef4444; color:#fff; }

    /* ─ Empty State ─ */
    .empty-state { text-align:center; padding:3rem 1rem !important; color:#94a3b8; }
    .empty-state-inner { display:flex; flex-direction:column; align-items:center; }
    .empty-state-inner i { font-size:2.5rem; display:block; margin-bottom:.75rem; color:#cbd5e1; }
    .empty-state-inner p { margin:.25rem 0 0; font-size:.9rem; font-weight:600; }

    /* ─ Pagination ─ */
    .sale-pagination { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.5rem; border-top:1px solid #f1f5f9; background:#fafafa; flex-wrap:wrap; gap:.5rem; }
    .pagination-info { font-size:.8rem; color:#64748b; }

    /* ─ Responsive ─ */
    @media (max-width:768px) {
        .sale-stats-grid { grid-template-columns:1fr; }
        .sale-card-header { flex-direction:column; align-items:flex-start; }
        .sale-toolbar { flex-direction:column; align-items:flex-start; }
        .sale-toolbar-right { width:100%; }
        .sale-search-input { width:100%; }
        .sale-search-input:focus { width:100%; }
        .sale-pagination { flex-direction:column; align-items:flex-start; }
    }
</style>

<script>
const storeName = "{{ addslashes(DB::table('settings')->value('store_name') ?? 'TOKO FAA') }}";
const storeAddress = "{{ addslashes(DB::table('settings')->value('store_address') ?? '') }}";
const storePhone = "{{ addslashes(DB::table('settings')->value('store_whatsapp') ?? '') }}";

function reprintStruk(trxId, customer, date, products, qty, total, payment) {
    let strukWindow = window.open('', '', 'width=400,height=600');
    let productList = products.split(', ').map(p => `<div>- ${p}</div>`).join('');
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

// Client-side search filter
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.sale-row');
        const noResults = document.getElementById('noSearchResults');
        let visibleCount = 0;

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            const match = !query || searchData.includes(query);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        if (noResults) {
            noResults.style.display = (query && visibleCount === 0) ? 'block' : 'none';
        }
    });
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
