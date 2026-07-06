@extends('layouts.public')

@section('title', 'Tentang Kami - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root {
        --primary-blue: #004aad;
        --primary-blue-dark: #003580;
        --primary-orange: #f97316;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }

    body { font-family: 'Poppins', sans-serif; color: var(--text-dark); background-color: #ffffff; }

    .logo-floating-wrapper { display: flex; align-items: center; justify-content: center; border-radius: 50%; filter: drop-shadow(0 0 12px rgba(0, 74, 173, 0.4)); animation: floatingEffect 3s ease-in-out infinite; }
    @keyframes floatingEffect { 0% { transform: translateY(0px); } 50% { transform: translateY(-5px); } 100% { transform: translateY(0px); } }

    .about-hero { position: relative; height: 380px; display: flex; align-items: center; justify-content: center; overflow: hidden; background-color: #000; }
    .hero-bg { position: absolute; inset: 0; background: url('{{ asset('template-sarab/img/banner-toko-faa.png') }}') no-repeat center center / cover; }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0, 74, 173, 0.85) 0%, rgba(15, 15, 15, 0.8) 70%); }
    .hero-content { position: relative; z-index: 10; text-align: center; padding: 0 24px; }
    .about-hero h1 { font-family: 'Playfair Display', serif; font-size: clamp(2.5rem, 6vw, 4.5rem); font-weight: 900; color: #ffffff; margin-bottom: 15px; letter-spacing: -1px; }
    .about-hero p { font-size: 1.1rem; max-width: 650px; margin: 0 auto 20px auto; color: rgba(255, 255, 255, 0.9) !important; }
    .hero-breadcrumb { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; }
    .hero-breadcrumb a { color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.2s; }
    .hero-breadcrumb a:hover { color: #ffffff; }
    .hero-breadcrumb .crumb-active { color: var(--primary-orange); }
    .hero-breadcrumb .sep { color: rgba(255,255,255,0.3); }
    .hero-wave { position: absolute; bottom: -1px; left: 0; right: 0; width: 100%; height: 30px; }
    section { padding: 80px 0; }
    .section-title { font-family: 'Playfair Display', serif; font-weight: 800; font-size: 2.25rem; color: var(--text-dark); margin-bottom: 20px; }
    .history-img { border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 74, 173, 0.08); width: 100%; transition: transform 0.4s ease; }
    .history-img:hover { transform: scale(1.015); }
    .vm-card { background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); height: 100%; border: 1px solid #e2e8f0; transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); }
    .vm-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 74, 173, 0.08); border-color: var(--primary-blue); }
    .vm-icon { width: 56px; height: 56px; background: rgba(0, 74, 173, 0.08); color: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border-radius: 16px; margin-bottom: 24px; }
    .structure-header { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 8px; }
    .structure-header .line { height: 1px; width: 35px; background-color: var(--primary-orange); }
    .structure-header .accent-text { color: var(--primary-orange); font-weight: 700; font-size: 0.75rem; letter-spacing: 2.5px; text-transform: uppercase; }
    .section-title.bskm-title { color: var(--primary-blue); font-weight: 800; font-size: 2.25rem; }
    .team-card { background: transparent; border: none; display: flex; flex-direction: column; align-items: center; width: 100%; transition: transform 0.3s ease; }
    .team-img-wrapper { width: 170px; height: 170px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary-blue); padding: 5px; background: #ffffff; margin-bottom: 16px; transition: all 0.3s ease; box-shadow: 0 10px 25px rgba(0, 74, 173, 0.05); }
    .team-card:hover .team-img-wrapper { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0, 74, 173, 0.12); border-color: var(--primary-orange); }
    .team-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .team-info { text-align: center; }
    .team-info h5 { font-weight: 700; color: #0f172a; margin-bottom: 4px; font-size: 1.1rem; }
    .team-info p { color: var(--text-muted); font-size: 0.85rem; font-weight: 500; margin-bottom: 0; }
    .swiper-pagination-team { display: flex; justify-content: center; gap: 8px; margin-top: 30px; }
    .swiper-pagination-team .swiper-pagination-bullet { width: 8px; height: 8px; background-color: #cbd5e1; opacity: 1; transition: all 0.3s ease; border-radius: 4px; }
    .swiper-pagination-team .swiper-pagination-bullet-active { background-color: var(--primary-blue); width: 24px; }
</style>

<section class="about-hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content" data-aos="fade-up">
        <h1>Tentang Kami</h1>
        <p>Mengenal lebih dekat FAA Frozen Food & Bakery, penyedia hidangan lezat dan berkualitas untuk keluarga Anda.</p>
        <nav class="hero-breadcrumb">
            <a href="{{ route('welcome') }}">Beranda</a>
            <span class="sep">/</span>
            <span class="crumb-active">Tentang Kami</span>
        </nav>
    </div>
    <svg class="hero-wave" viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0 60 L0 30 Q360 0 720 30 Q1080 60 1440 30 L1440 60 Z" fill="#ffffff"/>
    </svg>
</section>

<section class="history-section bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="{{ asset('template-sarab/img/banner-faa-new.jpeg') }}" alt="Sejarah FAA" class="history-img">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title">Sejarah Kami</h2>
                <p class="lead fw-bold text-primary mb-3">FAA Frozen Food & Bakery</p>
                <p>FAA Frozen Food & Bakery didirikan pada Mei 2013 oleh Bapak Yadi Cahyadi di Air Hanyut, Sungailiat, Bangka Belitung. Usaha ini bermula dari inisiatif rumahan sang pemilik yang bereksperimen membuat nugget sehat karena anak-anaknya sangat menggemari jajanan tersebut. Nama "FAA" sendiri dipilih sebagai bentuk kasih sayang kepada buah hatinya, yang diambil dari inisial nama ketiga anak beliau, yaitu Fatimah, Aisyah, dan Afgan.</p>
                <p>Setelah produk nugget buatannya mendapat respons positif dan pesanan dari para tetangga, Bapak Yadi melihat peluang bisnis yang menjanjikan dan resmi mengomersialkan usahanya. Seiring berjalan waktu, bisnis ini terus berkembang dan bervariasi dengan menambahkan lini produk bakso serta olahan kue melalui FAA Bakery. Berawal dari usaha kecil dengan 3 orang karyawan, kini FAA telah menjadi UMKM terstruktur yang bermitra dengan PLUT Bangka Belitung dan didukung oleh jaringan reseller lokal yang luas.</p>
            </div>
        </div>
    </div>
</section>

<section class="vm-section bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Visi & Misi</h2>
            <p class="text-muted">Komitmen kami dalam melayani Anda.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="vm-card">
                    <div class="vm-icon"><i class="bi bi-eye"></i></div>
                    <h3 class="fw-bold mb-3 h4">Visi</h3>
                    <p class="text-muted lh-lg m-0">Menghasilkan produk yang baik dengan mengutamakan kepuasan konsumen melalui penyediaan pilihan makanan yang sehat dan harga yang terjangkau.</p>
                </div>
            </div>
            <div class="col-md-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="vm-card">
                    <div class="vm-icon"><i class="bi bi-bullseye"></i></div>
                    <h3 class="fw-bold mb-3 h4">Misi</h3>
                    <ul class="list-unstyled text-muted lh-lg m-0">
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-primary me-2"></i> Meningkatkan kualitas produk secara berkala dengan tetap mempertahankan harga yang kompetitif.</li>
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-primary me-2"></i> Menyesuaikan produk makanan olahan dengan selera masyarakat setempat</li>
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-primary me-2"></i> Menciptakan kemasan produk yang aman dan higienis bagi konsumen.</li>
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-primary me-2"></i> Menjaga suasana produksi tetap nyaman serta mempertahakan standar pelayanan terbaik.</li>
                        <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-primary me-2"></i> Mengutamakan kepuasan konsumen dalam setiap aspek penjualan dan distribusi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="team-section bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="structure-header">
                <span class="line"></span><span class="accent-text">STRUKTUR</span><span class="line"></span>
            </div>
            <h2 class="section-title bskm-title">Tim Kami</h2>
            <p class="text-muted">Orang-orang di balik kelezatan FAA Frozen Food & Bakery.</p>
        </div>
        <div class="swiper team-swiper" data-aos="fade-up">
            <div class="swiper-wrapper">
                @forelse($pegawais as $pegawai)
                <div class="swiper-slide">
                    <div class="team-card">
                        <div class="team-img-wrapper">
                            @if($pegawai->foto)
                                <img src="{{ asset('storage/' . $pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="team-img">
                            @else
                                <img src="{{ asset('template-sarab/img/banner-toko-faa.png') }}" alt="Default" class="team-img">
                            @endif
                        </div>
                        <div class="team-info">
                            <h5>{{ $pegawai->nama }}</h5>
                            <p>{{ $pegawai->posisi }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="swiper-slide text-center py-4"><p class="text-muted">Data pegawai belum tersedia.</p></div>
                @endforelse
            </div>
            <div class="swiper-pagination-team"></div>
        </div>
    </div>
</section>

@include('chatbot')

@push('scripts')
<script src="{{ asset('template-sarab/js/swiper-bundle.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.team-swiper')) {
            new Swiper(".team-swiper", {
                slidesPerView: 1, spaceBetween: 30, loop: true,
                autoplay: { delay: 3000, disableOnInteraction: false },
                pagination: { el: ".swiper-pagination-team", clickable: true },
                breakpoints: { 576: { slidesPerView: 2 }, 992: { slidesPerView: 4 } },
            });
        }
    });
</script>
@endpush
@endsection
