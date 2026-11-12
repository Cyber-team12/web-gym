<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin FitGym',
            'email' => 'admin@gym.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $member = User::create([
            'name' => 'Member Setia',
            'email' => 'member@gym.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'role' => 'member',
            'is_active' => true,
        ]);

        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gym.com',
            'password' => Hash::make('password'),
            'phone' => '081234567892',
            'role' => 'member',
            'is_active' => true,
        ]);

        $siti = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@gym.com',
            'password' => Hash::make('password'),
            'phone' => '081234567893',
            'role' => 'member',
            'is_active' => true,
        ]);

        // Membership Plans
        $planHarian = MembershipPlan::create([
            'name' => 'Paket Harian',
            'duration_days' => 1,
            'price' => 25000,
            'description' => 'Akses 1 hari ke seluruh fasilitas gym, cardio, dan loker harian.',
            'is_active' => true,
        ]);

        $planBulanan = MembershipPlan::create([
            'name' => 'Paket Bulanan Basic',
            'duration_days' => 30,
            'price' => 150000,
            'description' => 'Akses tak terbatas selama 30 hari, bebas alat cardio & angkat beban.',
            'is_active' => true,
        ]);

        $plan3Bulan = MembershipPlan::create([
            'name' => 'Paket 3 Bulan Pro',
            'duration_days' => 90,
            'price' => 400000,
            'description' => 'Paket hemat 3 bulan, termasuk 1 sesi konsultasi personal trainer.',
            'is_active' => true,
        ]);

        $planTahunan = MembershipPlan::create([
            'name' => 'Paket Tahunan VIP',
            'duration_days' => 365,
            'price' => 1400000,
            'description' => 'Akses VIP 1 tahun penuh, loker eksklusif, dan free merchandise gym.',
            'is_active' => true,
        ]);

        // Vouchers
        $voucherDiskon = Voucher::create([
            'code' => 'FITHEMAT',
            'type' => 'percent',
            'value' => 20,
            'quota' => 50,
            'used_count' => 1,
            'valid_from' => today()->subDays(7),
            'valid_until' => today()->addDays(30),
            'is_active' => true,
        ]);

        $voucher50k = Voucher::create([
            'code' => 'DISKON50K',
            'type' => 'fixed',
            'value' => 50000,
            'quota' => 20,
            'used_count' => 0,
            'valid_from' => today()->subDays(7),
            'valid_until' => today()->addDays(30),
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'KEDALUWARSA',
            'type' => 'fixed',
            'value' => 25000,
            'quota' => 10,
            'used_count' => 0,
            'valid_from' => today()->subDays(30),
            'valid_until' => today()->subDay(),
            'is_active' => true,
        ]);

        // Transactions & Memberships
        // 1. Approved Payment & Active Membership for Member Setia
        $paymentApproved = Payment::create([
            'user_id' => $member->id,
            'plan_id' => $plan3Bulan->id,
            'voucher_id' => $voucherDiskon->id,
            'amount' => 400000,
            'discount' => 80000,
            'total' => 320000,
            'proof_path' => null,
            'status' => 'approved',
            'validated_by' => $admin->id,
            'validated_at' => now()->subDays(10),
        ]);

        $membershipMember = Membership::create([
            'user_id' => $member->id,
            'plan_id' => $plan3Bulan->id,
            'payment_id' => $paymentApproved->id,
            'start_date' => today()->subDays(10),
            'end_date' => today()->addDays(80),
            'status' => 'active',
        ]);

        // Attendances for Member Setia
        Attendance::create([
            'user_id' => $member->id,
            'membership_id' => $membershipMember->id,
            'checkin_at' => now()->subDays(5)->setTime(8, 30),
            'recorded_by' => $admin->id,
        ]);

        Attendance::create([
            'user_id' => $member->id,
            'membership_id' => $membershipMember->id,
            'checkin_at' => now()->subDays(2)->setTime(16, 45),
            'recorded_by' => $admin->id,
        ]);

        Attendance::create([
            'user_id' => $member->id,
            'membership_id' => $membershipMember->id,
            'checkin_at' => now()->setTime(9, 15),
            'recorded_by' => $admin->id,
        ]);

        // 2. Pending Payment for Budi Santoso (ready for admin validation demo)
        Payment::create([
            'user_id' => $budi->id,
            'plan_id' => $planBulanan->id,
            'voucher_id' => null,
            'amount' => 150000,
            'discount' => 0,
            'total' => 150000,
            'proof_path' => null,
            'status' => 'pending',
            'created_at' => now()->subHours(2),
        ]);

        // 3. Rejected Payment for Siti Rahma
        Payment::create([
            'user_id' => $siti->id,
            'plan_id' => $planHarian->id,
            'voucher_id' => null,
            'amount' => 25000,
            'discount' => 0,
            'total' => 25000,
            'proof_path' => null,
            'status' => 'rejected',
            'reject_reason' => 'Bukti transfer buram dan tidak terbaca. Harap upload ulang bukti yang jelas.',
            'validated_by' => $admin->id,
            'validated_at' => now()->subDay(),
        ]);
    }
}
