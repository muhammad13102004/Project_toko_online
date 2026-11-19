
@extends('backend.v_layouts.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">
                            <i class="fas fa-user-circle text-info"></i>
                            Detail Customer
                        </h4>
                        <p class="text-muted mb-0">
                            Informasi lengkap customer toko online
                        </p>
                    </div>

                    <a href="{{ route('backend.customer.index') }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <hr>

                {{-- Profil Customer --}}
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="p-4 bg-light rounded">
                            <i class="fas fa-user-circle text-info"
                               style="font-size: 100px;"></i>

                            <h4 class="mt-3">
                                {{ $customer->user->nama ?? '-' }}
                            </h4>

                            <span class="badge badge-info">
                                Customer
                            </span>
                        </div>
                    </div>

                    {{-- Informasi Customer --}}
                    <div class="col-md-8">
                        <h5 class="mb-3">Informasi Customer</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%;">
                                        <i class="fas fa-user text-info"></i>
                                        Nama
                                    </th>
                                    <td>
                                        {{ $customer->user->nama ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <i class="fas fa-envelope text-info"></i>
                                        Email
                                    </th>
                                    <td>
                                        {{ $customer->user->email ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        Alamat
                                    </th>
                                    <td>
                                        {{ $customer->alamat ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <i class="fas fa-mail-bulk text-info"></i>
                                        Kode Pos
                                    </th>
                                    <td>
                                        {{ $customer->pos ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-4">
                            <a href="{{ route('backend.customer.edit', $customer->id) }}"
                               class="btn btn-info">
                                <i class="fas fa-edit"></i> Ubah Data
                            </a>

                            <a href="{{ route('backend.customer.index') }}"
                               class="btn btn-light">
                                Tutup
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
