@extends('layouts.public')

@section('title', 'FAA - Frozen Food & Bakery')

@section('content')
<style>
    /* ==========================================
       USER-FRIENDLY & BRANDING STYLES (FAA Blue & Orange)
       ========================================== */

    /* Global Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }

    /* Efek Ngambang / Glowing pada Logo Bulat */
    .logo-floating-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        filter: drop-shadow(0 0 12px rgba(0, 74, 173, 0.4));
        animation: floatingEffect 3s ease-in-out infinite;
    }

    @keyframes floatingEffect {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-6px); }
        100% { transform: translateY(0px); }
    }

    .text-faa {
        font-family: 'Poppins', sans-serif;
        letter-spacing: 1px;
        color: #004aad;
    }

    .text-faa .text-red {
        color: #f97316;
    }

    /* Back To Top Button - Lebih Halus & Responsif */
    #btt {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
        background-color: #004aad;
        color: #ffffff;
        border: none;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0, 74, 173, 0.3);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
    }

    #btt.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    #btt:hover {
        background-color: #f97316;
        color: #ffffff;
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
    }

    /* Accordion FAQ Styling */
    #faq .accordion-button:not(.collapsed) {
        background-color: #004aad;
        color: white;
    }
    #faq .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(0, 74, 173, 0.25);
        border-color: #004aad;
    }

    /* Slider / Banner Section Typography Fix */
    .slider_section {
        padding: 0;
        background-color: #f9f9f9;
        overflow: hidden;
    }

    .slider_item-box {
        width: 100%;
        min-height: 550px;
        display: flex;
        align-items: center;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .slider_item-box::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.7);
        z-index: 1;
    }

    .slider_item-container {
        position: relative;
        z-index: 2;
        width: 100%;
    }

    .slider_item-detail h1 {
        font-size: calc(1.8rem + 1.5vw);
        color: #ffffff !important;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
    }

    .slider_item-detail p.text-muted,
    .slider_item-detail p {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 400;
        margin-bottom: 30px;
        max-width: 600px;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.6);
    }

    .slider_img-box img {
        max-width: 100%;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        transition: transform 0.5s ease;
    }

    .slider_img-box:hover img {
        transform: scale(1.03);
    }

    .custom-carousel-btn {
        width: 50px !important;
        height: 50px !important;
        background-color: rgba(0, 74, 173, 0.8) !important;
        border-radius: 50% !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        opacity: 0.8 !important;
        transition: all 0.3s ease !important;
        z-index: 5 !important;
        border: none !important;
    }

    .carousel-control-prev.custom-carousel-btn { left: 20px !important; }
    .carousel-control-next.custom-carousel-btn { right: 20px !important; }

    .custom-carousel-btn:hover {
        background-color: #f97316 !important;
        opacity: 1 !important;
    }

    .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #ffffff;
        opacity: 0.5;
        margin: 0 6px;
        transition: all 0.3s ease;
    }

    .carousel-indicators .active {
        background-color: #f97316 !important;
        width: 24px;
        border-radius: 5px;
        opacity: 1;
    }

    .keunggulan-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 74, 173, 0.05);
        position: relative;
        overflow: hidden;
    }

    .keunggulan-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 74, 173, 0.12);
    }

    .keunggulan-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: #f97316;
        transform: scaleX(0);
        transition: transform 0.4s ease;
        transform-origin: left;
    }

    .keunggulan-card:hover::after {
        transform: scaleX(1);
    }

    .hover-lift {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
    }

    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
    }

    .news-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
    }

    .news-card .img-container {
        overflow: hidden;
        position: relative;
    }

    .news-card:hover img {
        transform: scale(1.08);
    }

    .news-card img {
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .blur-effect {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    #menuPop {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    #menuPop.active {
        display: flex;
    }
</style>

<section class="slider_section position-relative">
   <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
         <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
         <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="1" aria-label="Slide 2"></button>
         <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>

      <div class="carousel-inner">

         <div class="carousel-item active">
         <div class="slider_item-box" style="background-image: url('{{ asset('template-sarab/img/banner-faa-new.jpeg') }}');">
            <div class="slider_item-container">
               <div class="container">
               <div class="row align-items-center">
                  <div class="col-md-6">
                     <div class="slider_item-detail">
                        <h1 class="fw-bold mb-3">
                           Welcome to <br />
                           FAA Frozen Food & Bakery
                        </h1>
                        <p class="mb-4">
                           Nikmati kelezatan roti segar dan berbagai pilihan makanan beku berkualitas tinggi setiap hari. Kami menyajikan produk terbaik untuk keluarga Anda.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                           <a href="{{ route('produk.makanan') }}" class="btn btn-warning text-uppercase rounded-pill px-4 py-2 text-white fw-bold" style="background-color: #f97316; border: none;">
                           Pesan Sekarang
                           </a>
                           <a href="https://wa.me/qr/KXSQYYUQSSG4P1" class="btn btn-outline-light text-uppercase rounded-pill px-4 py-2 fw-bold">
                           Hubungi Kami
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 mt-4 mt-md-0">
                     <div class="slider_img-box text-center">
                        <a href="{{ route('produk.makanan') }}">
                           <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Logo FAA" class="img-fluid" style="max-height: 350px;" />
                        </a>
                     </div>
                  </div>
               </div>
               </div>
            </div>
         </div>
         </div>

         <div class="carousel-item">
         <div class="slider_item-box" style="background-image: url('{{ asset('template-sarab/img/banner-faa-new.jpeg') }}');">
            <div class="slider_item-container">
               <div class="container">
               <div class="row align-items-center">
                  <div class="col-md-6">
                     <div class="slider_item-detail">
                        <h1 class="fw-bold mb-3">
                           Roti Segar <br />
                           Setiap Hari
                        </h1>
                        <p class="mb-4">
                           Dipanggang dengan cinta dan bahan-bahan premium untuk memastikan kelembutan dan rasa yang tak terlupakan di setiap gigitan.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                           <a href="{{ route('produk.makanan') }}" class="btn btn-warning text-uppercase rounded-pill px-4 py-2 text-white fw-bold" style="background-color: #f97316; border: none;">
                           Lihat Menu
                           </a>
                           <a href="https://wa.me/qr/KXSQYYUQSSG4P1" class="btn btn-outline-light text-uppercase rounded-pill px-4 py-2 fw-bold">
                           Hubungi Kami
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 mt-4 mt-md-0">
                     <div class="slider_img-box text-center">
                        <a href="{{ route('produk.makanan') }}">
                           <img src="{{ asset('template-sarab/img/roti-banner.jpg') }}" alt="Roti Segar" class="img-fluid" style="max-height: 350px; border-radius: 20px;" />
                        </a>
                     </div>
                  </div>
               </div>
               </div>
            </div>
         </div>
         </div>

         <div class="carousel-item">
         <div class="slider_item-box" style="background-image: url('{{ asset('template-sarab/img/banner-faa-new.jpeg') }}');">
            <div class="slider_item-container">
               <div class="container">
               <div class="row align-items-center">
                  <div class="col-md-6">
                     <div class="slider_item-detail">
                        <h1 class="fw-bold mb-3">
                           Frozen Food <br />
                           Kualitas Premium
                        </h1>
                        <p class="mb-4">
                           Solusi praktis dan lezat untuk hidangan keluarga Anda. Tersedia berbagai pilihan mulai dari daging olahan hingga camilan lezat.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                           <a href="{{ route('produk.makanan') }}" class="btn btn-warning text-uppercase rounded-pill px-4 py-2 text-white fw-bold" style="background-color: #f97316; border: none;">
                           Belanja Sekarang
                           </a>
                           <a href="https://wa.me/qr/KXSQYYUQSSG4P1" class="btn btn-outline-light text-uppercase rounded-pill px-4 py-2 fw-bold">
                           Hubungi Kami
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6 mt-4 mt-md-0">
                     <div class="slider_img-box text-center">
                        <a href="{{ route('produk.makanan') }}">
                           <img src="{{ asset('template-sarab/img/frozen-banner.jpg') }}" alt="Frozen Food" class="img-fluid" style="max-height: 350px; border-radius: 20px;" />
                        </a>
                     </div>
                  </div>
               </div>
               </div>
            </div>
         </div>
         </div>

      </div>

      <button class="carousel-control-prev custom-carousel-btn" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
         <span class="carousel-control-prev-icon" aria-hidden="true"></span>
         <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next custom-carousel-btn" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
         <span class="carousel-control-next-icon" aria-hidden="true"></span>
         <span class="visually-hidden">Next</span>
      </button>
   </div>
 </section>

<section id="keunggulan" class="py-5 bg-white">
   <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
         <span class="slbl">Mengapa Memilih Kami</span>
         <h2 class="stitle">Keunggulan <span>Toko FAA</span></h2>
         <div class="sline"></div>
      </div>
      <div class="row g-4 text-center">
         <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="keunggulan-card p-4 h-100">
               <div class="mb-3 fs-1" style="color: #004aad;">
                  <i class="fas fa-bread-slice"></i>
               </div>
               <h4 class="fw-bold h5" style="color: #004aad;">Dipanggang Segar Setiap Hari</h4>
               <p class="text-muted">Roti kami dipanggang setiap pagi untuk menjamin kelembutan dan kesegaran rasa di setiap gigitan.</p>
            </div>
         </div>
         <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="keunggulan-card p-4 h-100">
               <div class="mb-3 fs-1" style="color: #004aad;">
                  <i class="fas fa-certificate"></i>
               </div>
               <h4 class="fw-bold h5" style="color: #004aad;">100% Halal & Higienis</h4>
               <p class="text-muted">Proses produksi kami mengikuti standar kebersihan yang ketat dan menggunakan bahan-bahan pilihan yang 100% halal.</p>
            </div>
         </div>
         <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="keunggulan-card p-4 h-100">
               <div class="mb-3 fs-1" style="color: #004aad;">
                  <i class="fas fa-boxes"></i>
               </div>
               <h4 class="fw-bold h5" style="color: #004aad;">Produk Premium Lengkap</h4>
               <p class="text-muted">Tersedia banyak pilihan Frozen Food & Roti Premium untuk melengkapi kebutuhan kuliner keluarga Anda.</p>
            </div>
         </div>
      </div>
   </div>
</section>

<section id="categories" class="py-5 bg-white">
 <div class="container">

    <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
       <div>
          <span class="text-primary fw-bold text-uppercase small d-block mb-1" style="letter-spacing: 1px;">Kategori Produk</span>
          <h2 class="fw-bold m-0" style="color: #0f172a;">Temukan Semua yang Anda Butuhkan</h2>
       </div>
       <div>
          <a href="{{ route('produk.makanan') }}" class="{{ request()->routeIs('produk.makanan') ? 'active' : '' }}" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: #f97316;">
             Lihat Semua Produk <i class="bi bi-arrow-right ms-2"></i>
          </a>
       </div>
    </div>

    <div class="row g-4 mb-4">

       <div class="col-12 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="card border-0 rounded-4 overflow-hidden position-relative text-white shadow-sm hover-lift" style="height: 280px; cursor: pointer;">
             <img class="w-100 h-100" src="{{ asset('template-sarab/img/menu/roti unyil.jpeg') }}" alt="Frozen Food" style="object-fit: cover;">
             <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-end p-4" style="background: rgba(15, 23, 42, 0.7);">
                <span class="badge bg-white bg-opacity-25 blur-effect text-white rounded-pill px-3 py-2 mb-2 align-self-start small">
                   <i class="bi bi-snowflake me-1"></i> 20+ produk
                </span>
                <h3 class="fw-bold m-0 mb-1" style="color: #ffffff;">Frozen Food</h3>
                <p class="text-white-50 m-0 small">Nugget, bakso, sosis, & aneka makanan beku siap masak lainnya.</p>
             </div>
          </div>
       </div>

       <div class="col-12 col-md-6" data-aos="fade-up" data-aos-delay="200">
          <div class="card border-0 rounded-4 overflow-hidden position-relative text-white shadow-sm hover-lift" style="height: 280px; cursor: pointer;">
             <img class="w-100 h-100" src="{{ asset('template-sarab/img/menu/roti all varian.jpeg') }}" alt="Bakery" style="object-fit: cover;">
             <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-end p-4" style="background: rgba(15, 23, 42, 0.7);">
                <span class="badge bg-white bg-opacity-25 blur-effect text-white rounded-pill px-3 py-2 mb-2 align-self-start small">
                   <i class="bi bi-egg-fried me-1"></i> 15+ produk
                </span>
                <h3 class="fw-bold m-0 mb-1" style="color: #ffffff;">Bakery</h3>
                <p class="text-white-50 m-0 small">Roti all varian diproduksi setiap hari.</p>
             </div>
          </div>
       </div>

    </div>

    <div class="row g-4">

       <div class="col-12 col-sm-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
          <div class="card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between shadow-sm hover-lift" style="background-color: #f0f9ff; min-height: 160px; cursor: pointer;">
             <div>
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px; background-color: #e0f2fe; color: #004aad;">
                   <i class="bi bi-basket2-fill"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #0c4a6e;">Roti All Varian</h5>
                <p class="text-muted small m-0">Roti manis, roti tawar, dan varian lainnya</p>
             </div>
             <span class="fw-bold small mt-3" style="color: #004aad;">8+ produk</span>
          </div>
       </div>

       <div class="col-12 col-sm-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
          <div class="card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between shadow-sm hover-lift" style="background-color: #f0fdf4; min-height: 160px; cursor: pointer;">
             <div>
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px; background-color: #dcfce7; color: #15803d;">
                   <i class="bi bi-snow"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #14532d;">Bakso All Varian</h5>
                <p class="text-muted small m-0">Bakso sapi, bakso ayam, dan varian lainnya</p>
             </div>
             <span class="fw-bold small mt-3" style="color: #15803d;">14+ produk</span>
          </div>
       </div>

       <div class="col-12 col-sm-6 col-md-3" data-aos="fade-up" data-aos-delay="500">
          <div class="card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between shadow-sm hover-lift" style="background-color: #f0f9ff; min-height: 160px; cursor: pointer;">
             <div>
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px; background-color: #e0f2fe; color: #004aad;">
                   <i class="bi bi-snow"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #0c4a6e;">Nugget All Varian</h5>
                <p class="text-muted small m-0">Nugget ikan, nugget ayam, dan varian lainnya</p>
             </div>
             <span class="fw-bold small mt-3" style="color: #004aad;">5+ produk</span>
          </div>
       </div>

       <div class="col-12 col-sm-6 col-md-3" data-aos="fade-up" data-aos-delay="600">
          <div class="card border-0 rounded-4 p-4 h-100 d-flex flex-column justify-content-between shadow-sm hover-lift" style="background-color: #fffaf0; min-height: 160px; cursor: pointer;">
             <div>
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 40px; height: 40px; background-color: #fff4e0; color: #f97316;">
                   <i class="bi bi-snow"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #7c2d12;">Sempol All Varian</h5>
                <p class="text-muted small m-0">Sempol ikan, sempol ayam, dan varian lainnya</p>
             </div>
             <span class="fw-bold small mt-3" style="color: #f97316;">5+ produk</span>
          </div>
       </div>

    </div>
 </div>
</section>

<section id="quick-access" class="pb-5 bg-white">
 <div class="container">

    <div class="mb-4" data-aos="fade-up">
       <span class="text-primary fw-bold text-uppercase small d-block mb-1" style="letter-spacing: 1px;">Akses Cepat</span>
       <h2 class="fw-bold m-0" style="color: #0f172a;">Pengalaman Belanja Lebih Modern</h2>
    </div>

    <div class="row g-4 mb-4">

       <div class="col-12 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="card border-0 rounded-4 p-4 position-relative overflow-hidden text-white h-100 shadow-sm hover-lift"
               style="background: #004aad; min-height: 240px;">

             <div class="position-absolute end-0 top-50 translate-middle-y opacity-10"
                  style="width: 180px; height: 180px; border-radius: 50%; background: #ffffff; background-image: url('{{ asset("template-sarab/img/icons/vrShowroomnew.jpg") }}'); background-size: contain; background-repeat: no-repeat; border: 20px solid #ffffff; margin-right: -40px;"></div>

             <div class="position-relative z-1 d-flex flex-column justify-content-between h-100">
                <div>
                   <div class="rounded-3 d-flex align-items-center justify-content-center mb-3"
                        style="width: 45px; height: 45px; background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                      <i class="bi bi-box-seam fs-5 text-white"></i>
                   </div>
                   <h3 class="fw-bold mb-2 fs-4" style="color: #ffffff;">VR 3D Showroom</h3>
                   <p class="text-white-50 small mb-4" style="max-width: 85%;">Jelajahi toko kami secara virtual. Lihat produk dari segala sudut sebelum membeli.</p>
                </div>
                <a href="{{ route('showroom.3d') }}" class="btn rounded-pill px-4 py-2 align-self-start btn-sm fw-semibold d-flex align-items-center text-white"
                   style="background-color: #f97316; border: none;">
                   Masuk Showroom <i class="bi bi-arrow-right ms-2"></i>
                </a>
             </div>
          </div>
       </div>

    </div>

 </div>
</section>

<div id="menuPop">
 <div class="mpbox">
    <button id="mpClose" class="mpclose"><i class="fas fa-times"></i></button>
    <div class="mpimg">
       <img id="mpImg" src="" alt="" />
    </div>
    <div class="mpbody">
       <div id="mpCat"></div>
       <h3 id="mpTitle"></h3>
       <div id="mpStars"></div>
       <div id="mpDesc"></div>
       <div id="mpPrice"></div>
       <div class="mpmeta" id="mpMeta"></div>
       <div class="mptags" id="mpTags"></div>

       <div class="mpqty">
          <button class="mpqbtn" id="mpMinus">-</button>
          <div class="mpqnum" id="mpQnum">1</div>
          <button class="mpqbtn" id="mpPlus">+</button>
       </div>

       <button class="mpaddcart" id="mpAddCart" style="background-color: #004aad;">
          <i class="fas fa-shopping-cart"></i> Add to Cart
       </button>
    </div>
 </div>
</div>

<section id="blog" class="py-5" style="background-color: #fffaf0;">
 <div class="container">

    <div class="text-center mb-5" data-aos="fade-up">
       <span class="d-block text-primary fw-semibold text-capitalize mb-2" style="font-family: 'Poppins', sans-serif; font-style: italic; font-size: 1.15rem;">Berita & Pembaruan</span>
       <h2 class="fw-bold position-relative d-inline-block pb-3" style="color: #0f172a; font-size: 2.25rem;">
          Berita <span class="text-primary">Terbaru</span> Dari FAA
          <span class="position-absolute start-50 translate-middle-x bottom-0 rounded" style="width: 50px; height: 4px; background: #004aad;"></span>
       </h2>
    </div>

    <div class="row g-4 justify-content-center">
       @forelse($beritas as $index => $berita)
       <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
          <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100 bg-white news-card">

             <div class="position-relative overflow-hidden img-container" style="height: 220px;">
                @if($berita->gambar)
                   <img class="w-100 h-100" src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}" style="object-fit: cover; object-position: center;"/>
                @else
                   <img class="w-100 h-100" src="{{ asset('template-sarab/img/blog/' . ($index + 1) . '.jpg') }}" alt="Default" style="object-fit: cover; object-position: center;"/>
                @endif

                @php
                   $date = \Carbon\Carbon::parse($berita->created_at);
                @endphp

                <div class="position-absolute top-0 start-0 m-3 text-white rounded-3 d-flex flex-column align-items-center justify-content-center shadow" style="width: 45px; height: 48px; line-height: 1.1; background-color: #004aad !important;">
                   <span class="fw-bold fs-5">{{ $date->translatedFormat('d') }}</span>
                   <span class="text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $date->translatedFormat('M') }}</span>
                </div>
             </div>

             <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                   <span class="text-primary text-uppercase fw-bold d-block mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #004aad !important;">
                      {{ $berita->kategori ?? 'Berita' }}
                   </span>

                   <h5 class="fw-bold mb-3" style="font-size: 1.15rem; line-height: 1.4;">
                      <a href="{{ route('berita.show.public', $berita->id) }}" class="text-decoration-none text-dark hover-opacity" style="color: #1e293b;">
                         {{ $berita->judul }}
                      </a>
                   </h5>

                   <div class="text-muted small d-flex align-items-center mb-4">
                      <i class="far fa-calendar-alt me-2 text-primary"></i>
                      <span>{{ $date->translatedFormat('d M Y') }}</span>
                   </div>
                </div>

                <a href="{{ route('berita.show.public', $berita->id) }}" class="text-primary fw-bold text-decoration-none small d-inline-flex align-items-center mt-auto" style="letter-spacing: 0.2px; color: #004aad !important;">
                   Baca Selengkapnya <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                </a>
             </div>

          </div>
       </div>
       @empty
       <div class="col-12 text-center py-5">
          <p class="text-muted fs-5">Belum ada berita tersedia.</p>
       </div>
       @endforelse
    </div>

    <div class="text-center mt-5" data-aos="fade-up">
       <a href="{{ route('berita.public') }}" class="btn fw-semibold px-4 py-25 rounded-3 d-inline-flex align-items-center shadow-sm text-white" style="background-color: #004aad; border: none; font-size: 0.95rem; padding-top: 10px; padding-bottom: 10px;">
          Lihat Semua Berita <i class="fas fa-arrow-right ms-2"></i>
       </a>
    </div>

 </div>
</section>

@include('chatbot')

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {

       // 1. Live Search Product
       const searchInput = document.getElementById('searchInput');
       if (searchInput) {
          searchInput.addEventListener('input', function() {
             let query = this.value.toLowerCase().trim();
             let items = document.querySelectorAll('.mwrap');
             let hasResults = false;

             items.forEach(item => {
                let titleEl = item.querySelector('.card-title');
                let descEl = item.querySelector('.text-muted');

                let title = titleEl ? titleEl.textContent.toLowerCase() : '';
                let desc = descEl ? descEl.textContent.toLowerCase() : '';

                if (title.includes(query) || desc.includes(query)) {
                   item.classList.remove('gone');
                   item.style.display = 'block';
                   hasResults = true;
                } else {
                   item.classList.add('gone');
                   item.style.display = 'none';
                }
             });
          });
       }

       // 2. Back To Top (BTT) Logic
       const bttButton = document.getElementById('btt');
       if (bttButton) {
          window.addEventListener('scroll', function () {
             if (window.scrollY > 300) {
                bttButton.classList.add('show');
             } else {
                bttButton.classList.remove('show');
             }
          });

          bttButton.addEventListener('click', function () {
             window.scrollTo({
                top: 0,
                behavior: 'smooth'
             });
          });
       }

       // 3. Form Contact Simulation Feedback
       const ctcBtn = document.getElementById('ctcBtn');
       const ctcOk = document.getElementById('ctcOk');
       if (ctcBtn && ctcOk) {
          ctcBtn.addEventListener('click', function(e) {
             e.preventDefault();
             ctcOk.style.display = 'block';
             setTimeout(() => {
                ctcOk.style.display = 'none';
             }, 4000);
          });
       }
    });
</script>
@endpush
@endsection
