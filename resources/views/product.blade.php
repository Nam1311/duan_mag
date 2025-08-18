@extends('app')

@section('body')


    <div class="pruductall">
        <div class="grid wide container">
            <div class="row">
                <div class="col l-3 c-0">
                    <form method="GET" action="{{ route('product.filter') }}" class="product-filter-container">
                        @if(request()->has('category'))
                            @foreach(request()->input('category') as $category)
                                <input type="hidden" name="category[]" value="{{ $category }}">
                            @endforeach
                        @endif
                        @if(request()->has('size'))
                            <input type="hidden" name="size" value="{{ request()->input('size') }}">
                        @endif
                        @if(request()->has('price'))
                            <input type="hidden" name="price" value="{{ request()->input('price') }}">
                        @endif

                        <div class="product-filter-desktop pruductall-danhmuc">
                            <div class="total-product">
                                <p><span class="total">    {{    $total }}</span> sản phẩm</p>
                            </div>

                            <!-- DANH MỤC -->
                            <div class="filter-section">
                                <h3><i class="fas fa-list"></i> DANH MỤC</h3>
                                <div class="category-options">
                                    @foreach ($categories as $category)
                                    <div class="category-option">
                                        <input type="checkbox" id="category{{ $category->id }}" class="custom-checkbox" name="category[]" value="{{ $category->id }}"
                                            {{ in_array($category->id, (array)request()->input('category')) ? 'checked' : '' }}>
                                        <label for="category{{ $category->id }}">{{ $category->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- SIZE -->
                            <div class="filter-section">
                                <h3><i class="fas fa-ruler"></i> SIZE</h3>
                                <div class="filter-options">
                                    @foreach ($sizes as $size)
                                    <div class="filter-option {{ request()->input('size') == $size->id ? 'active' : '' }}">
                                        <input type="radio" id="size{{ $size->id }}" class="custom-radio" name="size" value="{{ $size->id }}"
                                            {{ request()->input('size') == $size->id ? 'checked' : '' }}>
                                        <label for="size{{ $size->id }}">{{ $size->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- GIÁ -->
                            <div class="filter-section">
                                <h3><i class="fas fa-tag"></i> GIÁ</h3>
                                <div class="price-options">
                                    <div class="price-option {{ request()->input('price') == 1 ? 'active' : '' }}">
                                        <input type="radio" name="price" id="price1" class="custom-radio-price" value="1"
                                            {{ request()->input('price') == 1 ? 'checked' : '' }}>
                                        <label for="price1">Dưới 100.000đ</label>
                                    </div>
                                    <div class="price-option {{ request()->input('price') == 2 ? 'active' : '' }}">
                                        <input type="radio" name="price" id="price2" class="custom-radio-price" value="2"
                                            {{ request()->input('price') == 2 ? 'checked' : '' }}>
                                        <label for="price2">100.000đ - 200.000đ</label>
                                    </div>
                                    <div class="price-option {{ request()->input('price') == 3 ? 'active' : '' }}">
                                        <input type="radio" name="price" id="price3" class="custom-radio-price" value="3"
                                            {{ request()->input('price') == 3 ? 'checked' : '' }}>
                                        <label for="price3">200.000đ - 300.000đ</label>
                                    </div>
                                    <div class="price-option {{ request()->input('price') == 4 ? 'active' : '' }}">
                                        <input type="radio" name="price" id="price4" class="custom-radio-price" value="4"
                                            {{ request()->input('price') == 4 ? 'checked' : '' }}>
                                        <label for="price4">Trên 300.000đ</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="filter-button">Áp dụng bộ lọc</button>
                        </div>
                    </form>
                </div>
                <div class="col l-9">
                    <div class="product-box-sp">
                        <div id="box1" class="box-sanpham active-sanpham">
                            <section class="product-thun">
                                <div class="grid wide container">
                                    <style>
                                        .product-sort-mobile {
                                            display: flex;
                                            /* align-items: center; */
                                            justify-content: space-between;
                                        }

                                        .product-sort-mobile>.sort-item {
                                            width: auto;
                                            padding: 3px 7px;
                                        }

                                        /* CSS phân trang */
                                        .pagination {
                                            display: flex;
                                            list-style: none;
                                            padding: 0;
                                            justify-content: center;
                                            margin-top: 20px;
                                        }

                                        .pagination li {
                                            margin: 0 5px;
                                        }

                                        .pagination li a,
                                        .pagination li span {
                                            display: inline-block;
                                            padding: 5px 10px;
                                            border: 1px solid #ddd;
                                            text-decoration: none;
                                            color: #333;
                                            border-radius: 3px;
                                        }

                                        .pagination li.active span {
                                            background-color: #007bff;
                                            color: white;
                                            border-color: #007bff;
                                        }

                                        .pagination li a:hover {
                                            background-color: #f5f5f5;
                                        }

                                        .pagination li.disabled span {
                                            color: #aaa;
                                            cursor: not-allowed;
                                        }
                                    </style>
                                    <div class="product-sort-mobile">
                                        <a href="/san-pham" style="text-decoration: none; color: black;"><h2 class="page-title">Tất cả sản phẩm</h2></a>
                                            <div class="relative">
                                                <p class="sort-title">Sắp xếp theo: </p>
                                                <div class="dropdown-container">
                                                    <div class="select-trigger" id="sortButton">

                                                        <span class="select-value" id="selectedValue">
                                                            @if(request()->is('san-pham-noi-bat'))
                                                                Nổi bật
                                                            @elseif(request()->is('san-pham-ban-chay'))
                                                                Bán chạy
                                                            @elseif(request()->is('gia-thap-den-cao'))
                                                                Giá: Thấp đến Cao
                                                            @elseif(request()->is('gia-cao-den-thap'))
                                                                Giá: Cao đến Thấp
                                                            @else
                                                                Mặc định
                                                            @endif
                                                        </span>

                                                        <i class="fas fa-chevron-down select-icon"></i>
                                                    </div>
                                                    <style>
                                                        .dropdown-menu>.dropdown-item>a{
                                                            text-decoration: none;
                                                            color: black;
                                                        }
                                                    </style>
                                                    <ul class="dropdown-menu" id="dropdownMenu">
                                                        <li class="dropdown-item {{ request()->is('product') ? 'selected' : '' }}" data-value="Mặc định">
                                                            <span class="radio"></span><a href="/san-pham">Mặc định</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('san-pham-noi-bat') ? 'selected' : '' }}" data-value="Nổi bật">
                                                            <span class="radio"></span><a href="/san-pham-noi-bat">Nổi bật</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('san-pham-ban-chay') ? 'selected' : '' }}" data-value="Bán chạy">
                                                            <span class="radio"></span><a href="/san-pham-ban-chay">Bán chạy</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('gia-thap-den-cao') ? 'selected' : '' }}" data-value="Giá: Thấp đến Cao">
                                                            <span class="radio"></span><a href="gia-thap-den-cao"> Giá: Thấp đến Cao</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('gia-cao-den-thap') ? 'selected' : '' }}" data-value="Giá: Cao đến Thấp">
                                                            <span class="radio"></span><a href="gia-cao-den-thap">Giá: Cao đến Thấp</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                    </div>
                                    <!-- Filter trên mobile - dạng dropdown -->
                                    <div class="product-filter-mobile">
                                        <div class="mobile-filter-header">
                                            <div class="mobile-filter-toggle">
                                                <i class="fas fa-filter"></i> Bộ lọc
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                            <div class="product-filter-count">0</div>
                                        </div>

                                        <div class="mobile-filter-content">
                                            <form method="GET" action="{{ route('product.filter') }}">
                                                <div class="filter-section">
                                                    <h3>DANH MỤC</h3>
                                                    <div class="category-options">
                                                        @foreach ($categories as $category)
                                                            <div class="category-option">
                                                                <input type="checkbox" id="mobile_category{{ $category->id }}" name="category[]" value="{{ $category->id }}"
                                                                    {{ request()->input('category') == $category->id ? 'checked' : '' }}>
                                                                <label for="mobile_category{{ $category->id }}">{{ $category->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="filter-section">
                                                    <h3>SIZE</h3>
                                                    <div class="filter-options">
                                                        @foreach ($sizes as $size)
                                                            <div class="filter-option">
                                                                <input type="radio" id="mobile_size{{ $size->id }}" name="size" value="{{ $size->id }}"
                                                                    {{ request()->input('size') == $size->id ? 'checked' : '' }}>
                                                                <label for="mobile_size{{ $size->id }}">{{ $size->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="filter-section">
                                                    <h3>GIÁ</h3>
                                                    <div class="price-options">
                                                        @foreach ([1 => 'Dưới 100.000đ', 2 => '100.000đ - 200.000đ', 3 => '200.000đ - 300.000đ', 4 => 'Trên 300.000đ'] as $value => $label)
                                                            <div class="price-option">
                                                                <input type="radio" name="price" id="mobile_price{{ $value }}" value="{{ $value }}"
                                                                    {{ request()->input('price') == $value ? 'checked' : '' }}>
                                                                <label for="mobile_price{{ $value }}">{{ $label }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <button type="submit" class="filter-button">Áp dụng</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 20px">
                                        @if ($productAll->isEmpty())
                                            <div class="col l-12 m-12 c-12">
                                                <p style="text-align: center; font-size: 18px; color: #888;">Không có lựa chọn phù hợp.</p>
                                            </div>
                                        @else
                                            @foreach ($productAll as $productItem)
                                                <div class="col l-4 m-6 c-6">
                                                    <div class="item product-pading-size">
                                                        <div class="item-img">
                                                            <span class="item-giam">-{{ $productItem->sale }}%</span>
                                                            <div class="item-icon">
                                                                <i class="fa-solid fa-cart-shopping"></i>
                                                            </div>
                                                            <a href="{{ asset('/detail/' . $productItem->id) }}">
                                                                @if ($productItem->thumbnail && $productItem->thumbnail->path)
                                                                    <img src="{{ asset($productItem->thumbnail->path) }}" alt="Ảnh" width="150">
                                                                @else
                                                                    <img src="{{ asset('img/kocoanh.png') }}" alt="no ảnh ok like" width="150">
                                                                @endif
                                                            </a>
                                                            <div class="item-select-variant">
                                                                <a class="a-buy-now"
                                                                data-id="{{ $productItem->id }}"
                                                                data-name="{{ $productItem->name }}"
                                                                data-price="{{ $productItem->price }}"
                                                                data-original-price="{{ $productItem->original_price }}"
                                                                data-image="{{ asset($productItem->images->first()->path ?? '/img/default.jpg') }}"
                                                                href="javascript:void(0)">Mua ngay</a>
                                                            </div>
                                                        </div>

                                                        <div class="item-name">
                                                            <h3><a href="">{{ $productItem->name }}</a></h3>
                                                        </div>

                                                        <div class="item-price">
                                                            <span style="color: red; padding-right: 10px;">
                                                                {{ number_format($productItem->original_price * (1 - $productItem->sale / 100)) }}đ
                                                            </span>
                                                            <span>
                                                                <del>{{ number_format($productItem->original_price) }}đ</del>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </section>
                            <!-- Phân trang -->
                            <div class="chuyentrang">
                                {{ $productAll->links('pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- popup chọn biến thể --}}
    <div id="variantModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-product-info">
                <img id="modal-product-image" src="" alt="" style="max-width: 100px; margin-right: 20px;">
                <div>
                    <h3 id="modal-product-name"></h3>
                    <div class="modal-product-price">
                        <span id="modal-product-price" style="color: red;"></span>
                        <span id="modal-product-original-price" style="text-decoration: line-through;"></span>
                    </div>
                </div>
            </div>

            <!-- Color Section -->
            <div class="option-title">Màu sắc: Chọn màu</div>
            <div class="option-container" id="color-container">
                <!-- Colors will be populated dynamically -->
            </div>

            <!-- Size Section -->
            <div class="option-title" id="selected-icon">Kích thước: Chọn size</div>
            <div class="option-container" id="size-container">
                <!-- Sizes will be populated dynamically -->
            </div>

            <!-- Quantity Selection -->
            <div class="variant-group">
                <label>Số lượng:</label>
                <input type="number" class="quantity" value="1" min="1">
            </div>

            <div id="stock-info" style="margin-top: 15px; font-weight: bold; color: #333;">
                Vui lòng chọn màu và kích thước
            </div>

            <!-- Hidden Variants Data -->
            <div class="variant-data-container" style="display: none;" id="variant-data-container">
                <!-- Variants will be populated dynamically -->
            </div>

            <button class="btn-continue" data-product-id="">Tiếp tục</button>
        </div>
    </div>

    {{-- css popup chọn biến thể --}}
    <style>
        .modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            position: relative;
            width: 500px auto;
        }
        .close {
            position: absolute;
            right: 10px;
            top: 5px;
            font-size: 20px;
            cursor: pointer;
        }
        .modal-product-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .variant-group {
            margin-bottom: 10px;
        }
        .variant-group>input{
            width: auto;
            padding: 5px;
        }
        .detail-textall-imgicon.disabled,
        .detail-textall-sizeicon.disabled {
            opacity: 0.4;
            pointer-events: none;
            cursor: not-allowed;
        }
        .detail-textall-imgicon, .detail-textall-sizeicon {
            cursor: pointer;
            padding: 5px;
            margin: 5px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .detail-textall-imgicon.selected, .detail-textall-sizeicon.selected {
            border-color: #ff0000;
            background-color: #f0f0f0;
        }
        .btn-continue{
            border: none;
            width: 200px;
            padding: 10px 30px;
            background-color: #000;
            color: white;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 10px;
        }

    </style>

<script src="{{asset('main.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/js/lightslider.min.js"></script>

<script>
        document.addEventListener('DOMContentLoaded', () => {
            const cartIcons = document.querySelectorAll('.item-icon');
            cartIcons.forEach(icon => {
                icon.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productLink = icon.closest('.item').querySelector('a');
                    const href = productLink.getAttribute('href');
                    const productId = href.split('/').pop();
                    addToCart(productId);
                });
            });

            // Handle "Mua ngay" buttons
            const buyNowButtons = document.querySelectorAll('.a-buy-now');
            const modal = document.getElementById('variantModal');
            const modalProductImage = document.getElementById('modal-product-image');
            const modalProductName = document.getElementById('modal-product-name');
            const modalProductPrice = document.getElementById('modal-product-price');
            const modalProductOriginalPrice = document.getElementById('modal-product-original-price');
            const colorContainer = document.getElementById('color-container');
            const sizeContainer = document.getElementById('size-container');
            const variantDataContainer = document.getElementById('variant-data-container');
            const stockInfo = document.getElementById('stock-info');
            const modalContinueBtn = modal.querySelector('.btn-continue');
            let selectedColor = null;
            let selectedSize = null;
            let currentProductId = null;

            // Format price
            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
            }

            // Populate modal with product data
            function populateModal(productId, productName, price, originalPrice, image, colors, sizes, variants) {
                currentProductId = productId;
                modalProductName.textContent = productName;
                modalProductPrice.textContent = formatPrice(price);
                modalProductOriginalPrice.textContent = formatPrice(originalPrice);
                modalProductImage.src = image;
                modalContinueBtn.dataset.productId = productId;

                // Clear previous content
                colorContainer.innerHTML = '';
                sizeContainer.innerHTML = '';
                variantDataContainer.innerHTML = '';

                // Populate colors
                colors.forEach(color => {
                    const colorDiv = document.createElement('div');
                    colorDiv.className = 'detail-textall-imgicon';
                    colorDiv.style.backgroundColor = color.hex_code;
                    colorDiv.dataset.colorId = color.id;
                    colorDiv.innerHTML = `<p hidden>${color.name ?? ''}</p>`;
                    colorContainer.appendChild(colorDiv);
                });

                // Populate sizes
                sizes.forEach(size => {
                    const sizeDiv = document.createElement('div');
                    sizeDiv.className = 'detail-textall-sizeicon';
                    sizeDiv.dataset.sizeId = size.id;
                    sizeDiv.innerHTML = `<p>${size.name ?? ''}</p>`;
                    sizeContainer.appendChild(sizeDiv);
                });

                // Populate variants
                variants.forEach(variant => {
                    const variantInput = document.createElement('input');
                    variantInput.type = 'hidden';
                    variantInput.className = 'variant-data';
                    variantInput.dataset.variantId = variant.id;
                    variantInput.dataset.colorId = variant.color_id;
                    variantInput.dataset.sizeId = variant.size_id;
                    variantInput.dataset.quantity = variant.quantity;
                    variantDataContainer.appendChild(variantInput);
                });

                // Reset selections
                selectedColor = null;
                selectedSize = null;
                stockInfo.textContent = 'Vui lòng chọn màu và kích thước';

                // Add event listeners for color selection
                colorContainer.querySelectorAll('.detail-textall-imgicon').forEach(item => {
                    item.addEventListener('click', function () {
                        if (this.classList.contains('disabled')) return;
                        colorContainer.querySelectorAll('.detail-textall-imgicon').forEach(c => c.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedColor = this.dataset.colorId;

                        // Update size availability
                        sizeContainer.querySelectorAll('.detail-textall-sizeicon').forEach(sizeEl => {
                            const variant = variantDataContainer.querySelector(
                                `.variant-data[data-color-id="${selectedColor}"][data-size-id="${sizeEl.dataset.sizeId}"]`
                            );
                            sizeEl.classList.toggle('disabled', !variant || parseInt(variant.dataset.quantity, 10) <= 0);
                        });

                        updateStockInfo();
                    });
                });

                // Add event listeners for size selection
                sizeContainer.querySelectorAll('.detail-textall-sizeicon').forEach(item => {
                    item.addEventListener('click', function () {
                        if (this.classList.contains('disabled')) return;
                        sizeContainer.querySelectorAll('.detail-textall-sizeicon').forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                        selectedSize = this.dataset.sizeId;
                        updateStockInfo();
                    });
                });
            }

            // Update stock info
            function updateStockInfo() {
                if (selectedColor && selectedSize) {
                    const variant = variantDataContainer.querySelector(
                        `.variant-data[data-color-id="${selectedColor}"][data-size-id="${selectedSize}"]`
                    );
                    stockInfo.textContent = variant
                        ? `Còn lại: ${variant.dataset.quantity} sản phẩm`
                        : 'Không có hàng cho biến thể này';
                } else {
                    stockInfo.textContent = 'Vui lòng chọn màu và kích thước';
                }
            }

            // Open modal on "Mua ngay" click
            buyNowButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = btn.dataset.id;
                    const productName = btn.dataset.name;
                    const price = parseFloat(btn.dataset.price);
                    const originalPrice = parseFloat(btn.dataset.originalPrice);
                    const image = btn.dataset.image;

                    // Fetch product data including colors, sizes, and variants
                    fetch(`/api/product/${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            const { colors, sizes, variants } = data;
                            populateModal(productId, productName, price, originalPrice, image, colors, sizes, variants);
                            modal.style.display = 'flex';
                        })
                        .catch(error => {
                            console.error('Error fetching product data:', error);
                            alert('Không thể tải thông tin sản phẩm');
                        });
                });
            });

            // Close modal
            modal.querySelector('.close').addEventListener('click', () => {
                modal.style.display = 'none';
            });

            window.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Handle continue button
            modalContinueBtn.addEventListener('click', () => {
                if (!selectedColor || !selectedSize) {
                    alert('Vui lòng chọn màu và kích thước');
                    return;
                }

                const variant = variantDataContainer.querySelector(
                    `.variant-data[data-color-id="${selectedColor}"][data-size-id="${selectedSize}"]`
                );

                if (!variant) {
                    alert('Biến thể này không tồn tại');
                    return;
                }

                const variantId = variant.dataset.variantId;
                const quantity = modal.querySelector('.quantity').value;

                BuyInHome(variantId, quantity, currentProductId);
            });
        });

        function addToCart(productId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_variant_id: null,
                    quantity: 1,
                    product_id: productId
                })
            })
                .then(response => response.json())
                .then(data => {
                    const toast = Toastify({
                        text: `
                            <div class="toastify-content">
                                <div class="toast-icon">✓</div>
                                <div class="toast-message">${data.message}</div>
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

                    updateCartCount();
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                    alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
                });
        }

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

        function BuyInHome(variantId, quantity, productId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/cart/add/home', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    product_variant_id: variantId,
                    quantity: quantity,
                    product_id: productId
                })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Đã thêm vào giỏ');
                    }
                })
                .catch(err => alert(err.message || 'Lỗi kết nối'));
        }

        document.querySelector('.icon-search-mobile').addEventListener('click', function () {
            const searchBox = document.querySelector('.search-input-mobile');
            searchBox.classList.toggle('active');

            if (searchBox.classList.contains('active')) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Lấy tất cả các biểu tượng giỏ hàng
        const cartIcons = document.querySelectorAll('.item-icon');
        cartIcons.forEach(icon => {
            icon.addEventListener('click', (e) => {
                e.preventDefault(); // Ngăn hành vi mặc định nếu có

                // Lấy ID sản phẩm từ liên kết chi tiết sản phẩm
                const productLink = icon.closest('.item').querySelector('a');
                const href = productLink.getAttribute('href');
                const productId = href.split('/').pop(); // Lấy ID từ URL (ví dụ: /detail/1 -> 1)

                // Gửi yêu cầu thêm vào giỏ hàng
                addToCart(productId);
            });
        });
    });

    function addToCart(productId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Vì trang chủ không có lựa chọn biến thể, giả định số lượng là 1 và biến thể mặc định
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                product_variant_id: null, // Sẽ xử lý ở backend
                quantity: 1,
                product_id: productId // Thêm product_id để backend xử lý
            })
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Hiển thị thông báo từ server
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
            });
    }
</script>
<script src="{{ asset('/js/detail.js') }}"></script>
<script>
    // Xử lý sắp xếp sản phẩm
    const sortButton = document.getElementById('sortButton');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const selectedValue = document.getElementById('selectedValue');
    const dropdownItems = document.querySelectorAll('.dropdown-item');

    // Xử lý form lọc sản phẩm
    document.querySelector('.product-filter-container').addEventListener('submit', function(e) {
        e.preventDefault();

        // Hiển thị loading
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(loader);

        try {
            // Lấy URL hiện tại và các tham số
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            // Xóa các tham số lọc cũ
            params.delete('category[]');
            params.delete('size');
            params.delete('price');
            params.delete('page'); // Xóa phân trang khi lọc mới

            // Thêm danh mục được chọn
            const checkedCategories = Array.from(
                document.querySelectorAll('.category-options input[type="checkbox"]:checked')
            ).map(checkbox => checkbox.value);

            checkedCategories.forEach(category => {
                params.append('category[]', category);
            });

            // Thêm size nếu được chọn
            const size = document.querySelector('input[name="size"]:checked')?.value;
            if (size) params.set('size', size);

            // Thêm khoảng giá nếu được chọn
            const price = document.querySelector('input[name="price"]:checked')?.value;
            if (price) params.set('price', price);

            // Cập nhật URL và chuyển hướng
            url.search = params.toString();
            window.location.href = url.toString();
        } catch (error) {
            console.error('Lỗi khi áp dụng bộ lọc:', error);
            document.querySelector('.loading-overlay')?.remove();
        }
    });

    // Show/hide menu sắp xếp
    sortButton.addEventListener('click', () => {
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Xử lý chọn option sắp xếp
    dropdownItems.forEach(item => {
        item.addEventListener('click', () => {
            dropdownItems.forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
            selectedValue.textContent = item.dataset.value;
            dropdownMenu.style.display = 'none';
        });
    });

    // Đóng menu khi click bên ngoài
    document.addEventListener('click', (event) => {
        if (!sortButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });

    // Xử lý filter mobile
    document.addEventListener('DOMContentLoaded', function () {
        const mobileFilterToggle = document.querySelector('.mobile-filter-toggle');
        const mobileFilterContent = document.querySelector('.mobile-filter-content');
        const filterCount = document.querySelector('.product-filter-count');

        // Toggle mobile filter
        if (mobileFilterToggle) {
            mobileFilterToggle.addEventListener('click', function () {
                mobileFilterContent.classList.toggle('active');
                const icon = this.querySelector('.fa-chevron-down');
                icon.classList.toggle('fa-rotate-180');
            });
        }

        // Xử lý active size option
        const sizeOptions = document.querySelectorAll('.filter-options .filter-option');
        sizeOptions.forEach(option => {
            option.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT') return;

                sizeOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Xử lý active price option
        const priceOptions = document.querySelectorAll('.price-options .price-option');
        priceOptions.forEach(option => {
            option.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT') return;

                priceOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    });

    window.addEventListener('scroll', function () {
        const box = document.querySelector('.pruductall-danhmuc');
        const stopPoint = 1100; // px, điểm muốn dừng lại

        if (window.scrollY >= stopPoint) {
            box.classList.add('stop-fixed');
            // Giữ nguyên vị trí khi dừng
            box.style.top = stopPoint + 'px';
        } else {
            box.classList.remove('stop-fixed');
            box.style.top = '20px'; // về lại vị trí ban đầu
        }
    });

</script>

<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #000000;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection
