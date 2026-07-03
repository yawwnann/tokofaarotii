@extends('layouts.app')

@section('title', 'Kelola User')
@section('subtitle', 'Manajemen hak akses dan akun pengguna sistem')

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
        <h3 class="toolbar-title">Daftar Pengguna</h3>
    </div>
    <div class="toolbar-right">
        <button onclick="toggleModal('modal-tambah')" class="btn-primary">
            <i class="fas fa-user-plus"></i> Tambah User
        </button>
    </div>
</div>

{{-- ── TABLE ── --}}
<div class="table-container">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>NAMA PENGGUNA</th>
                    <th>EMAIL</th>
                    <th style="text-align:center;">ROLE</th>
                    <th style="text-align:center;">BERGABUNG PADA</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="td-user">
                            <div class="user-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="user-info">
                                <span class="u-name">{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="self-tag">SAYA</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="u-email">{{ $user->email }}</span>
                    </td>
                    <td style="text-align:center;">
                        @switch($user->role)
                            @case('admin_master')
                                <span class="role-badge badge-red">ADMIN MASTER</span>
                                @break
                            @case('pemilik')
                                <span class="role-badge badge-orange">PEMILIK TOKO</span>
                                @break
                            @case('kasir')
                                <span class="role-badge badge-orange" style="background:#fef3c7; color:#d97706;">KASIR</span>
                                @break
                            @default
                                <span class="role-badge badge-gray">PELANGGAN</span>
                        @endswitch
                    </td>
                    <td style="text-align:center;">
                        <span class="u-date">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            {{-- Mengirimkan data via data-attribute agar JavaScript aman dari special character --}}
                            <button 
                                type="button"
                                class="btn-action btn-edit btn-edit-user" 
                                title="Edit User"
                                data-user="{{ json_encode($user) }}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            
                            {{-- Admin master / Pemilik tidak boleh menghapus dirinya sendiri & Pemilik toko tidak bisa dihapus di sini --}}
                            @if($user->id !== auth()->id() && $user->role !== 'pemilik' && $user->role !== 'admin_master')
                            <form action="{{ route('kelola-user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Hapus User">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="pagination-wrap" style="padding: 1rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- ── MODAL TAMBAH (FIXED AUTOFILL) ── --}}
<div id="modal-tambah" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Tambah User Baru</h3>
            <button onclick="toggleModal('modal-tambah')" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('kelola-user.store') }}" method="POST" class="modal-form" autocomplete="off">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Fitri Ria" autocomplete="none">
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" required placeholder="fitri@gmail.com" autocomplete="none">
            </div>
            <div class="form-group">
                <label>Role / Hak Akses</label>
                <select name="role" required class="form-input">
                    <option value="pengguna">Pelanggan</option>
                    <option value="pemilik">Pemilik Toko</option>
                    <option value="kasir">Kasir</option>
                    <option value="admin_master">Admin Master</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••" autocomplete="new-password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="toggleModal('modal-tambah')" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">Simpan User</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL EDIT (FIXED LOGIC & DROPDOWN) ── --}}
<div id="modal-edit" class="modal-overlay hidden">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Edit User</h3>
            <button onclick="toggleModal('modal-edit')" class="btn-close-modal"><i class="fas fa-times"></i></button>
        </div>
        <form id="form-edit" method="POST" class="modal-form" autocomplete="off">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit-name" required autocomplete="none">
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" id="edit-email" required autocomplete="none">
            </div>
            
            <div class="form-group" id="edit-role-wrapper">
                <label>Role / Hak Akses</label>
                <select name="role" id="edit-role" required class="form-input">
                    <option value="pengguna">Pelanggan</option>
                    <option value="pemilik">Pemilik Toko</option>
                    <option value="kasir">Kasir</option>
                    <option value="admin_master">Admin Master</option>
                </select>
                <p id="edit-role-warning" class="hidden" style="font-size: 0.75rem; color: #ef4444; margin-top: 4px;">
                    *Anda tidak bisa mengubah role Anda sendiri untuk mencegah hilangnya akses sistem.
                </p>
            </div>

            {{-- Section Ganti Password ini akan di sembunyikan/ditampilkan lewat JS secara dinamis --}}
            <div id="edit-password-section">
                <div class="divider"><span>Ganti Kata Sandi (Opsional)</span></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kata Sandi Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Kosongkan jika tidak diubah" autocomplete="new-password">
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="toggleModal('modal-edit')" class="btn-cancel">Batal</button>
                <button type="submit" class="btn-submit">Update User</button>
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
    .self-tag { background: #f0fdf4; color: #16a34a; font-size: .6rem; font-weight: 800; padding: .1rem .4rem; border-radius: 4px; width: fit-content; margin-top: 2px; }
    
    .u-email { font-size: .85rem; color: #64748b; font-weight: 500; }
    .u-date { font-size: .85rem; color: #94a3b8; font-weight: 600; }

    .role-badge { display: inline-block; padding: .25rem .75rem; border-radius: 9999px; font-size: .65rem; font-weight: 800; letter-spacing: .05em; }
    .badge-red { background: #fee2e2; color: #dc2626; }
    .badge-orange { background: #ffedd5; color: #c2410c; }
    .badge-gray { background: #f1f5f9; color: #475569; }

    /* Action buttons standard */
    .action-btns { display: flex; flex-direction: column; gap: 5px; align-items: center; }
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
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    .form-group label { display: block; font-size: .8rem; font-weight: 700; color: #475569; margin-bottom: .5rem; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: .625rem 1rem; border: 1.5px solid #e2e8f0; border-radius: .625rem; font-size: .85rem; outline: none; transition: all .2s; box-sizing: border-box; }
    .form-group input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, .1); }

    .divider { display: flex; align-items: center; margin: 1.5rem 0; color: #cbd5e1; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
    .divider span { padding: 0 1rem; font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }

    .modal-footer { display: flex; justify-content: flex-end; gap: .75rem; margin-top: 1.5rem; }
    .btn-cancel { background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; padding: .625rem 1.25rem; border-radius: .625rem; font-size: .85rem; font-weight: 700; cursor: pointer; }
    .btn-submit { background: #f97316; border: none; color: #fff; padding: .625rem 1.25rem; border-radius: .625rem; font-size: .85rem; font-weight: 700; cursor: pointer; transition: all .2s; }
    .btn-submit:hover { background: #ea580c; }

    .hidden { display: none !important; }

    /* ── Alert ── */
    .alert-banner { display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;padding:.875rem 1.125rem;border-radius:.75rem;margin-bottom:1.5rem;font-size:.85rem;font-weight:600; }
    .alert-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
    .alert-danger { background:#fef2f2;border:1px solid #fee2e2;color:#991b1b; }
    .alert-inner { display:flex;align-items:flex-start;gap:.5rem; }
    .alert-banner button { background:none;border:none;cursor:pointer;color:inherit;opacity:.6; }
</style>

<script>
    // Menyimpan ID user yang sedang login saat ini dari Auth Laravel
    const currentUserId = {{ auth()->id() }};

    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    // Menggunakan Event Listener untuk memproses klik edit agar lebih clean & aman
    document.querySelectorAll('.btn-edit-user').forEach(button => {
        button.addEventListener('click', function() {
            const user = JSON.parse(this.getAttribute('data-user'));
            
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-email').value = user.email;
            document.getElementById('form-edit').action = `{{ url('kelola-user') }}/${user.id}`;
            
            const roleSelect = document.getElementById('edit-role');
            const roleWarning = document.getElementById('edit-role-warning');
            const passwordSection = document.getElementById('edit-password-section');

            // Sinkronisasi data role ke dropdown dengan aman
            if(user.role === 'pelanggan') {
                roleSelect.value = 'pengguna';
            } else {
                roleSelect.value = user.role;
            }
            
            // ── VALIDASI UTAMA: LOGIKA KATA SANDI & AKSES ROLE ──
            if (user.id === currentUserId) {
                // Jika mengedit akun sendiri: Form password MUNCUL, dropdown role DIKUNCI
                passwordSection.classList.remove('hidden');
                
                roleSelect.disabled = true;
                roleWarning.classList.remove('hidden');
                
                if(!document.getElementById('hidden-role-input')) {
                    let hiddenRole = document.createElement('input');
                    hiddenRole.setAttribute('type', 'hidden');
                    hiddenRole.setAttribute('name', 'role');
                    hiddenRole.setAttribute('id', 'hidden-role-input');
                    hiddenRole.setAttribute('value', roleSelect.value);
                    document.getElementById('form-edit').appendChild(hiddenRole);
                }
            } else {
                // Jika mengedit akun orang lain: Form password HILANG, dropdown role DIBUKA
                passwordSection.classList.add('hidden');
                
                roleSelect.disabled = false;
                roleWarning.classList.add('hidden');
                
                const hiddenInput = document.getElementById('hidden-role-input');
                if(hiddenInput) hiddenInput.remove();
            }

            toggleModal('modal-edit');
        });
    });

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
