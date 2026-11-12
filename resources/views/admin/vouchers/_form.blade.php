@php $voucher = $voucher ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Kode Voucher</label>
        <input type="text" name="code" value="{{ old('code', $voucher?->code) }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror"
               placeholder="CONTOH: FITHEMAT">
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Tipe Diskon</label>
        <select name="type" class="form-select @error('type') is-invalid @enderror">
            <option value="percent" @selected(old('type', $voucher?->type) === 'percent')>Persentase (%)</option>
            <option value="fixed" @selected(old('type', $voucher?->type) === 'fixed')>Nominal Tetap (Rp)</option>
        </select>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nilai Diskon (% atau Rp)</label>
        <input type="number" step="any" name="value" value="{{ old('value', $voucher?->value) }}"
               class="form-control @error('value') is-invalid @enderror"
               placeholder="Contoh: 20 untuk 20% atau 50000 untuk Rp 50.000">
        @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Kuota Pemakaian</label>
        <input type="number" name="quota" value="{{ old('quota', $voucher?->quota ?? 50) }}"
               class="form-control @error('quota') is-invalid @enderror">
        @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if($voucher)
            <div class="form-text">Sudah dipakai: {{ $voucher->used_count }} kali.</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Berlaku Mulai</label>
        <input type="date" name="valid_from"
               value="{{ old('valid_from', $voucher?->valid_from?->format('Y-m-d') ?? today()->format('Y-m-d')) }}"
               class="form-control @error('valid_from') is-invalid @enderror">
        @error('valid_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Berlaku Sampai</label>
        <input type="date" name="valid_until"
               value="{{ old('valid_until', $voucher?->valid_until?->format('Y-m-d') ?? today()->addMonth()->format('Y-m-d')) }}"
               class="form-control @error('valid_until') is-invalid @enderror">
        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
           @checked(old('is_active', $voucher?->is_active ?? true))>
    <label class="form-check-label" for="is_active">Voucher aktif dan dapat digunakan</label>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Batal</a>
