@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Profil Saya
    </h2>

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    <form method="POST" action="{{ route('customer.profile.update') }}">

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label>Nama</label>

            <input
                class="form-control"
                value="{{ auth()->user()->name }}"
                disabled>

        </div>

        <div class="mb-3">

            <label>Email</label>

            <input
                class="form-control"
                value="{{ auth()->user()->email }}"
                disabled>

        </div>

        <div class="mb-3">

            <label>No HP</label>

            <input
                type="text"
                class="form-control"
                name="phone"
                value="{{ old('phone',$profile->phone) }}">

        </div>

        <div class="mb-3">

            <label>Jenis Kelamin</label>

            <select
                class="form-control"
                name="gender">

                <option value="">Pilih</option>

                <option value="Laki-laki"
                    {{ $profile->gender=="Laki-laki"?'selected':'' }}>
                    Laki-laki
                </option>

                <option value="Perempuan"
                    {{ $profile->gender=="Perempuan"?'selected':'' }}>
                    Perempuan
                </option>

            </select>

        </div>

        <div class="mb-3">

            <label>Tanggal Lahir</label>

            <input
                type="date"
                class="form-control"
                name="birth_date"
                value="{{ $profile->birth_date }}">

        </div>

        <button class="btn btn-primary">

            Simpan Profil

        </button>

    </form>

</div>

@endsection