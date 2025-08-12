<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Bill extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public float $productsTotal;
    public float $shippingFee;
    public float $voucherDiscount;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        // Load necessary relationships
        $order->load([
            'orderDetails.productVariant.product.thumbnail',
            'orderDetails.productVariant.product.images',
            'orderDetails.productVariant.size',
            'orderDetails.productVariant.color',
            'voucher'
        ]);
        
        $this->order = $order;
        // Tính tổng từ OrderDetail
        $this->shippingFee = $order->orderDetails->sum('ship_price');
        $this->voucherDiscount = $order->orderDetails->sum('voucher_discount');
        $this->productsTotal = $order->orderDetails->sum('total') - $this->shippingFee;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $publicUrl = route('orders.public.show', $this->order->order_code);

        return $this
            ->subject("Hóa đơn đơn hàng #{$this->order->order_code}")
            ->markdown('emails.bill')
            ->with([
                'order' => $this->order,
                'details' => $this->order->orderDetails,
                'productsTotal' => $this->productsTotal,
                'shippingFee' => $this->shippingFee,
                'voucherDiscount' => $this->voucherDiscount,
                'publicUrl' => $publicUrl

            ]);
    }
}