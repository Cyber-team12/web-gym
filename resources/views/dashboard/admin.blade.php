@extends('layouts.admin')

@section('content')
<div class="container">

    <section class="hero">
        <span class="role-pill role-admin">✦ Admin Workspace</span>
        <h1 class="mt-3">Selamat datang kembali,<br>{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}.</h1>
        <p class="text-secondary mt-2 mb-0">Pantau operasional gym, member, transaksi, dan presensi harian dari satu tempat.</p>
    </section>

    @include('partials.flash')

    <div class="row g-3 mt-2">
        <div class="col-lg-5">
            <div class="card glow-card h-100">
                <span class="card-star">✦</span>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="stat-label">Member Aktif Saat Ini</div>
                        <p class="text-secondary small mt-1 mb-0">Member dengan langganan aktif & valid.</p>
                    </div>
                    <div class="stat-number big my-3" data-count="{{ $stats['active_members'] ?? 0 }}">0</div>
                    <div class="small text-secondary">
                        Dari total {{ $stats['members'] ?? 0 }} member terdaftar
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Total Akun Member</div>
                            <div class="stat-number mt-2" data-count="{{ $stats['members'] ?? 0 }}">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card glow-card">
                        <span class="card-star">✧</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Presensi Hari Ini</div>
                            <div class="stat-number mt-2" data-count="{{ $stats['visits_today'] ?? 0 }}">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card glow-card">
                        <span class="card-star">✦</span>
                        <div class="card-body p-4">
                            <div class="stat-label">Pendapatan Bulan Ini</div>
                            <div class="fs-2 fw-bold text-warning mt-2">
                                Rp {{ number_format($stats['transactions_month'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Antrean Validasi Pembayaran Pending -->
    @if($pendingPayments->count() > 0)
    <div class="card glow-card mt-4 border-warning">
        <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 text-warning">
                ⏳ Menunggu Validasi Pembayaran ({{ $pendingPayments->count() }})
            </h5>
            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning">
                Kelola Semua Pembayaran →
            </a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Member</th>
                        <th>Paket</th>
                        <th>Total</th>
                        <th>Waktu Pengajuan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $p)
                    <tr>
                        <td class="font-monospace">#{{ $p->id }}</td>
                        <td class="fw-bold">{{ $p->user->name }}</td>
                        <td>{{ $p->plan->name }}</td>
                        <td class="fw-bold text-warning">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                        <td>{{ $p->created_at->diffForHumans() }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.payments.show', $p) }}" class="btn btn-sm btn-primary">
                                Periksa Bukti
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Presensi Hari Ini -->
    <div class="card glow-card mt-4">
        <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 text-warning">✦ Presensi Check-in Hari Ini</h5>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-outline-secondary">
                Buka Menu Presensi Lengkap →
            </a>
        </div>
        <div class="card-body p-0">
            @if($todayAttendances->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Paket Membership</th>
                            <th>Waktu Check-in</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayAttendances as $att)
                        <tr>
                            <td class="fw-bold">{{ $att->user->name }}</td>
                            <td>{{ $att->membership?->plan?->name ?? 'Membership' }}</td>
                            <td class="text-warning fw-bold">{{ $att->checkin_at->format('H:i') }} WIB</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">✦</div>
                <h5 class="mt-2">Belum ada check-in hari ini</h5>
                <p class="text-secondary mb-0">Member yang melakukan check-in akan otomatis tampil di sini.</p>
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-primary mt-3">+ Catat Check-in Member</a>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
