<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fc;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 100, 0.08);
        }

        .email-header {
            background: linear-gradient(135deg, #3b71ca, #1a56db);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .email-body {
            padding: 40px;
        }

        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
            border-top: 1px solid #eaeaea;
        }

        .logo {
            height: 50px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 28px;
            margin: 0 0 10px;
            font-weight: 600;
        }

        .subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .content-box {
            background: #f8faff;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border: 1px solid #e0e7ff;
        }

        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #3b71ca, #1a56db);
            color: white !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(59, 113, 202, 0.3);
            transition: all 0.3s ease;
            margin: 20px 0;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(59, 113, 202, 0.4);
        }

        .instruction {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #3b71ca;
        }

        .instruction ol {
            padding-left: 20px;
            margin: 15px 0;
        }

        .instruction li {
            margin-bottom: 10px;
        }

        .security-note {
            display: flex;
            align-items: center;
            background: #fff8e6;
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
            border-left: 4px solid #ffc107;
        }

        .security-note i {
            color: #ffc107;
            font-size: 24px;
            margin-right: 15px;
        }

        .expire-note {
            color: #e74c3c;
            font-weight: 500;
            margin: 15px 0;
        }

        .logo>a {
            text-decoration: none;

        }

        @media (max-width: 600px) {
            .email-body {
                padding: 25px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo">
                <a href="{{asset('/')}}">
                    <b style=" font-weight: 450; font-size: 35PX;text-decoration: none;color: black;">M A G</b>
                </a>
            </div>
            <h1>Đặt lại mật khẩu</h1>
            <p class="subtitle">Bảo mật tài khoản của bạn</p>
        </div>

        <div class="email-body">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
            <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>

            <div class="content-box">
                <p>Để thiết lập mật khẩu mới, vui lòng nhấp vào nút bên dưới:</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $resetUrl }}" class="btn-primary">Đặt lại mật khẩu</a>
                </div>

                <p class="expire-note">Lưu ý: Liên kết này sẽ hết hạn sau 24 giờ.</p>
                <p>Nếu bạn không nhấp được vào nút trên, vui lòng sao chép và dán URL sau vào trình duyệt:</p>
                <p style="word-break: break-all;">
                    <a href="{{ $resetUrl }}" style="color: #3b71ca;">{{ $resetUrl }}</a>
                </p>
            </div>

            <div class="instruction">
                <h3 style="margin-top: 0;">Hướng dẫn bảo mật:</h3>
                <ol>
                    <li>Không chia sẻ mật khẩu của bạn với bất kỳ ai</li>
                    <li>Sử dụng kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                    <li>Thay đổi mật khẩu định kỳ 3-6 tháng một lần</li>
                </ol>
            </div>

            <div class="security-note">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <strong>Quan trọng:</strong> Nếu bạn không yêu cầu đặt lại mật khẩu,
                    vui lòng bỏ qua email này và liên hệ ngay với bộ phận hỗ trợ của chúng tôi.
                </div>
            </div>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} M A G. Tất cả quyền được bảo lưu.</p>
            <p>
                <a href="#" style="color: #6c757d; text-decoration: none; margin: 0 10px;">Trợ giúp</a> |
                <a href="#" style="color: #6c757d; text-decoration: none; margin: 0 10px;">Chính sách bảo mật</a> |
                <a href="#" style="color: #6c757d; text-decoration: none; margin: 0 10px;">Liên hệ</a>
            </p>
            <p style="margin-top: 10px; font-size: 12px; color: #adb5bd;">
                Đây là email tự động, vui lòng không trả lời.
            </p>
        </div>
    </div>
</body>

</html>