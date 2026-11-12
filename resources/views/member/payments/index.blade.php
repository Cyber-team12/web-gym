@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Riwayat Pembayaran</h2>
            <p class="text-secondary small mb-0">Pantau status transaksi dan perpanjangan paket membership Anda.</p>
        </div>
        <a href="{{ route('member.plans.index') }}" class="btn btn-primary">+ Beli / Perpanjang Paket</a>
    </div>

    @include('partials.flash')

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Status Transaksi</option>
                <option value="pending" @selected(request('status') === 'pending')>Menunggu Validasi</option>
                <option value="approved" @selected(request('status') === 'approved')>Disetujui (Aktif)</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
            </select>
        </div>
    </form>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Paket</th>
                        <th>Voucher</th>
                        <th>Total Bayar</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                    <tr>
                        <td class="font-monospace">#{{ $payment->id }}</td>
                        <td>{{ $payment->created_at->format('d M Y, H:i') }}</td>
                        <td class="fw-bold">{{ $payment->plan->name }}</td>
                        <td>
                            @if($payment->voucher)
                                <span class="badge bg-dark-subtle text-warning font-monospace">{{ $payment->voucher->code }}</span>
                                <br><small class="text-success">-Rp {{ number_format($payment->discount, 0, ',', '.') }}</small>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                        <td class="fw-bold text-warning">
                            Rp {{ number_format($payment->total, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($payment->proof_path)
                                <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    Lihat File
                                </a>
                            @else
                                <span class="text-secondary small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($payment->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                                @if($payment->reject_reason)
                                    <br><small class="text-danger">{{ $payment->reject_reason }}</small>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('member.payments.show', $payment) }}" class="btn btn-sm btn-outline-light">
                                Detail
                            </a>
                            @if($payment->status === 'rejected')
                                <a href="{{ route('member.checkout.show', $payment->plan) }}" class="btn btn-sm btn-outline-warning ms-1">
                                    Ajukan Ulang
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">
                            Belum ada riwayat transaksi pembayaran.
                            <br>
                            <a href="{{ route('member.plans.index') }}" class="btn btn-sm btn-primary mt-2">Pilih Paket Sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>
@endsection
