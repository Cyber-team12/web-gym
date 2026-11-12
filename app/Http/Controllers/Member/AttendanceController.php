<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $attendances = Attendance::with(['membership.plan'])
            ->where('user_id', auth()->id())
            ->when($startDate, fn ($q) => $q->whereDate('checkin_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('checkin_at', '<=', $endDate))
            ->latest('checkin_at')
            ->paginate(10)
            ->withQueryString();

        $totalVisits = Attendance::where('user_id', auth()->id())->count();

        return view('member.attendances.index', compact('attendances', 'totalVisits', 'startDate', 'endDate'));
    }
}
