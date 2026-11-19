
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
                            <i class="fas fa-user-edit text-info"></i>
                            Ubah Customer
                        </h4>

                        <p class="text-muted mb-0">
                            Perbarui informasi customer toko online
                        </p>
                    </div>

                    <a href="{{ route('backend.customer.index') }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <hr>

                {{-- Form Ubah Customer --}}
                <form action="{{ route('backend.customer.update', $customer->id) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    <div class="row">

                        {{-- Kolom Kiri --}}
                        <div class="col-md-4">
                            <div class="bg-light rounded text-center p-4 mb-3">
                                <i class="fas fa-user-circle text-info"
                                   style="font-size: 100px;"></i>

                                <h5 class="mt-3">
                                    {{ $customer->user->nama ?? 'Customer' }}
                                </h5>

                                <span class="badge badge-info">
                                    Customer
                                </span>
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="col-md-8">

                            <h5 class="mb-3">Form Informasi Customer</h5>

                            {{-- Nama --}}
                            <div class="form-group">
                                <label for="nama">
                                    Nama Lengkap
                                </label>

                                <input type="text"
                                       id="nama"
                                       name="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $customer->user->nama ?? '') }}"
                                       placeholder="Masukkan nama lengkap"
                                       required>

                                @error('nama')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label for="email">
                                    Email
                                </label>

                                <input type="email"
                                       id="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $customer->user->email ?? '') }}"
                                       placeholder="Masukkan alamat email"
                                       required>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="form-group">
                                <label for="alamat">
                                    Alamat
                                </label>

                                <textarea id="alamat"
                                          name="alamat"
                                          class="form-control @error('alamat') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Masukkan alamat lengkap">{{ old('alamat', $customer->alamat ?? '') }}</textarea>

                                @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Kode Pos --}}
                            <div class="form-group">
                                <label for="pos">
                                    Kode Pos
                                </label>

                                <input type="text"
                                       id="pos"
                                       name="pos"
                                       class="form-control @error('pos') is-invalid @enderror"
                                       value="{{ old('pos', $customer->pos ?? '') }}"
                                       placeholder="Masukkan kode pos">

                                @error('pos')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <hr>

                            {{-- Tombol --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('backend.customer.index') }}"
                                   class="btn btn-light mr-2">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save"></i>
                                    Simpan Perubahan
                                </button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
