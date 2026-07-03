@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pesanan: {{ $order->invoice }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Kolom Kiri: Detail Pelanggan & Produk -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengiriman</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Nama Pelanggan / Akun</small>
                            <strong>{{ $order->user->name }}</strong> ({{ $order->user->email }})
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Penerima & Kontak</small>
                            <strong>{{ $order->address->receiver_name ?? '-' }}</strong><br>
                            {{ $order->address->phone ?? '-' }}
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Alamat Lengkap</small>
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

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produk yang Dipesan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga Satuan</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Subtotal Belanja</th>
                                    <th>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Ongkos Kirim</th>
                                    <th>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right h5 mb-0">Total Tagihan</th>
                                    <th class="h5 mb-0 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Status & Update Resi -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Metode Pembayaran</small>
                        <h6 class="font-weight-bold text-uppercase">{{ $order->payment_method }}</h6>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Status Pembayaran</small>
                        @if($order->payment_status == 'paid')
                            <span class="badge bg-success p-2 text-white">LUNAS</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="badge bg-warning p-2 text-dark">PENDING</span>
                        @else
                            <span class="badge bg-danger p-2 text-white">GAGAL / EXPIRED</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Pengiriman & Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label for="order_status">Status Pesanan</label>
                            <select class="form-select form-control" id="order_status" name="order_status" required>
                                <option value="menunggu_pembayaran" {{ $order->order_status == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                <option value="menunggu_diproses" {{ $order->order_status == 'menunggu_diproses' ? 'selected' : '' }}>Menunggu Diproses</option>
                                <option value="diproses" {{ $order->order_status == 'diproses' ? 'selected' : '' }}>Diproses (Penyiapan)</option>
                                <option value="sedang_dikemas" {{ $order->order_status == 'sedang_dikemas' ? 'selected' : '' }}>Sedang Dikemas</option>
                                <option value="dikirim" {{ $order->order_status == 'dikirim' ? 'selected' : '' }}>Dikirim (Input Resi)</option>
                                <option value="selesai" {{ $order->order_status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $order->order_status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="courier">Nama Kurir</label>
                            <input type="text" class="form-control" id="courier" name="courier" value="{{ $order->courier }}" placeholder="Contoh: JNE, J&T, Gosend">
                        </div>

                        <div class="form-group mb-4">
                            <label for="tracking_number">Nomor Resi Pengiriman</label>
                            <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="Nomor Resi">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
