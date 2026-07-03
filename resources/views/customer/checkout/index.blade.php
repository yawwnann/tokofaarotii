@extends('layouts.frontend')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Alamat & Pembayaran -->
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Alamat Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        @if($address)
                            <div class="border p-3 rounded mb-3 border-success">
                                <span class="badge bg-success mb-2">Utama</span>
                                <h6>{{ $address->receiver_name }} ({{ $address->label }})</h6>
                                <p class="mb-1">{{ $address->phone }}</p>
                                <p class="mb-1">{{ $address->address }}</p>
                                <small class="text-muted">
                                    {{ $address->village ? $address->village . ',' : '' }}
                                    {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                                </small>
                            </div>
                            <input type="hidden" name="address_id" value="{{ $address->id }}">
                            <a href="{{ route('customer.address') }}" class="btn btn-sm btn-outline-primary">Ubah Alamat Utama</a>
                        @else
                            <div class="alert alert-warning">
                                Anda belum memiliki alamat pengiriman. Silakan tambahkan alamat terlebih dahulu.
                            </div>
                            <a href="{{ route('customer.address') }}" class="btn btn-primary">Tambah Alamat</a>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check border p-3 rounded mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_midtrans" value="midtrans" checked>
                            <label class="form-check-label w-100 fw-bold" for="payment_midtrans">
                                Bayar Online (Transfer Bank, Gopay, QRIS, dll)
                            </label>
                        </div>
                        <div class="form-check border p-3 rounded">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod">
                            <label class="form-check-label w-100 fw-bold" for="payment_cod">
                                Bayar di Tempat (COD)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Pesanan -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($cart as $item)
                                <li class="list-group-item d-flex justify-content-between lh-sm px-0">
                                    <div>
                                        <h6 class="my-0">{{ $item['name'] }}</h6>
                                        <small class="text-muted">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</small>
                                    </div>
                                    <span class="text-muted">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span>Ongkos Kirim</span>
                            <strong>{{ $shippingCost == 0 ? 'Gratis' : 'Rp ' . number_format($shippingCost, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0">Total Tagihan</h5>
                            <h5 class="mb-0 text-primary">Rp {{ number_format($total, 0, ',', '.') }}</h5>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg" {{ !$address ? 'disabled' : '' }}>
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
