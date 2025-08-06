@extends('app')

@section('body')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/css/cart.css">
    <div class="gh-cart-root">
        <main class="gh-cart-container">
            <div class="gh-cart-layout">
                @include('cart._items')
                @include('cart._summary')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Cập nhật số lượng
            $(document).on('click', '.gh-cart-quantity-btn', function () {
                var button = $(this);
                var variantId = button.data('variant-id');
                var isPlus = button.hasClass('plus');
                var input = button.siblings('.gh-cart-quantity-input');
                var currentQuantity = parseInt(input.val());
                var newQuantity = isPlus ? currentQuantity + 1 : currentQuantity - 1;
                if (newQuantity < 1) return;

                var scrollPosition = $(window).scrollTop();
                $.ajax({
                    url: '/cart/update/' + variantId,
                    method: 'PUT',
                    data: { quantity: newQuantity },
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        $(window).scrollTop(scrollPosition);
                    },
                    error: function (xhr) {
                        const toast = Toastify({
                            text: `
                                        <div class="toastify-content">
                                            <div class="toast-icon">✓</div>
                                            <div class="toast-message">${xhr.responseJSON?.error}</div>
                                            <button class="toast-close">×</button>
                                        </div>
                                    `,
                            duration: 3000,
                            close: false,
                            gravity: "top",
                            position: "right",
                            // stopOnFocus: true,
                            className: "custom-toast success",
                            escapeMarkup: false
                        });

                        toast.showToast();

                        // Đợi DOM render xong mới gán sự kiện
                        setTimeout(() => {
                            const toastElement = document.querySelector('.custom-toast');
                            const closeBtn = toastElement?.querySelector('.toast-close');

                            if (closeBtn) {
                                closeBtn.addEventListener('click', function () {
                                    // Áp dụng hiệu ứng fade-out
                                    toastElement.style.animation = 'fade-out 0.4s forwards';

                                    // Xoá khỏi DOM sau khi animation kết thúc
                                    toastElement.addEventListener('animationend', function () {
                                        toastElement.remove();
                                    });
                                });
                            }
                        }, 10); // Chờ DOM khởi tạo xong
                        // alert(xhr.responseJSON?.error || 'Có lỗi xảy ra khi cập nhật số lượng.');
                    }
                });
            });

            // Thay đổi size/color
            $(document).on('change', '.gh-cart-select', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var variantId = form.data('variant-id');
                var scrollPosition = $(window).scrollTop();
                var $item = form.closest('.gh-cart-item');
                var index = $item.data('index');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        $(window).scrollTop(scrollPosition);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        const toast = Toastify({
                            text: `
                                        <div class="toastify-content">
                                            <div class="toast-icon">✓</div>
                                            <div class="toast-message">${xhr.responseJSON?.error}</div>
                                            <button class="toast-close">×</button>
                                        </div>
                                    `,
                            duration: 3000,
                            close: false,
                            gravity: "top",
                            position: "right",
                            // stopOnFocus: true,
                            className: "custom-toast success",
                            escapeMarkup: false
                        });

                        toast.showToast();

                        // Đợi DOM render xong mới gán sự kiện
                        setTimeout(() => {
                            const toastElement = document.querySelector('.custom-toast');
                            const closeBtn = toastElement?.querySelector('.toast-close');

                            if (closeBtn) {
                                closeBtn.addEventListener('click', function () {
                                    // Áp dụng hiệu ứng fade-out
                                    toastElement.style.animation = 'fade-out 0.4s forwards';

                                    // Xoá khỏi DOM sau khi animation kết thúc
                                    toastElement.addEventListener('animationend', function () {
                                        toastElement.remove();
                                    });
                                });
                            }
                        }, 10); // Chờ DOM khởi tạo xong
                        // alert(xhr.responseJSON?.error || 'Có lỗi xảy ra khi thay đổi biến thể.');
                    }
                });
            });

            // Xóa sản phẩm
            $(document).on('click', '.gh-cart-remove-item', function () {
                var variantId = $(this).data('variant-id');
                var scrollPosition = $(window).scrollTop();
                $.ajax({
                    url: '/cart/remove/' + variantId,
                    method: 'DELETE',
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        $(window).scrollTop(scrollPosition);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('Có lỗi xảy ra khi xóa sản phẩm: ' + (xhr.responseJSON?.error || 'Không xác định'));
                    }
                });
            });

            // Áp dụng voucher
            $(document).on('change', '.gh-cart-voucher-select-field', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var scrollPosition = $(window).scrollTop();
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        if (response.message) {


                            const toast = Toastify({
                                text: `
                            <div class="toastify-content">
                                <div class="toast-icon">✓</div>
                                <div class="toast-message">${response.message}</div>
                                <button class="toast-close">×</button>
                            </div>
                        `,
                                duration: 2000,
                                close: false,
                                gravity: "top",
                                position: "right",
                                // stopOnFocus: true,
                                className: "custom-toast success",
                                escapeMarkup: false
                            });

                            toast.showToast();

                            // Đợi DOM render xong mới gán sự kiện
                            setTimeout(() => {
                                const toastElement = document.querySelector('.custom-toast');
                                const closeBtn = toastElement?.querySelector('.toast-close');

                                if (closeBtn) {
                                    closeBtn.addEventListener('click', function () {
                                        // Áp dụng hiệu ứng fade-out
                                        toastElement.style.animation = 'fade-out 0.4s forwards';

                                        // Xoá khỏi DOM sau khi animation kết thúc
                                        toastElement.addEventListener('animationend', function () {
                                            toastElement.remove();
                                        });
                                    });
                                }
                            }, 10);
                        }



                        $(window).scrollTop(scrollPosition);
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert(xhr.responseJSON?.error || 'Có lỗi xảy ra khi áp dụng voucher.');
                    }
                });
            });
        });
    </script>
@endsection