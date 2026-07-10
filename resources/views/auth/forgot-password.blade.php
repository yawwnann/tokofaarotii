<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — Toko FAA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; }

        /* PANEL KIRI */
        .panel-left {
            flex: 1; position: relative; display: flex;
            flex-direction: column; align-items: center; justify-content: center;
            padding: 3rem 2.5rem; overflow: hidden;
        }
        .panel-left::before {
            content: ''; position: absolute; inset: 0;
            background: url("{{ asset('template-sarab/img/banner-toko-faa.png') }}") no-repeat center center;
            background-size: cover; filter: blur(6px) brightness(0.55);
            transform: scale(1.05); z-index: 0;
        }
        .panel-left::after {
            content: ''; position: absolute; inset: 0;
            background: rgba(15, 23, 42, 0.7);
            z-index: 1;
        }
        .panel-left-content { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .brand-logo { height: 110px; object-fit: contain; filter: drop-shadow(0 6px 18px rgba(0,0,0,0.45)); margin-bottom: 1.6rem; }
        .brand-name { font-size: 2rem; font-weight: 800; color: #fff; letter-spacing: 3px; margin-bottom: 0.9rem; text-shadow: 0 2px 8px rgba(0,0,0,0.4); }
        .brand-tagline { font-size: 0.95rem; color: rgba(255,255,255,0.82); line-height: 1.75; max-width: 300px; text-shadow: 0 1px 4px rgba(0,0,0,0.3); }
        .brand-badge { margin-top: 2rem; display: flex; gap: 0.65rem; flex-wrap: wrap; justify-content: center; }
        .badge-item { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(6px); color: rgba(255,255,255,0.9); font-size: 0.78rem; font-weight: 600; padding: 0.4rem 0.95rem; border-radius: 50px; }

        /* PANEL KANAN */
        .panel-right { width: 460px; flex-shrink: 0; min-height: 100vh; background: #fff; display: flex; align-items: center; justify-content: center; padding: 2.5rem 2.25rem; box-shadow: -8px 0 40px rgba(0,0,0,0.12); }
        .form-card { width: 100%; max-width: 360px; }

        .logo-wrap { display: flex; justify-content: center; margin-bottom: 1.5rem; }
        .logo-wrap img { height: 70px; object-fit: contain; filter: drop-shadow(0 3px 8px rgba(0,0,0,0.08)); }

        .page-icon-wrap { display: flex; justify-content: center; margin-bottom: 1rem; }
        .page-icon-circle { width: 58px; height: 58px; background: var(--color-primary, #ea580c); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(249,115,22,0.35); font-size: 1.4rem; color: #fff; }

        .form-header { text-align: center; margin-bottom: 1.75rem; }
        .form-header h2 { font-size: 1.55rem; font-weight: 800; color: #004aad; margin-bottom: 0.35rem; }
        .form-header p { font-size: 0.87rem; color: #6b7280; line-height: 1.55; }

        .alert-success { display: flex; align-items: center; gap: 0.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; border-radius: 10px; padding: 0.7rem 1rem; font-size: 0.85rem; margin-bottom: 1.2rem; }

        .input-group { margin-bottom: 1rem; }
        .input-label { display: block; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 0.38rem; letter-spacing: 0.04em; text-transform: uppercase; }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .icon-left { position: absolute; left: 13px; color: #9ca3af; font-size: 0.92rem; pointer-events: none; z-index: 2; }
        .form-input { width: 100%; padding: 0.68rem 1rem 0.68rem 2.6rem; border: 1.5px solid #e5e7eb; border-radius: 11px; font-size: 0.92rem; color: #1f2937; background: #f9fafb; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none; }
        .form-input:focus { border-color: #3b5bdb; background: #fff; box-shadow: 0 0 0 3px rgba(59,91,219,0.11); }
        .form-input.is-invalid { border-color: #f87171; box-shadow: 0 0 0 3px rgba(248,113,113,0.12); }
        .input-error { font-size: 0.77rem; color: #ef4444; margin-top: 0.28rem; display: flex; align-items: center; gap: 0.3rem; }

        .btn-submit { width: 100%; padding: 0.82rem; background: #004aad; color: #fff; font-size: 0.96rem; font-weight: 700; border: none; border-radius: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: transform 0.2s, box-shadow 0.2s; margin-top: 0.5rem; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,74,173,0.28); }
        .btn-submit:active { transform: translateY(0); }

        .back-link-wrap { text-align: center; margin-top: 1.5rem; }
        .back-link { font-size: 0.85rem; font-weight: 700; color: #f97316; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; transition: color 0.2s; }
        .back-link:hover { color: #ea580c; text-decoration: underline; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .panel-left { min-height: 240px; padding: 2rem 1.5rem; }
            .brand-logo { height: 80px; }
            .brand-name { font-size: 1.5rem; }
            .panel-right { width: 100%; min-height: auto; padding: 2rem 1.25rem; box-shadow: none; }
        }
    </style>
</head>
<body>

    <div class="panel-left">
        <div class="panel-left-content">
            <img src="{{ asset('template-sarab/img/logo-toko-faa.png') }}" alt="Logo Toko FAA" class="brand-logo">
            <div class="brand-name">TOKO FAA</div>
            <p class="brand-tagline">Sistem Manajemen Inventori & Penjualan<br>Frozen Food & Bakery Terbaik untuk Bisnis Anda.</p>
            <div class="brand-badge">
                <span class="badge-item"><i class="fas fa-box-open"></i> Inventori</span>
                <span class="badge-item"><i class="fas fa-cash-register"></i> Penjualan</span>
                <span class="badge-item"><i class="fas fa-chart-line"></i> Laporan</span>
            </div>
        </div>
    </div>

    <div class="panel-right">
        <div class="form-card">

            <div class="page-icon-wrap">
                <div class="page-icon-circle"><i class="fas fa-key"></i></div>
            </div>

            <div class="form-header">
                <h2>Lupa Password?</h2>
                <p>Masukkan email Anda dan kami akan mengirimkan tautan untuk reset password.</p>
            </div>

            @if (session('status'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="input-group">
                    <label class="input-label">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope icon-left"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            class="form-input @error('email') is-invalid @enderror"
                            placeholder="emailanda@gmail.com">
                    </div>
                    @error('email')
                        <span class="input-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    <span>Kirim Tautan Reset</span>
                </button>

                <div class="back-link-wrap">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="fas fa-arrow-left"></i> Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>