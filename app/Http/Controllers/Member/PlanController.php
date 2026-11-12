<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        $activeMembership = auth()->user()->activeMembership;

        return view('member.plans.index', compact('plans', 'activeMembership'));
    }
}
