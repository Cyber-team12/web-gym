<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAndFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_plans_search_and_filter(): void
    {
        MembershipPlan::create(['name' => 'Paket Alpha', 'duration_days' => 10, 'price' => 50000, 'is_active' => true]);
        MembershipPlan::create(['name' => 'Paket Beta', 'duration_days' => 20, 'price' => 100000, 'is_active' => false]);

        // Search
        $response = $this->actingAs($this->admin)->get('/admin/plans?q=Alpha');
        $response->assertStatus(200);
        $response->assertSee('Paket Alpha');
        $response->assertDontSee('Paket Beta');

        // Filter status
        $filterActive = $this->actingAs($this->admin)->get('/admin/plans?status=inactive');
        $filterActive->assertStatus(200);
        $filterActive->assertSee('Paket Beta');
        $filterActive->assertDontSee('Paket Alpha');
    }

    public function test_vouchers_search_and_filter(): void
    {
        Voucher::create([
            'code' => 'DISC20',
            'type' => 'percent',
            'value' => 20,
            'quota' => 10,
            'valid_from' => today(),
            'valid_until' => today()->addDays(5),
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'FIXED50',
            'type' => 'fixed',
            'value' => 50000,
            'quota' => 5,
            'valid_from' => today(),
            'valid_until' => today()->addDays(5),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/vouchers?type=fixed');
        $response->assertStatus(200);
        $response->assertSee('FIXED50');
        $response->assertDontSee('DISC20');
    }

    public function test_financial_report_aggregations(): void
    {
        $member = User::factory()->create();
        $plan = MembershipPlan::create(['name' => 'Paket VIP', 'duration_days' => 30, 'price' => 200000, 'is_active' => true]);

        Payment::create([
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'amount' => 200000,
            'discount' => 20000,
            'total' => 180000,
            'status' => 'approved',
            'created_at' => now(),
        ]);

        Payment::create([
            'user_id' => $member->id,
            'plan_id' => $plan->id,
            'amount' => 200000,
            'discount' => 0,
            'total' => 200000,
            'status' => 'pending', // Not counted in revenue
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/reports?start_date='.today()->format('Y-m-d').'&end_date='.today()->format('Y-m-d'));
        $response->assertStatus(200);
        $response->assertSee('Rp 180.000');
        $response->assertSee('Rp 20.000');
    }
}
