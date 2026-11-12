@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Presensi & Check-in Member</h2>
            <p class="text-secondary small mb-0">Catat kunjungan harian member yang memiliki membership aktif.</p>
        </div>
    </div>

    @include('partials.flash')

    <!-- Form Check-in Baru -->
    <div class="card glow-card mb-4">
        <div class="card-body p-4">
            <h5 class="card-title text-warning mb-3">✦ Catat Check-in Hari Ini</h5>
            <form action="{{ route('admin.attendances.store') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-9">
                    <label class="form-label text-secondary small">Pilih Member (Hanya yang berstatus membership aktif)</label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Member untuk Check-in --</option>
                        @foreach($activeMembers as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->name }} ({{ $m->email }}) — Masa aktif s.d. {{ $m->activeMembership?->end_date?->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        ✓ Catat Check-in
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter & Riwayat Presensi -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama member / email...">
        </div>
        <div class="col-md-3">
            <input type="date" name="date" value="{{ $date }}" class="form-control" @disabled(request('all_dates'))>
        </div>
        <div class="col-md-3 d-flex align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="all_dates" value="1" id="all_dates"
                       @checked(request('all_dates')) onchange="this.form.submit()">
                <label class="form-check-label text-secondary" for="all_dates">
                    Tampilkan Semua Tanggal
                </label>
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill">Terapkan</button>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Paket Membership</th>
                        <th>Waktu Check-in</th>
                        <th>Petugas (Admin)</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $att)
                    <tr>
                        <td>{{ $attendances->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="fw-bold">{{ $att->user->name }}</div>
                            <small class="text-secondary">{{ $att->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $att->membership?->plan?->name ?? 'Membership' }}
                            </span>
                            <br>
                            <small class="text-secondary">
                                s.d. {{ $att->membership?->end_date?->format('d M Y') }}
                            </small>
                        </td>
                        <td class="fw-bold text-warning">
                            {{ $att->checkin_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td>
                            <small class="text-secondary">{{ $att->recordedBy->name ?? 'Sistem' }}</small>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('admin.attendances.destroy', $att) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus catatan presensi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">Belum ada data kunjungan pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
