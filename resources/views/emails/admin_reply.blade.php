<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Phản hồi từ MAG</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f4f6f9;
            padding: 0;
            margin: 0;
        }

        .wrapper {
            width: 100%;
            padding: 30px 0;
        }

        .email-box {
            max-width: 640px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-height: 60px;
        }

        .header {
            font-size: 22px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 12px;
        }

        .message-intro {
            font-size: 16px;
            margin-bottom: 16px;
            color: #333;
        }

        .message {
            white-space: pre-line;
            background-color: #f3f4f6;
            padding: 16px;
            border-radius: 8px;
            font-size: 15px;
            color: #111827;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #fff;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        .button:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(79, 70, 229, 0.4);
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 13px;
            color: #999;
        }

        @media (max-width: 640px) {
            .email-box {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="email-box">
            <div class="logo">
                <img src="https://yourdomain.com/logo.png" alt="MAG Logo">
            </div>

            <div class="header">Xin chào {{ $name }},</div>

            <div class="message-intro">
                Cảm ơn bạn đã liên hệ với <strong>MAG</strong>. Dưới đây là phản hồi của chúng tôi dành cho bạn:
            </div>

            <div class="message">
                {!! nl2br(e($reply)) !!}
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="https://yourwebsite.com" class="button" target="_blank">🔗 Truy cập website MAG</a>
            </div>

            <div style="margin-top: 24px;">
                Trân trọng,<br><strong>Admin MAG</strong>
            </div>
        </div>

        <div class="footer">
            © 2025 MAG Fashion. Mọi quyền được bảo lưu.
        </div>
    </div>
</body>

</html>
