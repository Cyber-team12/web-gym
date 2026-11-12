@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Manajemen Voucher</h2>
            <p class="text-secondary small mb-0">Kelola kupon potongan harga dan masa berlaku promo.</p>
        </div>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">+ Tambah Voucher</a>
    </div>

    @include('partials.flash')

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode voucher...">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">Semua tipe diskon</option>
                <option value="percent" @selected(request('type') === 'percent')>Persentase (%)</option>
                <option value="fixed" @selected(request('type') === 'fixed')>Nominal Tetap (Rp)</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif & Berlaku</option>
                <option value="expired" @selected(request('status') === 'expired')>Kedaluwarsa</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Dinonaktifkan</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill">Terapkan</button>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        @include('partials.sort-th', ['col' => 'code', 'label' => 'Kode'])
                        <th>Tipe</th>
                        @include('partials.sort-th', ['col' => 'value', 'label' => 'Nilai'])
                        @include('partials.sort-th', ['col' => 'quota', 'label' => 'Kuota (Terpakai)'])
                        @include('partials.sort-th', ['col' => 'valid_until', 'label' => 'Berlaku Sampai'])
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vouchers as $voucher)
                    <tr>
                        <td>{{ $vouchers->firstItem() + $loop->index }}</td>
                        <td>
                            <span class="badge bg-dark-subtle text-warning border border-warning font-monospace fs-6">
                                {{ $voucher->code }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $voucher->type === 'percent' ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary' }}">
                                {{ $voucher->type === 'percent' ? 'Persen' : 'Nominal' }}
                            </span>
                        </td>
                        <td class="fw-bold">
                            @if ($voucher->type === 'percent')
                                {{ $voucher->value }}%
                            @else
                                Rp {{ number_format($voucher->value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td>
                            {{ $voucher->used_count }} / {{ $voucher->quota }}
                            @if ($voucher->used_count >= $voucher->quota)
                                <span class="badge bg-danger ms-1">Penuh</span>
                            @endif
                        </td>
                        <td>
                            {{ $voucher->valid_from->format('d M Y') }} - {{ $voucher->valid_until->format('d M Y') }}
                        </td>
                        <td>
                            @if (! $voucher->is_active)
                                <span class="badge bg-secondary">Nonaktif</span>
                            @elseif ($voucher->valid_until->lt(today()))
                                <span class="badge bg-danger">Kedaluwarsa</span>
                            @elseif ($voucher->valid_from->gt(today()))
                                <span class="badge bg-info">Akan Datang</span>
                            @else
                                <span class="badge bg-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-outline-warning">Ubah</a>
                            <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Data voucher tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $vouchers->links() }}
    </div>
</div>
@endsection
