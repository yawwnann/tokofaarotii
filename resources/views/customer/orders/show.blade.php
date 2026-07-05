@extends('layouts.frontend')

@section('title', 'Detail Pesanan ' . $order->invoice)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Kembali ke Pesanan Saya
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Rincian Produk -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="fw-bold mb-0 text-primary">Detail Produk</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">Produk</th>
                                    <th class="py-3 border-0">Harga</th>
                                    <th class="py-3 border-0 text-center">Qty</th>
                                    <th class="px-4 py-3 border-0 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/'.$item->product->image) }}" alt="{{ $item->product_name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex justify-content-center align-items-center text-muted" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $item->product_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-muted">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="fw-bold mb-0 text-primary">Info Pengiriman</h5>
                </div>
                <div class="card-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Penerima</p>
                            <p class="mb-1 fw-bold">{{ $order->address->receiver_name ?? '-' }}</p>
                            <p class="mb-0">{{ $order->address->phone ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1 small text-uppercase fw-bold">Alamat</p>
                            <p class="mb-0">
                                {{ $order->address->address ?? '-' }}<br>
                                {{ $order->address->village ?? '' }} {{ $order->address->district ?? '' }}, 
                                {{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }} 
                                {{ $order->address->postal_code ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan & Pelacakan -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-primary">Ringkasan Pesanan</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Produk</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Total Bayar</span>
                        <span class="fw-bold fs-4 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>

                    @if($order->payment_method == 'midtrans' && $order->payment_status == 'pending' && $order->order_status == 'menunggu_pembayaran')
                        <a href="{{ route('payment.show', $order->id) }}" class="btn btn-warning rounded-pill w-100 fw-bold mb-2 py-2">
                            Lanjutkan Pembayaran
                        </a>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-primary">Pelacakan Pesanan</h5>
                    
                    <div class="mb-3">
                        <p class="text-muted mb-1 small text-uppercase fw-bold">Status Saat Ini</p>
                        <h6 class="fw-bold">
                            {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                        </h6>
                    </div>

                    @if($order->courier || $order->tracking_number)
                    <div class="mb-3">
                        <p class="text-muted mb-1 small text-uppercase fw-bold">Kurir Pengiriman</p>
                        <p class="mb-0 fw-bold">{{ strtoupper($order->courier) ?: '-' }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-muted mb-1 small text-uppercase fw-bold">Nomor Resi</p>
                        <p class="mb-0 fw-bold text-primary fs-5">{{ $order->tracking_number ?: '-' }}</p>
                    </div>
                    @endif

                    @if($order->order_status == 'dikirim')
                        <hr>
                        <p class="text-muted small mb-3">Pesanan Anda sedang dalam perjalanan. Jika paket sudah diterima dengan baik, silakan konfirmasi pesanan.</p>
                        <form action="{{ route('customer.orders.confirm', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2" onclick="return confirm('Apakah Anda yakin paket sudah diterima?')">
                                Pesanan Diterima
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
