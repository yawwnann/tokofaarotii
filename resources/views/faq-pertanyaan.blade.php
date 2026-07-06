@extends('layouts.public')

@section('title', 'FAQ - FAA Frozen Food & Bakery')

@section('content')
<style>
    :root { --primary: #004aad; --primary-dark: #003580; --primary-light: #e6f0fa; --accent: #f97316; --dark: #0f172a; --slate: #475569; --light: #f8fafc; --border: #e2e8f0; --white: #ffffff; --shadow-sm: 0 4px 6px -1px rgba(15, 23, 42, 0.03), 0 2px 4px -2px rgba(15, 23, 42, 0.03); --shadow-md: 0 10px 25px -5px rgba(15, 23, 42, 0.05); --radius-lg: 16px; --radius-md: 12px; }
    body { font-family: 'Poppins', sans-serif; background-color: var(--light); color: var(--slate); }
    .faq-section { padding: 140px 0 80px 0; }
    .section-tag { font-size: 12px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary); margin-bottom: 6px; }
    .section-title { font-family: 'Playfair Display', serif; font-weight: 700; font-size: clamp(28px, 4vw, 38px); color: var(--dark); margin-bottom: 12px; }
    .section-title span { color: var(--primary); position: relative; }
    .custom-faq-container { max-width: 820px; margin: 0 auto; }
    .accordion-flush { background: transparent; }
    .accordion-item-custom { background-color: var(--white) !important; border: 1px solid var(--border) !important; border-radius: var(--radius-md) !important; overflow: hidden; margin-bottom: 16px; box-shadow: var(--shadow-sm); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; }
    .accordion-item-custom:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    .accordion-button-custom { padding: 20px 24px !important; font-size: 15px; font-weight: 600 !important; color: var(--dark) !important; background-color: var(--white) !important; box-shadow: none !important; transition: color 0.2s ease, background-color 0.3s ease; }
    .accordion-button-custom:not(.collapsed) { color: var(--primary-dark) !important; background-color: var(--primary-light) !important; border-bottom: 1px solid var(--border); }
    .accordion-button-custom::after { background-size: 1.25rem; transition: transform 0.2s ease; }
    .accordion-body-custom { padding: 24px !important; font-size: 14px; line-height: 1.7; color: var(--slate); background-color: var(--white); }
    #btt { position: fixed; bottom: 30px; right: 30px; z-index: 9999; background-color: var(--accent); color: var(--white); border: none; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3); transition: all 0.3s ease; opacity: 0; visibility: hidden; transform: translateY(15px); }
    #btt.show { opacity: 1; visibility: visible; transform: translateY(0); }
    #btt:hover { background-color: var(--primary); transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0, 74, 173, 0.4); }
    .empty-state { padding: 40px 20px; text-align: center; background: var(--white); border-radius: var(--radius-lg); border: 2px dashed var(--border); }
</style>

<section class="faq-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-tag">Bantuan Pelanggan</div>
            <h2 class="section-title">Pertanyaan <span>Sering</span> Diajukan</h2>
            <p class="text-muted small mx-auto" style="max-width: 480px;">Temukan jawaban cepat untuk pertanyaan umum mengenai produk, pemesanan, dan layanan kami.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-12" data-aos="fade-up" data-aos-delay="50">
                <div class="custom-faq-container">
                    <div class="accordion accordion-flush" id="accordionFaq">
                        @forelse($faqs as $index => $faq)
                        <div class="accordion-item accordion-item-custom">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button accordion-button-custom {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                    {{ $faq->pertanyaan }}
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionFaq">
                                <div class="accordion-body accordion-body-custom">{!! $faq->jawaban !!}</div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="bi bi-patch-question text-muted mb-3" style="font-size: 2.5rem; opacity: 0.3;"></i>
                            <p class="text-muted mb-0">Belum ada pertanyaan yang tersedia saat ini.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<button id="btt" aria-label="Kembali ke atas"><i class="bi bi-arrow-up-short" style="font-size: 22px;"></i></button>

<script>
    const bttButton = document.getElementById('btt');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) { bttButton.classList.add('show'); }
        else { bttButton.classList.remove('show'); }
    });
    bttButton.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });
</script>

@include('chatbot')
@endsection
