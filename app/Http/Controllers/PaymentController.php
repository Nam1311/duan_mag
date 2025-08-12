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
        $calculatedVoucherDiscount = 0;
        
        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            if ($voucher && $voucher->quantity > 0) {
                // Tính toán số tiền giảm giá thực tế
                if ($voucher->value_type === 'percent') {
                    $calculatedVoucherDiscount = min(($subtotal * $voucher->discount_amount / 100), $voucher->discount_amount);
                } else { // fixed
                    $calculatedVoucherDiscount = min($subtotal, $voucher->discount_amount);
                }
                $voucher->decrement('quantity');
            } else {
                $voucherId = null;
            }
        }

        $order = new Order();
        $order->user_id = Auth::id() ?? null;
        $order->voucher_id = $voucherId;
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
            $order->address_text = $address->address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province;
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
            $order->address_text = $request->address . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->city;
            $order->address_id = $address->id;
        }
        $order->note = $request->note ?? null;
        $order->save();

        // Lưu thông tin chi tiết vào OrderDetail với phân bổ các giá trị
        $itemCount = count($cartDetails);
        $shippingPerItem = $itemCount > 0 ? $shippingFee / $itemCount : 0;
        $voucherDiscountPerItem = $itemCount > 0 ? $calculatedVoucherDiscount / $itemCount : 0;

        foreach ($cartDetails as $item) {
            $itemSubtotal = $item->productVariant->product->price * $item->quantity;
            $itemTotal = $itemSubtotal + $shippingPerItem;
            $itemFinal = $itemTotal - $voucherDiscountPerItem;

            $orderDetail = new OrderDetail();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_variant_id = $item->productVariant->id;
            $orderDetail->quantity = $item->quantity;
            $orderDetail->ship_price = $shippingPerItem;
            $orderDetail->total = $itemTotal;
            $orderDetail->total_final = $itemFinal;
            $orderDetail->voucher_discount = $voucherDiscountPerItem;
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
                "vnp_OrderInfo" => "Thanh toan don hang " . $vnp_TxnRef,
                "vnp_OrderType" => "other",
                "vnp_ReturnUrl" => $this->vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+30 minutes')),
            ];

            if ($vnp_BankCode) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = "";
            $hashdata = "";
            
            foreach ($inputData as $key => $value) {
                $hashdata .= urlencode($key) . "=" . urlencode($value) . '&';
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            
            $hashdata = rtrim($hashdata, '&');
            $query = rtrim($query, '&');

            $vnp_Url = $this->vnp_Url . "?" . $query;
            if (isset($this->vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
                $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
            }
            
            \Log::info('VNPay Payment URL Created', [
                'order_id' => $order->id,
                'amount' => $vnp_Amount,
                'url' => $vnp_Url
            ]);
            
            return redirect($vnp_Url);
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
            session()->forget(['cart', 'checkout_data', 'applied_voucher', 'applied_voucher_id']);

            // Lấy email để gửi bill
            $email = null;
            if (Auth::check() && $order->user) {
                $email = $order->user->email;
            } elseif ($order->address_id) {
                $guestAddress = addresses::find($order->address_id);
                if ($guestAddress && $guestAddress->email) {
                    $email = $guestAddress->email;
                }
            } elseif ($request->email) {
                $email = $request->email;
            }
            
            if ($email) {
                Mail::to($email)->send(new Bill($order));
            }

            return view('payment.success');
        }
    }

    public function result(Request $request)
    {
        // Lấy tất cả parameters từ request
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $inputData = [];
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        // Xóa vnp_SecureHash khỏi dữ liệu để tính hash
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

        // Log để debug
        \Log::info('VNPay Result Debug', [
            'calculated_hash' => $secureHash,
            'received_hash' => $vnp_SecureHash,
            'response_code' => $request->input('vnp_ResponseCode'),
            'order_id' => $request->input('vnp_TxnRef'),
            'input_data' => $inputData
        ]);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->input('vnp_ResponseCode') == '00') {
                $orderId = $request->input('vnp_TxnRef');
                $order = Order::find($orderId);
                if ($order) {
                    $order->status_payment = 'Đã thanh toán';
                    $order->save();
                    
                    // Cập nhật số lượng sản phẩm
                    foreach ($order->orderDetails as $detail) {
                        $variant = product_variants::find($detail->product_variant_id);
                        if ($variant) {
                            $variant->quantity = max(0, $variant->quantity - $detail->quantity);
                            $variant->save();
                        }
                    }
                    
                    // Xóa giỏ hàng và session
                    if (Auth::check()) {
                        Cart::where('user_id', Auth::id())->delete();
                    }
                    session()->forget(['cart', 'checkout_data', 'applied_voucher', 'applied_voucher_id']);
                    
                    // Xử lý email cho cả user đã đăng nhập và chưa đăng nhập
                    $email = null;
                    if (Auth::check() && $order->user && $order->user->email) {
                        $email = $order->user->email;
                    } elseif ($order->address_id) {
                        $shippingAddress = addresses::find($order->address_id);
                        if ($shippingAddress && $shippingAddress->email) {
                            $email = $shippingAddress->email;
                        }
                    }

                    if ($email) {
                        try {
                            Mail::to($email)->send(new Bill($order));
                        } catch (\Exception $e) {
                            \Log::error('Email send failed: ' . $e->getMessage());
                        }
                    }

                    return view('payment.success');
                } else {
                    \Log::error('Order not found: ' . $orderId);
                    return view('payment.error');
                }
            } else {
                // Xử lý các mã lỗi VNPay
                $errorMessage = $this->getVNPayErrorMessage($request->input('vnp_ResponseCode'));
                \Log::error('VNPay Error: ' . $request->input('vnp_ResponseCode') . ' - ' . $errorMessage);
                return view('payment.fail', ['error' => $errorMessage]);
            }
        } else {
            \Log::error('VNPay Hash verification failed');
            return view('payment.error');
        }
    }

    /**
     * Lấy thông báo lỗi VNPay dựa trên mã phản hồi
     */
    private function getVNPayErrorMessage($responseCode)
    {
        $errorMessages = [
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
        ];

        return $errorMessages[$responseCode] ?? 'Giao dịch thất bại. Vui lòng thử lại sau.';
    }
}