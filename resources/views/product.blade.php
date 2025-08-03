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
                                        <a href="/products" style="text-decoration: none; color: black;"><h2 class="page-title">Tất cả sản phẩm</h2></a>
                                            <div class="relative">
                                                <p class="sort-title">Sắp xếp theo: </p>
                                                <div class="dropdown-container">
                                                    <div class="select-trigger" id="sortButton">

                                                        <span class="select-value" id="selectedValue">
                                                            @if(request()->is('productFeatured'))
                                                                Nổi bật
                                                            @elseif(request()->is('productBestseller'))
                                                                Bán chạy
                                                            @elseif(request()->is('productPriceLowToHight'))
                                                                Giá: Thấp đến Cao
                                                            @elseif(request()->is('productPriceHightToLow'))
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
                                                            <span class="radio"></span><a href="/products">Mặc định</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('productFeatured') ? 'selected' : '' }}" data-value="Nổi bật">
                                                            <span class="radio"></span><a href="/productFeatured">Nổi bật</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('productBestseller') ? 'selected' : '' }}" data-value="Bán chạy">
                                                            <span class="radio"></span><a href="/productBestseller">Bán chạy</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('productPriceLowToHight') ? 'selected' : '' }}" data-value="Giá: Thấp đến Cao">
                                                            <span class="radio"></span><a href="productPriceLowToHight"> Giá: Thấp đến Cao</a>
                                                        </li>
                                                        <li class="dropdown-item {{ request()->is('productPriceHightToLow') ? 'selected' : '' }}" data-value="Giá: Cao đến Thấp">
                                                            <span class="radio"></span><a href="productPriceHightToLow">Giá: Cao đến Thấp</a>
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
                                                            <div class="item-buy-now">
                                                                <a class="a-buy-now" href="">Mua ngay</a>
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

    <script src="{{asset('main.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/js/lightslider.min.js"></script>
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
