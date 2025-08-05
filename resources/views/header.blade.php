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
                <li><a href="{{asset('/about')}}">Thử đồ</a></li>
                <li><a href="{{asset('/contact')}}">Liên hệ</a></li>
                <li><a href="{{asset('/news')}}">Tin tức</a></li>
            </ul>
        </div>

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
