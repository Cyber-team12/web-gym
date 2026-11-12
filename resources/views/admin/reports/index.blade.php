@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Laporan Keuangan & Penjualan</h2>
            <p class="text-secondary small mb-0">Agregasi pendapatan dari transaksi membership yang telah disetujui.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-info d-print-none">
            🖨 Cetak Laporan
        </button>
    </div>

    <!-- Filter Periode Laporan -->
    <div class="card glow-card mb-4 d-print-none">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-secondary small">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small">Filter Paket</label>
                    <select name="plan_id" class="form-select">
                        <option value="">Semua Paket</option>
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" @selected($planId == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-fill">Tampilkan</button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Ringkasan Angka -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card glow-card h-100">
                <div class="card-body p-3">
                    <div class="stat-label">Total Pendapatan Bersih</div>
                    <div class="fs-3 fw-bold text-warning mt-2">
                        Rp {{ number_format($metrics->total_revenue, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glow-card h-100">
                <div class="card-body p-3">
                    <div class="stat-label">Jumlah Transaksi</div>
                    <div class="fs-3 fw-bold text-info mt-2">
                        {{ number_format($metrics->total_transactions, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glow-card h-100">
                <div class="card-body p-3">
                    <div class="stat-label">Total Diskon Diberikan</div>
                    <div class="fs-3 fw-bold text-danger mt-2">
                        Rp {{ number_format($metrics->total_discount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glow-card h-100">
                <div class="card-body p-3">
                    <div class="stat-label">Total Bruto / Kotor</div>
                    <div class="fs-3 fw-bold text-secondary mt-2">
                        Rp {{ number_format($metrics->gross_amount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Per Paket -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card glow-card">
                <div class="card-body p-4">
                    <h5 class="card-title text-warning mb-3">✦ Rincian Penjualan per Paket</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Paket</th>
                                    <th>Total Pembelian</th>
                                    <th class="text-end">Kontribusi Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($planBreakdown as $pb)
                                <tr>
                                    <td class="fw-bold">{{ $pb->name }}</td>
                                    <td>{{ $pb->count }} transaksi</td>
                                    <td class="text-end fw-bold text-warning">
                                        Rp {{ number_format($pb->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-3">Tidak ada data penjualan pada periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Transaksi -->
    <div class="card glow-card">
        <div class="card-header bg-transparent border-secondary p-3">
            <h5 class="mb-0 text-warning">✦ Rincian Transaksi Pembayaran</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Tanggal</th>
                        <th>Member</th>
                        <th>Paket</th>
                        <th>Voucher</th>
                        <th>Nominal</th>
                        <th>Diskon</th>
                        <th class="text-end">Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $p)
                    <tr>
                        <td class="font-monospace">#{{ $p->id }}</td>
                        <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="fw-bold">{{ $p->user->name }}</div>
                            <small class="text-secondary">{{ $p->user->email }}</small>
                        </td>
                        <td>{{ $p->plan->name }}</td>
                        <td>
                            @if($p->voucher)
                                <span class="badge bg-dark-subtle text-warning font-monospace">{{ $p->voucher->code }}</span>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td>
                            @if($p->discount > 0)
                                <span class="text-danger">-Rp {{ number_format($p->discount, 0, ',', '.') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end fw-bold text-warning">
                            Rp {{ number_format($p->total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Tidak ada data transaksi pembayaran pada rentang tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-print-none">
        {{ $payments->links() }}
    </div>
</div>
@endsection
