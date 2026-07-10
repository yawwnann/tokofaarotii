@extends('layouts.app')

@section('title', 'Video Dokumentasi')
@section('subtitle', 'Koleksi video kegiatan dan promosi Toko Faa')

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
        <h3 class="toolbar-title">Galeri Video</h3>
    </div>
    <div class="toolbar-right">
        <button onclick="toggleModal('modal-tambah')" class="btn-primary">
            <i class="fas fa-plus"></i> Tambah Video
        </button>
    </div>
</div>

{{-- ── GRID VIDEO ── --}}
@if($videos->isEmpty())
    <div class="empty-state-card">
        <i class="fas fa-video"></i>
        <p>Belum ada koleksi video</p>
    </div>
@else
    <div class="video-grid">
        @foreach($videos as $video)
        <div class="video-card">
            <div class="video-embed-wrap">
                @if($video->gambar)
                    <a href="{{ $video->url }}" target="_blank" class="social-cover-link">
                        <div class="social-overlay-badge">
                            <i class="fas fa-play" style="color: var(--primary); font-size: 16px;"></i> <span>Tonton Video</span>
                        </div>
                        <img src="{{ Storage::url($video->gambar) }}" class="social-cover-img" alt="Cover Video">
                    </a>
                @elseif(str_contains($video->url, 'instagram.com') || str_contains($video->url, 'tiktok.com'))
                    <!-- Tampilan khusus Instagram & TikTok agar tidak memicu error "Refused to Connect" -->
                    <a href="{{ $video->url }}" target="_blank" class="social-cover-link">
                        <div class="social-overlay-badge">
                            @if(str_contains($video->url, 'instagram.com'))
                                <i class="fab fa-instagram" style="color: #e1306c; font-size: 16px;"></i> <span>Tonton di Instagram</span>
                            @else
                                <i class="fab fa-tiktok" style="color: #000000; font-size: 16px;"></i> <span>Tonton di TikTok</span>
                            @endif
                        </div>
                        <img src="{{ asset('template-sarab/img/frozen-banner.jpg') }}" class="social-cover-img" alt="Konten Media Sosial">
                    </a>
                @else
                    <!-- Tampilan Pemutar Default untuk YouTube -->
                    @php
                        $embedUrl = str_replace('watch?v=', 'embed/', $video->url);
                        if (str_contains($embedUrl, 'youtu.be/')) {
                            $embedUrl = str_replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                        }
                    @endphp
                    <iframe src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                @endif
            </div>
            <div class="video-body">
                <h4 class="video-title">{{ $video->judul }}</h4>
                <p class="video-desc">{{ Str::limit($video->deskripsi, 100) }}</p>
                <div class="video-footer-standard">
                    <div class="video-actions-standard">
                        <button onclick="editVideo({{ json_encode($video) }})" class="btn-action btn-edit" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('dokumentasi.video.destroy', $video->id) }}" method="POST" onsubmit="return confirm('Hapus video ini?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action btn-delete" title="Hapus">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($videos->hasPages())
    <div class="pagination-wrap" style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $videos->links() }}
    </div>
    @endif
@endif

{{-- ── MODAL TAMBAH ── --}}
<div id="modal-tambah" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Video</h3>
            <button onclick="toggleModal('modal-tambah')" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('dokumentasi.video.store') }}" method="POST" class="modal-form" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Judul Video</label>
                <input type="text" name="judul" required placeholder="Contoh: Info Promo Produk Toko FAA">
            </div>
            <div class="form-group">
                <label>Gambar Cover (Opsional)</label>
                <input type="file" name="gambar" accept="image/*">
            </div>
            <div class="form-group">
                <label>URL Video / Postingan (YouTube / Instagram / TikTok)</label>
                <input type="url" name="url" required placeholder="https://...">
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="3" placeholder="Apa isi video ini?"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="toggleModal('modal-tambah')" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">Simpan Video</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT ── --}}
<div id="modal-edit" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Edit Video</h3>
            <button onclick="toggleModal('modal-edit')" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <form id="form-edit" method="POST" class="modal-form" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Judul Video</label>
                <input type="text" name="judul" id="edit-judul" required>
            </div>
            <div class="form-group">
                <label>Gambar Cover (Biarkan kosong jika tidak ingin mengubah)</label>
                <input type="file" name="gambar" accept="image/*">
            </div>
            <div class="form-group">
                <label>URL Video / Postingan</label>
                <input type="url" name="url" id="edit-url" required>
            </div>
            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" id="edit-deskripsi" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="toggleModal('modal-edit')" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">Update Video</button>
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
    .video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 2rem; }
    .video-card { background: #fff; border-radius: 1.5rem; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 1px 3px rgba(15,23,42,.05); transition: all .3s; }
    .video-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(15,23,42,.1); }
    
    .video-embed-wrap { width: 100%; aspect-ratio: 16/9; background: #000; overflow: hidden; position: relative; }
    .video-embed-wrap iframe { width: 100%; height: 100%; }

    /* Styling Tambahan Komponen Deteksi Cover Instagram & TikTok */
    .social-cover-link { display: block; width: 100%; height: 100%; position: relative; text-decoration: none; }
    .social-cover-img { width: 100%; height: 100%; object-fit: cover; opacity: 0.65; transition: all 0.3s ease; }
    .social-cover-link:hover .social-cover-img { opacity: 0.85; transform: scale(1.03); }
    
    .social-overlay-badge { 
        position: absolute; 
        top: 50%; 
        left: 50%; 
        transform: translate(-50%, -50%); 
        background: rgba(255, 255, 255, 0.95); 
        padding: 10px 18px; 
        border-radius: 50px; 
        font-weight: 700; 
        font-size: 0.78rem; 
        color: #0f172a; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.15); 
        z-index: 5; 
        display: flex; 
        align-items: center; 
        gap: 8px;
        white-space: nowrap;
    }

    .video-body { padding: 1.5rem; }
    .video-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0 0 .75rem; line-height: 1.4; }
    .video-desc { font-size: .85rem; color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }

    .video-footer-standard { border-top: 1px solid #f8fafc; padding-top: 1rem; }
    .video-actions-standard { display: flex; align-items: center; gap: .5rem; justify-content: center; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; padding: 0.3rem 0.75rem; border: none; border-radius: 0.35rem; font-size: 0.72rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; width: 80px; }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
    .btn-edit { background: #f59e0b; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    /* ── Modal ── */
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

    @media (max-width: 600px) {
        .video-grid { grid-template-columns: 1fr; }
    }
</style>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function editVideo(video) {
        document.getElementById('edit-judul').value = video.judul;
        document.getElementById('edit-url').value = video.url;
        document.getElementById('edit-deskripsi').value = video.deskripsi || '';
        document.getElementById('form-edit').action = `{{ url('dokumentasi/video') }}/${video.id}`;
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
