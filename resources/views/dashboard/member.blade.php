@extends('layouts.app')

@section('content')
<div class="container">

    <section class="hero">
        <span class="role-pill role-member">✦ Member</span>
        <h1 class="mt-3">Halo, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}.<br>Siap latihan hari ini?</h1>
    </section>

    <div class="row g-3 mt-2">
        <div class="col-lg-5">
            <div class="card glow-card h-100">
                <span class="card-star">✦</span>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="stat-label">Total kunjungan</div>
                    <div class="stat-number big mt-4" data-count="{{ $stats['visits'] ?? 0 }}">0</div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Status membership</div>
                            <div class="stat-number mt-2" style="font-size: 2rem;">Belum aktif</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Aktif sampai</div>
                            <div class="stat-number mt-2" style="font-size: 2rem;">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card glow-card mt-3">
        <div class="card-body empty-state">
            <div class="empty-icon">✦</div>
            <h5 class="mt-2">Belum ada paket aktif</h5>
            <p class="text-secondary">Pilih paket untuk mulai latihan.</p>
            <a href="#" class="btn btn-primary disabled" aria-disabled="true">✦ Lihat paket</a>
        </div>
    </div>

</div>
@endsection