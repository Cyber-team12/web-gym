<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Payment;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $stats = [
                'active_members' => User::where('role', 'member')
                    ->where('is_active', true)
                    ->whereHas('memberships', function ($q) {
                        $q->where('status', 'active')->whereDate('end_date', '>=', today());
                    })->count(),
                'members' => User::where('role', 'member')->count(),
                'visits_today' => Attendance::whereDate('checkin_at', today())->count(),
                'transactions_month' => (float) Payment::where('status', 'approved')
                    ->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->sum('total'),
            ];

            $todayAttendances = Attendance::with(['user', 'membership.plan'])
                ->whereDate('checkin_at', today())
                ->latest('checkin_at')
                ->limit(5)
                ->get();

            $pendingPayments = Payment::with(['user', 'plan'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get();

            return view('dashboard.admin', compact('stats', 'todayAttendances', 'pendingPayments'));
        }

        // Role: Member
        $stats = [
            'visits' => Attendance::where('user_id', $user->id)->count(),
        ];

        $activeMembership = $user->activeMembership;
        $recentAttendances = Attendance::with('membership.plan')
            ->where('user_id', $user->id)
            ->latest('checkin_at')
            ->limit(5)
            ->get();

        $pendingPayment = Payment::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('dashboard.member', compact('stats', 'activeMembership', 'recentAttendances', 'pendingPayment'));
    }
}
