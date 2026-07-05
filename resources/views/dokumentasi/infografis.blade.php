@extends('layouts.app')

@section('title', 'Infografis')
@section('subtitle', 'Kumpulan infografis informasi produk dan perusahaan')

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
        <h3 class="toolbar-title">Daftar Infografis</h3>
    </div>
    <div class="toolbar-right">
        <button onclick="toggleModal('modal-tambah')" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Infografis
        </button>
    </div>
</div>

{{-- ── GRID INFOGRAFIS ── --}}
@if($infografis->isEmpty())
    <div class="empty-state-card">
        <i class="fas fa-chart-pie"></i>
        <p>Belum ada infografis tersedia</p>
    </div>
@else
    <div class="info-grid">
        @foreach($infografis as $info)
        <div class="info-card group">
            <div class="info-img-wrap">
                @if($info->gambar)
                <img src="{{ asset('storage/' . $info->gambar) }}" alt="{{ $info->judul }}">
                <div class="info-overlay">
                    <a href="{{ asset('storage/' . $info->gambar) }}" target="_blank" class="btn-view-full">
                        <i class="fas fa-search-plus"></i> Lihat Full
                    </a>
                </div>
                @else
                <div style="width:100%;height:200px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#94a3b8;"><i class="fas fa-image" style="font-size:2rem;"></i></div>
                @endif
            </div>
            <div class="info-body">
                <h4 class="info-title">{{ $info->judul }}</h4>
                <p class="info-desc">{{ Str::limit($info->deskripsi, 60) }}</p>
                <div class="info-actions-standard">
                    <button onclick="editInfografis({{ json_encode($info) }})" class="btn-action btn-edit" title="Edit">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('dokumentasi.infografis.destroy', $info->id) }}" method="POST" onsubmit="return confirm('Hapus infografis ini?')" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-action btn-delete" title="Hapus">
                            <i class="fas fa-trash-alt"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($infografis->hasPages())
    <div class="pagination-wrap" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $infografis->links() }}
    </div>
    @endif
@endif

{{-- ── MODAL TAMBAH ── --}}
<div id="modal-tambah" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Infografis</h3>
            <button onclick="toggleModal('modal-tambah')" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('dokumentasi.infografis.store') }}" method="POST" enctype="multipart/form-data" class="modal-form">
            @csrf
            <div class="form-group">
                <label>Judul Infografis</label>
                <input type="text" name="judul" required placeholder="Contoh: Manfaat Frozen Food">
            </div>
            <div class="form-group">
                <label>File Gambar</label>
                <input type="file" name="gambar" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>Keterangan Singkat</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat infografis..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="toggleModal('modal-tambah')" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">Simpan Infografis</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ── --}}
<div id="modal-edit" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Edit Infografis</h3>
            <button onclick="toggleModal('modal-edit')" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <form id="form-edit" method="POST" enctype="multipart/form-data" class="modal-form">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Judul Infografis</label>
                <input type="text" name="judul" id="edit-judul" required>
            </div>
            <div class="form-group">
                <label>Ganti Gambar (Opsional)</label>
                <input type="file" name="gambar" accept="image/*">
            </div>
            <div class="form-group">
                <label>Keterangan Singkat</label>
                <textarea name="deskripsi" id="edit-deskripsi" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="toggleModal('modal-edit')" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">Update Infografis</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* ── Toolbar ── */
    .toolbar-wrap { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; }
    .toolbar-title { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-primary { background: #f97316; color: #fff; border: none; padding: .625rem 1.25rem; border-radius: .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .5rem; transition: all .2s; }
    .btn-primary:hover { background: #ea580c; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(249, 115, 22, .2); }

    /* ── Grid ── */
    .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem; }
    .info-card { background: #fff; border-radius: 1.25rem; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 1px 3px rgba(15,23,42,.05); transition: all .3s; }
    .info-card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(15,23,42,.08); }
    
    .info-img-wrap { position: relative; width: 100%; aspect-ratio: 3/4; background: #f8fafc; overflow: hidden; }
    .info-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
    .info-card:hover .info-img-wrap img { transform: scale(1.05); }
    
    .info-overlay { position: absolute; inset: 0; background: rgba(15,23,42,.6); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity .3s; }
    .info-card:hover .info-overlay { opacity: 1; }
    .btn-view-full { background: #fff; color: #1e293b; padding: .5rem 1rem; border-radius: 99px; font-size: .75rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: .4rem; }

    .info-body { padding: 1rem; }
    .info-title { font-size: .9rem; font-weight: 800; color: #1e293b; margin: 0 0 .25rem; }
    .info-desc { font-size: .75rem; color: #64748b; line-height: 1.4; margin-bottom: 1rem; }

    .info-actions-standard { display: flex; align-items: center; gap: .5rem; border-top: 1px solid #f8fafc; padding-top: 0.75rem; justify-content: center; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; padding: 0.3rem 0.75rem; border: none; border-radius: 0.35rem; font-size: 0.72rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; width: 80px; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .btn-edit { background: #f59e0b; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    /* ── Modal (Sama dengan Album) ── */
    .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.6); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .modal-card { background: #fff; border-radius: 1.5rem; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; animation: zoomIn .2s ease-out; }
    @keyframes zoomIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .modal-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; }
    .btn-close-modal { background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer; }
    
    .modal-form { padding: 1.5rem; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-size: .8rem; font-weight: 700; color: #475569; margin-bottom: .5rem; }
    .form-group input, .form-group textarea { width: 100%; padding: .625rem 1rem; border: 1.5px solid #e2e8f0; border-radius: .625rem; font-size: .85rem; outline: none; transition: all .2s; box-sizing: border-box; }
    .form-group input:focus, .form-group textarea:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .1); }

    .modal-footer { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 2rem; }
    .btn-cancel { background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; padding: .625rem 1.25rem; border-radius: .625rem; font-size: .85rem; font-weight: 700; cursor: pointer; }
    .btn-submit { background: #f97316; border: none; color: #fff; padding: .625rem 1.25rem; border-radius: .625rem; font-size: .85rem; font-weight: 700; cursor: pointer; transition: all .2s; }
    .btn-submit:hover { background: #ea580c; }

    .empty-state-card { background: #fff; border-radius: 1.5rem; padding: 4rem 1rem; text-align: center; border: 1px solid #f1f5f9; color: #cbd5e1; }
    .empty-state-card i { font-size: 3rem; margin-bottom: 1rem; display: block; }
    .empty-state-card p { font-size: .9rem; font-weight: 700; color: #94a3b8; }

    .hidden { display: none !important; }

    /* ── Alert ── */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6; }
</style>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function editInfografis(info) {
        document.getElementById('edit-judul').value = info.judul;
        document.getElementById('edit-deskripsi').value = info.deskripsi || '';
        document.getElementById('form-edit').action = `{{ url('dokumentasi/infografis') }}/${info.id}`;
        toggleModal('modal-edit');
    }

    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>

@endsection
