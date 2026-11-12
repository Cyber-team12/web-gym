<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_checkin_rules(): void
    {
        $admin = User::factory()->admin()->create();
        $memberWithActivePlan = User::factory()->create(['role' => 'member']);
        $memberWithoutPlan = User::factory()->create(['role' => 'member']);

        $plan = MembershipPlan::create([
            'name' => 'Paket Bulanan',
            'duration_days' => 30,
            'price' => 150000,
            'is_active' => true,
        ]);

        $payment = Payment::create([
            'user_id' => $memberWithActivePlan->id,
            'plan_id' => $plan->id,
            'amount' => 150000,
            'total' => 150000,
            'status' => 'approved',
        ]);

        Membership::create([
            'user_id' => $memberWithActivePlan->id,
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
            'start_date' => today()->subDays(5),
            'end_date' => today()->addDays(25),
            'status' => 'active',
        ]);

        // 1. Member without active plan cannot check in
        $failResponse = $this->actingAs($admin)->post('/admin/attendances', [
            'user_id' => $memberWithoutPlan->id,
        ]);
        $failResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('attendances', ['user_id' => $memberWithoutPlan->id]);

        // 2. Member with active plan can check in
        $successResponse = $this->actingAs($admin)->post('/admin/attendances', [
            'user_id' => $memberWithActivePlan->id,
        ]);
        $successResponse->assertSessionHas('success');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $memberWithActivePlan->id,
            'recorded_by' => $admin->id,
        ]);

        // 3. Duplicate check in on same day is blocked
        $duplicateResponse = $this->actingAs($admin)->post('/admin/attendances', [
            'user_id' => $memberWithActivePlan->id,
        ]);
        $duplicateResponse->assertSessionHas('error');
        $this->assertEquals(1, Attendance::where('user_id', $memberWithActivePlan->id)->count());
    }
}
