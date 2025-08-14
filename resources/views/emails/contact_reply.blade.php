<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>MAG - Phản hồi liên hệ</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            padding: 30px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #005baa;
        }

        p {
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-height: 60px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            background-color: #005baa;
            color: #fff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #004080;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="cid:mag-logo" alt="Logo Mail" style="max-height:60px;">
        </div>
        <h2>Xin chào {{ $name }},</h2>
        <p>
            Cảm ơn bạn đã liên hệ với <strong>MAG</strong>. Chúng tôi đã nhận được thông tin của bạn và sẽ phản hồi
            trong thời gian sớm nhất.
        </p>
        <p>
            Nếu bạn có bất kỳ câu hỏi nào thêm, đừng ngần ngại liên hệ lại với chúng tôi qua email hoặc hotline.
        </p>

        <p style="text-align: center;">
            <a href="https://yourwebsite.com" class="btn" target="_blank">Truy cập website MAG</a>
        </p>

        <p>
            Trân trọng,<br>
            <strong>Đội ngũ MAG</strong>
        </p>
        <div class="footer">
            © 2025 MAG Fashion. Mọi quyền được bảo lưu.
        </div>
    </div>
</body>

</html>
