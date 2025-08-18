<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\product_variants;
use App\Models\Products;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use function Laravel\Prompts\select;

class CartController extends Controller
{
    public function viewCart()
    {
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())
                ->with([
                    'productVariant.product.thumbnail',
                    'productVariant.product',
                    'productVariant.size',
                    'productVariant.color'
                ])
                ->get();
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->product->price * $item->quantity;
            });


            // $usedVoucherIds = Order::where('user_id', auth()->id())
            //     ->whereNotNull('voucher_id')
            //     ->pluck('voucher_id')
            //     ->toArray();

            $availableVouchers = Voucher::where('quantity', '>', 0)
                ->where('start_date', '<=', now())
                ->where('expiration_date', '>=', now())
                ->get();
        } else {
            $sessionCart = session()->get('cart', []);
            $cartItems = collect($sessionCart)->map(function ($item) {
                $variant = product_variants::with([
                    'product.thumbnail',
                    'product',
                    'size',
                    'color'
                ])->find($item['product_variant_id']);
                if (!$variant) {
                    return null;
                }
                return (object) [
                    'productVariant' => $variant,
                    'quantity' => $item['quantity'],
                ];
            })->filter();  // loại bỏ null

            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->product->price * $item->quantity;
            });
            $availableVouchers = null;

        }

        $appliedVoucherCode = session()->get('applied_voucher');
        $voucherDiscount = 0;
        if ($appliedVoucherCode) {
            $voucher = Voucher::where('code', $appliedVoucherCode)
                ->where('expiration_date', '>=', now())
                ->where('start_date', '<=', now())
                ->where('quantity', '>', 0)
                ->first();

            if ($voucher) {
                if ($voucher->value_type == 'fixed') {
                    $voucherDiscount = $voucher->discount_amount;
                } elseif ($voucher->value_type == 'percent') {
                    $voucherDiscount = $subtotal * ($voucher->discount_amount / 100);
                }
            } else {
                session()->forget('applied_voucher');
            }
        }
        
        $shippingFee = Setting::where('id', 1)->value('ship_price') ?? 40000;
        $total = $subtotal - $voucherDiscount + $shippingFee;
        $data = $this->getCartData();

        return view('Cart', compact('cartItems', 'subtotal', 'shippingFee', 'voucherDiscount', 'total', 'availableVouchers'), $data);
    }
    public function storeSessionCart(Request $request)
    {
        $productVariantId = $request->input('product_variant_id');
        $quantity = $request->input('quantity', 1);

        $cart = Session::get('cart', []);
        $found = false;
        foreach ($cart as &$item) {
            if ($item['product_variant_id'] == $productVariantId) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $cart[] = [
                'product_variant_id' => $productVariantId,
                'quantity' => $quantity,
            ];
        }
        Session::put('cart', $cart);
        return response()->json([
            'message' => 'Đã lưu vào session thành công',
            'cart' => $cart,
        ]);
    }
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);
        $productVariantId = $validated['product_variant_id'] ?? null;

        if (!$productVariantId) {
            $productId = $validated['product_id'] ?? null;
            
            if (!$productId) {
                return response()->json([
                    'message' => 'Cần cung cấp product_id hoặc product_variant_id'
                ], 422);
            }
            
            $product = Products::with('category')->find($productId);
            
            if (!$product) {
                return response()->json([
                    'message' => 'Sản phẩm không tồn tại'
                ], 422);
            }
            
            // Kiểm tra category để quyết định logic variant
            $categoryName = strtolower($product->category->name ?? '');
            $isAccessoryOrTrousers = str_contains($categoryName, 'phụ kiện') || 
                                   str_contains($categoryName, 'quần') ||
                                   str_contains($categoryName, 'accessories') ||
                                   str_contains($categoryName, 'pants') ||
                                   str_contains($categoryName, 'trousers');
            
            if ($isAccessoryOrTrousers) {
                // Với phụ kiện/quần: chỉ cần màu, không cần size
                $variant = product_variants::where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->whereNotNull('color_id') // có màu
                    ->orderBy('id')
                    ->first();
            } else {
                // Với áo và sản phẩm khác: cần cả size và màu
                $variant = product_variants::where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->whereNotNull('size_id') // có size
                    ->whereNotNull('color_id') // có màu
                    ->orderBy('id')
                    ->first();
            }
            
            if ($variant) {
                $productVariantId = $variant->id;
            } else {
                return response()->json([
                    'message' => 'Sản phẩm này hiện không có sẵn biến thể nào phù hợp'
                ], 422);
            }
        } else {
            $variant = product_variants::find($productVariantId);

        }
        $variant = product_variants::find($productVariantId);
        if (!$variant || $variant->quantity < 1) {
            return response()->json([
                'message' => 'Sản phẩm đã hết hàng rồi :<'
            ], 422);
        }


        $quantity = $validated['quantity'];
        if (Auth::check()) {
            $userId = Auth::id();
            $sessionCart = collect(Session::get('cart', []))
                ->filter(fn($i) => !empty($i['product_variant_id']))
                ->all();
            foreach ($sessionCart as $item) {
                $this->upsertCartItem($userId, $item['product_variant_id'], $item['quantity']);
            }
            Session::forget('cart');
            $this->upsertCartItem($userId, $productVariantId, $quantity);
        } else {
            $cart = Session::get('cart', []);
            $exists = false;
            foreach ($cart as &$item) {
                if ($item['product_variant_id'] == $productVariantId) {
                    $item['quantity'] += $quantity;
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $cart[] = [
                    'product_variant_id' => $productVariantId,
                    'quantity' => $quantity
                ];
            }
            Session::put('cart', $cart);
        }

        return response()->json(['message' => 'Thêm vào giỏ hàng thành công']);
    }

    public function BuyInHome(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'product_id' => 'nullable|integer|exists:products,id',
        ]);

        $productVariantId = $validated['product_variant_id'];

        // Nếu không có product_variant_id, lấy biến thể đầu tiên theo product_id
        if (!$productVariantId) {
            $productId = $validated['product_id'];
            $product = Products::with('category')->find($productId);
            
            if (!$product) {
                return response()->json([
                    'message' => 'Sản phẩm không tồn tại'
                ], 422);
            }
            
            // Kiểm tra category để quyết định logic variant
            $categoryName = strtolower($product->category->name ?? '');
            $isAccessoryOrTrousers = str_contains($categoryName, 'phụ kiện') || 
                                   str_contains($categoryName, 'quần') ||
                                   str_contains($categoryName, 'accessories') ||
                                   str_contains($categoryName, 'pants') ||
                                   str_contains($categoryName, 'trousers');
            
            if ($isAccessoryOrTrousers) {
                // Với phụ kiện/quần: chỉ cần màu, không cần size
                $variant = product_variants::where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->whereNotNull('color_id') // có màu
                    ->orderBy('id')
                    ->first();
            } else {
                // Với áo và sản phẩm khác: cần cả size và màu
                $variant = product_variants::where('product_id', $productId)
                    ->where('quantity', '>', 0) // chỉ lấy biến thể còn hàng
                    ->whereNotNull('size_id') // có size
                    ->whereNotNull('color_id') // có màu
                    ->orderBy('id')
                    ->first();
            }

            if (!$variant) {
                return response()->json([
                    'message' => 'Sản phẩm này hiện không còn biến thể nào còn hàng'
                ], 422);
            }

            $productVariantId = $variant->id;
        } else {
            $variant = product_variants::find($productVariantId);
        }

        $variant = product_variants::find($productVariantId);
        if (!$variant || $variant->quantity < 1) {
            return response()->json([
                'message' => 'Sản phẩm đã hết hàng'
            ], 422);
        }

        $quantity = $validated['quantity'];

        if (Auth::check()) {
            $userId = Auth::id();

            // Đồng bộ giỏ hàng từ session vào DB nếu có
            $sessionCart = collect(Session::get('cart', []))
                ->filter(fn($i) => !empty($i['product_variant_id']))
                ->all();
            foreach ($sessionCart as $item) {
                $this->upsertCartItem($userId, $item['product_variant_id'], $item['quantity']);
            }
            Session::forget('cart');

            $this->upsertCartItem($userId, $productVariantId, $quantity);
        } else {
            // Ghi lại vào session
            $cart = Session::get('cart', []);
            $exists = false;

            foreach ($cart as &$item) {
                if ($item['product_variant_id'] == $productVariantId) {
                    $item['quantity'] += $quantity;
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $cart[] = [
                    'product_variant_id' => $productVariantId,
                    'quantity' => $quantity
                ];
            }

            Session::put('cart', $cart);
            \Log::info('Added to session cart:', [
                'variant_id' => $productVariantId,
                'quantity' => $quantity,
                'session_cart' => $cart
            ]);
        }

        // Lưu dữ liệu thanh toán session
        $this->createDirectCheckoutData($variant, $quantity);

        return response()->json([
            'redirect' => route('payment.show')
        ]);
    }


    private function upsertCartItem(int $userId, ?int $productVariantId, int $quantity): void
    {
        $cartItem = Cart::firstOrNew([
            'user_id' => $userId,
            'product_variant_id' => $productVariantId,
        ]);
        $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;
        $cartItem->save();
    }


    public function getCartCount()
    {
        if (auth()->check()) {
            $cartCount = Cart::where('user_id', auth()->id())->count('product_variant_id');
        } else {
            $cart = session()->get('cart', []);
            $cartCount = count($cart);
        }
        return response()->json(['count' => $cartCount]);
    }
    public function applyVoucher(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Vui lòng đăng nhập để sử dụng Voucher.'], 403);
            }
            return redirect()->route('cart.view')->with('error', 'Vui lòng đăng nhập để sử dụng Voucher.');
        }

        $vouCherCode = $request->input('voucher_code');
        $selectedVariants = $request->input('selected_variants', []); // Mảng variant IDs được chọn
        
        if (empty($vouCherCode)) {
            session()->forget('applied_voucher');
            session()->forget('voucher_selected_variants'); // Xóa thông tin sản phẩm đã chọn cho voucher
            $data = $this->getCartData();
            if ($request->ajax()) {
                return response()->json([
                    'items_html' => view('cart._items', $data)->render(),
                    'summary_html' => view('cart._summary', $data)->render(),
                    'message' => 'Không có mã giảm giá nào được áp dụng.',
                ]);
            }
            return redirect()->route('cart.view')->with('success', 'Không có mã giảm giá nào được áp dụng.');
        }

        $voucher = Voucher::where('code', $vouCherCode)
            ->where('expiration_date', '>=', now())
            ->where('start_date', '<=', now())
            ->where('quantity', '>', 0)
            ->first();

        if (!$voucher) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'], 422);
            }
            return redirect()->route('cart.view')->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        $userId = Auth::id();
        $hasUsed = Order::where('user_id', $userId)
            ->where('voucher_id', $voucher->id)
            ->exists();
        if ($hasUsed) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Bạn đã sử dụng mã giảm giá này rồi.'], 422);
            }
            return redirect()->route('cart.view')->with('error', 'Bạn đã sử dụng mã giảm giá này rồi.');
        }

        session()->put('applied_voucher', $vouCherCode);
        session()->put('applied_voucher_id', $voucher->id);
        
        // Lưu thông tin sản phẩm được chọn cho voucher (nếu có)
        if (!empty($selectedVariants)) {
            session()->put('voucher_selected_variants', $selectedVariants);
        } else {
            session()->forget('voucher_selected_variants');
        }
        
        $data = $this->getCartData();
        if ($request->ajax()) {
            return response()->json([
                'items_html' => view('cart._items', $data)->render(),
                'summary_html' => view('cart._summary', $data)->render(),
                'message' => 'Áp dụng mã giảm giá thành công!',
            ]);
        }
        return redirect()->route('cart.view')->with('success', 'Áp dụng mã giảm giá thành công!');
    }
    public function removeFromCart($variantId)
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->where('product_variant_id', $variantId)->delete();
        } else {
            $cart = Session::get('cart', []);
            $cart = array_filter($cart, function ($item) use ($variantId) {
                return $item['product_variant_id'] != $variantId;
            });
            Session::put('cart', array_values($cart));
        }

        $data = $this->getCartData();
        if (request()->ajax()) {
            return response()->json([
                'items_html' => view('cart._items', $data)->render(),
                'summary_html' => view('cart._summary', $data)->render(),
            ]);
        }
        return redirect()->route('cart.view');
    }

    public function removeMultiple(Request $request)
    {
        $validated = $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'integer|exists:product_variants,id',
        ]);

        $variantIds = $validated['variant_ids'];

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->whereIn('product_variant_id', $variantIds)
                ->delete();
        } else {
            $cart = Session::get('cart', []);
            $cart = array_filter($cart, function ($item) use ($variantIds) {
                return !in_array($item['product_variant_id'], $variantIds);
            });
            Session::put('cart', array_values($cart));
        }

        $data = $this->getCartData();
        if ($request->ajax()) {
            return response()->json([
                'items_html' => view('cart._items', $data)->render(),
                'summary_html' => view('cart._summary', $data)->render(),
                'message' => 'Đã xóa ' . count($variantIds) . ' sản phẩm khỏi giỏ hàng',
            ]);
        }
        return redirect()->route('cart.view')->with('success', 'Đã xóa ' . count($variantIds) . ' sản phẩm khỏi giỏ hàng');
    }

    public function checkoutSelected(Request $request)
    {
        $validated = $request->validate([
            'variant_ids' => 'required|array|min:1',
            'variant_ids.*' => 'integer|exists:product_variants,id',
        ]);

        $variantIds = $validated['variant_ids'];

        // Lấy thông tin các sản phẩm được chọn
        if (Auth::check()) {
            $selectedItems = Cart::where('user_id', Auth::id())
                ->whereIn('product_variant_id', $variantIds)
                ->with([
                    'productVariant.product.thumbnail',
                    'productVariant.product',
                    'productVariant.size',
                    'productVariant.color'
                ])
                ->get();
        } else {
            $sessionCart = session()->get('cart', []);
            $selectedItems = collect($sessionCart)
                ->filter(function ($item) use ($variantIds) {
                    return in_array($item['product_variant_id'], $variantIds);
                })
                ->map(function ($item) {
                    $variant = product_variants::with([
                        'product.thumbnail',
                        'product',
                        'size',
                        'color'
                    ])->find($item['product_variant_id']);
                    
                    if ($variant && $variant->product) {
                        return (object) [
                            'productVariant' => $variant,
                            'quantity' => $item['quantity'],
                        ];
                    }
                    return null;
                })
                ->filter();
        }

        if ($selectedItems->isEmpty()) {
            return response()->json([
                'error' => 'Không có sản phẩm nào được chọn để thanh toán.'
            ], 422);
        }

        // Tạo dữ liệu thanh toán cho các sản phẩm được chọn
        $cartDetails = $selectedItems->map(function ($item) {
            if ($item && $item->productVariant && $item->productVariant->product) {
                return (object) [
                    'productVariant' => $item->productVariant,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->productVariant->product->price * $item->quantity,
                ];
            }
            return null;
        })->filter()->toArray();

        $subtotal = collect($cartDetails)->sum('subtotal');
        
        // Tính voucher discount
        $appliedVoucherCode = session()->get('applied_voucher');
        $voucherDiscount = 0;
        if ($appliedVoucherCode) {
            $voucher = Voucher::where('code', $appliedVoucherCode)
                ->where('expiration_date', '>=', now())
                ->where('start_date', '<=', now())
                ->where('quantity', '>', 0)
                ->first();
            
            if ($voucher) {
                if ($voucher->value_type == 'fixed') {
                    $voucherDiscount = $voucher->discount_amount;
                } elseif ($voucher->value_type == 'percent') {
                    $voucherDiscount = $subtotal * ($voucher->discount_amount / 100);
                }
            }
        }

        $shippingFee = Setting::where('id', 1)->value('ship_price') ?? 40000;
        $total = $subtotal - $voucherDiscount + $shippingFee;

        // Lưu dữ liệu thanh toán vào session
        session()->put('checkout_data', [
            'cartDetails' => $cartDetails,
            'subtotal' => $subtotal,
            'voucherDiscount' => $voucherDiscount,
            'shippingFee' => $shippingFee,
            'total' => $total,
            'selected_checkout' => true
        ]);

        return response()->json([
            'redirect' => route('payment.show')
        ]);
    }
    public function updateQuantity($variantId, Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $quantity = $validated['quantity'];
        $variant = product_variants::find($variantId);
        if (!$variant || $variant->quantity < $quantity) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Số lượng không đủ hoặc sản phẩm không tồn tại.'], 422);
            }
            return redirect()->route('cart.view')->with('error', 'Số lượng không đủ hoặc sản phẩm không tồn tại.');
        }

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())->where('product_variant_id', $variantId)->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        } else {
            $cart = Session::get('cart', []);
            foreach ($cart as &$item) {
                if ($item['product_variant_id'] == $variantId) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            Session::put('cart', $cart);
        }

        $data = $this->getCartData();
        if ($request->ajax()) {
            return response()->json([
                'items_html' => view('cart._items', $data)->render(),
                'summary_html' => view('cart._summary', $data)->render(),
            ]);
        }
        return redirect()->route('cart.view');
    }
    public function updateVariant(Request $request, $variantId)
    {
        try {
            \Log::info('Raw request data:', ['data' => $request->all()]);
            \Log::info('Request headers:', ['headers' => $request->headers->all()]);
            \Log::info('Variant ID from URL:', ['variantId' => $variantId]);
            
            $validated = $request->validate([
                'color_id' => 'required|integer|exists:colors,id',
                'size_id' => 'required|integer|exists:sizes,id',
                'quantity' => 'required|integer|min:1',
            ]);
            
            \Log::info('Validated data:', ['validated' => $validated]);
            
            $oldVariant = product_variants::find($variantId);
            if (!$oldVariant) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Biến thể cũ không tồn tại.'], 422);
                }
                return redirect()->route('cart.view')->with('error', 'Biến thể cũ không tồn tại.');
            }

            $newVariant = product_variants::where('product_id', $oldVariant->product_id)
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->first();

            if (!$newVariant) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Không tìm thấy biến thể với màu sắc và kích thước này.'], 422);
                }
                return redirect()->route('cart.view')->with('error', 'Không tìm thấy biến thể với màu sắc và kích thước này.');
            }

            if ($newVariant->quantity < $validated['quantity']) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Chỉ còn ' . $newVariant->quantity . ' sản phẩm. Vui lòng giảm số lượng.'], 422);
                }
                return redirect()->route('cart.view')->with('error', 'Không đủ số lượng trong kho.');
            }

            if (Auth::check()) {
                // Lấy cart item cũ để giữ nguyên vị trí
                $oldCartItem = Cart::where('user_id', Auth::id())->where('product_variant_id', $variantId)->first();
                
                if ($oldCartItem) {
                    // Kiểm tra xem variant mới đã tồn tại trong cart chưa
                    $existingNewCartItem = Cart::where('user_id', Auth::id())
                        ->where('product_variant_id', $newVariant->id)
                        ->first();
                    
                    if ($existingNewCartItem && $existingNewCartItem->id !== $oldCartItem->id) {
                        // Nếu variant mới đã tồn tại, cộng dồn số lượng và xóa item cũ
                        $existingNewCartItem->quantity += $validated['quantity'];
                        $existingNewCartItem->save();
                        $oldCartItem->delete();
                    } else {
                        // Nếu variant mới chưa tồn tại, chỉ cập nhật variant_id của item cũ
                        $oldCartItem->product_variant_id = $newVariant->id;
                        $oldCartItem->quantity = $validated['quantity'];
                        $oldCartItem->save();
                    }
                }
            } else {
                $cart = Session::get('cart', []);
                $oldItemIndex = null;
                
                // Tìm vị trí của item cũ
                foreach ($cart as $index => $item) {
                    if ($item['product_variant_id'] == $variantId) {
                        $oldItemIndex = $index;
                        break;
                    }
                }
                
                if ($oldItemIndex !== null) {
                    // Kiểm tra xem variant mới đã tồn tại chưa
                    $existingNewItemIndex = null;
                    foreach ($cart as $index => $item) {
                        if ($item['product_variant_id'] == $newVariant->id && $index !== $oldItemIndex) {
                            $existingNewItemIndex = $index;
                            break;
                        }
                    }
                    
                    if ($existingNewItemIndex !== null) {
                        // Nếu variant mới đã tồn tại, cộng dồn số lượng và xóa item cũ
                        $cart[$existingNewItemIndex]['quantity'] += $validated['quantity'];
                        unset($cart[$oldItemIndex]);
                        $cart = array_values($cart); // Reindex array
                    } else {
                        // Nếu variant mới chưa tồn tại, chỉ cập nhật variant_id của item cũ
                        $cart[$oldItemIndex]['product_variant_id'] = $newVariant->id;
                        $cart[$oldItemIndex]['quantity'] = $validated['quantity'];
                    }
                    
                    Session::put('cart', $cart);
                }
            }

            $data = $this->getCartData();
            if ($request->ajax()) {
                return response()->json([
                    'items_html' => view('cart._items', $data)->render(),
                    'summary_html' => view('cart._summary', $data)->render(),
                    'message' => 'Đã cập nhật',
                ]);
            }
            return redirect()->route('cart.view')->with('success', 'Đã cập nhật biến thể sản phẩm');
            
        } catch (\Exception $e) {
            \Log::error('Cart update variant error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
            }
            return redirect()->route('cart.view')->with('error', 'Có lỗi xảy ra khi cập nhật biến thể.');
        }
    }
    public function proceedToCheckout(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);
        $productVariantId = $validated['product_variant_id'] ?? null;
        $quantity = $validated['quantity'] ?? 1;

        if (Auth::check()) {
            $cartItems = Cart::where('user_id', operator: Auth::id())->get();

        } else {
            $cartItems = session()->get('cart', default: []);
        }


        if (empty($cartItems) && $request->has('direct_checkout')) {
            $variant = product_variants::find($request->product_variant_id);

            $cartItems = [
                [
                    'product_variant_id' => $request->product_variant_id,
                    'quantity' => $request->quantity,
                    'product' => $variant->product
                ]
            ];
        }



        //  $cartItems = Cart::where('user_id', Auth::id())->get();
        // $cartItems = session()->get('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('cart.view')->with('error', 'Vui lòng thêm sản phẩm vào giỏ hàng.');
        }


        $cartDetails = collect($cartItems)->map(function ($item) {
            $variant = product_variants::with([
                'product.thumbnail',
                'product',
                'size',
                'color'
            ])->find($item['product_variant_id']);
            if ($variant) {
                return (object) [
                    'productVariant' => $variant,
                    'quantity' => $item['quantity'],
                    'subtotal' => $variant->product->price * $item['quantity'],
                ];
            }
            return null;
        })->filter()->all();

        $subtotal = collect($cartDetails)->sum('subtotal');
        $appliedVoucherCode = session()->get('applied_voucher');
        $voucherDiscount = 0;
        if ($appliedVoucherCode) {
            $voucher = Voucher::where('code', $appliedVoucherCode)
                ->where('expiration_date', '>=', now())
                ->where('start_date', '<=', now())
                ->where('quantity', '>', 0)
                ->first();
            if ($voucher) {
                if ($voucher->value_type == 'fixed') {
                    $voucherDiscount = $voucher->discount_amount;
                } elseif ($voucher->value_type == 'percent') {
                    $voucherDiscount = $subtotal * ($voucher->discount_amount / 100);
                }
            } else {
                session()->forget('applied_voucher');
            }
        }

        $shippingFee = Setting::where('id', 1)->value('ship_price') ?? 40000;

        $total = $subtotal - $voucherDiscount + $shippingFee;

        session()->put('checkout_data', [
            'cartDetails' => $cartDetails,
            'subtotal' => $subtotal,
            'voucherDiscount' => $voucherDiscount,
            'shippingFee' => $shippingFee,
            'total' => $total,
        ]);

        // Chuyển hướng đến trang thanh toán
        return redirect()->route('payment.show');
    }
    public function checkoutDirect(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $variant = product_variants::with([
            'product.thumbnail',
            'product',
            'size',
            'color'
        ])->find($validated['product_variant_id']);

        if (!$variant) {
            return response()->json([
                'error' => 'Biến thể sản phẩm không tồn tại'
            ], 422);
        }

        if ($variant->quantity < $validated['quantity']) {
            return response()->json([
                'error' => 'Số lượng sản phẩm không đủ. Chỉ còn ' . $variant->quantity . ' sản phẩm'
            ], 422);
        }

        if (Auth::check()) {
            $userId = Auth::id();

            Cart::where('user_id', $userId)->delete();

            $cartItem = new Cart();
            $cartItem->user_id = $userId;
            $cartItem->product_variant_id = $validated['product_variant_id'];
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();
        } else {
            session()->forget('cart');
            session()->put('cart', [
                [
                    'product_variant_id' => $validated['product_variant_id'],
                    'quantity' => $validated['quantity']
                ]
            ]);
        }

        $this->createDirectCheckoutData($variant, $validated['quantity']);

        return response()->json([
            'redirect' => route('payment.show')
        ]);
    }

    public function getSummaryForSelected(Request $request)
    {
        $validated = $request->validate([
            'variant_ids' => 'required|array|min:1',
            'variant_ids.*' => 'integer|exists:product_variants,id',
        ]);

        $variantIds = $validated['variant_ids'];

        if (Auth::check()) {
            $selectedItems = Cart::where('user_id', Auth::id())
                ->whereIn('product_variant_id', $variantIds)
                ->with([
                    'productVariant.product.thumbnail',
                    'productVariant.product',
                    'productVariant.size',
                    'productVariant.color'
                ])
                ->get();
        } else {
            $sessionCart = session()->get('cart', []);
            $selectedItems = collect($sessionCart)
                ->filter(function ($item) use ($variantIds) {
                    return in_array($item['product_variant_id'], $variantIds);
                })
                ->map(function ($item) {
                    $variant = product_variants::with([
                        'product.thumbnail',
                        'product',
                        'size',
                        'color'
                    ])->find($item['product_variant_id']);
                    
                    if ($variant && $variant->product) {
                        return (object) [
                            'productVariant' => $variant,
                            'quantity' => $item['quantity'],
                        ];
                    }
                    return null;
                })
                ->filter();
        }

        if ($selectedItems->isEmpty()) {
            return response()->json([
                'error' => 'Không có sản phẩm nào được chọn.'
            ], 422);
        }

        $subtotal = $selectedItems->sum(function ($item) {
            return $item->productVariant->product->price * $item->quantity;
        });

        $appliedVoucherCode = session()->get('applied_voucher');
        $voucherSelectedVariants = session()->get('voucher_selected_variants', []);
        $voucherDiscount = 0;
        
        if ($appliedVoucherCode && Auth::check()) {
            $voucher = Voucher::where('code', $appliedVoucherCode)
                ->where('expiration_date', '>=', now())
                ->where('start_date', '<=', now())
                ->where('quantity', '>', 0)
                ->first();
            
            if ($voucher) {
                if (!empty($voucherSelectedVariants)) {
                    $voucherApplicableVariants = array_intersect($variantIds, $voucherSelectedVariants);
                    if (!empty($voucherApplicableVariants)) {
                        // Tính subtotal chỉ cho những sản phẩm áp dụng voucher
                        $voucherSubtotal = $selectedItems
                            ->filter(function($item) use ($voucherApplicableVariants) {
                                return in_array($item->productVariant->id, $voucherApplicableVariants);
                            })
                            ->sum(function ($item) {
                                return $item->productVariant->product->price * $item->quantity;
                            });
                        
                        if ($voucher->value_type == 'fixed') {
                            $voucherDiscount = $voucher->discount_amount;
                        } elseif ($voucher->value_type == 'percent') {
                            $voucherDiscount = $voucherSubtotal * ($voucher->discount_amount / 100);
                        }
                    }
                } else {
                    // Nếu voucher áp dụng cho toàn bộ, tính theo subtotal
                    if ($voucher->value_type == 'fixed') {
                        $voucherDiscount = $voucher->discount_amount;
                    } elseif ($voucher->value_type == 'percent') {
                        $voucherDiscount = $subtotal * ($voucher->discount_amount / 100);
                    }
                }
            }
        }

        $shippingFee = Setting::where('id', 1)->value('ship_price') ?? 40000;
        $total = $subtotal - $voucherDiscount + $shippingFee;

        // Lấy vouchers có sẵn
        $availableVouchers = null;
        if (Auth::check()) {
            $availableVouchers = Voucher::where('quantity', '>', 0)
                ->where('start_date', '<=', now())
                ->where('expiration_date', '>=', now())
                ->get();
        }

        $data = compact('subtotal', 'shippingFee', 'voucherDiscount', 'total', 'availableVouchers', 'appliedVoucherCode');

        return response()->json([
            'summary_html' => view('cart._summary_selected', $data)->render(),
        ]);
    }

    public function getSummaryForAll()
    {
        $data = $this->getCartData();
        
        return response()->json([
            'summary_html' => view('cart._summary', $data)->render(),
        ]);
    }

    public function clearViewedHistory()
    {
        session()->forget('viewed_products');
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa lịch sử đã xem'
        ]);
    }

    private function createDirectCheckoutData($variant, $quantity)
    {
        $cartDetails = [
            (object) [
                'productVariant' => $variant,
                'quantity' => $quantity,
                'subtotal' => $variant->product->price * $quantity,
            ]
        ];

        $subtotal = $variant->product->price * $quantity;
        $shippingFee = Setting::where('id', 1)->value('ship_price') ?? 40000;
        $voucherDiscount = 0;

        // Áp dụng voucher nếu có
        $appliedVoucherCode = session()->get('applied_voucher');
        if ($appliedVoucherCode) {
            $voucher = Voucher::where('code', $appliedVoucherCode)
                ->where('expiration_date', '>=', now())
                ->where('start_date', '<=', now())
                ->where('quantity', '>', 0)
                ->first();

            if ($voucher) {
                if ($voucher->value_type == 'fixed') {
                    $voucherDiscount = $voucher->discount_amount;
                } elseif ($voucher->value_type == 'percent') {
                    $voucherDiscount = $subtotal * ($voucher->discount_amount / 100);
                }
            }
        }

        $total = $subtotal - $voucherDiscount + $shippingFee;

        // Lưu dữ liệu thanh toán vào session
        session()->put('checkout_data', [
            'cartDetails' => $cartDetails,
            'subtotal' => $subtotal,
            'voucherDiscount' => $voucherDiscount,
            'shippingFee' => $shippingFee,
            'total' => $total,
            'is_direct_checkout' => true // Đánh dấu là thanh toán trực tiếp
        ]);
    }

    private function getCartData()
    {
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())
                ->with('productVariant.product.thumbnail')
                ->orderBy('id', 'asc') // Sử dụng id của cart item để giữ thứ tự
                ->get();
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->product->price * $item->quantity;
            });
            $availableVouchers = Voucher::where('quantity', '>', 0)
                ->where('start_date', '<=', now())
                ->where('expiration_date', '>=', now())
                ->get();
        } else {
            $sessionCart = session()->get('cart', []);
            $cartItems = collect($sessionCart)
                ->map(function ($item) {
                    $variant = product_variants::with([
                        'product.thumbnail',
                        'product',
                        'size',
                        'color'
                    ])->find($item['product_variant_id']);
                    if ($variant) {
                        return (object) [
                            'id' => $item['id'] ?? $item['product_variant_id'], // Thêm id duy nhất
                            'productVariant' => $variant,
                            'quantity' => $item['quantity'],
                        ];
                    }
                    return null;
                })
                ->filter()
                ->values(); // Giữ thứ tự từ session
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->product->price * $item->quantity;
            });
            $availableVouchers = null;
        }

        $appliedVoucherCode = session()->get('applied_voucher');
        $voucherDiscount = 0;
        if ($appliedVoucherCode) {
            $voucher = Voucher::where('code', $appliedVoucherCode)
                ->where('expiration_date', '>=', now())
                ->where('start_date', '<=', now())
                ->where('quantity', '>', 0)
                ->first();
            if ($voucher) {
                if ($voucher->value_type == 'fixed') {
                    $voucherDiscount = $voucher->discount_amount;
                } elseif ($voucher->value_type == 'percent') {
                    $voucherDiscount = $subtotal * ($voucher->discount_amount / 100);
                }
            } else {
                session()->forget('applied_voucher');
            }
        }

        $shippingFee = Setting::where('id', 1)->value('ship_price') ?? 40000;
        $total = $subtotal - $voucherDiscount + $shippingFee;

        // Lấy sản phẩm gợi ý
        $suggestedProducts = $this->getSuggestedProducts($cartItems);
        
        // Lấy lịch sử sản phẩm đã xem từ session
        $viewedProducts = $this->getViewedProducts();

        return compact('cartItems', 'subtotal', 'shippingFee', 'voucherDiscount', 'total', 'availableVouchers', 'appliedVoucherCode', 'suggestedProducts', 'viewedProducts');
    }

    private function getSuggestedProducts($cartItems)
    {
        if ($cartItems->isEmpty()) {
            return \App\Models\Products::with('thumbnail')
                ->active()
                ->featured()
                ->inRandomOrder()
                ->limit(8)
                ->get();
        }

        $cartCategoryIds = $cartItems->pluck('productVariant.product.category_id')->unique();
        
        // Logic gợi ý sản phẩm
        $suggestedCategoryIds = collect();
        
        foreach ($cartCategoryIds as $categoryId) {
            // Lấy thông tin category để xác định loại sản phẩm
            $category = \App\Models\Product_categories::find($categoryId);
            if ($category) {
                $categoryName = strtolower($category->name);
                
                // Logic gợi ý dựa trên tên danh mục
                if (str_contains($categoryName, 'áo') || str_contains($categoryName, 'shirt') || str_contains($categoryName, 'top')) {
                    // Nếu có áo, gợi ý quần và phụ kiện
                    $suggested = \App\Models\Product_categories::where(function($query) {
                        $query->where('name', 'like', '%quần%')
                              ->orWhere('name', 'like', '%pant%')
                              ->orWhere('name', 'like', '%jean%')
                              ->orWhere('name', 'like', '%phụ kiện%')
                              ->orWhere('name', 'like', '%accessory%');
                    })->pluck('id');
                    $suggestedCategoryIds = $suggestedCategoryIds->merge($suggested);
                    
                } elseif (str_contains($categoryName, 'quần') || str_contains($categoryName, 'pant') || str_contains($categoryName, 'jean')) {
                    // Nếu có quần, gợi ý áo và phụ kiện
                    $suggested = \App\Models\Product_categories::where(function($query) {
                        $query->where('name', 'like', '%áo%')
                              ->orWhere('name', 'like', '%shirt%')
                              ->orWhere('name', 'like', '%top%')
                              ->orWhere('name', 'like', '%phụ kiện%')
                              ->orWhere('name', 'like', '%accessory%');
                    })->pluck('id');
                    $suggestedCategoryIds = $suggestedCategoryIds->merge($suggested);
                    
                } elseif (str_contains($categoryName, 'phụ kiện') || str_contains($categoryName, 'accessory')) {
                    // Nếu có phụ kiện, gợi ý áo khoác và áo
                    $suggested = \App\Models\Product_categories::where(function($query) {
                        $query->where('name', 'like', '%áo khoác%')
                              ->orWhere('name', 'like', '%jacket%')
                              ->orWhere('name', 'like', '%coat%')
                              ->orWhere('name', 'like', '%áo%')
                              ->orWhere('name', 'like', '%shirt%');
                    })->pluck('id');
                    $suggestedCategoryIds = $suggestedCategoryIds->merge($suggested);
                }
            }
        }

        // Loại bỏ danh mục đã có trong giỏ hàng
        $suggestedCategoryIds = $suggestedCategoryIds->diff($cartCategoryIds)->unique();

        // Lấy sản phẩm từ các danh mục gợi ý
        if ($suggestedCategoryIds->isNotEmpty()) {
            return \App\Models\Products::with('thumbnail')
                ->active()
                ->whereIn('category_id', $suggestedCategoryIds)
                ->inRandomOrder()
                ->limit(8)
                ->get();
        }

        // Nếu không có gợi ý cụ thể, lấy sản phẩm ngẫu nhiên
        return \App\Models\Products::with('thumbnail')
            ->active()
            ->whereNotIn('category_id', $cartCategoryIds)
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }

    private function getViewedProducts()
    {
        $viewedProductIds = session()->get('viewed_products', []);
        
        if (empty($viewedProductIds)) {
            return collect();
        }

        // Lấy tối đa 6 sản phẩm đã xem gần nhất
        $recentViewedIds = array_slice(array_reverse($viewedProductIds), 0, 6);
        
        return \App\Models\Products::with('thumbnail')
            ->active()
            ->whereIn('id', $recentViewedIds)
            ->get()
            ->sortBy(function($product) use ($recentViewedIds) {
                return array_search($product->id, $recentViewedIds);
            });
    }
}



