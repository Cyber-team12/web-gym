<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $sortable = ['created_at', 'amount', 'total'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $payments = Payment::with(['user', 'plan', 'voucher', 'validator'])
            ->when($request->q, function ($q, $search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->plan_id, fn ($q, $pId) => $q->where('plan_id', $pId))
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        $plans = MembershipPlan::orderBy('name')->get();

        return view('admin.payments.index', compact('payments', 'plans'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'plan', 'voucher', 'validator', 'membership']);

        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi berstatus pending yang dapat divalidasi.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'approved',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
                'reject_reason' => null,
            ]);

            if ($payment->voucher_id && $payment->voucher) {
                $payment->voucher->increment('used_count');
            }

            // Hitung masa aktif membership
            $activeMembership = Membership::where('user_id', $payment->user_id)
                ->where('status', 'active')
                ->whereDate('end_date', '>=', today())
                ->latest('end_date')
                ->first();

            $startDate = $activeMembership ? $activeMembership->end_date->copy()->addDay() : today();
            $endDate = $startDate->copy()->addDays($payment->plan->duration_days);

            Membership::create([
                'user_id' => $payment->user_id,
                'plan_id' => $payment->plan_id,
                'payment_id' => $payment->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
            ]);
        });

        return redirect()->route('admin.payments.index')->with('success', "Pembayaran #{$payment->id} berhasil disetujui & membership telah diaktifkan.");
    }

    public function reject(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Hanya transaksi berstatus pending yang dapat ditolak.');
        }

        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:255'],
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $payment->update([
            'status' => 'rejected',
            'reject_reason' => $validated['reject_reason'],
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return redirect()->route('admin.payments.index')->with('success', "Pembayaran #{$payment->id} telah ditolak.");
    }
}
