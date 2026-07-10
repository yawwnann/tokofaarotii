<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Toko Faa') }} - Authentication</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary: #6366f1;
                --primary-dark: #4f46e5;
                --accent: #f97316;
                --bg-body: #f8fafc;
                --text-main: #1e293b;
                --text-soft: #64748b;
            }

            body {
                font-family: 'Poppins', sans-serif;
                background-color: var(--bg-body);
                color: var(--text-main);
                margin: 0;
                display: flex;
                min-height: 100vh;
            }

            .auth-layout {
                display: grid;
                grid-template-columns: 1fr 550px;
                width: 100%;
                min-height: 100vh;
            }

            /* ── Visual Side ── */
            .auth-visual {
                background: var(--primary);
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: #fff;
                padding: 4rem;
                overflow: hidden;
            }

            .auth-visual::before {
                content: '';
                position: absolute;
                top: -10%;
                right: -10%;
                width: 40%;
                height: 40%;
                background: rgba(255,255,255,0.1);
                border-radius: 50%;
                filter: blur(60px);
            }

            .auth-visual::after {
                content: '';
                position: absolute;
                bottom: -10%;
                left: -10%;
                width: 40%;
                height: 40%;
                background: rgba(249,115,22,0.15);
                border-radius: 50%;
                filter: blur(60px);
            }

            .visual-content {
                position: relative;
                z-index: 10;
                text-align: center;
                max-width: 500px;
            }

            .brand-big {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
                margin-bottom: 3rem;
            }

            .brand-logo-circle {
                width: 80px;
                height: 80px;
                background: #fff;
                border-radius: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 15px 30px rgba(0,0,0,0.15);
                transform: rotate(-10deg);
            }

            .brand-logo-circle i {
                font-size: 2.5rem;
                color: var(--accent);
            }

            .brand-name-big {
                font-size: 2.5rem;
                font-weight: 800;
                letter-spacing: -0.02em;
                margin: 0;
            }

            .visual-tagline {
                font-size: 1.1rem;
                font-weight: 500;
                opacity: 0.9;
                line-height: 1.6;
            }

            /* ── Form Side ── */
            .auth-form-side {
                background: #fff;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 3rem;
                box-shadow: -10px 0 30px rgba(0,0,0,0.02);
            }

            .form-container {
                width: 100%;
                max-width: 400px;
            }

            .form-header {
                margin-bottom: 2.5rem;
                text-align: center;
            }

            .form-title {
                font-size: 1.75rem;
                font-weight: 800;
                color: var(--text-main);
                margin: 0 0 0.5rem;
            }

            .form-subtitle {
                font-size: 0.95rem;
                color: var(--text-soft);
                margin: 0;
            }

            /* ── Form Elements (Reset & Custom) ── */
            .auth-input-group {
                margin-bottom: 1.5rem;
            }

            .auth-label {
                display: block;
                font-size: 0.85rem;
                font-weight: 700;
                color: #475569;
                margin-bottom: 0.5rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .auth-input-wrap {
                position: relative;
            }

            .auth-input-icon {
                position: absolute;
                left: 1.25rem;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                font-size: 0.9rem;
            }

            .auth-input {
                width: 100%;
                padding: 0.875rem 1.25rem 0.875rem 3rem;
                background: #f8fafc;
                border: 2px solid #f1f5f9;
                border-radius: 1rem;
                font-size: 0.95rem;
                font-weight: 500;
                color: var(--text-main);
                transition: all 0.2s;
                box-sizing: border-box;
            }

            .auth-input:focus {
                background: #fff;
                border-color: var(--primary);
                outline: none;
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            }

            .auth-error {
                color: #ef4444;
                font-size: 0.8rem;
                font-weight: 600;
                margin-top: 0.5rem;
                display: block;
            }

            .auth-footer-links {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1rem;
            }

            .auth-link {
                font-size: 0.875rem;
                font-weight: 600;
                color: var(--primary);
                text-decoration: none;
                transition: color 0.2s;
            }

            .auth-link:hover {
                color: var(--primary-dark);
                text-decoration: underline;
            }

            .btn-auth-submit {
                width: 100%;
                padding: 1rem;
                background: var(--primary);
                color: #fff;
                border: none;
                border-radius: 1rem;
                font-size: 1rem;
                font-weight: 700;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                transition: all 0.2s;
                margin-top: 2rem;
            }

            .btn-auth-submit:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
            }

            .btn-auth-submit:active {
                transform: translateY(0);
            }

            .divider {
                display: flex;
                align-items: center;
                margin: 2rem 0;
                color: #cbd5e1;
            }

            .divider::before, .divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: #e2e8f0;
            }

            .divider span {
                padding: 0 1rem;
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
            }

            @media (max-width: 1024px) {
                .auth-layout {
                    grid-template-columns: 1fr;
                }
                .auth-visual {
                    display: none;
                }
                .auth-form-side {
                    padding: 2rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="auth-layout">
            {{-- Visual Side --}}
            <div class="auth-visual">
                <div class="visual-content">
                    <div class="brand-big">
                        <div class="brand-logo-circle">
                            <i class="fas fa-bread-slice"></i>
                        </div>
                        <h1 class="brand-name-big">TOKO FAA</h1>
                    </div>
                    <p class="visual-tagline">
                        Sistem Manajemen Inventori & Penjualan Frozen Food & Bakery Terbaik untuk Bisnis Anda.
                    </p>
                </div>
            </div>

            {{-- Form Side --}}
            <div class="auth-form-side">
                <div class="form-container">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
