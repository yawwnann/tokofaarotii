@extends('layouts.app')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

{{-- ── TOOLBAR ── --}}
<div class="toolbar-wrap">
    <div class="toolbar-left">
        <h3 class="toolbar-title">Detail Pesanan: <span style="color:#f97316;">{{ $order->invoice }}</span></h3>
    </div>
    <div class="toolbar-right">
        <a href="{{ route('admin.orders.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Detail Pelanggan & Produk -->
    <div class="col-xl-8 col-lg-7">
        
        <div class="custom-card mb-4">
            <div class="card-header-custom">
                <i class="fas fa-map-marker-alt text-primary-custom"></i> Informasi Pengiriman
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted-custom">Pemesan</small>
                        <div class="fw-bold-custom">{{ $order->user->name }}</div>
                        <div class="text-small-custom">{{ $order->user->email }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted-custom">Penerima & Kontak</small>
                        <div class="fw-bold-custom">{{ $order->address->receiver_name ?? '-' }}</div>
                        <div class="text-small-custom">{{ $order->address->phone ?? '-' }}</div>
                    </div>
                    <div class="col-12 mt-2">
                        <small class="text-muted-custom">Alamat Lengkap</small>
                        <div class="address-box">
                            <strong>{{ $order->address->address ?? '-' }}</strong><br>
                            {{ $order->address->village ?? '' }} {{ $order->address->district ?? '' }}, 
                            {{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }} 
                            {{ $order->address->postal_code ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="custom-card mb-4">
            <div class="card-header-custom">
                <i class="fas fa-shopping-bag text-primary-custom"></i> Produk yang Dipesan
            </div>
            <div class="card-body-custom p-0">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>PRODUK</th>
                                <th style="text-align:right;">HARGA</th>
                                <th style="text-align:center;">QTY</th>
                                <th style="text-align:right;">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td><span class="fw-bold-custom">{{ $item->product_name }}</span></td>
                                <td style="text-align:right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td style="text-align:center;"><span class="qty-badge">{{ $item->quantity }}</span></td>
                                <td style="text-align:right; font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Subtotal Belanja</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->cod_fee > 0)
                    <div class="summary-row" style="color:#dc2626;">
                        <span>Biaya COD (2%)</span>
                        <span>Rp {{ number_format($order->cod_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="summary-row total-row">
                        <span>Total Tagihan</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Kolom Kanan: Status & Update Resi -->
    <div class="col-xl-4 col-lg-5">
        
        <div class="custom-card mb-4">
            <div class="card-header-custom">
                <i class="fas fa-wallet text-primary-custom"></i> Status Pembayaran
            </div>
            <div class="card-body-custom">
                <div class="status-item">
                    <small>Metode Pembayaran</small>
                    <div class="role-badge" style="background:#e2e8f0; color:#334155; margin-top:5px;">
                        {{ strtoupper($order->payment_method) }}
                    </div>
                </div>
                <div class="status-item mt-3">
                    <small>Status Transaksi</small>
                    <div class="mt-1">
                        @if($order->payment_status == 'paid')
                            <span class="role-badge" style="background:#dcfce7; color:#16a34a; font-size:0.8rem; padding:0.4rem 1rem;">LUNAS</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="role-badge" style="background:#fef3c7; color:#d97706; font-size:0.8rem; padding:0.4rem 1rem;">PENDING</span>
                        @else
                            <span class="role-badge" style="background:#fee2e2; color:#dc2626; font-size:0.8rem; padding:0.4rem 1rem;">GAGAL</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="custom-card mb-4">
            <div class="card-header-custom" style="background:#fff7ed; border-bottom:1px solid #ffedd5;">
                <i class="fas fa-truck-fast text-primary-custom"></i> Update Pengiriman
            </div>
            <div class="card-body-custom">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label>Status Pesanan Saat Ini</label>
                        <select class="form-input" name="order_status" required>
                            <option value="menunggu_pembayaran" {{ $order->order_status == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="menunggu_diproses" {{ $order->order_status == 'menunggu_diproses' ? 'selected' : '' }}>Menunggu Diproses</option>
                            <option value="diproses" {{ $order->order_status == 'diproses' ? 'selected' : '' }}>Diproses (Penyiapan Gudang)</option>
                            <option value="sedang_dikemas" {{ $order->order_status == 'sedang_dikemas' ? 'selected' : '' }}>Sedang Dikemas</option>
                            <option value="dikirim" {{ $order->order_status == 'dikirim' ? 'selected' : '' }}>Dikirim (Dalam Perjalanan)</option>
                            <option value="selesai" {{ $order->order_status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $order->order_status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Nama Kurir Ekspedisi</label>
                        <input type="text" class="form-input" name="courier" value="{{ $order->courier }}" placeholder="Contoh: JNE, SiCepat">
                    </div>

                    <div class="form-group mb-4">
                        <label>Nomor Resi Pengiriman</label>
                        <input type="text" class="form-input" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="Contoh: JP123456789">
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:0.8rem;">
                        <i class="fas fa-save"></i> Simpan Perubahan Status
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<style>
    /* ── Toolbar ── */
    .toolbar-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
    .toolbar-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-cancel { background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; padding: .5rem 1.25rem; border-radius: .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; transition:all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; color:#0f172a; }
    .btn-primary { background: #f97316; color: #fff; border: none; padding: .625rem 1.25rem; border-radius: .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-primary:hover { background: #ea580c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(249, 115, 22, .2); }

    /* ── Custom Card ── */
    .custom-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden; }
    .card-header-custom { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #1e293b; font-size: 1rem; display:flex; align-items:center; gap:0.5rem; }
    .text-primary-custom { color: #f97316; }
    .card-body-custom { padding: 1.5rem; }
    
    .text-muted-custom { color: #94a3b8; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.2rem; display:block; }
    .fw-bold-custom { font-weight: 700; color: #334155; font-size:0.95rem; }
    .text-small-custom { font-size: 0.85rem; color: #64748b; }
    .address-box { background: #f8fafc; border: 1px dashed #cbd5e1; padding: 1rem; border-radius: 0.75rem; margin-top: 0.5rem; font-size:0.85rem; color:#475569; line-height:1.5; }

    /* ── Table Inside Card ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; border-bottom:1px solid #e2e8f0; }
    td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size:0.85rem; color:#475569; }
    .qty-badge { background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: 0.35rem; font-weight:700; font-size:0.75rem; }
    
    .summary-box { padding: 1.5rem; background: #fafaf9; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9rem; color: #64748b; font-weight: 600; }
    .summary-row.total-row { margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed #e2e8f0; font-size: 1.15rem; color: #f97316; font-weight: 800; margin-bottom: 0; }

    /* ── Badges ── */
    .role-badge { display: inline-block; padding: .25rem .75rem; border-radius: 9999px; font-size: .65rem; font-weight: 800; letter-spacing: .05em; }
    
    /* ── Form Inputs ── */
    .form-group label { display: block; font-size: .8rem; font-weight: 700; color: #475569; margin-bottom: .5rem; }
    .form-input { width: 100%; padding: .625rem 1rem; border: 1.5px solid #e2e8f0; border-radius: .625rem; font-size: .85rem; outline: none; transition: all .2s; box-sizing: border-box; background:#fff; color:#334155; font-weight:500; }
    .form-input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, .1); }

    /* ── Alert ── */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6; }
</style>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>
@endsection
