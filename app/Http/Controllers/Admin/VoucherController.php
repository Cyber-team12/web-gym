<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Voucher;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $sortable = ['code', 'value', 'quota', 'valid_until', 'created_at'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';
        $today = today();

        $vouchers = Voucher::query()
            ->when($request->q, fn ($q, $s) => $q->where('code', 'like', "%{$s}%"))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->status, function ($q, $status) use ($today) {
                match ($status) {
                    'active' => $q->where('is_active', true)
                        ->whereDate('valid_from', '<=', $today)
                        ->whereDate('valid_until', '>=', $today),
                    'expired' => $q->whereDate('valid_until', '<', $today),
                    'inactive' => $q->where('is_active', false),
                    default => null,
                };
            })
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(VoucherRequest $request)
    {
        $data = $request->validated();
        $data['used_count'] = 0;

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $voucher->update($request->validated());

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        try {
            $voucher->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Voucher tidak bisa dihapus karena sudah dipakai transaksi. Nonaktifkan saja switch statusnya.');
        }

        return back()->with('success', 'Voucher berhasil dihapus.');
    }
}
