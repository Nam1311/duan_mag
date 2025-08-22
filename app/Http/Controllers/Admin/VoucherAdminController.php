<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class VoucherAdminController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::paginate(5);
        return view('admin.khuyenmai', ['vouchers' => $vouchers]);
    }

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

        if (Voucher::where('code', $validated['code'])->exists()) {
            return redirect()->back()->with('error', 'Voucher bạn tạo đã tồn tại!');
        }

        $voucher = Voucher::create($validated);

        // 🔔 Thêm notification mới khi tạo voucher
        DB::table('notifications')->insert([
            'type'       => 'voucher',
            'title'      => 'Voucher mới',
            'message'    => "Voucher {$voucher->code} đã được tạo thành công!",
            'voucher_id' => $voucher->id,
            'is_read'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Thêm voucher thành công!');
    }

    public function update(Request $request, string $id)
    {
        if (Voucher::where('code', $request->code)->where('id', '!=', $id)->exists()) {
            return back()->with('error', 'Mã voucher đã tồn tại.')->withInput();
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vouchers')->ignore($id),
            ],
            'discount_amount' => 'required|numeric|min:0',
            'value_type' => 'required|in:percent,fixed',
            'start_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:start_date',
            'quantity' => 'required|integer|min:1',
        ]);

        $voucher = Voucher::findOrFail($id);
        $voucher->update($validated);

        // 🔔 Cập nhật notification có sẵn thay vì tạo mới
        DB::table('notifications')
            ->where('voucher_id', $voucher->id)
            ->update([
                'title'      => 'Cập nhật voucher',
                'message'    => "Voucher {$voucher->code} đã được cập nhật!",
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    public function destroy(string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return redirect()->back()->with('error', 'Voucher không tồn tại.');
        }

        $code = $voucher->code;
        $voucher->delete();

        // 🔔 Thay vì xóa notification -> cập nhật thành “Voucher hết hạn”
        DB::table('notifications')
            ->where('voucher_id', $id)
            ->update([
                'title'      => 'Voucher hết hạn',
                'message'    => "Voucher {$code} đã hết hạn hoặc bị xóa!",
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Xóa voucher thành công.');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $vouchers = Voucher::query()
            ->where('code', 'like', '%' . $keyword . '%')
            ->orWhere('discount_amount', 'like', '%' . $keyword . '%')
            ->paginate(5);

        return view('admin.khuyenmai', [
            'vouchers' => $vouchers,
            'keyword'  => $keyword
        ]);
    }
}
