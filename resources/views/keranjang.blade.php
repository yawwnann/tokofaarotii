<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - FAA Frozen Food & Bakery</title>
    <link href="{{ asset('template-sarab/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('template-sarab/css/style.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 100px; }
        .cart-container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .cart-item { display: flex; align-items: center; padding: 20px 0; border-bottom: 1px solid #eee; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; margin-right: 20px; }
        .cart-info { flex-grow: 1; }
        .cart-info h5 { margin: 0; font-weight: 700; }
        .cart-price { color: #004aad; font-weight: 700; }
        .cart-qty { width: 80px; }
        .cart-remove { color: #f97316; cursor: pointer; font-size: 1.2rem; }
    </style>
</head>
<body>
    @include('layouts.partials.navbar')

    <div class="container">
        <div class="cart-container">
            <h2 class="fw-bold mb-4"><i class="bi bi-cart3"></i> Keranjang Belanja</h2>
            
            @if(count($cart) > 0)
                <div id="cartItems">
                    @foreach($cart as $id => $item)
                        <div class="cart-item" data-id="{{ $id }}">
                            <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('template-sarab/img/menu/1.jpg') }}" alt="{{ $item['name'] }}">
                            <div class="cart-info">
                                <h5>{{ $item['name'] }}</h5>
                                <p class="cart-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="me-4">
                                <input type="number" class="form-control cart-qty-input" value="{{ $item['quantity'] }}" min="1">
                            </div>
                            <div class="cart-remove">
                                <i class="bi bi-trash"></i>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-end">
                    <h4 class="fw-bold">Total: Rp <span id="cartTotal">{{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 0, ',', '.') }}</span></h4>
                    <a href="{{ route('checkout.index') }}" class="btn btn-warning btn-lg mt-3 px-5 text-white fw-bold" style="border-radius: 50px; background-color: #f97316; border: none;">Checkout Sekarang</a>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Keranjang Anda masih kosong.</p>
                    <a href="{{ route('produk.makanan') }}" class="btn btn-primary px-4 py-2 mt-2" style="border-radius: 50px; background-color: #004aad; border: none;">Lihat Produk</a>
                </div>
            @endif
        </div>
    </div>

    @include('layouts.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Remove item
            $('.cart-remove').on('click', function() {
                const item = $(this).closest('.cart-item');
                const id = item.data('id');

                $.ajax({
                    url: `/cart/remove/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        item.fadeOut(300, function() { 
                            $(this).remove();
                            if ($('.cart-item').length === 0) location.reload();
                        });
                        $('#cartCount').text(response.cart_count);
                        Swal.fire('Berhasil', response.message, 'success');
                    }
                });
            });

            // Update Qty
            $('.cart-qty-input').on('change', function() {
                const id = $(this).closest('.cart-item').data('id');
                const qty = $(this).val();

                $.ajax({
                    url: '{{ route("cart.update") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', id: id, quantity: qty },
                    success: function(response) {
                        $('#cartTotal').text(response.cart_total);
                    }
                });
            });
        });
    </script>
</body>
</html>