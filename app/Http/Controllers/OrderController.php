<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Add this method to your OrderController
public function showPublic($order_code)
{
    $order = Order::with(['orderDetails.productVariant.product', 'user'])
        ->where('order_code', $order_code)
        ->firstOrFail();

    // Calculate product total
    $productsTotal = $order->orderDetails->sum(function($item) {
        return ($item->price ?? $item->productVariant->product->price) * $item->quantity;
    });

    return view('order.public-show', compact('order', 'productsTotal'));
}
}
