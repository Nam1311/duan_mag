<?php

namespace App\Http\Controllers;

use App\Mail\Bill;
use App\Models\addresses;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\product_variants;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Log;

class PaymentController extends Controller
{
    protected $vnp_TmnCode;
    protected $vnp_HashSecret;
    protected $vnp_Url;
    protected $vnp_Returnurl;
    protected $vnp_apiUrl;
    protected $apiUrl;
    public function __construct()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->vnp_TmnCode = "AJT0AAYH"; // Mã định danh merchant
        $this->vnp_HashSecret = "50394OTCASPHF09AVM4EDBEQINVFJCDO"; // Secret key
        $this->vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $this->vnp_Returnurl = "http://127.0.0.1:8080/payment/result";
        $this->vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
        $this->apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
    }

    public function showPayment()
    {
        $checkoutData = session()->get('checkout_data');
        if (empty($checkoutData) || empty($checkoutData['cartDetails'])) {
            return redirect()->route('cart.view')->with('error', 'Giỏ hàng rỗng. Vui lòng thêm sản phẩm để thanh toán.');
        }
        $user = Auth::user();

        if (Auth::check()) {
            $address = addresses::where('user_id', $user->id)->get();
        } else {
            $address = null;
        }

        return view('payment', [
            'cartDetails' => $checkoutData['cartDetails'] ?? [],
            'subtotal' => $checkoutData['subtotal'] ?? 0,
            'voucherDiscount' => $checkoutData['voucherDiscount'] ?? 0,
            'shippingFee' => $checkoutData['shippingFee'] ?? 0,
            'total' => $checkoutData['total'] ?? 0,
            'address' => $address,
        ]);
    }

    public function paymentStore(Request $request)
    {
        $rules = [
            'payment' => 'required',
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $rules['address'] = 'required'; // ID của địa chỉ từ dropdown
            if (!$user->phone) {
                $rules['phone'] = 'required|regex:/^0[0-9]{9,10}$/';
            }
        } else {
            $rules = array_merge($rules, [
                'fullname' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
                'city' => 'required',
                'district' => 'required',
                'ward' => 'required',
            ]);
        }
        $request->validate($rules);

        $checkoutData = session()->get('checkout_data');
        if (empty($checkoutData) || empty($checkoutData['cartDetails'])) {
            return redirect()->route('cart.view')->with('error', 'Giỏ hàng rỗng. Vui lòng thêm sản phẩm để thanh toán.');
        }
        $cartDetails = $checkoutData['cartDetails'];
        $subtotal = $checkoutData['subtotal'];
        $voucherDiscount = $checkoutData['voucherDiscount'];
        $shippingFee = $checkoutData['shippingFee'];
        $total = $checkoutData['total'];
        $voucherCode = session()->get('applied_voucher');
        $voucherId = session()->get('applied_voucher_id');
        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            if ($voucher && $voucher->quantity > 0) {
                $voucher->decrement('quantity');
            } else {
                $voucherId = null;
            }
        }

        $order = new Order();
        $order->user_id = Auth::id() ?? null;
        $order->voucher_id = $voucherId;
        $order->total_price = $total;
        $order->total = $subtotal; // Tổng tiền trước khi áp dụng voucher và phí ship
        $order->total_final = $total; // Tổng tiền cuối cùng sau khi áp dụng voucher và phí ship
        $order->ship_price = $shippingFee; // Phí vận chuyển
        $order->status_payment = 'Chờ xử lý';
        $order->payment_methods = $request->payment;
        $order->status = 'Chờ xác nhận';
        $order->order_code = "MAG" . implode('', array_map(fn() => rand(0, 9), range(1, 5)));

        if (Auth::check()) {
            $user = Auth::user();
            $order->name = $user->name; // Tên người đặt hàng
            if (!$user->phone && $request->filled('phone')) {
                $user->phone = $request->phone;
                $user->save();
            }
            $order->phone = $user->phone ?? $request->phone;
            $order->address_id = $request->address;
            $address = addresses::find($order->address_id);
            if (!$address) {
                return back()->withErrors(['address' => 'Địa chỉ không tồn tại.']);
            }
            $order->address = $address->address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province;
        } else {
            $address = new addresses();
            $address->user_id = null;
            $address->receiver_name = $request->fullname;
            $address->phone = $request->phone;
            $address->email = $request->email;
            $address->province = $request->city;
            $address->district = $request->district;
            $address->ward = $request->ward;
            $address->address = $request->address;
            $address->save();
            
            $order->name = $request->fullname; // Tên người đặt hàng khi chưa đăng nhập
            $order->phone = $request->phone;
            $order->address = $request->address . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city;
            $order->address_id = $address->id;
        }
        $order->note = $request->note ?? null;
        $order->save();

        foreach ($cartDetails as $item) {
            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_variant_id = $item->productVariant->id;
            $orderDetail->unit_price = $item->productVariant->product->price;
            $orderDetail->quantity = $item->quantity;
            $orderDetail->save();
        }

        if ($request->payment == 'Banking') {
            $vnp_TxnRef = $order->id;
            $vnp_Amount = $total * 100;
            $vnp_Locale = 'vn';
            $vnp_BankCode = $request->bankCode ?? '';
            $vnp_IpAddr = $request->ip();

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $this->vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => "Thanh toan GD: " . $vnp_TxnRef,
                "vnp_OrderType" => "other",
                "vnp_ReturnUrl" => $this->vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')),
            ];

            if ($vnp_BankCode) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $this->vnp_Url . "?" . $query;
            if (isset($this->vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            header('Location: ' . $vnp_Url);
            die();
        } elseif ($request->payment == 'Momo') {
            // Xử lý Momo nếu cần
        } else { // COD
            $order->status_payment = 'Chờ xử lý'; // Trạng thái phù hợp cho COD
            $order->save();

            foreach ($cartDetails as $item) {
                $variant = product_variants::find($item->productVariant->id);
                if ($variant) {
                    $variant->quantity = max(0, $variant->quantity - $item->quantity);
                    $variant->save();
                }
            }

            if (Auth::check()) {
                Cart::where('user_id', Auth::id())->delete();
            }
            session()->forget(['cart', 'checkout_data', 'applied_voucher']);

            $email = Auth::check() && $order->user ? $order->user->email : $request->email;
            if ($email) {
                Mail::to($email)->send(new Bill($order));
            }

            return view('payment.success');
        }
    }

    public function result(Request $request)
    {
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = [];
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = '';
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                $orderId = $request->vnp_TxnRef;
                $order = Order::find($orderId);
                if ($order) {
                    $order->status_payment = 'Đã thanh toán';
                    $order->save();
                    foreach ($order->orderDetails as $detail) {
                        $variant = product_variants::find($detail->product_variant_id);
                        if ($variant) {
                            $variant->quantity = max(0, $variant->quantity - $detail->quantity);
                            $variant->save();
                        }
                    }
                    if (Auth::check()) {
                        Cart::where('user_id', Auth::id())->delete();
                    }
                    session()->forget(['cart', 'checkout_data', 'applied_voucher']);
                    $email = null;
                    if (Auth::check() && $order->user && $order->user->email) {
                        $email = $order->user->email;
                    } elseif ($order->shippingAddress  && $order->shippingAddress->email) {
                        $email = $order->shippingAddress->email;
                    } elseif ($request->has('email')) {
                        $email = $request->input('email');
                    }

                    if ($email) {
                        Mail::to($email)->send(new Bill($order));
                    }

                    return view('payment.success');
                } else {
                    return view('payment.error');
                }
            } else {
                return view('payment.fail');
            }
        } else {
            return view('payment.error');
        }
    }
}