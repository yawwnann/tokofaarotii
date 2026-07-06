

<!-- Footer -->
<link rel="stylesheet" href="{{ asset('template-sarab/css/footer.css') }}">
<footer>
    <div class="container py-5">
    <div class="row g-5">
        <!-- Kolom 1: Logo & Deskripsi -->
        <div class="col-sm-6 col-lg-3">
            <div class="fnm">FAA <span>Frozen Food & Bakery</span></div>
            <p class="fdesc">FAA Frozen Food & Bakery menyediakan berbagai produk makanan beku dan Roti berkualitas tinggi dengan rasa yang lezat dan konsisten.</p>
            <div class="fsoc">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

        <!-- Kolom 2: Link Cepat-->
        <div class="col-sm-6 col-lg-2">
            <div class="ftit">Link Cepat</div>
            <ul class="flinks ps-0">
                <li><a href="{{ route('welcome') }}"><i class="fas fa-chevron-right"></i>Beranda</a></li>
                <li><a href="{{ route('tentang-kami') }}"><i class="fas fa-chevron-right"></i>Tentang Kami</a></li>
                <li><a href="{{ route('berita.public') }}"><i class="fas fa-chevron-right"></i>Berita</a></li>
                <li><a href="{{ route('produk.makanan') }}"><i class="fas fa-chevron-right"></i>Produk</a></li>
                <li><a href="{{ route('faq.public') }}"><i class="fas fa-chevron-right"></i>FAQ</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Hubungi Kami-->
        <div class="col-sm-6 col-lg-4">
            <div class="ftit">Hubungi Kami</div>
            <div class="fci">
                <div class="fciico"><i class="fas fa-map-marker-alt"></i></div>
                <div class="fciinfo"><strong>Alamat</strong>Kuday, Sungai Liat, Kabupaten Bangka, Kepulauan Bangka Belitung 33211</div>
            </div>
            <div class="fci">
                <div class="fciico"><i class="fas fa-phone-alt"></i></div>
                <div class="fciinfo"><strong>Telepon</strong>+62 0853-6878-7893</div>
            </div>
            <div class="fci">
                <div class="fciico"><i class="fas fa-envelope"></i></div>
                <div class="fciinfo"><strong>Email</strong>hello@sarabfood.com</div>
            </div>
            <div class="fci">
                <div class="fciico"><i class="fas fa-clock"></i></div>
                <div class="fciinfo"><strong>Jam Operasional</strong>Setiap Hari: 06.00 - 18.00</div>
            </div>
        </div>

        <!-- Kolom 4: Lokasi / Google Maps Toko FAA Frozen Food & Bakery -->
        <div class="col-sm-6 col-lg-3">
            <div class="ftit">Lokasi</div>
            <div class="fmap" style="border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3985.449717171717!2d106.1104212!3d-1.8504601!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e22f3e7784d51ad%3A0xf0b32b5d14082039!2sFAA+FROZEN+FOOD!5e0!3m2!1sid!2sid!4v1717424400000!5m2!1sid!2sid"
                width="100%"
                height="200"
                style="border:0; display:block;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
    </div>

    <div class="fbot">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <p>&copy 2026 <span>FAA Frozen Food & Bakery</span>. <br>Dibuat oleh <a target="_blank" class="mx-0 fw-bold" href="https://www.instagram.com/juliy_safteri?igsh=eTQwZXE3Y2QxdXFj">Juliarti Safitri</a></p>
        </div>
    </div>
    </div>
</footer>



<!-- JavaScript Libraries -->
<script src="{{ asset('template-sarab/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('template-sarab/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template-sarab/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('template-sarab/js/aos.js') }}"></script>
<script src="{{ asset('template-sarab/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('template-sarab/js/main.js') }}"></script>

<script>
    // Back to Top functionality
    const btt = document.getElementById('btt');
    if (btt) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btt.classList.add('show');
            } else {
                btt.classList.remove('show');
            }
        });
    }

    // AOS Initialization
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'slide',
            once: true
        });
    }
</script>
