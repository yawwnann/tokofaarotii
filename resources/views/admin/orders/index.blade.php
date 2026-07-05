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
        <h3 class="toolbar-title">Manajemen Pesanan Online</h3>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="table-container">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>INVOICE</th>
                    <th>PELANGGAN</th>
                    <th style="text-align:right;">TOTAL</th>
                    <th style="text-align:right;">COD FEE</th>
                    <th style="text-align:center;">METODE</th>
                    <th style="text-align:center;">BAYAR</th>
                    <th style="text-align:center;">STATUS PESANAN</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $key => $order)
                <tr>
                    <td>
                        <span class="u-name">{{ $order->invoice }}</span>
                        <div class="u-date mt-1">{{ $order->created_at->translatedFormat('d F Y H:i') }}</div>
                    </td>
                    <td>
                        <div class="td-user">
                            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-info">
                                <span class="u-name">{{ $order->user->name }}</span>
                                <span class="u-email">{{ $order->user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <span class="u-name" style="color: #f97316;">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </td>
                    <td style="text-align:right;">
                        @if($order->cod_fee > 0)
                            <span style="font-size:0.85rem; font-weight:600; color:#dc2626;">Rp {{ number_format($order->cod_fee, 0, ',', '.') }}</span>
                        @else
                            <span style="font-size:0.85rem; color:#94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($order->payment_method == 'midtrans')
                            <span class="role-badge" style="background:#e0f2fe; color:#0284c7;">ONLINE</span>
                        @else
                            <span class="role-badge badge-gray">COD</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($order->payment_status == 'paid')
                            <span class="role-badge badge-success" style="background:#dcfce7; color:#16a34a;">LUNAS</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="role-badge badge-warning" style="background:#fef3c7; color:#d97706;">PENDING</span>
                        @else
                            <span class="role-badge badge-danger" style="background:#fee2e2; color:#dc2626;">GAGAL</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @php
                            $statusColors = [
                                'menunggu_pembayaran' => ['#f1f5f9', '#475569'],
                                'menunggu_diproses' => ['#fef3c7', '#d97706'],
                                'diproses' => ['#e0f2fe', '#0284c7'],
                                'sedang_dikemas' => ['#ede9fe', '#7c3aed'],
                                'dikirim' => ['#ffedd5', '#c2410c'],
                                'selesai' => ['#dcfce7', '#16a34a'],
                                'dibatalkan' => ['#fee2e2', '#dc2626'],
                            ];
                            $colors = $statusColors[$order->order_status] ?? ['#f1f5f9', '#475569'];
                        @endphp
                        <span class="role-badge" style="background: {{ $colors[0] }}; color: {{ $colors[1] }};">
                            {{ str_replace('_', ' ', strtoupper($order->order_status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-action btn-edit" style="text-decoration:none; background:#3b82f6;">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 3rem;">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                        <p style="color: #64748b; font-weight: 600;">Belum ada pesanan online.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="pagination-wrap" style="padding: 1rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<style>
    /* ── Toolbar ── */
    .toolbar-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
    .toolbar-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }

    /* ── Table ── */
    .table-container { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .td-user { display: flex; align-items: center; gap: .75rem; }
    .user-avatar { width: 40px; height: 40px; background: #fff7ed; color: #f97316; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .user-info { display: flex; flex-direction: column; }
    .u-name { font-size: .9rem; font-weight: 700; color: #1e293b; }
    
    .u-email { font-size: .85rem; color: #64748b; font-weight: 500; }
    .u-date { font-size: .75rem; color: #94a3b8; font-weight: 600; }

    .role-badge { display: inline-block; padding: .35rem .75rem; border-radius: 9999px; font-size: .65rem; font-weight: 800; letter-spacing: .05em; }
    .badge-gray { background: #f1f5f9; color: #475569; }

    /* Action buttons */
    .action-btns { display: flex; flex-direction: column; gap: 5px; align-items: center; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; padding: 0.4rem 0.75rem; border: none; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }

    /* ── Alert ── */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6; }
</style>

<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>
@endsection
