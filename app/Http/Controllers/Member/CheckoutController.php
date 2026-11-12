<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\Voucher;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show(MembershipPlan $plan)
    {
        if (! $plan->is_active) {
            return redirect()->route('member.plans.index')->with('error', 'Paket yang dipilih sedang tidak aktif.');
        }

        return view('member.checkout.show', compact('plan'));
    }

    public function checkVoucher(Request $request, MembershipPlan $plan)
    {
        $code = strtoupper(trim((string) $request->code));
        if (empty($code)) {
            return response()->json([
                'valid' => false,
                'message' => 'Masukkan kode voucher.',
            ], 422);
        }

        $voucher = Voucher::where('code', $code)->first();
        if (! $voucher || ! $voucher->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher tidak valid, sudah kedaluwarsa, atau kuota habis.',
            ], 422);
        }

        $discount = $voucher->calculateDiscount((float) $plan->price);
        $total = max(0, (float) $plan->price - $discount);

        return response()->json([
            'valid' => true,
            'code' => $voucher->code,
            'type' => $voucher->type,
            'value' => (float) $voucher->value,
            'discount' => $discount,
            'discount_formatted' => 'Rp '.number_format($discount, 0, ',', '.'),
            'total' => $total,
            'total_formatted' => 'Rp '.number_format($total, 0, ',', '.'),
            'message' => 'Voucher berhasil diterapkan!',
        ]);
    }

    public function process(Request $request, MembershipPlan $plan)
    {
        if (! $plan->is_active) {
            return redirect()->route('member.plans.index')->with('error', 'Paket yang dipilih sudah tidak aktif.');
        }

        $request->validate([
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:3072'],
        ], [
            'proof_file.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_file.mimes' => 'Format bukti pembayaran harus JPG, PNG, WEBP, atau PDF.',
            'proof_file.max' => 'Ukuran file bukti pembayaran maksimal 3 MB.',
        ]);

        $voucher = null;
        $discount = 0.0;

        if ($request->filled('voucher_code')) {
            $code = strtoupper(trim($request->voucher_code));
            $voucher = Voucher::where('code', $code)->first();

            if (! $voucher || ! $voucher->isValid()) {
                return back()->withInput()->with('error', 'Voucher yang dimasukkan sudah tidak valid atau kuotanya habis.');
            }

            $discount = $voucher->calculateDiscount((float) $plan->price);
        }

        $amount = (float) $plan->price;
        $total = max(0, $amount - $discount);

        $path = $request->file('proof_file')->store('proofs', 'public');

        Payment::create([
            'user_id' => auth()->id(),
            'plan_id' => $plan->id,
            'voucher_id' => $voucher?->id,
            'amount' => $amount,
            'discount' => $discount,
            'total' => $total,
            'proof_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('member.payments.index')->with('success', 'Pembayaran berhasil diajukan! Admin akan memverifikasi bukti pembayaran Anda dalam waktu 1x24 jam.');
    }
}
