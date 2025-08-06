<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #{{ $order->order_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .order-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            border: none;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .summary-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #dee2e6, transparent);
            margin: 1.5rem 0;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="order-header text-center">
            <h1 class="display-5 fw-bold">Chi tiết đơn hàng</h1>
            <p class="lead mb-0">Mã đơn hàng: <strong>#{{ $order->order_code }}</strong></p>
        </div>

        <div class="card order-card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                    <span class="status-badge bg-primary text-white">
                        {{ $order->status === 'completed' ? 'ĐÃ HOÀN THÀNH' : 'ĐANG XỬ LÝ' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Thông tin người nhận</h6>
                        <p class="mb-1"><strong>{{ $order->user->name }}</strong></p>
                        <p class="mb-1">{{ $order->phone }}</p>
                        <p class="mb-0 text-muted">{{ $order->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin đơn hàng</h6>
                        <p class="mb-1">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="mb-1">Phương thức thanh toán: {{ $order->payment_methods === 'COD' ? 'COD (Thanh toán khi nhận hàng)' : 'Chuyển khoản' }}</p>
                        <p class="mb-0">Ghi chú: {{ $order->note ?? 'Không có ghi chú' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="55%">Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $item)
                            @php
                                $product = $item->productVariant->product;
                                $unitPrice = $item->price ?? $product->price;
                                $itemTotal = $unitPrice * $item->quantity;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             class="product-image me-3">
                                        @endif
                                        <div>
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <p class="mb-0 text-muted small">Mã SP: {{ $product->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($unitPrice, 0, ',', '.') }}₫</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($itemTotal, 0, ',', '.') }}₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card order-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thông tin vận chuyển</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Đơn vị vận chuyển: Giao hàng (MAG)</p>
                        <p class="mb-0">Dự kiến giao hàng: {{ $order->created_at->addDays(3)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="summary-card">
                    <h5 class="mb-3">Tóm tắt thanh toán</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng tiền hàng:</span>
                        <span>{{ number_format($productsTotal, 0, ',', '.') }}₫</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span>40.000₫</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Giảm giá:</span>
                        <span class="text-danger">-{{ number_format($order->voucherDiscount ?? 0, 0, ',', '.') }}₫</span>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Tổng thanh toán:</span>
                        <span class="text-primary">{{ number_format($order->total_price, 0, ',', '.') }}₫</span>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-shopping-cart me-2"></i>Tiếp tục mua sắm
                        </a>
                        <button class="btn btn-outline-secondary px-4 py-2 ms-2">
                            <i class="fas fa-print me-2"></i>In hóa đơn
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="mt-5 text-center text-muted">
            <p>Nếu bạn cần hỗ trợ, vui lòng liên hệ <a href="mailto:support@mag.com">support@mag.com</a> hoặc 0962615032</p>
            <p class="mb-0">© 2023 M A G. Tất cả quyền được bảo lưu.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>