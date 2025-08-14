@extends('app')

@section('body')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        .favourite-container {
            padding: 0px;
            max-width: 1200px;
            margin: auto;
            color: #333;
            font-family: 'Poppins', sans-serif;

        }

        .favourite-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            margin-top: 100px;
        }

        .btn-clear-all {
            color: red;
            text-decoration: none;
            font-weight: 500;
            border: 1px solid red;
            padding: 8px 16px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-clear-all:hover {
            background-color: red;
            color: #fff;
        }

        .favourite-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        @media (max-width: 992px) {
            .favourite-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .favourite-grid {
                grid-template-columns: 1fr;
            }
        }

        .favourite-item {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
            position: relative;
            color: #333;
        }

        .favourite-item:hover {
            transform: translateY(-6px);
        }

        .favourite-img-wrapper {
            position: relative;
            overflow: hidden;
            height: 300px;
        }

        .favourite-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .btn-remove {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 0, 0, 0.85);
            color: white;
            padding: 7px 10px;
            border-radius: 50%;
            font-size: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .favourite-img-wrapper:hover .btn-remove {
            opacity: 1;
        }

        .favourite-info {
            padding: 18px;
        }

        .favourite-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.5;
            letter-spacing: 0.3px;
            min-height: 48px;
            overflow: hidden;
            color: #222;
            text-decoration: none;
            display: block;
            transition: color 0.2s ease;
        }

        .favourite-name:hover {
            color: #e53935;
            text-decoration: none;
        }

        .favourite-price {
            color: #111;
            font-weight: 500;
            font-size: 16px;
            padding-top: 6px;
            text-decoration: none;
            letter-spacing: 0.3px;
        }

        .no-product-msg {
            text-align: center;
            font-size: 16px;
            color: #666;
            margin-top: 20px;
        }
    </style>
    <div class="favourite-container">
        <div class="favourite-header">
            <h2>Sản phẩm yêu thích</h2>
            <a href="{{ route('wishlist.clear') }}" class="btn-clear-all">Xóa tất cả</a>
        </div>

        <div class="favourite-grid">
            @forelse ($wishlistItems as $product)
                <div class="favourite-item">
                    <div class="favourite-img-wrapper">
                        <a href="{{ asset('/detail/' . $product->id) }}">
                            <img src="{{ $product->thumbnail->path ?? '/default.png' }}" alt="">
                        </a>
                        <a href="{{ route('wishlist.remove', $product->id) }}" class="btn-remove">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <div class="favourite-info">
                        <a href="{{ asset('/detail/' . $product->id) }}" class="favourite-name">{{ $product->name }}</a>
                        <div class="favourite-price">{{ number_format($product->price) }}đ</div>
                    </div>
                </div>
            @empty
                <p class="no-product-msg">Không có sản phẩm yêu thích nào.</p>
            @endforelse
        </div>
    </div>




    <script src="{{ asset('main.js') }}"></script>
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




        document.addEventListener('DOMContentLoaded', function() {
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
                mobileFilterToggle.addEventListener('click', function() {
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

            // Xử lý lọc danh mục
            const categoryCheckboxes = document.querySelectorAll('.category-options input[type="checkbox"]');
            categoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const category = this.nextElementSibling.textContent;

                    if (this.checked) {
                        if (!selectedFilters.categories.includes(category)) {
                            selectedFilters.categories.push(category);
                        }
                    } else {
                        selectedFilters.categories = selectedFilters.categories.filter(item =>
                            item !== category);
                    }

                    updateFilterCount();
                });
            });

            // Xử lý lọc size (giữ nguyên từ trước)
            const sizeOptions = document.querySelectorAll('.filter-options .filter-option');
            sizeOptions.forEach(option => {
                option.addEventListener('click', function(e) {
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

            // Xử lý lọc màu sắc (giữ nguyên từ trước)
            const colorOptions = document.querySelectorAll('.color-options .color-option');
            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    colorOptions.forEach(color => color.classList.remove('selected'));
                    this.classList.add('selected');

                    // Cập nhật selectedFilters
                    selectedFilters.colors = [this.getAttribute('title')];
                    updateFilterCount();
                });
            });

            // Xử lý lọc giá (giữ nguyên từ trước)
            const priceOptions = document.querySelectorAll('.price-options .price-option');
            priceOptions.forEach(option => {
                option.addEventListener('click', function(e) {
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

            // Xử lý nút áp dụng bộ lọc
            const filterButtons = document.querySelectorAll('.filter-button');
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Thu gọn mobile filter nếu đang mở
                    if (mobileFilterContent.classList.contains('active')) {
                        mobileFilterContent.classList.remove('active');
                        mobileFilterToggle.querySelector('.fa-chevron-down').classList.remove(
                            'fa-rotate-180');
                    }

                    // Gửi dữ liệu filter đi (có thể là AJAX hoặc filter client-side)
                    console.log('Filters applied:', selectedFilters);

                    // Hiển thị thông báo
                    alert(`Đã áp dụng bộ lọc:
                                    Danh mục: ${selectedFilters.categories.join(', ') || 'Tất cả'}
                                    Size: ${selectedFilters.sizes.join(', ') || 'Tất cả'}
                                    Màu: ${selectedFilters.colors.join(', ') || 'Tất cả'}
                                    Giá: ${selectedFilters.price || 'Tất cả'}`);

                    // Ở đây bạn có thể thêm code để thực sự lọc sản phẩm
                    // filterProducts(selectedFilters);
                });
            });

            // Khởi tạo filter count
            updateFilterCount();
        });
    </script>
@endsection
