@extends('layouts.frontend')

@section('title', 'Keranjang Belanja')

@section('content')
<style>
    /* ── Cart Premium Styles ── */
    body { background-color: #f8fafc; }
    .cart-wrapper { max-width: 1140px; margin: 0 auto; padding: 8rem 1rem 4rem 1rem; }
    .cart-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 2rem; letter-spacing: -0.02em; }
    
    .cart-card {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
    }
    .cart-card-header { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem; }
    .cart-card-header i { color: #f97316; font-size: 1.25rem; }
    
    .cart-item { display: flex; align-items: center; padding: 1.5rem 0; border-bottom: 1px dashed #e2e8f0; transition: all 0.2s; }
    .cart-item:last-child { border-bottom: none; padding-bottom: 0; }
    
    .cart-img-box { width: 90px; height: 90px; flex-shrink: 0; overflow: hidden; border-radius: 1rem; border: 1px solid #f1f5f9; margin-right: 1.5rem; }
    .cart-img-box img { width: 100%; height: 100%; object-fit: cover; }
    
    .cart-info { flex-grow: 1; }
    .cart-item-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem; }
    .cart-item-price { font-size: 1rem; font-weight: 600; color: #004aad; margin: 0; }
    
    .cart-actions { display: flex; align-items: center; gap: 1.5rem; }
    .cart-qty-wrapper { display: flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 2rem; padding: 0.25rem; }
    .qty-btn { width: 32px; height: 32px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #475569; transition: all 0.2s; }
    .qty-btn:hover { background: #f1f5f9; color: #0f172a; }
    .cart-qty-input { width: 40px; text-align: center; border: none; background: transparent; font-weight: 600; color: #0f172a; font-size: 0.95rem; }
    .cart-qty-input:focus { outline: none; }
    /* Remove default arrows */
    .cart-qty-input::-webkit-outer-spin-button, .cart-qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    
    .btn-remove { color: #ef4444; background: #fef2f2; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; border: none; }
    .btn-remove:hover { background: #fee2e2; color: #dc2626; transform: scale(1.05); }

    .summary-sidebar { position: sticky; top: 100px; }
    .tot-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: #475569; font-weight: 500; }
    .tot-row.grand { margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid #e2e8f0; font-size: 1.25rem; font-weight: 800; color: #0f172a; }
    .grand-price { color: #f97316; }
    
    .btn-checkout { 
        background: #f97316; color: #ffffff; width: 100%; padding: 1rem; border-radius: 1rem; 
        border: none; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.2s; 
        margin-top: 1.5rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem; text-decoration: none;
    }
    .btn-checkout:hover { background: #ea580c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); color: #ffffff; }

    .empty-state { text-align: center; padding: 4rem 2rem; background: #ffffff; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .empty-icon { font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; }
    .btn-shop { background: #004aad; color: #ffffff; padding: 0.75rem 2rem; border-radius: 2rem; font-weight: 600; text-decoration: none; display: inline-block; margin-top: 1.5rem; transition: all 0.2s; }
    .btn-shop:hover { background: #003580; color: #ffffff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 74, 173, 0.2); }
</style>

<div class="cart-wrapper">
    <h2 class="cart-title">Keranjang Belanja</h2>
    
    @if(count($cart) > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cart-card">
                    <div class="cart-card-header">
                        <i class="fas fa-shopping-cart"></i> Daftar Pesanan Anda
                    </div>
                    
                    <div id="cartItems">
                        @foreach($cart as $id => $item)
                            <div class="cart-item" data-id="{{ $id }}">
                                <div class="cart-img-box">
                                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('template-sarab/img/menu/1.jpg') }}" alt="{{ $item['name'] }}">
                                </div>
                                <div class="cart-info">
                                    <h5 class="cart-item-title">{{ $item['name'] }}</h5>
                                    <p class="cart-item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="cart-actions">
                                    <div class="cart-qty-wrapper">
                                        <button type="button" class="qty-btn btn-minus" aria-label="Kurangi">
                                            <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <input type="number" class="cart-qty-input" value="{{ $item['quantity'] }}" min="1">
                                        <button type="button" class="qty-btn btn-plus" aria-label="Tambah">
                                            <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn-remove" title="Hapus Item">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-sidebar">
                    <div class="cart-card">
                        <div class="cart-card-header">
                            <i class="fas fa-receipt"></i> Ringkasan Belanja
                        </div>
                        <div class="tot-row">
                            <span>Total Produk</span>
                            <strong id="summaryCount">{{ collect($cart)->sum('quantity') }} Barang</strong>
                        </div>
                        <div class="tot-row grand">
                            <span>Total Harga</span>
                            <span class="grand-price">Rp <span id="cartTotal">{{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 0, ',', '.') }}</span></span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn-checkout">
                            Lanjut ke Checkout <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-shopping-basket empty-icon"></i>
            <h3 class="fw-bold" style="color: #0f172a;">Keranjang Anda Kosong</h3>
            <p class="text-muted">Yuk, temukan roti dan frozen food favorit Anda sekarang!</p>
            <a href="{{ route('produk.makanan') }}" class="btn-shop">Belanja Sekarang</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = '{{ csrf_token() }}';

        // Helper function to update total
        function updateCartUI(totalStr, countStr) {
            const totalEl = document.getElementById('cartTotal');
            if(totalEl) totalEl.textContent = totalStr;
            const countEl = document.getElementById('summaryCount');
            if(countEl) countEl.textContent = countStr + ' Barang';
            
            // Perbarui badge notifikasi keranjang di navbar
            const navBadge = document.getElementById('cartCount');
            if(navBadge) navBadge.textContent = countStr;
        }

        // ── Hapus Item ──────────────────────────────────────────
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', async function() {
                const itemEl = this.closest('.cart-item');
                const id = itemEl.dataset.id;

                try {
                    const response = await fetch(`/cart/remove/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if(response.ok) {
                        itemEl.style.opacity = '0';
                        setTimeout(() => {
                            itemEl.remove();
                            // Jika habis, reload untuk menampilkan state kosong
                            if(document.querySelectorAll('.cart-item').length === 0) {
                                location.reload();
                            }
                        }, 300);
                        
                        Swal.fire({
                            title: 'Dihapus!',
                            text: data.message,
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        
                        // Opsi: Hitung ulang dari data response jika CartController mengirim total terbaru
                        // Tapi jika controller hanya mengirim message dan cart_count, kita bisa reload jika perlu akurasi
                        location.reload(); 
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });

        // ── Ubah Kuantitas (Minus & Plus & Input) ───────────────
        async function updateQuantity(id, qty) {
            try {
                const response = await fetch('{{ route("cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${id}&quantity=${qty}`
                });
                
                const data = await response.json();
                if(response.ok) {
                    updateCartUI(data.cart_total, data.cart_count);
                }
            } catch (error) {
                console.error('Error updating cart:', error);
            }
        }

        document.querySelectorAll('.cart-qty-wrapper').forEach(wrapper => {
            const btnMinus = wrapper.querySelector('.btn-minus');
            const btnPlus = wrapper.querySelector('.btn-plus');
            const input = wrapper.querySelector('.cart-qty-input');
            const itemEl = wrapper.closest('.cart-item');
            const id = itemEl.dataset.id;

            btnMinus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                if(val > 1) {
                    val--;
                    input.value = val;
                    updateQuantity(id, val);
                }
            });

            btnPlus.addEventListener('click', () => {
                let val = parseInt(input.value) || 1;
                val++;
                input.value = val;
                updateQuantity(id, val);
            });

            input.addEventListener('change', () => {
                let val = parseInt(input.value);
                if(isNaN(val) || val < 1) {
                    val = 1;
                    input.value = val;
                }
                updateQuantity(id, val);
            });
        });
    });
</script>
@endpush