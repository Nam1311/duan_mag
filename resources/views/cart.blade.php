@extends('app')

@section('body')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/css/cart.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <!-- Preload important resources -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/toastify-js" as="script">

    <div class="gh-cart-root">
        <main class="gh-cart-container">
            <div class="gh-cart-layout">
                <div class="gh-cart-items-section">
                    @include('cart._bulk_actions')
                    @include('cart._items')
                </div>
                @include('cart._summary')
            </div>
            
            {{-- Phần gợi ý sản phẩm và lịch sử đã xem --}}
            @include('cart._suggestions')
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

            // Function để cập nhật trạng thái các nút
            function updateButtonStates() {
                var selectedCount = $('.gh-cart-item-checkbox:checked').length;
                var totalCount = $('.gh-cart-item-checkbox').length;
                
                // Cập nhật trạng thái "chọn tất cả"
                if (selectedCount === 0) {
                    $('#gh-cart-select-all').prop('checked', false);
                    $('#gh-cart-select-all').prop('indeterminate', false);
                } else if (selectedCount === totalCount) {
                    $('#gh-cart-select-all').prop('checked', true);
                    $('#gh-cart-select-all').prop('indeterminate', false);
                } else {
                    $('#gh-cart-select-all').prop('checked', false);
                    $('#gh-cart-select-all').prop('indeterminate', true);
                }
                
                // Cập nhật trạng thái các nút
                var checkoutBtn = $('#gh-cart-checkout-selected');
                var removeBtn = $('#gh-cart-remove-selected');
                
                if (selectedCount > 0) {
                    checkoutBtn.prop('disabled', false);
                    removeBtn.prop('disabled', false);
                    checkoutBtn.html(`<i class="fas fa-credit-card"></i> Thanh toán ${selectedCount} sản phẩm`);
                    removeBtn.html(`<i class="fas fa-trash-alt"></i> Xóa ${selectedCount} sản phẩm`);
                } else {
                    checkoutBtn.prop('disabled', true);
                    removeBtn.prop('disabled', true);
                    checkoutBtn.html('<i class="fas fa-credit-card"></i> Thanh toán đã chọn');
                    removeBtn.html('<i class="fas fa-trash-alt"></i> Xóa đã chọn');
                }
            }

            // Chọn tất cả checkbox
            $(document).on('change', '#gh-cart-select-all', function () {
                var checked = $(this).is(':checked');
                $('.gh-cart-item-checkbox').prop('checked', checked);
                
                // Cập nhật trạng thái nút
                updateButtonStates();
            });

            // Chọn/bỏ chọn checkbox items
            $(document).on('change', '.gh-cart-item-checkbox', function () {
                updateButtonStates();
            });

            // Ẩn/hiện bulk actions dựa trên số lượng sản phẩm
            function toggleBulkActions() {
                var hasItems = $('.gh-cart-item-checkbox').length > 0;
                $('.gh-cart-bulk-actions').toggle(hasItems);
                
                // Reset trạng thái nút khi không có items
                if (!hasItems) {
                    $('#gh-cart-select-all').prop('checked', false);
                    $('#gh-cart-select-all').prop('indeterminate', false);
                    $('#gh-cart-checkout-selected').prop('disabled', true);
                    $('#gh-cart-remove-selected').prop('disabled', true);
                } else {
                    // Cập nhật trạng thái nút khi có items
                    updateButtonStates();
                }
            }

            // Gọi khi trang load
            toggleBulkActions();

            // Gọi lại sau mỗi lần update cart và reset checkbox states
            $(document).ajaxComplete(function() {
                setTimeout(function() {
                    toggleBulkActions();
                    // Reset checkbox states sau khi load lại
                    $('#gh-cart-select-all').prop('checked', false);
                    $('#gh-cart-select-all').prop('indeterminate', false);
                    $('.gh-cart-item-checkbox').prop('checked', false);
                    // Sử dụng function để cập nhật trạng thái nút
                    updateButtonStates();
                }, 100);
            });

            // Xóa các sản phẩm đã chọn
            $(document).on('click', '#gh-cart-remove-selected', function () {
                var selected = $('.gh-cart-item-checkbox:checked').map(function () {
                    return $(this).data('variant-id');
                }).get();
                
                if (selected.length === 0) {
                    const toast = Toastify({
                        text: `
                            <div class="toastify-content">
                                <div class="toast-icon">⚠️</div>
                                <div class="toast-message">Vui lòng chọn sản phẩm để xóa.</div>
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
                
                if (!confirm('Bạn có chắc muốn xóa ' + selected.length + ' sản phẩm đã chọn?')) return;
                
                // Hiển thị loading
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xóa...');
                
                var scrollPosition = $(window).scrollTop();
                $.ajax({
                    url: '/cart/remove-multiple',
                    method: 'DELETE',
                    data: { variant_ids: selected },
                    success: function (response) {
                        $('.gh-cart-items-container').html(response.items_html);
                        $('.gh-cart-summary').html(response.summary_html);
                        $(window).scrollTop(scrollPosition);
                        
                        // Cập nhật số lượng trên header
                        updateCartCount();
                        
                        const toast = Toastify({
                            text: `
                                <div class="toastify-content">
                                    <div class="toast-icon">✓</div>
                                    <div class="toast-message">Đã xóa ${selected.length} sản phẩm khỏi giỏ hàng</div>
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
                    },
                    error: function (xhr) {
                        // Khôi phục nút
                        $('#gh-cart-remove-selected').prop('disabled', false)
                            .html(`<i class="fas fa-trash-alt"></i> Xóa ${selected.length} sản phẩm`);
                            
                        const toast = Toastify({
                            text: `
                                <div class="toastify-content">
                                    <div class="toast-icon">❌</div>
                                    <div class="toast-message">${xhr.responseJSON?.error || 'Có lỗi xảy ra khi xóa sản phẩm'}</div>
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

            // Thanh toán các sản phẩm đã chọn
            $(document).on('click', '#gh-cart-checkout-selected', function () {
                var selected = $('.gh-cart-item-checkbox:checked').map(function () {
                    return $(this).data('variant-id');
                }).get();
                
                if (selected.length === 0) {
                    const toast = Toastify({
                        text: `
                            <div class="toastify-content">
                                <div class="toast-icon">⚠️</div>
                                <div class="toast-message">Vui lòng chọn sản phẩm để thanh toán.</div>
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
                
                // Hiển thị loading
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                
                $.ajax({
                    url: '/cart/checkout-selected',
                    method: 'POST',
                    data: { variant_ids: selected },
                    success: function (response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function (xhr) {
                        // Khôi phục nút
                        $('#gh-cart-checkout-selected').prop('disabled', false)
                            .html(`<i class="fas fa-credit-card"></i> Thanh toán ${selected.length} sản phẩm`);
                            
                        const toast = Toastify({
                            text: `
                                <div class="toastify-content">
                                    <div class="toast-icon">❌</div>
                                    <div class="toast-message">${xhr.responseJSON?.error || 'Có lỗi xảy ra khi thanh toán'}</div>
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

            // Áp dụng voucher khi bấm nút submit
            $(document).on('submit', 'form[action*="applyVoucher"]', function (e) {
                e.preventDefault();
                var form = $(this);
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
                        
                        // Animate badge update
                        cartBadge.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            cartBadge.style.transform = 'scale(1)';
                        }, 200);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi lấy số lượng giỏ hàng:', error);
                });
        }

        // Enhanced mobile touch support
        if ('ontouchstart' in window) {
            // Add touch-friendly classes
            document.body.classList.add('touch-device');
            
            // Improve tap targets
            const buttons = document.querySelectorAll('button, .gh-cart-item');
            buttons.forEach(button => {
                button.style.minHeight = '44px';
            });
        }

        // Performance optimization - lazy load images
        const images = document.querySelectorAll('img[loading="lazy"]');
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }

        // Enhanced error handling
        window.addEventListener('error', function(e) {
            console.error('Cart page error:', e.error);
        });

        // Track page visibility for analytics
        if (typeof trackViewedProduct === 'function') {
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    console.log('Cart page visible - ready for interactions');
                }
            });
        }
    </script>
@endsection