<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? $request->date : today()->format('Y-m-d');
        $showAll = $request->boolean('all_dates');

        $attendances = Attendance::with(['user', 'membership.plan', 'recordedBy'])
            ->when(! $showAll && $date, fn ($q) => $q->whereDate('checkin_at', $date))
            ->when($request->q, function ($q, $search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('checkin_at')
            ->paginate(15)
            ->withQueryString();

        // Get members with active memberships for quick checkin dropdown
        $activeMembers = User::where('role', 'member')
            ->where('is_active', true)
            ->whereHas('memberships', function ($q) {
                $q->where('status', 'active')->whereDate('end_date', '>=', today());
            })
            ->orderBy('name')
            ->get();

        return view('admin.attendances.index', compact('attendances', 'activeMembers', 'date', 'showAll'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ], [
            'user_id.required' => 'Pilih member terlebih dahulu.',
            'user_id.exists' => 'Member tidak ditemukan.',
        ]);

        $user = User::with('activeMembership.plan')->findOrFail($request->user_id);

        if (! $user->is_active) {
            return back()->with('error', "Akun member {$user->name} sedang dinonaktifkan.");
        }

        $membership = $user->activeMembership;
        if (! $membership) {
            return back()->with('error', "Member {$user->name} tidak memiliki membership aktif atau masa aktif telah habis.");
        }

        // Cek apakah sudah check-in hari ini
        $alreadyCheckedIn = Attendance::where('user_id', $user->id)
            ->whereDate('checkin_at', today())
            ->first();

        if ($alreadyCheckedIn) {
            $time = $alreadyCheckedIn->checkin_at->format('H:i');

            return back()->with('error', "Member {$user->name} sudah melakukan check-in hari ini pada pukul {$time} WIB.");
        }

        Attendance::create([
            'user_id' => $user->id,
            'membership_id' => $membership->id,
            'checkin_at' => now(),
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('success', "Presensi check-in berhasil dicatat untuk {$user->name} ({$membership->plan->name}).");
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Catatan presensi berhasil dihapus.');
    }
}
