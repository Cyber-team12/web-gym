<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/home');
        $response->assertRedirect('/login');
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@gym.com',
            'password' => 'password123',
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'user@gym.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user@gym.com',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@gym.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_member_cannot_access_admin_routes(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get('/admin/plans');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/plans');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_member_routes(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/member/plans');
        $response->assertStatus(403);
    }
}
