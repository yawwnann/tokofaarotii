@extends('layouts.app')

@section('title', 'Tambah Penjualan Offline')

@section('content')

{{-- ── ALERT ── --}}
@if(isset($errors) && $errors->any())
<div class="pos-alert pos-alert-danger">
    <div class="pos-alert-inner"><i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:1rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    <button onclick="this.closest('.pos-alert').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

<div class="pos-layout">

    {{-- ══════ LEFT: KATALOG ══════ --}}
    <div class="pos-catalog">
        <div class="pos-catalog-header">
            <div class="pos-catalog-title">
                <i class="fas fa-store"></i>
                <div>
                    <h2>Katalog Produk</h2>
                    <p>Klik produk untuk menambah ke keranjang</p>
                </div>
            </div>
            <div class="pos-search-wrap">
                <i class="fas fa-search pos-search-icon"></i>
                <input type="text" id="productSearch" placeholder="Cari produk..." class="pos-search-input">
            </div>
        </div>

        {{-- Category Filter --}}
        <div class="pos-category-bar">
            <button type="button" class="pos-cat-btn active" data-cat="all">Semua</button>
            @php $categories = $products->pluck('category')->unique('id')->filter(); @endphp
            @foreach($categories as $cat)
                <button type="button" class="pos-cat-btn" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- Product Grid --}}
        <div class="pos-product-grid" id="productGrid">
            @foreach($products as $product)
            <div class="pos-product-card"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-cat="{{ $product->category_id }}"
                 onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">

                <div class="pos-product-img">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div class="pos-product-placeholder"><i class="fas fa-bread-slice"></i></div>
                    @endif
                </div>

                <div class="pos-product-info">
                    <h3 class="pos-product-name">{{ $product->name }}</h3>
                    @if($product->category)
                        <span class="pos-product-cat">{{ $product->category->name }}</span>
                    @endif
                    <span class="pos-product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pos-no-results" id="noResults" style="display:none;">
            <i class="fas fa-search"></i>
            <p>Produk tidak ditemukan</p>
        </div>
    </div>

    {{-- ══════ RIGHT: KERANJANG ══════ --}}
    <div class="pos-cart">
        <div class="pos-cart-header">
            <div class="pos-cart-title"><i class="fas fa-shopping-cart"></i> Keranjang</div>
            <button type="button" class="pos-cart-clear" id="clearCartBtn" style="display:none;" onclick="clearCart()">
                <i class="fas fa-trash-alt"></i> Kosongkan
            </button>
        </div>

        {{-- Customer --}}
        <div class="pos-customer-bar">
            <div class="pos-customer-icon"><i class="fas fa-user"></i></div>
            <input type="text" id="customerName" placeholder="Nama Pelanggan (Umum)" class="pos-customer-input">
        </div>

        {{-- Cart Items --}}
        <div class="pos-cart-items" id="cartItems">
            <div class="pos-cart-empty">
                <i class="fas fa-shopping-basket"></i>
                <p>Klik produk untuk menambah</p>
            </div>
        </div>

        {{-- Payment Method --}}
        <div class="pos-payment-section" id="paymentSection" style="display:none;">
            <label class="pos-payment-label"><i class="fas fa-credit-card"></i> Metode Bayar</label>
            <div class="pos-payment-options">
                <button type="button" class="pos-pay-btn active" data-method="tunai" onclick="selectPayment(this)">
                    <i class="fas fa-money-bill-wave"></i> Tunai
                </button>
                <button type="button" class="pos-pay-btn" data-method="transfer" onclick="selectPayment(this)">
                    <i class="fas fa-university"></i> Transfer
                </button>
            </div>
        </div>

        {{-- Footer Total --}}
        <div class="pos-cart-footer">
            <div class="pos-total-row">
                <div>
                    <span class="pos-total-label">Total Pembayaran</span>
                    <div class="pos-total-value">
                        <span class="pos-rp">Rp</span>
                        <span id="grandTotal">0</span>
                    </div>
                </div>
                <span class="pos-item-count" id="itemCount" style="display:none;">0 item</span>
            </div>
            <button type="button" id="submitBtn" class="pos-submit-btn" onclick="submitOrder()" disabled>
                <i class="fas fa-check-circle"></i> SELESAIKAN ORDER
            </button>
        </div>
    </div>
</div>

{{-- ══════ STRUK MODAL ══════ --}}
<div class="pos-struk-overlay" id="strukOverlay">
    <div class="pos-struk-box" id="strukContent"></div>
</div>

<style>
/* ═══ Layout ═══ */
.pos-layout { display:grid; grid-template-columns:1fr 380px; gap:1.5rem; min-height:calc(100vh - 120px); }

/* ═══ Alert ═══ */
.pos-alert { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; padding:.875rem 1.125rem; border-radius:.625rem; margin-bottom:1.25rem; font-size:.85rem; font-weight:500; animation:posSlide .3s ease; }
@keyframes posSlide { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
.pos-alert-danger { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.pos-alert-inner { display:flex; align-items:flex-start; gap:.5rem; }
.pos-alert button { background:none; border:none; cursor:pointer; color:inherit; opacity:.6; }

/* ═══ Catalog ═══ */
.pos-catalog { background:#fff; border-radius:.875rem; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(15,23,42,.06); display:flex; flex-direction:column; overflow:hidden; }
.pos-catalog-header { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid #f1f5f9; gap:1rem; flex-wrap:wrap; }
.pos-catalog-title { display:flex; align-items:center; gap:.75rem; }
.pos-catalog-title > i { font-size:1.25rem; color:#f97316; }
.pos-catalog-title h2 { font-size:.95rem; font-weight:700; color:#1e293b; margin:0; }
.pos-catalog-title p { font-size:.75rem; color:#94a3b8; margin:2px 0 0; }

.pos-search-wrap { position:relative; }
.pos-search-icon { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); font-size:.7rem; color:#94a3b8; }
.pos-search-input { padding:.5rem .75rem .5rem 2rem; border:1px solid #e2e8f0; border-radius:.5rem; font-size:.8rem; outline:none; width:220px; transition:border-color .18s,box-shadow .18s; font-family:inherit; }
.pos-search-input:focus { border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,.12); }

/* ═══ Category Bar ═══ */
.pos-category-bar { display:flex; gap:.5rem; padding:.75rem 1.5rem; border-bottom:1px solid #f8fafc; background:#fafafa; overflow-x:auto; flex-shrink:0; }
.pos-category-bar::-webkit-scrollbar { height:0; }
.pos-cat-btn { padding:.35rem .75rem; border-radius:9999px; border:1px solid #e2e8f0; background:#fff; font-size:.72rem; font-weight:600; color:#64748b; cursor:pointer; white-space:nowrap; transition:all .18s; }
.pos-cat-btn:hover { border-color:#f97316; color:#f97316; }
.pos-cat-btn.active { background:#f97316; color:#fff; border-color:#f97316; }

/* ═══ Product Grid ═══ */
.pos-product-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(155px, 1fr)); gap:.875rem; padding:1.25rem 1.5rem; overflow-y:auto; flex:1; max-height:calc(100vh - 310px); }
.pos-product-grid::-webkit-scrollbar { width:4px; }
.pos-product-grid::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:99px; }

.pos-product-card { background:#fff; border-radius:.75rem; border:1px solid #f1f5f9; cursor:pointer; overflow:hidden; transition:all .2s; }
.pos-product-card:hover { border-color:#f97316; box-shadow:0 4px 16px rgba(249,115,22,.12); transform:translateY(-2px); }

.pos-product-img { aspect-ratio:1; background:#f8fafc; overflow:hidden; }
.pos-product-img img { width:100%; height:100%; object-fit:cover; transition:transform .4s; }
.pos-product-card:hover .pos-product-img img { transform:scale(1.08); }
.pos-product-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:2rem; }

.pos-product-info { padding:.625rem .75rem .75rem; }
.pos-product-name { font-size:.78rem; font-weight:700; color:#1e293b; margin:0 0 2px; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.pos-product-cat { display:block; font-size:.6rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.03em; margin-bottom:4px; }
.pos-product-price { font-size:.8rem; font-weight:800; color:#f97316; }

.pos-no-results { text-align:center; padding:3rem 1rem; color:#cbd5e1; }
.pos-no-results i { font-size:2rem; margin-bottom:.5rem; display:block; }
.pos-no-results p { font-size:.85rem; font-weight:600; color:#94a3b8; }

/* ═══ Cart ═══ */
.pos-cart { background:#fff; border-radius:.875rem; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(15,23,42,.06); display:flex; flex-direction:column; max-height:calc(100vh - 120px); position:sticky; top:86px; }
.pos-cart-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9; }
.pos-cart-title { font-size:.9rem; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:.5rem; }
.pos-cart-title i { color:#f97316; }
.pos-cart-clear { background:none; border:none; font-size:.72rem; font-weight:600; color:#ef4444; cursor:pointer; display:flex; align-items:center; gap:.3rem; padding:.25rem .5rem; border-radius:.375rem; transition:background .15s; }
.pos-cart-clear:hover { background:#fef2f2; }

/* Customer */
.pos-customer-bar { display:flex; align-items:center; gap:.75rem; padding:.75rem 1.25rem; border-bottom:1px solid #f8fafc; }
.pos-customer-icon { width:32px; height:32px; border-radius:50%; background:#fff7ed; display:flex; align-items:center; justify-content:center; color:#f97316; font-size:.7rem; flex-shrink:0; }
.pos-customer-input { flex:1; border:none; outline:none; font-size:.8rem; font-weight:600; color:#1e293b; background:transparent; font-family:inherit; }
.pos-customer-input::placeholder { color:#cbd5e1; font-weight:500; }

/* Cart Items */
.pos-cart-items { flex:1; overflow-y:auto; padding:.75rem 1.25rem; min-height:200px; }
.pos-cart-items::-webkit-scrollbar { width:3px; }
.pos-cart-items::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:99px; }

.pos-cart-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; min-height:200px; color:#cbd5e1; }
.pos-cart-empty i { font-size:2.5rem; margin-bottom:.75rem; }
.pos-cart-empty p { font-size:.8rem; font-weight:500; color:#94a3b8; }

.pos-cart-item { display:flex; align-items:center; justify-content:space-between; padding:.625rem .75rem; background:#fafafa; border-radius:.5rem; margin-bottom:.5rem; border:1px solid #f1f5f9; transition:all .15s; }
.pos-cart-item:hover { border-color:#e2e8f0; }
.pos-cart-item-info h4 { font-size:.78rem; font-weight:700; color:#1e293b; margin:0 0 2px; }
.pos-cart-item-info span { font-size:.65rem; font-weight:700; color:#f97316; }

.pos-qty-controls { display:flex; align-items:center; gap:.4rem; }
.pos-qty-btn { width:26px; height:26px; border-radius:.375rem; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.55rem; transition:all .15s; }
.pos-qty-btn.minus { background:#f1f5f9; color:#64748b; }
.pos-qty-btn.minus:hover { background:#fee2e2; color:#ef4444; }
.pos-qty-btn.plus { background:#fff7ed; color:#f97316; }
.pos-qty-btn.plus:hover { background:#f97316; color:#fff; }
.pos-qty-num { font-size:.8rem; font-weight:800; color:#1e293b; min-width:20px; text-align:center; }

/* Payment */
.pos-payment-section { padding:.75rem 1.25rem; border-top:1px solid #f1f5f9; }
.pos-payment-label { font-size:.7rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.5rem; display:flex; align-items:center; gap:.35rem; }
.pos-payment-options { display:flex; gap:.5rem; }
.pos-pay-btn { flex:1; padding:.5rem; border-radius:.5rem; border:1.5px solid #e2e8f0; background:#fff; font-size:.75rem; font-weight:600; color:#64748b; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.35rem; transition:all .18s; }
.pos-pay-btn:hover { border-color:#f97316; color:#f97316; }
.pos-pay-btn.active { background:#fff7ed; border-color:#f97316; color:#f97316; }

/* Footer */
.pos-cart-footer { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; background:#fafafa; border-radius:0 0 .875rem .875rem; }
.pos-total-row { display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:.75rem; }
.pos-total-label { font-size:.65rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; }
.pos-total-value { display:flex; align-items:baseline; gap:.25rem; color:#f97316; }
.pos-rp { font-size:.9rem; font-weight:700; }
#grandTotal { font-size:1.5rem; font-weight:900; letter-spacing:-.02em; }
.pos-item-count { font-size:.7rem; font-weight:600; color:#94a3b8; background:#f1f5f9; padding:.2rem .5rem; border-radius:9999px; }

.pos-submit-btn { width:100%; padding:.75rem; background:#f97316; color:#fff; border:none; border-radius:.625rem; font-size:.875rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.4rem; transition:all .2s; box-shadow:0 3px 10px rgba(249,115,22,.3); }
.pos-submit-btn:hover:not(:disabled) { background:#ea580c; transform:translateY(-1px); box-shadow:0 5px 16px rgba(249,115,22,.4); }
.pos-submit-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; box-shadow:none; }

/* ═══ Struk Modal ═══ */
.pos-struk-overlay { position:fixed; inset:0; background:rgba(15,23,42,.5); backdrop-filter:blur(3px); z-index:100; display:none; align-items:center; justify-content:center; padding:1rem; }
.pos-struk-overlay.show { display:flex; }
.pos-struk-box { background:#fff; border-radius:1rem; padding:2rem; max-width:420px; width:100%; box-shadow:0 20px 60px rgba(15,23,42,.2); text-align:center; }
.pos-struk-box h3 { font-size:1.1rem; font-weight:700; color:#1e293b; margin:0 0 .5rem; }
.pos-struk-box .trx-id { font-size:.75rem; color:#94a3b8; margin-bottom:1.25rem; }
.pos-struk-detail { text-align:left; font-size:.8rem; color:#475569; margin-bottom:1rem; }
.pos-struk-detail div { display:flex; justify-content:space-between; padding:.35rem 0; border-bottom:1px dashed #f1f5f9; }
.pos-struk-total { font-size:1rem; font-weight:800; color:#f97316; margin:.75rem 0; }
.pos-struk-actions { display:flex; gap:.75rem; margin-top:1rem; }
.pos-struk-actions button { flex:1; padding:.625rem; border-radius:.5rem; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .15s; }
.pos-struk-print { background:#f97316; color:#fff; border:none; }
.pos-struk-print:hover { background:#ea580c; }
.pos-struk-close { background:#f1f5f9; color:#475569; border:none; }
.pos-struk-close:hover { background:#e2e8f0; }

/* ═══ Responsive ═══ */
@media (max-width:1024px) {
    .pos-layout { grid-template-columns:1fr; }
    .pos-cart { position:static; max-height:none; }
    .pos-product-grid { max-height:400px; }
}
@media (max-width:640px) {
    .pos-catalog-header { flex-direction:column; align-items:stretch; }
    .pos-search-input { width:100%; }
    .pos-product-grid { grid-template-columns:repeat(2, 1fr); }
}
</style>

<script>
let cart = [];
let selectedPayment = 'tunai';
const csrfToken = '{{ csrf_token() }}';
const posStoreUrl = '{{ route("sales.pos.store") }}';
const salesIndexUrl = '{{ route("sales.index") }}';
const storeName = "{{ addslashes(DB::table('settings')->value('store_name') ?? 'TOKO FAA') }}";

// ── Add to Cart ──
function addToCart(id, name, price) {
    const existing = cart.find(i => i.id === id);
    if (existing) { existing.qty += 1; }
    else { cart.push({ id, name, price, qty: 1 }); }
    renderCart();
}

function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
    renderCart();
}

function clearCart() {
    if (!confirm('Kosongkan keranjang?')) return;
    cart = [];
    renderCart();
}

// ── Render Cart ──
function renderCart() {
    const container = document.getElementById('cartItems');
    const totalEl = document.getElementById('grandTotal');
    const countEl = document.getElementById('itemCount');
    const clearBtn = document.getElementById('clearCartBtn');
    const paySection = document.getElementById('paymentSection');
    const submitBtn = document.getElementById('submitBtn');

    if (cart.length === 0) {
        container.innerHTML = `<div class="pos-cart-empty"><i class="fas fa-shopping-basket"></i><p>Klik produk untuk menambah</p></div>`;
        totalEl.textContent = '0';
        countEl.style.display = 'none';
        clearBtn.style.display = 'none';
        paySection.style.display = 'none';
        submitBtn.disabled = true;
        return;
    }

    let total = 0;
    let totalQty = 0;
    container.innerHTML = cart.map(item => {
        const sub = item.price * item.qty;
        total += sub;
        totalQty += item.qty;
        return `<div class="pos-cart-item">
            <div class="pos-cart-item-info">
                <h4>${item.name}</h4>
                <span>Rp ${item.price.toLocaleString('id-ID')} × ${item.qty} = Rp ${sub.toLocaleString('id-ID')}</span>
            </div>
            <div class="pos-qty-controls">
                <button type="button" class="pos-qty-btn minus" onclick="changeQty(${item.id},-1)"><i class="fas fa-minus"></i></button>
                <span class="pos-qty-num">${item.qty}</span>
                <button type="button" class="pos-qty-btn plus" onclick="changeQty(${item.id},1)"><i class="fas fa-plus"></i></button>
            </div>
        </div>`;
    }).join('');

    totalEl.textContent = total.toLocaleString('id-ID');
    countEl.textContent = totalQty + ' item';
    countEl.style.display = 'inline';
    clearBtn.style.display = 'flex';
    paySection.style.display = 'block';
    submitBtn.disabled = false;
}

// ── Payment ──
function selectPayment(btn) {
    document.querySelectorAll('.pos-pay-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedPayment = btn.dataset.method;
}

// ── Submit via POS endpoint ──
function submitOrder() {
    if (cart.length === 0) return;
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    fetch(posStoreUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
            items: cart.map(i => ({ id: i.id, qty: i.qty, price: i.price })),
            customer_name: document.getElementById('customerName').value || 'Umum',
            payment_method: selectedPayment
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showStruk(data);
            cart = [];
            renderCart();
            document.getElementById('customerName').value = '';
        } else {
            alert(data.message || 'Gagal menyimpan transaksi');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> SELESAIKAN ORDER';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan jaringan');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> SELESAIKAN ORDER';
    });
}

// ── Struk ──
function showStruk(data) {
    const overlay = document.getElementById('strukOverlay');
    const box = document.getElementById('strukContent');
    box.innerHTML = `
        <div style="margin-bottom:1rem;"><i class="fas fa-check-circle" style="font-size:2.5rem;color:#22c55e;"></i></div>
        <h3>Transaksi Berhasil!</h3>
        <p class="trx-id">${data.transaction_id}</p>
        <div class="pos-struk-detail">
            <div><span>Pelanggan</span><strong>${data.customer}</strong></div>
            <div><span>Waktu</span><strong>${data.time}</strong></div>
            <div><span>Bayar</span><strong>${(data.payment_method||'tunai').toUpperCase()}</strong></div>
        </div>
        <div class="pos-struk-actions">
            <button class="pos-struk-close" onclick="closeStruk()"><i class="fas fa-arrow-left"></i> Transaksi Baru</button>
            <button class="pos-struk-print" onclick="window.location='${salesIndexUrl}'"><i class="fas fa-list"></i> Ke Riwayat</button>
        </div>`;
    overlay.classList.add('show');
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check-circle"></i> SELESAIKAN ORDER';
}

function closeStruk() {
    document.getElementById('strukOverlay').classList.remove('show');
}

// ── Search ──
document.getElementById('productSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    const activeCat = document.querySelector('.pos-cat-btn.active')?.dataset.cat || 'all';
    filterProducts(q, activeCat);
});

// ── Category Filter ──
document.querySelectorAll('.pos-cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pos-cat-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const q = document.getElementById('productSearch').value.toLowerCase().trim();
        filterProducts(q, this.dataset.cat);
    });
});

function filterProducts(query, cat) {
    const cards = document.querySelectorAll('.pos-product-card');
    let visible = 0;
    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const cardCat = card.dataset.cat;
        const matchSearch = !query || name.includes(query);
        const matchCat = cat === 'all' || cardCat === cat;
        const show = matchSearch && matchCat;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('noResults').style.display = visible === 0 ? 'block' : 'none';
}
</script>

@endsection
