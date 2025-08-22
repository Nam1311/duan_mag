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





                    <div id="thongbaou-notification-system">
                        <!-- Notification bell -->
                        <button id="thongbaou-notificationBell" aria-label="Thông báo">
                            <i class="fas fa-bell"></i>
                            <span id="thongbaou-notificationBadge">3</span>
                        </button>

                        <!-- Notification panel -->
                        <div id="thongbaou-notificationPanel">
                            <div id="thongbaou-notificationHeader">
                                <span>Thông báo mới</span>
                                <div id="thongbaou-notificationActions">
                                    <button id="thongbaou-markAllAsRead" title="Đánh dấu tất cả đã đọc" aria-label="Đánh dấu đã đọc">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <button id="thongbaou-refreshNotifications" title="Làm mới" aria-label="Làm mới">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="thongbaou-notificationContent">
                                <!-- Comment notification -->
                                <div class="thongbaou-notificationItem unread thongbaou-new-notification" data-comment-id="1" data-type="comment">
                                    <div class="thongbaou-notificationBody">
                                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="thongbaou-userAvatar" alt="Nguyễn Thị A">
                                        <div class="thongbaou-notificationText">
                                            <p class="thongbaou-notificationMessage"><strong>Nguyễn Thị A</strong> đã bình luận về sản phẩm của bạn</p>
                                            <div class="thongbaou-notificationComment">"Sản phẩm này có size lớn hơn bình thường không bạn? Mình đang phân vân giữa size M và L"</div>
                                            <div class="thongbaou-notificationMeta">
                                                <span class="thongbaou-notificationTime">10 phút trước</span>
                                                <span class="thongbaou-notificationType comment">Bình luận</span>
                                            </div>
                                            <div class="thongbaou-notificationActions">
                                                <button class="thongbaou-notificationBtn primary" data-action="view-comment">
                                                    <i class="fas fa-comment-alt"></i> Xem chi tiết
                                                </button>
                                                <button class="thongbaou-notificationBtn secondary" data-action="reply">
                                                    <i class="fas fa-reply"></i> Trả lời
                                                </button>
                                            </div>
                                            <div class="thongbaou-reply-form" data-comment-id="1">
                                                <textarea class="thongbaou-reply-textarea" placeholder="Viết phản hồi của bạn..."></textarea>
                                                <div class="thongbaou-reply-actions">
                                                    <button class="thongbaou-notificationBtn secondary" data-action="cancel-reply">
                                                        <i class="fas fa-times"></i> Hủy
                                                    </button>
                                                    <button class="thongbaou-notificationBtn primary" data-action="submit-reply">
                                                        <i class="fas fa-paper-plane"></i> Gửi
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Shipping notification -->
                                <div class="thongbaou-notificationItem unread thongbaou-new-notification" data-type="shipping">
                                    <div class="thongbaou-notificationBody">
                                        <div class="thongbaou-userAvatar" style="background: var(--purple); color: white; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="thongbaou-notificationText">
                                            <p class="thongbaou-notificationMessage"><strong>Đơn hàng đang giao</strong></p>
                                            <div class="thongbaou-notificationComment">Đơn hàng #DH123456 của bạn đang được vận chuyển. Dự kiến giao vào 15/12/2023</div>
                                            <div class="thongbaou-notificationMeta">
                                                <span class="thongbaou-notificationTime">1 giờ trước</span>
                                                <span class="thongbaou-notificationType shipping">Vận chuyển</span>
                                            </div>
                                            <div class="thongbaou-notificationActions">
                                                <button class="thongbaou-notificationBtn primary" data-action="track-order">
                                                    <i class="fas fa-map-marker-alt"></i> Theo dõi đơn hàng
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sale notification -->
                                <div class="thongbaou-notificationItem" data-type="sale">
                                    <div class="thongbaou-notificationBody">
                                        <div class="thongbaou-userAvatar" style="background: var(--pink); color: white; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-percent"></i>
                                        </div>
                                        <div class="thongbaou-notificationText">
                                            <p class="thongbaou-notificationMessage"><strong>Khuyến mãi đặc biệt</strong></p>
                                            <div class="thongbaou-notificationComment">Giảm giá 30% tất cả sản phẩm thời trang trong 24h. Mua ngay kẻo lỡ!</div>
                                            <div class="thongbaou-notificationMeta">
                                                <span class="thongbaou-notificationTime">2 ngày trước</span>
                                                <span class="thongbaou-notificationType sale">Khuyến mãi</span>
                                            </div>
                                            <div class="thongbaou-notificationActions">
                                                <button class="thongbaou-notificationBtn primary" data-action="view-sale">
                                                    <i class="fas fa-shopping-bag"></i> Xem ưu đãi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overlay -->
                    <div id="thongbaou-overlay"></div>

                    <!-- Comment detail box -->
                    <div id="thongbaou-commentDetailBox">
                        <div id="thongbaou-commentDetailHeader">
                            <div id="thongbaou-commentDetailTitle">Chi tiết bình luận</div>
                            <button id="thongbaou-closeCommentBox" aria-label="Đóng">
                                &times;
                            </button>
                        </div>
                        
                        <div id="thongbaou-commentDetailContent">
                            <div id="thongbaou-productInfo">
                                <img id="thongbaou-productImage" src="https://via.placeholder.com/600x400?text=Áo+thun+nam" alt="Áo thun nam cao cấp">
                                <h3 id="thongbaou-productTitle">Áo thun nam cao cấp - Phiên bản giới hạn 2023</h3>
                            </div>
                            
                            <div id="thongbaou-commentThread">
                                <!-- Original comment -->
                                <div class="thongbaou-commentItem">
                                    <div class="thongbaou-commentHeader">
                                        <img src="https://randomuser.me/api/portraits/men/1.jpg" class="thongbaou-commentUserAvatar" alt="Bạn">
                                        <div class="thongbaou-commentUserInfo">
                                            <span class="thongbaou-commentUserName">Bạn</span>
                                            <span class="thongbaou-commentTime">3 giờ trước</span>
                                        </div>
                                    </div>
                                    <div class="thongbaou-commentText">
                                        Sản phẩm này có size lớn hơn bình thường không? Mình cao 1m75 nặng 70kg nên đang phân vân giữa size L và XL. Ai đã mua có thể tư vấn giúp mình không?
                                    </div>
                                    <div class="thongbaou-commentActions">
                                        <button class="thongbaou-commentLike">
                                            <i class="far fa-heart"></i>
                                            <span class="thongbaou-commentLikeCount">12</span>
                                        </button>
                                        <button class="thongbaou-notificationBtn secondary" data-action="reply-in-detail">
                                            <i class="fas fa-reply"></i> Trả lời
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Reply 1 -->
                                <div class="thongbaou-commentItem">
                                    <div class="thongbaou-commentHeader">
                                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="thongbaou-commentUserAvatar" alt="Nguyễn Thị A">
                                        <div class="thongbaou-commentUserInfo">
                                            <span class="thongbaou-commentUserName">Nguyễn Thị A</span>
                                            <span class="thongbaou-commentTime">10 phút trước</span>
                                        </div>
                                    </div>
                                    <div class="thongbaou-commentText">
                                        Mình 1m72 68kg mặc size L vừa đẹp, bạn nên lấy size L nhé. Chất liệu áo co giãn tốt nên ôm vừa người nhưng không bị chật.
                                    </div>
                                    <div class="thongbaou-commentActions">
                                        <button class="thongbaou-commentLike active">
                                            <i class="fas fa-heart"></i>
                                            <span class="thongbaou-commentLikeCount">3</span>
                                        </button>
                                        <button class="thongbaou-notificationBtn secondary" data-action="reply-in-detail">
                                            <i class="fas fa-reply"></i> Trả lời
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Reply form in detail box -->
                                <div id="thongbaou-detail-reply-form">
                                    <textarea id="thongbaou-detail-reply-textarea" placeholder="Viết phản hồi của bạn..."></textarea>
                                    <div class="thongbaou-reply-actions">
                                        <button class="thongbaou-notificationBtn secondary" data-action="cancel-reply-in-detail">
                                            <i class="fas fa-times"></i> Hủy
                                        </button>
                                        <button class="thongbaou-notificationBtn primary" data-action="submit-reply-in-detail">
                                            <i class="fas fa-paper-plane"></i> Gửi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                



                    
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
        document.addEventListener('DOMContentLoaded', function() {
            // DOM elements
            const bell = document.getElementById('thongbaou-notificationBell');
            const panel = document.getElementById('thongbaou-notificationPanel');
            const badge = document.getElementById('thongbaou-notificationBadge');
            const overlay = document.getElementById('thongbaou-overlay');
            const commentBox = document.getElementById('thongbaou-commentDetailBox');
            const closeCommentBox = document.getElementById('thongbaou-closeCommentBox');
            const markAllAsReadBtn = document.getElementById('thongbaou-markAllAsRead');
            const refreshBtn = document.getElementById('thongbaou-refreshNotifications');

            // Count unread notifications
            let unreadItems = document.querySelectorAll('.thongbaou-notificationItem.unread');
            updateBadge(unreadItems.length);

            // Toggle notification panel
            bell.addEventListener('click', function(e) {
                e.stopPropagation();
                panel.classList.toggle('show');
                if (panel.classList.contains('show')) {
                    markAllNotificationsAsRead();
                }
            });

            // Close panel when clicking outside
            document.addEventListener('click', function(e) {
                if (!bell.contains(e.target) && !panel.contains(e.target)) {
                    panel.classList.remove('show');
                    // Hide all reply forms in notifications
                    document.querySelectorAll('.thongbaou-reply-form').forEach(form => {
                        form.classList.remove('show');
                    });
                }
            });

            // Update badge count
            function updateBadge(count) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
                if (count > 0) {
                    bell.classList.add('active');
                    setTimeout(() => bell.classList.remove('active'), 500);
                }
            }

            // Mark all notifications as read
            function markAllNotificationsAsRead() {
                document.querySelectorAll('.thongbaou-notificationItem.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.classList.remove('thongbaou-new-notification');
                });
                unreadItems = [];
                updateBadge(0);
            }

            // Mark all as read button
            markAllAsReadBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllNotificationsAsRead();
            });

            // Refresh notifications
            refreshBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                alert('Đã làm mới thông báo');
            });

            // Handle notification actions
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;

                const action = btn.dataset.action;
                const notification = btn.closest('.thongbaou-notificationItem');
                const commentId = notification?.dataset.commentId;

                if (action === 'view-comment') {
                    overlay.classList.add('show');
                    commentBox.classList.add('show');
                    panel.classList.remove('show');
                    if (notification && notification.classList.contains('unread')) {
                        notification.classList.remove('unread');
                        unreadItems = document.querySelectorAll('.thongbaou-notificationItem.unread');
                        updateBadge(unreadItems.length);
                    }
                }
                else if (action === 'reply') {
                    const replyForm = notification.querySelector('.thongbaou-reply-form');
                    replyForm.classList.toggle('show');
                    if (replyForm.classList.contains('show')) {
                        const textarea = replyForm.querySelector('.thongbaou-reply-textarea');
                        setTimeout(() => textarea.focus(), 100);
                    }
                }
                else if (action === 'cancel-reply') {
                    const replyForm = btn.closest('.thongbaou-reply-form');
                    replyForm.classList.remove('show');
                    replyForm.querySelector('.thongbaou-reply-textarea').value = '';
                }
                else if (action === 'submit-reply') {
                    const replyForm = btn.closest('.thongbaou-reply-form');
                    const textarea = replyForm.querySelector('.thongbaou-reply-textarea');
                    if (textarea.value.trim() === '') {
                        alert('Vui lòng nhập nội dung phản hồi');
                        return;
                    }
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi';
                    setTimeout(() => {
                        alert(`Đã gửi phản hồi cho bình luận #${commentId}: "${textarea.value}"`);
                        textarea.value = '';
                        replyForm.classList.remove('show');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi';
                    }, 1000);
                }
                else if (action === 'track-order') {
                    alert('Chuyển hướng đến trang theo dõi đơn hàng');
                }
                else if (action === 'view-sale') {
                    alert('Chuyển hướng đến trang khuyến mãi');
                }
                else if (action === 'reply-in-detail') {
                    const replyForm = document.getElementById('thongbaou-detail-reply-form');
                    replyForm.classList.toggle('show');
                    const textarea = document.getElementById('thongbaou-detail-reply-textarea');
                    setTimeout(() => textarea.focus(), 100);
                }
                else if (action === 'cancel-reply-in-detail') {
                    const replyForm = document.getElementById('thongbaou-detail-reply-form');
                    replyForm.classList.remove('show');
                    replyForm.querySelector('#thongbaou-detail-reply-textarea').value = '';
                }
                else if (action === 'submit-reply-in-detail') {
                    const replyForm = document.getElementById('thongbaou-detail-reply-form');
                    const textarea = document.getElementById('thongbaou-detail-reply-textarea');
                    if (textarea.value.trim() === '') {
                        alert('Vui lòng nhập nội dung phản hồi');
                        return;
                    }
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi';
                    setTimeout(() => {
                        alert(`Đã gửi phản hồi: "${textarea.value}"`);
                        textarea.value = '';
                        replyForm.classList.remove('show');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi';
                    }, 1000);
                }
            });

            // Like comment
            document.addEventListener('click', function(e) {
                const likeBtn = e.target.closest('.thongbaou-commentLike');
                if (!likeBtn) return;

                const icon = likeBtn.querySelector('i');
                const countEl = likeBtn.querySelector('.thongbaou-commentLikeCount');
                let count = parseInt(countEl.textContent);

                if (likeBtn.classList.contains('active')) {
                    likeBtn.classList.remove('active');
                    icon.classList.replace('fas', 'far');
                    countEl.textContent = count - 1;
                } else {
                    likeBtn.classList.add('active');
                    icon.classList.replace('far', 'fas');
                    countEl.textContent = count + 1;
                }
            });

            // Close comment detail box
            closeCommentBox.addEventListener('click', closeCommentDetail);
            overlay.addEventListener('click', closeCommentDetail);

            function closeCommentDetail() {
                overlay.classList.remove('show');
                commentBox.classList.remove('show');
                document.getElementById('thongbaou-detail-reply-form').classList.remove('show');
            }

            // Simulate new notification
            setTimeout(() => {
                const newCount = Math.floor(Math.random() * 2) + 1;
                if (newCount > 0) {
                    updateBadge(newCount);
                    if (!panel.classList.contains('show')) {
                        if (Notification.permission === 'granted') {
                            new Notification('Bạn có thông báo mới', {
                                body: 'Có khuyến mãi mới dành cho bạn!',
                                icon: 'https://via.placeholder.com/64?text=TB'
                            });
                        } else if (Notification.permission !== 'denied') {
                            Notification.requestPermission().then(permission => {
                                if (permission === 'granted') {
                                    new Notification('Bạn có thông báo mới', {
                                        body: 'Có khuyến mãi mới dành cho bạn!',
                                        icon: 'https://via.placeholder.com/64?text=TB'
                                    });
                                }
                            });
                        }
                    }
                }
            }, 15000);

            // Request notification permission on page load
            if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        });
    </script>
  