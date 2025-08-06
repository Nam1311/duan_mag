<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Bill extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public float $productsTotal;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->productsTotal = $order->total_price - 40000 + ($order->voucherDiscount ?? 0);
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
                'publicUrl' => $publicUrl

            ]);
    }
}