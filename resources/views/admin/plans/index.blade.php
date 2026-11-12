@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Manajemen Paket Membership</h2>
            <p class="text-secondary small mb-0">Kelola daftar pilihan paket latihan, durasi, dan harga langganan.</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">+ Tambah Paket</a>
    </div>

    @include('partials.flash')

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama paket...">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary flex-fill">Terapkan</button>
            <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        @include('partials.sort-th', ['col' => 'name', 'label' => 'Nama Paket'])
                        @include('partials.sort-th', ['col' => 'duration_days', 'label' => 'Durasi'])
                        @include('partials.sort-th', ['col' => 'price', 'label' => 'Harga'])
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                    <tr>
                        <td>{{ $plans->firstItem() + $loop->index }}</td>
                        <td class="fw-bold">{{ $plan->name }}</td>
                        <td>{{ $plan->duration_days }} hari</td>
                        <td class="fw-bold text-warning">Rp {{ number_format($plan->price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $plan->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-warning">Ubah</a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus paket {{ $plan->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Data paket tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $plans->links() }}
    </div>
</div>
@endsection
