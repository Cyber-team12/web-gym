@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center my-4">
        <span class="role-pill role-member">✦ Pilihan Paket Membership</span>
        <h1 class="mt-3">Investasi Terbaik untuk Kebugaranmu</h1>
        <p class="text-secondary mx-auto" style="max-width: 600px;">
            Pilih paket langganan gym sesuai target latihanmu. Akses alat lengkap, instruktur ramah, dan komunitas suportif.
        </p>
    </div>

    @include('partials.flash')

    @if($activeMembership)
    <div class="alert alert-info border border-info-subtle glow-card mb-4 text-center">
        <span class="fs-5">✦ Status Langganan Anda: <strong>{{ $activeMembership->plan->name }}</strong></span>
        <br>
        <span class="small text-secondary">
            Aktif s.d. {{ $activeMembership->end_date->format('d F Y') }}
            ({{ max(0, today()->diffInDays($activeMembership->end_date, false)) }} hari lagi).
            Membeli paket baru sekarang akan otomatis memperpanjang masa aktif setelah paket saat ini berakhir.
        </span>
    </div>
    @endif

    <div class="row g-4 justify-content-center">
        @forelse($plans as $plan)
        <div class="col-md-6 col-lg-3">
            <div class="card glow-card h-100 d-flex flex-column text-center p-4">
                <span class="card-star">✦</span>
                <h4 class="fw-bold mb-1">{{ $plan->name }}</h4>
                <div class="badge bg-secondary-subtle text-secondary mx-auto mb-3">
                    Durasi {{ $plan->duration_days }} Hari
                </div>

                <div class="my-3">
                    <span class="fs-6 text-secondary">Rp</span>
                    <span class="fs-2 fw-bold text-warning">{{ number_format($plan->price, 0, ',', '.') }}</span>
                </div>

                <p class="text-secondary small flex-grow-1">
                    {{ $plan->description ?? 'Akses bebas ke fasilitas gym pilihan.' }}
                </p>

                <hr class="border-secondary my-3">

                <a href="{{ route('member.checkout.show', $plan) }}" class="btn btn-primary w-100 py-2">
                    ✦ Pilih Paket Ini
                </a>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-secondary py-5">
            Belum ada paket membership yang tersedia saat ini.
        </div>
        @endforelse
    </div>
</div>
@endsection
