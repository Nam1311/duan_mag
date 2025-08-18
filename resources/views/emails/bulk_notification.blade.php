<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>{{ $subjectText }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef2f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 620px;
            margin: auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .logo {
            text-align: center;
            padding: 16px 0;
            background: #fff;
        }

        .header {
            background: linear-gradient(135deg, #ff8a00, #e52e71);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            line-height: 1.4;
        }

        .content {
            padding: 30px 25px;
        }

        .content p {
            font-size: 15.5px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .button {
            display: inline-block;
            background: #00b894;
            color: #fff;
            text-decoration: none;
            padding: 12px 25px;
            font-weight: bold;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .button:hover {
            background: #009f80;
        }

        .footer {
            background-color: #fafafa;
            color: #777;
            font-size: 13px;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="cid:mag-logo" alt="Logo Mail" style="max-height:60px; display:block; margin:0 auto;">
        </div>

        <div class="header">
            <h1>{{ $subjectText }}</h1>
        </div>

        <div class="content">
            <p>{!! nl2br(e($content)) !!}</p>
            <a href="{{ url('/') }}" class="button">Xem ngay ưu đãi</a>
        </div>

        <div class="footer">
            © {{ date('Y') }} Web bán áo Dev Store. Tất cả quyền được bảo lưu.
        </div>
    </div>
</body>

</html>
