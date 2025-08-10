<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id', 'voucher_id', 'total_price', 'status_payment', 'payment_methods', 
        'status', 'order_code', 'address_id', 'note', 'phone', 'address'
    ];
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function address()
    {
        return $this->belongsTo(addresses::class);
    }
     public function shippingAddress()
    {
        return $this->belongsTo(addresses::class, 'address_id');
    }
   
}
