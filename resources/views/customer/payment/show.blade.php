@extends('layouts.frontend')

@section('title', 'Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white text-center py-3">
                    <h4 class="mb-0">Menunggu Pembayaran</h4>
                </div>
                <div class="card-body text-center p-5">
                    <h5 class="text-muted mb-3">Total Tagihan</h5>
                    <h2 class="text-primary fw-bold mb-4">Rp {{ number_format($order->total, 0, ',', '.') }}</h2>
                    
                    <p class="mb-2">Invoice: <strong>{{ $order->invoice }}</strong></p>
                    <p class="mb-4">Status Pesanan: <span class="badge bg-warning text-dark">Menunggu Pembayaran</span></p>

                    @if($order->payment_method === 'midtrans' && $order->snap_token)
                        <button id="pay-button" class="btn btn-primary btn-lg px-5 rounded-pill">
                            <i class="bi bi-credit-card me-2"></i> Bayar Sekarang
                        </button>
                    @elseif($order->payment_method === 'cod')
                        <div class="alert alert-success">
                            Pesanan COD Anda sedang kami siapkan. Siapkan uang tunai sebesar Rp {{ number_format($order->total, 0, ',', '.') }} saat kurir tiba.
                        </div>
                        <a href="{{ route('welcome') }}" class="btn btn-primary rounded-pill px-4 mt-3">Kembali ke Beranda</a>
                    @else
                        <div class="alert alert-danger">
                            Terjadi kesalahan pada sistem pembayaran. Token tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($order->payment_method === 'midtrans' && $order->snap_token)
<!-- Midtrans Snap JS -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $order->snap_token }}', {
            onSuccess: function(result){
                alert("Pembayaran berhasil!");
                window.location.href = "{{ route('welcome') }}"; // Arahkan ke history pesanan jika sudah ada
            },
            onPending: function(result){
                alert("Menunggu pembayaran!");
            },
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                console.log('Customer closed the popup without finishing the payment');
            }
        });
    };
</script>
@endif
@endsection
