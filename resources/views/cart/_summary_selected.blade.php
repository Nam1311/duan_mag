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
                <form id="apply-voucher-selected-form">
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
                            <i class="fas fa-check"></i> Áp dụng mã cho sản phẩm đã chọn
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
    <div style="margin-bottom: 15px; padding: 15px; background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border-radius: 12px; border: 2px solid #4caf50;">
        <p style="margin: 0; color: #2e7d32; font-weight: 600; font-size: 14px; text-align: center;">
            <i class="fas fa-check-circle"></i> Đã tính toán cho các sản phẩm đã chọn
        </p>
    </div>
    <button id="gh-cart-checkout-selected" class="gh-cart-checkout-btn">
        <i class="fas fa-lock"></i> Thanh toán sản phẩm đã chọn
    </button>
    <a href="{{ route('home') }}" class="gh-cart-continue-shopping">
        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
    </a>
    </div>
</div>

<script>
$(document).ready(function() {
    // Xử lý form áp dụng voucher cho sản phẩm đã chọn
    $(document).on('submit', '#apply-voucher-selected-form', function (e) {
        e.preventDefault();
        var form = $(this);
        var voucherCode = form.find('select[name="voucher_code"]').val();
        
        // Lấy danh sách variant IDs đã chọn
        var selectedVariantIds = $('.gh-cart-item-checkbox:checked').map(function () {
            return $(this).data('variant-id');
        }).get();
        
        if (selectedVariantIds.length === 0) {
            const toast = Toastify({
                text: `
                    <div class="toastify-content">
                        <div class="toast-icon">⚠️</div>
                        <div class="toast-message">Vui lòng chọn sản phẩm trước khi áp dụng voucher.</div>
                    </div>
                `,
                duration: 3000,
                close: false,
                gravity: "top",
                position: "right",
                className: "custom-toast error",
                escapeMarkup: false
            });
            toast.showToast();
            return;
        }
        
        var scrollPosition = $(window).scrollTop();
        $.ajax({
            url: '{{ route("cart.applyVoucher") }}',
            method: 'POST',
            data: {
                voucher_code: voucherCode,
                selected_variants: selectedVariantIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.message) {
                    const toast = Toastify({
                        text: `
                            <div class="toastify-content">
                                <div class="toast-icon">✓</div>
                                <div class="toast-message">${response.message}</div>
                            </div>
                        `,
                        duration: 2000,
                        close: false,
                        gravity: "top",
                        position: "right",
                        className: "custom-toast success",
                        escapeMarkup: false
                    });
                    toast.showToast();
                }
                
                // Cập nhật lại summary cho sản phẩm đã chọn
                updateSummaryForSelected();
                $(window).scrollTop(scrollPosition);
            },
            error: function (xhr) {
                const toast = Toastify({
                    text: `
                        <div class="toastify-content">
                            <div class="toast-icon">❌</div>
                            <div class="toast-message">${xhr.responseJSON?.error || 'Có lỗi xảy ra khi áp dụng voucher.'}</div>
                        </div>
                    `,
                    duration: 3000,
                    close: false,
                    gravity: "top",
                    position: "right",
                    className: "custom-toast error",
                    escapeMarkup: false
                });
                toast.showToast();
            }
        });
    });
});
</script>
