@extends('layouts.public')

@section('title', 'Infografis - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root { --primary: #f97316; --primary-dark: #ea580c; --primary-light: #ffedd5; --dark: #0f172a; --slate: #475569; --light: #f8fafc; --border: #e2e8f0; --white: #ffffff; --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.04); --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08); --radius-lg: 16px; --radius-md: 12px; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--light); color: var(--slate); }
    .info-hero { position: relative; height: 380px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 0 0 32px 32px; }
    .hero-bg { position: absolute; inset: 0; background: url('{{ asset('template-sarab/img/banner-toko-faa.png') }}') no-repeat center center / cover; will-change: transform; }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%); }
    .hero-content { position: relative; z-index: 10; text-align: center; padding: 0 24px; }
    .info-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(36px, 6vw, 54px); font-weight: 700; color: var(--white); margin-bottom: 12px; }
    .info-hero p { font-size: 14px; max-width: 600px; margin: 0 auto 20px auto; color: rgba(255, 255, 255, 0.9) !important; line-height: 1.6; }
    .hero-breadcrumb { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 12px; }
    .hero-breadcrumb a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.2s; }
    .hero-breadcrumb a:hover { color: var(--primary); }
    .hero-breadcrumb .crumb-active { color: var(--primary); font-weight: 500; }
    .hero-breadcrumb .sep { color: rgba(255, 255, 255, 0.3); }
    .info-section { padding: 60px 0; }
    .section-tag { font-size: 12px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); margin-bottom: 6px; }
    .section-title { font-family: 'Playfair Display', serif; font-weight: 700; font-size: clamp(26px, 4vw, 36px); color: var(--dark); margin-bottom: 8px; }
    .info-card-wrapper { height: 100%; }
    .info-card { background: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; border: 1px solid var(--border); height: 100%; display: flex; flex-direction: column; text-decoration: none; }
    .info-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    .info-img-wrapper { position: relative; overflow: hidden; background: #eaeaea; aspect-ratio: 4/3; }
    .info-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease; }
    .info-card:hover .info-img { transform: scale(1.04); }
    .info-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; backdrop-filter: blur(2px); }
    .info-card:hover .info-overlay { opacity: 1; }
    .info-overlay i { color: var(--white); font-size: 22px; background: var(--primary); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    .info-content { padding: 20px; text-align: center; display: flex; align-items: center; justify-content: center; flex-grow: 1; }
    .info-content h5 { font-size: 15px; font-weight: 600; color: var(--dark); margin-bottom: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<section class="info-hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-up">
        <h1>Infografis</h1>
        <p>Informasi menarik seputar gizi, cara penyajian, dan tips bermanfaat dari FAA Frozen Food & Bakery.</p>
        <nav class="hero-breadcrumb">
            <a href="{{ route('welcome') }}">Beranda</a>
            <span class="sep">/</span>
            <span class="crumb-active">Infografis</span>
        </nav>
    </div>
</section>

<section class="info-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-tag">Edukasi &amp; Tips</div>
            <h2 class="section-title">Galeri Infografis</h2>
            <p class="text-muted small">Visualisasi informasi untuk gaya hidup yang lebih praktis dan sehat.</p>
        </div>
        <div class="row g-4 popup-gallery">
            @forelse($infografis as $info)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="info-card-wrapper">
                    <a href="{{ asset('storage/' . $info->gambar) }}" class="info-card" title="{{ $info->judul }}">
                        <div class="info-img-wrapper">
                            <img src="{{ asset('storage/' . $info->gambar) }}" alt="{{ $info->judul }}" class="info-img">
                            <div class="info-overlay"><i class="bi bi-zoom-in"></i></div>
                        </div>
                        <div class="info-content">
                            <h5>{{ $info->judul }}</h5>
                        </div>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="bi bi-bar-chart-line text-muted" style="font-size: 3rem; opacity: 0.3;"></i></div>
                <p class="text-muted">Belum ada data infografis yang tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

@include('chatbot')

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('template-sarab/js/jquery.magnific-popup.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.popup-gallery').magnificPopup({
            delegate: 'a.info-card', type: 'image',
            tLoading: 'Memuat gambar #%curr%...',
            mainClass: 'mfp-img-mobile mfp-with-zoom',
            gallery: { enabled: true, navigateByImgClick: true, preload: [0,1] },
            image: { tError: '<a href="%url%">Gambar #%curr%</a> gagal dimuat.', titleSrc: function(item) { return item.el.attr('title'); } }
        });
    });
</script>
@endpush
@endsection
