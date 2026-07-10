<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $berita->judul }} - FAA Frozen Food & Bakery</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS Assets -->
    <link href="{{ asset('template-sarab/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('template-sarab/css/style.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #faf6f0; padding-top: 100px; }
        .berita-container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .berita-img { width: 100%; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .berita-judul { font-family: 'Poppins', sans-serif; font-weight: 900; font-size: 2.5rem; color: #0f172a; line-height: 1.2; margin-bottom: 20px; }
        .berita-meta { display: flex; gap: 20px; color: #64748b; font-size: 0.9rem; margin-bottom: 30px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
        .berita-isi { line-height: 1.8; color: #334155; font-size: 1.1rem; }
        .btn-back { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: #dc3545; font-weight: 700; margin-bottom: 30px; transition: transform 0.3s; }
        .btn-back:hover { transform: translateX(-5px); }
    </style>
</head>
<body>

    @include('layouts.partials.navbar')

    <div class="container mb-5">
        <a href="{{ route('berita.public') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Berita
        </a>

        <div class="berita-container">
            @if($berita->gambar)
                <img src="{{ asset('storage/' . $berita->gambar) }}" class="berita-img" alt="{{ $berita->judul }}">
            @else
                <img src="{{ asset('template-sarab/img/blog/1.jpg') }}" class="berita-img" alt="Default">
            @endif

            <h1 class="berita-judul">{{ $berita->judul }}</h1>

            <div class="berita-meta">
                <span><i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::parse($berita->created_at)->translatedFormat('d F Y') }}</span>
                <span><i class="bi bi-person me-2"></i> Admin FAA</span>
            </div>

            <div class="berita-isi">
                {!! nl2br($berita->isi) !!}
            </div>
        </div>
    </div>

    @include('layouts.partials.footer')

</body>
</html>
