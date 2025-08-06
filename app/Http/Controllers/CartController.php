<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\product_variants;
use App\Models\Products;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;

class CartController extends Controller
{
    public function viewCart()
    {
        if (auth()->check()) {
            $cartItems = Cart::where('user_id', auth()->id())->with('productVariant.product.thumbnail')->get();
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
                $variant = product_variants::with('product.thumbnail')
                    ->find($item['product_variant_id']);
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
        $shippingFee = 40000;
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
        $productVariantId = $validated['product_variant_id'];

        if (!$productVariantId) {
            $productId = $validated['product_id'];
            $variant = product_variants::where('product_id', $productId)->first();
            if ($variant) {
                $productVariantId = $variant->id;
            } else {
                return response()->json([
                    'message' => 'Sản phẩm này hiện không có sẵn biến thể nào'
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
        if (empty($vouCherCode)) {
            session()->forget('applied_voucher');
            $data = $this->getCartData();
            if ($request->ajax()) {
                return response()->json([
                    'items_html' => view('cart._items', $data)->render(),
                    'summary_html' => view('cart._summary', $data)->render(),
                    'message' => 'Đã xóa mã giảm giá.',
                ]);
            }
            return redirect()->route('cart.view')->with('success', 'Đã xóa mã giảm giá.');
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
    public function updateVariant($variantId, Request $request)
    {
        $validated = $request->validate([
            'color_id' => 'required|exists:colors,id',
            'size_id' => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:1',
        ]);
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

        if (!$newVariant || $newVariant->quantity < $validated['quantity']) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Vui lòng giảm số lượng'], 422);
            }
            return redirect()->route('cart.view')->with('error', 'Biến thể không tồn tại hoặc hết hàng.');
        }

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->where('product_variant_id', $variantId)->delete();
            $cartItem = Cart::firstOrNew([
                'user_id' => Auth::id(),
                'product_variant_id' => $newVariant->id,
            ]);
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();
        } else {
            $cart = Session::get('cart', []);
            $cart = array_filter($cart, fn($item) => $item['product_variant_id'] != $variantId);
            $cart[] = [
                'product_variant_id' => $newVariant->id,
                'quantity' => $validated['quantity'],
            ];
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
            $variant = product_variants::with('product.thumbnail')->find($item['product_variant_id']);
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

        // Tính phí vận chuyển (giả sử cố định là 40,000 VND)
        $shippingFee = 40000;

        // Tính tổng cộng
        $total = $subtotal - $voucherDiscount + $shippingFee;

        // Bước 3: Lưu thông tin vào session và chuyển hướng
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

        $variant = product_variants::find($validated['product_variant_id']);

        // Kiểm tra tồn kho chặt chẽ hơn
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

        // Xử lý đăng nhập/chưa đăng nhập
        if (Auth::check()) {
            $userId = Auth::id();

            // Xóa toàn bộ giỏ hàng cũ trước khi thêm sản phẩm mới
            Cart::where('user_id', $userId)->delete();

            // Tạo cart item mới
            $cartItem = new Cart();
            $cartItem->user_id = $userId;
            $cartItem->product_variant_id = $validated['product_variant_id'];
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();
        } else {
            // Xóa toàn bộ giỏ hàng trong session
            session()->forget('cart');

            // Tạo giỏ hàng mới chỉ với sản phẩm hiện tại
            session()->put('cart', [
                [
                    'product_variant_id' => $validated['product_variant_id'],
                    'quantity' => $validated['quantity']
                ]
            ]);
        }

        // Tạo dữ liệu thanh toán trực tiếp
        $this->createDirectCheckoutData($variant, $validated['quantity']);

        // Chuyển hướng đến trang thanh toán
        return response()->json([
            'redirect' => route('payment.show')
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
        $shippingFee = 40000;
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
                    $variant = product_variants::with('product.thumbnail')->find($item['product_variant_id']);
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

        $shippingFee = 40000;
        $total = $subtotal - $voucherDiscount + $shippingFee;

        return compact('cartItems', 'subtotal', 'shippingFee', 'voucherDiscount', 'total', 'availableVouchers', 'appliedVoucherCode');
    }
}



