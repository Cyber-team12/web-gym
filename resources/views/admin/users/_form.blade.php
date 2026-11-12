@php $user = $user ?? null; @endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Contoh: John Doe">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user?->email) }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Contoh: user@gym.com">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nomor Telepon / WhatsApp</label>
        <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}"
               class="form-control @error('phone') is-invalid @enderror"
               placeholder="08123456789">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Role Akses</label>
        <select name="role" class="form-select @error('role') is-invalid @enderror"
                @disabled($user && $user->is(auth()->user()))>
            <option value="member" @selected(old('role', $user?->role) === 'member')>Member</option>
            <option value="admin" @selected(old('role', $user?->role) === 'admin')>Administrator</option>
        </select>
        @if($user && $user->is(auth()->user()))
            <input type="hidden" name="role" value="{{ $user->role }}">
            <div class="form-text text-warning">Kamu tidak dapat mengubah role akunmu sendiri.</div>
        @endif
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Password {{ $user ? '(Kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Minimal 8 karakter">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control"
               placeholder="Ulangi password">
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
           @checked(old('is_active', $user?->is_active ?? true))
           @disabled($user && $user->is(auth()->user()))>
    <label class="form-check-label" for="is_active">Akun Aktif (Dapat Login)</label>
    @if($user && $user->is(auth()->user()))
        <input type="hidden" name="is_active" value="1">
    @endif
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
