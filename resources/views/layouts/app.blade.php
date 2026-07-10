<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko FAA - @yield('title', 'Admin Master')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================================
            ROOT VARIABLES
        ========================================= */
        :root {
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --color-primary:    #f97316; /* orange brand */
            --color-primary-bg: #fff7ed;
            --color-primary-lt: #ffedd5;
            --sidebar-bg:       #ffffff;
            --sidebar-border:   rgba(0, 0, 0, 0.04);
            --body-bg:          #f4f7fe; /* Slightly cooler grey for premium feel */
            --text-main:        #1e293b;
            --text-muted:       #64748b;
            --text-soft:        #94a3b8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
        }

        /* =========================================
            OVERLAY (mobile)
        ========================================= */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(2px);
            z-index: 25;
        }
        .overlay.active { display: block; }

        /* =========================================
            SIDEBAR
        ========================================= */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 30;
            display: flex;
            flex-direction: column;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
            transition: left 0.3s cubic-bezier(.4,0,.2,1);
        }

        /* Brand area */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 1.5rem;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background: var(--color-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(249,115,22,.35);
        }
        .sidebar-brand-icon i { color: #fff; font-size: 1rem; }
        .sidebar-brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .sidebar-brand-role {
            font-size: 0.75rem;
            color: var(--text-soft);
            margin-top: 2px;
            font-weight: 500;
        }

        /* Scrollable nav */
        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .sidebar-nav-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 99px;
        }

        /* Section label */
        .nav-section-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-soft);
            padding: 1.25rem 1.5rem 0.5rem;
        }
        .nav-section-label:first-child { padding-top: 0.5rem; }

        /* Nav link */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            margin: 0.2rem 1rem;
            border-radius: 0.75rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            background: transparent;
            width: calc(100% - 2rem);
            text-align: left;
        }
        .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
            transform: translateX(4px);
        }
        .nav-link i {
            width: 1.25rem;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
            transition: color 0.2s ease;
        }
        /* Active state — premium pill */
        .nav-link.active {
            background-color: var(--color-primary-bg);
            color: var(--color-primary);
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--color-primary);
        }
        .nav-link.active i { color: var(--color-primary); }

        /* Collapse toggle */
        .nav-collapse-toggle {
            justify-content: space-between;
            user-select: none;
        }
        .nav-collapse-toggle:hover {
            background-color: #eef2ff !important;
            color: #4f46e5 !important;
            transform: translateX(4px);
        }
        .nav-collapse-toggle:hover .collapse-arrow {
            color: #4f46e5;
        }
        .nav-collapse-toggle .nc-left {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            pointer-events: none;
        }
        .nav-collapse-toggle .collapse-arrow {
            pointer-events: none;
        }
        .collapse-arrow {
            font-size: 0.75rem;
            color: #94a3b8;
            transition: transform 0.22s ease, color 0.2s ease;
            flex-shrink: 0;
        }
        .collapse-arrow.open { transform: rotate(90deg); }
        .nav-collapse-toggle.active .collapse-arrow { color: #f97316; }

        /* Submenu */
        .submenu {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.35s cubic-bezier(.4,0,.2,1);
        }
        .submenu.open { max-height: 300px; }
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem 0.5rem 3rem;
            margin: 0.15rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }
        .submenu-item::before {
            content: '';
            position: absolute;
            left: 1.75rem;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
            opacity: 0.4;
            transition: opacity 0.2s;
        }
        .submenu-item:hover { 
            background-color: #f8fafc; 
            color: #475569; 
            transform: translateX(4px);
        }
        .submenu-item.active {
            color: var(--color-primary);
            background-color: #fff;
            font-weight: 600;
        }
        .submenu-item.active::before { opacity: 1; transform: scale(1.2); }

        /* Logout */
        .nav-link-danger { color: #f43f5e !important; }
        .nav-link-danger i { color: #f43f5e !important; }
        .nav-link-danger:hover {
            background-color: #fff1f2 !important;
            color: #e11d48 !important;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            background-color: #fff;
        }
        .sidebar-footer-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-soft);
            background: #f8fafc;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
        }
        .sidebar-footer-inner i { color: #94a3b8; font-size: 1.1rem; }

        /* =========================================
            TOPBAR
        ========================================= */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #fff;
            border-bottom: 1px solid var(--sidebar-border);
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            gap: 1rem;
        }

        .topbar-left { display: flex; align-items: center; gap: 0.875rem; }
        .topbar-right { display: flex; align-items: center; gap: 0.5rem; }

        /* Hamburger */
        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
            flex-shrink: 0;
        }
        .btn-icon:hover { background: #f1f5f9; color: var(--text-main); }
        .btn-icon i { font-size: 0.875rem; }

        /* Page title in topbar */
        .topbar-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Breadcrumb (optional slot) */
        .topbar-date {
            font-size: 0.75rem;
            color: var(--text-soft);
        }

        /* Bell */
        .btn-bell {
            position: relative;
        }
        .btn-bell .badge-dot {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 8px;
            height: 8px;
            background: #f43f5e;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* Avatar */
        .topbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            flex-shrink: 0;
            border: 2px solid var(--color-primary-lt);
        }

        /* Dropdown */
        .topbar-dropdown {
            position: relative;
        }
        .dropdown-panel {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 8px 32px rgba(15,23,42,.12);
            min-width: 200px;
            overflow: hidden;
            z-index: 50;
        }
        .dropdown-panel.open { display: block; }
        .dropdown-panel-header {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .dropdown-panel-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .dropdown-panel-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .dropdown-panel-role {
            font-size: 0.7rem;
            color: var(--text-soft);
            margin-top: 1px;
        }
        .dropdown-panel-body { padding: 0.5rem; }
        .dropdown-panel-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.625rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: background 0.15s;
        }
        .dropdown-panel-item:hover { background: #f8fafc; color: var(--text-main); }
        .dropdown-panel-item i { width: 1rem; text-align: center; font-size: 0.75rem; }

        /* =========================================
            MAIN CONTENT
        ========================================= */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(.4,0,.2,1);
        }
        .main-content main {
            flex: 1;
            padding: 1.5rem;
        }

        /* =========================================
            RESPONSIVE
        ========================================= */
        @media (max-width: 1024px) {
            .sidebar { left: calc(-1 * var(--sidebar-width)); }
            .sidebar.open {
                left: 0;
                box-shadow: 8px 0 32px rgba(15,23,42,.12);
            }
            .main-content { margin-left: 0; }
            .topbar-date { display: none; }
        }

        /* Show toggle btn only on mobile */
        .sidebar-toggle-btn { display: none; }
        @media (max-width: 1024px) {
            .sidebar-toggle-btn { display: flex; }
        }
    </style>
</head>
<body>

    {{-- Overlay (mobile) --}}
    <div class="overlay" id="overlay"></div>

    {{-- ================================================ --}}
    {{-- SIDEBAR                                          --}}
    {{-- ================================================ --}}
    <aside class="sidebar" id="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand" style="justify-content: center; padding: 1.25rem;">
            <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Logo Toko FAA" style="max-height: 45px; width: auto; max-width: 100%; object-fit: contain;">
        </div>

        {{-- Nav --}}
        <div class="sidebar-nav-scroll">
            <nav>

                <p class="nav-section-label">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Beranda</span>
                </a>

                @if(in_array(auth()->user()?->role, ['admin_master', 'kasir']))
                <a href="{{ route('products.index') }}"
                   class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produk</span>
                </a>

                <a href="{{ route('categories.index') }}"
                   class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Kategori</span>
                </a>

                <a href="{{ route('stock-entries.index') }}"
                   class="nav-link {{ request()->routeIs('stock-entries.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Stok</span>
                </a>

                <a href="{{ route('sales.index') }}"
                   class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Penjualan Offline</span>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-truck-fast"></i>
                    <span>Pesanan Online</span>
                </a>
                @endif

                @if(in_array(auth()->user()?->role, ['admin_master', 'pemilik']))
                <a href="{{ route('reports.index') }}"
                   class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
                @endif

                @if(auth()->user()?->role == 'admin_master')
                <p class="nav-section-label">Manajemen Sistem</p>

                <a href="{{ route('kelola-user.index') }}"
                   class="nav-link {{ request()->routeIs('kelola-user.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Kelola User</span>
                </a>

                <a href="{{ route('pegawai.index') }}"                   
                   class="nav-link {{ request()->is('pegawai*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola Pegawai</span>
                </a>

                <a href="{{ route('stores.index') }}"
                   class="nav-link {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    <span>Kelola Toko</span>
                </a>

                <a href="{{ route('shipping-zones.index') }}"
                   class="nav-link {{ request()->routeIs('shipping-zones.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Tarif Ongkir</span>
                </a>
                @endif

                @if(in_array(auth()->user()?->role, ['admin_master', 'pemilik']))
                <p class="nav-section-label">Manajemen Konten</p>

                {{-- Dokumentasi Collapsible --}}
                <button type="button"
                        class="nav-link nav-collapse-toggle {{ request()->routeIs('dokumentasi.*') ? 'active' : '' }}"
                        onclick="toggleCollapse('collapseDokumentasi', this)">
                    <div class="nc-left">
                        <i class="fas fa-cloud"></i>
                        <span>Dokumentasi</span>
                    </div>
                    <i class="fas fa-chevron-right collapse-arrow {{ request()->routeIs('dokumentasi.*') ? 'open' : '' }}"></i>
                </button>

                <div class="submenu {{ request()->routeIs('dokumentasi.*') ? 'open' : '' }}" id="collapseDokumentasi">
                    <a href="{{ route('dokumentasi.album') }}"
                       class="submenu-item {{ request()->routeIs('dokumentasi.album') ? 'active' : '' }}">
                        Album Kegiatan
                    </a>
                    <a href="{{ route('dokumentasi.infografis') }}"
                       class="submenu-item {{ request()->routeIs('dokumentasi.infografis') ? 'active' : '' }}">
                        Infografis
                    </a>
                    <a href="{{ route('dokumentasi.video') }}"
                       class="submenu-item {{ request()->routeIs('dokumentasi.video') ? 'active' : '' }}">
                        Video
                    </a>
                </div>

                <a href="{{ route('berita.index') }}"
                   class="nav-link {{ request()->routeIs('berita.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    <span>Kelola Berita</span>
                </a>

                <a href="{{ route('faq.index') }}"
                   class="nav-link {{ request()->routeIs('faq.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ</span>
                </a>
                @endif

                <p class="nav-section-label">Sistem</p>

                <a href="{{ route('settings.index') }}"
                   class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>

                <form id="logoutFormSidebar" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
                <button type="button" class="nav-link nav-link-danger" style="width: 100%; border: none; text-align: left; cursor: pointer; background: transparent;" onclick="confirmLogout('logoutFormSidebar')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar</span>
                </button>

            </nav>
        </div>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-footer-inner">
                <i class="fas fa-shield-alt"></i>
                <span>vTKFAA &nbsp;·&nbsp; v1.0</span>
            </div>
        </div>

    </aside>

    {{-- ================================================ --}}
    {{-- MAIN CONTENT                                     --}}
    {{-- ================================================ --}}
    <div class="main-content" id="mainContent">

        {{-- TOPBAR --}}
        <header class="topbar">
            {{-- Left --}}
            <div class="topbar-left">
                {{-- Hamburger (mobile only) --}}
                <button class="btn-icon sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                {{-- Sidebar collapse (desktop) --}}
                <button class="btn-icon" id="sidebarCollapseBtn" aria-label="Collapse sidebar" style="display:flex;">
                    <i class="fas fa-table-columns"></i>
                </button>

                <div>
                    <div class="topbar-title">@yield('title', 'Panel Kendali')</div>
                </div>
            </div>

            {{-- Right --}}
            <div class="topbar-right">
                {{-- Date --}}
                <span class="topbar-date">{{ now()->translatedFormat('l, d F Y') }}</span>

                {{-- Avatar / Profile Dropdown --}}
                <div class="topbar-dropdown">
                    <div class="topbar-avatar" id="avatarToggle">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="dropdown-panel" id="avatarPanel">
                        <div class="dropdown-panel-header">
                            <div class="dropdown-panel-avatar">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div class="dropdown-panel-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                                {{-- FIX: Menampilkan nama role secara dinamis dan rapi --}}
                                <div class="dropdown-panel-role">
                                    @php
                                        $roles = [
                                            'admin_master' => 'Admin Master',
                                            'pemilik' => 'Pemilik Toko',
                                            'kasir' => 'Kasir'
                                        ];
                                        $userRole = Auth::user()->role ?? 'customer';
                                    @endphp
                                    {{ $roles[$userRole] ?? ucwords(str_replace('_', ' ', $userRole)) }}
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-panel-body">
                            <a href="{{ route('profile.edit') }}" class="dropdown-panel-item">
                                <i class="fas fa-user-circle"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="dropdown-panel-item">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                            <div style="border-top:1px solid #f1f5f9;margin:0.25rem 0;"></div>
                            <form id="logoutFormDropdown" method="POST" action="{{ route('logout') }}" style="display: none;">
                                @csrf
                            </form>
                            <button type="button" class="dropdown-panel-item" style="border:none;background:none;width:100%;cursor:pointer;color:#f43f5e;text-align:left;" onclick="confirmLogout('logoutFormDropdown')">
                                <i class="fas fa-sign-out-alt" style="color:#f43f5e;"></i>
                                <span>Keluar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main>
            @yield('content')
        </main>

    </div>

    {{-- Logout Confirmation Modal --}}
    <div class="modal-overlay" id="logoutModalOverlay">
        <div class="modal-box">
            <div class="modal-icon text-red-500">
                <i class="fas fa-sign-out-alt fa-2x"></i>
            </div>
            <h3 class="modal-title">Konfirmasi Keluar</h3>
            <p class="modal-text">Apakah Anda yakin ingin keluar dari sistem Toko FAA?</p>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
                <button type="button" class="btn-confirm-danger" onclick="submitLogout()">Ya, Keluar</button>
            </div>
        </div>
    </div>

        <style>
        #logoutModalOverlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden;
            transition: all 0.3s ease;
        }
        #logoutModalOverlay.show { opacity: 1; visibility: visible; }
        .modal-box {
            background: #ffffff;
            border-radius: 1.25rem;
            width: 90%; max-width: 400px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .modal-overlay.show .modal-box { transform: translateY(0) scale(1); }
        .modal-icon { width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; }
        .modal-title { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0 0 0.5rem; }
        .modal-text { font-size: 0.9rem; color: #475569; margin: 0 0 1.5rem; line-height: 1.5; }
        .modal-actions { display: flex; gap: 1rem; }
        .modal-actions button { flex: 1; padding: 0.75rem; border-radius: 0.75rem; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
        .btn-cancel { background: #f1f5f9; color: #475569; }
        .btn-cancel:hover { background: #e2e8f0; }
        .btn-confirm-danger { background: #ef4444; color: #ffffff; }
        .btn-confirm-danger:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); }
    </style>

    <script>
        // ── Sidebar mobile toggle ───────────────────────────
        const sidebar      = document.getElementById('sidebar');
        const overlay      = document.getElementById('overlay');
        const toggleBtn    = document.getElementById('sidebarToggle');
        const collapseBtn  = document.getElementById('sidebarCollapseBtn');
        const mainContent  = document.getElementById('mainContent');

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });

        // ── Sidebar desktop collapse ────────────────────────
        let sidebarCollapsed = false;
        collapseBtn?.addEventListener('click', () => {
            if (window.innerWidth < 1024) return; // mobile handled by toggleBtn
            sidebarCollapsed = !sidebarCollapsed;
            if (sidebarCollapsed) {
                sidebar.style.left = 'calc(-1 * var(--sidebar-width))';
                mainContent.style.marginLeft = '0';
            } else {
                sidebar.style.left = '0';
                mainContent.style.marginLeft = 'var(--sidebar-width)';
            }
        });

        // ── Collapse submenu ────────────────────────────────
        function toggleCollapse(id, btn) {
            const menu  = document.getElementById(id);
            const arrow = btn.querySelector('.collapse-arrow');
            menu.classList.toggle('open');
            arrow.classList.toggle('open');
        }

        // ── Topbar dropdowns ────────────────────────────────
        function setupDropdown(triggerId, panelId) {
            const trigger = document.getElementById(triggerId);
            const panel   = document.getElementById(panelId);
            if (!trigger || !panel) return;
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                // Close all other panels first
                document.querySelectorAll('.dropdown-panel.open').forEach(p => {
                    if (p !== panel) p.classList.remove('open');
                });
                panel.classList.toggle('open');
            });
        }


        setupDropdown('avatarToggle', 'avatarPanel');

        // Close dropdowns on outside click
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-panel.open')
                    .forEach(p => p.classList.remove('open'));
        });

        // ── Logout Modal Logic ──────────────────────────────
        let currentLogoutFormId = null;
        function confirmLogout(formId) {
            currentLogoutFormId = formId;
            document.getElementById('logoutModalOverlay').classList.add('show');
            // Close dropdowns if open
            document.querySelectorAll('.dropdown-panel.open').forEach(p => p.classList.remove('open'));
        }
        function closeLogoutModal() {
            document.getElementById('logoutModalOverlay').classList.remove('show');
            currentLogoutFormId = null;
        }
        function submitLogout() {
            if (currentLogoutFormId) {
                document.getElementById(currentLogoutFormId).submit();
            }
        }
    </script>

    @stack('scripts')
</body>
</html>