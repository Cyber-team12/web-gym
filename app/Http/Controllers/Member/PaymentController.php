<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;

        $payments = Payment::with(['plan', 'voucher'])
            ->where('user_id', auth()->id())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('member.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load(['plan', 'voucher', 'membership']);

        return view('member.payments.show', compact('payment'));
    }
}
