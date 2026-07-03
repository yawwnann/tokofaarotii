<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko FAA - @yield('title', 'Admin Master')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================================
            ROOT VARIABLES
        ========================================= */
        :root {
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --color-primary:    #f97316; /* orange brand */
            --color-primary-bg: #fff7ed;
            --color-primary-lt: #ffedd5;
            --sidebar-bg:       #ffffff;
            --sidebar-border:   #f1f5f9;
            --body-bg:          #f8fafc;
            --text-main:        #1e293b;
            --text-muted:       #64748b;
            --text-soft:        #94a3b8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
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
            transition: left 0.3s cubic-bezier(.4,0,.2,1);
        }

        /* Brand area */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.125rem 1.25rem;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(249,115,22,.35);
        }
        .sidebar-brand-icon i { color: #fff; font-size: 0.875rem; }
        .sidebar-brand-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
        }
        .sidebar-brand-role {
            font-size: 0.7rem;
            color: var(--text-soft);
            margin-top: 1px;
        }

        /* Scrollable nav */
        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .sidebar-nav-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 99px;
        }

        /* Section label */
        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-soft);
            padding: 1rem 0.5rem 0.35rem;
        }
        .nav-section-label:first-child { padding-top: 0.25rem; }

        /* Nav link */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5625rem 0.75rem;
            border-radius: 0.5rem;
            color: var(--text-muted);
            font-size: 0.825rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 1px;
            transition: background-color 0.18s ease, color 0.18s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }
        .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
        }
        .nav-link i {
            width: 1.125rem;
            text-align: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        /* Active state — orange brand */
        .nav-link.active {
            background-color: var(--color-primary-lt);
            color: var(--color-primary);
            font-weight: 600;
        }
        .nav-link.active i { color: var(--color-primary); }

        /* Collapse toggle */
        .nav-collapse-toggle {
            justify-content: space-between;
        }
        .nav-collapse-toggle .nc-left {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }
        .collapse-arrow {
            font-size: 0.6rem;
            color: #cbd5e1;
            transition: transform 0.22s ease;
            flex-shrink: 0;
        }
        .collapse-arrow.open { transform: rotate(90deg); }

        /* Submenu */
        .submenu {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.28s ease;
        }
        .submenu.open { max-height: 260px; }
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 0.45rem 0.75rem 0.45rem 2.625rem;
            border-radius: 0.5rem;
            font-size: 0.775rem;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 1px;
            transition: background-color 0.15s ease, color 0.15s ease;
            position: relative;
        }
        .submenu-item::before {
            content: '';
            position: absolute;
            left: 1.625rem;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: currentColor;
            opacity: 0.5;
        }
        .submenu-item:hover { background-color: #f1f5f9; color: #475569; }
        .submenu-item.active {
            color: var(--color-primary);
            background-color: var(--color-primary-lt);
            font-weight: 600;
        }
        .submenu-item.active::before { opacity: 1; }

        /* Logout */
        .nav-link-danger { color: #f43f5e !important; }
        .nav-link-danger i { color: #f43f5e !important; }
        .nav-link-danger:hover {
            background-color: #fff1f2 !important;
            color: #e11d48 !important;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 0.875rem 1.25rem;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            background-color: #fafafa;
        }
        .sidebar-footer-inner {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.7rem;
            color: var(--text-soft);
        }
        .sidebar-footer-inner i { color: #cbd5e1; }

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
            background: linear-gradient(135deg, #f97316, #ea580c);
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
            background: linear-gradient(135deg, #f97316, #ea580c);
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

        /* Notification dropdown */
        .notif-panel {
            min-width: 280px;
        }
        .notif-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .notif-panel-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-main);
        }
        .notif-badge {
            background: #f97316;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 99px;
        }
        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f8fafc;
            transition: background 0.15s;
        }
        .notif-item:hover { background: #fafafa; }
        .notif-item-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f97316;
            margin-top: 5px;
            flex-shrink: 0;
        }
        .notif-item-text { font-size: 0.775rem; color: var(--text-main); line-height: 1.4; }
        .notif-item-time { font-size: 0.7rem; color: var(--text-soft); margin-top: 2px; }
        .notif-footer {
            padding: 0.625rem 1rem;
            text-align: center;
        }
        .notif-footer a {
            font-size: 0.775rem;
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
        }
        .notif-footer a:hover { text-decoration: underline; }

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
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-store"></i>
            </div>
            <div>
                <div class="sidebar-brand-name">Toko FAA</div>
                <div class="sidebar-brand-role">Frozen Food & Bakery</div>
            </div>
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
                   class="nav-link {{ request()->requestIs('pegawai*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Kelola Pegawai</span>
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

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link nav-link-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </button>
                </form>

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

                {{-- Notification Bell --}}
                <div class="topbar-dropdown">
                    <button class="btn-icon btn-bell" id="notifToggle" aria-label="Notifikasi">
                        <i class="fas fa-bell" style="font-size:0.8rem;"></i>
                        <span class="badge-dot"></span>
                    </button>
                    <div class="dropdown-panel notif-panel" id="notifPanel">
                        <div class="notif-panel-header">
                            <span class="notif-panel-title">Notifikasi</span>
                            <span class="notif-badge">3</span>
                        </div>
                        <div class="notif-item">
                            <div class="notif-item-dot"></div>
                            <div>
                                <div class="notif-item-text">Stok produk hampir habis</div>
                                <div class="notif-item-time">5 menit yang lalu</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-item-dot"></div>
                            <div>
                                <div class="notif-item-text">Penjualan baru masuk #00123</div>
                                <div class="notif-item-time">30 menit yang lalu</div>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-item-dot"></div>
                            <div>
                                <div class="notif-item-text">Laporan bulanan siap diunduh</div>
                                <div class="notif-item-time">1 jam yang lalu</div>
                            </div>
                        </div>
                        <div class="notif-footer">
                            <a href="#">Lihat semua notifikasi</a>
                        </div>
                    </div>
                </div>

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
                            <a href="{{ route('settings.index') }}" class="dropdown-panel-item">
                                <i class="fas fa-user-circle"></i>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="dropdown-panel-item">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                            <div style="border-top:1px solid #f1f5f9;margin:0.25rem 0;"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-panel-item" style="border:none;background:none;width:100%;cursor:pointer;color:#f43f5e;">
                                    <i class="fas fa-sign-out-alt" style="color:#f43f5e;"></i>
                                    <span>Keluar</span>
                                </button>
                            </form>
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

        setupDropdown('notifToggle', 'notifPanel');
        setupDropdown('avatarToggle', 'avatarPanel');

        // Close dropdowns on outside click
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-panel.open')
                    .forEach(p => p.classList.remove('open'));
        });
    </script>

    @stack('scripts')
</body>
</html>