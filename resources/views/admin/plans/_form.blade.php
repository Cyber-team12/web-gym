@php $plan = $plan ?? null; @endphp

<div class="mb-3">
    <label class="form-label">Nama Paket</label>
    <input type="text" name="name" value="{{ old('name', $plan?->name) }}"
           class="form-control @error('name') is-invalid @enderror">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Durasi (hari)</label>
        <input type="number" name="duration_days" value="{{ old('duration_days', $plan?->duration_days) }}"
               class="form-control @error('duration_days') is-invalid @enderror">
        @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $plan?->price) }}"
               class="form-control @error('price') is-invalid @enderror">
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $plan?->description) }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
           @checked(old('is_active', $plan?->is_active ?? true))>
    <label class="form-check-label" for="is_active">Paket aktif</label>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Batal</a>