@extends('layouts.app')

@section('title', 'Ubah Password')
@section('subtitle', 'Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan')

@section('content')

{{-- ── ALERT NOTIFIKASI ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@if($errors->any())
<div class="alert-banner" style="background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;">
    <div class="alert-inner">
        <i class="fas fa-exclamation-circle" style="margin-top: 3px;"></i>
        <ul style="list-style: disc; padding-left: 1rem; margin: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="password-container">
    <div class="password-card">
        <div class="password-header">
            <div class="password-icon">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h3 class="password-title">Ubah Kata Sandi</h3>
                <p class="password-desc">Gunakan kata sandi yang kuat dan unik untuk melindungi akun Anda dari akses yang tidak sah.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.password.update') }}">
            @csrf
            @method('PUT')

            <div class="section-card">
                <div class="form-group">
                    <label class="form-label">Password Saat Ini</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="current_password" class="form-input icon-padded" 
                               placeholder="Masukkan password Anda saat ini" required autocomplete="current-password">
                    </div>
                </div>

                <div class="password-divider">
                    <span>Buat Password Baru</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock-open"></i>
                            <input type="password" name="password" class="form-input icon-padded" 
                                   placeholder="Minimal 8 karakter" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-with-icon">
                            <i class="fas fa-check-circle"></i>
                            <input type="password" name="password_confirmation" class="form-input icon-padded" 
                                   placeholder="Ulangi password baru" required autocomplete="new-password">
                        </div>
                    </div>
                </div>

                {{-- Password Strength Tips --}}
                <div class="password-tips">
                    <div class="password-tips-header">
                        <i class="fas fa-shield-alt"></i>
                        <span>Tips Keamanan Kata Sandi</span>
                    </div>
                    <ul class="password-tips-list">
                        <li><i class="fas fa-check-circle"></i> Gunakan minimal 8 karakter</li>
                        <li><i class="fas fa-check-circle"></i> Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                        <li><i class="fas fa-check-circle"></i> Jangan gunakan kata sandi yang sama dengan akun lain</li>
                        <li><i class="fas fa-check-circle"></i> Ganti kata sandi secara berkala (3-6 bulan sekali)</li>
                    </ul>
                </div>
            </div>

            <div class="form-actions-bar">
                <a href="{{ route('settings.index') }}" class="btn-cancel-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Pengaturan
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .password-container { max-width: 700px; margin: 0 auto; }
    .password-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }

    .password-header { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; }
    .password-icon { width: 48px; height: 48px; border-radius: 14px; background: #fef2f2; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .password-icon i { font-size: 1.25rem; color: #ef4444; }
    .password-title { font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 .25rem 0; }
    .password-desc { font-size: .825rem; color: #64748b; margin: 0; }

    .section-card { display: flex; flex-direction: column; gap: 1.25rem; }
    .form-group { display: flex; flex-direction: column; gap: .5rem; }
    .form-label { font-size: .8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .05em; }
    .form-input { width: 100%; background: #fff; border: 1px solid #cbd5e1; border-radius: .5rem; padding: .625rem .875rem; font-size: .9rem; color: #1e293b; transition: all .2s; box-sizing: border-box; }
    .form-input:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }
    .input-with-icon { position: relative; width: 100%; }
    .input-with-icon i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .9rem; z-index: 1; }
    .icon-padded { padding-left: 2.5rem !important; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .password-divider { display: flex; align-items: center; gap: 1rem; margin: .5rem 0; }
    .password-divider::before,
    .password-divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
    .password-divider span { font-size: .75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }

    .password-tips { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1.25rem; }
    .password-tips-header { display: flex; align-items: center; gap: .5rem; font-size: .8rem; font-weight: 700; color: #475569; margin-bottom: .75rem; }
    .password-tips-header i { color: #3b82f6; }
    .password-tips-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .4rem; }
    .password-tips-list li { display: flex; align-items: center; gap: .5rem; font-size: .8rem; color: #64748b; }
    .password-tips-list li i { font-size: .7rem; color: #22c55e; }

    .form-actions-bar { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .btn-cancel-link { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: .625rem 1.25rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; transition: all .2s; text-decoration: none; display: inline-flex; align-items: center; gap: .5rem; }
    .btn-cancel-link:hover { background: #e2e8f0; }
    .btn-save { background: #f97316; color: #fff; border: none; padding: .625rem 1.5rem; font-size: .85rem; font-weight: 600; border-radius: .5rem; cursor: pointer; display: inline-flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-save:hover { background: #ea580c; }

    .alert-banner { display: flex; align-items: center; justify-content: space-between; padding: .875rem 1.125rem; border-radius: .75rem; margin-bottom: 2rem; font-size: .85rem; font-weight: 600; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .alert-inner { display: flex; align-items: center; gap: .5rem; }
    .alert-banner button { background: none; border: none; cursor: pointer; color: inherit; opacity: .6; }

    @media (max-width: 700px) { .form-row { grid-template-columns: 1fr; } }
</style>

<script>
    // Auto dismiss alert
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>

@endsection
