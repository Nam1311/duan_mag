<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Add this method to your OrderController
public function showPublic($order_code)
{
    $order = Order::with([
        'orderDetails.productVariant.product.thumbnail',
        'orderDetails.productVariant.product.images',
        'orderDetails.productVariant.size',
        'orderDetails.productVariant.color',
        'voucher',
        'user'
    ])->where('order_code', $order_code)->firstOrFail();

    // Tính tổng từ OrderDetail
    $shippingFee = $order->orderDetails->sum('ship_price');
    $voucherDiscount = $order->orderDetails->sum('voucher_discount');
    $productsTotal = $order->orderDetails->sum('total') - $shippingFee;

    // Debug information (remove in production)
    \Log::info('Order calculation debug:', [
        'order_code' => $order_code,
        'productsTotal' => $productsTotal,
        'shippingFee' => $shippingFee,
        'total_final' => $order->orderDetails->sum('total_final'),
        'voucher_discount' => $voucherDiscount,
        'voucher_info' => $order->voucher ? [
            'code' => $order->voucher->code,
            'type' => $order->voucher->value_type,
            'amount' => $order->voucher->discount_amount
        ] : null,
        'calculation' => $productsTotal + $shippingFee - $voucherDiscount
    ]);

    return view('order.public-show', compact('order', 'productsTotal', 'shippingFee', 'voucherDiscount'));
}
}
