<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\MembershipPlan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['name', 'duration_days', 'price', 'created_at'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $plans = MembershipPlan::query()
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(PlanRequest $request)
    {
        MembershipPlan::create($request->validated());

        return redirect()->route('admin.plans.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(MembershipPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(PlanRequest $request, MembershipPlan $plan)
    {
        $plan->update($request->validated());

        return redirect()->route('admin.plans.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(MembershipPlan $plan)
    {
        try {
            $plan->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Paket tidak bisa dihapus karena sudah dipakai transaksi. Nonaktifkan saja.');
        }

        return back()->with('success', 'Paket berhasil dihapus.');
    }
}
