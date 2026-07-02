<style>
    :root {
        --blue: #004aad;
        --blue-dark: #003580;
        --orange: #f97316;
        --orange-soft: #fff7ed;
        --dark: #1e293b;
        --gray: #64748b;
        --light: #f8fafc;
        --white: #ffffff;
        --border: #e2e8f0;
    }

    .navbar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 3px solid var(--blue);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        box-shadow: 0 4px 20px rgba(0, 74, 173, 0.1);
        transition: all 0.4s ease;
    }

    body {
        padding-top: 85px;
    }

    html {
        scroll-padding-top: 95px;
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: white;
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 10px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .nav-actions {
            flex-wrap: wrap;
            justify-content: flex-start !important;
        }
        .nav-actions .flex-grow-1 {
            width: 100%;
            max-width: 100% !important;
            order: -1;
            margin-bottom: 1rem;
        }
    }

    @media (min-width: 992px) and (max-width: 1250px) {
        .nav-links {
            gap: 10px !important;
        }
        .nav-links a {
            padding: 8px 8px !important;
            font-size: 11px !important;
        }
        .nav-actions .flex-grow-1 {
            max-width: 200px !important;
        }
    }

    .brand-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: 20px;
        color: var(--blue) !important;
        letter-spacing: -0.2px;
        line-height: 1;
    }

    .brand-sub {
        display: block;
        font-size: 8px;
        font-weight: 600;
        color: var(--gray);
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 2px;
        list-style: none;
    }

    .nav-links a {
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        padding: 8px 15px;
        border-radius: 8px;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: 'Poppins', sans-serif;
    }

    .nav-links a:hover { 
        background: var(--orange-soft); 
        color: var(--orange); 
    }
    
    .nav-links a.active { color: var(--blue); font-weight: 600; }

    /* Dokumentasi Dropdown Custom Styling */
    .nav-link.dropdown-toggle-custom {
        background-color: transparent;
        color: #334155 !important;
        border-radius: 8px;
        padding: 6px 15px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .nav-link.dropdown-toggle-custom:hover {
        background-color: var(--orange-soft);
        color: var(--orange) !important;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 40px rgba(0, 74, 173, 0.1);
        border-radius: 12px;
        padding: 0.75rem;
    }

    .dropdown-item-icon {
        width: 35px;
        height: 35px;
        background-color: var(--orange-soft);
        color: var(--orange);
        transition: all 0.2s;
    }

    .dropdown-item:hover .dropdown-item-icon {
        background-color: var(--orange);
        color: white;
    }

    .dropdown-item:hover {
        background-color: var(--orange-soft);
        color: var(--orange);
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-nav-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        background: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--dark);
        transition: all 0.2s;
        position: relative;
        text-decoration: none;
        font-size: 13px;
    }

    .btn-nav-icon:hover { border-color: var(--blue); color: var(--blue); }

    .cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 17px;
        height: 17px;
        background: var(--orange);
        border-radius: 50%;
        font-size: 8px;
        font-weight: 800;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-login {
        background: var(--blue);
        color: white !important;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-login:hover { background: var(--blue-dark); transform: translateY(-1px); }

    .btn-dashboard {
        background: transparent;
        color: var(--blue) !important;
        border: 1.5px solid var(--blue);
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-dashboard:hover { background: var(--blue); color: white !important; }

    .navbar-toggler {
        display: none;
        background: none;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        width: 40px;
        height: 40px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 5px;
        padding: 8px;
    }

    .navbar-toggler span {
        display: block;
        width: 20px;
        height: 2px;
        background: var(--dark);
        border-radius: 2px;
        transition: all 0.3s;
    }

    @media (max-width: 900px) {
        .nav-links { display: none; }
        .navbar-toggler { display: flex; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Search Overlay -->
<div id="searchOv">
    <button id="searchClose" class="sovclose"><i class="fas fa-times"></i></button>
    <div class="sovbox">
    <h4>Apa yang Anda dambakan hari ini?</h4>
    <div class="sovinput">
        <input type="text" id="searchInput" placeholder="Cari roti, frozen food, pastry...">
        <button><i class="fas fa-search"></i></button>
    </div>
    <div class="sovcats">
        <div class="sovcat active" data-cat="all">
            <img src="{{ asset('template-sarab/img/category/1.jpg') }}" alt="Semua"> <span>Semua Item</span>
        </div>
        <div class="sovcat" data-cat="bread">
            <img src="{{ asset('template-sarab/img/category/1.jpg') }}" alt="Roti"> <span>Bakery</span>
        </div>
        <div class="sovcat" data-cat="frozen">
            <img src="{{ asset('template-sarab/img/category/2.jpg') }}" alt="Frozen"> <span>Frozen Food</span>
        </div>
        <div class="sovcat" data-cat="pastry">
            <img src="{{ asset('template-sarab/img/category/3.jpg') }}" alt="Pastry"> <span>Tentang Kami</span>
        </div>
    </div>
    <div class="sovtrend">
        <p>🔥 Pencarian yang Sedang Tren</p>
        <span class="ttag">Roti O Coklat</span>
        <span class="ttag">Bakso Ayam</span>
        <span class="ttag">Visi & Misi Toko FAA</span>
    </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 fixed-top">
   <div class="container-fluid px-lg-4">
      
      <a class="navbar-brand d-flex align-items-center gap-3 text-decoration-none me-4" href="{{ route('welcome') }}">
         <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Logo FAA" class="brand-logo-img" style="height: 45px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
         <span class="brand-text m-0">FAA <small style="font-size: 10px; display: block; font-weight: 500; color: var(--gray);">FROZEN & BAKERY</small></span>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
         <i class="bi bi-list fs-2 text-blue"></i>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
         <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between w-100 gap-3 mt-3 mt-lg-0">
            
            <ul class="nav-links d-flex flex-column flex-lg-row align-items-lg-center list-unstyled gap-3 gap-lg-4 m-0 flex-shrink-0">
               <li><a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'active' : '' }}">Beranda</a></li>
               <li><a href="{{ route('tentang-kami') }}" class="{{ request()->routeIs('tentang-kami') ? 'active' : '' }}">Tentang Kami</a></li>
               
               <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle dropdown-toggle-custom" 
                     href="#" 
                     id="navbarDropdown" 
                     role="button" 
                     data-bs-toggle="dropdown" 
                     aria-expanded="false">
                      Dokumentasi
                  </a>
                  <ul class="dropdown-menu mt-2" aria-labelledby="navbarDropdown" style="min-width: 240px;">
                     <li>
                        <a class="dropdown-item d-flex align-items-center gap-3 py-2 rounded-3 {{ request()->routeIs('berita.public') ? 'active' : '' }}" href="{{ route('berita.public') }}">
                           <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 dropdown-item-icon">
                              <i class="bi bi-newspaper fs-6"></i>
                           </div>
                           <span class="fw-medium text-dark" style="font-size: 0.9rem;">Berita FAA</span>
                        </a>
                     </li>
                     <li>
                        <a class="dropdown-item d-flex align-items-center gap-3 py-2 rounded-3 {{ request()->routeIs('album.public') ? 'active' : '' }}" href="{{ route('album.public') }}">
                           <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 dropdown-item-icon">
                              <i class="bi bi-images fs-6"></i>
                           </div>
                           <span class="fw-medium text-dark" style="font-size: 0.9rem;">Album Kegiatan</span>
                        </a>
                     </li>
                     <li>
                        <a class="dropdown-item d-flex align-items-center gap-3 py-2 rounded-3 {{ request()->routeIs('infografis.public') ? 'active' : '' }}" href="{{ route('infografis.public') }}">
                           <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 dropdown-item-icon">
                              <i class="bi bi-bar-chart-line fs-6"></i>
                           </div>
                           <span class="fw-medium text-dark" style="font-size: 0.9rem;">Infografis</span>
                        </a>
                     </li>
                     <li>
                        <a class="dropdown-item d-flex align-items-center gap-3 py-2 rounded-3 {{ request()->routeIs('video.public') ? 'active' : '' }}" href="{{ route('video.public') }}">
                           <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 dropdown-item-icon">
                              <i class="bi bi-camera-video fs-6"></i>
                           </div>
                           <span class="fw-medium text-dark" style="font-size: 0.9rem;">Video</span>
                        </a>
                     </li>
                  </ul>
               </li>

               <li><a href="{{ route('produk.makanan') }}" class="{{ request()->routeIs('produk.makanan') ? 'active' : '' }}">Produk Makanan</a></li>
               <li><a href="{{ route('faq.public') }}" class="{{ request()->routeIs('faq.public') ? 'active' : '' }}">FAQ</a></li>
            </ul>

            <div class="nav-actions d-flex align-items-center gap-2 gap-lg-3 flex-grow-1 justify-content-lg-end w-100 w-lg-auto">
               
               <div class="position-relative flex-grow-1" style="max-width: 380px;">
                  <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                  <input type="text" class="form-control rounded-pill border-0 ps-5 py-2 shadow-sm" placeholder="Cari produk..." style="background-color: #f1f5f9; font-size: 0.9rem;">
               </div>

               <a href="{{ route('showroom.3d') }}" class="text-secondary p-2 d-flex align-items-center justify-content-center rounded-circle hover-bg-light" title="VR 3D Showroom">
                  <i class="bi bi-box-seam fs-5" style="color: #64748b;"></i>
               </a>

               <a href="{{ route('cart.index') }}" class="text-secondary p-2 d-flex align-items-center justify-content-center rounded-circle position-relative hover-bg-light me-1" title="Keranjang">
                  <i class="bi bi-cart3 fs-5" style="color: #334155;"></i>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger d-flex align-items-center justify-content-center p-0 shadow" 
                        id="cartCount" 
                        style="width: 18px; height: 18px; font-size: 0.65rem; font-family: sans-serif; margin-top: 6px; margin-left: -6px; background-color: var(--orange) !important;">
                     {{ count(session('cart', [])) }}
                  </span>
               </a>

               <div class="d-flex align-items-center gap-2 ms-lg-2 flex-shrink-0">

                @auth

                    @if(in_array(auth()->user()->role, ['admin_master', 'pemilik']))

                        <a href="{{ url('/dashboard') }}"
                        class="btn btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>

                    @else

                        <div class="dropdown">

                            <a class="btn btn-light border rounded-pill dropdown-toggle d-flex align-items-center gap-2"
                            href="#"
                            id="profileDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                                <i class="bi bi-person-circle fs-5"></i>
                                <span>{{ Auth::user()->name }}</span>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow"
                                aria-labelledby="profileDropdown">

                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                        <i class="bi bi-person me-2"></i>
                                        Profil Saya
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        Alamat Saya
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-bag me-2"></i>
                                        Pesanan Saya
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </li>

                            </ul>

                        </div>

                    @endif

                @else

                    <a href="{{ route('login') }}"
                    class="text-decoration-none fw-semibold px-2 text-secondary">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}"
                    class="btn text-white fw-semibold px-3 py-2 btn-sm shadow-sm"
                    style="background-color: var(--orange); border-radius:10px;">
                        Daftar
                    </a>

                @endauth

            </div>

            </div>
         </div>
      </div>

   </div>
</nav>