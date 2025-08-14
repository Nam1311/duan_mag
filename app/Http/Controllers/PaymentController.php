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
use Exception;
use Log;

class PaymentController extends Controller
{
    protected $vnp_TmnCode;
    protected $vnp_HashSecret;
    protected $vnp_Url;
    protected $vnp_Returnurl;
    protected $vnp_apiUrl;
    protected $apiUrl;
    
    // ZaloPay Configuration
    protected $zp_app_id;
    protected $zp_key1;
    protected $zp_key2;
    protected $zp_endpoint;
    protected $zp_callback_url;
    protected $zp_redirect_url;
    
    public function __construct()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        
        // VNPay Config
        $this->vnp_TmnCode = "AJT0AAYH"; // Mã định danh merchant
        $this->vnp_HashSecret = "50394OTCASPHF09AVM4EDBEQINVFJCDO"; // Secret key
        $this->vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $this->vnp_Returnurl = "http://127.0.0.1:8080/payment/result";
        $this->vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
        $this->apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
        
        // ZaloPay Config
        $this->zp_app_id = config('zalopay.app_id');
        $this->zp_key1 = config('zalopay.key1');
        $this->zp_key2 = config('zalopay.key2');
        $environment = config('zalopay.environment');
        $this->zp_endpoint = config("zalopay.{$environment}.create_order");
        $this->zp_callback_url = config('zalopay.callback_url');
        $this->zp_redirect_url = config('zalopay.redirect_url');
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
        } elseif ($request->payment == 'ZaloPay') {
            // Xử lý ZaloPay
            return $this->processZaloPayPayment($order, $total);
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

    /**
     * Xử lý thanh toán ZaloPay
     */
    private function processZaloPayPayment($order, $total)
    {
        try {
            \Log::info('Starting ZaloPay Payment Process', [
                'order_id' => $order->id,
                'total' => $total,
                'app_id' => $this->zp_app_id,
                'endpoint' => $this->zp_endpoint
            ]);

            $transID = rand(0, 1000000);
            $embeddata = "{}"; // Empty JSON object as string
            $items = "[]"; // Empty array as string
            
            // Tạo app_trans_id theo format chuẩn ZaloPay
            $app_trans_id = date("ymd") . "_" . $transID;
            
            // Xử lý tên người dùng - chỉ dùng ký tự an toàn
            $username = $order->name ? preg_replace('/[^a-zA-Z0-9_]/', '', $order->name) : "user123";
            if (empty($username) || strlen($username) < 3) {
                $username = "user" . $order->id;
            }

            $order_data = [
                "app_id" => $this->zp_app_id,
                "app_trans_id" => $app_trans_id,
                "app_user" => $username,
                "app_time" => round(microtime(true) * 1000),
                "item" => $items,
                "embed_data" => $embeddata,
                "amount" => (int)$total,
                "description" => "Thanh toan don hang #" . $order->order_code,
                "bank_code" => "",
                "callback_url" => $this->zp_callback_url
            ];

            // Tạo MAC theo đúng format ZaloPay: app_id|app_trans_id|app_user|amount|app_time|embed_data|item
            $data = $order_data["app_id"] . "|" . $order_data["app_trans_id"] . "|" . $order_data["app_user"] . "|" . $order_data["amount"] . "|" . $order_data["app_time"] . "|" . $order_data["embed_data"] . "|" . $order_data["item"];
            $order_data["mac"] = hash_hmac("sha256", $data, $this->zp_key1);

            \Log::info('ZaloPay Order Data Created', [
                'order_data' => $order_data,
                'mac_string' => $data,
                'key1' => substr($this->zp_key1, 0, 8) . '...' // Log một phần key để debug
            ]);

            // Lưu app_trans_id vào order để tracking
            $order->app_trans_id = $order_data["app_trans_id"];
            $order->save();

            // Gọi API ZaloPay với cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->zp_endpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($order_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new \Exception("cURL Error: " . $error);
            }

            $result = json_decode($resp, true);

            \Log::info('ZaloPay Create Order Response', [
                'order_id' => $order->id,
                'response' => $result,
                'http_code' => $httpCode,
                'raw_response' => $resp
            ]);

            if ($result && isset($result['return_code'])) {
                if ($result['return_code'] == 1 && isset($result['order_url'])) {
                    \Log::info('ZaloPay redirect to: ' . $result['order_url']);
                    return redirect($result['order_url']);
                } else {
                    // Map error codes
                    $errorMessages = [
                        -1 => 'Tham số không hợp lệ',
                        -2 => 'Merchant không được phép sử dụng',
                        -3 => 'Chữ ký MAC không đúng',
                        -4 => 'Lỗi tham số hoặc chữ ký',
                        -5 => 'Merchant không tồn tại',
                        -6 => 'Đơn hàng đã tồn tại'
                    ];
                    
                    $errorMsg = $errorMessages[$result['return_code']] ?? ($result['return_message'] ?? 'Lỗi không xác định');
                    
                    \Log::error('ZaloPay API Error', [
                        'response' => $result,
                        'order_id' => $order->id,
                        'error_message' => $errorMsg
                    ]);
                    
                    return back()->with('error', 'Lỗi ZaloPay: ' . $errorMsg . ' (Code: ' . $result['return_code'] . ')');
                }
            } else {
                throw new \Exception("Invalid response from ZaloPay API: " . $resp);
            }
        } catch (\Exception $e) {
            \Log::error('ZaloPay Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id
            ]);
            return back()->with('error', 'Có lỗi hệ thống khi xử lý thanh toán ZaloPay: ' . $e->getMessage());
        }
    }

    /**
     * Xử lý callback từ ZaloPay
     */
    public function zaloPayCallback(Request $request)
    {
        $result = [];
        try {
            $key2 = $this->zp_key2;
            $postdata = $request->getContent();
            $postdatajson = json_decode($postdata, true);
            
            $mac = hash_hmac("sha256", $postdatajson["data"], $key2);
            $requestmac = $postdatajson["mac"];

            // Kiểm tra callback hợp lệ (đến từ ZaloPay server)
            if (strcmp($mac, $requestmac) != 0) {
                // callback không hợp lệ
                $result["returncode"] = -1;
                $result["returnmessage"] = "mac not equal";
            } else {
                // Thanh toán thành công
                $dataJson = json_decode($postdatajson["data"], true);
                $app_trans_id = $dataJson["apptransid"];
                
                // Tìm order theo app_trans_id
                $order = Order::where('app_trans_id', $app_trans_id)->first();
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

                    \Log::info("ZaloPay payment updated for order: " . $order->id);
                }

                $result["returncode"] = 1;
                $result["returnmessage"] = "success";
            }
        } catch (Exception $e) {
            $result["returncode"] = 0; // ZaloPay server sẽ callback lại (tối đa 3 lần)
            $result["returnmessage"] = $e->getMessage();
        }

        return response()->json($result);
    }

    /**
     * Xử lý kết quả thanh toán ZaloPay (redirect từ ZaloPay)
     */
    public function zaloPayResult(Request $request)
    {
        $app_trans_id = $request->input('apptransid');
        $status = $request->input('status');

        if ($app_trans_id) {
            $order = Order::where('app_trans_id', $app_trans_id)->first();
            
            if ($order) {
                if ($status == 1) {
                    // Thanh toán thành công
                    $order->status_payment = 'Đã thanh toán';
                    $order->save();

                    // Xóa giỏ hàng và session
                    if (Auth::check()) {
                        Cart::where('user_id', Auth::id())->delete();
                    }
                    session()->forget(['cart', 'checkout_data', 'applied_voucher', 'applied_voucher_id']);

                    // Gửi email
                    $this->sendOrderEmail($order);

                    return view('payment.success');
                } else {
                    // Thanh toán thất bại
                    return view('payment.fail', ['error' => 'Thanh toán ZaloPay không thành công.']);
                }
            }
        }

        return view('payment.error');
    }

    /**
     * Kiểm tra trạng thái thanh toán ZaloPay
     */
    public function checkZaloPayStatus($app_trans_id)
    {
        $data = $this->zp_app_id . "|" . $app_trans_id . "|" . $this->zp_key1;
        $params = [
            "appid" => $this->zp_app_id,
            "apptransid" => $app_trans_id,
            "mac" => hash_hmac("sha256", $data, $this->zp_key1)
        ];

        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($params)
            ]
        ]);

        $resp = file_get_contents("https://sandbox.zalopay.com.vn/v001/tpe/getstatusbyapptransid", false, $context);
        return json_decode($resp, true);
    }

    /**
     * Gửi email hóa đơn
     */
    private function sendOrderEmail($order)
    {
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
    }
}