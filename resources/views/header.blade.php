<header>
    <div class="nav-top">
        <label for="nav_mobile" class="nav-mobile-btn">
            <i class="fa-solid fa-bars fa-xl"></i>
        </label>

        <input hidden type="checkbox" class="nav_input" id="nav_mobile">
        <label for="nav_mobile" class="nav_overlay"></label>

        <nav class="nav_mobile">
            <label for="nav_mobile" class="mobile-close">
                <i class="fa-regular fa-circle-xmark fa-lg" style="color: #666;"></i>
            </label>
            <div class="nav-logo-mobile" style="margin: 60px 0px 0px 30px;">
                <h2 style=" font-weight: 450; font-size: 35PX;">M A G</h2>
            </div>
            <ul class="list">
                <li><a class="mobile_link" href="{{asset('/')}}">Trang chủ</a></li>
                <li><a class="mobile_link" href="/products">Sản phẩm</a></li>
                <li><a class="mobile_link sp" href="#">Áo thun</a></li>
                <li><a class="mobile_link sp" href="#">Áo polo</a></li>
                <li><a class="mobile_link sp" href="#">Áo sơ mi</a></li>
                <li><a class="mobile_link sp" href="#">Áo khoác</a></li>
                <li><a class="mobile_link sp" href="#">Quần</a></li>
                <li><a class="mobile_link sp" href="#">Phụ kiện</a></li>
                <li><a class="mobile_link" href="/form.html">Bảng size</a></li>
                <li><a class="mobile_link" href="/Returns.html">Chính sách đổi trả</a></li>
                <li><a class="mobile_link" href="/about.html">Về chúng tôi</a></li>
                <li><a class="mobile_link" href="/about.html">Yêu thích</a></li>
                <br><br>
                <li><a class="mobile_link" href="/info-user.html">Tài khoản</a></li>
                <li><a class="mobile_link" href="/dangnhap.html">Đăng nhập</a></li>
                <li><a class="mobile_link" href="/dangky.html">Đăng ký</a></li>
            </ul>
        </nav>

        <div class="logo">
            <a href="{{asset('/')}}">
                <b style=" font-weight: 450; font-size: 35PX;text-decoration: none;color: black;">M A G</b>
            </a>
        </div>

        <style>
            .suggestion-list {
                position: absolute;
                background: #fff;
                border: none;
                width: 100%;
                z-index: 1000;
                list-style: none;
                margin: 0;
                padding: 0;
                /* max-height: 200px; */
                /* overflow-y: auto; */
                margin-top: 40px;
                /* text-decoration: none; */
            }

            .suggestion-list li {
                padding: 10px;
                cursor: pointer;
                /* text-decoration: none; */

            }

            .suggestion-list li:hover {
                background-color: #f0f0f0;
            }
        </style>
        <div class="Search" style="position: relative;">
            <form id="search-form" action="{{ route('search') }}" method="GET" autocomplete="off">
                <input type="text" id="search-input" name="keyword" placeholder="Tìm kiếm sản phẩm...">

                <button class="icon-search icon-search-pc" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <ul id="suggestion-box" class="suggestion-list"></ul>
            </form>
                <button class="icon-search-mobile" type="" style="display: none"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>

        <div class="nav-pc">
            <ul>
                <li><a href="{{asset('/')}}">Trang chủ</a></li>
                <li><a href="{{asset('/products')}}">Sản phẩm</a></li>
                <li class="nav-item">
                    <a href="{{asset('/try-on')}}">Thử đồ
                    <span class="tag-with-ai">với AI</span></a>
                    <span id="ai-label" class="ai-label">Phòng thử đồ Online. <br> <a style="font-weight: 700; color: white" href="/try-on">Trải nghiệm ngay !</a></span>
                </li>
                <li><a href="{{asset('/contact')}}">Liên hệ</a></li>
                <li><a href="{{asset('/news')}}">Tin tức</a></li>
            </ul>
        </div>
       <style>
    .nav-item {
        position: relative;
        display: inline-block;
    }
    .tag-with-ai{
        background-color: rgb(0, 0, 0);
        font-size: 11px;
        font-weight: 600;
        padding: 5px;
        color: white
    }
    .ai-label {
        position: absolute;
        top: 30px; /* Kept from user-provided code */
        left: 50%;
        transform: translateX(-50%);
        color: #ffffff; /* White text for contrast */
        background-color: #1a1a1a; /* Dark gray-black for modern black theme */
        padding: 8px 12px;
        border-radius: 7px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Slightly darker shadow for depth */
        animation: swing 1.5s ease-in-out infinite;
        transition: opacity 0.5s ease;
        z-index: 1000;
        white-space: nowrap;
    }

    /* Speech bubble tail pointing upward */
    .ai-label::after {
        content: '';
        position: absolute;
        top: -8px; /* Place tail at the top */
        left: 50%; /* Kept from user-provided code */
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-bottom-color: #1a1a1a; /* Matches the dark gray-black background */
    }
    @keyframes swing {
        0% {
            transform: translateX(-50%) rotate(5deg);
        }
        50% {
            transform: translateX(-50%) rotate(-5deg);
        }
        100% {
            transform: translateX(-50%) rotate(5deg);
        }
    }

    .ai-label.hidden {
        opacity: 0;
        pointer-events: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const aiLabel = document.getElementById('ai-label');
        if (aiLabel) {
            setTimeout(() => {
                aiLabel.classList.add('hidden');
            }, 15000); // Hide after 10 seconds
        }
    });
</script>
        <div class="user">
            <div class="favourite-container">
                <a href="{{asset('/wishlist')}}"><i class="fa fa-heart" style="color: red; font-size: 26px;"></i></a>
            </div>
            <div class="cart-container">
                <a href="{{asset('/cart')}}"><i class="fa-solid fa-cart-shopping fa-xl" style="color: rgb(255, 64, 64);"></i></a>
                <div class="cart-badge">{{ $cartCount ?? 0 }}</div>
            </div>
            <div class="user-all">
                <ul>
                    <li>
                        <a href="{{asset('/check-login')}}"><i class="fa-solid fa-circle-user fa-2xl" style="color: rgb(189, 189, 189);"></i></a>
                        <ul>
                            @guest
                                {{-- Chưa đăng nhập --}}
                                <li><a href="{{ route('showlogin') }}">Đăng nhập</a></li>
                                <li><a href="{{ route('register') }}">Đăng ký</a></li>
                            @endguest

                            @auth
                                {{-- Đã đăng nhập --}}
                                <li><a href="/infouser">Trang cá nhân</a></li>
                                <li><a href="/wishlist">Yêu thích</a></li>
                                <li>
                                    <style>
                                        .form-logout>.btn-logout{
                                            border: none;
                                            background-color: white;
                                            cursor: pointer;
                                            font-size: 15px
                                        }
                                    </style>
                                    <form class="form-logout" method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="btn-logout" type="submit">Đăng xuất</button>
                                    </form>
                                </li>
                            @endauth
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="search-input-mobile" style="position: relative;">
        <form id="search-form-mobile" action="search" method="GET" autocomplete="off">
            <input type="text" id="search-input-mobile" name="keyword" placeholder="Tìm kiếm sản phẩm...">
            <ul id="suggestion-box" class="suggestion-list"></ul>
            <button type="submit">Tìm</button>
        </form>
    </div>

</header>
