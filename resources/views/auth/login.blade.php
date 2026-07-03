<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Toko FAA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
        }

        /* =============================================
           PANEL KIRI — BACKGROUND BLUR + LOGO FAA
        ============================================= */
        .panel-left {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            overflow: hidden;
        }

        /* Layer 1: background image */
        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("{{ asset('template-sarab/img/banner-toko-faa.png') }}") no-repeat center center;
            background-size: cover;
            filter: blur(6px) brightness(0.55);
            transform: scale(1.05); /* cegah tepi putih efek blur */
            z-index: 0;
        }

        /* Layer 2: overlay gelap supaya teks terbaca */
        .panel-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(10,30,90,0.55) 0%, rgba(30,60,180,0.45) 100%);
            z-index: 1;
        }

        /* Semua konten di atas kedua layer */
        .panel-left-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Logo FAA */
        .brand-logo {
            height: 110px;
            object-fit: contain;
            filter: drop-shadow(0 6px 18px rgba(0,0,0,0.45));
            margin-bottom: 1.6rem;
        }

        .brand-name {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 3px;
            margin-bottom: 0.9rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }

        .brand-tagline {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.82);
            line-height: 1.75;
            max-width: 300px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        .brand-badge {
            margin-top: 2rem;
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .badge-item {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(6px);
            color: rgba(255,255,255,0.9);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.4rem 0.95rem;
            border-radius: 50px;
        }

        /* =============================================
           PANEL KANAN — FORM LOGIN
        ============================================= */
        .panel-right {
            width: 460px;
            flex-shrink: 0;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2.25rem;
            box-shadow: -8px 0 40px rgba(0,0,0,0.12);
        }

        .form-card {
            width: 100%;
            max-width: 360px;
        }

        /* Logo di form */
        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .logo-wrap img {
            height: 70px;
            object-fit: contain;
            filter: drop-shadow(0 3px 8px rgba(0,0,0,0.08));
        }

        /* Header */
        .form-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .form-header h2 {
            font-size: 1.55rem;
            font-weight: 800;
            color: #004aad;
            margin-bottom: 0.35rem;
        }
        .form-header p {
            font-size: 0.87rem;
            color: #6b7280;
            line-height: 1.55;
        }

        /* Alert status */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.2rem;
        }

        /* Input group */
        .input-group {
            margin-bottom: 1rem;
        }
        .input-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.38rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .icon-left {
            position: absolute;
            left: 13px;
            color: #9ca3af;
            font-size: 0.92rem;
            pointer-events: none;
            z-index: 2;
        }
        .btn-eye {
            position: absolute;
            right: 13px;
            color: #9ca3af;
            font-size: 0.92rem;
            cursor: pointer;
            z-index: 2;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
        }
        .btn-eye:hover { color: #004aad; }

        .form-input {
            width: 100%;
            padding: 0.68rem 2.6rem; /* seragam kiri-kanan untuk ikon */
            border: 1.5px solid #e5e7eb;
            border-radius: 11px;
            font-size: 0.92rem;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #3b5bdb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59,91,219,0.11);
        }

        /* Error */
        .input-error {
            font-size: 0.77rem;
            color: #ef4444;
            margin-top: 0.28rem;
            display: block;
        }

        /* Remember & Forgot */
        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.35rem;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.84rem;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: #3b5bdb;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 0.84rem;
            font-weight: 700;
            color: #f97316;
            text-decoration: none;
        }
        .forgot-link:hover { color: #ea580c; text-decoration: underline; }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 0.82rem;
            background: linear-gradient(135deg, #004aad, #1d4ed8);
            color: #fff;
            font-size: 0.96rem;
            font-weight: 700;
            border: none;
            border-radius: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,74,173,0.28);
        }
        .btn-submit:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin: 1.3rem 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }
        .divider span {
            font-size: 0.74rem;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        /* Footer */
        .form-footer {
            text-align: center;
            font-size: 0.86rem;
            color: #6b7280;
            font-weight: 600;
        }
        .register-link {
            color: #f97316;
            font-weight: 800;
            text-decoration: none;
        }
        .register-link:hover { color: #ea580c; text-decoration: underline; }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .panel-left { min-height: 240px; padding: 2rem 1.5rem; }
            .brand-logo { height: 80px; }
            .brand-name { font-size: 1.5rem; }
            .panel-right {
                width: 100%;
                min-height: auto;
                padding: 2rem 1.25rem;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    {{-- ======================== PANEL KIRI ======================== --}}
    <div class="panel-left">
        <div class="panel-left-content">
            {{-- Logo FAA asli sebagai visual utama --}}
            <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Logo Toko FAA" class="brand-logo">

            <div class="brand-name">TOKO FAA</div>
            <p class="brand-tagline">
                Sistem E-Commerce dan Profil Toko FAA<br>
                Frozen Food & Bakery.
            </p>
            <div class="brand-badge">
                <span class="badge-item"><i class="fas fa-shopping-cart"></i> Belanja Produk Makanan Kami</span>
                <span class="badge-item"><i class="fas fa-robot"></i> Chatbot AI</span>
                <span class="badge-item"><i class="fas fa-cubes"></i> VR 3D Showroom</span>
            </div>
        </div>
    </div>

    {{-- ======================== PANEL KANAN ======================== --}}
    <div class="panel-right">
        <div class="form-card">

            {{-- Logo kecil di atas form --}}
            <div class="logo-wrap">
                <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Logo FAA">
            </div>

            <div class="form-header">
                <h2>Selamat Datang!</h2>
                <p>Silakan masuk ke akun Anda untuk melanjutkan transaksi.</p>
            </div>

            @if (session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="input-group">
                    <label class="input-label">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope icon-left"></i>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required autofocus
                            autocomplete="email"
                            class="form-input"
                            placeholder="emailanda@gmail.com"
                        >
                    </div>
                    @error('email')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password — SATU ikon mata, tidak ada duplikat --}}
                <div class="input-group">
                    <label class="input-label">Kata Sandi</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock icon-left"></i>
                        <input
                            type="password"
                            id="passwordInput"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="form-input"
                            placeholder="masukkan kata sandi Anda"
                        >
                        <button type="button" class="btn-eye" onclick="togglePassword()" aria-label="Tampilkan/sembunyikan kata sandi">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="input-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember & Lupa Sandi --}}
                <div class="form-meta">
                    <label class="remember-label">
                        <input type="checkbox" name="remember">
                        Ingat Saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa Kata Sandi?</a>
                    @endif
                </div>

                <button type="submit" class="btn-submit">
                    <span>Masuk Sekarang</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="divider"><span>Atau</span></div>

                <p class="form-footer">
                    Belum punya akun? <a href="{{ route('register') }}" class="register-link">Daftar Gratis</a>
                </p>

            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('eyeIcon');
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye',      !hidden);
            icon.classList.toggle('fa-eye-slash', hidden);
        }
    </script>
</body>
</html>