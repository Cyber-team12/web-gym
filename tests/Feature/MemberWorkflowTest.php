<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_checkout_flow_and_admin_approval(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);

        $plan = MembershipPlan::create([
            'name' => 'Paket Sebulan',
            'duration_days' => 30,
            'price' => 150000,
            'is_active' => true,
        ]);

        $voucher = Voucher::create([
            'code' => 'SUPERFIT',
            'type' => 'fixed',
            'value' => 50000,
            'quota' => 10,
            'used_count' => 0,
            'valid_from' => today()->subDay(),
            'valid_until' => today()->addDays(10),
            'is_active' => true,
        ]);

        // 1. Member views plans
        $response = $this->actingAs($member)->get('/member/plans');
        $response->assertStatus(200);
        $response->assertSee('Paket Sebulan');

        // 2. Member checks voucher
        $voucherCheck = $this->actingAs($member)->postJson("/member/checkout/{$plan->id}/voucher", [
            'code' => 'SUPERFIT',
        ]);
        $voucherCheck->assertStatus(200);
        $voucherCheck->assertJson(['valid' => true, 'discount' => 50000, 'total' => 100000]);

        // 3. Member submits checkout with payment proof
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');
        $checkoutResponse = $this->actingAs($member)->post("/member/checkout/{$plan->id}", [
            'voucher_code' => 'SUPERFIT',
            'proof_file' => $file,
        ]);

        $checkoutResponse->assertRedirect('/member/payments');
        $this->assertDatabaseHas('payments', [
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'voucher_id' => $voucher->id,
            'amount' => 150000,
            'discount' => 50000,
            'total' => 100000,
            'status' => 'pending',
        ]);

        $payment = Payment::where('user_id', $member->id)->first();
        Storage::disk('public')->assertExists($payment->proof_path);

        // 4. Admin approves payment
        $approveResponse = $this->actingAs($admin)->post("/admin/payments/{$payment->id}/approve");
        $approveResponse->assertRedirect('/admin/payments');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'approved',
            'validated_by' => $admin->id,
        ]);

        // Membership should be created and voucher used count incremented
        $this->assertDatabaseHas('memberships', [
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'used_count' => 1,
        ]);
    }

    public function test_admin_can_reject_payment_with_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => 'member']);

        $plan = MembershipPlan::create([
            'name' => 'Paket Harian',
            'duration_days' => 1,
            'price' => 25000,
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'amount' => 25000,
            'discount' => 0,
            'total' => 25000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/payments/{$payment->id}/reject", [
            'reject_reason' => 'Transfer tidak masuk ke rekening',
        ]);

        $response->assertRedirect('/admin/payments');
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'rejected',
            'reject_reason' => 'Transfer tidak masuk ke rekening',
        ]);
    }
}
