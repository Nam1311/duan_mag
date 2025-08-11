@extends('app')

@section('body')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/css/cart.css">
    <style>
        .gh-cart-bulk-actions {
            border: 1px solid #e0e0e0;
            box-sizing: border-box;
            width: 100%;
        }
        
        .gh-cart-bulk-actions label {
            color: #333;
            font-size: 14px;
            white-space: nowrap;
        }
        
        #gh-cart-remove-selected {
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        
        #gh-cart-remove-selected:hover {
            background: #c82333 !important;
            transform: translateY(-1px);
        }
        
        .gh-cart-item-checkbox-wrapper input[type="checkbox"] {
            cursor: pointer;
        }
        
        .gh-cart-item-checkbox-wrapper input[type="checkbox"]:checked {
            accent-color: #007bff;
        }

        /* Đảm bảo layout không bị vỡ */
        .gh-cart-layout {
            max-width: 100%;
            overflow-x: hidden;
        }

        .gh-cart-container {
            max-width: 100%;
            overflow-x: hidden;
        }

        .gh-cart-root {
            max-width: 100vw;
            overflow-x: hidden;
        }

        /* Đảm bảo tất cả phần tử con không vượt quá container */
        .gh-cart-item * {
            max-width: 100%;
            box-sizing: border-box;
        }

        .gh-cart-bulk-actions {
            min-width: 0;
            flex-wrap: wrap;
        }

        /* Đảm bảo layout 2 cột rõ ràng */
        .gh-cart-layout {
            display: grid !important;
            grid-template-columns: 2fr 1fr !important;
            gap: 30px !important;
            align-items: start !important;
            min-height: 500px;
        }

        .gh-cart-items-section {
            min-height: 400px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0; /* Temporary border để thấy rõ */
        }

        /* Debug: Tạm thời border cho summary */
        .gh-cart-summary {
            border: 1px solid #007bff !important; /* Temporary border để thấy rõ */
        }

        /* Loading và highlight states */
        .gh-cart-item.updating {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .gh-cart-item.updating::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .gh-cart-item.just-updated {
            background-color: #e8f5e8 !important;
            border: 2px solid #28a745 !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* CSS cho error toast */
        .custom-toast.error {
            background-color: #dc3545 !important;
        }

        .custom-toast.error .toast-icon {
            color: white;
        }
    </style>
    <div class="gh-cart-root">
        <main class="gh-cart-container">
            <div class="gh-cart-layout">
                <div class="gh-cart-items-section">
                    <div class="gh-cart-bulk-actions" style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 8px; display: flex; align-items: center; gap: 15px; max-width: 100%; box-sizing: border-box;">
                        <label style="display: flex; align-items: center; gap: 8px; margin: 0; cursor: pointer; font-weight: 500; white-space: nowrap;">
                            <input type="checkbox" id="gh-cart-select-all" style="margin: 0;" />
                            Chọn tất cả
                        </label>
                        <button id="gh-cart-remove-selected" style="padding: 8px 16px; background: #dc3545; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: background-color 0.2s; white-space: nowrap; flex-shrink: 0;">
                            <i class="fas fa-trash-alt"></i> Xóa đã chọn
                        </button>
                    </div>
                    @include('cart._items')
                </div>
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

            // Chọn tất cả checkbox
            $(document).on('change', '#gh-cart-select-all', function () {
                var checked = $(this).is(':checked');
                $('.gh-cart-item-checkbox').prop('checked', checked);
            });

            // Nếu bỏ chọn 1 item thì bỏ chọn "chọn tất cả"
            $(document).on('change', '.gh-cart-item-checkbox', function () {
                if (!$(this).is(':checked')) {
                    $('#gh-cart-select-all').prop('checked', false);
                } else if ($('.gh-cart-item-checkbox:checked').length === $('.gh-cart-item-checkbox').length) {
                    $('#gh-cart-select-all').prop('checked', true);
                }
            });

            // Ẩn/hiện bulk actions dựa trên số lượng sản phẩm
            function toggleBulkActions() {
                var hasItems = $('.gh-cart-item-checkbox').length > 0;
                $('.gh-cart-bulk-actions').toggle(hasItems);
            }

            // Gọi khi trang load
            toggleBulkActions();

            // Gọi lại sau mỗi lần update cart
            $(document).ajaxComplete(function() {
                setTimeout(toggleBulkActions, 100);
            });

            // Xóa các sản phẩm đã chọn
            $(document).on('click', '#gh-cart-remove-selected', function () {
                var selected = $('.gh-cart-item-checkbox:checked').map(function () {
                    return $(this).data('variant-id');
                }).get();
                
                if (selected.length === 0) {
                    alert('Vui lòng chọn sản phẩm để xóa.');
                    return;
                }
                
                if (!confirm('Bạn có chắc muốn xóa ' + selected.length + ' sản phẩm đã chọn?')) return;
                
                var scrollPosition = $(window).scrollTop();
                $.ajax({
                    url: '/cart/remove-multiple',
                    method: 'DELETE',
                    data: { variant_ids: selected },
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        $(window).scrollTop(scrollPosition);
                        $('#gh-cart-select-all').prop('checked', false);
                        
                        // Cập nhật số lượng trên header
                        updateCartCount();
                        
                        const toast = Toastify({
                            text: `
                                <div class="toastify-content">
                                    <div class="toast-icon">✓</div>
                                    <div class="toast-message">Đã xóa ${selected.length} sản phẩm khỏi giỏ hàng</div>
                                    <button class="toast-close">×</button>
                                </div>
                            `,
                            duration: 3000,
                            close: false,
                            gravity: "top",
                            position: "right",
                            className: "custom-toast success",
                            escapeMarkup: false
                        });
                        toast.showToast();
                        
                        setTimeout(() => {
                            const toastElement = document.querySelector('.custom-toast');
                            const closeBtn = toastElement?.querySelector('.toast-close');
                            if (closeBtn) {
                                closeBtn.addEventListener('click', function () {
                                    toastElement.style.animation = 'fade-out 0.4s forwards';
                                    toastElement.addEventListener('animationend', function () {
                                        toastElement.remove();
                                    });
                                });
                            }
                        }, 10);
                    },
                    error: function (xhr) {
                        alert('Có lỗi xảy ra khi xóa sản phẩm: ' + (xhr.responseJSON?.error || 'Không xác định'));
                    }
                });
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
                        
                        // Cập nhật số lượng trên header
                        updateCartCount();
                    },
                    error: function (xhr) {
                        const toast = Toastify({
                            text: `
                                        <div class="toastify-content">
                                            <div class="toast-icon">✗</div>
                                            <div class="toast-message">${xhr.responseJSON?.error || 'Có lỗi xảy ra khi cập nhật số lượng'}</div>
                                            <button class="toast-close">×</button>
                                        </div>
                                    `,
                            duration: 3000,
                            close: false,
                            gravity: "top",
                            position: "right",
                            // stopOnFocus: true,
                            className: "custom-toast error",
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
                var cartId = $item.data('cart-id'); // Lấy cart ID để định vị lại
                
                // Debug form data
                console.log('Form:', form);
                console.log('Form serialized:', form.serialize());
                var formData = {
                    color_id: form.find('select[name="color_id"]').val(),
                    size_id: form.find('select[name="size_id"]').val(),
                    quantity: form.find('input[name="quantity"]').val(),
                    _token: form.find('input[name="_token"]').val(),
                    _method: 'PUT'
                };
                console.log('Form data manually:', formData);
                
                // Thêm loading state
                $item.addClass('updating');
                $item.find('.gh-cart-select').prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        
                        // Hiển thị thông báo thành công
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
                                        toastElement.style.animation = 'fade-out 0.4s forwards';
                                        toastElement.addEventListener('animationend', function () {
                                            toastElement.remove();
                                        });
                                    });
                                }
                            }, 10);
                        }
                        
                        // Giữ nguyên vị trí scroll và highlight item được update
                        setTimeout(function() {
                            var $updatedItem = $('.gh-cart-item[data-cart-id="' + cartId + '"]');
                            if ($updatedItem.length > 0) {
                                // Chỉ highlight, không scroll
                                $updatedItem.addClass('just-updated');
                                
                                // Xóa highlight sau 2 giây
                                setTimeout(function() {
                                    $updatedItem.removeClass('just-updated');
                                }, 2000);
                            }
                            
                            // Giữ nguyên vị trí scroll ban đầu
                            $(window).scrollTop(scrollPosition);
                        }, 100);
                    },
                    error: function (xhr) {
                        // Loại bỏ loading state khi có lỗi
                        $item.removeClass('updating');
                        $item.find('.gh-cart-select').prop('disabled', false);
                        
                        console.log('Error details:', xhr);
                        console.log('Response text:', xhr.responseText);
                        console.log('Response JSON:', xhr.responseJSON);
                        
                        const errorMessage = xhr.responseJSON?.error || 'Có lỗi xảy ra khi thay đổi biến thể';
                        console.log('Final error message:', errorMessage);
                        
                        const toast = Toastify({
                            text: `
                                        <div class="toastify-content">
                                            <div class="toast-icon">✗</div>
                                            <div class="toast-message">${errorMessage}</div>
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
                        
                        // Cập nhật số lượng trên header
                        updateCartCount();
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

        // Function để cập nhật số lượng cart trên header
        function updateCartCount() {
            fetch('/cart/count')
                .then(response => response.json())
                .then(data => {
                    const cartBadge = document.querySelector('.cart-badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.count;
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi lấy số lượng giỏ hàng:', error);
                });
        }
    </script>
@endsection