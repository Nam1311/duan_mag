<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\product_variants;
use App\Models\sizes;
use App\Models\colors;
use App\Models\News;
use App\Models\reviews;
use App\Models\Product_categories;
use App\Models\ProductCountDown;
use App\Models\Banners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PageController extends Controller
{
    public function home()
    {
        $products_sale = Products::with(['images', 'variants'])->where('products.sale', '>', 30)->take(8)->get();
        $products_is_featured = Products::with(['images', 'variants'])->orderBy('views', 'desc')->take(8)->get();
        $product_categories = Product_categories::all();
        $news = News::where('views', '>', 190)->take(6)->get();
        $product_new = Product_categories::with(['products' => function ($query) {
            $query->take(8);
        }])->get();
        $products_bestseller = Products::with('thumbnail')
            ->orderBy('sold_count', 'desc')
            ->select('id', 'name', 'sale', 'price', 'original_price', 'sold_count')
            ->take(8)->get();

        // ======== Flash Sale =========
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $currentHour = $now->format('H:i');
        $flash_sale_products = collect();
        $countdown = [
            'hours' => '00',
            'minutes' => '00',
            'seconds' => '00',
        ];

        $activePromotions = ProductCountDown::with('products')
            ->where('status', 'active')
            ->get();
        $sliders = Banners::where('status', '>', 0)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($activePromotions as $promotion) {
            if ($currentHour >= $promotion->start_hour && $currentHour <= $promotion->end_hour) {
                foreach ($promotion->products as $product) {
                    $product->flash_sale_percent = $promotion->percent_discount;
                    $flash_sale_products->push($product);
                }

                try {
                    $end = Carbon::createFromFormat('H:i:s', $promotion->end_hour, 'Asia/Ho_Chi_Minh');
                    if ($end->lessThan($now)) {
                        $end->addDay();
                    }
                    $timeLeft = $now->diffInSeconds($end);

                    $countdown['hours'] = str_pad(floor($timeLeft / 3600), 2, '0', STR_PAD_LEFT);
                    $countdown['minutes'] = str_pad(floor(($timeLeft % 3600) / 60), 2, '0', STR_PAD_LEFT);
                    $countdown['seconds'] = str_pad($timeLeft % 60, 2, '0', STR_PAD_LEFT);
                } catch (\Exception $e) {
                    $end = Carbon::createFromFormat('H:i', rtrim($promotion->end_hour, ':0'), 'Asia/Ho_Chi_Minh');
                    if ($end->lessThan($now)) {
                        $end->addDay();
                    }
                    $timeLeft = $now->diffInSeconds($end);

                    $countdown['hours'] = str_pad(floor($timeLeft / 3600), 2, '0', STR_PAD_LEFT);
                    $countdown['minutes'] = str_pad(floor(($timeLeft % 3600) / 60), 2, '0', STR_PAD_LEFT);
                    $countdown['seconds'] = str_pad($timeLeft % 60, 2, '0', STR_PAD_LEFT);
                }

                break;
            }
        }

        $data = [
            'products_sale' => $products_sale,
            'product_categories' => $product_categories,
            'products_is_featured' => $products_is_featured,
            'news' => $news,
            'product_new' => $product_new,
            'flash_sale_products' => $flash_sale_products->unique('id'),
            'countdown' => $countdown,
            'products_bestseller' => $products_bestseller,
            'sliders' => $sliders
        ];

        return view('home', $data);
    }

    public function detail($id)
    {
        $product_detail = Products::with(['images', 'variants'])->find($id);
        $colors = $product_detail->variants->pluck('color')->unique();
        $sizes = $product_detail->variants->pluck('size')->unique();

        $products = Products::with(['images', 'variants'])
            ->where('category_id', $product_detail->category_id)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();
        $product = Products::findOrFail($id);
        $product->increment('views');

        $reviewDetail = reviews::with('user')->where('product_id', $id)->get();

        $data = [
            'product_detail' => $product_detail,
            'colors' => $colors,
            'sizes' => $sizes,
            'products' => $products,
            'reviewDetail' => $reviewDetail,
        ];

        return view('detail', $data);
    }

    public function getVariantQuantity(Request $request)
    {
        $productId = (int) $request->query('product_id');
        $colorName = $request->query('color');
        $sizeName = $request->query('size');

        $colorId = colors::where('name', $colorName)->value('id');
        $sizeId = sizes::where('name', $sizeName)->value('id');

        $variant = product_variants::where('product_id', $productId)
            ->where('color_id', $colorId)
            ->where('size_id', $sizeId)
            ->first();

        if (!$variant) {
            return response()->json(['quantity' => 0]);
        }

        return response()->json([
            'quantity' => $variant->quantity,
            'sku' => $variant->sku,
            'product_variant_id' => $variant->id,
            'price' => $variant->product->price,
            'name' => $variant->product->name


        ]);
    }





}
