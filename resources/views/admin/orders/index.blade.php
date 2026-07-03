@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pesanan</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pesanan Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode Bayar</th>
                            <th>Status Bayar</th>
                            <th>Status Pesanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key => $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $key }}</td>
                            <td>{{ $order->invoice }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                @if($order->payment_method == 'midtrans')
                                    <span class="badge bg-info text-white">Online</span>
                                @else
                                    <span class="badge bg-secondary text-white">COD</span>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success text-white">Lunas</span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-danger text-white">Gagal</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'menunggu_pembayaran' => 'secondary',
                                        'menunggu_diproses' => 'warning',
                                        'diproses' => 'info',
                                        'sedang_dikemas' => 'primary',
                                        'dikirim' => 'success',
                                        'selesai' => 'success',
                                        'dibatalkan' => 'danger',
                                    ];
                                    $color = $statusColors[$order->order_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} text-white">
                                    {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada pesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
