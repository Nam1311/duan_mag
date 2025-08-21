<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\Product_categories;
use App\Models\sizes;

class ProductController extends Controller
{
    public function ProductAll(Request $request)
    {
        $category = $request->category;
        $size     = $request->size;
        $price    = $request->price;

        $productAll = Products::with([
                'thumbnail',
                'variants' => function ($q) {
                    $q->where('quantity', '>', 0);
                }
            ])
            ->where('is_active', '>', 0)
            ->whereHas('variants', function ($q) {
                $q->where('quantity', '>', 0);
            });

        // Lọc danh mục
        if (!empty($category)) {
            $productAll->whereIn('category_id', (array) $category);
        }

        // Lọc size
        if (!empty($size)) {
            $productAll->whereHas('variants', function ($q) use ($size) {
                $q->where('size_id', $size)
                ->where('quantity', '>', 0);
            });
        }

        // Lọc theo giá
        if (!empty($price)) {
            if ($price == 1) {
                $productAll->where('price', '<', 100000);
            } elseif ($price == 2) {
                $productAll->whereBetween('price', [100000, 200000]);
            } elseif ($price == 3) {
                $productAll->whereBetween('price', [200000, 300000]);
            } elseif ($price == 4) {
                $productAll->where('price', '>', 300000);
            }
        }


        $productAll = $productAll->select('id', 'name', 'sale', 'price', 'original_price')
                                ->paginate(12)
                                ->appends($request->query());

        $total = $productAll->total();
        $categories = Product_categories::select('id', 'name')->get();
        $sizes = sizes::select('id', 'name')->get();

        return view('product', compact('productAll', 'total', 'categories', 'sizes'));
    }


    public function ProductFeatured()
    {
        $productAll = Products::with('thumbnail')
            ->where('is_featured', 1)
            ->where('is_active', '>', 0)
            ->select('id', 'name', 'sale', 'price', 'original_price')
            ->paginate(12);

        $total = $productAll->total();
        $categories = Product_categories::select('id', 'name')->get();
        $sizes = sizes::select('id', 'name')->get();

        return view('product', compact('productAll', 'total', 'categories', 'sizes'));
    }

    public function ProductBestseller()
    {
        $productAll = Products::with('thumbnail')
            ->orderBy('sold_count', 'desc')
            ->select('id', 'name', 'sale', 'price', 'original_price', 'sold_count')
            ->paginate(12);

        $total = $productAll->total();
        $categories = Product_categories::select('id', 'name')->get();
        $sizes = sizes::select('id', 'name')->get();

        return view('product', compact('productAll', 'total', 'categories', 'sizes'));
    }

    public function ProductPriceLowToHight()
    {
        $productAll = Products::with('thumbnail')
            ->orderBy('price', 'asc')
            ->select('id', 'name', 'sale', 'price', 'original_price', 'sold_count')
            ->paginate(12);

        $total = $productAll->total();
        $categories = Product_categories::select('id', 'name')->get();
        $sizes = sizes::select('id', 'name')->get();

        return view('product', compact('productAll', 'total', 'categories', 'sizes'));
    }

    public function ProductPriceHightToLow()
    {
        $productAll = Products::with('thumbnail')
            ->orderBy('price', 'desc')
            ->select('id', 'name', 'sale', 'price', 'original_price', 'sold_count')
            ->paginate(12);

        $total = $productAll->total();
        $categories = Product_categories::select('id', 'name')->get();
        $sizes = sizes::select('id', 'name')->get();

        return view('product', compact('productAll', 'total', 'categories', 'sizes'));
    }

    // sp gơi ý tìm kiếm
    public function searchSuggestions(Request $request)
    {
        $keyword = $request->get('keyword');

        $products = Products::with('thumbnail')
            ->where('name', 'like', '%' . $keyword . '%')
            ->take(5)
            ->get();

        $results = $products->map(function ($product) {

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->thumbnail ? asset($product->thumbnail->path) : asset('img/kocoanh.png'),
            ];
        });

        return response()->json($results);
    }


    // kq tìm kiếm
    public function search(Request $request)
    {
        $keyword = trim($request->get('keyword'));

        if (empty($keyword)) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm!');
        }

        $productAll = Products::with('thumbnail')
        ->where('name', 'like', '%' . $keyword . '%')
        ->select('id', 'name', 'base_sale', 'sale', 'price', 'original_price') // 👈 thêm base_sale
        ->where('is_active', '>', 0)
        ->paginate(12)
        ->appends(['keyword' => $keyword]);


        $total = $productAll->total();

        return view('searchpage', compact('productAll', 'total', 'keyword'));
    }
}
