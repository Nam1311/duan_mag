<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cập nhật trạng thái đơn hàng</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
            color: #333;
        }

        .container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 550px;
            margin: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-top: 5px solid #9c27b0;
        }

        h2 {

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, #a1ffce, #faffd1);
            margin: 15px 0;
        }

        .status-badge {
            padding: 8px 14px;
            border-radius: 20px;
            color: #fff;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .status-old {
            background-color: #607d8b;
        }

        .status-success {
            background-color: #4caf50;
        }

        .status-confirmed {
            background-color: #2196f3;
        }

        .status-cancelled {
            background-color: #f44336;
        }

        .icon {
            font-size: 16px;
        }

        .footer {
            margin-top: 20px;
            font-size: 13px;
            color: #777;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Xin chào {{ $order->user->name ?? 'Khách hàng' }},</h2>
        <p>🛒 Đơn hàng <strong>#{{ $order->id }}</strong> của bạn đã được cập nhật:</p>

        <p><strong>Trạng thái cũ:</strong>
            <span class="status-badge status-old">
                <span class="icon">⏳</span>{{ $oldStatus }}
            </span>
        </p>

        <p><strong>Trạng thái mới:</strong>
            @php
                $statusClass = '';
                $icon = '';
                if ($newStatus === 'Thành công') {
                    $statusClass = 'status-success';
                    $icon = '✅';
                } elseif ($newStatus === 'Đã xác nhận') {
                    $statusClass = 'status-confirmed';
                    $icon = '📦';
                } elseif ($newStatus === 'Hủy') {
                    $statusClass = 'status-cancelled';
                    $icon = '❌';
                } else {
                    $statusClass = 'status-old';
                    $icon = 'ℹ️';
                }
            @endphp
            <span class="status-badge {{ $statusClass }}">
                <span class="icon">{{ $icon }}</span>{{ $newStatus }}
            </span>
        </p>

        <div class="divider"></div>

        <p>Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi! 💖</p>

        <div class="footer">
            Đây là email tự động, vui lòng không trả lời trực tiếp.
        </div>
    </div>
</body>

</html>
