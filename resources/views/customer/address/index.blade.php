@extends('layouts.public')

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
                            @if($address->village){{ $address->village }}, @endif
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
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Label Alamat (Rumah, Kantor, dll)</label>
                                    <input type="text" name="label" class="form-control" value="{{ $address->label }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>Nama Penerima</label>
                                    <input type="text" name="receiver_name" class="form-control" value="{{ $address->receiver_name }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Nomor HP</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $address->phone }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>Kode Pos</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ $address->postal_code }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Provinsi</label>
                                    <input type="hidden" name="province" class="province-name" value="{{ $address->province }}">
                                    <select name="province_id" class="form-select province-select" data-selected="{{ $address->province_id }}" required>
                                        <option value="">Pilih Provinsi...</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>Kota/Kabupaten</label>
                                    <input type="hidden" name="city" class="city-name" value="{{ $address->city }}">
                                    <select name="city_id" class="form-select city-select" data-selected="{{ $address->city_id }}" required disabled>
                                        <option value="">Pilih Kota...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label>Kecamatan</label>
                                    <input type="hidden" name="district" class="district-name" value="{{ $address->district }}">
                                    <select name="district_id" class="form-select district-select" data-selected="{{ $address->district_id }}" required disabled>
                                        <option value="">Pilih Kecamatan...</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label>Desa/Kelurahan <small class="text-muted">(opsional)</small></label>
                                    <input type="hidden" name="village" class="village-name" value="{{ $address->village }}">
                                    <select name="village_id" class="form-select village-select" data-selected="{{ $address->village_id }}" disabled>
                                        <option value="">Pilih Desa...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Alamat Lengkap (Nama jalan, gedung, no. rumah)</label>
                                <textarea name="address" class="form-control" rows="2" required>{{ $address->address }}</textarea>
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
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Label Alamat (Rumah, Kantor, dll)</label>
                            <input type="text" name="label" class="form-control" placeholder="Rumah" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Nama Penerima</label>
                            <input type="text" name="receiver_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Nomor HP</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Provinsi</label>
                            <input type="hidden" name="province" class="province-name">
                            <select name="province_id" class="form-select province-select" required>
                                <option value="">Pilih Provinsi...</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Kota/Kabupaten</label>
                            <input type="hidden" name="city" class="city-name">
                            <select name="city_id" class="form-select city-select" required disabled>
                                <option value="">Pilih Kota...</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Kecamatan</label>
                            <input type="hidden" name="district" class="district-name">
                            <select name="district_id" class="form-select district-select" required disabled>
                                <option value="">Pilih Kecamatan...</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Desa/Kelurahan <small class="text-muted">(opsional)</small></label>
                            <input type="hidden" name="village" class="village-name">
                            <select name="village_id" class="form-select village-select" disabled>
                                <option value="">Pilih Desa...</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Alamat Lengkap (Nama jalan, gedung, no. rumah)</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
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

@push('styles')
<style>
    .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border-color: #cbd5e1;
    }
    .form-select:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.25rem rgba(0, 74, 173, 0.25);
    }
    .form-select:disabled {
        background-color: #f1f5f9;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    async function fetchData(url) {
        const response = await fetch(url);
        if (!response.ok) throw new Error('Gagal memuat data');
        return response.json();
    }

    async function loadProvinces(modalElement, selectedProvinceId = null) {
        const provinceSelect = modalElement.querySelector('.province-select');

        if (provinceSelect.dataset.loaded === 'true') return;

        provinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';
        provinceSelect.disabled = true;

        try {
            const provinces = await fetchData('/api/wilayah/provinces');

            provinceSelect.innerHTML = '<option value="">Pilih Provinsi...</option>';
            provinces.forEach(prov => {
                const isSelected = selectedProvinceId && selectedProvinceId === prov.id ? 'selected' : '';
                provinceSelect.innerHTML += `<option value="${prov.id}" ${isSelected}>${prov.name}</option>`;
            });

            provinceSelect.dataset.loaded = 'true';
            provinceSelect.disabled = false;

            if (selectedProvinceId) {
                provinceSelect.value = selectedProvinceId;
                provinceSelect.dispatchEvent(new Event('change'));
            }
        } catch (error) {
            console.error('Error fetching provinces:', error);
            provinceSelect.innerHTML = '<option value="">Gagal memuat provinsi</option>';
            provinceSelect.disabled = false;
        }
    }

    async function loadRegencies(modalElement, provinceId, selectedCityId = null) {
        const citySelect = modalElement.querySelector('.city-select');
        const districtSelect = modalElement.querySelector('.district-select');
        const villageSelect = modalElement.querySelector('.village-select');

        citySelect.innerHTML = '<option value="">Memuat kota...</option>';
        citySelect.disabled = true;
        districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
        districtSelect.disabled = true;
        villageSelect.innerHTML = '<option value="">Pilih Desa...</option>';
        villageSelect.disabled = true;

        modalElement.querySelector('.city-name').value = '';
        modalElement.querySelector('.district-name').value = '';
        modalElement.querySelector('.village-name').value = '';

        try {
            const regencies = await fetchData(`/api/wilayah/regencies/${provinceId}`);

            citySelect.innerHTML = '<option value="">Pilih Kota...</option>';
            regencies.forEach(regency => {
                const isSelected = selectedCityId && selectedCityId === regency.id ? 'selected' : '';
                citySelect.innerHTML += `<option value="${regency.id}" ${isSelected}>${regency.name}</option>`;
            });

            citySelect.disabled = false;

            if (selectedCityId) {
                citySelect.value = selectedCityId;
                citySelect.dispatchEvent(new Event('change'));
            }
        } catch (error) {
            console.error('Error fetching regencies:', error);
            citySelect.innerHTML = '<option value="">Gagal memuat kota</option>';
            citySelect.disabled = false;
        }
    }

    async function loadDistricts(modalElement, regencyId, selectedDistrictId = null) {
        const districtSelect = modalElement.querySelector('.district-select');
        const villageSelect = modalElement.querySelector('.village-select');

        districtSelect.innerHTML = '<option value="">Memuat kecamatan...</option>';
        districtSelect.disabled = true;
        villageSelect.innerHTML = '<option value="">Pilih Desa...</option>';
        villageSelect.disabled = true;

        modalElement.querySelector('.district-name').value = '';
        modalElement.querySelector('.village-name').value = '';

        try {
            const districts = await fetchData(`/api/wilayah/districts/${regencyId}`);

            districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
            districts.forEach(district => {
                const isSelected = selectedDistrictId && selectedDistrictId === district.id ? 'selected' : '';
                districtSelect.innerHTML += `<option value="${district.id}" ${isSelected}>${district.name}</option>`;
            });

            districtSelect.disabled = false;

            if (selectedDistrictId) {
                districtSelect.value = selectedDistrictId;
                districtSelect.dispatchEvent(new Event('change'));
            }
        } catch (error) {
            console.error('Error fetching districts:', error);
            districtSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
            districtSelect.disabled = false;
        }
    }

    async function loadVillages(modalElement, districtId, selectedVillageId = null) {
        const villageSelect = modalElement.querySelector('.village-select');

        villageSelect.innerHTML = '<option value="">Memuat desa...</option>';
        villageSelect.disabled = true;

        modalElement.querySelector('.village-name').value = '';

        try {
            const villages = await fetchData(`/api/wilayah/villages/${districtId}`);

            villageSelect.innerHTML = '<option value="">Pilih Desa...</option>';
            villages.forEach(village => {
                const isSelected = selectedVillageId && selectedVillageId === village.id ? 'selected' : '';
                villageSelect.innerHTML += `<option value="${village.id}" ${isSelected}>${village.name}</option>`;
            });

            villageSelect.disabled = false;
        } catch (error) {
            console.error('Error fetching villages:', error);
            villageSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
            villageSelect.disabled = false;
        }
    }

    function setupCascading(modalElement) {
        const provinceSelect = modalElement.querySelector('.province-select');
        const citySelect = modalElement.querySelector('.city-select');
        const districtSelect = modalElement.querySelector('.district-select');
        const villageSelect = modalElement.querySelector('.village-select');

        provinceSelect.addEventListener('change', function() {
            const provName = this.options[this.selectedIndex]?.text || '';
            modalElement.querySelector('.province-name').value = (provName !== 'Pilih Provinsi...' && provName !== '') ? provName : '';

            citySelect.innerHTML = '<option value="">Pilih Kota...</option>';
            citySelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
            districtSelect.disabled = true;
            villageSelect.innerHTML = '<option value="">Pilih Desa...</option>';
            villageSelect.disabled = true;
            modalElement.querySelector('.city-name').value = '';
            modalElement.querySelector('.district-name').value = '';
            modalElement.querySelector('.village-name').value = '';

            if (this.value) {
                loadRegencies(modalElement, this.value);
            }
        });

        citySelect.addEventListener('change', function() {
            const cityName = this.options[this.selectedIndex]?.text || '';
            modalElement.querySelector('.city-name').value = (cityName !== 'Pilih Kota...' && cityName !== '') ? cityName : '';

            districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
            districtSelect.disabled = true;
            villageSelect.innerHTML = '<option value="">Pilih Desa...</option>';
            villageSelect.disabled = true;
            modalElement.querySelector('.district-name').value = '';
            modalElement.querySelector('.village-name').value = '';

            if (this.value) {
                loadDistricts(modalElement, this.value);
            }
        });

        districtSelect.addEventListener('change', function() {
            const districtName = this.options[this.selectedIndex]?.text || '';
            modalElement.querySelector('.district-name').value = (districtName !== 'Pilih Kecamatan...' && districtName !== '') ? districtName : '';

            villageSelect.innerHTML = '<option value="">Pilih Desa...</option>';
            villageSelect.disabled = true;
            modalElement.querySelector('.village-name').value = '';

            if (this.value) {
                loadVillages(modalElement, this.value);
            }
        });

        villageSelect.addEventListener('change', function() {
            const villageName = this.options[this.selectedIndex]?.text || '';
            modalElement.querySelector('.village-name').value = (villageName !== 'Pilih Desa...' && villageName !== '') ? villageName : '';
        });
    }

    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            if (!this.dataset.cascadingSetup) {
                setupCascading(this);
                this.dataset.cascadingSetup = 'true';
            }

            const selectedProvince = this.querySelector('.province-select').getAttribute('data-selected');
            loadProvinces(this, selectedProvince || null);
        });
    });

});
</script>
@endpush
