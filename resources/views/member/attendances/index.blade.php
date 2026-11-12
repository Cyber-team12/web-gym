@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Riwayat Kunjungan & Latihan</h2>
            <p class="text-secondary small mb-0">Catatan presensi setiap kali Anda datang berolahraga di gym.</p>
        </div>
        <div class="text-end">
            <span class="text-secondary small d-block">Total Kedatangan:</span>
            <span class="fs-4 fw-bold text-warning">{{ $totalVisits }} Kali</span>
        </div>
    </div>

    <!-- Filter Rentang Tanggal -->
    <div class="card glow-card mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-secondary small">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-secondary small">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary flex-fill">Terapkan Filter</button>
                    <a href="{{ route('member.attendances.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card glow-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Waktu Check-in</th>
                        <th>Paket yang Digunakan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $att)
                    <tr>
                        <td>{{ $attendances->firstItem() + $loop->index }}</td>
                        <td class="fw-bold">{{ $att->checkin_at->format('d F Y') }}</td>
                        <td class="text-warning fw-bold">{{ $att->checkin_at->format('H:i') }} WIB</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                {{ $att->membership?->plan?->name ?? 'Membership' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">Hadir & Berlatih</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-5">
                            <span class="empty-icon d-block mb-2">✦</span>
                            Belum ada riwayat kunjungan pada rentang tanggal ini.
                            <br>
                            <small>Silakan lakukan check-in di meja resepsionis gym saat Anda tiba.</small>
                        </td>
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
