<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\product_variants;
use App\Models\sizes;
use App\Models\colors;
use App\Models\Product_images;
use App\Models\Product_categories;


class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $products = Products::with(['thumbnail', 'category', 'variants.size', 'variants.color', 'countDowns'])
            ->orderBy('id', 'desc')
            ->paginate(8);
        $categories = Product_categories::all();
        $sizes = sizes::all();
        $colors = colors::all();

        return view('admin.products', compact('products','categories','sizes','colors'));
    }

    public function viewDetail($id)
    {
        $product = Products::with(['category', 'images', 'variants.color', 'variants.size'])->findOrFail($id);

        return response()->json([
            'name' => $product->name,
            'category' => $product->category,
            'original_price' => number_format($product->original_price),
            'price' => number_format($product->price),
            'sale' => $product->sale,
            'stock' => $product->variants->sum('quantity'),
            'is_active' => $product->is_active,
            'description' => $product->description,
            'image' => asset($product->images->first()->path ?? 'img/default.jpg'),
            'variants' => $product->variants->map(function ($v) {
                return [
                    'color' => $v->color->name ?? '',
                    'size' => $v->size->name ?? '',
                    'quantity' => $v->quantity
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'original_price' => 'required|numeric|min:0',
            'sale' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable|string',
            'variants' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $originalPrice = $request->original_price;
        $sale = $request->sale ?? 0;
        $price = round($originalPrice * (1 - $sale / 100));
        $slug = Str::slug($request->name);

        $product = Products::create([
            'name' => $request->name,
            'slug' => $slug,
            'original_price' => $originalPrice,
            'price' => $price,
            'sale' => $sale,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'is_active' => 1,
            'sku' => $this->generateUniqueCode('MAG-')
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $fileName = time() . '_' . $index . '_' . Str::slug($file->getClientOriginalName());
                $filePath = $file->storeAs('img/products', $fileName, 'public');

                Product_images::create([
                    'product_id' => $product->id,
                    'path' => 'storage/' . $filePath,
                    'order' => $index + 1,
                ]);
            }
        }

        $variants = json_decode($request->variants, true);

        if (!is_array($variants)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu biến thể không hợp lệ'], 400);
        }

        foreach ($variants as $variant) {
            if (
                !empty($variant['size']) &&
                !empty($variant['color']) &&
                isset($variant['quantity']) &&
                sizes::where('id', $variant['size'])->exists() &&
                colors::where('id', $variant['color'])->exists()
            ) {
                $color = colors::find($variant['color']);
                $size = sizes::find($variant['size']);
                $colorName = $color ? Str::upper(Str::slug($color->name, '')) : 'UNKNOWN';
                $sizeName = $size ? Str::upper(Str::slug($size->name, '')) : 'UNKNOWN';
                $variantSku = $product->sku . '-' . $sizeName . '-' . $colorName;

                product_variants::create([
                    'product_id' => $product->id,
                    'size_id' => $variant['size'],
                    'color_id' => $variant['color'],
                    'quantity' => $variant['quantity'],
                    'sku' => $variantSku
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Size hoặc màu không hợp lệ'], 400);
            }
        }
    }

    public function destroy($id)
    {
        $product = Products::findOrFail($id);
        $product->delete();
        $variant = product_variants::find($id);
        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy biến thể']);
        }
        $variant->delete();

        return response()->json(['success' => true, 'message' => 'Sản phẩm đã được xóa']);
    }

    public function deletevariant($id)
    {
        $variant = product_variants::find($id);
        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy biến thể']);
        }

        $variant->delete();

        return response()->json(['success' => true, 'message' => 'Biến thể đã được xoá']);
    }


    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $originalPrice = $request->original_price;
        $sale = $request->sale ?? 0;
        $price = round($originalPrice * (1 - $sale / 100));

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'original_price' => $originalPrice,
            'sale' => $sale,
            'price' => $price,
            'category_id' => $request->category_id,
            'is_active' => $request->is_active,
        ]);

        // Xóa ảnh
        // if ($request->has('delete_images')) {
        //     foreach ($request->delete_images as $imgId) {
        //         $img = Product_images::find($imgId);
        //         if ($img) {
        //             Storage::disk('public')->delete($img->path);
        //             $img->delete();
        //         }
        //     }
        // }

        // Thay ảnh cũ
        // if ($request->hasFile('new_images')) {
        //     foreach ($request->file('new_images') as $imgId => $file) {
        //         $path = $file->store('img/products', 'public');
        //         Product_images::where('id', $imgId)->update(['path' => 'storage/' . $path]);
        //     }
        // }

        // Cập nhật biến thể
        if ($request->has('variants')) {
            foreach ($request->variants as $variantId => $variantData) {
                product_variants::where('id', $variantId)->update([
                    'size_id' => $variantData['size_id'],
                    'color_id' => $variantData['color_id'],
                    'quantity' => $variantData['quantity'],
                ]);
            }
        }
        // Thêm biến thể mới (nếu có)
        if ($request->has('new_variants')) {
            foreach ($request->new_variants as $variant) {
                product_variants::create([
                    'product_id' => $product->id, // hoặc $request->product_id
                    'size_id' => $variant['size_id'],
                    'color_id' => $variant['color_id'],
                    'quantity' => $variant['quantity'],
                ]);
            }
        }


        return back()->with('success', 'Cập nhật thành công');
    }

    protected function generateUniqueCode($prefix = 'MAG-', $length = 4)
    {
        return $prefix . strtoupper(substr(sha1(uniqid(mt_rand(), true)), 0, $length));
    }

    public function LocDanhMuc($id)
    {
        $categories = Product_categories::all();
        $category = Product_categories::findOrFail($id); // Lấy tên danh mục
        $products = Products::where('category_id', $id)->with('variants')->paginate(8);
        $sizes = sizes::all();
        $colors = colors::all();

        return view('admin.products', compact('products', 'categories', 'sizes', 'colors', 'category'));
    }


    public function LocTrangThai($status)
    {
        $categories = Product_categories::all();
        $products = Products::query();
        $sizes = sizes::all();
        $colors = colors::all();

        if ($status === 'Còn hàng') {
            $products->whereHas('variants', function ($query) {
                $query->where('quantity', '>', 0);
        });
        } else if ($status === 'Hết hàng') {
            $products->whereDoesntHave('variants', function ($query) {
                $query->where('quantity', '>', 0);
            });


        } else if ($status === 'Đang kinh doanh') {
            $products->where('is_active', '>', 0);
        } else if ($status === 'Ngừng kinh doanh') {
            $products->where('is_active', '=', 0);
        }

        $products = $products->with('variants')->paginate(8);

        return view('admin.products', compact('products', 'categories', 'sizes', 'colors', 'status'));
    }

    public function search(Request $request)
    {
        $categories = Product_categories::all();
        $sizes = sizes::all();
        $colors = colors::all();

        $keyword = $request->input('keyword');

        $products = Products::query()
            ->with('variants')
            ->where('name', 'like', '%' . $keyword . '%')
            ->paginate(8);

        return view('admin.products', compact('products', 'categories', 'sizes', 'colors', 'keyword'));
    }
}
