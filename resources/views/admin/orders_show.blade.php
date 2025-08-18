@extends('admin.app')

@section('admin.body')
    <style>
        @media print {
            .aorders-header, .aorders-actions, .aorders-toast, .aorders-sidebar-item {
                display: none !important;
            }
            .aorders-main-content {
                padding: 0;
                margin: 0;
                width: 100%;
            }
            .aorders-data-card {
                border: none;
                box-shadow: none;
            }
            .aorders-page-title, .aorders-page-subtitle {
                text-align: center;
            }
            .aorders-order-details, .aorders-order-items {
                page-break-inside: avoid;
            }
            .aorders-data-table {
                width: 100%;
                border-collapse: collapse;
            }
            .aorders-data-table th, .aorders-data-table td {
                border: 1px solid #000;
                padding: 8px;
            }
        }
    </style>

    <div class="aorders-main-content">
        <div class="aorders-header">
            <div class="aorders-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="order-search" placeholder="Tìm kiếm mã đơn, khách hàng, trạng thái..." />
            </div>
            <div class="aorders-user-profile">
                <div class="aorders-notification-bell">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="aorders-profile-avatar">QT</div>
            </div>
        </div>
        <h1 class="aorders-page-title">Chi tiết đơn hàng: {{ $order->order_code }}</h1>
        <p class="aorders-page-subtitle">
            Thông tin chi tiết về đơn hàng và sản phẩm
        </p>

        @if (session('success'))
            <div class="aorders-toast aorders-toast-success show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="aorders-toast aorders-toast-error show">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="aorders-data-card">
            <div class="aorders-order-details">
                <h2>Thông tin đơn hàng</h2>
                <p><strong>Mã đơn:</strong> {{ $order->order_code }}</p>
                <p><strong>Khách hàng:</strong>
                    {{ !empty($order->address_details['receiver_name']) ? $order->address_details['receiver_name'] : ($order->name ?? ($order->user->name ?? 'Không xác định')) }}
                </p>
                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d-m-Y') }}</p>
                <p><strong>Trạng thái:</strong>
                    <span class="aorders-status-badge {{ $order->status == 'Đã hủy' ? 'aorders-status-inactive' : 'aorders-status-active' }}">
                        {{ $order->status }}
                    </span>
                </p>
                <p><strong>Số điện thoại:</strong>
                    {{ !empty($order->address_details['phone']) ? $order->address_details['phone'] : ($order->phone ?? ($order->user->phone ?? 'N/A')) }}
                </p>
                @if ($order->address_details && $order->address_details['province_name'] !== 'Không xác định')
                    <p><strong>Địa chỉ:</strong>
                        {{ $order->address_details['address'] . ', ' .
                           $order->address_details['ward_name'] . ', ' .
                           $order->address_details['district_name'] . ', ' .
                           $order->address_details['province_name'] }}
                    </p>
                @else
                    <p><strong>Địa chỉ:</strong> {{ $order->address ?? 'Không xác định' }}</p>
                @endif
            </div>

            <div class="aorders-order-items">
                <h2>Sản phẩm trong đơn hàng</h2>
                <table class="aorders-data-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Size</th>
                            <th>Màu sắc</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                            <tr>
                                <td>
                                    <img src="{{ asset($detail->productVariant->product->images->first()->path ?? '/images/placeholder.png') }}"
                                        alt="{{ $detail->productVariant->product->name ?? 'Không xác định' }}"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>{{ $detail->productVariant->product->name ?? 'Không xác định' }}</td>
                                <td>{{ $detail->productVariant->size ? $detail->productVariant->size->name : 'N/A' }}</td>
                                <td>{{ $detail->productVariant->color ? $detail->productVariant->color->name : 'N/A' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ number_format($detail->productVariant->product->price ?? 0, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px; text-align: right;">
                <div style="display: inline-block; padding: 10px 20px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; font-weight: bold;">
                    Tổng tiền đơn hàng: {{ number_format($order->total_price, 0, ',', '.') }}đ
                </div>
            </div>

            <div class="aorders-actions">
                <a href="{{ route('admin.orders.index') }}" class="aorders-btn aorders-btn-primary">Quay lại</a>
                <button onclick="window.print()" class="aorders-btn aorders-btn-primary">In</button>
            </div>

            <div class="aorders-toast" id="toast"></div>
        </div>

        <script src="/js/app.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const sidebarItems = document.querySelectorAll(".aorders-sidebar-item");
                sidebarItems.forEach((item) => {
                    item.addEventListener("click", function(e) {
                        e.preventDefault();
                        sidebarItems.forEach((i) => i.classList.remove("aorders-active"));
                        this.classList.add("aorders-active");
                    });
                });

                const toasts = document.querySelectorAll('.aorders-toast.show');
                toasts.forEach(toast => {
                    setTimeout(() => toast.classList.remove('show'), 3000);
                });
            });
        </script>
    </div>
@endsection
