@extends('layouts.public')

@section('title','Pesanan Saya')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pesanan Saya</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($orders as $order)
        <div class="card mb-3 shadow-sm border-light">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="text-muted small">
                                <i class="bi bi-calendar3"></i> {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
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
                            <span class="badge bg-{{ $color }}">{{ str_replace('_', ' ', strtoupper($order->order_status)) }}</span>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $order->invoice }}</h5>
                        <h4 class="text-primary fw-bold mb-1">Rp {{ number_format($order->total, 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ $order->payment_method == 'midtrans' ? 'Pembayaran Online' : 'Bayar di Tempat (COD)' }}</small>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        @if($order->payment_method == 'midtrans' && $order->payment_status == 'pending' && $order->order_status == 'menunggu_pembayaran')
                            <a href="{{ route('payment.show', $order->id) }}" class="btn btn-warning btn-sm fw-bold text-dark">
                                <i class="bi bi-credit-card"></i> Bayar
                            </a>
                        @endif
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada riwayat pesanan. <a href="{{ route('welcome') }}">Mulai belanja</a> yuk!</div>
    @endforelse

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>

@endsection
