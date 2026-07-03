@extends('layouts.frontend')

@section('title','Alamat Saya')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Alamat Saya</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahAlamatModal">
            <i class="bi bi-plus-circle"></i> Tambah Alamat
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($addresses as $address)
        <div class="card mb-3 shadow-sm border-{{ $address->is_default ? 'success' : 'light' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>
                            {{ $address->label }}
                            @if($address->is_default)
                                <span class="badge bg-success">Utama</span>
                            @endif
                        </h5>
                        <strong>{{ $address->receiver_name }}</strong><br>
                        {{ $address->phone }}
                        <p class="mt-2 mb-0">{{ $address->address }}</p>
                        <small>
                            {{ $address->village ? $address->village . ',' : '' }}
                            {{ $address->district }},
                            {{ $address->city }},
                            {{ $address->province }}
                            {{ $address->postal_code }}
                        </small>
                    </div>
                    
                    <div class="d-flex flex-column align-items-end">
                        <button class="btn btn-sm btn-outline-secondary mb-2" data-bs-toggle="modal" data-bs-target="#editAlamatModal{{ $address->id }}">
                            Edit
                        </button>
                        
                        <form action="{{ route('customer.address.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger mb-2">Hapus</button>
                        </form>
                        
                        @if(!$address->is_default)
                            <form action="{{ route('customer.address.default', $address->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">Jadikan Utama</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editAlamatModal{{ $address->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('customer.address.update', $address->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Alamat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Label Alamat (Rumah, Kantor, dll)</label>
                                <input type="text" name="label" class="form-control" value="{{ $address->label }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Nama Penerima</label>
                                <input type="text" name="receiver_name" class="form-control" value="{{ $address->receiver_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Nomor HP</label>
                                <input type="text" name="phone" class="form-control" value="{{ $address->phone }}" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Provinsi</label>
                                    <input type="text" name="province" class="form-control" value="{{ $address->province }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>Kota/Kabupaten</label>
                                    <input type="text" name="city" class="form-control" value="{{ $address->city }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Kecamatan</label>
                                    <input type="text" name="district" class="form-control" value="{{ $address->district }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>Kode Pos</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ $address->postal_code }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Alamat Lengkap</label>
                                <textarea name="address" class="form-control" rows="3" required>{{ $address->address }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada alamat.</div>
    @endforelse

</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahAlamatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.address.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Alamat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Label Alamat (Rumah, Kantor, dll)</label>
                        <input type="text" name="label" class="form-control" placeholder="Rumah" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Penerima</label>
                        <input type="text" name="receiver_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nomor HP</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Provinsi</label>
                            <input type="text" name="province" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Kota/Kabupaten</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Kecamatan</label>
                            <input type="text" name="district" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Alamat Lengkap</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Nama jalan, gedung, no. rumah" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection