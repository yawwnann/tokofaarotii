@extends('layouts.public')

@section('title', 'Album Kegiatan - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root {
        --primary: #f97316; --primary-dark: #ea580c; --primary-light: #ffedd5;
        --dark: #0f172a; --slate: #475569; --light: #f8fafc;
        --border: #e2e8f0; --white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.04);
        --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
        --radius-lg: 16px; --radius-md: 12px;
    }
    body { font-family: 'Poppins', sans-serif; background-color: var(--light); color: var(--slate); }
    .album-hero { position: relative; height: 380px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 0 0 32px 32px; }
    .hero-bg { position: absolute; inset: 0; background: url('{{ asset('template-sarab/img/banner-toko-faa.png') }}') no-repeat center center / cover; will-change: transform; }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%); }
    .hero-content { position: relative; z-index: 10; text-align: center; padding: 0 24px; }
    .album-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(36px, 6vw, 54px); font-weight: 700; color: var(--white); margin-bottom: 12px; }
    .album-hero p { font-size: 14px; max-width: 600px; margin: 0 auto 20px auto; color: rgba(255, 255, 255, 0.9) !important; line-height: 1.6; }
    .hero-breadcrumb { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 12px; }
    .hero-breadcrumb a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.2s; }
    .hero-breadcrumb a:hover { color: var(--primary); }
    .hero-breadcrumb .crumb-active { color: var(--primary); font-weight: 500; }
    .hero-breadcrumb .sep { color: rgba(255, 255, 255, 0.3); }
    .album-section { padding: 60px 0; }
    .section-tag { font-size: 12px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); margin-bottom: 6px; }
    .section-title { font-family: 'Playfair Display', serif; font-weight: 700; font-size: clamp(26px, 4vw, 36px); color: var(--dark); margin-bottom: 8px; }
    .album-card-wrapper { height: 100%; }
    .album-card { background: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; height: 100%; border: 1px solid var(--border); display: flex; flex-direction: column; text-decoration: none; }
    .album-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    .album-img-wrapper { position: relative; aspect-ratio: 4/3; overflow: hidden; background-color: #eaeaea; }
    .album-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .album-card:hover .album-img { transform: scale(1.04); }
    .album-img-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
    .album-card:hover .album-img-overlay { opacity: 1; }
    .album-img-overlay i { color: var(--white); font-size: 24px; background: var(--primary); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
    .album-info { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
    .album-info h5 { font-size: 16px; font-weight: 600; color: var(--dark); margin-bottom: 8px; line-height: 1.4; }
    .album-info p { color: var(--slate); font-size: 13px; line-height: 1.6; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .album-date { font-size: 12px; color: var(--slate); opacity: 0.8; font-weight: 500; display: flex; align-items: center; gap: 6px; margin-top: auto; border-top: 1px solid var(--border); padding-top: 12px; }
    .album-date i { color: var(--primary); }
</style>

<section class="album-hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-up">
        <h1>Album Kegiatan</h1>
        <p>Kumpulan momen berharga dan dokumentasi berbagai kegiatan FAA Frozen Food & Bakery.</p>
        <nav class="hero-breadcrumb">
            <a href="{{ route('welcome') }}">Beranda</a>
            <span class="sep">/</span>
            <span class="crumb-active">Album</span>
        </nav>
    </div>
</section>

<section class="album-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-tag">Dokumentasi</div>
            <h2 class="section-title">Galeri Foto Kegiatan</h2>
            <p class="text-muted small">Melihat kembali jejak langkah dan kebersamaan kami.</p>
        </div>
        <div class="row g-4 popup-gallery">
            @forelse($albums as $album)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="album-card-wrapper">
                    <a href="{{ $album->gambar ? asset('storage/' . $album->gambar) : '#' }}" class="album-card" title="{{ $album->judul }}">
                        <div class="album-img-wrapper">
                            @if($album->gambar)
                            <img src="{{ asset('storage/' . $album->gambar) }}" alt="{{ $album->judul }}" class="album-img">
                            @else
                            <div class="album-img" style="display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#94a3b8;"><i class="fas fa-image" style="font-size:2rem;"></i></div>
                            @endif
                            <div class="album-img-overlay"><i class="bi bi-fullscreen"></i></div>
                        </div>
                        <div class="album-info">
                            <h5>{{ $album->judul }}</h5>
                            <p>{{ strip_tags($album->deskripsi) }}</p>
                            <div class="album-date">
                                <i class="bi bi-calendar3"></i> 
                                {{ $album->tanggal ? \Carbon\Carbon::parse($album->tanggal)->translatedFormat('d F Y') : \Carbon\Carbon::parse($album->created_at)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="bi bi-images text-muted" style="font-size: 3rem; opacity: 0.3;"></i></div>
                <p class="text-muted">Belum ada album kegiatan yang tersedia.</p>
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
            delegate: 'a.album-card',
            type: 'image',
            tLoading: 'Memuat gambar #%curr%...',
            mainClass: 'mfp-img-mobile mfp-with-zoom',
            gallery: { enabled: true, navigateByImgClick: true, preload: [0,1] },
            image: { tError: '<a href="%url%">Gambar #%curr%</a> gagal dimuat.', titleSrc: function(item) { return item.el.attr('title'); } }
        });
    });
</script>
@endpush
@endsection
