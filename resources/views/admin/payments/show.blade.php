@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Detail Pembayaran #{{ $payment->id }}</h2>
            <p class="text-secondary small mb-0">Diajukan pada {{ $payment->created_at->format('d F Y, H:i') }} WIB</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">← Kembali ke Daftar</a>
    </div>

    @include('partials.flash')

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card glow-card h-100">
                <div class="card-body p-4">
                    <h5 class="card-title text-warning mb-3">✦ Rincian Member & Paket</h5>
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="text-secondary" style="width: 140px;">Nama Member</td>
                            <td class="fw-bold">{{ $payment->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Email</td>
                            <td>{{ $payment->user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">No. Telepon</td>
                            <td>{{ $payment->user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Paket Dipilih</td>
                            <td class="fw-bold fs-6">{{ $payment->plan->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Durasi Paket</td>
                            <td>{{ $payment->plan->duration_days }} hari</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Harga Normal</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($payment->discount > 0)
                        <tr>
                            <td class="text-secondary">Diskon Voucher</td>
                            <td class="text-success fw-bold">
                                - Rp {{ number_format($payment->discount, 0, ',', '.') }}
                                @if($payment->voucher)
                                    <span class="badge bg-dark-subtle text-warning font-monospace ms-1">{{ $payment->voucher->code }}</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr class="border-top border-secondary">
                            <td class="text-secondary fs-5">Total Bayar</td>
                            <td class="text-warning fw-bold fs-4">Rp {{ number_format($payment->total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary">Status</td>
                            <td>
                                @if($payment->status === 'approved')
                                    <span class="badge bg-success fs-6">Disetujui</span>
                                @elseif($payment->status === 'rejected')
                                    <span class="badge bg-danger fs-6">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark fs-6">Menunggu Validasi</span>
                                @endif
                            </td>
                        </tr>
                        @if($payment->validated_by)
                        <tr>
                            <td class="text-secondary">Divalidasi Oleh</td>
                            <td>{{ $payment->validator->name ?? 'Admin' }} ({{ $payment->validated_at->format('d M Y H:i') }})</td>
                        </tr>
                        @endif
                        @if($payment->reject_reason)
                        <tr>
                            <td class="text-secondary">Alasan Ditolak</td>
                            <td class="text-danger">{{ $payment->reject_reason }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card glow-card h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title text-warning mb-3">✦ Bukti Transfer Pembayaran</h5>
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center bg-dark rounded p-3 mb-3 border border-secondary text-center" style="min-height: 250px;">
                        @if($payment->proof_path)
                            @php
                                $ext = strtolower(pathinfo($payment->proof_path, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Bukti Transfer" class="img-fluid rounded" style="max-height: 350px;">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="btn btn-outline-info">
                                    Unduh Dokumen Bukti ({{ strtoupper($ext) }})
                                </a>
                            @endif
                        @else
                            <div class="text-secondary">
                                <span class="fs-1 d-block mb-2">🧾</span>
                                <em>Tidak ada file bukti pembayaran yang diunggah.</em>
                            </div>
                        @endif
                    </div>

                    @if($payment->status === 'pending')
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            ✕ Tolak Pembayaran
                        </button>
                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui transaksi ini dan mengaktifkan membership?')">
                            @csrf
                            <button class="btn btn-success">✓ Setujui Pembayaran</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($payment->status === 'pending')
    <!-- Modal Tolak Pembayaran diletakkan di luar card agar tidak tertutup backdrop -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #1c1742; border: 1px solid rgba(255, 92, 138, 0.4); border-radius: 1rem; box-shadow: 0 15px 50px rgba(0,0,0,0.8);">
                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                    @csrf
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-danger fw-bold" id="rejectModalLabel">
                            ✕ Tolak Pembayaran #{{ $payment->id }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-secondary small mb-3">
                            Member <strong>{{ $payment->user->name }}</strong> akan melihat alasan penolakan ini pada riwayat pembayarannya.
                        </p>
                        <div class="mb-3">
                            <label class="form-label text-white">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="reject_reason" class="form-control" rows="3" required
                                      placeholder="Contoh: Bukti transfer buram / nominal tidak sesuai / mutasi rekening tidak ditemukan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Tolak Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
