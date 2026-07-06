@extends('layouts.public')

@section('title', 'Portal Berita - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root {
        --primary: #f97316; --primary-dark: #ea580c; --primary-light: #ffedd5;
        --dark: #0f172a; --slate: #475569; --light: #f8fafc;
        --border: #e2e8f0; --white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.05);
        --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
        --radius-lg: 16px; --radius-md: 12px;
    }
    body { font-family: 'Poppins', sans-serif; background-color: var(--light); color: var(--slate); padding-top: 90px; overflow-x: hidden; }
    .hero { position: relative; height: 380px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 0 0 32px 32px; }
    .hero-bg { position: absolute; inset: 0; background-image: url('{{ asset('template-sarab/img/banner-toko-faa.png') }}'); background-size: cover; background-position: center; will-change: transform; }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.6)); }
    .hero-content { position: relative; z-index: 10; text-align: center; padding: 0 20px; }
    .hero-tag { display: inline-block; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.3); color: var(--white); font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; padding: 6px 18px; border-radius: 50px; margin-bottom: 16px; }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(32px, 5vw, 54px); font-weight: 700; color: var(--white); margin-bottom: 16px; }
    .hero h1 em { font-style: italic; color: var(--primary); }
    .hero-breadcrumb { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 13px; }
    .hero-breadcrumb a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.2s; }
    .hero-breadcrumb a:hover { color: var(--primary); }
    .hero-breadcrumb .crumb-active { color: var(--primary); font-weight: 500; }
    .hero-breadcrumb .sep { color: rgba(255, 255, 255, 0.4); }
    .main-content { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
    .section-header { margin-bottom: 36px; }
    .section-tag { font-size: 12px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); margin-bottom: 8px; }
    .section-title { font-family: 'Playfair Display', serif; font-size: clamp(26px, 4vw, 36px); font-weight: 700; color: var(--dark); }
    .section-title span { color: var(--primary); }
    .section-desc { font-size: 14px; color: var(--slate); margin-top: 6px; }
    .featured-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; margin-bottom: 60px; }
    .card-featured { background: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-md); border: 1px solid var(--border); display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card-featured:hover { transform: translateY(-6px); box-shadow: 0 20px 35px -5px rgba(15, 23, 42, 0.12); }
    .card-featured-img { position: relative; aspect-ratio: 16/9; overflow: hidden; }
    .card-featured-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
    .card-featured:hover .card-featured-img img { transform: scale(1.04); }
    .img-badge-top { position: absolute; top: 16px; left: 16px; background: var(--primary); color: var(--white); font-size: 11px; font-weight: 500; padding: 4px 12px; border-radius: 6px; z-index: 2; }
    .card-featured-body { padding: 30px; display: flex; flex-direction: column; flex-grow: 1; }
    .card-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; font-size: 12px; }
    .meta-tag { font-weight: 600; color: var(--primary); background: var(--primary-light); padding: 3px 10px; border-radius: 4px; }
    .meta-loc { color: var(--slate); }
    .meta-loc i { color: var(--primary); margin-right: 4px; }
    .card-featured h3 { font-size: clamp(20px, 2.5vw, 24px); font-weight: 700; color: var(--dark); line-height: 1.4; margin-bottom: 12px; }
    .card-featured p { font-size: 14px; line-height: 1.6; color: var(--slate); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 20px; }
    .btn-read { display: inline-flex; align-items: center; gap: 8px; background: var(--primary); color: var(--white) !important; padding: 10px 22px; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; text-decoration: none; align-self: flex-start; transition: background 0.2s ease; }
    .btn-read:hover { background: var(--primary-dark); }
    .side-stack { display: flex; flex-direction: column; gap: 20px; }
    .card-side { background: var(--white); border-radius: var(--radius-md); padding: 20px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); transition: all 0.2s ease; }
    .card-side:hover { border-color: var(--primary); box-shadow: var(--shadow-md); }
    .side-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 11px; }
    .side-badge { font-weight: 600; color: var(--primary); }
    .side-date { color: var(--slate); }
    .card-side h4 { font-size: 14px; font-weight: 600; color: var(--dark); line-height: 1.4; margin-bottom: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .card-side p { font-size: 13px; color: var(--slate); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 12px; }
    .side-link { font-size: 12px; font-weight: 500; color: var(--primary); text-decoration: none; }
    .side-link:hover { color: var(--primary-dark); }
    .card-side-promo { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: var(--radius-md); padding: 24px; color: var(--white); text-align: center; }
    .card-side-promo i { font-size: 24px; margin-bottom: 8px; }
    .card-side-promo h4 { color: var(--white); font-size: 14px; margin-bottom: 4px; }
    .card-side-promo p { font-size: 12px; opacity: 0.9; margin-bottom: 0; }
    .all-news-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-tab { font-size: 12px; font-weight: 500; padding: 8px 16px; border-radius: 30px; border: 1px solid var(--border); background: var(--white); color: var(--slate); cursor: pointer; transition: all 0.2s; }
    .filter-tab.active, .filter-tab:hover { background: var(--primary); border-color: var(--primary); color: var(--white); }
    .news-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
    .card-news { background: var(--white); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; height: 100%; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card-news:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
    .card-news-img { position: relative; aspect-ratio: 4/3; overflow: hidden; background-color: #eaeaea; }
    .card-news-img img { width: 100%; height: 100%; object-fit: cover; }
    .news-date-pill { position: absolute; top: 12px; right: 12px; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); font-size: 11px; color: var(--white); padding: 3px 10px; border-radius: 4px; }
    .card-news-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
    .news-source { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--primary); margin-bottom: 6px; }
    .card-news h5 { font-size: 15px; font-weight: 600; color: var(--dark); line-height: 1.4; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .card-news p { font-size: 13px; line-height: 1.6; color: var(--slate); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 16px; }
    .card-news-footer { margin-top: auto; padding-top: 12px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .btn-detail { font-size: 12px; font-weight: 500; color: var(--primary); text-decoration: none; }
    .btn-detail:hover { color: var(--primary-dark); }
    .news-read-time { font-size: 11px; color: var(--slate); opacity: 0.8; }
    .empty-state { grid-column: 1 / -1; padding: 60px 20px; text-align: center; }
    .empty-state i { font-size: 40px; color: var(--slate); opacity: 0.3; margin-bottom: 10px; display: block; }
    .fade-in { opacity: 0; transform: translateY(15px); transition: opacity 0.5s ease, transform 0.5s ease; }
    .fade-in.visible { opacity: 1; transform: translateY(0); }
    @media (max-width: 1024px) { .featured-grid { grid-template-columns: 1fr; gap: 24px; } .side-stack { flex-direction: row; } .side-stack > div { flex: 1; } .news-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { body { padding-top: 70px; } .hero { height: 280px; } .side-stack { flex-direction: column; } .news-grid { grid-template-columns: 1fr; } .all-news-header { flex-direction: column; align-items: flex-start; } }
</style>

<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content fade-in">
        <div class="hero-tag">Portal Berita &amp; Kegiatan</div>
        <h1>Berita <em>Kegiatan</em></h1>
        <nav class="hero-breadcrumb">
            <a href="{{ route('welcome') }}">Beranda</a>
            <span class="sep">/</span>
            <a href="{{ route('faq.public') }}">FAQ</a>
            <span class="sep">/</span>
            <span class="crumb-active">Berita</span>
        </nav>
    </div>
</section>

<main class="main-content">
    <section style="margin-bottom: 60px;" class="fade-in">
        <div class="section-header">
            <div class="section-tag">Berita Terbaru</div>
            <h2 class="section-title">Update Terkini dari <span>Toko FAA</span></h2>
            <p class="section-desc">Ikuti terus perkembangan kegiatan, promo, dan informasi terbaru kami.</p>
        </div>
        @php $featured = $beritas->first(); $sideItems = $beritas->skip(1)->take(2); @endphp
        @if($featured)
        <div class="featured-grid">
            <div class="card-featured">
                <div class="card-featured-img">
                    @if($featured->gambar)
                        <img src="{{ asset('storage/' . $featured->gambar) }}" alt="{{ $featured->judul }}">
                    @else
                        <img src="{{ asset('template-sarab/img/banner-toko-faa.png') }}" alt="Default">
                    @endif
                    <div class="img-badge-top">Sorotan Utama</div>
                </div>
                <div class="card-featured-body">
                    <div class="card-meta">
                        <span class="meta-tag">Terupdate</span>
                        <span class="meta-loc"><i class="bi bi-geo-alt-fill"></i> Desa Karyamakmur</span>
                    </div>
                    <h3>{{ $featured->judul }}</h3>
                    <p>{{ strip_tags($featured->isi) }}</p>
                    <a href="{{ route('berita.show.public', $featured->id) }}" class="btn-read">Baca Selengkapnya <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="side-stack">
                @foreach($sideItems as $side)
                <div class="card-side">
                    <div class="side-top">
                        <span class="side-badge">Kegiatan</span>
                        <span class="side-date">{{ \Carbon\Carbon::parse($side->created_at)->translatedFormat('d M Y') }}</span>
                    </div>
                    <h4>{{ $side->judul }}</h4>
                    <p>{{ Str::limit(strip_tags($side->isi), 100) }}</p>
                    <a href="{{ route('berita.show.public', $side->id) }}" class="side-link">Detail Berita <i class="bi bi-chevron-right"></i></a>
                </div>
                @endforeach
                @if($sideItems->count() < 2)
                <div class="card-side-promo">
                    <i class="bi bi-bell-fill"></i>
                    <h4>Nantikan Info Lainnya</h4>
                    <p>Pantau terus portal ini untuk update terbaru selanjutnya.</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </section>

    <section id="semua-berita">
        <div class="all-news-header fade-in">
            <div>
                <div class="section-tag">Arsip Lengkap</div>
                <h2 class="section-title">Semua <span>Berita</span></h2>
            </div>
            <div class="filter-tabs">
                <button class="filter-tab active" data-cat="all">Semua</button>
                <button class="filter-tab" data-cat="bakery">Bakery</button>
                <button class="filter-tab" data-cat="frozen">Frozen Food</button>
                <button class="filter-tab" data-cat="promo">Promo</button>
            </div>
        </div>
        <div class="news-grid" id="newsGrid">
            @forelse($beritas as $item)
            <div class="card-news news-item fade-in" data-cat="{{ $item->kategori ?? 'all' }}">
                <div class="card-news-img">
                    @if($item->gambar)
                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}">
                    @else
                        <img src="{{ asset('template-sarab/img/blog/2.jpg') }}" alt="Default">
                    @endif
                    <div class="news-date-pill">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</div>
                </div>
                <div class="card-news-body">
                    <div class="news-source">Berita Toko</div>
                    <h5>{{ $item->judul }}</h5>
                    <p>{{ Str::limit(strip_tags($item->isi), 120) }}</p>
                    <div class="card-news-footer">
                        <a href="{{ route('berita.show.public', $item->id) }}" class="btn-detail">Selengkapnya <i class="bi bi-arrow-right"></i></a>
                        <span class="news-read-time"><i class="bi bi-clock"></i> 3 mnt</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="bi bi-newspaper"></i>
                <p class="m-0">Belum ada arsip berita saat ini.</p>
            </div>
            @endforelse
        </div>
    </section>
</main>

@include('chatbot')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fadeEls = document.querySelectorAll('.fade-in');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) { setTimeout(() => entry.target.classList.add('visible'), i * 50); observer.unobserve(entry.target); }
        });
    }, { threshold: 0.05 });
    fadeEls.forEach(el => observer.observe(el));

    document.querySelectorAll('.filter-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.cat;
            document.querySelectorAll('.news-item').forEach(card => {
                const cardCat = card.dataset.cat || 'all';
                card.style.display = (cat === 'all' || cardCat === cat) ? 'flex' : 'none';
            });
        });
    });
});
</script>
@endpush
@endsection
