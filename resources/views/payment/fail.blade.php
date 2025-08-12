<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Thất Bại</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgb(227, 226, 226) 0%, #7e7d7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .failed-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            animation: slideUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .failed-icon-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            position: relative;
            border-radius: 50%;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.6s ease-out 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .failed-icon {
            width: 50px;
            height: 50px;
            position: relative;
            animation: drawFailed 0.5s ease-out 0.8s both;
        }

        .failed-icon::before,
        .failed-icon::after {
            content: '';
            position: absolute;
            width: 4px;
            height: 40px;
            background: white;
            border-radius: 2px;
            left: 50%;
            top: 50%;
            transform-origin: center;
        }

        .failed-icon::before {
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .failed-icon::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        @keyframes drawFailed {
            from {
                opacity: 0;
                transform: scale(0);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .failed-title {
            color: #1a1a1a;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        .failed-message {
            color: #4a4a4a;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease-out 0.7s both;
        }

        .failed-reasons {
            background: rgba(231, 76, 60, 0.1);
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            text-align: left;
            animation: fadeInUp 0.6s ease-out 0.8s both;
        }

        .failed-reasons h4 {
            color: #c0392b;
            font-size: 1rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .failed-reasons ul {
            color: #666;
            font-size: 0.95rem;
            list-style: none;
            padding-left: 0;
        }

        .failed-reasons li {
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }

        .failed-reasons li::before {
            content: "•";
            color: #e74c3c;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .support-info {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 0.95rem;
            color: #666;
            animation: fadeInUp 0.6s ease-out 0.85s both;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s ease-out 0.9s both;
        }

        .btn {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
            background: linear-gradient(45deg, #c0392b, #a93226);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #1a1a1a, #333333);
            color: white;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, #333333, #4a4a4a);
        }

        .btn-outline {
            background: transparent;
            color: #1a1a1a;
            border: 2px solid rgba(0, 0, 0, 0.2);
        }

        .btn-outline:hover {
            transform: translateY(-2px);
            background: rgba(0, 0, 0, 0.05);
            border-color: rgba(0, 0, 0, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 10px;
            height: 10px;
            left: 20%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 6px;
            height: 6px;
            left: 80%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            width: 8px;
            height: 8px;
            left: 60%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .failed-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .failed-title {
                font-size: 1.8rem;
            }

            .failed-message {
                font-size: 1rem;
            }

            .failed-icon-container {
                width: 80px;
                height: 80px;
            }

            .failed-icon {
                width: 40px;
                height: 40px;
            }

            .failed-icon::before,
            .failed-icon::after {
                height: 30px;
            }

            .button-group {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 200px;
                padding: 12px 20px;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .failed-container {
                padding: 25px 15px;
            }

            .failed-title {
                font-size: 1.5rem;
            }

            .failed-reasons,
            .support-info {
                font-size: 0.9rem;
                padding: 12px;
            }
        }

        /* Pulse effect for failed icon */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.4);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(231, 76, 60, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }
    </style>
</head>
<body>
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="failed-container">
        <div class="failed-icon-container pulse">
            <div class="failed-icon"></div>
        </div>

        <h1 class="failed-title">Thanh toán thất bại!</h1>

        <p class="failed-message">
            @if(isset($error))
                {{ $error }}
            @else
                Rất tiếc, giao dịch của bạn không thể được xử lý.
                Vui lòng kiểm tra thông tin và thử lại.
            @endif
        </p>

        <div class="failed-reasons">
            <h4>Có thể do các nguyên nhân sau:</h4>
            <ul>
                <li>Thông tin thẻ không chính xác</li>
                <li>Số dư tài khoản không đủ</li>
                <li>Thẻ đã hết hạn hoặc bị khóa</li>
                <li>Kết nối mạng không ổn định</li>
                <li>Ngân hàng từ chối giao dịch</li>
            </ul>
        </div>

        <div class="support-info">
            <strong>Cần hỗ trợ?</strong><br>
            Liên hệ hotline: <strong>1900-1234</strong> hoặc email: <strong>support@example.com</strong>
        </div>

        <div class="button-group">
            <a href="#" class="btn btn-primary" onclick="retryPayment()">
                Thử lại
            </a>
            <a href="#" class="btn btn-secondary" onclick="goToHome()">
                Về trang chủ
            </a>
            <a href="#" class="btn btn-outline" onclick="contactSupport()">
                Liên hệ hỗ trợ
            </a>
        </div>
    </div>

    <script>
        function retryPayment() {
            // Quay lại trang thanh toán
            window.history.back();
        }

        function goToHome() {
            // Thay đổi URL này thành đường dẫn trang chủ thực tế của bạn
            window.location.href = '/';
        }

        function contactSupport() {
            // Chuyển đến trang hỗ trợ hoặc mở email
            window.location.href = 'mailto:support@example.com';
        }

        // Tạo hiệu ứng rung lắc nhẹ khi load
        function createShakeEffect() {
            const container = document.querySelector('.failed-container');
            setTimeout(() => {
                container.style.animation = 'slideUp 0.8s ease-out, shake 0.3s ease-in-out 1.2s';
            }, 1200);
        }

        // CSS animation cho shake effect
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-3px); }
                75% { transform: translateX(3px); }
            }
        `;
        document.head.appendChild(style);

        // Khởi tạo khi trang load
        window.addEventListener('load', () => {
            createShakeEffect();
        });
    </script>
</body>
</html>
