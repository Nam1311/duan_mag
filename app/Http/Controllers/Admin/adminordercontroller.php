<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AdminOrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng với tìm kiếm và lọc.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        // Load kèm user và address
        $query = Order::with(['user', 'address'])->latest();

        // Tìm kiếm
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('orders.name', 'like', "%{$search}%")
                  ->orWhere('orders.phone', 'like', "%{$search}%")
                  ->orWhereHas('address', function ($q) use ($search) {
                      $q->where('receiver_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        $orders = $query->get();

        foreach ($orders as $order) {
            $order->address_details = $this->fetchAddressDetails($order);
        }

        return view('admin.orders', compact('orders'));
    }

    /**
     * Xóa mềm đơn hàng.
     */
    public function softDelete($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->status !== 'Đã hủy') {
                return redirect()->route('admin.orders.index')->with('error', 'Chỉ có thể xóa đơn hàng có trạng thái "Đã hủy"!');
            }
            $order->delete();
            return redirect()->route('admin.orders.index')->with('success', 'Xóa mềm đơn hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng hoặc lỗi khi xóa!');
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Chờ xác nhận,Đã xác nhận,Đang giao hàng,Thành công,Đã hủy,Hoàn hàng',
        ]);

        try {
            $order = Order::findOrFail($id);
            if (in_array($order->status, ['Thành công', 'Đang giao hàng']) && $request->status === 'Đã hủy') {
                return redirect()->route('admin.orders.index')->with('error', 'Không thể hủy đơn hàng đang giao hoặc đã thành công!');
            }

            $order->status = $request->status;
            $order->save();

            return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Lỗi khi cập nhật trạng thái đơn hàng!');
        }
    }

    /**
     * Hiển thị chi tiết đơn hàng.
     */
    public function show($id)
    {
        try {
            $order = Order::with([
                'user',
                'address',
                'orderDetails.productVariant.product.images',
                'orderDetails.productVariant.size',
                'orderDetails.productVariant.color'
            ])->findOrFail($id);

            // Lấy cột orders.address_text (string)
            $orderAddressString = $order->getAttribute('address_text') ?? 'Không xác định';

            // Lấy quan hệ addresses
            $addressRelation = $order->getRelation('address') ?? ($order->address_id ? $order->load('address')->getRelation('address') : null);

            // Gán address_details
            $order->address_details = $this->fetchAddressDetails($order);

            return view('admin.orders_show', [
                'order' => $order,
                'orderAddressString' => $orderAddressString,
                'addressRelation' => $addressRelation
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }

    /**
     * Hiển thị giao diện in cho đơn hàng.
     */
    public function printOrder($id)
    {
        try {
            $order = Order::with([
                'user',
                'address',
                'orderDetails.productVariant.product.images',
                'orderDetails.productVariant.size',
                'orderDetails.productVariant.color'
            ])->findOrFail($id);

            $orderAddressString = $order->getAttribute('address_text') ?? 'Không xác định';
            $addressRelation = $order->getRelation('address') ?? ($order->address_id ? $order->load('address')->getRelation('address') : null);
            $order->address_details = $this->fetchAddressDetails($order);

            return view('admin.order_print', [
                'order' => $order,
                'orderAddressString' => $orderAddressString,
                'addressRelation' => $addressRelation
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }

    /**
     * Lấy thông tin chi tiết địa chỉ, ưu tiên từ orders.name, sau đó address.receiver_name, rồi user.name.
     */
    protected function fetchAddressDetails($order)
    {
        // Khởi tạo giá trị mặc định
        $receiver_name = 'Không xác định';
        $phone = 'Không xác định';
        $address = $order->address_text ?? 'Không xác định';

        // Lấy quan hệ address
        $addressRelation = $order->getRelation('address') ?? ($order->address_id ? $order->load('address')->getRelation('address') : null);

        // Lấy tên khách hàng
        if (!empty($order->name)) {
            $receiver_name = $order->name;
        } elseif ($addressRelation && !empty($addressRelation->receiver_name)) {
            $receiver_name = $addressRelation->receiver_name;
        } elseif ($order->user && !empty($order->user->name)) {
            $receiver_name = $order->user->name;
        }

        // Lấy số điện thoại
        if ($addressRelation && !empty($addressRelation->phone)) {
            $phone = $addressRelation->phone;
        } elseif (!empty($order->phone)) {
            $phone = $order->phone;
        } elseif ($order->user && !empty($order->user->phone)) {
            $phone = $order->user->phone;
        }

        // Nếu thiếu province, district, hoặc ward, trả về giá trị mặc định
        if (!$addressRelation || empty($addressRelation->province) || empty($addressRelation->district) || empty($addressRelation->ward)) {
            return [
                'province_name' => 'Không xác định',
                'district_name' => 'Không xác định',
                'ward_name' => 'Không xác định',
                'address' => $address,
                'phone' => $phone,
                'receiver_name' => $receiver_name,
            ];
        }

        $baseUrl = 'https://provinces.open-api.vn/api/';

        $provinceName = Cache::remember("province_" . $addressRelation->province, 3600, function () use ($baseUrl, $addressRelation) {
            $response = Http::get("{$baseUrl}p/{$addressRelation->province}?depth=1");
            return $response->successful() ? $response->json()['name'] : 'Không xác định';
        });

        $districtName = Cache::remember("district_" . $addressRelation->district, 3600, function () use ($baseUrl, $addressRelation) {
            $response = Http::get("{$baseUrl}d/{$addressRelation->district}?depth=1");
            return $response->successful() ? $response->json()['name'] : 'Không xác định';
        });

        $wardName = Cache::remember("ward_" . $addressRelation->ward, 3600, function () use ($baseUrl, $addressRelation) {
            $response = Http::get("{$baseUrl}w/{$addressRelation->ward}?depth=1");
            return $response->successful() ? $response->json()['name'] : 'Không xác định';
        });

        return [
            'province_name' => $provinceName,
            'district_name' => $districtName,
            'ward_name' => $wardName,
            'address' => $address,
            'phone' => $phone,
            'receiver_name' => $receiver_name,
        ];
    }
}