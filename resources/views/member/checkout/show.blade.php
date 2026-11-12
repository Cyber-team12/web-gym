@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('member.plans.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Pilihan Paket</a>
    </div>

    @include('partials.flash')

    <div class="row g-4 justify-content-center">
        <!-- Rincian Pesanan -->
        <div class="col-lg-5">
            <div class="card glow-card h-100 p-4">
                <span class="card-star">✦</span>
                <h4 class="text-warning mb-3">Ringkasan Pesanan</h4>
                <div class="border-bottom border-secondary pb-3 mb-3">
                    <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                    <span class="badge bg-secondary-subtle text-secondary">Durasi {{ $plan->duration_days }} Hari</span>
                    <p class="text-secondary small mt-2 mb-0">{{ $plan->description }}</p>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Harga Normal:</span>
                        <span class="fw-bold">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="discount-row" style="display: none !important;">
                        <span class="text-success">Diskon Voucher:</span>
                        <span class="text-success fw-bold" id="discount-amount">-Rp 0</span>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold">Total Tagihan:</span>
                        <span class="fs-4 fw-bold text-warning" id="total-amount">
                            Rp {{ number_format($plan->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="alert alert-dark border border-secondary mt-auto mb-0">
                    <div class="fw-bold text-warning mb-1">🏦 Rekening Pembayaran Resmi:</div>
                    <div class="small text-secondary">Bank BCA: <strong>8829-1234-5678</strong><br>A.N. PT FITGYM INDONESIA</div>
                </div>
            </div>
        </div>

        <!-- Form Pembayaran & Voucher -->
        <div class="col-lg-7">
            <div class="card glow-card p-4">
                <h4 class="mb-3">Konfirmasi & Pembayaran</h4>
                
                <form action="{{ route('member.checkout.process', $plan) }}" method="POST" enctype="multipart/form-data" id="checkout-form">
                    @csrf
                    
                    <!-- Voucher Section -->
                    <div class="mb-4">
                        <label class="form-label">Punya Kode Voucher?</label>
                        <div class="input-group">
                            <input type="text" name="voucher_code" id="voucher_code"
                                   value="{{ old('voucher_code') }}"
                                   class="form-control text-uppercase @error('voucher_code') is-invalid @enderror"
                                   placeholder="Contoh: FITHEMAT">
                            <button type="button" class="btn btn-outline-warning" id="btn-check-voucher">
                                Terapkan Voucher
                            </button>
                        </div>
                        <div id="voucher-msg" class="form-text mt-1"></div>
                        @error('voucher_code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Upload Bukti Bayar -->
                    <div class="mb-4">
                        <label class="form-label">Unggah Bukti Transfer / Pembayaran <span class="text-danger">*</span></label>
                        <input type="file" name="proof_file" id="proof_file"
                               class="form-control @error('proof_file') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,.webp,.pdf" required>
                        <div class="form-text">Format didukung: JPG, PNG, WEBP, PDF (Maksimal 3 MB).</div>
                        @error('proof_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label small text-secondary" for="terms">
                            Saya telah mentransfer nominal sesuai total tagihan dan mengunggah bukti pembayaran yang sah.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                        ✦ Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCheck = document.getElementById('btn-check-voucher');
    const voucherInput = document.getElementById('voucher_code');
    const voucherMsg = document.getElementById('voucher-msg');
    const discountRow = document.getElementById('discount-row');
    const discountAmount = document.getElementById('discount-amount');
    const totalAmount = document.getElementById('total-amount');

    btnCheck.addEventListener('click', function () {
        const code = voucherInput.value.trim();
        if (!code) {
            voucherMsg.innerHTML = '<span class="text-warning">Silakan ketik kode voucher terlebih dahulu.</span>';
            return;
        }

        voucherMsg.innerHTML = '<span class="text-secondary">Memeriksa voucher...</span>';

        fetch("{{ route('member.checkout.voucher', $plan) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200 && res.body.valid) {
                voucherMsg.innerHTML = '<span class="text-success">✓ ' + res.body.message + ' Hemat ' + res.body.discount_formatted + '</span>';
                discountRow.style.setProperty('display', 'flex', 'important');
                discountAmount.textContent = '-' + res.body.discount_formatted;
                totalAmount.textContent = res.body.total_formatted;
            } else {
                voucherMsg.innerHTML = '<span class="text-danger">✕ ' + (res.body.message || 'Voucher tidak valid.') + '</span>';
                discountRow.style.setProperty('display', 'none', 'important');
                totalAmount.textContent = 'Rp {{ number_format($plan->price, 0, ',', '.') }}';
            }
        })
        .catch(err => {
            voucherMsg.innerHTML = '<span class="text-danger">Gagal memeriksa voucher. Coba lagi.</span>';
        });
    });
});
</script>
@endsection
