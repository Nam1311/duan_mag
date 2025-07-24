<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vouchers = Voucher::paginate(5);
        return view('admin.khuyenmai', compact('vouchers'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'value_type' => 'required|in:percent,fixed',
            'start_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:start_date',
            'quantity' => 'required|integer|min:1',
        ]);

        $exists = Voucher::withTrashed()
            ->where('code', $validated['code'])
            ->exists();

        if (Voucher::withTrashed()->where('code', $validated['code'])->exists()) {
            return redirect()->back()->with('error', 'Voucher bạn tạo đã tồn tại!');
        }


        $voucher = new Voucher();
        $voucher->code = $validated['code'];
        $voucher->discount_amount = $validated['discount_amount'];
        $voucher->start_date = $validated['start_date'];
        $voucher->expiration_date = $validated['expiration_date'];
        $voucher->quantity = $validated['quantity'] ?? 0; // nếu null thì gán 0
        $voucher->value_type = $validated['value_type'];
        $voucher->save();

        return redirect()->back()->with('success', 'Thêm voucher thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'value_type' => 'required|in:percent,fixed',
            'start_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:start_date',
            'quantity' => 'required|integer|min:1',
        ]);

        $exists = Voucher::withTrashed()
            ->where('code', $validated['code'])
            ->exists();

        if (Voucher::withTrashed()->where('code', $validated['code'])->exists()) {
            return redirect()->back()->with('error', 'Voucher bạn tạo đã tồn tại!');
        }


        $voucher = Voucher::findOrFail($id);

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Cập nhật khuyến mãi thành công!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return redirect()->back()->with('error', 'Voucher không tồn tại.');
        }

        $voucher->delete();
        return redirect()->back()->with('success', 'Xóa voucher thành công.');
    }
}
