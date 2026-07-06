@extends('layouts.app')

@section('title', 'Data Berita')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success" id="alertBanner">
    <div class="alert-inner">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button onclick="this.closest('.alert-banner').remove()">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert-banner alert-danger" id="alertBanner">
    <div class="alert-inner">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:1rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button onclick="this.closest('.alert-banner').remove()">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

{{-- ── PAGE CARD ── --}}
<div class="faq-card">

    {{-- Card Header --}}
    <div class="faq-card-header">
        <div class="faq-card-title">
            <i class="fas fa-newspaper"></i>
            <div>
                <h2>Data Berita</h2>
                <p>Kelola artikel, berita, dan informasi terbaru yang ditampilkan ke publik</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i>
            Tambah Berita
        </button>
    </div>

    {{-- Toolbar: Search --}}
    <div class="faq-toolbar">
        <div class="toolbar-left"></div>
        <div class="toolbar-right">
            <label class="search-label">Cari:</label>
            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Cari berita..."
                       class="search-input" onkeyup="filterTable()">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="faq-table" id="faqTable">
            <thead>
                <tr>
                    <th class="col-no">NO <i class="fas fa-sort"></i></th>
                    <th style="width: 110px;">TANGGAL <i class="fas fa-sort"></i></th>
                    <th style="width: 250px;">JUDUL <i class="fas fa-sort"></i></th>
                    <th style="width: 100px; text-align: center;">GAMBAR</th>
                    <th>ISI <i class="fas fa-sort"></i></th>
                    <th class="col-action">AKSI <i class="fas fa-sort"></i></th>
                </tr>
            </thead>
            <tbody id="faqTableBody">
                @forelse($beritas as $b)
                <tr class="faq-row">
                    <td class="col-no">
                        <span class="row-num">{{ $loop->iteration }}</span>
                    </td>
                    <td>
                        <span style="color: #64748b; font-weight: 500;">
                            {{ $b->created_at ? $b->created_at->translatedFormat('d F Y') : '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="question-text">{{ $b->judul }}</span>
                    </td>
                    <td style="text-align: center;">
                        <img src="{{ $b->gambar ? asset('storage/'.$b->gambar) : 'https://via.placeholder.com/80' }}"
                             alt="Gambar Berita"
                             style="width: 64px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                    </td>
                    <td>
                        <div class="answer-clamp">{{ Str::limit(strip_tags($b->isi), 120) }}</div>
                    </td>
                    <td class="col-action">
                        <div class="action-btns">
                            <button onclick='openDetail(@json($b))' class="btn-action btn-view" title="Detail Berita">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button onclick='openModal(@json($b))' class="btn-action btn-edit" title="Edit Berita">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                            <form action="{{ route('berita.destroy', $b->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus berita ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Hapus Berita">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data berita</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($beritas, 'links'))
    <div class="faq-pagination">
        {{ $beritas->links() }}
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════════
     MODAL TAMBAH / EDIT BERITA
══════════════════════════════════════════ --}}
<div id="beritaModal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width: 720px;">

        {{-- Modal Header --}}
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3 id="modalTitle">Tambah Berita</h3>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="beritaForm" method="POST" enctype="multipart/form-data" class="modal-body">
            @csrf
            <input type="hidden" id="methodField" name="_method">

            {{-- Preview & Upload Gambar --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-image"></i> Gambar Utama Berita
                </label>
                <div style="display: flex; align-items: center; gap: 1.5rem; background: #fafafa; padding: 1rem; border-radius: 0.5rem; border: 1px dashed #cbd5e1;">
                    <img id="preview" src="https://via.placeholder.com/150" 
                         style="width: 100px; height: 100px; border-radius: 8px; object-fit: cover; border: 2px solid #e2e8f0; background: #fff;">
                    <div style="flex: 1;">
                        <input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewImage(this)" class="form-input" style="padding: 0.4rem;">
                        <p style="font-size: 0.7rem; color: #94a3b8; margin: 4px 0 0 4px;">Format: JPG, JPEG, PNG (Maks. 2MB)</p>
                    </div>
                </div>
            </div>

            {{-- Judul --}}
            <div class="form-group">
                <label class="form-label" for="judul">
                    <i class="fas fa-heading"></i> Judul Berita
                </label>
                <input type="text" name="judul" id="judul" required placeholder="Masukkan judul berita..." class="form-input">
            </div>

            {{-- Isi Berita Field dengan CKEditor --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-file-alt"></i> Isi Berita
                </label>
                <div id="editor"></div>
                <input type="hidden" name="isi" id="isi_berita">
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" id="submitBtn" class="btn-submit">
                    <i class="fas fa-save"></i>
                    <span id="submitLabel">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL DETAIL BERITA
══════════════════════════════════════════ --}}
<div id="detailModal" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width: 680px;">
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon" style="background: #2563eb;">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Detail Berita</h3>
            </div>
            <button class="modal-close-btn" onclick="closeDetailModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" style="gap: 1rem;">
            <h2 id="detailJudul" style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; line-height: 1.4;"></h2>
            <p id="detailTanggal" style="font-size: 0.75rem; color: #94a3b8; margin: 0; font-weight: 500;"></p>
            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 0;">
            <img id="detailGambar" src="" alt="Gambar" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; display: none;">
            <div id="detailIsi" style="font-size: 0.875rem; color: #334155; line-height: 1.6; white-space: normal; word-break: break-word;"></div>
        </div>
        <div class="modal-footer" style="padding: 1rem 1.5rem;">
            <button type="button" onclick="closeDetailModal()" class="btn-cancel">Tutup</button>
        </div>
    </div>
</div>

{{-- ── STYLES ── --}}
<style>
    .alert-banner {
        display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;
        padding: 0.875rem 1.125rem; border-radius: 0.625rem; margin-bottom: 1.25rem; font-size: 0.85rem; font-weight: 500;
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-inner { display: flex; align-items: flex-start; gap: 0.5rem; }
    .alert-banner button { background: none; border: none; cursor: pointer; color: inherit; opacity: 0.6; padding: 2px; flex-shrink: 0; }
    .alert-banner button:hover { opacity: 1; }

    .faq-card { background: #fff; border-radius: 0.875rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 4px rgba(15,23,42,.06); overflow: hidden; }
    .faq-card-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; gap: 1rem; flex-wrap: wrap; }
    .faq-card-title { display: flex; align-items: center; gap: 0.75rem; }
    .faq-card-title i { font-size: 1.25rem; color: #f97316; }
    .faq-card-title h2 { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0; }
    .faq-card-title p { font-size: 0.75rem; color: #94a3b8; margin: 2px 0 0; }

    .btn-primary {
        display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; border: none; border-radius: 0.5rem;
        font-size: 0.825rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: box-shadow 0.2s, transform 0.15s;
        box-shadow: 0 3px 10px rgba(249,115,22,.35); white-space: nowrap;
    }
    .btn-primary:hover { box-shadow: 0 5px 16px rgba(249,115,22,.45); transform: translateY(-1px); }

    .faq-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1.5rem; border-bottom: 1px solid #f8fafc; gap: 1rem; flex-wrap: wrap; background: #fafafa; }
    .search-label { font-size: 0.8rem; color: #64748b; font-weight: 500; }
    .search-wrap { position: relative; }
    .search-icon { position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); font-size: 0.7rem; color: #94a3b8; }
    .search-input { padding: 0.4rem 0.75rem 0.4rem 1.875rem; border: 1px solid #e2e8f0; border-radius: 0.4rem; font-size: 0.8rem; color: #1e293b; background: #fff; outline: none; width: 200px; transition: border-color 0.18s, box-shadow 0.18s; }
    .search-input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.12); }

    .table-wrap { overflow-x: auto; }
    .faq-table { width: 100%; border-collapse: collapse; font-size: 0.825rem; }
    .faq-table thead tr { background: #f8fafc; border-bottom: 2px solid #f1f5f9; }
    .faq-table thead th { padding: 0.75rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #64748b; white-space: nowrap; }
    .col-no { width: 52px; }
    
    /* Action buttons */
    .action-btns {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: center;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border: none;
        border-radius: 0.35rem;
        font-size: 0.72rem;
        font-weight: 600;
        cursor: pointer;
        transition: filter 0.15s, transform 0.1s;
        white-space: nowrap;
        width: 80px;
    }
    .btn-action:hover { filter: brightness(1.1); transform: translateY(-1px); }
    
    .btn-view   { background: #2563eb; color: #fff; }
    .btn-edit   { background: #f59e0b; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    .faq-row { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .faq-row:hover { background: #fffbf7; }
    .faq-row td { padding: 0.875rem 1rem; vertical-align: middle; }

    .row-num { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #fff7ed; color: #f97316; font-size: 0.7rem; font-weight: 700; }
    .question-text { font-weight: 600; color: #1e293b; line-height: 1.45; }
    .answer-clamp { color: #475569; line-height: 1.6; max-width: 450px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .empty-state { text-align: center; padding: 3rem 1rem !important; color: #94a3b8; }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }
    .faq-pagination { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; }

    .modal-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(3px); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: fadeIn 0.2s ease; }
    .modal-backdrop.hidden { display: none !important; }
    .modal-box { background: #fff; border-radius: 0.875rem; box-shadow: 0 20px 60px rgba(15,23,42,.2); width: 100%; max-height: 90vh; overflow-y: auto; animation: slideUp 0.25s cubic-bezier(.4,0,.2,1); }
    @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
    @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

    .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.125rem 1.5rem; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; background: #fff; z-index: 5; border-radius: 0.875rem 0.875rem 0 0; }
    .modal-header-left { display: flex; align-items: center; gap: 0.625rem; }
    .modal-icon { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.8rem; flex-shrink: 0; }
    .modal-header h3 { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0; }
    .modal-close-btn { width: 30px; height: 30px; border-radius: 50%; border: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.75rem; transition: background 0.15s, color 0.15s; }
    .modal-close-btn:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }

    .modal-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1.125rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.375rem; }
    .form-label { font-size: 0.8rem; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 0.375rem; }
    .form-label i { color: #f97316; font-size: 0.7rem; }
    .form-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; padding: 0.625rem 0.875rem; font-size: 0.825rem; color: #1e293b; background: #fafafa; outline: none; transition: border-color 0.18s, box-shadow 0.18s, background 0.18s; font-family: inherit; }
    .form-input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.12); background: #fff; }

    .ck-editor__editable_inline { min-height: 250px !important; background-color: #fff !important; border: 1.5px solid #e2e8f0 !important; border-radius: 0 0 0.5rem 0.5rem !important; }
    .ck-toolbar { border: 1.5px solid #e2e8f0 !important; border-bottom: none !important; border-radius: 0.5rem 0.5rem 0 0 !important; background: #f8fafc !important; }

    .modal-footer { display: flex; justify-content: flex-end; gap: 0.625rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; margin-top: 0.25rem; }
    .btn-cancel { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1.125rem; border: 1.5px solid #e2e8f0; background: #fff; color: #64748b; border-radius: 0.5rem; font-size: 0.825rem; font-weight: 600; cursor: pointer; transition: background 0.15s, border-color 0.15s; }
    .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; color: #374151; }
    
    .btn-submit { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1.375rem; background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; border: none; border-radius: 0.5rem; font-size: 0.825rem; font-weight: 600; cursor: pointer; box-shadow: 0 3px 10px rgba(249,115,22,.35); transition: box-shadow 0.2s, transform 0.15s; }
    .btn-submit:hover { box-shadow: 0 5px 16px rgba(249,115,22,.45); transform: translateY(-1px); }
    .btn-submit.update-mode { background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 3px 10px rgba(34,197,94,.35); }
    .btn-submit.update-mode:hover { box-shadow: 0 5px 16px rgba(34,197,94,.45); }

    @media (max-width: 640px) {
        .faq-toolbar { flex-direction: column; align-items: flex-start; }
        .toolbar-right { width: 100%; }
        .search-input { width: 100%; }
        .faq-card-header { flex-direction: column; align-items: flex-start; }
    }
</style>

@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading', '|', 
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', 'imageUpload', 'mediaEmbed', '|',
                'undo', 'redo'
            ],
            table: { contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ] },
            mediaEmbed: { previewsInData: true }
        })
        .then(editor => { editorInstance = editor; })
        .catch(error => { console.error('Gagal memuat CKEditor:', error); });

    document.getElementById('beritaForm').addEventListener('submit', function(e) {
        const editorContent = editorInstance ? editorInstance.getData() : '';
        if (editorContent.trim() === '' || editorContent.trim() === '<p>&nbsp;</p>') {
            document.getElementById('isi_berita').value = '';
        } else {
            document.getElementById('isi_berita').value = editorContent;
        }
    });

    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = "https://via.placeholder.com/150";
        }
    }

    function openModal(data = null) {
        const modal       = document.getElementById('beritaModal');
        const form        = document.getElementById('beritaForm');
        const modalTitle  = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');
        const judul       = document.getElementById('judul');
        const preview     = document.getElementById('preview');
        const submitBtn   = document.getElementById('submitBtn');
        const submitLabel = document.getElementById('submitLabel');
        const fileInput   = document.getElementById('gambar');

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        fileInput.required = false; 

        if (data) {
            modalTitle.textContent  = 'Edit Data Berita';
            form.action             = `{{ url('berita') }}/${data.id}`;
            methodField.disabled    = false;
            methodField.value       = 'PUT';
            judul.value             = data.judul;
            submitLabel.textContent = 'Update';
            submitBtn.classList.add('update-mode');

            if (editorInstance) { editorInstance.setData(data.isi || ''); }
            if (data.gambar) {
                preview.src = `{{ asset('storage') }}/${data.gambar}`;
            } else {
                preview.src = "https://via.placeholder.com/150";
            }
            setTimeout(() => judul.focus(), 100);
        } else {
            modalTitle.textContent  = 'Tambah Data Berita';
            form.action             = `{{ route('berita.store') }}`;
            methodField.disabled    = true;
            methodField.value       = '';
            judul.value             = '';
            preview.src             = "https://via.placeholder.com/150";
            fileInput.value         = ''; 
            submitLabel.textContent = 'Simpan';
            submitBtn.classList.remove('update-mode');

            if (editorInstance) { editorInstance.setData(''); }
            setTimeout(() => judul.focus(), 100);
        }
    }

    function closeModal() {
        document.getElementById('beritaModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openDetail(data) {
        const detailModal   = document.getElementById('detailModal');
        const detailJudul   = document.getElementById('detailJudul');
        const detailTanggal = document.getElementById('detailTanggal');
        const detailGambar  = document.getElementById('detailGambar');
        const detailIsi     = document.getElementById('detailIsi');

        detailJudul.textContent = data.judul;
        let tgl = '-';
        if(data.created_at) {
            const d = new Date(data.created_at);
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            tgl = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }
        detailTanggal.innerHTML = `<i class="far fa-calendar-alt"></i> Diposting pada: ${tgl}`;
        
        if (data.gambar) {
            detailGambar.src = `{{ asset('storage') }}/${data.gambar}`;
            detailGambar.style.display = 'block';
        } else {
            detailGambar.style.display = 'none';
        }
        detailIsi.innerHTML = data.isi;
        detailModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('beritaModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    document.getElementById('detailModal').addEventListener('click', function(e) { if (e.target === this) closeDetailModal(); });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeDetailModal();
        }
    });

    function filterTable() {
        const val  = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#faqTableBody .faq-row');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(val) ? '' : 'none';
        });
    }

    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>
@endpush