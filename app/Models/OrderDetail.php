<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{

    protected $fillable = ['order_id', 'product_variant_id', 'quantity', 'unit_price'];
    public function order()
    {
        return $this->belongsTo(Order::class,'address');
    }

    public function productVariant()
    {
        return $this->belongsTo(product_variants::class, 'product_variant_id', 'id')->withDefault();
    }
    public function product()
{
    return $this->belongsTo(Products::class);
}
}
