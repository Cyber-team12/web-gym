@extends('layouts.app')

@section('content')
<div class="container">

    <section class="hero">
        <span class="role-pill role-admin">✦ Admin</span>
        <h1 class="mt-3">Selamat datang kembali,<br>{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}.</h1>
        <p class="text-secondary mt-3 mb-0">Pantau member, transaksi, dan kunjungan dari satu tempat.</p>
    </section>

    <div class="row g-3 mt-2">
        <div class="col-lg-5">
            <div class="card glow-card h-100">
                <span class="card-star">✦</span>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="stat-label">Member aktif</div>
                    <div class="stat-number big mt-4" data-count="{{ $stats['active_members'] ?? 0 }}">0</div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Total member</div>
                            <div class="stat-number mt-2" data-count="{{ $stats['members'] ?? 0 }}">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Kunjungan hari ini</div>
                            <div class="stat-number mt-2" data-count="{{ $stats['visits_today'] ?? 0 }}">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card glow-card">
                        <span class="card-star">✦</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Transaksi bulan ini</div>
                            <div class="stat-number mt-2" data-count="{{ $stats['transactions_month'] ?? 0 }}">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card glow-card mt-3">
        <div class="card-body empty-state">
            <div class="empty-icon">✦</div>
            <h5 class="mt-2">Belum ada kunjungan hari ini</h5>
            <p class="text-secondary mb-0">Check-in member akan muncul di sini.</p>
        </div>
    </div>

</div>
@endsection