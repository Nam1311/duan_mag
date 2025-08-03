<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Order $order)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'Thành công') {
            abort(403, 'Không hợp lệ');
        }

        // Load product variant kèm theo product + thumbnail + size
       $orderDetails = $order->orderDetails()->with([
    'productVariant.product.images', // 👈 thêm dòng này
    'productVariant.product.thumbnail', // nếu có ảnh đại diện riêng
    'productVariant.size'
])->get();

$productVariants = $orderDetails->pluck('productVariant');


        return view('review.form', compact('order', 'productVariants'));
    }

    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'required|string|max:500',
        ]);

        foreach ($data['reviews'] as $review) {
            reviews::create([
                'user_id' => auth()->id(),
                'product_id' => $review['product_id'],
                'rating' => $review['rating'],
                'comment' => $review['comment'],
            ]);
        }

        return redirect()->route('infouser')->with('success', 'Đánh giá thành công!');
    }
}
