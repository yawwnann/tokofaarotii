@extends('layouts.public')

@section('title', 'VR 3D Showroom - FAA Frozen Food & Bakery')

@section('content')

<script src="https://aframe.io/releases/1.5.0/aframe.min.js"></script>

<style>
.showroom{
    width:100%;
    height:100vh;
}

body{
    overflow:hidden;
}
</style>

<div class="showroom">

<a-scene>

    <!-- Langit -->
    <a-sky color="#87CEEB"></a-sky>

    <!-- Cahaya -->
    <a-light type="ambient" intensity="1.2"></a-light>

    <a-light
        type="directional"
        intensity="1"
        position="2 4 2">
    </a-light>

    <!-- Lantai -->
    <a-plane
        rotation="-90 0 0"
        width="100"
        height="100"
        color="#cfcfcf">
    </a-plane>

    <!-- Bangunan Toko -->
    <a-entity
        gltf-model="{{ asset('models/showroom/maptokoroti.glb') }}"
        position="0 0 0"
        rotation="0 0 0"
        scale="1 1 1">
    </a-entity>

    <!-- Produk Kue -->
    <a-entity
        id="kue"
        gltf-model="{{ asset('models/produk/kue.glb') }}"
        position="-2 1 -3"
        scale="0.5 0.5 0.5">
    </a-entity>

    <!-- Produk Frozen -->
    <a-entity
        id="frozen"
        gltf-model="{{ asset('models/produk/frozen.glb') }}"
        position="2 1 -3"
        scale="0.5 0.5 0.5">
    </a-entity>

    <!-- Kamera -->
    <a-entity position="0 1.6 8">

        <a-camera
            wasd-controls
            look-controls>

            <a-cursor></a-cursor>

        </a-camera>

    </a-entity>

</a-scene>

</div>

<script>
document.querySelector("#kue").addEventListener("click", function () {
    alert("Kue FAA Frozen Food");
});

document.querySelector("#frozen").addEventListener("click", function () {
    alert("Produk Frozen FAA");
});
</script>

@endsection
