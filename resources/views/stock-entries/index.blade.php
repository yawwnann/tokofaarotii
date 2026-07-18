@extends('layouts.app')

@section('title', 'Entri Stok')
@section('subtitle', 'Riwayat penambahan dan pengurangan stok')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
    <div class="alert-inner"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
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

{{-- ── SUMMARY STATS ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <i class="fas fa-arrow-down" style="color:#22c55e;"></i>
        </div>
        <div>
            <p class="stat-label">Total Stok Masuk</p>
            <p class="stat-value" style="color:#22c55e;">{{ number_format($totalIn) }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;">
            <i class="fas fa-arrow-up" style="color:#ef4444;"></i>
        </div>
        <div>
            <p class="stat-label">Total Stok Keluar</p>
            <p class="stat-value" style="color:#ef4444;">{{ number_format($totalOut) }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <i class="fas fa-exchange-alt" style="color:#3b82f6;"></i>
        </div>
        <div>
            <p class="stat-label">Total Transaksi</p>
            <p class="stat-value" style="color:#3b82f6;">{{ number_format($totalTransactions) }}</p>
        </div>
    </div>
</div>

{{-- ── PAGE CARD ── --}}
<div class="stk-card">

    {{-- Card Header --}}
    <div class="stk-card-header">
        <div class="stk-card-title">
            <i class="fas fa-boxes"></i>
            <div>
                <h2>Entri Stok</h2>
                <p>Riwayat penambahan dan pengurangan stok produk</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i> Tambah Entri Stok
        </button>
    </div>

    {{-- Toolbar: Filter --}}
    <div class="stk-toolbar">
        <form method="GET" action="{{ route('stock-entries.index') }}" class="toolbar-filters">
            {{-- Filter Produk --}}
            <div class="filter-group">
                <label class="filter-label"><i class="fas fa-box"></i> Produk</label>
                <select name="product_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tipe --}}
            <div class="filter-group">
                <label class="filter-label"><i class="fas fa-exchange-alt"></i> Tipe</label>
                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                </select>
            </div>

            {{-- Filter Tanggal --}}
            <div class="filter-group">
                <label class="filter-label"><i class="far fa-calendar-alt"></i> Dari Tanggal</label>
                <input type="date" name="start_date"
                       value="{{ request('start_date') }}"
                       class="filter-select"
                       onchange="this.form.submit()">
            </div>

            {{-- Reset --}}
            @if(request()->hasAny(['product_id','type','start_date']))
            <div class="filter-group" style="justify-content:flex-end;align-self:flex-end;">
                <a href="{{ route('stock-entries.index') }}" class="btn-reset">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="stk-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th style="width:110px;">TANGGAL</th>
                    <th>PRODUK</th>
                    <th style="width:110px;text-align:center;">KUANTITAS</th>
                    <th>CATATAN</th>
                    <th class="col-action">AKSI</th>
                </tr>
            </thead>
            <tbody id="stkTableBody">
                @forelse($stockEntries as $index => $entry)
                <tr class="stk-row">
                    {{-- NO --}}
                    <td class="col-no">
                        <span class="row-num">{{ $stockEntries->firstItem() + $index }}</span>
                    </td>

                    {{-- TANGGAL --}}
                    <td>
                        <div class="date-main">{{ $entry->created_at->translatedFormat('d F Y') }}</div>
                        <div class="date-sub">{{ $entry->created_at->format('H:i') }} WIB</div>
                    </td>

                    {{-- PRODUK --}}
                    <td>
                        <div class="prod-name">{{ $entry->product->name ?? 'Produk tidak ditemukan' }}</div>
                        @if($entry->product->sku ?? false)
                            <div class="prod-sku">SKU: {{ $entry->product->sku }}</div>
                        @endif
                    </td>

                    {{-- KUANTITAS --}}
                    <td style="text-align:center;">
                        @if($entry->type == 'in')
                        <div class="qty-num qty-in">
                            +{{ number_format($entry->quantity) }}
                        </div>
                        @else
                        <div class="qty-num qty-out">
                            -{{ number_format($entry->quantity) }}
                        </div>
                        @endif
                        <div class="qty-stock">Stok Saat Ini: {{ number_format($entry->product->total_stok ?? 0) }}</div>
                    </td>

                    {{-- Catatan (Sesuai nama kolom database) --}}
                    <td>
                        <span class="note-text">{{ $entry->notes ?? $entry->note ?? '-' }}</span>
                    </td>

                    {{-- AKSI --}}
                    <td class="col-action">
                        <div class="action-btns">
                            <a href="{{ route('stock-entries.show', $entry) }}"
                               class="btn-action btn-detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('stock-entries.edit', $entry) }}"
                               class="btn-action btn-edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </a>
                            <form action="{{ route('stock-entries.destroy', $entry) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus entri stok ini?')">
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
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-boxes"></i>
                        <p>Belum ada entri stok</p>
                        <button onclick="openModal()" class="btn-primary" style="margin-top:.75rem;border:none;cursor:pointer;">
                            <i class="fas fa-plus"></i> Tambah Entri Pertama
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($stockEntries->hasPages())
    <div class="stk-pagination">
        <div class="pagination-info">
            Menampilkan {{ $stockEntries->firstItem() }}–{{ $stockEntries->lastItem() }} dari {{ $stockEntries->total() }} entri
        </div>
        <div>{{ $stockEntries->links() }}</div>
    </div>
    @endif

</div>

{{-- ══════════════════════════════════════════
     MODAL TAMBAH ENTRI STOK
══════════════════════════════════════════ --}}
<div id="stockModal" class="modal-backdrop hidden">
    <div class="modal-box">

        {{-- Modal Header --}}
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3>Tambah Entri Stok</h3>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form action="{{ route('stock-entries.store') }}" method="POST" class="modal-body">
            @csrf

            {{-- Produk --}}
            <div class="form-group">
                <label class="form-label" for="product_id">
                    <i class="fas fa-box"></i> Produk <span class="required">*</span>
                </label>
                <select name="product_id" id="product_id" required class="form-input">
                    <option value="">— Pilih Produk —</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                            @if($product->sku) ({{ $product->sku }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('product_id')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Tipe (Masuk / Keluar) --}}
            <div class="form-group">
                <label class="form-label"><i class="fas fa-exchange-alt"></i> Tipe Entri <span class="required">*</span></label>
                <div class="type-toggle-wrap">
                    <label class="type-toggle-opt">
                        <input type="radio" name="type" value="in" required {{ old('type', 'in') == 'in' ? 'checked' : '' }}>
                        <div class="toggle-label toggle-in">
                            <i class="fas fa-arrow-down"></i> Stok Masuk
                        </div>
                    </label>
                    <label class="type-toggle-opt">
                        <input type="radio" name="type" value="out" required {{ old('type') == 'out' ? 'checked' : '' }}>
                        <div class="toggle-label toggle-out">
                            <i class="fas fa-arrow-up"></i> Stok Keluar
                        </div>
                    </label>
                </div>
                @error('type')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Grid: Jumlah & Tanggal --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                {{-- Jumlah --}}
                <div class="form-group">
                    <label class="form-label" for="quantity">
                        <i class="fas fa-sort-numeric-up"></i> Jumlah <span class="required">*</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" required min="1"
                           value="{{ old('quantity') }}"
                           placeholder="0"
                           class="form-input">
                    @error('quantity')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal --}}
                <div class="form-group">
                    <label class="form-label" for="entry_date">
                        <i class="far fa-calendar-alt"></i> Tanggal <span class="required">*</span>
                    </label>
                    <input type="date" name="entry_date" id="entry_date" required
                           value="{{ old('entry_date', date('Y-m-d')) }}"
                           class="form-input">
                    @error('entry_date')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Catatan --}}
            <div class="form-group">
                <label class="form-label" for="note">
                    <i class="fas fa-sticky-note"></i> Catatan
                </label>
                <textarea name="note" id="note" rows="2"
                          placeholder="Opsional — keterangan tambahan..."
                          class="form-input" style="resize:vertical;">{{ old('note') }}</textarea>
                @error('note')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan Entri
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ── STYLES ── --}}
<style>
    /* ─ Alert ─ */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.625rem;margin-bottom:1.25rem;font-size:.85rem;font-weight:500;animation:slideDown .3s ease; }
    @keyframes slideDown { from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)} }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-danger  { background:#fef2f2;border:1px solid #fecaca;color:#991b1b; }
    .alert-inner   { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6;padding:2px;flex-shrink:0; }
    .alert-banner button:hover { opacity:1; }

    /* ─ Stats ─ */
    .stats-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.25rem; }
    .stat-card  { background:#fff;border-radius:.875rem;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(15,23,42,.06);padding:1rem 1.25rem;display:flex;align-items:center;gap:.875rem; }
    .stat-icon  { width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0; }
    .stat-label { font-size:.75rem;font-weight:500;color:#64748b;margin:0 0 2px; }
    .stat-value { font-size:1.5rem;font-weight:800;margin:0;line-height:1; }

    /* ─ Card ─ */
    .stk-card { background:#fff;border-radius:.875rem;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(15,23,42,.06);overflow:hidden; }

    /* ─ Card Header ─ */
    .stk-card-header { display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;gap:1rem;flex-wrap:wrap; }
    .stk-card-title { display:flex;align-items:center;gap:.75rem; }
    .stk-card-title > i { font-size:1.25rem;color:#f97316; }
    .stk-card-title h2 { font-size:.95rem;font-weight:700;color:#1e293b;margin:0; }
    .stk-card-title p  { font-size:.75rem;color:#94a3b8;margin:2px 0 0; }

    /* ─ Primary Button ─ */
    .btn-primary { display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:var(--color-primary,#ea580c);color:#fff;border:none;border-radius:.5rem;font-size:.825rem;font-weight:600;cursor:pointer;text-decoration:none;transition:box-shadow .2s,transform .15s;box-shadow:0 3px 10px rgba(249,115,22,.35);white-space:nowrap; }
    .btn-primary:hover { box-shadow:0 5px 16px rgba(249,115,22,.45);transform:translateY(-1px); }

    /* ─ Toolbar / Filters ─ */
    .stk-toolbar { padding:.875rem 1.5rem;border-bottom:1px solid #f8fafc;background:#fafafa; }
    .toolbar-filters { display:flex;align-items:flex-end;gap:.875rem;flex-wrap:wrap; }
    .filter-group { display:flex;flex-direction:column;gap:.3rem; }
    .filter-label { font-size:.72rem;font-weight:600;color:#64748b;display:flex;align-items:center;gap:.3rem; }
    .filter-label i { color:#f97316;font-size:.65rem; }
    .filter-select { border:1px solid #e2e8f0;border-radius:.4rem;padding:.4rem .75rem;font-size:.8rem;color:#374151;background:#fff;outline:none;transition:border-color .18s,box-shadow .18s;min-width:150px; }
    .filter-select:focus { border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.12); }
    .btn-reset { display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .875rem;border:1px solid #e2e8f0;background:#fff;color:#64748b;border-radius:.4rem;font-size:.78rem;font-weight:600;text-decoration:none;transition:background .15s; }
    .btn-reset:hover { background:#f8fafc;color:#374151; }

    /* ─ Table ─ */
    .table-wrap { overflow-x:auto; }
    .stk-table  { width:100%;border-collapse:collapse;font-size:.825rem; }
    .stk-table thead tr { background:#f8fafc;border-bottom:2px solid #f1f5f9; }
    .stk-table thead th { padding:.75rem 1rem;text-align:left;font-size:.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#64748b;white-space:nowrap; }
    .col-no     { width:52px; }
    .col-action { width:115px;text-align:center !important; }
    .stk-row    { border-bottom:1px solid #f1f5f9;transition:background .15s; }
    .stk-row:hover { background:#fffbf7; }
    .stk-row td { padding:.875rem 1rem;vertical-align:middle; }

    .row-num { display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#fff7ed;color:#f97316;font-size:.7rem;font-weight:700; }

    .date-main { font-size:.8rem;font-weight:600;color:#1e293b; }
    .date-sub  { font-size:.7rem;color:#94a3b8;margin-top:1px; }

    .prod-name { font-weight:600;color:#1e293b;font-size:.825rem; }
    .prod-sku  { font-size:.7rem;color:#94a3b8;margin-top:1px; }

    .type-badge { display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;border-radius:9999px;font-size:.7rem;font-weight:700; }
    .type-in    { background:#dcfce7;color:#15803d; }
    .type-out   { background:#fee2e2;color:#b91c1c; }

    .qty-num   { font-size:.95rem;font-weight:800;line-height:1; }
    .qty-in    { color:#16a34a; }
    .qty-out   { color:#dc2626; }
    .qty-stock { font-size:.7rem;color:#94a3b8;margin-top:2px; }

    .note-text { font-size:.8rem;color:#475569;font-style:italic; }

    /* ─ Action Buttons ─ */
    .action-btns { display:flex;flex-direction:column;gap:5px;align-items:center; }
    .btn-action { display:inline-flex;align-items:center;justify-content:center;gap:.3rem;padding:.3rem .75rem;border:none;border-radius:.35rem;font-size:.72rem;font-weight:600;cursor:pointer;transition:filter .15s,transform .1s;white-space:nowrap;width:85px;text-decoration:none; }
    .btn-action:hover { filter:brightness(1.1);transform:translateY(-1px); }
    .btn-detail { background:#2563eb;color:#fff; }
    .btn-edit   { background:#f59e0b;color:#fff; }
    .btn-delete { background:#ef4444;color:#fff; }

    /* ─ Empty ─ */
    .empty-state { text-align:center;padding:3rem 1rem !important;color:#94a3b8; }
    .empty-state i { font-size:2.5rem;display:block;margin-bottom:.5rem; }
    .empty-state p { font-size:.875rem;margin:0; }

    /* ─ Pagination ─ */
    .stk-pagination { display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-top:1px solid #f1f5f9;background:#fafafa;flex-wrap:wrap;gap:.5rem; }
    .pagination-info { font-size:.8rem;color:#64748b; }

    /* ─ Modal ─ */
    .modal-backdrop { position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(3px);z-index:50;display:flex;align-items:center;justify-content:center;padding:1rem;animation:fadeIn .2s ease; }
    @keyframes fadeIn { from{opacity:0}to{opacity:1} }
    .modal-backdrop.hidden { display:none !important; }
    .modal-box { background:#fff;border-radius:.875rem;box-shadow:0 20px 60px rgba(15,23,42,.2);width:100%;max-width:520px;max-height:90vh;overflow-y:auto;animation:slideUp .25s cubic-bezier(.4,0,.2,1); }
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
    .field-error { font-size:.75rem;color:#ef4444;margin:2px 0 0; }

    /* ─ Type Toggle ─ */
    .type-toggle-wrap { display:grid;grid-template-columns:1fr 1fr;gap:.625rem; }
    .type-toggle-opt input[type="radio"] { display:none; }
    .toggle-label { display:flex;align-items:center;justify-content:center;gap:.4rem;padding:.55rem 1rem;border-radius:.5rem;border:1.5px solid #e2e8f0;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .18s;background:#fafafa;color:#64748b; }
    .toggle-label:hover { border-color:#cbd5e1; }
    .type-toggle-opt input[type="radio"]:checked + .toggle-in  { background:#dcfce7;border-color:#86efac;color:#15803d; }
    .type-toggle-opt input[type="radio"]:checked + .toggle-out { background:#fee2e2;border-color:#fca5a5;color:#b91c1c; }

    .modal-footer { display:flex;justify-content:flex-end;gap:.625rem;padding-top:1rem;border-top:1px solid #f1f5f9;margin-top:.25rem; }
    .btn-cancel { display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1.125rem;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;border-radius:.5rem;font-size:.825rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s; }
    .btn-cancel:hover { background:#f8fafc;border-color:#cbd5e1;color:#374151; }
    .btn-submit { display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1.375rem;background:var(--color-primary,#ea580c);color:#fff;border:none;border-radius:.5rem;font-size:.825rem;font-weight:600;cursor:pointer;box-shadow:0 3px 10px rgba(249,115,22,.35);transition:box-shadow .2s,transform .15s; }
    .btn-submit:hover { box-shadow:0 5px 16px rgba(249,115,22,.45);transform:translateY(-1px); }

    @media(max-width:768px) {
        .stats-grid { grid-template-columns:1fr; }
        .toolbar-filters { flex-direction:column;align-items:stretch; }
        .filter-select { min-width:unset;width:100%; }
        .stk-card-header { flex-direction:column;align-items:flex-start; }
    }
</style>

@endsection

@push('scripts')
<script>
    function openModal() {
        document.getElementById('stockModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('product_id').focus(), 100);
        updateTypeStyle();
    }

    function closeModal() {
        document.getElementById('stockModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function updateTypeStyle() {
        // visual sudah ditangani CSS :checked, tidak perlu JS tambahan
    }

    // Backdrop click
    document.getElementById('stockModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Buka modal otomatis jika ada error validasi
    @if(isset($errors) && $errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal());
    @endif

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-banner').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 4000);
</script>
@endpush