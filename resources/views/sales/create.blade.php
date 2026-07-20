@extends('layouts.app')

@section('title', 'Kasir (Point of Sales)')

@section('content')

@if(isset($errors) && $errors->any())
<div class="pos-alert-danger">
    <i class="fas fa-exclamation-triangle"></i>
    <ul>
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<div class="pos-wrapper">
    <!-- ══════ KATALOG PRODUK (KIRI) ══════ -->
    <div class="pos-panel pos-catalog">
        <div class="catalog-header">
            <div class="header-title">
                <i class="fas fa-store text-orange"></i>
                <div>
                    <h2 class="mb-0">Katalog Produk</h2>
                    <p class="text-muted mb-0" style="font-size:12px;">Pilih produk untuk ditambahkan ke keranjang</p>
                </div>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="productSearch" placeholder="Cari nama produk...">
            </div>
        </div>

        <div class="category-tabs">
            <button class="cat-tab active" data-cat="all">Semua Produk</button>
            @php $categories = $products->pluck('category')->unique('id')->filter(); @endphp
            @foreach($categories as $cat)
                <button class="cat-tab" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        <div class="product-grid" id="productGrid">
            @foreach($products as $product)
            <div class="product-card"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-cat="{{ $product->category_id }}"
                 onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                
                <div class="product-img-box">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="product-placeholder" style="display:none;"><i class="fas fa-image"></i></div>
                    @else
                        <div class="product-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                    
                    @if($product->category)
                        <span class="product-badge">{{ $product->category->name }}</span>
                    @endif
                </div>

                <div class="product-info-box">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    <p class="product-description">{{ Str::limit($product->description ?? 'Produk pilihan terbaik dari toko kami.', 50) }}</p>
                    <div class="product-action">
                        <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <div class="btn-add"><i class="fas fa-plus"></i></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div id="noResults" style="display:none; text-align:center; padding: 40px; color:#94a3b8;">
            <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:10px; opacity:0.5;"></i>
            <p>Tidak ada produk yang cocok.</p>
        </div>
    </div>

    <!-- ══════ KERANJANG (KANAN) ══════ -->
    <div class="pos-panel pos-cart">
        <div class="cart-header">
            <h3 class="mb-0"><i class="fas fa-shopping-cart text-orange"></i> Keranjang</h3>
            <button id="clearCartBtn" onclick="clearCart()" style="display:none;"><i class="fas fa-trash"></i> Kosongkan</button>
        </div>

        <div class="customer-input-box">
            <i class="fas fa-user text-orange"></i>
            <input type="text" id="customerName" placeholder="Nama Pelanggan (opsional)">
        </div>

        <div class="cart-items" id="cartItems">
            <div class="cart-empty-state">
                <i class="fas fa-cart-arrow-down"></i>
                <p>Keranjang masih kosong</p>
            </div>
        </div>

        <div class="payment-box" id="paymentSection" style="display:none;">
            <p class="payment-label">METODE PEMBAYARAN</p>
            <div class="payment-options">
                <button class="pay-btn active" data-method="tunai" onclick="selectPayment(this)"><i class="fas fa-money-bill"></i> Tunai</button>
                <button class="pay-btn" data-method="transfer" onclick="selectPayment(this)"><i class="fas fa-university"></i> Transfer</button>
            </div>
        </div>

        <div class="cart-footer">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <span style="font-size:12px; font-weight:700; color:#64748b;">TOTAL PEMBAYARAN</span>
                <span class="total-amount">Rp <span id="grandTotal">0</span></span>
            </div>
            <button id="submitBtn" class="btn-submit" onclick="submitOrder()" disabled>
                <i class="fas fa-check-circle"></i> BAYAR SEKARANG
            </button>
        </div>
    </div>
</div>

<!-- ══════ MODAL STRUK ══════ -->
<div class="struk-overlay" id="strukOverlay">
    <div class="struk-box" id="strukContent"></div>
</div>

<style>
/* CSS Reset & Variables */
:root {
    --orange: #f97316;
    --orange-dark: #ea580c;
    --orange-light: #fff7ed;
    --slate: #64748b;
    --slate-dark: #1e293b;
    --bg: #f8fafc;
    --border: #e2e8f0;
}
.text-orange { color: var(--orange); }

/* Layout */
.pos-wrapper { display: flex; gap: 20px; height: calc(100vh - 110px); min-height: 600px; font-family: 'Inter', sans-serif; }
.pos-panel { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden; }
.pos-catalog { flex: 1; }
.pos-cart { width: 380px; flex-shrink: 0; }

/* Alert */
.pos-alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; }
.pos-alert-danger ul { margin: 0; padding-left: 20px; }

/* Catalog Header */
.catalog-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.header-title { display: flex; align-items: center; gap: 15px; }
.header-title i { font-size: 24px; }
.header-title h2 { font-size: 18px; font-weight: 700; color: var(--slate-dark); }
.search-box { position: relative; width: 250px; }
.search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--slate); font-size: 14px; }
.search-box input { width: 100%; padding: 10px 15px 10px 40px; border: 1px solid var(--border); border-radius: 50px; font-size: 13px; outline: none; transition: border 0.3s; }
.search-box input:focus { border-color: var(--orange); }

/* Category Tabs */
.category-tabs { display: flex; gap: 10px; padding: 15px 20px; background: #fafafa; border-bottom: 1px solid var(--border); overflow-x: auto; }
.cat-tab { background: #fff; border: 1px solid var(--border); padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 600; color: var(--slate); cursor: pointer; transition: all 0.2s; white-space: nowrap; }
.cat-tab:hover { border-color: var(--orange); color: var(--orange); }
.cat-tab.active { background: var(--orange); border-color: var(--orange); color: #fff; }

/* Product Grid */
.product-grid { padding: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; overflow-y: auto; flex: 1; align-content: start; }
.product-grid::-webkit-scrollbar { width: 6px; }
.product-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

/* Product Card - BULLETPROOF BLOCK LAYOUT */
.product-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; cursor: pointer; transition: all 0.2s; height: 100%; }
.product-card:hover { border-color: var(--orange); box-shadow: 0 10px 20px rgba(249,115,22,0.1); transform: translateY(-4px); }

.product-img-box { width: 100%; height: 160px; position: relative; background: var(--bg); border-bottom: 1px solid var(--bg); }
.product-image { width: 100%; height: 100%; object-fit: cover; display: block; }
.product-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #fff7ed, #ffedd5); color: var(--orange); font-size: 3rem; opacity: 0.6; }
.product-badge { position: absolute; top: 12px; left: 12px; background: rgba(255,255,255,0.95); color: var(--orange-dark); font-weight: 800; font-size: 10px; padding: 4px 10px; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); letter-spacing: 0.5px; text-transform: uppercase; }

.product-info-box { padding: 15px; display: flex; flex-direction: column; flex-grow: 1; }
.product-title { font-size: 14px; font-weight: 700; color: var(--slate-dark); margin: 0 0 6px 0; line-height: 1.4; }
.product-description { font-size: 11px; color: var(--slate); margin: 0 0 15px 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1; }
.product-action { display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed var(--border); padding-top: 12px; margin-top: auto; }
.product-price { font-size: 16px; font-weight: 800; color: var(--orange); }
.btn-add { width: 32px; height: 32px; border-radius: 50%; background: var(--orange); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: 0.2s; }
.product-card:hover .btn-add { background: var(--orange-dark); transform: scale(1.1); }

/* Cart Header */
.cart-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.cart-header h3 { font-size: 16px; font-weight: 700; color: var(--slate-dark); }
.cart-header button { background: #fef2f2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; transition: 0.2s; }
.cart-header button:hover { background: #fee2e2; }

/* Customer Input */
.customer-input-box { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid var(--border); background: #fafafa; }
.customer-input-box i { background: var(--orange-light); width: 32px; height: 32px; display: flex; justify-content: center; align-items: center; border-radius: 50%; font-size: 12px; }
.customer-input-box input { border: none; background: transparent; font-size: 13px; font-weight: 600; outline: none; width: 100%; color: var(--slate-dark); }

/* Cart Items */
.cart-items { flex: 1; overflow-y: auto; padding: 15px; }
.cart-items::-webkit-scrollbar { width: 4px; }
.cart-items::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.cart-empty-state { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; }
.cart-empty-state i { font-size: 3rem; margin-bottom: 15px; opacity: 0.5; }
.cart-empty-state p { font-size: 13px; font-weight: 500; }

.cart-item-row { display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #fafafa; border: 1px solid var(--border); border-radius: 10px; margin-bottom: 10px; }
.cart-item-info h4 { font-size: 13px; font-weight: 700; color: var(--slate-dark); margin: 0 0 4px 0; }
.cart-item-info span { font-size: 11px; font-weight: 700; color: var(--orange); }
.qty-box { display: flex; align-items: center; gap: 8px; }
.qty-btn { width: 24px; height: 24px; border: none; border-radius: 6px; display: flex; justify-content: center; align-items: center; font-size: 10px; cursor: pointer; transition: 0.2s; }
.qty-btn.minus { background: #f1f5f9; color: var(--slate); }
.qty-btn.minus:hover { background: #fee2e2; color: #dc2626; }
.qty-btn.plus { background: var(--orange-light); color: var(--orange); }
.qty-btn.plus:hover { background: var(--orange); color: #fff; }
.qty-num { font-size: 13px; font-weight: 700; min-width: 20px; text-align: center; }

/* Payment Options */
.payment-box { padding: 15px 20px; border-top: 1px solid var(--border); }
.payment-label { font-size: 10px; font-weight: 800; color: var(--slate); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
.payment-options { display: flex; gap: 10px; }
.pay-btn { flex: 1; padding: 10px; background: #fff; border: 1px solid var(--border); border-radius: 8px; font-size: 12px; font-weight: 700; color: var(--slate); cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
.pay-btn:hover { border-color: var(--orange); }
.pay-btn.active { background: var(--orange-light); border-color: var(--orange); color: var(--orange); }

/* Cart Footer */
.cart-footer { padding: 20px; background: #fafafa; border-top: 1px solid var(--border); }
.total-amount { font-size: 24px; font-weight: 900; color: var(--orange); line-height: 1; }
.btn-submit { width: 100%; padding: 14px; background: var(--orange); color: #fff; border: none; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 12px rgba(249,115,22,0.2); }
.btn-submit:hover:not(:disabled) { background: var(--orange-dark); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(249,115,22,0.3); }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }

/* Struk Modal */
.struk-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1050; padding: 20px; }
.struk-overlay.show { display: flex; }
.struk-box { background: #fff; padding: 30px; border-radius: 20px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
.struk-box h3 { font-weight: 800; color: var(--slate-dark); margin: 15px 0 5px; }
.struk-detail { margin: 20px 0; text-align: left; }
.struk-detail div { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border); font-size: 13px; color: var(--slate); }
.struk-detail div strong { color: var(--slate-dark); font-weight: 700; }
.struk-actions { display: flex; gap: 10px; margin-top: 25px; }
.struk-actions button { flex: 1; padding: 12px; border-radius: 8px; border: none; font-size: 13px; font-weight: 700; cursor: pointer; transition: 0.2s; }
.btn-new-trx { background: #f1f5f9; color: var(--slate-dark); }
.btn-new-trx:hover { background: #e2e8f0; }
.btn-history { background: var(--orange); color: #fff; }
.btn-history:hover { background: var(--orange-dark); }

@media (max-width: 992px) {
    .pos-wrapper { flex-direction: column; height: auto; }
    .pos-cart { width: 100%; height: 600px; }
}
@media (max-width: 576px) {
    .product-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 15px; }
    .product-img-box { height: 120px; }
    .product-title { font-size: 12px; }
    .product-price { font-size: 13px; }
}
</style>

<script>
let cart = [];
let selectedPayment = 'tunai';
const csrfToken = '{{ csrf_token() }}';
const posStoreUrl = '{{ route("sales.pos.store") }}';
const salesIndexUrl = '{{ route("sales.index") }}';

// ── Search & Filter ──
document.getElementById('productSearch').addEventListener('input', function() {
    filterProducts(this.value.toLowerCase(), document.querySelector('.cat-tab.active').dataset.cat);
});
document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        filterProducts(document.getElementById('productSearch').value.toLowerCase(), this.dataset.cat);
    });
});
function filterProducts(query, cat) {
    let visible = 0;
    document.querySelectorAll('.product-card').forEach(card => {
        const matchQ = card.dataset.name.toLowerCase().includes(query);
        const matchC = cat === 'all' || card.dataset.cat === cat;
        if(matchQ && matchC) { card.style.display = 'flex'; visible++; } 
        else { card.style.display = 'none'; }
    });
    document.getElementById('noResults').style.display = visible ? 'none' : 'block';
}

// ── Cart Logic ──
function addToCart(id, name, price) {
    let item = cart.find(i => i.id === id);
    if(item) item.qty++;
    else cart.push({id, name, price, qty: 1});
    renderCart();
}
function changeQty(id, delta) {
    let item = cart.find(i => i.id === id);
    if(!item) return;
    item.qty += delta;
    if(item.qty <= 0) cart = cart.filter(i => i.id !== id);
    renderCart();
}
function clearCart() {
    if(confirm('Yakin ingin mengosongkan keranjang?')) { cart = []; renderCart(); }
}
function selectPayment(btn) {
    document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedPayment = btn.dataset.method;
}

function renderCart() {
    const box = document.getElementById('cartItems');
    const totalEl = document.getElementById('grandTotal');
    const submitBtn = document.getElementById('submitBtn');
    
    if(cart.length === 0) {
        box.innerHTML = `<div class="cart-empty-state"><i class="fas fa-cart-arrow-down"></i><p>Keranjang kosong</p></div>`;
        totalEl.innerText = '0';
        document.getElementById('clearCartBtn').style.display = 'none';
        document.getElementById('paymentSection').style.display = 'none';
        submitBtn.disabled = true;
        return;
    }

    let total = 0;
    box.innerHTML = cart.map(item => {
        let sub = item.price * item.qty;
        total += sub;
        return `
        <div class="cart-item-row">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <span>Rp ${item.price.toLocaleString('id-ID')} × ${item.qty}</span>
            </div>
            <div class="qty-box">
                <button class="qty-btn minus" onclick="changeQty(${item.id}, -1)"><i class="fas fa-minus"></i></button>
                <div class="qty-num">${item.qty}</div>
                <button class="qty-btn plus" onclick="changeQty(${item.id}, 1)"><i class="fas fa-plus"></i></button>
            </div>
        </div>`;
    }).join('');

    totalEl.innerText = total.toLocaleString('id-ID');
    document.getElementById('clearCartBtn').style.display = 'block';
    document.getElementById('paymentSection').style.display = 'block';
    submitBtn.disabled = false;
}

// ── Submit Checkout ──
function submitOrder() {
    if(cart.length === 0) return;
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> MEMPROSES...';

    fetch(posStoreUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
            items: cart,
            customer_name: document.getElementById('customerName').value || 'Umum',
            payment_method: selectedPayment
        })
    }).then(r => r.json()).then(res => {
        if(res.success) {
            showStruk(res);
            cart = [];
            renderCart();
            document.getElementById('customerName').value = '';
        } else {
            alert(res.message || 'Gagal diproses');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> BAYAR SEKARANG';
        }
    }).catch(e => {
        alert('Terjadi kesalahan jaringan');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> BAYAR SEKARANG';
    });
}

function showStruk(data) {
    document.getElementById('strukContent').innerHTML = `
        <i class="fas fa-check-circle" style="font-size: 4rem; color: #22c55e;"></i>
        <h3>Pembayaran Berhasil!</h3>
        <p style="font-size:11px; color:var(--slate); margin-bottom:20px;">ID: ${data.transaction_id}</p>
        
        <div class="struk-detail">
            <div><span>Pelanggan</span> <strong>${data.customer}</strong></div>
            <div><span>Metode</span> <strong style="text-transform:uppercase">${data.payment_method || 'Tunai'}</strong></div>
            <div><span>Waktu</span> <strong>${data.time}</strong></div>
        </div>
        
        <div class="struk-actions">
            <button class="btn-new-trx" onclick="document.getElementById('strukOverlay').classList.remove('show')">Order Baru</button>
            <button class="btn-history" onclick="window.location='${salesIndexUrl}'">Lihat Riwayat</button>
        </div>
    `;
    document.getElementById('strukOverlay').classList.add('show');
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check-circle"></i> BAYAR SEKARANG';
}
</script>

@endsection
