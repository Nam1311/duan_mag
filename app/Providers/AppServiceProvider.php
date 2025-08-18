<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                // Đếm số loại sản phẩm khác nhau trong giỏ hàng của user
                $cartCount = Cart::where('user_id', auth()->id())->count('product_variant_id');
            } else {
                $cart = session()->get('cart', []);
                // Đếm số loại sản phẩm khác nhau trong giỏ hàng session
                $cartCount = count($cart);
            }
            $view->with('cartCount', $cartCount);
        });

        View::composer('*', function ($view) {
            $view->with('settings', Setting::all());
        });
    }

}
