@extends('layouts.public')

@section('title', 'Produk Makanan - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root {
        --primary: #f97316;
        --primary-dark: #ea580c;
        --primary-light: #ffedd5;
        --dark: #0f172a;
        --slate: #475569;
        --light: #f8fafc;
        --border: #e2e8f0;
        --white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.04);
        --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
        --radius-lg: 16px;
        --radius-md: 12px;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--light);
        color: var(--slate);
    }

    .product-hero {
        position: relative;
        height: 380px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 0 0 32px 32px;
    }

    .hero-bg {
        position: absolute;
        inset: 0;
        background: url('{{ asset("template-sarab/img/banner-toko-faa.png") }}') no-repeat center center / cover;
        will-change: transform;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%);
    }

    .hero-content {
        position: relative;
        z-index: 10;
        text-align: center;
        padding: 0 24px;
    }

    .product-hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(36px, 6vw, 54px);
        font-weight: 700;
        color: var(--white);
        margin-bottom: 12px;
    }

    .product-hero p {
        font-size: 14px;
        max-width: 600px;
        margin: 0 auto 20px auto;
        color: rgba(255, 255, 255, 0.9) !important;
        line-height: 1.6;
    }

    .hero-breadcrumb {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 12px;
    }

    .hero-breadcrumb a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.2s; }
    .hero-breadcrumb a:hover { color: var(--primary); }
    .hero-breadcrumb .crumb-active { color: var(--primary); font-weight: 500; }
    .hero-breadcrumb .sep { color: rgba(255, 255, 255, 0.3); }

    .category-section { padding: 60px 0; }
    .category-section:nth-child(even) {
        background-color: var(--white);
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }
    .section-header { margin-bottom: 40px; text-align: center; }
    .section-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: clamp(26px, 4vw, 36px);
        color: var(--dark);
        margin-bottom: 8px;
    }

    .product-card-wrapper { height: 100%; }
    .product-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        height: 100%;
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }
    .product-img-wrapper {
        position: relative;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: var(--light);
    }
    .product-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover .product-img { transform: scale(1.04); }
    .product-badge {
        position: absolute; top: 12px; left: 12px;
        background: var(--primary-light); color: var(--primary-dark);
        padding: 4px 12px; border-radius: 50px;
        font-size: 11px; font-weight: 600; z-index: 2;
    }
    .product-info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .product-info h5 { font-weight: 600; color: var(--dark); margin-bottom: 8px; font-size: 16px; line-height: 1.4; transition: color 0.2s; }
    .product-card:hover .product-info h5 { color: var(--primary); }
    .product-desc { color: var(--slate); font-size: 13px; line-height: 1.5; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .product-footer { margin-top: auto; display: flex; align-items: center; justify-content: space-between; padding-top: 14px; border-top: 1px solid var(--border); }
    .product-price { font-weight: 700; color: var(--primary); font-size: 18px; margin-bottom: 0; }
    .btn-action-round { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s ease; font-size: 14px; }
    .btn-view-detail { background: var(--light); color: var(--slate); border: 1px solid var(--border); }
    .btn-view-detail:hover { background: var(--slate); color: var(--white); }
    .btn-cart-add { background: var(--primary); color: var(--white); }
    .btn-cart-add:hover { background: var(--primary-dark); transform: scale(1.05); color: var(--white); }
    .modal-content-custom { border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: var(--shadow-md); }
    .modal-header-custom { border-bottom: none; padding: 16px 20px 0 20px; }
    .modal-product-img { border-radius: var(--radius-md); width: 100%; aspect-ratio: 4/3; object-fit: cover; margin-bottom: 16px; background: var(--light); }
    .empty-state { padding: 60px 20px; text-align: center; background: var(--white); border-radius: var(--radius-lg); border: 2px dashed var(--border); }
</style>

<section class="product-hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-up">
        <h1>Katalog Produk</h1>
        <p>Pilihan hidangan Frozen Food praktis dan Roti segar berkualitas tinggi untuk keceriaan meja makan Anda.</p>
        <nav class="hero-breadcrumb">
            <a href="{{ route('welcome') }}">Beranda</a>
            <span class="sep">/</span>
            <span class="crumb-active">Produk</span>
        </nav>
    </div>
</section>

<div id="product-catalog">
    @forelse($categories as $category)
        @if($category->products->count() > 0)
            <section class="category-section">
                <div class="container">
                    <div class="section-header" data-aos="fade-up">
                        <h2 class="section-title">{{ $category->name }}</h2>
                    </div>
                    <div class="row g-4">
                        @foreach($category->products as $product)
                            <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                                <div class="product-card-wrapper">
                                    <div class="product-card">
                                        <div class="product-img-wrapper">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-img">
                                            @else
                                                <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Default" class="product-img">
                                            @endif
                                            <span class="product-badge">{{ $category->name }}</span>
                                        </div>
                                        <div class="product-info">
                                            <h5>{{ $product->name }}</h5>
                                            <p class="product-desc">{{ $product->description ?? 'Produk pilihan terbaik dari FAA Frozen Food & Bakery.' }}</p>
                                            <div class="product-footer">
                                                <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                                <div class="d-flex gap-2">
                                                    <a href="#" class="btn-action-round btn-cart-add add-to-cart" data-id="{{ $product->id }}" title="Tambah ke Keranjang">
                                                        <i class="bi bi-cart-plus"></i>
                                                    </a>
                                                    <a href="#" class="btn-action-round btn-view-detail open-modal" 
                                                       data-bs-toggle="modal" data-bs-target="#productModal"
                                                       data-id="{{ $product->id }}"
                                                       data-name="{{ $product->name }}"
                                                       data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
                                                       data-desc="{{ $product->description }}"
                                                       data-img="{{ $product->image ? asset('storage/' . $product->image) : asset('template-sarab/img/logo-toko-faa.png') }}"
                                                       data-unit="{{ $product->unit }}"
                                                       title="Detail Produk">
                                                        <i class="bi bi-plus-lg"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @empty
        <section class="category-section">
            <div class="container py-5">
                <div class="empty-state">
                    <i class="bi bi-box-seam text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mb-0">Belum ada produk yang tersedia saat ini.</p>
                </div>
            </div>
        </section>
    @endforelse
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <img id="modalImg" src="" alt="" class="modal-product-img">
                <h3 id="modalName" class="fw-bold mb-2 text-dark" style="font-size: 22px;"></h3>
                <p id="modalPrice" class="fs-4 fw-bold mb-3" style="color: var(--primary);"></p>
                <p id="modalDesc" class="text-muted small mb-4" style="line-height: 1.6;"></p>
                <div class="mb-4">
                    <span class="badge bg-light text-dark p-2 px-3 border" style="border-radius: 8px; font-weight: 500;">
                        <i class="bi bi-tag-fill text-warning me-2" style="color: var(--primary) !important;"></i> Satuan: <span id="modalUnit"></span>
                    </span>
                </div>
                <button id="checkoutBtn" class="btn w-100 py-2 fw-bold" style="background-color: var(--primary); color: white; border: none; border-radius: 50px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease;">
                    <i class="bi bi-cart-check"></i> Tambah & Lanjut Checkout
                </button>
            </div>
        </div>
    </div>
</div>

@include('chatbot')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('.add-to-cart').on('click', function(e) {
            e.preventDefault();
            @guest
                Swal.fire({
                    title: 'Silakan Login', text: 'Anda harus login terlebih dahulu untuk menggunakan keranjang belanja.',
                    icon: 'warning', showCancelButton: true,
                    confirmButtonText: 'Login Sekarang', cancelButtonText: 'Nanti Saja'
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = "{{ route('login') }}"; }
                });
            @else
                const id = $(this).data('id');
                $.ajax({
                    url: `/cart/add/${id}`,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        $('#cartCount').text(response.cart_count);
                        Swal.fire({ title: 'Berhasil!', text: response.message, icon: 'success', timer: 2000, showConfirmButton: false });
                    }
                });
            @endguest
        });

        const modal = document.getElementById('productModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const name = button.getAttribute('data-name');
            const price = button.getAttribute('data-price');
            const desc = button.getAttribute('data-desc');
            const img = button.getAttribute('data-img');
            const unit = button.getAttribute('data-unit');
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalPrice').textContent = price;
            document.getElementById('modalDesc').textContent = desc || 'Produk pilihan terbaik dari FAA Frozen Food & Bakery.';
            document.getElementById('modalImg').src = img;
            document.getElementById('modalUnit').textContent = unit || '-';
            const checkoutBtn = document.getElementById('checkoutBtn');
            const productId = button.getAttribute('data-id');
            checkoutBtn.onclick = function() {
                @auth
                    $.ajax({
                        url: `/cart/add/${productId}`,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            $('#cartCount').text(response.cart_count);
                            window.location.href = '{{ route("checkout.index") }}';
                        },
                        error: function() {
                            window.location.href = '{{ route("checkout.index") }}';
                        }
                    });
                @else
                    window.location.href = '{{ route("login") }}';
                @endauth
            };
        });
    });
</script>
@endpush
@endsection
