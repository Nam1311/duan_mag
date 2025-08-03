<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang chủ')</title>

    {{-- <link rel="stylesheet" href="{{ asset('/css/grid.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/info.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/info_ctdh.css') }}">c
    <link rel="stylesheet" href="{{ asset('/css/thanhtoan.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/detail.css') }}"> --}}



    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/grid.css">
    <link rel="stylesheet" href="/css/info.css">
    <link rel="stylesheet" href="/css/info_ctdh.css">
    <link rel="stylesheet" href="/css/thanhtoan.css">
    <link rel="stylesheet" href="/css/about.css">
    <link rel="stylesheet" href="/css/detail.css">







    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
    <a href="{{route('tryon.form')}}">
        <div class="avatar2" id="avatar2">
            <i class="fas fa-robot"></i>
        </div>
    </a>
    <div class="avatar" id="avatar">
        <i class="fas fa-robot"></i>
    </div>
    <div class="box-ai" id="box-ai">
        @include('chat')
    </div>
    <div>
        @yield('body')
    </div>

    @include('footer')

    {{-- <script src="{{ asset('/js/slider.js') }}"></script>
    <script src="{{ asset('/js/main.js') }}"></script>
    <script src="{{ asset('/js/AI.js') }}"></script> --}}


    <script src="js/slider.js"></script>
    <script src="js/main.js"></script>
    <script src="js/AI.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/js/lightslider.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Toggle search input mobile
            const searchButton = document.querySelector(".Search");
            const searchInputMobile = document.querySelector(".search-input-mobile");
            if (searchButton && searchInputMobile) {
                searchButton.addEventListener("click", function () {
                    searchInputMobile.classList.toggle("active");
                });
            }

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

    <script>
        function updateCountdown() {
            const now = new Date();

            // Cấu hình giờ flash sale (ví dụ: 8h sáng đến 15h chiều)
            const startHour = 8;
            const endHour = 15;

            const startTime = new Date(now);
            startTime.setHours(startHour, 0, 0, 0);

            const endTime = new Date(now);
            endTime.setHours(endHour, 0, 0, 0);

            let targetTime;
            let isBeforeStart = now < startTime;
            let isAfterEnd = now >= endTime;

            if (isBeforeStart) {
                // Đếm đến giờ bắt đầu
                targetTime = startTime;
                document.getElementById('countdown-start-label').style.display = 'block';
                document.getElementById('countdown-label').style.display = 'none';
            } else if (!isAfterEnd) {
                // Đếm đến giờ kết thúc
                targetTime = endTime;
                document.getElementById('countdown-start-label').style.display = 'none';
                document.getElementById('countdown-label').style.display = 'block';
            } else {
                // Hết flash sale rồi => reset về hôm sau
                const tomorrowStart = new Date(now);
                tomorrowStart.setDate(now.getDate() + 1);
                tomorrowStart.setHours(startHour, 0, 0, 0);
                targetTime = tomorrowStart;
                document.getElementById('countdown-start-label').style.display = 'block';
                document.getElementById('countdown-label').style.display = 'none';
            }

            const distance = targetTime - now;

            if (distance > 0) {
                const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((distance / (1000 * 60)) % 60);
                const seconds = Math.floor((distance / 1000) % 60);

                document.getElementById("countdown-hour").textContent = String(hours).padStart(2, '0');
                document.getElementById("countdown-minute").textContent = String(minutes).padStart(2, '0');
                document.getElementById("countdown-second").textContent = String(seconds).padStart(2, '0');
            }
        }

        // Cập nhật mỗi giây
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>


    <!-- Khởi tạo các slider -->

    <script>
        $(document).ready(function () {
            const sliderConfigs = [
                { selector: '.product-list', item: 5 },
                { selector: '.list-cat', item: 5 },
                { selector: '.product-list-sale', item: 3, auto: true, speed: 1000, pause: 5000 }
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
                                enableDrag: false,
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
        const hourEl = document.querySelector('.time-hour');
        const minuteEl = document.querySelector('.time-minute');
        const secondEl = document.querySelector('.time-second');
        const saleSection = document.querySelector('.product-sale');

        let hours = parseInt(hourEl?.textContent || 0);
        let minutes = parseInt(minuteEl?.textContent || 0);
        let seconds = parseInt(secondEl?.textContent || 0);
        let totalSeconds = hours * 3600 + minutes * 60 + seconds;

        function updateCountdown() {
            if (totalSeconds <= 0) {
                clearInterval(countdown);
                // if (saleSection) saleSection.style.display = 'none';
                return;
            }

            totalSeconds--;
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;

            if (hourEl) hourEl.textContent = h.toString().padStart(2, '0');
            if (minuteEl) minuteEl.textContent = m.toString().padStart(2, '0');
            if (secondEl) secondEl.textContent = s.toString().padStart(2, '0');
        }

        const countdown = setInterval(updateCountdown, 1000);

        //chặn gửi form khi chưa nhập từ khóa

        document.getElementById('search-form').addEventListener('submit', function (e) {
            const input = document.getElementById('search-input');
            const keyword = input.value.trim();

            if (keyword === '') {
                e.preventDefault(); // Ngăn form submit
                alert('Vui lòng nhập từ khóa tìm kiếm!');
            }
        });
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
