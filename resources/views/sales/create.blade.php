@extends('layouts.app')

@section('title', 'Kasir - Toko FAA')
@section('subtitle', 'Pilih produk dan selesaikan transaksi dengan cepat')

@section('content')
<div class="pos-header">
    <div class="pos-header-left">
        <h2 class="pos-header-title">Kasir Offline</h2>
        <span class="pos-header-store">
            <i class="fas fa-store"></i>
            {{ auth()->user()->store->name ?? ($store->store_name ?? 'Toko FAA') }}
            @if(auth()->user()->store)
                — {{ auth()->user()->store->address ?? '' }}
            @endif
        </span>
    </div>
</div>

<div class="pos-container">
    <div class="pos-layout">
        
        {{-- ── KATALOG PRODUK ── --}}
        <div class="catalog-section">
            <div class="catalog-header">
                <h2 class="section-title">KATALOG PRODUK</h2>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Cari produk...">
                </div>
            </div>

            <div id="productGrid" class="product-grid custom-scrollbar">
                @foreach($products as $product)
                <div onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})" 
                     class="product-card">
                    
                    <div class="product-img-wrap">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-placeholder">
                                <i class="fas fa-bread-slice"></i>
                            </div>
                        @endif
                        <div class="product-overlay">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="p-name product-name-text">{{ $product->name }}</h3>
                        <p class="p-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── KERANJANG ── --}}
        <div class="cart-section">
            <div class="cart-card">
                <div class="cart-header">
                    <div class="customer-input-wrap">
                        <div class="user-icon"><i class="fas fa-user"></i></div>
                        <input type="text" id="customer_name" placeholder="Nama Pelanggan (Umum)">
                    </div>
                </div>

                <div class="payment-method-selector">
                    <p class="selector-label">METODE PEMBAYARAN</p>
                    <div class="method-options">
                        <label class="method-option">
                            <input type="radio" name="payment_method" value="tunai" checked>
                            <div class="option-content">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Tunai</span>
                            </div>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="payment_method" value="transfer">
                            <div class="option-content">
                                <i class="fas fa-credit-card"></i>
                                <span>Transfer</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="cartItems" class="cart-items custom-scrollbar">
                    <div class="cart-empty">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Keranjang Kosong</p>
                    </div>
                </div>

                <div class="cart-summary">
                    <div class="total-wrap">
                        <p class="total-label">Total Pembayaran</p>
                        <div class="total-value">
                            <span class="currency">Rp</span>
                            <span id="grandTotalDisplay">0</span>
                        </div>
                    </div>

                    <button type="button" onclick="selesaikanOrder()" class="btn-checkout">
                        <i class="fas fa-check-circle"></i> SELESAIKAN ORDER
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* ── Header ── */
    .pos-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .pos-header-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }
    .pos-header-store { font-size: .8rem; font-weight: 600; color: #f97316; display: flex; align-items: center; gap: .4rem; background: #fff7ed; padding: .3rem .75rem; border-radius: .5rem; }

    /* ── Layout ── */
    .pos-container { max-width: 1400px; margin: 0 auto; }
    .pos-layout { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; height: calc(100vh - 180px); min-height: 600px; }

    /* ── Catalog ── */
    .catalog-section { display: flex; flex-direction: column; gap: 1.25rem; }
    .catalog-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
    .section-title { font-size: 1.125rem; font-weight: 800; color: #1e293b; margin: 0; }
    
    .search-box { position: relative; width: 280px; }
    .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .85rem; }
    .search-box input { width: 100%; padding: .625rem 1rem .625rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: .75rem; font-size: .85rem; outline: none; transition: all .2s; }
    .search-box input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(99, 102, 241, .1); }

    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; overflow-y: auto; padding-right: .5rem; }
    .product-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; padding: .75rem; cursor: pointer; transition: all .2s; position: relative; overflow: hidden; display: flex; flex-direction: column; gap: .75rem; box-shadow: 0 1px 3px rgba(15,23,42,.05); }
    .product-card:hover { transform: translateY(-3px); border-color: #f97316; box-shadow: 0 8px 15px rgba(99, 102, 241, .1); }
    
    .product-img-wrap { width: 100%; aspect-ratio: 1/1; border-radius: .875rem; background: #f8fafc; overflow: hidden; position: relative; }
    .product-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
    .product-card:hover .product-img-wrap img { transform: scale(1.1); }
    .product-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 2rem; }
    
    .product-overlay { position: absolute; inset: 0; background: rgba(99, 102, 241, .4); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; opacity: 0; transition: opacity .2s; }
    .product-card:hover .product-overlay { opacity: 1; }

    .product-info { display: flex; flex-direction: column; gap: 2px; }
    .p-name { font-size: .8rem; font-weight: 700; color: #475569; margin: 0; line-height: 1.3; height: 2.1rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    .p-price { font-size: .95rem; font-weight: 800; color: #f97316; margin: 0; }

    /* ── Cart ── */
    .cart-section { height: 100%; }
    .cart-card { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 10px 25px rgba(15,23,42,.08); display: flex; flex-direction: column; height: 100%; overflow: hidden; }
    
    .cart-header { padding: 1.25rem; border-bottom: 1px solid #f8fafc; }
    .customer-input-wrap { display: flex; align-items: center; gap: .75rem; background: #f8fafc; padding: .5rem 1rem; border-radius: 1rem; }
    .user-icon { width: 32px; height: 32px; background: #fff; border-radius: .5rem; display: flex; align-items: center; justify-content: center; color: #f97316; font-size: .75rem; }
    .customer-input-wrap input { flex: 1; border: none; background: transparent; font-size: .85rem; font-weight: 700; outline: none; color: #475569; }

    /* ── Payment Selector ── */
    .payment-method-selector { padding: 0 1.25rem 1.25rem; border-bottom: 1px solid #f8fafc; }
    .selector-label { font-size: .65rem; font-weight: 800; color: #94a3b8; margin-bottom: .75rem; letter-spacing: .05em; }
    .method-options { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
    .method-option { cursor: pointer; position: relative; }
    .method-option input { position: absolute; opacity: 0; }
    .option-content { background: #f8fafc; border: 1.5px solid #f1f5f9; padding: .6rem; border-radius: .875rem; display: flex; align-items: center; justify-content: center; gap: .5rem; transition: all .2s; }
    .method-option input:checked + .option-content { background: #fff7ed; border-color: #f97316; color: #f97316; }
    .option-content i { font-size: .85rem; }
    .option-content span { font-size: .75rem; font-weight: 700; }

    .cart-items { flex: 1; overflow-y: auto; padding: 1.25rem; display: flex; flex-direction: column; gap: .75rem; }
    .cart-item { display: flex; align-items: center; justify-content: space-between; background: #fff; padding: .875rem; border-radius: 1.125rem; border: 1.5px solid #f8fafc; transition: all .2s; }
    .cart-item:hover { border-color: #f97316; }
    .item-info h4 { font-size: .8rem; font-weight: 700; color: #1e293b; margin: 0 0 2px; }
    .item-info p { font-size: .7rem; font-weight: 800; color: #f97316; margin: 0; }
    
    .qty-controls { display: flex; align-items: center; gap: .75rem; }
    .btn-qty { width: 28px; height: 28px; border-radius: .5rem; border: none; display: flex; align-items: center; justify-content: center; font-size: .65rem; cursor: pointer; transition: all .2s; }
    .btn-minus { background: #f1f5f9; color: #64748b; }
    .btn-minus:hover { background: #fee2e2; color: #ef4444; }
    .btn-plus { background: #fff7ed; color: #f97316; }
    .btn-plus:hover { background: #f97316; color: #fff; }
    .qty-num { font-size: .85rem; font-weight: 800; color: #1e293b; min-width: 1rem; text-align: center; }

    .cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #cbd5e1; gap: 1rem; }
    .cart-empty i { font-size: 3rem; }
    .cart-empty p { font-size: .9rem; font-weight: 700; }

    .cart-summary { padding: 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; }
    .total-wrap { margin-bottom: 1.25rem; }
    .total-label { font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
    .total-value { display: flex; align-items: baseline; gap: 4px; color: #f97316; }
    .currency { font-size: 1.125rem; font-weight: 700; }
    #grandTotalDisplay { font-size: 2.5rem; font-weight: 900; letter-spacing: -1px; }

    .btn-checkout { width: 100%; padding: 1rem; background: #f97316; color: #fff; border: none; border-radius: 1.125rem; font-size: 1rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .75rem; transition: all .2s; }
    .btn-checkout:hover { background: #ea580c; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99, 102, 241, .2); }

    /* ── Utilities ── */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    @media (max-width: 1024px) {
        .pos-layout { grid-template-columns: 1fr; height: auto; }
        .cart-section { height: 600px; }
    }

    /* ── Print Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(2px); }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:1.5rem; padding:2rem; max-width:400px; width:90%; text-align:center; box-shadow:0 25px 50px rgba(0,0,0,.15); animation:modalIn .25s ease; }
    @keyframes modalIn { from { transform:scale(.9); opacity:0; } to { transform:scale(1); opacity:1; } }
    .modal-icon { width:64px; height:64px; border-radius:50%; background:#f0fdf4; color:#22c55e; display:flex; align-items:center; justify-content:center; font-size:1.75rem; margin:0 auto 1rem; }
    .modal-title { font-size:1.1rem; font-weight:800; color:#1e293b; margin:0 0 .25rem; }
    .modal-sub { font-size:.85rem; color:#64748b; margin:0 0 1.5rem; }
    .modal-actions { display:flex; gap:.75rem; }
    .modal-actions button { flex:1; padding:.75rem 1rem; border-radius:.75rem; font-weight:700; font-size:.85rem; cursor:pointer; transition:all .2s; border:none; }
    .btn-modal-print { background:#f97316; color:#fff; }
    .btn-modal-print:hover { background:#ea580c; }
    .btn-modal-no { background:#f1f5f9; color:#475569; }
    .btn-modal-no:hover { background:#e2e8f0; }
</style>

<div id="printModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon"><i class="fas fa-check-circle"></i></div>
        <h3 class="modal-title">Transaksi Berhasil!</h3>
        <p class="modal-sub">Apakah kamu ingin mencetak struk?</p>
        <div class="modal-actions">
            <button class="btn-modal-no" onclick="closePrintModal(false)">Tidak</button>
            <button class="btn-modal-print" onclick="closePrintModal(true)"><i class="fas fa-print"></i> Cetak Struk</button>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let _printData = null;
    let _printCart = null;

    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }
        renderCart();
    }

    function changeQty(id, delta) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const display = document.getElementById('grandTotalDisplay');
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-basket"></i>
                    <p>Keranjang Kosong</p>
                </div>`;
            display.innerText = "0";
            return;
        }

        let total = 0;
        container.innerHTML = cart.map(item => {
            total += (item.price * item.qty);
            return `
                <div class="cart-item">
                    <div class="item-info">
                        <h4>${item.name}</h4>
                        <p>Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="qty-controls">
                        <button onclick="changeQty(${item.id}, -1)" class="btn-qty btn-minus">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="qty-num">${item.qty}</span>
                        <button onclick="changeQty(${item.id}, 1)" class="btn-qty btn-plus">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>`;
        }).join('');
        display.innerText = total.toLocaleString('id-ID');
    }

    function showPrintModal(data, cart) {
        _printData = data;
        _printCart = cart;
        document.getElementById('printModal').classList.add('active');
    }

    function closePrintModal(doPrint) {
        document.getElementById('printModal').classList.remove('active');
        if (doPrint && _printData) {
            printStruk(_printData, _printCart);
        }
        window.location.href = "{{ route('sales.index') }}";
    }

    function filterProducts() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.querySelector('.product-name-text').innerText.toLowerCase();
            card.style.display = name.includes(query) ? "flex" : "none";
        });
    }

    const storeName = "{{ addslashes(auth()->user()->store->name ?? ($store->store_name ?? 'TOKO FAA')) }}";
    const storeAddress = "{{ addslashes(auth()->user()->store->address ?? ($store->store_address ?? '')) }}";
    const storePhone = "{{ addslashes(auth()->user()->store->phone ?? ($store->store_whatsapp ?? '')) }}";

    function printStruk(data, items) {
        let strukWindow = window.open('', '', 'width=400,height=600');
        let itemHtml = items.map(item => `
            <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 5px;">
                <span>${item.qty}x ${item.name}</span>
                <span>Rp ${(item.price * item.qty).toLocaleString('id-ID')}</span>
            </div>
        `).join('');

        let total = items.reduce((sum, item) => sum + (item.price * item.qty), 0);

        strukWindow.document.write(`
            <html>
            <body style="font-family: 'Courier New', Courier, monospace; width: 300px; padding: 10px; color: #333;">
                <div style="text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 18px;">${storeName}</h2>
                    ${storeAddress ? `<p style="margin: 2px 0; font-size: 10px;">${storeAddress}</p>` : ''}
                    ${storePhone ? `<p style="margin: 2px 0; font-size: 10px;">${storePhone}</p>` : ''}
                </div>
                <div style="font-size: 10px; margin-bottom: 10px; line-height: 1.4;">
                    <div style="display: flex; justify-content: space-between;"><span>No:</span> <span>${data.transaction_id}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Tgl:</span> <span>${data.time}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Plg:</span> <span>${data.customer}</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Byr:</span> <span>${data.payment_method.toUpperCase()}</span></div>
                </div>
                <div style="border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px;">
                    ${itemHtml}
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; margin-bottom: 10px;">
                    <span>TOTAL</span>
                    <span>Rp ${total.toLocaleString('id-ID')}</span>
                </div>
                <div style="text-align: center; font-size: 10px; margin-top: 20px;">
                    *** TERIMA KASIH ***<br>Selamat Menikmati!
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

    function selesaikanOrder() {
        if (cart.length === 0) return alert('Pilih produk dulu!');

        const customerName = document.getElementById('customer_name').value || 'Umum';
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

        fetch("{{ route('sales.pos.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                items: cart,
                customer_name: customerName,
                payment_method: paymentMethod
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.is_transfer && data.snap_token) {
                    // Transfer → buka Midtrans Snap
                    if (typeof snap !== 'undefined') {
                        snap.pay(data.snap_token, {
                            onSuccess: function(result){
                                alert('Pembayaran Berhasil!');
                                window.location.href = "{{ route('sales.index') }}";
                            },
                            onPending: function(result){
                                alert('Menunggu pembayaran pelanggan...');
                                window.location.href = "{{ route('sales.index') }}";
                            },
                            onError: function(result){
                                alert('Pembayaran gagal! Silakan coba lagi.');
                                window.location.href = "{{ route('sales.index') }}";
                            }
                        });
                    } else {
                        alert('Midtrans tidak dapat dimuat. Periksa koneksi internet.');
                        window.location.href = "{{ route('sales.index') }}";
                    }
                } else {
                    // Tunai → langsung cetak struk
                    showPrintModal(data, cart);
                }
            } else {
                alert('Gagal menyimpan transaksi: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi ke server.');
        });
    }
</script>
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function payWithMidtrans(snapToken) {
    if (typeof snap === 'undefined') {
        alert('Midtrans tidak dapat dimuat. Periksa koneksi internet.');
        return;
    }
    snap.pay(snapToken, {
        onSuccess: function(result){
            alert('Pembayaran Berhasil!');
            window.location.href = "{{ route('sales.index') }}";
        },
        onPending: function(result){
            alert('Menunggu pembayaran pelanggan...');
        },
        onError: function(result){
            alert('Pembayaran gagal! Silakan coba lagi.');
        }
    });
}
</script>
@endsection
