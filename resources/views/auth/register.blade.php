@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card glow-card p-4 p-md-5">
                <span class="card-star">✦</span>
                <div class="text-center mb-4">
                    <span class="brand-star fs-3">✦</span>
                    <h2 class="fw-bold mt-2 mb-1">Daftar Akun Baru</h2>
                    <p class="text-secondary small">Bergabung menjadi member gym dan nikmati berbagai keuntungan</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                               placeholder="Contoh: Budi Santoso">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email"
                               placeholder="nama@email.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="form-label">Konfirmasi Password</label>
                        <input id="password-confirm" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="Ulangi password">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        ✦ Buat Akun Member
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-secondary">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="small text-warning text-decoration-none ms-1">Masuk ke sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
