@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card glow-card p-4 p-md-5">
                <span class="card-star">✦</span>
                <div class="text-center mb-4">
                    <span class="brand-star fs-3">✦</span>
                    <h2 class="fw-bold mt-2 mb-1">Masuk ke Akun</h2>
                    <p class="text-secondary small">Masukkan email dan password untuk melanjutkan</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="nama@email.com">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label mb-0">Password</label>
                            @if (Route::has('password.request'))
                                <a class="small text-secondary text-decoration-none" href="{{ route('password.request') }}">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <input id="password" type="password" class="form-control mt-2 @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password"
                               placeholder="••••••••">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-secondary" for="remember">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        ✦ Masuk Sekarang
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-secondary">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="small text-warning text-decoration-none ms-1">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
