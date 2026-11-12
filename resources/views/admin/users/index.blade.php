@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Manajemen Pengguna & Member</h2>
            <p class="text-secondary small mb-0">Kelola akun, hak akses role, dan status keaktifan pengguna.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
    </div>

    @include('partials.flash')

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama atau email...">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">Semua role</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="member" @selected(request('role') === 'member')>Member</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill">Terapkan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        @include('partials.sort-th', ['col' => 'name', 'label' => 'Nama'])
                        @include('partials.sort-th', ['col' => 'email', 'label' => 'Email'])
                        <th>Telepon</th>
                        @include('partials.sort-th', ['col' => 'role', 'label' => 'Role'])
                        <th>Status</th>
                        @include('partials.sort-th', ['col' => 'created_at', 'label' => 'Terdaftar'])
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>
                        <td class="fw-bold">
                            {{ $u->name }}
                            @if($u->is(auth()->user()))
                                <span class="badge bg-warning-subtle text-warning ms-1">Anda</span>
                            @endif
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->phone ?? '-' }}</td>
                        <td>
                            <span class="role-pill role-{{ $u->role }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-warning">Ubah</a>
                            @if(! $u->is(auth()->user()))
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">Data pengguna tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
