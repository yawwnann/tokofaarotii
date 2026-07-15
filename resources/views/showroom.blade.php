@extends('layouts.public')

@section('title', 'VR 3D Showroom - FAA Frozen Food & Bakery')

@section('content')

<script src="https://aframe.io/releases/1.5.0/aframe.min.js"></script>

<style>
    .showroom {
        width: 100%;
        height: 100vh;
        position: relative;
    }

    body {
        overflow: hidden;
    }

    /* ===== Loading Screen ===== */
    #vr-loading {
        position: fixed;
        inset: 0;
        background: #0f172a;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999;
        font-family: 'Poppins', sans-serif;
        transition: opacity 0.4s ease;
    }

    #vr-loading .spinner {
        width: 48px;
        height: 48px;
        border: 4px solid rgba(255, 255, 255, 0.2);
        border-top-color: #f97316;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 16px;
    }

    #vr-loading .progress-text {
        font-size: 14px;
        color: #93c5fd;
        margin-top: 6px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ===== Error Log ===== */
    #vr-error-log {
        position: fixed;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.75);
        color: #f87171;
        font-family: monospace;
        font-size: 12px;
        padding: 10px;
        border-radius: 6px;
        max-width: 400px;
        z-index: 998;
        display: none;
    }

    /* ===== Instruksi Navigasi ===== */
    #vr-instructions {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        padding: 10px 18px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        gap: 14px;
        z-index: 997;
        border: 1px solid rgba(249, 115, 22, 0.5);
        transition: opacity 0.4s ease;
    }

    #vr-instructions span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #vr-instructions .key {
        background: #1e293b;
        border: 1px solid #f97316;
        border-radius: 4px;
        padding: 2px 6px;
        font-weight: 600;
        color: #fdba74;
    }

    #vr-instructions .close-btn {
        cursor: pointer;
        color: #94a3b8;
        margin-left: 6px;
        font-weight: bold;
    }

    #vr-instructions .close-btn:hover {
        color: #fff;
    }

    /* ===== Reset View Button ===== */
    #vr-reset-btn {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 997;
        background: #fff;
        color: #1e293b;
        border: none;
        border-radius: 8px;
        padding: 10px 14px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s ease;
    }

    #vr-reset-btn:hover {
        background: #f97316;
        color: #fff;
    }
</style>

<div class="showroom">

    <div id="vr-loading">
        <div class="spinner"></div>
        <p>Memuat Showroom 3D...</p>
        <p class="progress-text" id="vr-progress-text">Menyiapkan model...</p>
    </div>

    <div id="vr-error-log"></div>

    <div id="vr-instructions" style="display:none;">
        <span><span class="key">Klik + Geser</span> Lihat Sekeliling</span>
        <span><span class="key">W A S D</span> Bergerak</span>
        <span class="close-btn" id="vr-instructions-close">&times;</span>
    </div>

    <button id="vr-reset-btn">⟳ Reset Tampilan</button>

    <a-scene loading-screen="enabled: false">

        <a-assets timeout="20000">
            <!-- Model Showroom -->
        <a-asset-item
                id="model-toko"
                src="{{ asset('models/showroom/maptokofaa.glb') }}">
            </a-asset-item>

            <!-- Produk Bakery -->
            <a-asset-item
                id="model-abon"
                src="{{ asset('models/produk/roti/abon-dalam.glb') }}">
            </a-asset-item>

        </a-assets>

        <!-- Langit -->
        <a-sky color="#87CEEB"></a-sky>

        <!-- Cahaya -->
        <a-light type="ambient" intensity="1.2"></a-light>
        <a-light type="directional" intensity="1" position="2 4 2"></a-light>

        <!-- Bangunan Toko -->
        <a-entity
            id="toko"
            gltf-model="#model-toko"
            position="0 0 0"
            rotation="0 0 0"
            scale="1 1 1">
        </a-entity>

        <!-- Produk Bakery -->
        <a-entity
            id="produk-abon"
            gltf-model="#model-abon"
            position="0 1.18 -1.15"
            rotation="0 90 0"
            scale="0.08 0.08 0.08">
        </a-entity>

        <!-- Rig Pergerakan (WASD) + Kamera (Look Controls) -->
        <a-entity
            id="rig"
            position="0 1.6 6"
            movement-controls="fly: false; speed: 0.15"
            wasd-controls="acceleration: 30">
            <a-entity
                id="cam"
                camera="near: 0.05; far: 10000"
                look-controls="pointerLockEnabled: false"
                position="0 0 0">
                <a-cursor fuse="false" color="#f97316"></a-cursor>
            </a-entity>
        </a-entity>

    </a-scene>

</div>

<script>
    const loadingScreen   = document.getElementById('vr-loading');
    const progressText    = document.getElementById('vr-progress-text');
    const errorLog        = document.getElementById('vr-error-log');
    const instructions     = document.getElementById('vr-instructions');
    const instructionsClose = document.getElementById('vr-instructions-close');
    const resetBtn         = document.getElementById('vr-reset-btn');
    const tokoEl           = document.querySelector('#toko');
    //tambahan element untuk model produk roti
    const abonEl = document.querySelector('#produk-abon');

    const rigEl            = document.querySelector('#rig');

    const RESET_POSITION = { x: 0, y: 1.6, z: 6 };
    const RESET_ROTATION = { x: 0, y: 0, z: 0 };

    function hideLoading() {
        loadingScreen.style.opacity = '0';
        setTimeout(() => (loadingScreen.style.display = 'none'), 400);
        instructions.style.display = 'flex';
    }


    // Event untuk model produk roti
    abonEl.addEventListener('model-loaded', function () {
        console.log('✅ Produk Abon Dalam berhasil dimuat');
    });

    abonEl.addEventListener('model-error', function (e) {
        console.error('❌ Gagal memuat produk Abon Dalam', e.detail);

        errorLog.style.display = 'block';
        errorLog.innerHTML += '<br>❌ Produk Abon Dalam gagal dimuat.';
    });

    // Update progress kasar dari event asset A-Frame
    const sceneEl = document.querySelector('a-scene');
    sceneEl.addEventListener('loaded', function () {
        progressText.textContent = 'Merender scene...';
    });

    // Safety timeout — kalau lebih dari 20 detik masih loading, paksa hilangkan
    setTimeout(function () {
        if (loadingScreen.style.display !== 'none') {
            loadingScreen.style.display = 'none';
            if (errorLog.style.display === 'none') {
                errorLog.style.display = 'block';
                errorLog.innerHTML = '⚠️ Loading timeout. Model mungkin terlalu besar atau gagal fetch.';
            }
        }
    }, 20000);

    // Tutup instruksi navigasi
    instructionsClose.addEventListener('click', function () {
        instructions.style.display = 'none';
    });

    // Reset posisi kamera ke titik awal
    resetBtn.addEventListener('click', function () {
        rigEl.setAttribute('position', RESET_POSITION);
        rigEl.setAttribute('rotation', RESET_ROTATION);
        const camEl = document.querySelector('#cam');
        camEl.setAttribute('rotation', RESET_ROTATION);
    });

    // ===============================
// DEBUG POSISI PRODUK (sementara)
// ===============================
window.addEventListener('keydown', function(e){

    const p = abonEl.object3D.position;

    switch(e.key){

        case 'ArrowUp':
            p.z -= 0.02;
            break;

        case 'ArrowDown':
            p.z += 0.02;
            break;

        case 'ArrowLeft':
            p.x -= 0.02;
            break;

        case 'ArrowRight':
            p.x += 0.02;
            break;

        case 'q':
            p.y += 0.02;
            break;

        case 'e':
            p.y -= 0.02;
            break;

    }

    console.clear();

    console.log(
        "Position :",
        p.x.toFixed(2),
        p.y.toFixed(2),
        p.z.toFixed(2)
    );

});
</script>

@endsection
