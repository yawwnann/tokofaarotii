@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="alert-banner alert-danger">
    <div class="alert-inner">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:1rem;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    <button onclick="this.closest('.alert-banner').remove()"><i class="fas fa-times"></i></button>
</div>
@endif

{{-- ── PAGE CARD ── --}}
<div class="cat-card">

    {{-- Card Header --}}
    <div class="cat-card-header">
        <div class="cat-card-title">
            <i class="fas fa-tags"></i>
            <div>
                <h2>Data Kategori</h2>
                <p>Kelola kategori produk yang tersedia di toko</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>

    {{-- Toolbar --}}
    <div class="cat-toolbar">
        <div class="toolbar-left">
            <span class="total-badge">
                <i class="fas fa-tags"></i>
                {{ $categories->total() }} Kategori
            </span>
        </div>
        <div class="toolbar-right">
            <label class="search-label">Cari:</label>
            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput"
                       placeholder="Cari nama kategori..."
                       class="search-input"
                       onkeyup="filterTable()">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="cat-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th>NAMA KATEGORI</th>
                    <th style="width:120px;">JUMLAH PRODUK</th>
                    <th class="col-action">AKSI</th>
                </tr>
            </thead>
            <tbody id="catTableBody">
                @forelse($categories as $index => $category)
                <tr class="cat-row">
                    <td class="col-no">
                        <span class="row-num">{{ $categories->firstItem() + $index }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div class="cat-icon-dot"></div>
                            <span class="cat-name">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="prod-count-badge">
                            {{ $category->products_count }} produk
                        </span>
                    </td>
                    <td class="col-action">
                        <div class="action-btns">
                            <button onclick='openModal(@json($category))' class="btn-action btn-edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fas fa-tags"></i>
                        <p>Belum ada data kategori</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($categories->hasPages())
    <div class="cat-pagination">
        <div class="pagination-info">
            Menampilkan {{ $categories->firstItem() }}–{{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
        </div>
        <div>{{ $categories->links() }}</div>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════════
     MODAL TAMBAH / EDIT KATEGORI
══════════════════════════════════════════ --}}
<div id="categoryModal" class="modal-backdrop hidden">
    <div class="modal-box">

        {{-- Modal Header --}}
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 id="modalTitle">Tambah Kategori</h3>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="categoryForm" method="POST" class="modal-body">
            @csrf
            <input type="hidden" id="methodField" name="_method">

            {{-- Nama Kategori --}}
            <div class="form-group">
                <label class="form-label" for="name">
                    <i class="fas fa-tag"></i> Nama Kategori <span class="required">*</span>
                </label>
                <input type="text" name="name" id="name" required
                       placeholder="Contoh: Frozen, Bakery, Minuman..."
                       class="form-input">
                <p class="field-hint">Nama kategori akan tampil sebagai filter produk</p>
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

{{-- ── STYLES ── --}}
{{-- Paste seluruh kode ini ke dalam file kelola kategori kamu --}}

<style>
    /* ─ Alert ─ */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.625rem;margin-bottom:1.25rem;font-size:.85rem;font-weight:500;animation:slideDown .3s ease; }
    @keyframes slideDown { from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)} }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-danger  { background:#fef2f2;border:1px solid #fecaca;color:#991b1b; }
    .alert-inner   { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6;padding:2px;flex-shrink:0; }
    .alert-banner button:hover { opacity:1; }

    /* ─ Card ─ */
    .cat-card { background:#fff;border-radius:.875rem;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(15,23,42,.06);overflow:hidden; }

    /* ─ Card Header ─ */
    .cat-card-header { display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;gap:1rem;flex-wrap:wrap; }
    .cat-card-title { display:flex;align-items:center;gap:.75rem; }
    .cat-card-title > i { font-size:1.25rem;color:#f97316; }
    .cat-card-title h2 { font-size:.95rem;font-weight:700;color:#1e293b;margin:0; }
    .cat-card-title p  { font-size:.75rem;color:#94a3b8;margin:2px 0 0; }

    /* ─ Button Primary ─ */
    .btn-primary { display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:var(--color-primary,#ea580c);color:#fff;border:none;border-radius:.5rem;font-size:.825rem;font-weight:600;cursor:pointer;text-decoration:none;transition:box-shadow .2s,transform .15s;box-shadow:0 3px 10px rgba(249,115,22,.35);white-space:nowrap; }
    .btn-primary:hover { box-shadow:0 5px 16px rgba(249,115,22,.45);transform:translateY(-1px); }

    /* ─ Toolbar ─ */
    .cat-toolbar { display:flex;align-items:center;justify-content:space-between;padding:.875rem 1.5rem;border-bottom:1px solid #f8fafc;gap:1rem;flex-wrap:wrap;background:#fafafa; }
    .toolbar-left  { display:flex;align-items:center;gap:.5rem; }
    .toolbar-right { display:flex;align-items:center;gap:.5rem; }
    .total-badge { display:inline-flex;align-items:center;gap:.375rem;padding:.3rem .75rem;background:#fff7ed;color:#f97316;border:1px solid #fed7aa;border-radius:9px;font-size:.75rem;font-weight:600; }
    .search-label { font-size:.8rem;color:#64748b;font-weight:500; }
    .search-wrap  { position:relative; }
    .search-icon  { position:absolute;left:.625rem;top:50%;transform:translateY(-50%);font-size:.7rem;color:#94a3b8; }
    .search-input { padding:.4rem .75rem .4rem 1.875rem;border:1px solid #e2e8f0;border-radius:.4rem;font-size:.8rem;color:#1e293b;background:#fff;outline:none;width:200px;transition:border-color .18s,box-shadow .18s; }
    .search-input:focus { border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.12); }

    /* ─ Tabel ─ */
    .table-wrap { overflow-x:auto; }
    .cat-table  { width:100%;border-collapse:collapse;font-size:.825rem; }
    .cat-table thead tr { background:#f8fafc;border-bottom:2px solid #f1f5f9; }
    .cat-table thead th { padding:.75rem 1rem;text-align:left;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#64748b;white-space:nowrap; }
    .col-no     { width:52px; }
    .col-action { width:150px;text-align:center !important; }
    .cat-row    { border-bottom:1px solid #f1f5f9;transition:background .15s; }
    .cat-row:hover { background:#fffbf7; }
    .cat-row td { padding:.875rem 1rem;vertical-align:middle; }

    .row-num { display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#fff7ed;color:#f97316;font-size:.7rem;font-weight:700; }

    .cat-icon-dot { width:8px;height:8px;border-radius:50%;background:var(--color-primary,#ea580c);flex-shrink:0; }
    .cat-name { font-weight:600;color:#1e293b; }
    .prod-count-badge { display:inline-block;padding:.2rem .625rem;background:#f1f5f9;color:#475569;border-radius:9px;font-size:.72rem;font-weight:600; }

    /* ─ button ─ */
    .action-btns { display:flex;flex-direction:column;gap:5px;align-items:center; }
    .btn-action { display:inline-flex;align-items:center;justify-content:center;gap:.3rem;padding:.3rem .75rem;border:none;border-radius:.35rem;font-size:.72rem;font-weight:600;cursor:pointer;transition:filter .15s,transform .1s;white-space:nowrap;width:80px; }
    .btn-action:hover { filter:brightness(1.1);transform:translateY(-1px); }
    .btn-edit { background: #f59e0b; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    .empty-state { text-align:center;padding:3rem 1rem !important;color:#94a3b8; }
    .empty-state i { font-size:2.5rem;display:block;margin-bottom:.5rem; }
    .empty-state p { font-size:.875rem;margin:0; }

    .cat-pagination { display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-top:1px solid #f1f5f9;background:#fafafa;flex-wrap:wrap;gap:.5rem; }
    .pagination-info { font-size:.8rem;color:#64748b; }

    .modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(3px);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem;animation:fadeIn .2s ease; }
    @keyframes fadeIn { from{opacity:0}to{opacity:1} }
    .modal-backdrop.hidden { display:none !important; }
    .modal-box { background:#fff;border-radius:.875rem;box-shadow:0 20px 60px rgba(15,23,42,.2);width:100%;max-width:440px;max-height:90vh;overflow-y:auto;animation:slideUp .25s cubic-bezier(.4,0,.2,1); }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)} }

    .modal-header { display:flex;align-items:center;justify-content:space-between;padding:1.125rem 1.5rem;border-bottom:1px solid #f1f5f9;position:sticky;top:0;background:#fff;z-index:5;border-radius:.875rem .875rem 0 0; }
    .modal-header-left { display:flex;align-items:center;gap:.625rem; }
    .modal-icon { width:32px;height:32px;border-radius:8px;background:var(--color-primary,#ea580c);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem;flex-shrink:0; }
    .modal-header h3 { font-size:.95rem;font-weight:700;color:#1e293b;margin:0; }
    .modal-close-btn { width:30px;height:30px;border-radius:50%;border:1px solid #e2e8f0;background:#f8fafc;color:#64748b;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.75rem;transition:background .15s,color .15s; }
    .modal-close-btn:hover { background:#fee2e2;color:#ef4444;border-color:#fca5a5; }

    .modal-body { padding:1.5rem;display:flex;flex-direction:column;gap:1.125rem; }
    .form-group  { display:flex;flex-direction:column;gap:.375rem; }
    .form-label  { font-size:.8rem;font-weight:600;color:#374151;display:flex;align-items:center;gap:.375rem; }
    .form-label i { color:#f97316;font-size:.7rem; }
    .required { color:#ef4444; }
    .form-input { width:100%;border:1.5px solid #e2e8f0;border-radius:.5rem;padding:.625rem .875rem;font-size:.825rem;color:#1e293b;background:#fafafa;outline:none;transition:border-color .18s,box-shadow .18s,background .18s;font-family:inherit;box-sizing:border-box; }
    .form-input:focus { border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.12);background:#fff; }
    .field-hint { font-size:.72rem;color:#94a3b8;margin:0; }

    .modal-footer { display:flex;justify-content:flex-end;gap:.625rem;padding-top:1rem;border-top:1px solid #f1f5f9;margin-top:.25rem; }
    .btn-cancel { display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1.125rem;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;border-radius:.5rem;font-size:.825rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s; }
    .btn-cancel:hover { background:#f8fafc;border-color:#cbd5e1;color:#374151; }

    .btn-submit { display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1.375rem;background:var(--color-primary,#ea580c);color:#fff;border:none;border-radius:.5rem;font-size:.825rem;font-weight:600;cursor:pointer;box-shadow:0 3px 10px rgba(249,115,22,.35);transition:box-shadow .2s,transform .15s; }
    .btn-submit:hover { box-shadow:0 5px 16px rgba(249,115,22,.45);transform:translateY(-1px); }

    .btn-submit.update-mode { background:var(--color-primary,#ea580c);box-shadow:0 3px 10px rgba(249,115,22,.35); }
    .btn-submit.update-mode:hover { box-shadow:0 5px 16px rgba(249,115,22,.45); }

    @media(max-width:640px) {
        .cat-toolbar { flex-direction:column;align-items:flex-start; }
        .toolbar-right { width:100%; }
        .search-input  { width:100%; }
        .cat-card-header { flex-direction:column;align-items:flex-start; }
    }
</style>

@endsection

@push('scripts')
<script>
    function openModal(data = null) {
        const modal       = document.getElementById('categoryModal');
        const form        = document.getElementById('categoryForm');
        const submitBtn   = document.getElementById('submitBtn');
        const submitLabel = document.getElementById('submitLabel');
        const nameInput   = document.getElementById('name');

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (data) {
            document.getElementById('modalTitle').innerText   = 'Edit Kategori';
            form.action                                        = `/categories/${data.id}`;
            document.getElementById('methodField').value      = 'PUT';
            nameInput.value                                    = data.name;
            submitLabel.textContent                            = 'Update';
            submitBtn.classList.add('update-mode');
        } else {
            document.getElementById('modalTitle').innerText   = 'Tambah Kategori';
            form.action                                        = `{{ route('categories.store') }}`;
            document.getElementById('methodField').value      = '';
            form.reset();
            submitLabel.textContent                            = 'Simpan';
            submitBtn.classList.remove('update-mode');
        }

        setTimeout(() => nameInput.focus(), 100);
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Backdrop click
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Search filter
    function filterTable() {
        const val  = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#catTableBody .cat-row');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    }

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);

    // Buka modal otomatis jika ada error validasi (setelah redirect dengan errors)
    @if(isset($errors) && $errors->any())
        document.addEventListener('DOMContentLoaded', () => openModal());
    @endif
</script>
@endpush
