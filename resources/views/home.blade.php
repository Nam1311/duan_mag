@extends('app')

@section('body')
    <div class="index-slider-container" id="slider">
        <div class="index-progress-bar"></div>
        <div class="index-slider-track-container">
            <div class="index-slider-track">
                @foreach ($sliders as $slider )
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


    {{-- bắt đầu slider --}}

    {{-- kết thúc slider --}}

    <div class="introduce">
        <p class="tieude">Enjoy Your Youth!</p>
        <p class="introduce-test">Không chỉ là thời trang, M A G còn là “phòng thí nghiệm”
            của tuổi trẻ - nơi nghiên cứu và cho ra đời nguồn năng lượng
            mang tên “Youth”. Chúng mình luôn muốn tạo nên những trải
            nghiệm vui vẻ, năng động và trẻ trung.
        </p>
    </div>
    <section class="product-sale" style="margin-bottom: 10px">
        <div class="header-product-sale">
            <div>
                <h2 class="section-title">Flash Sale mỗi ngày</h2>
                <img src="{{ asset('/img/sale.webp') }}" alt="">
            </div>

            <div class="count-down">
                <p id="countdown-start-label" style="color: green; display: none;">Bắt đầu sau:</p>
                <p id="countdown-label" style="color: red;">Kết thúc sau:</p>
                <p id="flash-sale-start" style="display: none; color: green;">Flash Sale bắt đầu lúc 8h hàng ngày</p>

                <div class="box-time">
                    <div class="time time-hour" id="countdown-hour">00</div>
                    <div class="time-bottom">Giờ</div>
                </div>
                <div class="box-time">
                    <div class="time time-minute" id="countdown-minute">00</div>
                    <div class="time-bottom">Phút</div>
                </div>
                <div class="box-time">
                    <div class="time time-second" id="countdown-second">00</div>
                    <div class="time-bottom">Giây</div>
                </div>
            </div>

            <div class="see-more-sale"
                style="position: absolute; display: flex; align-items: center; gap: 5px; right: 11%; margin-top: 189px;">
                <a class="see-all" href="#" style="color: black; text-decoration: none;">Xem tất cả</a>
                <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </div>
        </div>

        <div class="product-sale-box">
            <div class="product-sale-banner">
                <img src="{{ asset('/img/Ảnh chụp màn hình 2025-05-24 230355.png') }}" alt="">
            </div>

            <ul class="row product-list-sale">
                @forelse ($flash_sale_products as $product)
                    <li class="item" style="background-color: white; border-radius: 7px;">
                        <div class="item-img">
                            <span class="item-giam">-{{ $product->sale }}%</span>
                            <div class="item-icon" id="addToCartBtn"><i class="fa-solid fa-cart-shopping"></i></div>
                            <a href="{{ asset('/detail/' . $product->id) }}">
                                <img src="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}" alt="">
                            </a>
                            <span class="item-flash-sale" style="">Flash sale!!</span>
                            <div class="item-buy-now">
                                <a class="a-buy-now" href="">Mua ngay</a>
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
                            <span class="item-giam">-{{ $product->sale }}%</span>
                            <div class="item-icon" id="addToCartBtn"><i class="fa-solid fa-cart-shopping"></i></div>
                            <a href="{{ asset('/detail/' . $product->id) }}">
                                <img src="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}" alt="">
                            </a>
                            <div class="item-buy-now">
                                <a class="a-buy-now" href="">Mua ngay</a>
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

        <div class="pruduct-xemthem see-more-mobile" style="display: none; margin-left: 36%; margin-top: 10px;">
            <div style="display: flex; align-items: center; gap: 5px;">
                <a class="see-all" href="#" style="color: black; text-decoration: none;">Xem tất cả</a>
                <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </div>
        </div>
    </section>
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




    <!-- load danh muc -->
    <section class="section-cat" style="padding-bottom: 10px; background-color: white; position: relative; z-index: 10;">
        <div class=" grid wide container">
            <h2 class="section-title" style="margin-bottom: 10px;">Danh mục</h2>
            <ul class="list-cat">
                @foreach ($product_categories as $product_categories)
                    <li class="item-category">
                        <img class="category-img" src="{{ asset('img/categories/' . $product_categories->image) }}" alt="">
                        <div class="detail-cat">
                            <h2 class="category-name">{{$product_categories->name}}</h2>
                            <a href="/products?category%5B%5D={{$product_categories->id}}"><button>Xem ngay</button></a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
     </section>

    {{-- ds sp mới --}}
    <section class="new-design">
        <div class="grid wide container">
            <div style="padding: 0px 0px;">
                <h2 class="section-title" style="text-align: center">Sản phẩm mới</h2>

            </div>
            <div class="tab-header" >
                {{-- load hết danh mục ra đây --}}
                <ul class="tabs" style="justify-content: center">

                    @foreach ($product_new as $index => $category)
                        <li class="tab {{ $index == 0 ? 'active' : '' }}" data-tab="tab{{ $loop->iteration }}">
                            {{$category->name}}
                        </li>
                    @endforeach
                </ul>
                {{-- <hr style="max-width: 60% auto"> --}}
            </div>

            <div class="tab-content">
                @foreach ($product_new as $index => $category)
                <div id="tab{{ $loop->iteration }}" class="tab-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="breard" style="display: flex; justify-content:space-between; align-item: center; padding: 20px 0;">
                        <h3 style="text-align: center;">Các thiết kế mới được M A G cập nhật liên tục và đa dạng mẫu mã</h3>
                        <a class="see-all" href="/products?category[]={{ $category->id }}" style="color: black; text-decoration: none;">
                            Xem tất cả <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                    {{-- load dssp --}}
                    <div class="row product-list-n-d">
                        @forelse ($category->products as $product)
                            <div class="col l-3 m-6 c-6">
                                <div class="item">
                                <div class="item-img">
                                    <span class="item-giam">-{{ $product->sale }}%</span>
                                    <div class="item-icon">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>

                                    <a href="{{asset('/detail/'. $product->id)}}">
                                        <img src="{{ asset($product->images->first()->path) }}" alt="{{ $product->name }}">
                                    </a>
                                    <div class="item-buy-now">
                                        <a class="a-buy-now" href="">Mua ngay</a>
                                    </div>
                                </div>
                                <div class="item-name">
                                    <h3>
                                        <a href="{{asset('/detail/'. $product->id)}}">
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

     <!-- san pham bán chạy -->
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
                @foreach ($products_bestseller as $products_bestseller)
                    <div class="col l-3 m-6 c-6 ">
                        <div class="item">
                            <div class="item-img">
                                <span class="item-giam">-{{ $products_bestseller->sale }}%</span>
                                <div class="item-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>

                                <a href="{{asset('/detail/' . $products_bestseller->id)}}">
                                    <img src="{{ asset($products_bestseller->images->first()->path) }}"
                                        alt="{{ $products_bestseller->name }}">
                                </a>
                                <div class="item-buy-now" id="btnAddCheckout">
                                    <a class="a-buy-now" id="btnAddCheckout" href="">Mua ngay</a>
                                </div>
                            </div>
                            <div class="item-name">
                                <h3>
                                    <a href="{{asset('/detail/' . $products_bestseller->id)}}">
                                        {{ $products_bestseller->name }}
                                    </a>
                                </h3>
                            </div>
                            <div class="item-price">
                                <span style="color: red;padding-right: 10px;">
                                    {{ number_format($products_bestseller->price , 0, ',', '.') }}đ
                                </span>
                                <span><del>{{ number_format($products_bestseller->original_price, 0, ',', '.') }}đ</del></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <!-- san pham nổi bật -->
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
                @foreach ($products_is_featured as $products_is_featured)
                    <div class="col l-3 m-6 c-6 ">
                        <div class="item">
                            <div class="item-img">
                                <span class="item-giam">-{{ $products_is_featured->sale }}%</span>
                                <div class="item-icon">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>

                                <a href="{{asset('/detail/' . $products_is_featured->id)}}">
                                    <img src="{{ asset($products_is_featured->images->first()->path) }}"
                                        alt="{{ $products_is_featured->name }}">
                                </a>
                                <span class="item-view" style="">{{$products_is_featured->views}} <i class="fa fa-eye" aria-hidden="true"></i></span>
                                <div class="item-buy-now">
                                    <a class="a-buy-now" href="">Mua ngay</a>
                                </div>
                            </div>
                            <div class="item-name">
                                <h3>
                                    <a href="{{asset('/detail/' . $products_is_featured->id)}}">
                                        {{ $products_is_featured->name }}
                                    </a>
                                </h3>
                            </div>
                            <div class="item-price">
                                <span style="color: red;padding-right: 10px;">
                                    {{ number_format($products_is_featured->price , 0, ',', '.') }}đ
                                </span>
                                <span><del>{{ number_format($products_is_featured->original_price, 0, ',', '.') }}đ</del></span>
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
                            <button>Xem thêm</button>
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
                                <input class="input-email" style="width: 300px; height: 41px; padding: 10px; border: none;" type="text" placeholder="Nhập email nhận ưu đãi ">
                                <button class="btn-log">Gửi</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid wide container post-container"
        style="background-color: white; position: relative; z-index: 99; top: 0px">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%">
            <h2 style="font-size: 35px; font-weight: normal; padding: 20px 0px;">Tin tức</h2>
            <div style="display: flex;align-items: center;gap: 5px; margin-top: 18px;">
                <a class="see-all" href="" style="color: black; text-decoration: none;">Xem tất cả</a><i
                    class="fa fa-arrow-right" aria-hidden="true"></i>
            </div>
        </div>

        <div class="row">
            @foreach ($news as $news)
                <div class="col l-4 m-6 c-12">
                    <div class="post-item">
                        <div class="post-img">
                            <img src="{{asset('/img/' . $news->image)}}" alt="">
                        </div>
                        <div class="post-time">
                            {{ \Carbon\Carbon::parse($news->posted_date)->format('d/m/Y') }}
                        </div>
                        <div class="post-name">
                            <h2>{{$news->title}}</h2>
                        </div>
                        <div class="post-content">
                            <p>{{$news->description}}</p>
                        </div>
                        <button>Đọc tiếp <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </div>
            @endforeach


        </div>
    </div>


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

@endsection
