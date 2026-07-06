@extends('layouts.public')

@section('title','Profil Saya')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <h3 class="mb-0">Profil Saya</h3>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ auth()->user()->name }}"
                                   disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   class="form-control"
                                   value="{{ auth()->user()->email }}"
                                   disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone',$profile->phone) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>

                            <select name="gender" class="form-select">
                                <option value="">Pilih</option>

                                <option value="Laki-laki"
                                    {{ old('gender',$profile->gender)=='Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>

                                <option value="Perempuan"
                                    {{ old('gender',$profile->gender)=='Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tanggal Lahir</label>

                            <input type="date"
                                   name="birth_date"
                                   class="form-control"
                                   value="{{ old('birth_date',$profile->birth_date) }}">
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Simpan Profil
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection