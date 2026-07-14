@extends('layouts.app')

@section('title', 'Kelola FAQ')

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
            <i class="fas fa-question-circle"></i>
            <div>
                <h2>Data FAQ</h2>
                <p>Kelola pertanyaan & jawaban yang ditampilkan ke publik</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i>
            Tambah Data
        </button>
    </div>

    {{-- Toolbar: Export + Search --}}
    <div class="faq-toolbar">
        <div class="toolbar-left">
            <a href="{{ Route::has('faq.export.excel') ? route('faq.export.excel') : '#' }}" class="btn-export btn-excel">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ Route::has('faq.export.pdf') ? route('faq.export.pdf') : '#' }}" class="btn-export btn-pdf">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
        <div class="toolbar-right">
            <label class="search-label">Cari:</label>
            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Ketik untuk mencari..."
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
                    <th class="col-question">PERTANYAAN <i class="fas fa-sort"></i></th>
                    <th class="col-answer">JAWABAN <i class="fas fa-sort"></i></th>
                    <th class="col-action">AKSI <i class="fas fa-sort"></i></th>
                </tr>
            </thead>
            <tbody id="faqTableBody">
                @forelse($faqs as $f)
                <tr class="faq-row">
                    <td class="col-no">
                        <span class="row-num">{{ $loop->iteration }}</span>
                    </td>
                    <td class="col-question">
                        <span class="question-text">{{ $f->pertanyaan }}</span>
                    </td>
                    <td class="col-answer">
                        <div class="answer-clamp">{!! $f->jawaban !!}</div>
                    </td>
                    <td class="col-action">
                        <div class="action-btns">
                            <button onclick='openModal(@json($f))' class="btn-action btn-edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                            <form action="{{ route('faq.destroy', $f->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus FAQ ini?')">
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
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data FAQ</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (if any) --}}
    @if(method_exists($faqs, 'links'))
    <div class="faq-pagination">
        {{ $faqs->links() }}
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════════
     MODAL TAMBAH / EDIT
══════════════════════════════════════════ --}}
<div id="faqModal" class="modal-backdrop hidden">
    <div class="modal-box">

        {{-- Modal Header --}}
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 id="modalTitle">Tambah Data FAQ</h3>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="faqForm" method="POST" class="modal-body">
            @csrf
            <input type="hidden" id="methodField" name="_method">

            {{-- Pertanyaan --}}
            <div class="form-group">
                <label class="form-label" for="pertanyaan">
                    <i class="fas fa-question"></i> Pertanyaan
                </label>
                <input type="text"
                       name="pertanyaan"
                       id="pertanyaan"
                       required
                       placeholder="Masukkan pertanyaan..."
                       class="form-input">
            </div>

            {{-- Jawaban --}}
            <div class="form-group">
                <label class="form-label" for="jawaban">
                    <i class="fas fa-comment-dots"></i> Jawaban
                </label>
                <textarea name="jawaban"
                          id="jawaban"
                          rows="6"
                          required
                          placeholder="Masukkan jawaban lengkap..."
                          class="form-textarea"></textarea>
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
<style>
    /* ── Alert ── */
    .alert-banner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.875rem 1.125rem;
        border-radius: 0.625rem;
        margin-bottom: 1.25rem;
        font-size: 0.85rem;
        font-weight: 500;
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown {
        from { opacity:0; transform:translateY(-8px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    .alert-inner { display: flex; align-items: flex-start; gap: 0.5rem; }
    .alert-banner button {
        background: none; border: none; cursor: pointer;
        color: inherit; opacity: 0.6; padding: 2px;
        flex-shrink: 0;
    }
    .alert-banner button:hover { opacity: 1; }

    /* ── Page Card ── */
    .faq-card {
        background: #fff;
        border-radius: 0.875rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(15,23,42,.06);
        overflow: hidden;
    }

    /* ── Card Header ── */
    .faq-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .faq-card-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .faq-card-title i {
        font-size: 1.25rem;
        color: var(--color-primary, #f97316);
    }
    .faq-card-title h2 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .faq-card-title p {
        font-size: 0.75rem;
        color: #94a3b8;
        margin: 2px 0 0;
    }

    /* ── Buttons ── */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        background: var(--color-primary, #ea580c);
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: box-shadow 0.2s, transform 0.15s;
        box-shadow: 0 3px 10px rgba(249,115,22,.35);
        white-space: nowrap;
    }
    .btn-primary:hover {
        box-shadow: 0 5px 16px rgba(249,115,22,.45);
        transform: translateY(-1px);
    }

    /* ── Toolbar ── */
    .faq-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.875rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        gap: 1rem;
        flex-wrap: wrap;
        background: #fafafa;
    }
    .toolbar-left { display: flex; align-items: center; gap: 0.5rem; }
    .toolbar-right { display: flex; align-items: center; gap: 0.5rem; }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.4rem 0.875rem;
        border-radius: 0.4rem;
        font-size: 0.775rem;
        font-weight: 600;
        text-decoration: none;
        transition: filter 0.15s, transform 0.15s;
        border: none;
        cursor: pointer;
    }
    .btn-export:hover { filter: brightness(1.08); transform: translateY(-1px); }
    .btn-excel { background: #1d6f42; color: #fff; }
    .btn-pdf   { background: #c0392b; color: #fff; }

    .search-label { font-size: 0.8rem; color: #64748b; font-weight: 500; }
    .search-wrap { position: relative; }
    .search-icon {
        position: absolute;
        left: 0.625rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.7rem;
        color: #94a3b8;
    }
    .search-input {
        padding: 0.4rem 0.75rem 0.4rem 1.875rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.4rem;
        font-size: 0.8rem;
        color: #1e293b;
        background: #fff;
        outline: none;
        width: 200px;
        transition: border-color 0.18s, box-shadow 0.18s;
    }
    .search-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249,115,22,.12);
    }

    /* ── Table ── */
    .table-wrap { overflow-x: auto; }
    .faq-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.825rem;
    }
    .faq-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #f1f5f9;
    }
    .faq-table thead th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
    }
    .faq-table thead th i {
        font-size: 0.6rem;
        margin-left: 4px;
        opacity: 0.5;
    }
    .faq-table thead th:hover { color: #f97316; }

    .col-no     { width: 52px; }
    .col-action { width: 140px; text-align: center !important; }

    .faq-row {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .faq-row:hover { background: #fffbf7; }
    .faq-row td { padding: 0.875rem 1rem; vertical-align: top; }

    .row-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #fff7ed;
        color: #f97316;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .question-text {
        font-weight: 600;
        color: #1e293b;
        line-height: 1.45;
    }

    .answer-clamp {
        color: #475569;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

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
    .btn-edit   { background: #f59e0b; color: #fff; }
    .btn-delete { background: #ef4444; color: #fff; }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem !important;
        color: #94a3b8;
    }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }
    .empty-state p { font-size: 0.875rem; margin: 0; }

    /* Pagination */
    .faq-pagination {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
    }

    /* ── Modal ── */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(3px);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn {
        from { opacity:0; }
        to   { opacity:1; }
    }
    .modal-backdrop.hidden { display: none !important; }

    .modal-box {
        background: #fff;
        border-radius: 0.875rem;
        box-shadow: 0 20px 60px rgba(15,23,42,.2);
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.25s cubic-bezier(.4,0,.2,1);
    }
    @keyframes slideUp {
        from { opacity:0; transform:translateY(20px); }
        to   { opacity:1; transform:translateY(0); }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.125rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 5;
        border-radius: 0.875rem 0.875rem 0 0;
    }
    .modal-header-left {
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .modal-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--color-primary, #ea580c);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .modal-header h3 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .modal-close-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.75rem;
        transition: background 0.15s, color 0.15s;
    }
    .modal-close-btn:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }

    .modal-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1.125rem; }

    .form-group { display: flex; flex-direction: column; gap: 0.375rem; }
    .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    .form-label i { color: #f97316; font-size: 0.7rem; }

    .form-input,
    .form-textarea {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        font-size: 0.825rem;
        color: #1e293b;
        background: #fafafa;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
        resize: vertical;
        font-family: inherit;
    }
    .form-input:focus,
    .form-textarea:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249,115,22,.12);
        background: #fff;
    }
    .form-textarea { min-height: 130px; }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.625rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
        margin-top: 0.25rem;
    }
    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1.125rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        border-radius: 0.5rem;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
    }
    .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; color: #374151; }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1.375rem;
        background: var(--color-primary, #ea580c);
        color: #fff;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(249,115,22,.35);
        transition: box-shadow 0.2s, transform 0.15s;
    }
    .btn-submit:hover {
        box-shadow: 0 5px 16px rgba(249,115,22,.45);
        transform: translateY(-1px);
    }
    .btn-submit.update-mode {
        background: #16a34a;
        box-shadow: 0 3px 10px rgba(34,197,94,.35);
    }
    .btn-submit.update-mode:hover {
        box-shadow: 0 5px 16px rgba(34,197,94,.45);
    }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .faq-toolbar { flex-direction: column; align-items: flex-start; }
        .toolbar-right { width: 100%; }
        .search-input { width: 100%; }
        .faq-card-header { flex-direction: column; align-items: flex-start; }
    }
</style>

@endsection

@push('scripts')
<script>
function openModal(data = null) {
    const modal       = document.getElementById('faqModal');
    const form        = document.getElementById('faqForm');
    const modalTitle  = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');
    const pertanyaan  = document.getElementById('pertanyaan');
    const jawaban     = document.getElementById('jawaban');
    const submitBtn   = document.getElementById('submitBtn');
    const submitLabel = document.getElementById('submitLabel');

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (data) {
        // ── EDIT MODE ──
        modalTitle.textContent  = 'Edit Data FAQ';
        form.action             = `{{ url('faq') }}/${data.id}`;
        methodField.disabled    = false;
        methodField.value       = 'PUT';
        pertanyaan.value        = data.pertanyaan;
        jawaban.value           = data.jawaban;
        submitLabel.textContent = 'Update';
        submitBtn.classList.add('update-mode');

        // Focus pertanyaan
        setTimeout(() => pertanyaan.focus(), 100);
    } else {
        // ── TAMBAH MODE ──
        modalTitle.textContent  = 'Tambah Data FAQ';
        form.action             = `{{ route('faq.store') }}`;
        methodField.disabled    = true;
        methodField.value       = '';
        pertanyaan.value        = '';
        jawaban.value           = '';
        submitLabel.textContent = 'Simpan';
        submitBtn.classList.remove('update-mode');

        setTimeout(() => pertanyaan.focus(), 100);
    }
}

function closeModal() {
    document.getElementById('faqModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('faqModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// ── Search / filter ──
function filterTable() {
    const val  = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#faqTableBody .faq-row');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(val) ? '' : 'none';
    });
}

// ── Auto-dismiss alerts ──
setTimeout(() => {
    document.querySelectorAll('.alert-banner').forEach(el => {
        el.style.transition = 'opacity .4s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 4000);
</script>
@endpush