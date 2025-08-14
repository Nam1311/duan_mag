<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Voucher extends Model
{
    protected $table = 'vouchers';

     protected $fillable = [
        'code', 'discount_amount', 'value_type',
        'start_date', 'expiration_date', 'quantity'
    ];

    public function isValid()
    {
        $today = Carbon::today();
        return $this->quantity > 0 &&
               $today->between(Carbon::parse($this->start_date), Carbon::parse($this->end_date));
    }

    public function calculateDiscount($total)
    {
        if ($this->value_type === 'percent') {
            return round($total * $this->discount_amount / 100);
        }

        return $this->discount_amount;
    }
}
