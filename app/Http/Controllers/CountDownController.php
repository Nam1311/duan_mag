<?php
namespace App\Http\Controllers;

use App\Models\ProductCountDown;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CountDownController extends Controller
{
    public function kiem_tra_flashsale()
    {
        $now = Carbon::now()->format('H:i:s');

        $countdowns = ProductCountDown::where('status', 'active')
            ->with('products')
            ->get();

        $changed = 0;

        foreach ($countdowns as $countdown) {
            $inTime = $now >= $countdown->start_hour && $now < $countdown->end_hour;
            $extraSale = floatval($countdown->percent_discount);

            foreach ($countdown->products as $product) {
                $baseSale = floatval($product->base_sale ?? 0);
                $newSale = $inTime ? min($baseSale + $extraSale, 100) : $baseSale;

                if ($product->sale != $newSale) {
                    $product->sale = $newSale;
                    $product->price = round($product->original_price * (1 - $newSale / 100), 2);
                    $product->save();
                    $changed++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => '⏱ Countdown xử lý xong.',
            'reload_page' => $changed > 0
        ]);
    }

}


