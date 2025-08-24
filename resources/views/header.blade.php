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
                    <li><a class="mobile_link" href="/san-pham">Sản phẩm</a></li>
                    <li><a class="mobile_link" href="/contact">Liên hệ</a></li>
                    <li><a class="mobile_link" href="/about">Về chúng tôi</a></li>
                    <li><a class="mobile_link" href="/wishlist">Yêu thích</a></li>
                    <br><br>
                    <li class="mobile_link">
                        {{-- <a href="{{asset('/check-login')}}">Tài khoản</a> --}}
                        <ul>
                            @guest
                                {{-- Chưa đăng nhập --}}
                                <li><a href="{{ route('showlogin') }}">Đăng nhập</a></li>
                                <li><a href="{{ route('register') }}">Đăng ký</a></li>
                            @endguest

                            @auth
                                {{-- Đã đăng nhập --}}
                                <li><a href="/infouser">Trang cá nhân</a></li>
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
                    <i id="voiceBtn" class="fa fa-microphone" aria-hidden="true"></i>
                    <button class="icon-search icon-search-pc" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <ul id="suggestion-box" class="suggestion-list"></ul>
                </form>

                    <button class="icon-search-mobile" type="" style="display: none"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>


            {{-- menu pc --}}
            <div class="nav-pc">
                <ul>
                    <li><a href="{{asset('/')}}">Trang chủ</a></li>
                    <li><a href="{{asset('/san-pham')}}">Sản phẩm</a></li>
                    <li class="nav-item">
                        <a href="{{asset('/try-on')}}">Thử đồ
                        <span class="tag-with-ai">với AI</span></a>
                        <span id="ai-label" class="ai-label">Phòng thử đồ Online.
                            <a style="font-weight: 700; color: white" href="/try-on"> Trải nghiệm ngay !</a>
                            <div class="icon-ai">
                                <img src="{{ asset('/img/icon_ai.png') }}" alt="">
                            </div>
                        </span>
                    </li>
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
                                    @auth
                                        @if(auth()->user()->role === 'admin')
                                            <li><a href="/admin">Trang quản trị</a></li>
                                        @endif
                                    @endauth
                                    <li>
                                        <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fa fa-sign-out" aria-hidden="true"></i>    Đăng xuất
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
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
                <button class="btn-search-mobile" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <ul id="suggestion-box-mobile" class="suggestion-list"></ul>
            </form>
        </div>
</header>

<script>
    const searchInput = document.getElementById("search-input");
const voiceBtn = document.getElementById("voiceBtn");
const searchForm = document.getElementById("search-form");

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

if (SpeechRecognition) {
    const recognition = new SpeechRecognition();
    recognition.lang = "vi-VN";
    recognition.continuous = false;
    recognition.interimResults = false;

    voiceBtn.addEventListener("click", () => {
        recognition.start();
        voiceBtn.innerText = "🎙️ Đang nghe...";
    });

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        searchInput.value = transcript;
        voiceBtn.innerText = "🎤";

        // 🚀 Submit form để tìm kiếm trong DB Laravel
        searchForm.submit();
    };

    recognition.onerror = () => {
        voiceBtn.innerText = "🎤";
    };

    recognition.onend = () => {
        voiceBtn.innerText = "🎤";
    };
} else {
    alert("Trình duyệt của bạn không hỗ trợ Voice Search.");
}

    </script>
