let selectedColor = null;  // Biến lưu màu sắc được chọn
let selectedSize = null;   // Biến lưu kích thước được chọn
let currentVariantId = null;
let maxStock = 0;

// Hàm cập nhật số lượng hiển thị theo màu + size đã chọn
function updateQuantityDisplay() {
    const productIdInput = document.getElementById('product-id');  // Lấy input ẩn chứa product id
    const categoryInput = document.getElementById('product-category'); // Lấy category
    
    if (!productIdInput) {
        console.error('Không tìm thấy input product-id trong DOM');
        return;  // Nếu không có product id thì thoát hàm
    }

    const productId = productIdInput.value;  // Lấy giá trị product id
    const category = categoryInput ? categoryInput.value : '';
    
    // Kiểm tra xem có phải phụ kiện hoặc quần không
    const isAccessoryOrTrousers = category.includes('phụ kiện') || 
                                category.includes('quần') ||
                                category.includes('accessories') ||
                                category.includes('pants') ||
                                category.includes('trousers');
    
    // Với phụ kiện/quần: chỉ cần màu, không cần size
    // Với áo khác: cần cả màu và size
    const canProceed = isAccessoryOrTrousers ? 
        (selectedColor) : 
        (selectedColor && selectedSize);

    if (canProceed) {  // Nếu đã chọn đủ thông tin cần thiết
        console.log('Gọi API với:', { productId, selectedColor, selectedSize, category });

        const url = isAccessoryOrTrousers ? 
            `/get-variant-quantity?product_id=${productId}&color=${selectedColor}` :
            `/get-variant-quantity?product_id=${productId}&color=${selectedColor}&size=${selectedSize}`;
            
        // Gọi API lấy số lượng variant dựa trên product_id, color, size (tùy category)
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Lỗi khi fetch dữ liệu'); // Nếu lỗi HTTP thì báo lỗi
                return response.json();  // Trả về dữ liệu JSON
            })
            .then(data => {
                maxStock = data.quantity;

                // Hiển thị số lượng còn trong phần tử #stock-info
                document.getElementById('stock-info').textContent = `Số lượng còn: ${data.quantity}`;
                if (data.sku == undefined) {
                    data.sku = ''
                }
                document.getElementById('sku-info').textContent = `Mã SKU: ${data.sku}`;
                currentVariantId = data.product_variant_id;
                document.getElementById('product_variant_id').value = currentVariantId;
                toggleQuantityButtons();
            })
            .catch(error => {  // Xử lý lỗi khi fetch
                console.error('Lỗi:', error);
                // Thông báo lỗi cho người dùng
                document.getElementById('stock-info').textContent = 'Không thể lấy dữ liệu';
            });
    } else {
        // Nếu chưa chọn đủ màu và size thì nhắc người dùng chọn
        document.getElementById('stock-info').textContent = 'Vui lòng chọn màu và kích thước';
    }
}


function toggleQuantityButtons() {
    const decreaseBtn = document.getElementById("decrease");
    const increaseBtn = document.getElementById("increase");
    const quantityInput = document.getElementById("quantity");
    const currentValue = parseInt(quantityInput.value, 10) || 1;

    // Giảm luôn bật nếu >1
    decreaseBtn.disabled = currentValue <= 1;

    // Tăng chỉ bật khi chưa đạt maxStock
    // increaseBtn.disabled = currentValue >= maxStock;
    // Nếu hết hàng, bạn có thể disable luôn nút thêm giỏ:
    // document.getElementById('btnAddCart').disabled = maxStock === 0;
}

// Đợi DOM load xong rồi thực hiện các thao tác
document.addEventListener('DOMContentLoaded', () => {
    const sizeIcons = document.querySelectorAll('.detail-textall-sizeicon');  // Lấy tất cả div chọn size
    const colorIcons = document.querySelectorAll('.detail-textall-imgicon');  // Lấy tất cả div chọn màu
    const sizeDisplay = document.getElementById('selected-icon');             // Phần hiển thị kích thước được chọn
    const colorDisplay = document.getElementById('selected-iconhinhanh');     // Phần hiển thị màu được chọn

    // Khởi tạo mặc định:
    if (sizeIcons.length > 0) {
        sizeIcons[0].classList.add('active');          // Đánh dấu size đầu tiên là active
        selectedSize = sizeIcons[0].textContent.trim();  // Lấy giá trị size đầu tiên
        sizeDisplay.textContent = `Kích thước: ${selectedSize}`;  // Hiển thị kích thước được chọn
    }

    if (colorIcons.length > 0) {
        colorIcons[0].classList.add('active');          // Đánh dấu màu đầu tiên là active
        selectedColor = colorIcons[0].textContent.trim();  // Lấy giá trị màu đầu tiên
        colorDisplay.textContent = `Màu: ${selectedColor}`;  // Hiển thị màu được chọn
    }

    updateQuantityDisplay();  // Cập nhật số lượng hiển thị ngay khi trang tải xong

    // Lắng nghe sự kiện click chọn size
    sizeIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            sizeIcons.forEach(i => i.classList.remove('active'));  // Bỏ active ở tất cả size
            icon.classList.add('active');  // Đánh dấu size vừa click là active
            selectedSize = icon.textContent.trim();  // Lấy giá trị size mới
            sizeDisplay.textContent = `Kích thước: ${selectedSize}`;  // Cập nhật hiển thị size
            updateQuantityDisplay();  // Cập nhật số lượng mới
        });
    });

    // Lắng nghe sự kiện click chọn màu
    colorIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            colorIcons.forEach(i => i.classList.remove('active'));  // Bỏ active ở tất cả màu
            icon.classList.add('active');  // Đánh dấu màu vừa click là active
            selectedColor = icon.textContent.trim();  // Lấy giá trị màu mới
            colorDisplay.textContent = `Màu: ${selectedColor}`;  // Cập nhật hiển thị màu
            updateQuantityDisplay();  // Cập nhật số lượng mới
        });

    });
    document.getElementById('btnAddCart').addEventListener('click', () => {
        const categoryInput = document.getElementById('product-category');
        const category = categoryInput ? categoryInput.value : '';
        
        // Kiểm tra category để quyết định yêu cầu
        const isAccessoryOrTrousers = category.includes('phụ kiện') || 
                                    category.includes('quần') ||
                                    category.includes('accessories') ||
                                    category.includes('pants') ||
                                    category.includes('trousers');
        
        // Kiểm tra điều kiện dựa trên category
        if (isAccessoryOrTrousers) {
            if (!selectedColor) {
                alert('Vui lòng chọn màu trước khi thêm giỏ hàng.');
                return;
            }
        } else {
            if (!selectedColor || !selectedSize) {
                alert('Vui lòng chọn màu và kích thước trước khi thêm giỏ hàng.');
                return;
            }
        }

        if (!currentVariantId) {
            alert('Không tìm thấy biến thể phù hợp. Vui lòng thử lại.');
            return;
        }


        const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/cart/add', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                product_variant_id: currentVariantId,
                quantity: quantity,
            })
        })
            .then(res => res.json())
            .then(resp => {
                const toast = Toastify({
                    text: `
                    <div class="toastify-content">
                        <div class="toast-icon">✓</div>
                        <div class="toast-message">${resp.message}</div>
                        <button class="toast-close">×</button>
                    </div>
                `,
                    duration: 3000,
                    close: false,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
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
                updateCartCount();

            })
            .catch(err => { console.error(err); alert('Lỗi khi thêm giỏ hàng'); });

    });



    // document.getElementById('btnAddCheckout').addEventListener('click', () => {
    //     const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
    //     const variantId = currentVariantId; // hoặc lấy từ data-attribute
    //     // Chuyển thẳng sang URL có query params
    //     window.location.href = `/payment?product_variant_id=${variantId}&quantity=${quantity}`;
    // });

    document.getElementById('btnAddCheckout').addEventListener('click', () => {
        const categoryInput = document.getElementById('product-category');
        const category = categoryInput ? categoryInput.value : '';
        
        // Kiểm tra category để quyết định yêu cầu
        const isAccessoryOrTrousers = category.includes('phụ kiện') || 
                                    category.includes('quần') ||
                                    category.includes('accessories') ||
                                    category.includes('pants') ||
                                    category.includes('trousers');
        
        // Kiểm tra điều kiện dựa trên category
        if (isAccessoryOrTrousers) {
            if (!selectedColor) {
                alert('Vui lòng chọn màu trước khi thanh toán.');
                return;
            }
        } else {
            if (!selectedColor || !selectedSize) {
                alert('Vui lòng chọn màu và kích thước trước khi thanh toán.');
                return;
            }
        }

        if (!currentVariantId) {
            alert('Không tìm thấy biến thể phù hợp. Vui lòng thử lại.');
            return;
        }

        const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
        const productId = document.getElementById('product-id').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Gửi yêu cầu kiểm tra và thanh toán
        fetch('/checkout-direct', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                product_variant_id: currentVariantId,
                quantity: quantity,
                product_id: productId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.error) {
                    const toast = Toastify({
                        text: `
        <div class="toastify-content">
            <div class="toast-icon">✓</div>
            <div class="toast-message">${data.error}</div>
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

                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi xử lý thanh toán');
            });
    });
    // document.getElementById('btnAddCheckout').addEventListener('click', () => {


    //     if (!currentVariantId) {
    //         alert('Vui lòng chọn màu và kích thước trước khi thêm giỏ hàng.');
    //         return;
    //     }


    //     const quantity = parseInt(document.getElementById('quantity').value, 10) || 1;
    //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    //     fetch('/payment', {
    //         method: 'POST',
    //         credentials: 'same-origin',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'Accept': 'application/json',
    //             'X-CSRF-TOKEN': csrfToken
    //         },
    //         body: JSON.stringify({
    //             product_variant_id: currentVariantId,
    //             quantity: quantity,
    //         })
    //     })
    //         .then(res => res.json())
    //         .then(resp => alert(resp.message))
    //         .catch(err => { console.error(err); alert('Lỗi khi thêm giỏ hàng'); });

    // });

    // ------------ Slider ảnh chi tiết ------------
    const imagedaitals = document.querySelectorAll('.detail-imgall img');  // Ảnh lớn
    const thumbnails = document.querySelectorAll('.detail-itemimg');       // Ảnh thumbnail nhỏ
    const sliderdeital = document.getElementById('sliderdeital');          // Container slider
    const prevBtn = document.querySelector('.prev-btndeital');             // Nút previous
    const nextBtn = document.querySelector('.next-btndeital');             // Nút next
    let currentIndex = 0;   // Ảnh hiện tại đang hiển thị
    let isDragging = false; // Biến xác định đang kéo chuột
    let startX = 0;         // Vị trí bắt đầu kéo chuột
    let moveX = 0;          // Khoảng cách kéo chuột

    // Hàm cập nhật ảnh lớn và thumbnail theo ảnh hiện tại
    function updateImagedaitals() {
        imagedaitals.forEach((img, index) => {
            img.classList.toggle('activedeiatl', index === currentIndex);  // Ảnh lớn được active
        });
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('activedeiatl', index === currentIndex);  // Thumbnail được active
        });
    }

    // Click nút previous chuyển ảnh trước
    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + imagedaitals.length) % imagedaitals.length;  // Vòng lại nếu vượt biên
        updateImagedaitals();
    });

    // Click nút next chuyển ảnh tiếp theo
    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % imagedaitals.length;  // Vòng lại nếu vượt biên
        updateImagedaitals();
    });

    // Sự kiện bắt đầu kéo chuột trên slider
    sliderdeital.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.clientX;  // Ghi nhận vị trí bắt đầu kéo
    });

    // Sự kiện kéo chuột trên slider
    sliderdeital.addEventListener('mousemove', (e) => {
        if (!isDragging) return;  // Nếu chưa kéo thì không xử lý
        moveX = e.clientX - startX;  // Tính khoảng cách di chuyển chuột
    });

    // Sự kiện thả chuột trên slider
    sliderdeital.addEventListener('mouseup', () => {
        if (Math.abs(moveX) > 50) {  // Nếu kéo đủ khoảng cách (>50px)
            currentIndex = (moveX < 0)
                ? (currentIndex + 1) % imagedaitals.length  // Kéo sang trái chuyển ảnh kế tiếp
                : (currentIndex - 1 + imagedaitals.length) % imagedaitals.length;  // Kéo sang phải chuyển ảnh trước
            updateImagedaitals();
        }
        isDragging = false;  // Reset trạng thái kéo
        moveX = 0;           // Reset khoảng cách kéo
    });

    // Các sự kiện cảm ứng cho thiết bị di động
    sliderdeital.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;  // Vị trí bắt đầu chạm
    });

    sliderdeital.addEventListener('touchmove', (e) => {
        moveX = e.touches[0].clientX - startX;  // Khoảng cách di chuyển tay
    });

    sliderdeital.addEventListener('touchend', () => {
        if (Math.abs(moveX) > 50) {  // Nếu vuốt đủ khoảng cách
            currentIndex = (moveX < 0)
                ? (currentIndex + 1) % imagedaitals.length  // Vuốt sang trái chuyển ảnh kế tiếp
                : (currentIndex - 1 + imagedaitals.length) % imagedaitals.length;  // Vuốt sang phải chuyển ảnh trước
            updateImagedaitals();
        }
        moveX = 0;  // Reset khoảng cách vuốt
    });

    // Click vào thumbnail để chuyển ảnh tương ứng
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            currentIndex = index;
            updateImagedaitals();
        });
    });

    // ------------- Popup bảng size chart -------------
    const openBox = document.getElementById('openBox');  // Nút mở popup
    const popupBox = document.getElementById('popupBox');  // Hộp popup
    const overlay = document.getElementById('overlay');  // Màn hình phủ mờ nền
    const closeBox = document.getElementById('closeBox');  // Nút đóng popup

    // Mở popup khi click vào nút openBox
    openBox.addEventListener('click', (e) => {
        e.preventDefault();  // Ngăn sự kiện mặc định (nếu có)
        popupBox.style.display = 'block';  // Hiển thị popup
        overlay.style.display = 'block';   // Hiển thị overlay mờ nền
    });

    // Đóng popup khi click nút đóng
    closeBox.addEventListener('click', () => {
        popupBox.style.display = 'none';  // Ẩn popup
        overlay.style.display = 'none';   // Ẩn overlay
    });

    // Đóng popup khi click ra ngoài overlay
    overlay.addEventListener('click', () => {
        popupBox.style.display = 'none';  // Ẩn popup
        overlay.style.display = 'none';   // Ẩn overlay
    });

    // ------------- Nút tăng giảm số lượng -------------
    const decreaseBtn = document.getElementById("decrease");  // Nút giảm số lượng
    const increaseBtn = document.getElementById("increase");  // Nút tăng số lượng
    const quantityInput = document.getElementById("quantity");  // Input số lượng

    // Xử lý giảm số lượng (tối thiểu 1)
    decreaseBtn.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value, 10);  // Lấy giá trị hiện tại
        if (currentValue > 1) quantityInput.value = currentValue - 1;  // Giảm nếu > 1
    });

    // Xử lý tăng số lượng
    increaseBtn.addEventListener("click", () => {
        let currentValue = parseInt(quantityInput.value, 10);
        if (currentValue < maxStock) {
            quantityInput.value = currentValue + 1;
            toggleQuantityButtons();
        } else {
            alert(`Bạn chỉ có thể mua tối đa ${maxStock} sản phẩm.`);
        }
    });

    quantityInput.addEventListener("input", () => {
        let val = parseInt(quantityInput.value, 10) || 1;
        if (val < 1) val = 1;
        if (val > maxStock) {
            val = maxStock;
            alert(`Số lượng tối đa bạn có thể chọn là ${maxStock}.`);
        }
        quantityInput.value = val;
        toggleQuantityButtons();
    });



    // ------------- Chuyển tab Chi tiết sản phẩm / Bình luận -------------
    window.onload = function () {
        document.getElementById('detail-sp').style.border = '1px solid red';  // Tab chi tiết sản phẩm được highlight
        document.getElementById('box-detail-sp').style.display = 'block';     // Hiển thị nội dung chi tiết sản phẩm mặc định
    };
});


// Hàm chuyển đổi nội dung tab chi tiết sản phẩm hoặc bình luận
function changeContent(itemNumber) {
    document.getElementById('box-detail-sp').style.display = 'none';  // Ẩn box chi tiết sản phẩm
    document.getElementById('box-detail-bl').style.display = 'none';  // Ẩn box bình luận
    document.getElementById('detail-sp').style.border = '1px solid black';  // Reset border tab chi tiết sản phẩm
    document.getElementById('detail-bl').style.border = '1px solid black';  // Reset border tab bình luận

    if (itemNumber === 1) {  // Nếu chọn tab chi tiết sản phẩm
        document.getElementById('box-detail-sp').style.display = 'block';  // Hiển thị box chi tiết sản phẩm
        document.getElementById('detail-sp').style.border = '1px solid red';  // Đổi màu viền tab
    } else if (itemNumber === 2) {  // Nếu chọn tab bình luận
        document.getElementById('box-detail-bl').style.display = 'block';  // Hiển thị box bình luận
        document.getElementById('detail-bl').style.border = '1px solid red';  // Đổi màu viền tab
    }
}

