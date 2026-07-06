@extends('layouts.app')

@section('title', 'Kelola Pegawai')

@section('content')

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="alert-banner alert-success">
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
<div class="pgw-card">

    {{-- Card Header --}}
    <div class="pgw-card-header">
        <div class="pgw-card-title">
            <i class="fas fa-users-cog"></i>
            <div>
                <h2>Data Pegawai</h2>
                <p>Kelola data pegawai yang terdaftar dalam sistem</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i>
            Tambah Pegawai
        </button>
    </div>

    {{-- Toolbar: Search --}}
    <div class="pgw-toolbar">
        <div class="toolbar-left">
            <span class="total-badge">
                <i class="fas fa-users"></i>
                {{ $pegawais->count() }} Pegawai
            </span>
        </div>
        <div class="toolbar-right">
            <label class="search-label">Cari:</label>
            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput"
                       placeholder="Nama atau posisi..."
                       class="search-input"
                       onkeyup="filterTable()">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="pgw-table">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-foto">FOTO</th>
                    <th class="col-nama">NAMA</th>
                    <th class="col-posisi">POSISI</th>
                    <th class="col-action">AKSI</th>
                </tr>
            </thead>
            <tbody id="pgwTableBody">
                @forelse($pegawais as $p)
                <tr class="pgw-row">
                    <td class="col-no">
                        <span class="row-num">{{ $loop->iteration }}</span>
                    </td>
                    <td class="col-foto">
                        <img src="{{ $p->foto ? asset('storage/'.$p->foto) : 'https://ui-avatars.com/api/?name='.urlencode($p->nama).'&background=f97316&color=fff&bold=true' }}"
                             class="pgw-avatar"
                             alt="{{ $p->nama }}">
                    </td>
                    <td class="col-nama">
                        <span class="nama-text">{{ $p->nama }}</span>
                    </td>
                    <td class="col-posisi">
                        <span class="posisi-badge">{{ $p->posisi }}</span>
                    </td>
                    <td class="col-action">
                        <div class="action-btns">
                            <button onclick='openModal(@json($p))' class="btn-action btn-edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                            <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
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
                    <td colspan="5" class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>Belum ada data pegawai</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ══════════════════════════════════════════
     MODAL TAMBAH / EDIT
══════════════════════════════════════════ --}}
<div id="pegawaiModal" class="modal-backdrop hidden">
    <div class="modal-box">

        {{-- Modal Header --}}
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 id="modalTitle">Tambah Pegawai</h3>
            </div>
            <button class="modal-close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="pegawaiForm"
              method="POST"
              enctype="multipart/form-data"
              class="modal-body">
            @csrf
            <input type="hidden" id="methodField" name="_method">

            {{-- Foto Preview --}}
            <div class="foto-upload-wrap">
                <div class="foto-preview-container" onclick="document.getElementById('fotoInput').click()">
                    <img id="preview"
                         src="https://ui-avatars.com/api/?name=User&background=f97316&color=fff&bold=true&size=80"
                         class="foto-preview"
                         alt="Preview">
                    <div class="foto-overlay">
                        <i class="fas fa-camera"></i>
                        <span>Ganti Foto</span>
                    </div>
                </div>
                <input type="file"
                       id="fotoInput"
                       name="foto"
                       accept="image/*"
                       class="hidden-file-input"
                       onchange="previewImage(this)">
                <p class="foto-hint">Klik foto untuk mengubah<br><small>JPG, PNG — maks. 2MB</small></p>
            </div>

            {{-- Nama --}}
            <div class="form-group">
                <label class="form-label" for="nama">
                    <i class="fas fa-user"></i> Nama Lengkap
                </label>
                <input type="text"
                       name="nama"
                       id="nama"
                       required
                       placeholder="Masukkan nama lengkap..."
                       class="form-input">
            </div>

            {{-- Posisi --}}
            <div class="form-group">
                <label class="form-label" for="posisi">
                    <i class="fas fa-briefcase"></i> Posisi / Jabatan
                </label>
                <input type="text"
                       name="posisi"
                       id="posisi"
                       required
                       placeholder="Contoh: Manager, Staff, Kasir..."
                       class="form-input">
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
        gap: .75rem;
        padding: .875rem 1.125rem;
        border-radius: .625rem;
        margin-bottom: 1.25rem;
        font-size: .85rem;
        font-weight: 500;
        animation: slideDown .3s ease;
    }
    @keyframes slideDown {
        from { opacity:0; transform:translateY(-8px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .alert-danger  { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
    .alert-inner   { display:flex; align-items:flex-start; gap:.5rem; }
    .alert-banner button { background:none; border:none; cursor:pointer; color:inherit; opacity:.6; padding:2px; flex-shrink:0; }
    .alert-banner button:hover { opacity:1; }

    /* ── Page Card ── */
    .pgw-card {
        background: #fff;
        border-radius: .875rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(15,23,42,.06);
        overflow: hidden;
    }

    /* ── Card Header ── */
    .pgw-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .pgw-card-title { display:flex; align-items:center; gap:.75rem; }
    .pgw-card-title > i { font-size:1.25rem; color:#f97316; }
    .pgw-card-title h2 { font-size:.95rem; font-weight:700; color:#1e293b; margin:0; }
    .pgw-card-title p  { font-size:.75rem; color:#94a3b8; margin:2px 0 0; }

    /* ── Button Primary ── */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem 1rem;
        background: linear-gradient(135deg,#f97316,#ea580c);
        color: #fff;
        border: none;
        border-radius: .5rem;
        font-size: .825rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: box-shadow .2s, transform .15s;
        box-shadow: 0 3px 10px rgba(249,115,22,.35);
        white-space: nowrap;
    }
    .btn-primary:hover { box-shadow:0 5px 16px rgba(249,115,22,.45); transform:translateY(-1px); }

    /* ── Toolbar ── */
    .pgw-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .875rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        gap: 1rem;
        flex-wrap: wrap;
        background: #fafafa;
    }
    .toolbar-left  { display:flex; align-items:center; gap:.5rem; }
    .toolbar-right { display:flex; align-items:center; gap:.5rem; }

    .total-badge {
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        padding: .3rem .75rem;
        background: #fff7ed;
        color: #f97316;
        border: 1px solid #fed7aa;
        border-radius: 99px;
        font-size: .75rem;
        font-weight: 600;
    }

    .search-label { font-size:.8rem; color:#64748b; font-weight:500; }
    .search-wrap  { position:relative; }
    .search-icon  { position:absolute; left:.625rem; top:50%; transform:translateY(-50%); font-size:.7rem; color:#94a3b8; }
    .search-input {
        padding: .4rem .75rem .4rem 1.875rem;
        border: 1px solid #e2e8f0;
        border-radius: .4rem;
        font-size: .8rem;
        color: #1e293b;
        background: #fff;
        outline: none;
        width: 200px;
        transition: border-color .18s, box-shadow .18s;
    }
    .search-input:focus { border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,.12); }

    /* ── Table ── */
    .table-wrap { overflow-x:auto; }
    .pgw-table  { width:100%; border-collapse:collapse; font-size:.825rem; }
    .pgw-table thead tr { background:#f8fafc; border-bottom:2px solid #f1f5f9; }
    .pgw-table thead th {
        padding: .75rem 1rem;
        text-align: left;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #64748b;
        white-space: nowrap;
    }
    .col-no     { width:52px; }
    .col-foto   { width:72px; text-align:center !important; }
    .col-action { width:150px; text-align:center !important; }

    .pgw-row { border-bottom:1px solid #f1f5f9; transition:background .15s; }
    .pgw-row:hover { background:#fffbf7; }
    .pgw-row td { padding:.875rem 1rem; vertical-align:middle; }

    .row-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px; height: 26px;
        border-radius: 50%;
        background: #fff7ed;
        color: #f97316;
        font-size: .7rem;
        font-weight: 700;
    }

    .pgw-avatar {
        width: 40px; height: 40px;
        border-radius: .5rem;
        object-fit: cover;
        border: 2px solid #f1f5f9;
        display: block;
        margin: 0 auto;
        transition: transform .2s;
    }
    .pgw-row:hover .pgw-avatar { transform: scale(1.08); }

    .nama-text  { font-weight:600; color:#1e293b; }
    .posisi-badge {
        display: inline-block;
        padding: .2rem .625rem;
        background: #f1f5f9;
        color: #475569;
        border-radius: 99px;
        font-size: .72rem;
        font-weight: 600;
    }

    /* Action buttons */
    .action-btns { display:flex; flex-direction:column; gap:5px; align-items:center; }
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        padding: .3rem .75rem;
        border: none;
        border-radius: .35rem;
        font-size: .72rem;
        font-weight: 600;
        cursor: pointer;
        transition: filter .15s, transform .1s;
        white-space: nowrap;
        width: 80px;
    }
    .btn-action:hover { filter:brightness(1.1); transform:translateY(-1px); }
    .btn-edit   { background:#f59e0b; color:#fff; }
    .btn-delete { background:#ef4444; color:#fff; }

    /* Empty */
    .empty-state { text-align:center; padding:3rem 1rem !important; color:#94a3b8; }
    .empty-state i { font-size:2.5rem; display:block; margin-bottom:.5rem; }
    .empty-state p { font-size:.875rem; margin:0; }

    /* ── Modal ── */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.5);
        backdrop-filter: blur(3px);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        animation: fadeIn .2s ease;
    }
    @keyframes fadeIn { from{opacity:0} to{opacity:1} }
    .modal-backdrop.hidden { display:none !important; }

    .modal-box {
        background: #fff;
        border-radius: .875rem;
        box-shadow: 0 20px 60px rgba(15,23,42,.2);
        width: 100%;
        max-width: 480px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp .25s cubic-bezier(.4,0,.2,1);
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
        position: sticky; top:0;
        background: #fff;
        z-index: 5;
        border-radius: .875rem .875rem 0 0;
    }
    .modal-header-left  { display:flex; align-items:center; gap:.625rem; }
    .modal-icon {
        width:32px; height:32px;
        border-radius:8px;
        background: linear-gradient(135deg,#f97316,#ea580c);
        display:flex; align-items:center; justify-content:center;
        color:#fff; font-size:.8rem; flex-shrink:0;
    }
    .modal-header h3 { font-size:.95rem; font-weight:700; color:#1e293b; margin:0; }
    .modal-close-btn {
        width:30px; height:30px;
        border-radius:50%;
        border:1px solid #e2e8f0;
        background:#f8fafc;
        color:#64748b;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; font-size:.75rem;
        transition: background .15s, color .15s;
    }
    .modal-close-btn:hover { background:#fee2e2; color:#ef4444; border-color:#fca5a5; }

    .modal-body { padding:1.5rem; display:flex; flex-direction:column; gap:1.125rem; }

    /* Foto upload */
    .foto-upload-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .5rem;
        padding: 1rem;
        background: #fafafa;
        border: 1.5px dashed #e2e8f0;
        border-radius: .75rem;
        transition: border-color .2s;
    }
    .foto-upload-wrap:hover { border-color:#f97316; }
    .foto-preview-container {
        position: relative;
        cursor: pointer;
        border-radius: .625rem;
        overflow: hidden;
        width: 80px; height: 80px;
        flex-shrink: 0;
    }
    .foto-preview {
        width: 80px; height: 80px;
        object-fit: cover;
        border-radius: .625rem;
        display: block;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
    }
    .foto-overlay {
        position: absolute;
        inset: 0;
        background: rgba(249,115,22,.75);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: .65rem;
        font-weight: 600;
        opacity: 0;
        transition: opacity .2s;
        gap: 3px;
    }
    .foto-overlay i    { font-size: .9rem; }
    .foto-preview-container:hover .foto-overlay { opacity:1; }
    .hidden-file-input { display:none; }
    .foto-hint { font-size:.72rem; color:#94a3b8; text-align:center; line-height:1.5; }
    .foto-hint small { color:#cbd5e1; }

    /* Form */
    .form-group  { display:flex; flex-direction:column; gap:.375rem; }
    .form-label  { font-size:.8rem; font-weight:600; color:#374151; display:flex; align-items:center; gap:.375rem; }
    .form-label i { color:#f97316; font-size:.7rem; }
    .form-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: .5rem;
        padding: .625rem .875rem;
        font-size: .825rem;
        color: #1e293b;
        background: #fafafa;
        outline: none;
        transition: border-color .18s, box-shadow .18s, background .18s;
        font-family: inherit;
    }
    .form-input:focus { border-color:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,.12); background:#fff; }

    /* Modal footer */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: .625rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
        margin-top: .25rem;
    }
    .btn-cancel {
        display: inline-flex; align-items:center; gap:.375rem;
        padding: .5rem 1.125rem;
        border: 1.5px solid #e2e8f0;
        background: #fff; color: #64748b;
        border-radius: .5rem; font-size:.825rem; font-weight:600;
        cursor:pointer; transition: background .15s, border-color .15s;
    }
    .btn-cancel:hover { background:#f8fafc; border-color:#cbd5e1; color:#374151; }
    .btn-submit {
        display: inline-flex; align-items:center; gap:.375rem;
        padding: .5rem 1.375rem;
        background: linear-gradient(135deg,#f97316,#ea580c);
        color: #fff; border:none; border-radius:.5rem;
        font-size:.825rem; font-weight:600; cursor:pointer;
        box-shadow: 0 3px 10px rgba(249,115,22,.35);
        transition: box-shadow .2s, transform .15s;
    }
    .btn-submit:hover { box-shadow:0 5px 16px rgba(249,115,22,.45); transform:translateY(-1px); }
    .btn-submit.update-mode {
        background: linear-gradient(135deg,#22c55e,#16a34a);
        box-shadow: 0 3px 10px rgba(34,197,94,.35);
    }
    .btn-submit.update-mode:hover { box-shadow:0 5px 16px rgba(34,197,94,.45); }

    /* Responsive */
    @media (max-width:640px) {
        .pgw-toolbar    { flex-direction:column; align-items:flex-start; }
        .toolbar-right  { width:100%; }
        .search-input   { width:100%; }
        .pgw-card-header { flex-direction:column; align-items:flex-start; }
    }
</style>

@endsection

@push('scripts')
<script>
// ── openModal (fungsi asli dipertahankan + diperluas) ──────────
function openModal(data = null) {
    const modal       = document.getElementById('pegawaiModal');
    const form        = document.getElementById('pegawaiForm');
    const submitBtn   = document.getElementById('submitBtn');
    const submitLabel = document.getElementById('submitLabel');

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (data) {
        document.getElementById('modalTitle').innerText = 'Edit Pegawai';

        form.action = `/pegawai/${data.id}`;
        document.getElementById('methodField').value = 'PUT';

        document.getElementById('nama').value   = data.nama;
        document.getElementById('posisi').value = data.posisi;

        document.getElementById('preview').src =
            data.foto
            ? `{{ asset('storage') }}/${data.foto}`
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(data.nama)}&background=f97316&color=fff&bold=true`;

        submitLabel.textContent = 'Update';
        submitBtn.classList.add('update-mode');

    } else {
        document.getElementById('modalTitle').innerText = 'Tambah Pegawai';

        form.action = `/pegawai`;
        document.getElementById('methodField').value = '';

        form.reset();

        document.getElementById('preview').src =
            'https://ui-avatars.com/api/?name=User&background=f97316&color=fff&bold=true&size=80';

        submitLabel.textContent = 'Simpan';
        submitBtn.classList.remove('update-mode');
    }

    setTimeout(() => document.getElementById('nama').focus(), 100);
}

// ── closeModal ─────────────────────────────────────────────────
function closeModal() {
    document.getElementById('pegawaiModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── previewImage (fungsi asli dipertahankan) ───────────────────
function previewImage(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Close on backdrop click ────────────────────────────────────
document.getElementById('pegawaiModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// ── Close on ESC ───────────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// ── Search / filter ────────────────────────────────────────────
function filterTable() {
    const val  = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#pgwTableBody .pgw-row');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
}

// ── Auto-dismiss alerts ────────────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.alert-banner').forEach(el => {
        el.style.transition = 'opacity .4s';
        el.style.opacity    = '0';
        setTimeout(() => el.remove(), 400);
    });
}, 4000);
</script>
@endpush