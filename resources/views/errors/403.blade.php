@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card glow-card p-5 mx-auto border-danger" style="max-width: 650px;">
        <span class="card-star">✧</span>
        <div class="fs-1 text-danger mb-3">⛔ 403</div>
        <h2 class="fw-bold mb-2">Akses Ditolak</h2>
        <p class="text-secondary mb-4">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses yang memadai untuk membuka halaman ini.' }}
        </p>
        <div>
            <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
