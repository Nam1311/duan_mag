<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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

    //    manh
    View::composer('*', function ($view) {
    if (Auth::check()) {
        // Lấy thông báo của user + thông báo chung (user_id = null)
        $notifications = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id'); // cho tất cả user
            })
            ->latest()
            ->take(10) // chỉ lấy 10 cái mới nhất
            ->get();

        $view->with('notifications', $notifications);
    } else {
        // Nếu chưa đăng nhập thì chỉ lấy thông báo chung
        $notifications = Notification::whereNull('user_id')
            ->latest()
            ->take(10)
            ->get();

        $view->with('notifications', $notifications);
    }
});
    }

}
