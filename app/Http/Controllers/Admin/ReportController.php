<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : today()->format('Y-m-d');
        $planId = $request->plan_id;

        $baseQuery = Payment::with(['user', 'plan', 'voucher'])
            ->where('payments.status', 'approved')
            ->whereDate('payments.created_at', '>=', $startDate)
            ->whereDate('payments.created_at', '<=', $endDate)
            ->when($planId, fn ($q) => $q->where('payments.plan_id', $planId));

        // Metrik Ringkasan
        $metrics = (clone $baseQuery)->selectRaw('
            COUNT(payments.id) as total_transactions,
            COALESCE(SUM(payments.amount), 0) as gross_amount,
            COALESCE(SUM(payments.discount), 0) as total_discount,
            COALESCE(SUM(payments.total), 0) as total_revenue
        ')->first();

        // Rincian Penjualan Per Paket
        $planBreakdown = (clone $baseQuery)
            ->join('membership_plans', 'payments.plan_id', '=', 'membership_plans.id')
            ->groupBy('membership_plans.id', 'membership_plans.name')
            ->selectRaw('membership_plans.name, COUNT(payments.id) as count, SUM(payments.total) as total')
            ->get();

        // Daftar Transaksi Lengkap
        $payments = (clone $baseQuery)
            ->latest('payments.created_at')
            ->paginate(15)
            ->withQueryString();

        $plans = MembershipPlan::orderBy('name')->get();

        return view('admin.reports.index', compact(
            'payments',
            'metrics',
            'planBreakdown',
            'plans',
            'startDate',
            'endDate',
            'planId'
        ));
    }
}
