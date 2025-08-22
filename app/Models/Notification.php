<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'order_id',
        'voucher_id',
        'review_id',
        'promotion_id',
        'type',
        'title',
        'message',
        'is_read',
    ];

    // Quan hệ với người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với đơn hàng (nếu cần)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với đánh giá (nếu cần)
    public function review()
    {
        return $this->belongsTo(Reviews::class);
    }

    // Quan hệ với khuyến mãi (nếu cần)
    public function promotion()
    {
        return $this->belongsTo(ProductCountDown::class);
    }
}