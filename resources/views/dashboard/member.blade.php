@extends('layouts.app')

@section('content')
<div class="container">

    <section class="hero">
        <span class="role-pill role-member">✦ Member Lounge</span>
        <h1 class="mt-3">Halo, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}.<br>Siap latihan hari ini?</h1>
        <p class="text-secondary mt-2 mb-0">Pantau masa aktif membership dan riwayat latihanmu di sini.</p>
    </section>

    @include('partials.flash')

    @if($pendingPayment)
    <div class="alert alert-warning border border-warning glow-card my-3 d-flex justify-content-between align-items-center">
        <div>
            <strong>⏳ Pembayaran Sedang Diverifikasi:</strong> Anda memiliki pengajuan pembayaran paket <strong>{{ $pendingPayment->plan->name }}</strong> sebesar <strong>Rp {{ number_format($pendingPayment->total, 0, ',', '.') }}</strong> yang sedang menunggu validasi admin.
        </div>
        <a href="{{ route('member.payments.show', $pendingPayment) }}" class="btn btn-sm btn-outline-dark text-nowrap ms-2">
            Lihat Status
        </a>
    </div>
    @endif

    <div class="row g-3 mt-2">
        <div class="col-lg-5">
            <div class="card glow-card h-100">
                <span class="card-star">✦</span>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="stat-label">Total Kunjungan / Latihan</div>
                        <p class="text-secondary small mt-1 mb-0">Jumlah hari kamu datang dan check-in di gym.</p>
                    </div>
                    <div class="stat-number big my-3" data-count="{{ $stats['visits'] ?? 0 }}">0</div>
                    <div>
                        <a href="{{ route('member.attendances.index') }}" class="btn btn-sm btn-outline-secondary">
                            Lihat Riwayat Presensi →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="stat-label">Status Membership</div>
                                @if($activeMembership)
                                    <span class="badge bg-success fs-6">Aktif</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Tidak Aktif</span>
                                @endif
                            </div>
                            <div class="stat-number mt-2 text-warning" style="font-size: 1.8rem;">
                                {{ $activeMembership ? $activeMembership->plan->name : 'Belum Memiliki Paket Aktif' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Masa Berlaku Membership</div>
                            @if($activeMembership)
                                @php
                                    $daysRemaining = max(0, today()->diffInDays($activeMembership->end_date, false));
                                @endphp
                                <div class="fs-4 fw-bold mt-2">
                                    Sampai {{ $activeMembership->end_date->format('d F Y') }}
                                    <span class="badge bg-info-subtle text-info fs-6 ms-2">
                                        {{ $daysRemaining }} Hari Lagi
                                    </span>
                                </div>
                            @else
                                <div class="fs-5 text-secondary mt-2">-</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(! $activeMembership)
    <div class="card glow-card mt-4 border-warning">
        <div class="card-body empty-state p-5">
            <div class="empty-icon mb-2">✦</div>
            <h4 class="mt-2">Mulai Perjalanan Kebugaranmu!</h4>
            <p class="text-secondary mx-auto" style="max-width: 500px;">
                Kamu belum memiliki paket membership yang aktif. Pilih paket membership sekarang dan nikmati fasilitas lengkap gym kami.
            </p>
            <a href="{{ route('member.plans.index') }}" class="btn btn-primary px-4 py-2 mt-2">
                ✦ Lihat & Beli Paket Sekarang
            </a>
        </div>
    </div>
    @else
    <div class="card glow-card mt-4">
        <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 text-warning">✦ Riwayat Kunjungan Terakhir</h5>
            <a href="{{ route('member.attendances.index') }}" class="btn btn-sm btn-outline-secondary">
                Lihat Semua Kunjungan →
            </a>
        </div>
        <div class="card-body p-0">
            @if($recentAttendances->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Check-in</th>
                            <th>Paket</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendances as $att)
                        <tr>
                            <td class="fw-bold">{{ $att->checkin_at->format('d M Y') }}</td>
                            <td class="text-warning fw-bold">{{ $att->checkin_at->format('H:i') }} WIB</td>
                            <td>{{ $att->membership?->plan?->name ?? 'Membership' }}</td>
                            <td><span class="badge bg-success">Hadir</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state py-4">
                <p class="text-secondary mb-0">Belum ada catatan kunjungan. Mulai latihan hari ini!</p>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
