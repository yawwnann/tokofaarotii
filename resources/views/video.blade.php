@extends('layouts.public')

@section('title', 'Video Dokumentasi - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root { --primary: #f97316; --primary-dark: #ea580c; --primary-light: #ffedd5; --dark: #0f172a; --slate: #475569; --light: #f8fafc; --border: #e2e8f0; --white: #ffffff; --shadow-sm: 0 2px 4px rgba(15, 23, 42, 0.04); --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.08); --radius-lg: 16px; --radius-md: 12px; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--light); color: var(--slate); }
    .video-hero { position: relative; height: 380px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 0 0 32px 32px; }
    .hero-bg { position: absolute; inset: 0; background: url('{{ asset('template-sarab/img/banner-toko-faa.png') }}') no-repeat center center / cover; will-change: transform; }
    .hero-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.75); }
    .hero-content { position: relative; z-index: 10; text-align: center; padding: 0 24px; }
    .video-hero h1 { font-family: 'Poppins', sans-serif; font-size: clamp(36px, 6vw, 54px); font-weight: 700; color: var(--white); margin-bottom: 12px; }
    .video-hero p { font-size: 14px; max-width: 600px; margin: 0 auto 20px auto; color: rgba(255, 255, 255, 0.9) !important; line-height: 1.6; }
    .hero-breadcrumb { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 12px; }
    .hero-breadcrumb a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.2s; }
    .hero-breadcrumb a:hover { color: var(--primary); }
    .hero-breadcrumb .crumb-active { color: var(--primary); font-weight: 500; }
    .hero-breadcrumb .sep { color: rgba(255, 255, 255, 0.3); }
    .video-section { padding: 60px 0; }
    .section-tag { font-size: 12px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); margin-bottom: 6px; }
    .section-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: clamp(26px, 4vw, 36px); color: var(--dark); margin-bottom: 8px; }
    .video-card-wrapper { height: 100%; }
    .video-card { background: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; height: 100%; border: 1px solid var(--border); display: flex; flex-direction: column; }
    .video-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    .video-thumb-wrapper { position: relative; aspect-ratio: 16/9; overflow: hidden; background: #000; }
    .video-thumb { width: 100%; height: 100%; object-fit: cover; opacity: 0.9; transition: transform 0.5s ease, opacity 0.3s ease; }
    .video-card:hover .video-thumb { opacity: 1; transform: scale(1.04); }
    .play-btn { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 56px; height: 56px; background: var(--primary); color: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4); transition: all 0.3s ease; z-index: 2; }
    .play-btn i { margin-left: 4px; }
    .video-card:hover .play-btn { transform: translate(-50%, -50%) scale(1.1); background: var(--primary-dark); box-shadow: 0 6px 20px rgba(234, 88, 12, 0.5); }
    .social-badge-overlay { position: absolute; top: 12px; left: 12px; padding: 6px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 3; }
    .badge-instagram { background: #fdf2f8; color: #db2777; }
    .badge-tiktok { background: #f8fafc; color: #0f172a; }
    .video-info { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
    .video-info h5 { font-size: 16px; font-weight: 600; color: var(--dark); margin-bottom: 8px; line-height: 1.4; }
    .video-info p { color: var(--slate); font-size: 13px; line-height: 1.6; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .modal-content { background: #000; border: none; border-radius: 12px; overflow: hidden; }
    .modal-body { padding: 0; position: relative; }
    .btn-close-custom { position: absolute; top: -40px; right: 0; background: none; border: none; color: var(--white); font-size: 24px; opacity: 0.8; transition: opacity 0.2s; }
    .btn-close-custom:hover { opacity: 1; }
</style>

<section class="video-hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-up">
        <h1>Video Dokumentasi</h1>
        <p>Saksikan berbagai keseruan kegiatan dan proses pembuatan produk terbaik dari FAA Frozen Food & Bakery.</p>
        <nav class="hero-breadcrumb">
            <a href="{{ route('welcome') }}">Beranda</a>
            <span class="sep">/</span>
            <span class="crumb-active">Video</span>
        </nav>
    </div>
</section>

<section class="video-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-tag">Streaming &amp; Vlog</div>
            <h2 class="section-title">Galeri Video</h2>
            <p class="text-muted small">Koleksi dokumentasi aktivitas dan kreasi hidangan kami.</p>
        </div>
        <div class="row g-4">
            @forelse($videos as $video)
            @php
                $isSocialMedia = str_contains($video->url, 'instagram.com') || str_contains($video->url, 'tiktok.com');
                $videoID = '';
                if (!$isSocialMedia && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^\"&?/ ]{11})%i', $video->url, $match)) { $videoID = $match[1]; }
                $thumbUrl = (!$isSocialMedia && $videoID) ? "https://img.youtube.com/vi/{$videoID}/hqdefault.jpg" : asset('template-sarab/img/frozen-banner.jpg');
            @endphp
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                <div class="video-card-wrapper">
                    <div class="video-card">
                        <div class="video-thumb-wrapper">
                            <img src="{{ $thumbUrl }}" alt="{{ $video->judul }}" class="video-thumb" onerror="this.src='{{ asset('template-sarab/img/frozen-banner.jpg') }}'">
                            @if($isSocialMedia)
                                @if(str_contains($video->url, 'instagram.com'))
                                    <div class="social-badge-overlay badge-instagram"><i class="bi bi-instagram"></i> Instagram</div>
                                @else
                                    <div class="social-badge-overlay badge-tiktok"><i class="bi bi-tiktok"></i> TikTok</div>
                                @endif
                                <a href="{{ $video->url }}" target="_blank" class="play-btn" aria-label="Tonton di Media Sosial">
                                    <i class="bi bi-box-arrow-up-right" style="font-size: 18px; margin-left: 0;"></i>
                                </a>
                            @else
                                <a href="#" class="play-btn" data-bs-toggle="modal" data-bs-target="#videoModal" data-url="{{ $video->url }}" data-title="{{ $video->judul }}" aria-label="Putar Video">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            @endif
                        </div>
                        <div class="video-info">
                            <h5>{{ $video->judul }}</h5>
                            <p>{{ strip_tags($video->deskripsi) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="bi bi-camera-video text-muted" style="font-size: 3rem; opacity: 0.3;"></i></div>
                <p class="text-muted">Belum ada video dokumentasi yang tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" title="YouTube video" allow="autoplay; fullscreen" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@include('chatbot')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const videoModal = document.getElementById('videoModal');
        const videoIframe = document.getElementById('videoIframe');
        if (videoModal && videoIframe) {
            videoModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                let url = button.getAttribute('data-url');
                if (url.includes('watch?v=')) { url = url.replace('watch?v=', 'embed/'); }
                else if (url.includes('youtu.be/')) { url = url.replace('youtu.be/', 'youtube.com/embed/'); }
                url += url.includes('?') ? '&autoplay=1' : '?autoplay=1';
                videoIframe.setAttribute('src', url);
            });
            videoModal.addEventListener('hide.bs.modal', function () { videoIframe.setAttribute('src', ''); });
        }
    });
</script>
@endpush
@endsection
