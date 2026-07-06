<link rel="stylesheet" href="{{ asset('template-sarab/css/navbar.css') }}">

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
               
               <li class="nav-item dropdown-hover" style="cursor: pointer;">
                  <a class="nav-link dropdown-toggle-custom" href="#" id="navbarDropdown">
                      Dokumentasi <i class="fas fa-chevron-down dropdown-arrow"></i>
                  </a>
                  <ul class="dropdown-menu-custom" style="min-width: 240px;">
                     <li>
                        <a class="dropdown-item-custom {{ request()->routeIs('berita.public') ? 'active' : '' }}" href="{{ route('berita.public') }}">
                           <div class="dropdown-item-icon">
                              <i class="bi bi-newspaper fs-6"></i>
                           </div>
                           <span>Berita FAA</span>
                        </a>
                     </li>
                     <li>
                        <a class="dropdown-item-custom {{ request()->routeIs('album.public') ? 'active' : '' }}" href="{{ route('album.public') }}">
                           <div class="dropdown-item-icon">
                              <i class="bi bi-images fs-6"></i>
                           </div>
                           <span>Album Kegiatan</span>
                        </a>
                     </li>
                     <li>
                        <a class="dropdown-item-custom {{ request()->routeIs('infografis.public') ? 'active' : '' }}" href="{{ route('infografis.public') }}">
                           <div class="dropdown-item-icon">
                              <i class="bi bi-bar-chart-line fs-6"></i>
                           </div>
                           <span>Infografis</span>
                        </a>
                     </li>
                     <li>
                        <a class="dropdown-item-custom {{ request()->routeIs('video.public') ? 'active' : '' }}" href="{{ route('video.public') }}">
                           <div class="dropdown-item-icon">
                              <i class="bi bi-camera-video fs-6"></i>
                           </div>
                           <span>Video</span>
                        </a>
                     </li>
                  </ul>
               </li>

               <li><a href="{{ route('produk.makanan') }}" class="{{ request()->routeIs('produk.makanan') ? 'active' : '' }}">Produk Makanan</a></li>
               <li><a href="{{ route('faq.public') }}" class="{{ request()->routeIs('faq.public') ? 'active' : '' }}">FAQ</a></li>
            </ul>

            <div class="nav-actions d-flex align-items-center gap-2 gap-lg-3 flex-grow-1 justify-content-lg-end w-100 w-lg-auto">
               
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

                    @if(in_array(auth()->user()->role, ['admin_master', 'pemilik', 'kasir']))

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
                                    <a class="dropdown-item"
                                    href="{{ route('customer.address') }}">
                                        <i class="bi bi-geo-alt me-2"></i>
                                        Alamat Saya
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.orders.index') }}">
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