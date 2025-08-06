<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Thành Công</title>
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

        .success-container {
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

        .checkmark-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            position: relative;
            border-radius: 50%;
            background: linear-gradient(45deg, #4CAF50, #45a049);
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

        .checkmark {
            width: 40px;
            height: 20px;
            border: 4px solid white;
            border-top: none;
            border-right: none;
            transform: rotate(-45deg);
            animation: drawCheckmark 0.5s ease-out 0.8s both;
        }

        @keyframes drawCheckmark {
            from {
                width: 0;
                height: 0;
            }
            to {
                width: 40px;
                height: 20px;
            }
        }

        .success-title {
            color: #1a1a1a;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        .success-message {
            color: #4a4a4a;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            animation: fadeInUp 0.6s ease-out 0.7s both;
        }

        .home-button {
            background: linear-gradient(45deg, #1a1a1a, #333333);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            animation: fadeInUp 0.6s ease-out 0.9s both;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .home-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, #333333, #4a4a4a);
        }

        .home-button:active {
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
            .success-container {
                padding: 30px 20px;
                margin: 10px;
            }

            .success-title {
                font-size: 1.8rem;
            }

            .success-message {
                font-size: 1rem;
            }

            .checkmark-container {
                width: 80px;
                height: 80px;
            }

            .checkmark {
                width: 30px;
                height: 15px;
            }

            .home-button {
                padding: 12px 30px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 25px 15px;
            }

            .success-title {
                font-size: 1.5rem;
            }

            .home-button {
                padding: 10px 25px;
                font-size: 0.95rem;
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

    <div class="success-container">
        <div class="checkmark-container">
            <div class="checkmark"></div>
        </div>

        <h1 class="success-title">Thanh toán thành công!</h1>

        <p class="success-message">
            Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được xác nhận và đang được xử lý.
            Thông tin chi tiết về đơn hàng đã được gửi qua email của bạn.
        </p>

        <a href="#" class="home-button" onclick="goToHome()">
            Quay lại trang chủ
        </a>
    </div>

    <script>
        function goToHome() {
            // Thay đổi URL này thành đường dẫn trang chủ thực tế của bạn
            window.location.href = '/';
        }

        // Tạo hiệu ứng confetti khi trang load
        function createConfetti() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];

            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.cssText = `
                        position: fixed;
                        width: 8px;
                        height: 8px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        left: ${Math.random() * 100}vw;
                        top: -10px;
                        border-radius: 50%;
                        pointer-events: none;
                        z-index: 1000;
                        animation: confettiFall ${2 + Math.random() * 3}s linear forwards;
                    `;

                    document.body.appendChild(confetti);

                    setTimeout(() => {
                        confetti.remove();
                    }, 5000);
                }, i * 100);
            }
        }

        // CSS animation cho confetti
        const style = document.createElement('style');
        style.textContent = `
            @keyframes confettiFall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Chạy confetti sau khi animation chính hoàn thành
        setTimeout(createConfetti, 1500);
    </script>
</body>
</html>
