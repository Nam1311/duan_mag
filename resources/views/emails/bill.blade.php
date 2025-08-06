@component('mail::message')
# Cảm ơn bạn đã mua hàng! 🎉

Đơn hàng **#{{ $order->order_code }}** của bạn đã được thanh toán thành công.

@component('mail::panel')
**Ngày đặt:** {{ $order->created_at->format('d/m/Y H:i') }}  
**Tổng thanh toán:** {{ number_format($order->total_price, 0, ',', '.') }}₫  
**Phương thức thanh toán:** {{ $order->payment_method === 'cod' ? 'COD' : 'Chuyển khoản' }}  
**Trạng thái:** Đã xác nhận
@endcomponent

### Chi tiết sản phẩm
<table class="w-full">
    <thead>
        <tr class="bg-gray-100">
            <th class="text-left py-2 px-3">Sản phẩm</th>
            <th class="text-right py-2 px-3">Số lượng</th>
            <th class="text-right py-2 px-3">Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        @foreach($details as $item)
        @php
            $unitPrice = $item->price ?? $item->productVariant->product->price;
            $itemTotal = $unitPrice * $item->quantity;
        @endphp
        <tr>
            <td class="py-2 px-3 border-b">
                <div class="font-medium">{{ $item->productVariant->product->name }}</div>
                <div class="text-gray-600 text-sm">{{ number_format($unitPrice, 0, ',', '.') }}₫</div>
            </td>
            <td class="py-2 px-3 text-right border-b">{{ $item->quantity }}</td>
            <td class="py-2 px-3 text-right border-b">{{ number_format($itemTotal, 0, ',', '.') }}₫</td>
        </tr>
        @endforeach
    </tbody>
</table>

### Tóm tắt thanh toán
<div class="bg-gray-50 rounded-lg p-4 mt-4">
    <div class="flex justify-between py-1">
        <span>Tổng tiền hàng:</span>
        <span>{{ number_format($order->total_price - 40000 + ($order->voucherDiscount ?? 0), 0, ',', '.') }}₫</span>
    </div>
    <div class="flex justify-between py-1">
        <span>Phí vận chuyển:</span>
        <span>40.000₫</span>
    </div>
    <div class="flex justify-between py-1">
        <span>Giảm giá:</span>
        <span class="text-red-600">-{{ number_format($order->voucherDiscount ?? 0, 0, ',', '.') }}₫</span>
    </div>
    <div class="flex justify-between pt-1 mt-2 border-t border-gray-200 font-semibold">
        <span>Tổng thanh toán:</span>
        <span class="text-blue-600">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
    </div>
</div>

@component('mail::button', ['url' => $publicUrl, 'color' => 'success'])
Xem chi tiết đơn hàng & Theo dõi vận chuyển
@endcomponent

Trân trọng,<br>
**M A G**  
*Hệ thống mua sắm trực tuyến*
@endcomponent