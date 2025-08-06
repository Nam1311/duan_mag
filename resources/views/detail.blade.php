@extends('app')

@section('body')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="grid wide container">
        <div class="row">
            <div class="col l-1 c-12 order-1">
                <div class="detail-thumbnails">
                    {{-- detail img --}}
                    @foreach ($product_detail->images as $image)
                        <div class="detail-itemimg activedeiatl">
                            <img src="{{ asset($image->path) }}" alt="Thumbnail">
                        </div>
                    @endforeach
                    {{-- --}}
                </div>
            </div>

            <div class="col l-5 c-12 order-2">
                <div class="detail-imgall" id="sliderdeital">
                    <button class="prev-btndeital"><i class="fa-solid fa-chevron-left"></i></button>
                    {{-- detail img --}}
                    @foreach ($product_detail->images as $index => $image)
                        <img src="{{ asset($image->path) }}" alt="Image {{ $index + 1 }}"
                            class="{{ $index == 0 ? 'activedeiatl' : '' }}">
                    @endforeach
                    {{-- --}}
                    <button class="next-btndeital"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="col l-5 c-12 order-3">
                <div class="detail-textall">
                    <!-- Wishlist button -->
                    <a href="{{ route('wishlist.add', $product_detail->id) }}" class="wishlist-button">
                        <i class="fa fa-heart"></i> </a>
                    {{-- name --}}
                    <h2>{{ $product_detail->name }}</h2>
                    {{-- --}}
                    {{-- sku variants --}}
                    <div id="sku-info"></div>
                    {{-- --}}
                    <hr>
                    <p class="deital-star">
                        @php
                            $totalRating = $reviewDetail->sum('rating'); // Tổng số sao
                            $totalReview = $reviewDetail->count(); // Tổng số lượt đánh giá
                            $averageRating = $totalReview > 0 ? round($totalRating / $totalReview, 1) : 0; // Trung bình (làm tròn 1 số thập phân)
                            $starsToShow = floor($averageRating); // Số sao nguyên để hiển thị
                        @endphp

                        {{-- Hiển thị số sao --}}
                        @for ($i = 0; $i < $starsToShow; $i++)
                            <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                        @endfor

                        {{-- Nếu muốn nửa sao cho đẹp: --}}
                        @if ($averageRating - $starsToShow >= 0.5)
                            <i class="fa-solid fa-star-half-stroke" style="color: #FFD43B;"></i>
                        @endif

                        {{-- Text thống kê --}}
                        <span>{{ $averageRating }} ({{ $totalReview }} đánh giá)</span>

                    </p>

                    <div class="price-container">
                        <div class="current-price">
                            {{ number_format($product_detail->original_price * (1 - $product_detail->sale / 100), 0, ',', '.') }}đ
                        </div>
                        <div class="original-price">{{ number_format($product_detail->original_price) }}đ</div>
                        <div class="discount-badge">{{ $product_detail->sale }}%</div>
                    </div>
                    <div class="detail-button-mua" style="margin-bottom: 15px">
                        <button class="add-button-detail" id="btnAddCart">
                            <i class="fas fa-shopping-cart"></i> THÊM GIỎ HÀNG
                        </button>
                        <button class="buy-button-detail" id="btnAddCheckout">
                            <i class="fas fa-bolt"></i> MUA
                        </button>

                        {{-- Place to store variant id --}}
                        <input type="hidden" id="product_variant_id" name="product_variant_id" value="">
                        {{-- quantity input exists --}}
                        <input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
                    </div>

                    {{-- giới thiệu sản phẩm --}}
                    {!! $product_detail->short_description !!}
                    {{-- --}}
                    <div class="option-title" id="selected-iconhinhanh">Màu sắc: Chọn màu</div>
                    {{-- color --}}
                    <div class="option-container">
                        @foreach ($colors as $color)
                            <h1></h1>
                            <div class="detail-textall-imgicon" style="background-color: {{ $color->hex_code  }};"
                                id="iconhinhanh{{ $color->index }}">
                                <p hidden>{{ $color->name }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="option-title" id="selected-icon">Kích thước: Chọn size</div>
                    <div class="option-container">
                        {{-- size --}}
                        @foreach ($sizes as $size)
                            <div class="detail-textall-sizeicon " id="icondetail1">
                                <p>{{ $size->name }}</p>
                            </div>
                        @endforeach
                        {{-- --}}
                    </div>

                    <!-- Nơi hiển thị số lượng còn hàng -->
                    <div id="stock-info" style="margin-top: 15px; font-weight: bold; color: #333;">
                        Vui lòng chọn màu và kích thước
                    </div>

                    <!-- input product id ẩn -->
                    <input type="hidden" id="product-id" value="{{ $product_detail->id }}">



                    <a class="size-guide-link" href="#" id="openBox">
                        <i class="fas fa-ruler"></i> Hướng dẫn chọn size
                    </a>

                    <div id="overlay"></div>

                    <div id="popupBox">
                        <img src="{{ asset('img/sidetun.jpg') }}" alt="Hướng dẫn chọn size">
                        <button class="close-btn-size" id="closeBox">Đóng</button>
                    </div>

                    <div class="quantity-section">
                        <label class="quantity-label">Số lượng</label>
                        <div class="quantity-control">
                            <div class="quantity-buttons">
                                <button type="button" id="decrease">-</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" />
                                <button type="button" id="increase">+</button>

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>


    </div>


    <div class="grid wide container">
        <div class="row">
            <div class="col l-12 m-10 c-12 khoangcach">
                <div class="deitel-mota">
                    <button id="detail-sp" class="active" onclick="changeContent(1)">MÔ TẢ SẢN PHẨM</button>
                    <button id="detail-bl" onclick="changeContent(2)">ĐÁNH GIÁ SẢN PHẨM</button>
                </div>

                <div id="box-detail-sp" class="box-chuyendoi1" style="display: block;">

                    {!! $product_detail->description !!}

                </div>

                <div id="box-detail-bl" class="box-chuyendoi1" style="display: none;">
                    <div class="box-danhgia">
                        <h3>{{ $product_detail->name }}</h3>

                        <!-- Review 1 -->
                        @foreach ($reviewDetail as $review)
                            <div class="danhgia-sp">
                                <h4>{{ $review->user->name }}</h4>

                                <div class="star-rating">
                                    {{-- Hiển thị số sao --}}
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                    {{-- Nội dung mô tả đánh giá --}}
                                    @php
                                        $ratingText = match ($review->rating) {
                                            5, 4 => 'Rất hài lòng',
                                            3 => 'Khá ổn',
                                            1, 2 => 'Không hài lòng',
                                            default => 'Không xác định',
                                        };
                                    @endphp
                                    <span class="rating-text">{{ $review->rating }}/5 - {{ $ratingText }}</span>
                                </div>
                                <p><strong>Nhận xét:</strong> {{ $review->comment }}</p>
                                <p class="review-date">Đánh giá vào {{ $review->created_at }}</p>
                            </div>
                        @endforeach


                        <!-- Review Form (Optional) -->
                        <div class="danhgia-sp review-form">
                            <h4>Viết đánh giá của bạn</h4>
                            <form>
                                <div class="form-group">
                                    <label>Đánh giá của bạn:</label>
                                    <div class="rating-input">
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." rows="4"></textarea>
                                </div>
                                <button type="submit" class="submit-review">Gửi đánh giá</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="grid wide container">
        <div class="row">
            <div class="col l-12 m-6 c-12 ">
                <div class="text-sp-lq">
                    <h2>Sản phẩm Liên quan</h2>
                    <hr>
                </div>
            </div>
        </div>
    </div>
    <section class="product-thun">
        <div class="grid wide container">
            <div class="row">
                {{-- sản phẩm liên quan --}}
                @foreach ($products as $product)
                    <div class="col l-3 m-6 c-6 ">
                        <div class="item">
                            <div class="item-img">
                                <span class="item-giam">-{{ $product->sale }}%</span>
                                <div class="item-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                                <a href="{{ asset('/detail/' . $product->id) }}">
                                    <img src="{{ asset($product->images->first()->path) }}" alt="">
                                </a>
                            </div>
                            <div class="item-name">
                                <h3><a href="{{ asset('/detail/' . $product->id) }}">
                                        {{ $product->name }}
                                    </a></h3>
                            </div>
                            <div class="item-price">
                                <span style="color: red;padding-right: 10px;">
                                    {{ number_format($product->price * (1 - $product->sale / 100), 0, ',', '.') }}đ</span>
                                <span><del>{{ number_format($product->price, 0, ',', '.') }}đ</del></span>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!--  -->
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        window.routes = {
            addToCart: "{{ route('cart.add') }}"
        };

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
    <script src="{{ asset('/js/detail.js') }}"></script>
@endsection
{{--
<script src="{{ asset('/js/detail.js') }}"></script> --}}
{{--
<script src="/js/detail.js"></script>


@endsection --}}