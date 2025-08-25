<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\User;
use App\Models\ProductCountDown;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = ProductCountDown::with('products')->get();
        $products = Products::all();

        return view('admin.countdown', ['promotions' => $promotions, 'products' => $products]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percent_discount' => 'required|numeric|min:1|max:100',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'status' => 'required|in:active,inactive',
        ]);

        $promotion = ProductCountDown::create($validated);
        $promotion->products()->sync($request->product_ids);

        // Cập nhật cột sale và price trong bảng products
        foreach ($request->product_ids as $productId) {
            $product = Products::find($productId);
            if ($product) {
                $product->sale += $validated['percent_discount'];
                if ($product->sale > 100) $product->sale = 100;
                $product->price = $product->original_price * (100 - $product->sale) / 100;
                $product->save();
            }
        }

        // Gửi notification cho tất cả user
        $this->sendOrUpdateFlashSaleNotification($promotion);

        return redirect()->route('admin.countdown.index')->with('success', 'Khuyến mãi đã được tạo!');
    }

    public function update(Request $request, ProductCountDown $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percent_discount' => 'required|numeric|min:1|max:100',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Trừ discount cũ nếu sale đang active
        if ($promotion->status === 'active') {
            foreach ($promotion->products as $oldProduct) {
                $oldProduct->sale -= $promotion->percent_discount;
                if ($oldProduct->sale < 0) $oldProduct->sale = 0;
                $oldProduct->price = $oldProduct->original_price * (100 - $oldProduct->sale) / 100;
                $oldProduct->save();
            }
        }

        $promotion->update($validated);
        $promotion->products()->sync($request->product_ids);

        // Cộng discount mới nếu active
        if ($validated['status'] === 'active') {
            foreach ($request->product_ids as $productId) {
                $product = Products::find($productId);
                if ($product) {
                    $product->sale += $validated['percent_discount'];
                    if ($product->sale > 100) $product->sale = 100;
                    $product->price = $product->original_price * (100 - $product->sale) / 100;
                    $product->save();
                }
            }
            $this->sendOrUpdateFlashSaleNotification($promotion);
        } else {
            // Nếu inactive → xóa notification
            DB::table('notifications')->where('promotion_id', $promotion->id)->delete();
        }

        return redirect()->route('admin.countdown.index')->with('success', 'Khuyến mãi đã được cập nhật!');
    }

    public function destroy($id)
    {
        $countDown = ProductCountDown::with('products')->findOrFail($id);

        // Trừ discount khỏi sản phẩm
        foreach ($countDown->products as $product) {
            $product->sale -= $countDown->percent_discount;
            if ($product->sale < 0) $product->sale = 0;
            $product->price = $product->original_price * (100 - $product->sale) / 100;
            $product->save();
        }

        $countDown->products()->detach();
        $countDown->delete();

        // Xóa tất cả notification liên quan
        DB::table('notifications')->where('promotion_id', $id)->delete();

        return redirect()->route('admin.countdown.index')->with('success', 'Đã xóa chương trình khuyến mãi và cập nhật lại sản phẩm!');
    }

    /**
     * Tạo hoặc cập nhật notification Flash Sale cho tất cả user
     */
    private function sendOrUpdateFlashSaleNotification($promotion)
    {
        $users = User::all();

        foreach ($users as $user) {
            $message = 'Giảm '.$promotion->percent_discount.'% từ '.$promotion->start_hour.' đến '.$promotion->end_hour;

            $existing = DB::table('notifications')
                ->where('user_id', $user->id)
                ->where('promotion_id', $promotion->id)
                ->first();

            if ($existing) {
                // Update nếu đã tồn tại
                DB::table('notifications')
                    ->where('id', $existing->id)
                    ->update([
                        'title' => 'Flash Sale hôm nay',
                        'message' => $message,
                    ]);
            } else {
                // Tạo mới nếu chưa có
                DB::table('notifications')->insert([
                    'user_id' => $user->id,
                    'promotion_id' => $promotion->id,
                    'type' => 'flash_sale',
                    'title' => 'Flash Sale hôm nay',
                    'message' => $message,
                    'is_read' => 0,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
