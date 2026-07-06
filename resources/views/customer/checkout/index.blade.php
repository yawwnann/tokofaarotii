@extends('layouts.public')

@section('title', 'Checkout')

@section('content')
<style>
    body { background-color: #f8fafc; }
    .checkout-wrapper { max-width: 1140px; margin: 0 auto; padding: 2rem 1rem; }
    .checkout-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 2rem; letter-spacing: -0.02em; }

    .checkout-card {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
    }
    .checkout-card-header { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; }
    .checkout-card-header i { color: #f97316; font-size: 1.25rem; }

    .address-box { border: 2px solid #22c55e; border-radius: 1rem; padding: 1.25rem; background: #f0fdf4; position: relative; }
    .address-badge { background: #22c55e; color: #fff; padding: 0.25rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; display: inline-block; margin-bottom: 0.75rem; }
    .address-name { font-size: 1.05rem; font-weight: 700; color: #166534; margin: 0 0 0.25rem; }
    .address-text { font-size: 0.9rem; color: #15803d; margin: 0; line-height: 1.5; }
    .btn-change-address { background: #ffffff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 0.5rem 1rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.85rem; text-decoration: none; transition: all 0.2s; display: inline-block; margin-top: 1rem; }
    .btn-change-address:hover { background: #eff6ff; }

    .shipping-unavailable {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        color: #991b1b;
    }
    .shipping-unavailable i {
        font-size: 1.25rem;
        margin-top: 2px;
        flex-shrink: 0;
    }
    .shipping-unavailable strong {
        display: block;
        font-size: .9rem;
        margin-bottom: .25rem;
    }
    .shipping-unavailable p {
        margin: 0;
        font-size: .8rem;
        opacity: .85;
    }

    .shipping-rate-display {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .shipping-rate-display .rate-label { font-size: 0.9rem; font-weight: 600; color: #475569; }
    .shipping-rate-display .rate-value { font-size: 1.2rem; font-weight: 800; color: #f97316; }
    .shipping-rate-display .rate-note { font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem; }

    .shipping-breakdown { background: #fafafa; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.75rem 1rem; }
    .shipping-store-item { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; border-bottom: 1px dashed #e2e8f0; }
    .shipping-store-item:last-child { border-bottom: none; }
    .ssi-info { display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }
    .ssi-store { font-size: 0.8rem; font-weight: 600; color: #1e293b; }
    .ssi-district { font-size: 0.75rem; color: #94a3b8; }
    .ssi-cost { font-size: 0.85rem; font-weight: 700; color: #f97316; white-space: nowrap; }

    .payment-options { display: flex; flex-direction: column; gap: 1rem; }
    .payment-tile { position: relative; }
    .payment-tile input[type="radio"] { position: absolute; opacity: 0; }
    .payment-label {
        display: flex; align-items: center; gap: 1rem; padding: 1.25rem;
        border: 2px solid #e2e8f0; border-radius: 1rem; cursor: pointer;
        transition: all 0.2s ease; background: #ffffff;
    }
    .payment-label:hover { border-color: #cbd5e1; background: #f8fafc; }
    .payment-tile input[type="radio"]:checked + .payment-label {
        border-color: #f97316; background: #fff7ed;
    }
    .payment-icon { width: 48px; height: 48px; background: #f1f5f9; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #64748b; transition: all 0.2s; }
    .payment-tile input[type="radio"]:checked + .payment-label .payment-icon { background: #f97316; color: #ffffff; }
    .payment-info h6 { margin: 0 0 0.15rem; font-size: 1rem; font-weight: 700; color: #0f172a; }
    .payment-info p { margin: 0; font-size: 0.8rem; color: #64748b; font-weight: 500; }

    .cod-blocked-banner {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 1rem;
        padding: 1rem;
        color: #dc2626;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .cod-blocked-banner i { font-size: 1.25rem; }

    .summary-sidebar { position: sticky; top: 2rem; }
    .summary-item { display: flex; justify-content: space-between; align-items: flex-start; padding: 1rem 0; border-bottom: 1px solid #f1f5f9; }
    .summary-item:last-child { border-bottom: none; }
    .s-item-name { font-size: 0.95rem; font-weight: 600; color: #1e293b; margin: 0 0 0.25rem; }
    .s-item-qty { font-size: 0.8rem; color: #64748b; font-weight: 500; }
    .s-item-price { font-size: 0.95rem; font-weight: 700; color: #0f172a; }

    .summary-totals { margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px dashed #e2e8f0; }
    .tot-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: #475569; font-weight: 500; }
    .tot-row.cod-fee-row { color: #dc2626; font-weight: 700; }
    .tot-row.grand { margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid #e2e8f0; font-size: 1.25rem; font-weight: 800; color: #0f172a; }
    .grand-price { color: #f97316; }

    .btn-checkout {
        background: #f97316; color: #ffffff; width: 100%; padding: 1rem; border-radius: 1rem;
        border: none; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.2s;
        margin-top: 1.5rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;
    }
    .btn-checkout:hover { background: #ea580c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); }
    .btn-checkout:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none; }

    .cod-fee-note {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: #f1f5f9;
        border-radius: 0.5rem;
    }
</style>

<div class="checkout-wrapper">
    <h2 class="checkout-title">Selesaikan Pesanan Anda</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius:1rem; border:none;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">

                <div class="checkout-card">
                    <div class="checkout-card-header">
                        <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman
                    </div>

                    @if($address)
                        <div class="address-box">
                            <span class="address-badge">Utama ({{ $address->label }})</span>
                            <h6 class="address-name">{{ $address->receiver_name }}</h6>
                            <p class="address-text">{{ $address->phone }}</p>
                            <p class="address-text">{{ $address->address }}</p>
                            <p class="address-text" style="font-size:0.85rem; opacity:0.8;">
                                @if($address->village){{ $address->village }}, @endif
                                {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}
                            </p>
                        </div>
                        <input type="hidden" name="address_id" value="{{ $address->id }}">
                        <a href="{{ route('customer.address') }}" class="btn-change-address"><i class="fas fa-edit me-1"></i> Ubah Alamat Pengiriman</a>
                    @else
                        <div class="alert alert-warning" style="border-radius:1rem; border:none; background:#fffbeb; color:#92400e;">
                            Anda belum memiliki alamat pengiriman. Silakan tambahkan alamat terlebih dahulu.
                        </div>
                        <a href="{{ route('customer.address') }}" class="btn-checkout" style="width:auto; display:inline-flex; padding:0.75rem 1.5rem;">Tambah Alamat</a>
                    @endif
                </div>

                <div class="checkout-card">
                    <div class="checkout-card-header">
                        <i class="fas fa-truck"></i> Ongkos Kirim
                    </div>

                    @if($address)
                        @if(isset($hasRates) && !$hasRates)
                        <div class="shipping-unavailable">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Ongkos Kirim Belum Tersedia</strong>
                                <p>Admin belum mengatur tarif ongkos kirim. Silakan hubungi admin toko untuk melanjutkan checkout.</p>
                            </div>
                        </div>
                        @else
                        <div class="shipping-rate-display">
                            <div>
                                <div class="rate-label">Tarif Ongkir (Zonasi Toko)</div>
                                <div class="rate-note">Berdasarkan kecamatan tujuan pengiriman</div>
                            </div>
                            <div class="rate-value" id="display_shipping_rate">
                                @if($shippingCost > 0)
                                    Rp {{ number_format($shippingCost, 0, ',', '.') }}
                                @else
                                    <span style="color:#22c55e;">Gratis</span>
                                @endif
                            </div>
                        </div>

                        {{-- Multi-Store Shipping Breakdown --}}
                        @if(count($shippingBreakdown) > 0)
                        <div class="shipping-breakdown" style="margin-top:1rem;">
                            <p style="font-size:0.8rem;font-weight:700;color:#475569;margin:0 0 0.5rem;">Rincian Ongkos Kirim:</p>
                            @foreach($shippingBreakdown as $breakdown)
                            <div class="shipping-store-item">
                                <div class="ssi-info">
                                    <span class="ssi-store">{{ $breakdown['store_name'] }}</span>
                                    <span class="ssi-district">({{ $breakdown['store_district'] }})</span>
                                </div>
                                <span class="ssi-cost">
                                    @if($breakdown['cost'] > 0)
                                        Rp {{ number_format($breakdown['cost'], 0, ',', '.') }}
                                    @else
                                        <span style="color:#94a3b8;">Gratis</span>
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endif

                        <input type="hidden" name="shipping_cost" value="{{ $shippingCost }}">
                        <input type="hidden" name="courier" value="Zonasi Toko">
                        <input type="hidden" name="shipping_service" value="Reguler">

                        @if($codBlocked)
                            <div class="cod-blocked-banner mt-3">
                                <i class="fas fa-ban"></i>
                                <span>Fitur COD diblokir sementara karena Anda telah menolak pesanan COD sebanyak 3 kali. Silakan gunakan pembayaran online.</span>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning" style="border-radius:1rem; border:none; background:#fffbeb; color:#92400e;">
                            Silakan tambahkan alamat pengiriman terlebih dahulu untuk melanjutkan.
                        </div>
                    @endif
                </div>

                <div class="checkout-card">
                    <div class="checkout-card-header">
                        <i class="fas fa-wallet"></i> Metode Pembayaran
                    </div>
                    <div class="payment-options">

                        <div class="payment-tile">
                            <input type="radio" name="payment_method" id="payment_midtrans" value="midtrans" {{ $codBlocked ? 'checked' : 'checked' }}>
                            <label class="payment-label" for="payment_midtrans">
                                <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                                <div class="payment-info">
                                    <h6>Bayar Online Terintegrasi</h6>
                                    <p>Transfer Bank (VA), Gopay, QRIS, Alfamart</p>
                                </div>
                            </label>
                        </div>

                        <div class="payment-tile">
                            <input type="radio" name="payment_method" id="payment_cod" value="cod" {{ $codBlocked ? 'disabled' : '' }}>
                            <label class="payment-label" for="payment_cod" style="{{ $codBlocked ? 'opacity:0.5; cursor:not-allowed;' : '' }}">
                                <div class="payment-icon"><i class="fas fa-hand-holding-usd"></i></div>
                                <div class="payment-info">
                                    <h6>Bayar di Tempat (COD)</h6>
                                    <p>Bayar tunai kepada kurir saat pesanan tiba</p>
                                </div>
                            </label>
                        </div>

                    </div>
                    <div class="cod-fee-note" id="codFeeNote" style="display:none;">
                        <i class="fas fa-info-circle"></i> Biaya layanan COD sebesar 2% dari subtotal produk (Rp <span id="codFeeAmount">0</span>) akan ditambahkan ke total tagihan.
                    </div>
                </div>

            </div>

            <div class="col-lg-5">
                <div class="summary-sidebar">
                    <div class="checkout-card">
                        <div class="checkout-card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:1rem; margin-bottom:0;">
                            <i class="fas fa-shopping-bag"></i> Ringkasan Pesanan
                        </div>

                        <div style="max-height: 300px; overflow-y: auto; padding-right: 0.5rem;" class="custom-scrollbar">
                            @foreach($cart as $item)
                                <div class="summary-item">
                                    <div>
                                        <h6 class="s-item-name">{{ $item['name'] }}</h6>
                                        <span class="s-item-qty">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="s-item-price">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary-totals">
                            <div class="tot-row">
                                <span>Subtotal Produk</span>
                                <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                            </div>
                            <div class="tot-row">
                                <span>Ongkos Kirim</span>
                                <strong id="summary_shipping">Rp {{ number_format($shippingCost, 0, ',', '.') }}</strong>
                            </div>
                            <div class="tot-row cod-fee-row" id="cod_fee_row" style="display:none;">
                                <span>Biaya COD (2%)</span>
                                <strong id="summary_cod_fee">Rp 0</strong>
                            </div>
                            <div class="tot-row grand">
                                <span>Total Tagihan</span>
                                <span class="grand-price" id="summary_total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <input type="hidden" id="subtotal_value" value="{{ $subtotal }}">
                        <input type="hidden" id="shipping_cost_value" value="{{ $shippingCost }}">

                        <button type="submit" class="btn-checkout" id="btn_checkout" {{ !$address ? 'disabled' : '' }}>
                            Buat Pesanan Sekarang <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotal = parseInt(document.getElementById('subtotal_value').value);
    const shippingCost = parseInt(document.getElementById('shipping_cost_value').value);
    const summaryShipping = document.getElementById('summary_shipping');
    const summaryCodFee = document.getElementById('summary_cod_fee');
    const summaryTotal = document.getElementById('summary_total');
    const codFeeRow = document.getElementById('cod_fee_row');
    const codFeeNote = document.getElementById('codFeeNote');
    const codFeeAmount = document.getElementById('codFeeAmount');

    function formatRp(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
    }

    function updateTotal() {
        const isCod = document.getElementById('payment_cod').checked;
        const codFee = isCod ? Math.round(subtotal * 0.02) : 0;

        if (isCod) {
            codFeeRow.style.display = 'flex';
            codFeeNote.style.display = 'block';
            summaryCodFee.textContent = formatRp(codFee);
            codFeeAmount.textContent = new Intl.NumberFormat('id-ID').format(codFee);
        } else {
            codFeeRow.style.display = 'none';
            codFeeNote.style.display = 'none';
            summaryCodFee.textContent = formatRp(0);
            codFeeAmount.textContent = '0';
        }

        const total = subtotal + shippingCost + codFee;
        summaryTotal.textContent = formatRp(total);
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', updateTotal);
    });

    updateTotal();
});
</script>
@endpush
