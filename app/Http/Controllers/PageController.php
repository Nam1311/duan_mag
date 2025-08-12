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
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class PageController extends Controller
{
    public function home()
    {
        $products_sale = Products::with(['images', 'variants'])->where('products.sale', '>', 30)->take(8)->get();
        $products_is_featured = Products::with(['images', 'variants'])
            ->orderBy('views', 'desc')
            ->select('id', 'name', 'sale', 'price', 'original_price', 'sold_count', 'views')
            ->take(8)->get();
        $product_categories = Product_categories::all();
        $news = News::where('views', '>', 190)->take(6)->get();
        $product_new = Product_categories::with(['products' => function ($query) {
            $query->where('is_active', '>', 0)
                ->take(8);
        }])->get();


        $products_bestseller = Products::with([
                'thumbnail',
                'images',
                'variants'
            ])
            ->orderBy('sold_count', 'desc')
            ->select('id', 'name', 'sale', 'price', 'original_price', 'sold_count')
            ->take(8)
            ->get();


        // Lấy danh sách màu và kích thước toàn cục (nếu cần)
        $allColors = colors::all(); // Giả sử có model Color
        $allSizes = sizes::all();   // Giả sử có model Size



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

        $recommendedProducts = [];

        if (Auth::check()) {
            // Lấy đơn hàng gần nhất
            $latestOrder = Order::with(['orderDetails.productVariant.product.category'])
                ->where('user_id', Auth::id())
                ->latest()
                ->first();

            if ($latestOrder && $latestOrder->orderDetails->isNotEmpty()) {
                $firstDetail = $latestOrder->orderDetails->first();
                $product = $firstDetail->productVariant->product ?? null;

                if ($product && $product->category) {
                    $categoryId = $product->category->id;

                    $recommendedProducts = Products::where('category_id', $categoryId)
                        ->where('id', '!=', $product->id)
                        ->with('images')
                        ->get();
                    // Map thêm màu & size
                    $recommendedProducts->map(function ($product) {
                        $product->colors = $product->variants->pluck('color')->unique()->values();
                        $product->sizes  = $product->variants->pluck('size')->unique()->values();
                        return $product;
                    });
                }
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
            'sliders' => $sliders,
            'recommendedProducts' => $recommendedProducts,
            'allColors' => $allColors,
            'allSizes' => $allSizes
        ];

        return view('home', $data);
    }

    public function get_variant($id)
    {
        $product = Products::with(['variants.color', 'variants.size'])->findOrFail($id);
        $colors = $product->variants->map->color->unique('id')->values();
        $sizes = $product->variants->map->size->unique('id')->values();
        $variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'color_id' => $variant->color_id,
                'size_id' => $variant->size_id,
                'quantity' => $variant->quantity
            ];
        })->values();

        return response()->json([
            'colors' => $colors,
            'sizes' => $sizes,
            'variants' => $variants
        ]);
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

        // Lưu sản phẩm đã xem vào session
        $viewedProducts = session()->get('viewed_products', []);

        // Loại bỏ sản phẩm hiện tại nếu đã tồn tại để tránh trùng lặp
        $viewedProducts = array_filter($viewedProducts, function($productId) use ($id) {
            return $productId != $id;
        });

        // Thêm sản phẩm hiện tại vào đầu danh sách
        array_unshift($viewedProducts, (int)$id);

        // Giới hạn chỉ lưu 10 sản phẩm gần nhất
        $viewedProducts = array_slice($viewedProducts, 0, 10);

        // Cập nhật session
        session()->put('viewed_products', $viewedProducts);

$reviewDetail = reviews::with([
    'user',
    'replies.user',           // load user của reply
    'replies.replies.user'    // load reply của reply (2 cấp)
])
->where('product_id', $id)
->whereNull('parent_id')
->orderBy('created_at', 'desc')
->get();

        $data = [
            'product_detail' => $product_detail,
            'colors' => $colors,
            'sizes' => $sizes,
            'products' => $products,
            'reviewDetail' => $reviewDetail,
        ];

        return view('detail', $data);
    }
// ReviewController.php
 public function reply(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:reviews,id',
            'comment' => 'required|string|max:1000',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->back()->withErrors('Bạn cần đăng nhập để gửi phản hồi.');
        }

        $parentReview = reviews::findOrFail($request->parent_id);

        reviews::create([
            'product_id' => $parentReview->product_id,
            'parent_id' => $request->parent_id,
            'user_id' => $userId,
            'comment' => $request->comment,
            'rating' => null,
        ]);

        return redirect()->back()->with('success', 'Phản hồi của bạn đã được gửi thành công!');
    }



    public function getVariantQuantity(Request $request)
    {
        $productId = (int) $request->query('product_id');
        $colorName = $request->query('color');
        $sizeName = $request->query('size');

        $colorId = colors::where('name', $colorName)->value('id');
        $sizeId = $sizeName ? sizes::where('name', $sizeName)->value('id') : null;

        // Lấy thông tin sản phẩm và category
        $product = Products::with('category')->find($productId);
        if (!$product) {
            return response()->json(['quantity' => 0]);
        }

        // Kiểm tra category để quyết định logic tìm variant
        $categoryName = strtolower($product->category->name ?? '');
        $isAccessoryOrTrousers = str_contains($categoryName, 'phụ kiện') ||
                               str_contains($categoryName, 'quần') ||
                               str_contains($categoryName, 'accessories') ||
                               str_contains($categoryName, 'pants') ||
                               str_contains($categoryName, 'trousers');

        $variantQuery = product_variants::where('product_id', $productId)
            ->where('color_id', $colorId);

        if ($isAccessoryOrTrousers) {
            // Với phụ kiện/quần: chỉ cần màu, không cần size
            $variant = $variantQuery->whereNotNull('color_id')->first();
        } else {
            // Với áo và sản phẩm khác: cần cả size và màu
            if (!$sizeId) {
                return response()->json(['quantity' => 0, 'message' => 'Size is required for this product']);
            }
            $variant = $variantQuery->where('size_id', $sizeId)->first();
        }

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
