
 <style>
    .ghcart2{
     padding: 0px !important;
     box-shadow: none !important;
     height: fit-content;
     position: sticky;
     top: 100px;
    }
     .ghcart3{
     padding: 30px !important;
     box-shadow: none !important;
     height: fit-content;
     position: sticky;
     top: 100px;
    }
 </style>
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
            <form action="{{ route('cart.applyVoucher') }}" method="POST">
                @csrf
                <select name="voucher_code" class="gh-cart-voucher-select-field gh-cart-select">
                    <option value="">-- Chọn mã giảm giá --</option>
                    @foreach ($availableVouchers as $voucher)
                        <option value="{{ $voucher->code }}" {{ $appliedVoucherCode == $voucher->code ? 'selected' : '' }}>
                            {{ $voucher->code }} - 
                            {{ $voucher->value_type == 'percent' ? $voucher->discount_amount . '%' : number_format($voucher->discount_amount, 0, ',', '.') . 'đ' }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    @else
        <div class="gh-cart-voucher-box">
            <h1 class="gh-cart-summary-label">Vui lòng <a href="{{ route('showlogin') }}">đăng nhập</a> để sử dụng Voucher</h1>
        </div>
    @endif
    <div class="gh-cart-summary-row gh-cart-total-row">
        <span class="gh-cart-summary-label">Tổng cộng</span>
        <span class="gh-cart-summary-value">{{ number_format($total, 0, ',', '.') }}đ</span>
    </div>
    <a href="{{ route('payment.add') }}"><button class="gh-cart-checkout-btn">
        <i class="fas fa-lock"></i> Thanh toán an toàn
    </button></a>
    <a href="{{ route('home') }}" class="gh-cart-continue-shopping">
        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
    </a>
    </div>
</div>