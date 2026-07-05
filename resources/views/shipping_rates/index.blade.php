@extends('layouts.app')

@section('title', 'Tarif Ongkir Zonasi')
@section('subtitle', 'Kelola tarif ongkos kirim berdasarkan pasangan kecamatan asal → tujuan')

@section('content')

@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

<div class="toolbar-wrap">
    <div class="toolbar-left">
        <h3 class="toolbar-title">Tarif Ongkir Zonasi</h3>
    </div>
    <div class="toolbar-right">
        <a href="{{ route('shipping-rates.create') }}" class="btn-primary" style="text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-plus"></i> Tambah Tarif
        </a>
    </div>
</div>

<div class="table-container">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>KECAMATAN ASAL</th>
                    <th>KECAMATAN TUJUAN</th>
                    <th style="text-align:right;">TARIF (Rp)</th>
                    <th style="text-align:center;">DIBUAT</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rates as $rate)
                <tr>
                    <td>
                        <span class="u-name">{{ $rate->originDistrict->name ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="u-name">{{ $rate->destinationDistrict->name ?? '-' }}</span>
                    </td>
                    <td style="text-align:right;">
                        <span class="u-name" style="color:#f97316;">Rp {{ number_format($rate->rate, 0, ',', '.') }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="u-date">{{ $rate->created_at->translatedFormat('d M Y') }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div class="action-btns">
                            <a href="{{ route('shipping-rates.edit', $rate->id) }}" class="btn-action btn-edit" style="text-decoration:none; background:#3b82f6;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('shipping-rates.destroy', $rate->id) }}" method="POST" onsubmit="return confirm('Hapus tarif ongkir ini?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-danger" style="border:none; background:#ef4444;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 3rem;">
                        <i class="fas fa-truck" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                        <p style="color: #64748b; font-weight: 600;">Belum ada tarif ongkir. Tambah tarif pertama!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($rates->hasPages())
    <div class="pagination-wrap" style="padding: 1rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
        {{ $rates->links() }}
    </div>
    @endif
</div>

<style>
    .toolbar-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
    .toolbar-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-primary { background: #f97316; color: #fff; border: none; padding: .625rem 1.25rem; border-radius: .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; transition: all .2s; }
    .btn-primary:hover { background: #ea580c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(249, 115, 22, .2); }
    .table-container { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .u-name { font-size: .9rem; font-weight: 700; color: #1e293b; }
    .u-date { font-size: .75rem; color: #94a3b8; font-weight: 600; }
    .action-btns { display: flex; flex-direction: column; gap: 5px; align-items: center; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; padding: 0.4rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; white-space: nowrap; color:#fff; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
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
