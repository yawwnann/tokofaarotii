<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Toko FAA')</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Poppins:wght@300;400;500;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet"/>

    {{-- CSS --}}
    <link href="{{ asset('template-sarab/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="{{ asset('template-sarab/css/aos.css') }}" rel="stylesheet"/>
    <link href="{{ asset('template-sarab/css/swiper-bundle.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('template-sarab/css/all.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('template-sarab/css/magnific-popup.css') }}" rel="stylesheet"/>
    <link href="{{ asset('template-sarab/css/style.css') }}" rel="stylesheet" />

    {{-- Navbar & Footer CSS --}}
    <link rel="stylesheet" href="{{ asset('template-sarab/css/footer.css') }}">

    @stack('styles')
</head>

<body>

    @include('layouts.partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    {{-- Hidden Placeholders for Compatibility with main.js --}}
    <div style="display:none;">
        <button id="resBtn"></button>
        <div id="resOk"></div>
        <button id="nlBtn"></button>
        <input id="nlEmail">
    </div>

    @stack('scripts')

</body>
</html>
