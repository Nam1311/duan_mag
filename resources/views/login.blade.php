@extends('app')

@section('body')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href=" {{ asset('') }}css/login.css">
    <link rel="stylesheet" href="css/login.css">

    <div class="logins-body">
        <div class="logins-container">
            <div class="logins-decor-top"></div>
            <div class="logins-decor-bottom"></div>

            <div class="logins-panel-container">
                <div class="logins-left-panel logins-order-login-1">
                    <div class="logins-brand">
                        <div class="logins-brand-logo">M A G</div>
                        <div class="logins-brand-text">MEMBERS</div>
                    </div>

                    <div class="logins-welcome-text">
                        <h1>Trải nghiệm thành viên</h1>
                        <p>Tham gia hệ thống thành viên để tận hưởng những đặc quyền và ưu đãi độc quyền dành riêng cho bạn.
                        </p>
                    </div>

                    <div class="logins-benefits">
                        <h3>Lợi ích đặc biệt</h3>

                        <div class="logins-benefit-item">
                            <div class="logins-check-icon"><i class="fas fa-check"></i></div>
                            <div class="logins-benefit-text">Quản lý và nâng cấp hạng thẻ thành viên</div>
                        </div>

                        <div class="logins-benefit-item">
                            <div class="logins-check-icon"><i class="fas fa-check"></i></div>
                            <div class="logins-benefit-text">Theo dõi lịch sử giao dịch và hóa đơn điện tử</div>
                        </div>

                        <div class="logins-benefit-item">
                            <div class="logins-check-icon"><i class="fas fa-check"></i></div>
                            <div class="logins-benefit-text">Nhận ưu đãi đặc biệt chỉ dành riêng cho thành viên</div>
                        </div>

                        <div class="logins-benefit-item">
                            <div class="logins-check-icon"><i class="fas fa-check"></i></div>
                            <div class="logins-benefit-text">Tích điểm và đổi quà từ các chương trình khuyến mãi</div>
                        </div>
                    </div>
                </div>

                <div class="logins-right-panel logins-order-login-2">
                    <div class="logins-form-container">
                        <div class="logins-form-nav">
                            <div class="logins-nav-item logins-active" data-target="login">ĐĂNG NHẬP</div>
                            <div class="logins-nav-item" data-target="register">ĐĂNG KÝ</div>
                            <div class="logins-nav-item" data-target="forgot">QUÊN MẬT KHẨU</div>
                        </div>

                        <div class="logins-form-header">
                            <h2>Đăng nhập tài khoản</h2>
                            <p>Nhập thông tin của bạn để truy cập vào hệ thống</p>
                        </div>

                        <!-- Login Form -->
                        <div id="login" class="logins-form-content logins-active">
                            <form action="{{ route('login') }}" method="POST" class="logins-login-form">
                                @csrf

                                <div class="logins-input-group">
                                    <label for="email">TÊN TÀI KHOẢN</label>
                                    <input type="text" value="{{ old('email') }}" name="email" id="login-username"
                                        placeholder="Tên đăng nhập hoặc email">
                                    {{-- <p class="logins-error-message" id="login-checkusername">Vui lòng nhập tên đăng
                                        nhập</p>
                                    --}}
                                    @error('email')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="logins-input-group">
                                    <label for="login-password">MẬT KHẨU</label>
                                    <div class="logins-password-container">
                                        <input type="password" name="password" id="login-password"
                                            placeholder="Mật khẩu của bạn">
                                        <button type="button" class="logins-toggle-password"  
                                            onclick="togglePassword('login-password')">
                                        
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                    {{-- <p class="logins-error-message" id="login-checkpass">Vui lòng nhập mật khẩu</p> --}}
                                    @error('password')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="logins-submit-btn">ĐĂNG NHẬP</button>

                                <div class="logins-social-login">
                                    <div class="logins-divider">
                                        <div class="logins-divider-line"></div>
                                        <div class="logins-divider-text">HOẶC ĐĂNG NHẬP VỚI</div>
                                        <div class="logins-divider-line"></div>
                                    </div>

                                    <div class="logins-social-buttons">
                                        <a href="{{ route('login.google') }}" class="logins-social-btn logins-google" onclick="loginWithGoogle()">
                                            <i class="fab fa-google logins-google-icon logins-social-icon"></i>
                                            <span>Google</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="logins-action-links">
                                    <a href="#" class="logins-action-link" data-target="forgot">Quên mật khẩu?</a>
                                    <a href="#" class="logins-action-link" data-target="register">Tạo tài khoản
                                        mới</a>
                                </div>
                            </form>
                        </div>

                        <!-- Register Form -->
                        <div id="register" class="logins-form-content">
                            <form action="{{ route('register') }}" method="POST" class="logins-register-form">
                                @csrf
                                <div class="logins-input-group">
                                    <label for="name">HỌ VÀ TÊN</label>
                                    <input type="text" name="name" value="{{ old('name') }}" id="fullname"
                                        placeholder="Nhập họ và tên đầy đủ">
                                    {{-- <p class="logins-error-message" id="checkfullname">Vui lòng nhập họ tên</p> --}}
                                    @error('name')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="logins-input-group">
                                    <label for="email">EMAIL</label>
                                    <input type="email" name="email" value="{{ old('email') }}" id="email"
                                        placeholder="Địa chỉ email của bạn">
                                    {{-- <p class="logins-error-message" id="checkemail">Vui lòng nhập email hợp lệ</p> --}}
                                    @error('email')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="logins-input-group">
                                    <label for="phone">SỐ ĐIỆN THOẠI</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" id="phone"
                                        placeholder="Số điện thoại">
                                    {{-- <p class="logins-error-message" id="checkphone">Vui lòng nhập số điện thoại</p>
                                    --}}
                                    @error('phone')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="logins-input-group">
                                    <label for="register-password">MẬT KHẨU</label>
                                    <div class="logins-password-container">
                                        <input type="password" name="password" id="register-password"
                                            placeholder="Tạo mật khẩu mạnh">
                                        <button type="button" class="logins-toggle-password"
                                            onclick="togglePassword('register-password')">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                    {{-- <p class="logins-error-message" id="register-checkpass">Mật khẩu phải có ít nhất 8
                                        ký
                                        tự</p> --}}
                                    @error('password')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="logins-input-group">
                                    <label for="confirm-password">XÁC NHẬN MẬT KHẨU</label>
                                    <div class="logins-password-container">
                                        <input type="password" name="password_confirmation" id="confirm-password"
                                            placeholder="Nhập lại mật khẩu">
                                        <button type="button" class="logins-toggle-password"
                                            onclick="togglePassword('confirm-password')">
                                            <i class="far fa-eye"></i>
                                        </button>
                                    </div>
                                    {{-- <p class="logins-error-message" id="checkconfirmpass">Mật khẩu không khớp</p> --}}
                                </div>

                                <button type="submit" class="logins-submit-btn">ĐĂNG KÝ</button>

                                <div class="logins-social-login">
                                    <div class="logins-divider">
                                        <div class="logins-divider-line"></div>
                                        <div class="logins-divider-text">HOẶC ĐĂNG KÝ BẰNG</div>
                                        <div class="logins-divider-line"></div>
                                    </div>

                                    <div class="logins-social-buttons">
                                        <a href="{{ route('login.google') }}" class="logins-social-btn logins-google" >
                                            <i class="fab fa-google logins-google-icon logins-social-icon"></i>
                                            <span>Google</span>
                                        </a>
                                        <a href="{{ route('login.facebook') }}" class="logins-social-btn logins-facebook" >
                                            <i class="fab fa-facebook-f logins-facebook-icon logins-social-icon"></i>
                                            <span>Facebook</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="logins-action-links">
                                    <a href="#" class="logins-action-link" data-target="login">Đã có tài khoản?
                                        Đăng nhập</a>
                                </div>
                            </form>
                        </div>

                        <!-- Forgot Password Form -->
                        <div id="forgot" class="logins-form-content">
                            <form class="logins-forgot-form" action="{{ route('password.update') }}" method="POST">
                                @csrf
                                <div class="logins-input-group">
                                    <label for="account">EMAIL HOẶC SỐ ĐIỆN THOẠI</label>
                                    <input type="text" name="email" id="account" placeholder="Nhập email hoặc số điện thoại đăng ký">
                                    @error('account')
                                        <p class="logins-error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="logins-submit-btn">GỬI YÊU CẦU</button>

                                <div class="logins-action-links">
                                    <a href="#" class="logins-action-link" data-target="login">Quay lại đăng
                                        nhập</a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.logins-nav-item, .logins-action-link').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const target = this.getAttribute('data-target');

                // Update navigation
                document.querySelectorAll('.logins-nav-item').forEach(nav => {
                    nav.classList.remove('logins-active');
                });

                if (target) {
                    document.querySelector(`.logins-nav-item[data-target="${target}"]`).classList.add(
                        'logins-active');

                    // Show target form
                    document.querySelectorAll('.logins-form-content').forEach(form => {
                        form.classList.remove('logins-active');
                    });
                    document.getElementById(target).classList.add('logins-active');

                    // Update form header
                    const header = document.querySelector('.logins-form-header');
                    if (target === 'login') {
                        header.querySelector('h2').textContent = 'Đăng nhập tài khoản';
                        header.querySelector('p').textContent =
                            'Nhập thông tin của bạn để truy cập vào hệ thống';
                    } else if (target === 'register') {
                        header.querySelector('h2').textContent = 'Tạo tài khoản mới';
                        header.querySelector('p').textContent = 'Điền thông tin để đăng ký tài khoản';
                    } else if (target === 'forgot') {
                        header.querySelector('h2').textContent = 'Khôi phục mật khẩu';
                        header.querySelector('p').textContent =
                            'Vui lòng cung cấp thông tin để khôi phục mật khẩu';
                    }
                }
            });
        });
        
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling.querySelector('i');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';

            passwordField.setAttribute('type', type);
            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        }






        // Validation helpers
        function validateEmail(email) {
            const re =
                /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

        function validatePhone(phone) {
            const re = /^\d{10,11}$/;
            return re.test(phone);
        }
    </script>
@endsection
