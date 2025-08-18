<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = (string) $oldStatus;
        $this->newStatus = (string) $newStatus;
    }

    public function build()
    {
        return $this->subject('Cập nhật trạng thái đơn hàng')
            ->view('emails.order_status_changed')
            // Nếu bạn muốn truyền thêm vào view (không bắt buộc vì là public props):
            // ->with([
            //     'order' => $this->order,
            //     'oldStatus' => $this->oldStatus,
            //     'newStatus' => $this->newStatus,
            // ])
            ->withSymfonyMessage(function ($message) {
                // Nhúng logo và đặt Content-ID là "mag-logo"
                $logoPath = public_path('img/logomail.png');
                if (is_file($logoPath)) {
                    $message->embedFromPath($logoPath, 'mag-logo');
                }
            });
    }
}
