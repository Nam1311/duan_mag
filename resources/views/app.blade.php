<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang chủ')</title>

    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/grid.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/info.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/info_ctdh.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/thanhtoan.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/detail.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/css/lightslider.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:wght@100..900&family=Oxanium:wght@200..800&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.8.1/lottie.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('logo.jpg') }}">
    @stack('styles')
</head>

<body>

    @include('header')

    <div class="avatar" id="avatar">
        <i class="fas fa-robot"></i>
    </div>

    <!-- AI Introduction Bubble -->
    <div class="ai-intro-bubble" id="ai-intro-bubble" style="display: none;">
        <div class="bubble-content">
            <i class="fas fa-sparkles bubble-icon"></i>
            <div class="bubble-text">
                <strong>Xin chào! 👋</strong><br>
                Tôi có thể giúp bạn tư vấn chọn đồ, tìm kiếm sản phẩm phù hợp và trả lời mọi câu hỏi!
            </div>
            <button class="bubble-close" onclick="hideAIBubble()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="bubble-arrow"></div>
    </div>

    <div class="box-ai" id="box-ai">
        @include('chat')
    </div>
    <div>
        @yield('body')
    </div>

    @include('footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/js/lightslider.min.js"></script>

    <script src="js/slider.js"></script>
    <script src="js/main.js"></script>
    <script src="js/AI.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aiLabel = document.getElementById('ai-label');
            if (aiLabel) {
                setTimeout(() => {
                    aiLabel.classList.add('hidden');
                }, 5000); // Hide after 5 seconds
            }

            // AI Bubble Management
            const aiBubble = document.getElementById('ai-intro-bubble');
            const avatar = document.getElementById('avatar');

            // Show bubble immediately when page loads
            if (aiBubble && !localStorage.getItem('ai-bubble-dismissed')) {
                aiBubble.style.display = 'block';
                if (avatar) {
                    avatar.classList.add('has-notification');
                }
            }

            // Auto hide bubble after 15 seconds
            setTimeout(() => {
                if (aiBubble && !localStorage.getItem('ai-bubble-dismissed')) {
                    hideAIBubble();
                }
            }, 15000);

            // Show bubble when hover over AI avatar
            if (avatar && aiBubble) {
                avatar.addEventListener('mouseenter', () => {
                    if (!localStorage.getItem('ai-bubble-dismissed')) {
                        aiBubble.style.display = 'block';
                        aiBubble.classList.remove('hiding');
                    }
                });

                // Remove notification effect when avatar is clicked
                avatar.addEventListener('click', () => {
                    avatar.classList.remove('has-notification');
                    // Hide the bubble when avatar is clicked
                    if (aiBubble) {
                        hideAIBubble();
                    }
                });
            }
        });

        // Function to hide AI bubble
        function hideAIBubble() {
            const aiBubble = document.getElementById('ai-intro-bubble');
            const avatar = document.getElementById('avatar');

            if (aiBubble) {
                aiBubble.classList.add('hiding');
                setTimeout(() => {
                    aiBubble.style.display = 'none';
                    localStorage.setItem('ai-bubble-dismissed', 'true');
                }, 300);
            }

            if (avatar) {
                avatar.classList.remove('has-notification');
            }
        }

        // Reset bubble for new sessions (optional)
        window.addEventListener('beforeunload', () => {
            localStorage.removeItem('ai-bubble-dismissed');
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchButton = document.querySelector(".Search");
            // Live search setup
            function setupLiveSearch(inputId, formId, suggestionBoxId) {
                const input = document.getElementById(inputId);
                const form = document.getElementById(formId);
                const suggestionBox = document.getElementById(suggestionBoxId);

                if (!input || !form || !suggestionBox) return;

                input.addEventListener('keyup', function () {
                    const keyword = input.value.trim();
                    if (keyword.length > 1) {
                        fetch(`/search-suggestions?keyword=${encodeURIComponent(keyword)}`)
                            .then(res => res.json())
                            .then(data => {
                                suggestionBox.innerHTML = '';
                                if (data.length > 0) {
                                    suggestionBox.innerHTML = ''; // Xóa gợi ý cũ trước khi thêm mới
                                    data.forEach(item => {
                                        const li = document.createElement('li');
                                        li.style.display = 'flex';
                                        li.style.alignItems = 'center';
                                        li.style.cursor = 'pointer';
                                        li.style.padding = '5px';

                                        // anhanh
                                        const img = document.createElement('img');
                                        img.src = item.image;
                                        img.alt = item.name;
                                        img.style.width = '50px';
                                        img.style.height = '50px';
                                        img.style.objectFit = 'cover';
                                        img.style.marginRight = '10px';
                                        img.style.borderRadius = '4px';

                                        // ten & gia
                                        const infoContainer = document.createElement('div');
                                        infoContainer.style.display = 'flex';
                                        infoContainer.style.flexDirection = 'column';

                                        // tên sp
                                        const nameSpan = document.createElement('span');
                                        nameSpan.textContent = item.name;
                                        nameSpan.style.fontWeight = 'normal';

                                        // giái
                                        const priceSpan = document.createElement('span');
                                        priceSpan.textContent = `${Number(item.price).toLocaleString()}đ`;
                                        priceSpan.style.color = 'red';
                                        priceSpan.style.fontSize = '14px';
                                        priceSpan.style.marginTop = '2px';

                                        // Gộp vào infoContainer
                                        infoContainer.appendChild(nameSpan);
                                        infoContainer.appendChild(priceSpan);

                                        // Gộp tất cả vào li
                                        li.appendChild(img);
                                        li.appendChild(infoContainer);

                                        // Sự kiện click để vào chi tiết
                                        li.addEventListener('click', function () {
                                            window.location.href = `/detail/${item.id}`;
                                        });

                                        suggestionBox.appendChild(li);
                                    });
                                }
                            });
                    } else {
                        suggestionBox.innerHTML = '';
                    }
                });

                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        form.submit();
                    }
                });
            }

            setupLiveSearch('search-input', 'search-form', 'suggestion-box');
            setupLiveSearch('search-input-mobile', 'search-form-mobile', 'suggestion-box-mobile');

        });
    </script>

    <!-- Khởi tạo các slider -->
    <script>
        $(document).ready(function () {
            const sliderConfigs = [
                { selector: '.product-list', item: 5 },
                { selector: '.list-cat', item: 5 },
                { selector: '.product-list-sale', item: 3, auto: true, speed: 1000, pause: 5000 },
                { selector: '.product-recommend', item: 4, auto: false, speed: 1000, pause: 5000, controls: false }
            ];

            sliderConfigs.forEach(config => {
                $(config.selector).lightSlider({
                    item: config.item,
                    loop: true,
                    slideMargin: 20,
                    controls: true,
                    auto: config.auto || false,
                    speed: config.speed || 400,
                    pause: config.pause || 2000,
                    responsive: [
                        {
                            breakpoint: 992,
                            settings: { item: 2, slideMargin: 15 }
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                item: 2,
                                slideMove: 1,
                                slideMargin: 10,
                                adaptiveHeight: true,
                                enableDrag: true,
                                controls: true,
                                pager: true
                            }
                        }
                    ]
                });
            });
        });
    </script>

    <!-- Countdown -->
    <script>
        function updateCountdown() {
            const hourEl = document.getElementById('countdown-hour');
            const minuteEl = document.getElementById('countdown-minute');
            const secondEl = document.getElementById('countdown-second');
            const flashSaleStart = document.getElementById('flash-sale-start');
            const countdownLabel = document.getElementById('countdown-label');
            const boxTimes = document.querySelectorAll('.box-time');

            // Nếu thiếu phần tử → dừng
            if (!hourEl || !minuteEl || !secondEl || !flashSaleStart || !countdownLabel || boxTimes.length === 0) {
                return;
            }

            let hours = parseInt(hourEl.textContent);
            let minutes = parseInt(minuteEl.textContent);
            let seconds = parseInt(secondEl.textContent);

            // Giảm thời gian
            if (seconds > 0) {
                seconds--;
            } else {
                if (minutes > 0) {
                    minutes--;
                    seconds = 59;
                } else {
                    if (hours > 0) {
                        hours--;
                        minutes = 59;
                        seconds = 59;
                    } else {
                        // Hết giờ: hiện thông báo Flash Sale bắt đầu
                        flashSaleStart.style.display = 'block';
                        countdownLabel.style.display = 'none';
                        boxTimes.forEach(box => box.style.display = 'none');
                        return;
                    }
                }
            }

            // Cập nhật lại DOM
            hourEl.textContent = String(hours).padStart(2, '0');
            minuteEl.textContent = String(minutes).padStart(2, '0');
            secondEl.textContent = String(seconds).padStart(2, '0');
        }

        // Cập nhật mỗi giây
        setInterval(updateCountdown, 1000);
    </script>

    {{-- cuar sp moi --}}
    <script>
        const tabs = document.querySelectorAll('.tab');
        const tabItems = document.querySelectorAll('.tab-item');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Xóa class active khỏi tất cả tab và nội dung
                tabs.forEach(t => t.classList.remove('active'));
                tabItems.forEach(item => item.classList.remove('active'));

                // Thêm active vào tab hiện tại
                tab.classList.add('active');

                // Hiện nội dung tương ứng
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>

    @stack('scripts')

</body>

</html>
