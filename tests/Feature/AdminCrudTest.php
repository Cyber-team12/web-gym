<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_create_plan_with_validation(): void
    {
        // Validation fails if required fields missing
        $response = $this->actingAs($this->admin)->post('/admin/plans', []);
        $response->assertSessionHasErrors(['name', 'duration_days', 'price']);

        // Success create
        $response = $this->actingAs($this->admin)->post('/admin/plans', [
            'name' => 'Paket Platinum',
            'duration_days' => 60,
            'price' => 300000,
            'description' => 'Akses 2 bulan penuh.',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/plans');
        $this->assertDatabaseHas('membership_plans', [
            'name' => 'Paket Platinum',
            'duration_days' => 60,
            'price' => 300000,
        ]);
    }

    public function test_admin_can_update_and_delete_plan(): void
    {
        $plan = MembershipPlan::create([
            'name' => 'Paket Awal',
            'duration_days' => 30,
            'price' => 100000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/plans/{$plan->id}", [
            'name' => 'Paket Diperbarui',
            'duration_days' => 45,
            'price' => 150000,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/plans');
        $this->assertDatabaseHas('membership_plans', ['name' => 'Paket Diperbarui', 'duration_days' => 45]);

        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/plans/{$plan->id}");
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('membership_plans', ['id' => $plan->id]);
    }

    public function test_admin_can_create_voucher_with_validation(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/vouchers', []);
        $response->assertSessionHasErrors(['code', 'type', 'value', 'quota', 'valid_from', 'valid_until']);

        $response = $this->actingAs($this->admin)->post('/admin/vouchers', [
            'code' => 'promo2026',
            'type' => 'percent',
            'value' => 25,
            'quota' => 100,
            'valid_from' => today()->format('Y-m-d'),
            'valid_until' => today()->addDays(15)->format('Y-m-d'),
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/vouchers');
        $this->assertDatabaseHas('vouchers', [
            'code' => 'PROMO2026',
            'type' => 'percent',
            'value' => 25,
            'quota' => 100,
        ]);
    }

    public function test_admin_can_manage_users(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'User Baru',
            'email' => 'baru@gym.com',
            'phone' => '081299998888',
            'role' => 'member',
            'is_active' => '1',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'baru@gym.com', 'role' => 'member']);

        $newUser = User::where('email', 'baru@gym.com')->first();

        // Update user
        $this->actingAs($this->admin)->put("/admin/users/{$newUser->id}", [
            'name' => 'User Baru Ganti Nama',
            'email' => 'baru@gym.com',
            'phone' => '081299998888',
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'name' => 'User Baru Ganti Nama',
            'role' => 'admin',
        ]);
    }
}
