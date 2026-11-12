@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('member.payments.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Riwayat</a>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card glow-card p-4">
                <span class="card-star">✦</span>
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-3">
                    <div>
                        <h3 class="mb-0">Transaksi #{{ $payment->id }}</h3>
                        <small class="text-secondary">{{ $payment->created_at->format('d F Y, H:i') }} WIB</small>
                    </div>
                    <div>
                        @if($payment->status === 'approved')
                            <span class="badge bg-success fs-6">Disetujui</span>
                        @elseif($payment->status === 'rejected')
                            <span class="badge bg-danger fs-6">Ditolak</span>
                        @else
                            <span class="badge bg-warning text-dark fs-6">Menunggu Validasi</span>
                        @endif
                    </div>
                </div>

                @if($payment->status === 'rejected' && $payment->reject_reason)
                <div class="alert alert-danger mb-4">
                    <strong>Alasan Penolakan:</strong> {{ $payment->reject_reason }}
                    <div class="mt-2">
                        <a href="{{ route('member.checkout.show', $payment->plan) }}" class="btn btn-sm btn-danger">
                            Unggah Bukti Baru / Beli Ulang
                        </a>
                    </div>
                </div>
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="text-secondary small">Paket Membership:</div>
                        <div class="fw-bold fs-5">{{ $payment->plan->name }}</div>
                        <div class="small text-secondary">Durasi {{ $payment->plan->duration_days }} hari</div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <div class="text-secondary small">Total Pembayaran:</div>
                        <div class="fw-bold fs-4 text-warning">Rp {{ number_format($payment->total, 0, ',', '.') }}</div>
                        @if($payment->discount > 0)
                            <div class="small text-success">Hemat diskon Rp {{ number_format($payment->discount, 0, ',', '.') }}</div>
                        @endif
                    </div>
                </div>

                <div class="border-top border-secondary pt-3">
                    <h5 class="text-warning mb-3">Bukti Pembayaran:</h5>
                    @if($payment->proof_path)
                        @php
                            $ext = strtolower(pathinfo($payment->proof_path, PATHINFO_EXTENSION));
                        @endphp
                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                            <div class="text-center bg-dark p-3 rounded border border-secondary">
                                <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 400px;">
                            </div>
                        @else
                            <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="btn btn-outline-info">
                                Unduh File Bukti Transfer ({{ strtoupper($ext) }})
                            </a>
                        @endif
                    @else
                        <span class="text-secondary">Tidak ada bukti yang terunggah.</span>
                    @endif
                </div>

                @if($payment->membership)
                <div class="border-top border-secondary pt-3 mt-4">
                    <h5 class="text-info mb-2">✦ Informasi Membership yang Dihasilkan:</h5>
                    <p class="mb-0 text-secondary">
                        Masa aktif: <strong>{{ $payment->membership->start_date->format('d M Y') }}</strong> s.d. <strong>{{ $payment->membership->end_date->format('d M Y') }}</strong>
                        <span class="badge {{ $payment->membership->isActive() ? 'bg-success' : 'bg-secondary' }} ms-1">
                            {{ $payment->membership->isActive() ? 'Aktif' : 'Expired' }}
                        </span>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
