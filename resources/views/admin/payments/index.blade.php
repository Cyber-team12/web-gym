@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Validasi Pembayaran</h2>
            <p class="text-secondary small mb-0">Verifikasi bukti transfer dan setujui membership member.</p>
        </div>
    </div>

    @include('partials.flash')

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari member / email...">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Menunggu Validasi (Pending)</option>
                <option value="approved" @selected(request('status') === 'approved')>Disetujui (Approved)</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak (Rejected)</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="plan_id" class="form-select">
                <option value="">Semua Paket</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(request('plan_id') == $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Member</th>
                        <th>Paket</th>
                        @include('partials.sort-th', ['col' => 'amount', 'label' => 'Harga'])
                        <th>Diskon</th>
                        @include('partials.sort-th', ['col' => 'total', 'label' => 'Total'])
                        <th>Bukti</th>
                        <th>Status</th>
                        @include('partials.sort-th', ['col' => 'created_at', 'label' => 'Waktu'])
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                    <tr>
                        <td class="font-monospace">#{{ $payment->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $payment->user->name }}</div>
                            <small class="text-secondary">{{ $payment->user->email }}</small>
                        </td>
                        <td>{{ $payment->plan->name }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>
                            @if($payment->discount > 0)
                                <span class="text-success fw-bold">-Rp {{ number_format($payment->discount, 0, ',', '.') }}</span>
                                @if($payment->voucher)
                                    <br><small class="text-secondary font-monospace">{{ $payment->voucher->code }}</small>
                                @endif
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
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="badge bg-secondary">Tanpa Bukti</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($payment->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-light">
                                Detail
                            </a>
                            @if($payment->status === 'pending')
                                <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Setujui pembayaran ini dan aktifkan membership?')">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Setujui</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $payment->id }}">
                                    Tolak
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-secondary py-4">Tidak ada data transaksi pembayaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>

    <!-- Modals Tolak Pembayaran di luar card -->
    @foreach ($payments as $payment)
        @if ($payment->status === 'pending')
        <div class="modal fade" id="rejectModal-{{ $payment->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $payment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: #1c1742; border: 1px solid rgba(255, 92, 138, 0.4); border-radius: 1rem; box-shadow: 0 15px 50px rgba(0,0,0,0.8);">
                    <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                        @csrf
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title text-danger fw-bold" id="rejectModalLabel-{{ $payment->id }}">
                                ✕ Tolak Pembayaran #{{ $payment->id }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-secondary small mb-3">
                                Member: <strong>{{ $payment->user->name }}</strong> ({{ $payment->plan->name }})
                            </p>
                            <div class="mb-3">
                                <label class="form-label text-white">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="reject_reason" class="form-control" rows="3" required
                                          placeholder="Contoh: Bukti transfer buram / nominal tidak sesuai / mutasi rekening tidak ditemukan"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>
@endsection
