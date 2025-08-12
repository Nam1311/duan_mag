@extends('app')

@section('body')
    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif
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
                    <style>
                        .detail-try-on{
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            height: 50.4px;
                            background-color: black;
                            box-shadow: 0 2px 8px rgba(188, 19, 188, 0.6);
                            cursor: pointer;
                            margin-bottom: 5px;
                        }
                        .detail-try-on>a{
                            color: white;
                        }
                        .detail-try-on>a>i{
                            color: white;
                            font-size: 16px;
                        }
                        .detail-try-on>p{
                            color: white;
                            font-size: 12px;
                        }
                    </style>
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
                    <div class="detail-try-on">
                        <a href="/try-on"><i class="fa fa-asterisk" aria-hidden="true"></i>   Thử ngay</a>
                        <p>Phòng thử đồ online</p>
                    </div>

                    {{-- giới thiệu sản phẩm --}}
                    {!! $product_detail->short_description !!}
                    {{-- --}}
                    <div class="option-title" id="selected-iconhinhanh">Màu sắc: Chọn màu</div>
                    {{-- color --}}
                    <div class="option-container">
                        @foreach ($colors as $color)
                            <h1></h1>
                            <div class="detail-textall-imgicon" style="background-color: {{ $color->hex_code }};"
                                id="iconhinhanh{{ $color->index }}">
                                <p hidden>{{ $color->name ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="option-title" id="selected-icon">Kích thước: Chọn size</div>
                    <div class="option-container">
                        {{-- size --}}
                        @foreach ($sizes as $size)
                            <div class="detail-textall-sizeicon " id="icondetail1">
                                <p>{{ $size->name ?? '' }}</p>
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
                {{--  --}}

<div id="box-detail-bl" class="box-chuyendoi1" style="display: none;">
    @foreach ($reviewDetail as $review)
        <!-- Main Review Header -->
        <div class="detaill-review-header" id="review-{{ $review->id }}">
            <div class="detaill-user-avatar">
                <span>{{ strtoupper(mb_substr($review->user->name, 0, 1, 'UTF-8')) }}</span>
            </div>
            <div class="detaill-user-info">
                <h4 class="detaill-username">{{ $review->user->name }}</h4>
                <div class="detaill-review-meta">
                    <div class="detaill-rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($review->rating))
                                <i class="fas fa-star detaill-active"></i>
                            @elseif ($i == ceil($review->rating) && $review->rating - floor($review->rating) >= 0.5)
                                <i class="fas fa-star-half-alt detaill-active"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                        <span class="detaill-rating-value">{{ $review->rating }}</span>
                    </div>
                    <span class="detaill-review-date">
                        Đánh giá ngày {{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Review Content -->
        <div class="detaill-review-content">
            <p class="detaill-review-text">{{ $review->comment }}</p>
        </div>

        <!-- Reply Button -->
        <button class="detaill-action-btn detaill-reply-btn" data-type="review" data-id="{{ $review->id }}">
            <i class="far fa-comment-dots"></i> Trả lời
        </button>

        <!-- Reply Form for Review -->
        <div class="detaill-review-footer">
            <form method="POST" action="{{ route('reviews.reply') }}" class="detaill-reply-form" id="reply-form-review-{{ $review->id }}" style="display:none;">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $review->id }}">
                <textarea name="comment" placeholder="Viết phản hồi của bạn..." class="detaill-reply-input"></textarea>
                <div class="detaill-form-actions">
                    <button type="reset" class="detaill-cancel-btn" data-id="{{ $review->id }}">Hủy</button>
                    <button type="submit" class="detaill-submit-btn">Gửi phản hồi</button>
                </div>
            </form>
        </div>

        <!-- Replies cấp 1 -->
        @if ($review->replies->count() > 0)
            <button class="detaill-action-btn detaill-view-replies-btn" data-id="{{ $review->id }}">
                <i class="fas fa-chevron-down"></i> Xem {{ $review->replies->count() }} phản hồi
            </button>
            <div class="detaill-review-replies" id="replies-review-{{ $review->id }}" style="display: none; margin-left: 20px;">
                @foreach ($review->replies as $reply)
                    <div class="detaill-reply-item" id="reply-{{ $reply->id }}">
                        <div class="detaill-reply-header">
                            <div class="detaill-user-avatar">
                                {{ strtoupper(mb_substr($reply->user->name, 0, 1, 'UTF-8')) }}
                            </div>
                            <span class="detaill-reply-name">{{ $reply->user->name }}</span>
                            <span class="detaill-reply-date">{{ \Carbon\Carbon::parse($reply->created_at)->format('d/m/Y') }}</span>
                        </div>

                        <p class="detaill-reply-text">{{ $reply->comment }}</p>

                        <!-- Reply to Reply Button -->
                        <button class="detaill-action-btn detaill-reply-to-reply-btn" data-type="reply" data-id="{{ $reply->id }}" data-parent="{{ $review->id }}">
                            <i class="far fa-comment-dots"></i> Trả lời
                        </button>

                        <!-- Reply Form for Reply cấp 1 -->
                        <form method="POST" action="{{ route('reviews.reply') }}" class="detaill-reply-form" id="reply-form-reply-{{ $reply->id }}" style="display:none; margin-left: 20px;">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                            <textarea name="comment" placeholder="Viết phản hồi của bạn..." class="detaill-reply-input"></textarea>
                            <div class="detaill-form-actions">
                                <button type="reset" class="detaill-cancel-btn" data-id="{{ $reply->id }}">Hủy</button>
                                <button type="submit" class="detaill-submit-btn">Gửi phản hồi</button>
                            </div>
                        </form>

                        <!-- Replies cấp 2 (reply của reply) -->
                        @if ($reply->replies->count() > 0)
                            <div style="margin-left: 40px; margin-top: 10px;">
                                @foreach ($reply->replies as $reply2)
                                    <div class="detaill-reply-item" id="reply-{{ $reply2->id }}">
                                        <div class="detaill-reply-header">
                                            <div class="detaill-user-avatar">
                                                {{ strtoupper(mb_substr($reply2->user->name, 0, 1, 'UTF-8')) }}
                                            </div>
                                            <span class="detaill-reply-name">{{ $reply2->user->name }}</span>
                                            <span class="detaill-reply-date">{{ \Carbon\Carbon::parse($reply2->created_at)->format('d/m/Y') }}</span>
                                        </div>

                                        <p class="detaill-reply-text">{{ $reply2->comment }}</p>

                                        <!-- Reply to Reply Button (nếu cần) -->
                                        <button class="detaill-action-btn detaill-reply-to-reply-btn" data-type="reply" data-id="{{ $reply2->id }}" data-parent="{{ $reply->id }}">
                                            <i class="far fa-comment-dots"></i> Trả lời
                                        </button>

                                        <!-- Reply Form for Reply cấp 2 (nếu cần) -->
                                        <form method="POST" action="{{ route('reviews.reply') }}" class="detaill-reply-form" id="reply-form-reply-{{ $reply2->id }}" style="display:none; margin-left: 20px;">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply2->id }}">
                                            <textarea name="comment" placeholder="Viết phản hồi của bạn..." class="detaill-reply-input"></textarea>
                                            <div class="detaill-form-actions">
                                                <button type="reset" class="detaill-cancel-btn" data-id="{{ $reply2->id }}">Hủy</button>
                                                <button type="submit" class="detaill-submit-btn">Gửi phản hồi</button>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Collapse Replies Button -->
                <div class="detaill-collapse-replies">
                    <button class="detaill-action-btn detaill-collapse-replies-btn" data-id="{{ $review->id }}">
                        <i class="fas fa-chevron-up"></i> Thu gọn
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>



                {{--  --}}
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
    {{-- mạnh làm js bình luận --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle reply form for review or reply
            document.querySelectorAll('.detaill-reply-btn, .detaill-reply-to-reply-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type'); // review hoặc reply
                    const form = document.getElementById(`reply-form-${type}-${id}`);

                    if (form) {
                        form.style.display = (form.style.display === 'block') ? 'none' : 'block';
                        if (form.style.display === 'block') {
                            form.querySelector('textarea').focus();
                        }
                    }
                });
            });

            // Cancel reply form
            document.querySelectorAll('.detaill-cancel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const form = this.closest('form');
                    if (form) form.style.display = 'none';
                });
            });

            // Toggle replies section
            document.querySelectorAll('.detaill-view-replies-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const repliesSection = document.getElementById(`replies-review-${id}`);
                    if (repliesSection) {
                        repliesSection.style.display = 'block';
                        this.style.display = 'none';
                    }
                });
            });

            // Collapse replies section
            document.querySelectorAll('.detaill-collapse-replies-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const repliesSection = document.getElementById(`replies-review-${id}`);
                    const viewBtn = document.querySelector(
                        `.detaill-view-replies-btn[data-id="${id}"]`);
                    if (repliesSection) repliesSection.style.display = 'none';
                    if (viewBtn) viewBtn.style.display = 'flex';
                });
            });
        });
    </script>
@endsection

