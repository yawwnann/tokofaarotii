@extends('layouts.frontend')

@section('title','Alamat Saya')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Alamat Saya</h2>

        <a href="#" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Alamat
        </a>

    </div>

    @forelse($addresses as $address)

        <div class="card mb-3 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <h5>

                            {{ $address->label }}

                            @if($address->is_default)

                                <span class="badge bg-success">

                                    Utama

                                </span>

                            @endif

                        </h5>

                        <strong>{{ $address->receiver_name }}</strong>

                        <br>

                        {{ $address->phone }}

                        <p class="mt-2 mb-0">

                            {{ $address->address }}

                        </p>

                        <small>

                            {{ $address->village }},
                            {{ $address->district }},
                            {{ $address->city }},
                            {{ $address->province }}

                            {{ $address->postal_code }}

                        </small>

                    </div>

                </div>

            </div>

        </div>

    @empty

        <div class="alert alert-info">

            Belum ada alamat.

        </div>

    @endforelse

</div>

@endsection