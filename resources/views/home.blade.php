@extends('app')

@section('body')
    <div class="index-slider-container" id="slider">
        <div class="index-progress-bar"></div>
        <div class="index-slider-track-container">
            <div class="index-slider-track">
                @foreach ($sliders as $slider)
                    <div class="index-slide active" style="background-image: url('{{asset($slider->image)}}');">
                        <div class="index-slide-overlay"></div>
                        <div class="index-slide-content">
                            <span class="season-tag"></span>
                            <h1 class="index-slide-title">{{$slider->title}}</h1>
                            <p class="index-slide-description">{{$slider->description}}</p>
                            <a href="{{$slider->link}}" class="shop-btn">{{$slider->cta_text}}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button class="arrow-btn prev-btn">&#10094;</button>
        <button class="arrow-btn next-btn">&#10095;</button>

        <div class="index-slider-nav">
            <button class="nav-dot active"></button>
            <button class="nav-dot"></button>
            <button class="nav-dot"></button>
        </div>
    </div>

    <div class="introduce">
        <p class="tieude">Enjoy Your Youth!</p>
        <p class="introduce-test">Không chỉ là thời trang, M A G còn là “phòng thí nghiệm”
            của tuổi trẻ - nơi nghiên cứu và cho ra đời nguồn năng lượng
            mang tên “Youth”. Chúng mình luôn muốn tạo nên những trải
            nghiệm vui vẻ, năng động và trẻ trung.
        </p>
    </div>

    {{-- countdown sp --}}
    <section class="product-sale" style="margin-bottom: 10px">
        <div class="header-product-sale">
            <div>
                <h2 class="section-title">Flash Sale mỗi ngày</h2>
                <img src="{{ asset('/img/sale.webp') }}" alt="">
            </div>

            <div class="count-down">
                <p id="countdown-label" style="color: red;">Kết thúc sau:</p>
                <p id="flash-sale-start" style="display: none; color: green;">Flash Sale bắt đầu lúc 8h hàng ngày</p>

                <div class="box-time ">
                    <div class="time time-hour" id="countdown-hour">{{ $countdown['hours'] }}</div>
                    <div class="time-bottom">Giờ</div>
                </div>
                <div class="box-time">
                    <div class="time time-minute" id="countdown-minute">{{ $countdown['minutes'] }}</div>
                    <div class="time-bottom">Phút</div>
                </div>
                <div class="box-time">
                    <div class="time time-second" id="countdown-second">{{ $countdown['seconds'] }}</div>
                    <div class="time-bottom">Giây</div>
                </div>
            </div>
        </div>

        <div class="product-sale-box">
            <div class="product-sale-banner">
                <img src="{{ asset('/img/banner-sale.png') }}" alt="">
            </div>

            <div class="product-lists">
                <ul class="product-list-sale">
                    @forelse ($flash_sale_products as $product)
                        <li class="item" style="background-color: white; border-radius: 7px;">
                            <div class="item-img">
                                <span class="item-giam">-{{ $product->sale }}%</span>
                                <div class="item-icon" id="addToCartBtn"><i class="fa-solid fa-cart-shopping"></i></div>
                                <a href="{{ asset('/detail/' . $product->id) }}">
                                    <img src="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}" alt="">
                                </a>
                                <span class="item-flash-sale" style="">Flash sale!!</span>
                                <div class="item-select-variant">
                                    <a class="a-buy-now"
                                       data-id="{{ $product->id }}"
                                       data-name="{{ $product->name }}"
                                       data-price="{{ $product->price }}"
                                       data-original-price="{{ $product->original_price }}"
                                       data-image="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}"
                                       href="javascript:void(0)">Mua ngay</a>
                                </div>
                            </div>
                            <div class="item-name item-name-sale">
                                <h3>
                                    <a href="{{ asset('/detail/' . $product->id) }}">{{ $product->name }}</a>
                                </h3>
                            </div>
                            <div class="item-price item-price-sales">
                                <span style="color: red; padding-right: 10px;">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </span>
                                <span><del>{{ number_format($product->original_price, 0, ',', '.') }}đ</del></span>
                            </div>
                        </li>
                    @empty
                    @foreach ($products_sale as $product )
                        <li class="item" style="background-color: white; border-radius: 7px;">
                            <div class="item-img">
                                @if($product->sale > 0)
                                    <span class="item-giam">-{{ $product->sale }}%</span>
                                @endif
                                <div class="item-icon" id="addToCartBtn"><i class="fa-solid fa-cart-shopping"></i></div>
                                <a href="{{ asset('/detail/' . $product->id) }}">
                                    <img src="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}" alt="">
                                </a>
                                <div class="item-select-variant">
                                    <a class="a-buy-now"
                                       data-id="{{ $product->id }}"
                                       data-name="{{ $product->name }}"
                                       data-price="{{ $product->price }}"
                                       data-original-price="{{ $product->original_price }}"
                                       data-image="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}"
                                       href="javascript:void(0)">Mua ngay</a>
                                </div>
                            </div>
                            <div class="item-name item-name-sale">
                                <h3>
                                    <a href="{{ asset('/detail/' . $product->id) }}">{{ $product->name }}</a>
                                </h3>
                            </div>
                            <div class="item-price item-price-sales">
                                <span style="color: red; padding-right: 10px;">
                                    {{ number_format($product->price , 0, ',', '.') }}đ
                                </span>
                                <span><del>{{ number_format($product->original_price, 0, ',', '.') }}đ</del></span>
                            </div>
                        </li>
                    @endforeach
                    @endforelse
                </ul>
            </div>
        </div>
    </section>

    {{-- load danh mục --}}
    <section class="section-cat" style="padding-bottom: 10px; background-color: white; position: relative; z-index: 10;">
        <div class=" grid wide container">
            <h2 class="section-title" style="margin-bottom: 10px;">Danh mục</h2>
            <ul class="list-cat">
                @foreach ($product_categories as $product_categories)
                    <li class="item-category">
                        <img class="category-img" src="{{ asset('img/categories/' . $product_categories->image) }}" alt="">
                        <div class="detail-cat">
                            <h2 class="category-name">{{$product_categories->name}}</h2>
                            <a href="/san-pham?category%5B%5D={{$product_categories->id}}"><button>Xem ngay</button></a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- sp dành cho bạn, đăng nhập mới hiện --}}
    @if (Auth::check() && isset($recommendedProducts) && count($recommendedProducts) > 0)
        <section class="product-recommends">
            <div style="padding: 0px 0px">
                <h2 class="section-title">Sản phẩm dành cho bạn</h2>
            </div>
            <ul class="row product_featured product-recommend">
                @foreach ($recommendedProducts as $product)
                    <li class="item">
                        <div class="item-img">
                            @if($product->sale > 0)
                                <span class="item-giam">-{{ $product->sale }}%</span>
                            @endif
                            <div class="item-icon">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <a href="{{ asset('/detail/' . $product->id) }}">
                                <img src="{{ asset($product->images->first()->path ?? 'images/default.jpg') }}"
                                    alt="{{ $product->name }}">
                            </a>
                            <div class="item-select-variant">
                                <a class="a-buy-now"
                                   data-id="{{ $product->id }}"
                                   data-name="{{ $product->name }}"
                                   data-price="{{ $product->price }}"
                                   data-original-price="{{ $product->original_price }}"
                                   data-image="{{ asset($product->images->first()->path ?? 'images/default.jpg') }}"
                                   href="javascript:void(0)">Mua ngay</a>
                            </div>
                        </div>
                        <div class="item-name">
                            <h3>
                                <a href="{{ asset('/detail/' . $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                        </div>
                        <div class="item-price">
                            <span style="color: red;padding-right: 10px;">
                                {{ number_format($product->price , 0, ',', '.') }}đ
                            </span>
                            <span><del>{{ number_format($product->original_price, 0, ',', '.') }}đ</del></span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- sản phẩm mới --}}
    <section class="new-design">
        <div class="grid wide container">
            <div style="padding: 0px 0px;">
                <h2 class="section-title" style="text-align: center">Sản phẩm mới</h2>
            </div>
            <div class="tab-header">
                <ul class="tabs" style="justify-content: center">
                    @foreach ($product_new as $index => $category)
                        <li class="tab {{ $index == 0 ? 'active' : '' }}" data-tab="tab{{ $loop->iteration }}">
                            {{$category->name}}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-content">
                @foreach ($product_new as $index => $category)
                    <div id="tab{{ $loop->iteration }}" class="tab-item {{ $index == 0 ? 'active' : '' }}">
                        <div class="breard"
                            style="display: flex; justify-content:space-between; align-item: center; padding: 20px 0;">
                            <h3 style="text-align: center;">Các thiết kế mới được M A G cập nhật liên tục và đa dạng mẫu mã</h3>
                            <a class="see-all" href="/products?category[]={{ $category->id }}"
                                style="color: black; text-decoration: none;">
                                Xem tất cả <i class="fa fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="row product-list-n-d">
                            @forelse ($category->products as $product)
                                <div class="col l-3 m-6 c-6">
                                    <div class="item">
                                        <div class="item-img">
                                            @if($product->sale > 0)
                                                <span class="item-giam">-{{ $product->sale }}%</span>
                                            @endif
                                            <div class="item-icon">
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </div>
                                            <a href="{{asset('/detail/' . $product->id)}}">
                                                <img src="{{ asset($product->images->first()->path) }}" alt="{{ $product->name }}">
                                            </a>
                                            <div class="item-select-variant">
                                                <a class="a-buy-now"
                                                   data-id="{{ $product->id }}"
                                                   data-name="{{ $product->name }}"
                                                   data-price="{{ $product->price }}"
                                                   data-original-price="{{ $product->original_price }}"
                                                   data-image="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}"
                                                   href="javascript:void(0)">Mua ngay</a>
                                            </div>
                                        </div>
                                        <div class="item-name">
                                            <h3>
                                                <a href="{{asset('/detail/' . $product->id)}}">
                                                    {{ $product->name }}
                                                </a>
                                            </h3>
                                        </div>
                                        <div class="item-price">
                                            <span style="color: red;padding-right: 10px;">
                                                {{ number_format($product->price, 0, ',', '.') }}đ
                                            </span>
                                            <span><del>{{ number_format($product->original_price, 0, ',', '.') }}đ</del></span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p style="padding: 10px;">Chưa có sản phẩm</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- sản phẩm bán chạy --}}
    <section class="product-new">
        <div style="padding: 0px 7px">
            <h2 class="section-title">Sản phẩm bán chạy</h2>
            <div style="display: flex;align-items: center;gap: 5px; margin-top: 18px;">
                <a class="see-all" href="/productBestseller" style="color: black; text-decoration: none;">Xem tất cả</a><i
                    class="fa fa-arrow-right" aria-hidden="true"></i>
            </div>
        </div>
        <div class="grid wide container">
            <div class="row product_featured">
                @foreach ($products_bestseller as $product)
                    <div class="col l-3 m-6 c-6">
                        <div class="item">
                            <div class="item-img">
                                @if($product->sale > 0)
                                    <span class="item-giam">-{{ $product->sale }}%</span>
                                @endif
                                <div class="item-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                                <a href="{{ asset('/detail/' . $product->id) }}">
                                    <img src="{{ asset($product->images->first()->path) }}" alt="{{ $product->name }}">
                                </a>
                                <div class="item-select-variant">
                                    <a class="a-buy-now"
                                       data-id="{{ $product->id }}"
                                       data-name="{{ $product->name }}"
                                       data-price="{{ $product->price }}"
                                       data-original-price="{{ $product->original_price }}"
                                       data-image="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}"
                                       href="javascript:void(0)">Mua ngay</a>
                                </div>
                            </div>
                            <div class="item-name">
                                <h3>
                                    <a href="{{ asset('/detail/' . $product->id) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                            </div>
                            <div class="item-price">
                                <span style="color: red;padding-right: 10px;">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </span>
                                <span><del>{{ number_format($product->original_price, 0, ',', '.') }}đ</del></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- sản phẩm nổi bật --}}
    <section class="product-new">
        <div style="padding: 0px 7px">
            <h2 class="section-title">Sản phẩm nổi bật</h2>
            <div style="display: flex;align-items: center;gap: 5px; margin-top: 18px;">
                <a class="see-all" href="/productFeatured" style="color: black; text-decoration: none;">Xem tất cả</a><i
                    class="fa fa-arrow-right" aria-hidden="true"></i>
            </div>
        </div>
        <div class="grid wide container">
            <div class="row product_featured">
                @foreach ($products_is_featured as $product)
                    <div class="col l-3 m-6 c-6 ">
                        <div class="item">
                            <div class="item-img">
                                @if($product->sale > 0)
                                    <span class="item-giam">-{{ $product->sale }}%</span>
                                @endif
                                <div class="item-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                                <a href="{{asset('/detail/' . $product->id)}}">
                                    <img src="{{ asset($product->images->first()->path) }}"
                                        alt="{{ $product->name }}">
                                </a>
                                <span class="item-view" style="">{{$product->views}} <i class="fa fa-eye"
                                        aria-hidden="true"></i></span>
                                <div class="item-select-variant">
                                    <a class="a-buy-now"
                                       data-id="{{ $product->id }}"
                                       data-name="{{ $product->name }}"
                                       data-price="{{ $product->price }}"
                                       data-original-price="{{ $product->original_price }}"
                                       data-image="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}"
                                       href="javascript:void(0)">Mua ngay</a>
                                </div>
                            </div>
                            <div class="item-name">
                                <h3>
                                    <a href="{{asset('/detail/' . $product->id)}}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                            </div>
                            <div class="item-price">
                                <span style="color: red;padding-right: 10px;">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </span>
                                <span><del>{{ number_format($product->original_price, 0, ',', '.') }}đ</del></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- giới thiệu --}}
    <section class="about-mag">
        <div class="grid wide container">
            <div class="row">
                <div class="col l-12 m-12 c-12">
                    <div>
                        <h2 class="section-title-ab">Về chúng tôi</h2>
                    </div>
                    <div class="about-content">
                        <div class="about-img">
                            <img class="about-image" src="{{asset('/img/slider_2.webp')}}" alt="">
                        </div>
                        <div class="about-text">
                            <h2>Giới thiệu shop M A G</h2>
                            <p>Chúng tôi là một cửa hàng chuyên cung cấp các sản phẩm chất lượng cao với giá cả hợp lý. Với
                                hơn 10 năm kinh nghiệm trong ngành, chúng tôi tự hào mang đến cho khách hàng những trải
                                nghiệm mua sắm tốt nhất.</p>
                            <p>Đội ngũ nhân viên chuyên nghiệp, tận tâm luôn sẵn sàng hỗ trợ khách hàng 24/7. Chúng tôi cam
                                kết chỉ bán những sản phẩm chính hãng, có nguồn gốc xuất xứ rõ ràng.</p>
                            <p>Hãy đến với chúng tôi để trải nghiệm dịch vụ tốt nhất và những ưu đãi hấp dẫn!</p>
                            <a href="/about"><button>Xem thêm</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid wide container">
        <div class="row">
            <div class="col l-12 m-12 c-12">
                <div>
                    <h2 class="section-title-ab" style="margin-top: 30px">Đăng ký nhận ưu đãi</h2>
                </div>
                <div class="about-us" style="background-image: url('{{asset('img/slider_2.webp')}}');">
                    <div class="box-log">
                        <div class="text-content">
                            <h3 class="text">Trở thành thành viên của M A G ngay hôm nay !!</h3>
                            <h2 class="text">Tận hưởng ưu đãi mua sắm hằng ngày</h2>
                            <div style="display: flex; align-item:center; justify-content: center; gap: 10px  ">
                                <input class="input-email" style="width: 300px; height: 41px; padding: 10px; border: none;"
                                    type="text" placeholder="Nhập email nhận ưu đãi ">
                                <button class="btn-log">Gửi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- tin tức --}}
    <div class="grid wide container post-container"
        style="background-color: white; position: relative; z-index: 99; top: 0px">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%">
            <h2 style="font-size: 35px; font-weight: normal; padding: 20px 0px;">Tin tức</h2>
            <div style="display: flex;align-items: center;gap: 5px; margin-top: 18px;">
                <a class="see-all" href="/news" style="color: black; text-decoration: none;">Xem tất cả</a><i
                    class="fa fa-arrow-right" aria-hidden="true"></i>
            </div>
        </div>
        <div class="row">
            @foreach ($newhome as $newhome)
                <div class="col l-4 m-6 c-12">
                    <div class="post-item">
                        <div class="post-img">
                            <img src="{{asset('/img/' . $newhome->image)}}" alt="">
                        </div>
                        <div class="post-time">
                            {{ \Carbon\Carbon::parse($newhome->posted_date)->format('d/m/Y') }}
                        </div>
                        <div class="post-name">
                            <h2>{{$newhome->title}}</h2>
                        </div>
                        <div class="post-content">
                            <p>{{$newhome->description}}</p>
                        </div>
                        <a href="new_detail/{{ $newhome->id }}"><button>Đọc tiếp <i class="fa fa-arrow-right" aria-hidden="true"></i></button></a>

                    </div>
                </div>
            @endforeach
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

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    {{-- kieerm tra flash sale --}}
    <script>
        function handleCountdownAuto() {
            fetch("{{ route('ajax.kiem_tra_flashsale') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.reload_page) {
                        location.reload();
                    }
                });
        }

        handleCountdownAuto();
        setInterval(handleCountdownAuto, 60000);
    </script>

    {{-- theem gio hang, mua ngay --}}
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
    <script src="{{ asset('/js/detail.js') }}"></script>
@endsection
