<?php
namespace App\Models;
use App\Models\Products;
use Illuminate\Database\Eloquent\Model;

class ProductCountDown extends Model
{
    protected $fillable = [
        'name',
        'percent_discount',
        'start_hour',
        'end_hour',
        'status',
        'applied'
    ];

    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_count_down_pivot', 'count_down_id', 'product_id');
    }
}
