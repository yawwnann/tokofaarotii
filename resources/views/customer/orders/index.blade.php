@extends('layouts.frontend')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-primary">Pesanan Saya</h2>
            <p class="text-muted">Pantau status pesanan dan riwayat belanja Anda di sini.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            @forelse($orders as $order)
                <div class="card shadow-sm mb-4 border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small"><i class="bi bi-calendar3"></i> {{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            <h5 class="mb-0 mt-1 fw-bold">{{ $order->invoice }}</h5>
                        </div>
                        <div class="text-end">
                            @php
                                $statusColors = [
                                    'menunggu_pembayaran' => 'secondary',
                                    'menunggu_diproses' => 'warning',
                                    'diproses' => 'info',
                                    'sedang_dikemas' => 'primary',
                                    'dikirim' => 'primary',
                                    'selesai' => 'success',
                                    'dibatalkan' => 'danger',
                                ];
                                $color = $statusColors[$order->order_status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} rounded-pill px-3 py-2 text-white">
                                {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-3 me-3 text-primary">
                                        <i class="bi bi-box2-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">Total Belanja</p>
                                        <h5 class="mb-0 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</h5>
                                        <small class="text-muted">{{ $order->payment_method == 'midtrans' ? 'Pembayaran Online' : 'Bayar di Tempat (COD)' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                @if($order->payment_method == 'midtrans' && $order->payment_status == 'pending' && $order->order_status == 'menunggu_pembayaran')
                                    <a href="{{ route('payment.show', $order->id) }}" class="btn btn-warning rounded-pill px-4 text-dark fw-bold me-2">
                                        <i class="bi bi-credit-card"></i> Bayar
                                    </a>
                                @endif
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" alt="Belum ada pesanan" class="img-fluid mb-4" style="max-width: 250px; opacity: 0.7;">
                    <h4>Belum ada riwayat pesanan</h4>
                    <p class="text-muted mb-4">Yuk, mulai belanja dan temukan produk favorit Anda!</p>
                    <a href="{{ route('welcome') }}" class="btn btn-primary rounded-pill px-4 btn-lg">Mulai Belanja</a>
                </div>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
