@extends('layouts.app')

@section('title', 'Daftar Toko')
@section('subtitle', 'Kelola data toko cabang untuk perhitungan ongkos kirim multi-store')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@if(session('error'))
<div class="alert-banner alert-danger">
    <div class="alert-inner"><i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span></div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

{{-- ── TOOLBAR ── --}}
<div class="toolbar-wrap">
    <div class="toolbar-left">
        <h3 class="toolbar-title">Daftar Toko</h3>
    </div>
    <div class="toolbar-right">
        <a href="{{ route('stores.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Toko
        </a>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="table-container">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>NAMA TOKO</th>
                    <th>KECAMATAN</th>
                    <th>KABUPATEN/KOTA</th>
                    <th>PROVINSI</th>
                    <th style="text-align:center;">STATUS</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr>
                    <td>
                        <div class="td-store">
                            <div class="store-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="store-info">
                                <span class="s-name">{{ $store->name }}</span>
                                @if($store->phone)
                                    <span class="s-phone"><i class="fas fa-phone"></i> {{ $store->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="s-loc">{{ $store->district->name ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="s-loc">{{ $store->district->regency->name ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="s-loc">{{ $store->district->regency->province->name ?? '-' }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($store->is_active)
                            <span class="badge badge-green">Aktif</span>
                        @else
                            <span class="badge badge-red">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('stores.edit', $store->id) }}" class="btn-action btn-edit" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('stores.destroy', $store->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus toko ini?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Hapus">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-store-alt"></i>
                        <p>Belum ada toko. Tambahkan toko pertama Anda!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stores->hasPages())
    <div class="pagination-wrap" style="padding: 1rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
        {{ $stores->links() }}
    </div>
    @endif
</div>

<style>
    .toolbar-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
    .toolbar-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-primary { background: #f97316; color: #fff; border: none; padding: .625rem 1.25rem; border-radius: .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: all .2s; text-decoration: none; }
    .btn-primary:hover { background: #ea580c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(249,115,22,.2); color:#fff; }

    .table-container { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: .65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
    td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .td-store { display: flex; align-items: center; gap: .75rem; }
    .store-icon { width: 40px; height: 40px; background: #fff7ed; color: #f97316; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .store-info { display: flex; flex-direction: column; }
    .s-name { font-size: .9rem; font-weight: 700; color: #1e293b; }
    .s-phone { font-size: .75rem; color: #64748b; margin-top: 2px; }
    .s-loc { font-size: .85rem; color: #475569; font-weight: 500; }

    .badge { display: inline-block; padding: .25rem .75rem; border-radius: 9999px; font-size: .65rem; font-weight: 800; letter-spacing: .05em; }
    .badge-green { background: #dcfce7; color: #15803d; }
    .badge-red { background: #fee2e2; color: #b91c1c; }

    .action-btns { display: flex; flex-direction: column; gap: 5px; align-items: center; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; padding: 0.3rem 0.75rem; border: none; border-radius: 0.35rem; font-size: 0.72rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; width: 80px; text-decoration: none; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .btn-edit { background: #f59e0b; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    .empty-state { text-align: center; padding: 3rem 1rem !important; color: #94a3b8; }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: .5rem; }
    .empty-state p { margin: .25rem 0 0; font-size: .9rem; }

    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-danger { background:#fef2f2;border:1px solid #fee2e2;color:#991b1b; }
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
