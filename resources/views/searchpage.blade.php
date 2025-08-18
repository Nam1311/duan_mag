@extends('app')

@section('body')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="pruductall">
        <div class="grid wide container">
            <div class="row">
                {{-- <div class="col l-3 c-0">
                    <form method="GET" action="{{ route('product.filter') }}" class="product-filter-container">
                        <!-- Thêm các tham số hiện tại vào form -->
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
                                <p>Tất cả:   <span class="total">    {{    $total }}</span> sản phẩm</p>
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
                </div> --}}
                <div class="col l-12">
                    <div class="product-box-sp">
                        <div id="box1" class="box-sanpham active-sanpham">
                            <section class="product-thun">
                                <div class="grid wide container">
                                    <p class="title-search" style="margin-top: 20px">
                                        Tìm thấy <span style="font-weight: bold">{{$total}}</span> kết quả cho từ khóa: <span style="color: red">"{{$keyword}}"</span>
                                    </p>


                                    <style>
                                        .product-sort-mobile {
                                            display: flex;
                                            align-items: center;
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
                                    {{-- <div class="product-sort-mobile">
                                        <h2 class="page-title">Tất cả sản phẩm</h2>
                                        <div class="relative">
                                            <p>Sắp xếp theo: </p>
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
                                    </div> --}}
                                    <!-- Filter trên mobile - dạng dropdown -->
                                    {{-- <div class="product-filter-mobile">
                                        <div class="mobile-filter-header">
                                            <div class="mobile-filter-toggle">
                                                <i class="fas fa-filter"></i> Bộ lọc
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                            <div class="product-filter-count">0</div>
                                        </div>

                                        <div class="mobile-filter-content">

                                        </div>
                                    </div> --}}
                                    <div class="row" style="margin-top: 20px">
                                        @foreach ($productAll as $productItem)
                                            <div class="col l-3 m-6 c-6">
                                                <div class="item product-pading-size">
                                                    <div class="item-img">
                                                        <span class="item-giam">-{{ $productItem->sale }}%</span>
                                                        <div class="item-icon">
                                                            <i class="fa-solid fa-cart-shopping"></i>
                                                        </div>
                                                        <a href="{{ asset('/detail/' . $productItem->id) }}">
                                                            @if ($productItem->thumbnail && $productItem->thumbnail->path)
                                                                <img src="{{ asset( $productItem->thumbnail->path) }}" alt="Ảnh" width="150">
                                                            @else
                                                                <img src="{{ asset('img/kocoanh.png') }}" alt="no ảnh ok like" width="150">
                                                            @endif
                                                        </a>
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
        // lọc sản phẩm
        const sortButton = document.getElementById('sortButton');
        const dropdownMenu = document.getElementById('dropdownMenu');
        const selectedValue = document.getElementById('selectedValue');
        const dropdownItems = document.querySelectorAll('.dropdown-item');

        // Show/hide menu on button click
        sortButton.addEventListener('click', () => {
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Handle item selection
        dropdownItems.forEach(item => {
            item.addEventListener('click', () => {
                dropdownItems.forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                selectedValue.textContent = item.dataset.value;
                dropdownMenu.style.display = 'none';
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!sortButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Lấy các phần tử DOM
            const mobileFilterToggle = document.querySelector('.mobile-filter-toggle');
            const mobileFilterContent = document.querySelector('.mobile-filter-content');
            const desktopFilter = document.querySelector('.product-filter-desktop');
            const filterCount = document.querySelector('.product-filter-count');

            // Sao chép nội dung filter từ desktop sang mobile
            if (desktopFilter && mobileFilterContent) {
                mobileFilterContent.innerHTML = desktopFilter.innerHTML;
            }

            // Xử lý toggle mobile filter
            if (mobileFilterToggle) {
                mobileFilterToggle.addEventListener('click', function () {
                    mobileFilterContent.classList.toggle('active');
                    const icon = this.querySelector('.fa-chevron-down');
                    icon.classList.toggle('fa-rotate-180');
                });
            }

            // Biến lưu trữ các lựa chọn filter
            let selectedFilters = {
                categories: [],
                sizes: [],
                colors: [],
                price: null
            };

            // Hàm cập nhật số lượng filter đã chọn
            function updateFilterCount() {
                let count = 0;

                // Đếm danh mục
                count += selectedFilters.categories.length;

                // Đếm size
                count += selectedFilters.sizes.length;

                // Đếm màu
                count += selectedFilters.colors.length;

                // Đếm giá
                if (selectedFilters.price) count += 1;

                filterCount.textContent = count;
                filterCount.style.display = count > 0 ? 'flex' : 'none';
            }

            // Khôi phục trạng thái filter từ URL
            const urlParams = new URLSearchParams(window.location.search);

            // Khôi phục danh mục
            const selectedCategories = urlParams.getAll('category[]');
            selectedCategories.forEach(categoryId => {
                const checkbox = document.querySelector(`input[name="category[]"][value="${categoryId}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    selectedFilters.categories.push(checkbox.nextElementSibling.textContent);
                }
            });

            // Khôi phục size
            const selectedSize = urlParams.get('size');
            if (selectedSize) {
                const sizeOption = document.querySelector(`input[name="size"][value="${selectedSize}"]`);
                if (sizeOption) {
                    sizeOption.checked = true;
                    sizeOption.closest('.filter-option').classList.add('active');
                    selectedFilters.sizes = [sizeOption.nextElementSibling.textContent];
                }
            }

            // Khôi phục giá
            const selectedPrice = urlParams.get('price');
            if (selectedPrice) {
                const priceOption = document.querySelector(`input[name="price"][value="${selectedPrice}"]`);
                if (priceOption) {
                    priceOption.checked = true;
                    priceOption.closest('.price-option').classList.add('active');
                    selectedFilters.price = priceOption.nextElementSibling.textContent;
                }
            }

            // Cập nhật số lượng filter
            updateFilterCount();

            // Xử lý lọc danh mục
            const categoryCheckboxes = document.querySelectorAll('.category-options input[type="checkbox"]');
            categoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const category = this.nextElementSibling.textContent;

                    if (this.checked) {
                        if (!selectedFilters.categories.includes(category)) {
                            selectedFilters.categories.push(category);
                        }
                    } else {
                        selectedFilters.categories = selectedFilters.categories.filter(item => item !== category);
                    }

                    updateFilterCount();
                });
            });

            // Xử lý lọc size
            const sizeOptions = document.querySelectorAll('.filter-options .filter-option');
            sizeOptions.forEach(option => {
                option.addEventListener('click', function (e) {
                    if (e.target.tagName === 'INPUT') return;

                    sizeOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');

                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Cập nhật selectedFilters
                    selectedFilters.sizes = [this.querySelector('label').textContent];
                    updateFilterCount();
                });
            });

            // Xử lý lọc giá
            const priceOptions = document.querySelectorAll('.price-options .price-option');
            priceOptions.forEach(option => {
                option.addEventListener('click', function (e) {
                    if (e.target.tagName === 'INPUT') return;

                    priceOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');

                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Cập nhật selectedFilters
                    selectedFilters.price = this.querySelector('label').textContent;
                    updateFilterCount();
                });
            });
        });
    </script>
@endsection
