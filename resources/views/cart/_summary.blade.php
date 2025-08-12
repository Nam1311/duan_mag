
<div class="gh-cart-summary ghcart2">
    <div class=" ghcart3">
    <h3 class="gh-cart-summary-title">Tóm tắt đơn hàng</h3>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="gh-cart-summary-row">
        <span class="gh-cart-summary-label">Tạm tính</span>
        <span class="gh-cart-summary-value">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
    </div>
    <div class="gh-cart-summary-row">
        <span class="gh-cart-summary-label">Giảm giá</span>
        <span class="gh-cart-summary-value gh-cart-discount-value">-{{ number_format($voucherDiscount, 0, ',', '.') }}đ</span>
    </div>
    <div class="gh-cart-summary-row">
        <span class="gh-cart-summary-label">Phí vận chuyển</span>
        <span class="gh-cart-summary-value">{{ number_format($shippingFee, 0, ',', '.') }}đ</span>
    </div>
    @if (Auth::check())
        @if (!empty($availableVouchers))
            <div class="gh-cart-voucher-section" style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border: 2px solid #e0e0e0; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);">
                <h4 style="margin: 0 0 15px 0; color: #333; font-weight: 600; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-ticket-alt" style="color: #000;"></i>
                    Mã giảm giá
                </h4>
                <form action="{{ route('cart.applyVoucher') }}" method="POST">
                    @csrf
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <select name="voucher_code" class="gh-cart-voucher-select-field" style="width: 100%; padding: 12px 16px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; font-weight: 500; background: #fff; color: #333; transition: all 0.3s ease; cursor: pointer;">
                            <option value="">-- Chọn mã giảm giá --</option>
                            @foreach ($availableVouchers as $voucher)
                                <option value="{{ $voucher->code }}" {{ $appliedVoucherCode == $voucher->code ? 'selected' : '' }}>
                                    {{ $voucher->code }} - 
                                    @if($voucher->value_type == 'percent')
                                        Giảm {{ $voucher->discount_amount }}%
                                    @else 
                                        Giảm {{ number_format($voucher->discount_amount, 0, ',', '.') }}đ
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #000 0%, #333 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                            <i class="fas fa-check"></i> Áp dụng mã
                        </button>
                    </div>
                </form>
                <style>
                    .gh-cart-voucher-select-field:hover {
                        border-color: #000 !important;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
                    }
                    .gh-cart-voucher-select-field:focus {
                        outline: none !important;
                        border-color: #000 !important;
                        box-shadow: 0 0 0 3px rgba(0,0,0,0.1) !important;
                    }
                </style>
            </div>
        @endif
    @else
        <div class="gh-cart-voucher-box" style="margin: 20px 0; padding: 20px; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 16px; border: 2px solid #ffc107; text-align: center;">
            <i class="fas fa-user-lock" style="font-size: 24px; color: #856404; margin-bottom: 10px;"></i>
            <h4 style="margin: 0 0 5px 0; color: #856404; font-weight: 600; font-size: 16px;">Đăng nhập để sử dụng Voucher</h4>
            <p style="margin: 0; color: #856404; font-size: 14px;">
                <a href="{{ route('showlogin') }}" style="color: #856404; text-decoration: underline; font-weight: 600;">Nhấn vào đây để đăng nhập</a>
            </p>
        </div>
    @endif
    <div class="gh-cart-summary-row gh-cart-total-row">
        <span class="gh-cart-summary-label">Tổng cộng</span>
        <span class="gh-cart-summary-value">{{ number_format($total, 0, ',', '.') }}đ</span>
    </div>
    <div style="margin-bottom: 15px; padding: 15px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px; border: 2px solid #2196f3;">
        <p style="margin: 0; color: #1565c0; font-weight: 600; font-size: 14px; text-align: center;">
            <i class="fas fa-info-circle"></i> Sử dụng checkbox bên trái để chọn sản phẩm cụ thể, hoặc thanh toán tất cả bên dưới
        </p>
    </div>
    <a href="{{ route('payment.add') }}"><button class="gh-cart-checkout-btn">
        <i class="fas fa-lock"></i> Thanh toán tất cả sản phẩm
    </button></a>
    <a href="{{ route('home') }}" class="gh-cart-continue-shopping">
        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
    </a>
    </div>
</div>