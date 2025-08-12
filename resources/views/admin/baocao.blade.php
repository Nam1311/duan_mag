@extends('admin.app')

@section('admin.body')

   <div class="areports-main-content">
            <div class="areports-header">
                <div class="areports-search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm báo cáo..." />
                </div>
                <div class="areports-user-profile">
                    <div class="areports-notification-bell">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="areports-profile-avatar">QT</div>
                </div>
            </div>
            <h1 class="areports-page-title">Báo cáo kinh doanh</h1>
            <p class="areports-page-subtitle">Phân tích hiệu suất kinh doanh</p>
            <div class="areports-filter-container">
                <form id="filter-form" action="{{ route('admin.reports.filter') }}" method="POST" class="areports-filter-form">
                   @csrf
                    <div class="areports-filter-group">
                        <label>Từ ngày</label>
                        <input type="date" id="startDate" name="startDate" value="2025-07-25" />
                    </div>
                    <div class="areports-filter-group">
                        <label>Đến ngày</label>
                        <input type="date" id="endDate" name="endDate" value="2025-07-25" />
                    </div>
                    <div class="areports-filter-group">
                        <label>Từ giờ</label>
                        <input type="time" id="startTime" name="startTime" value="17:23" />
                    </div>
                    <div class="areports-filter-group">
                        <label>Đến giờ</label>
                        <input type="time" id="endTime" name="endTime" value="23:59" />
                    </div>
                    <div class="areports-filter-group">
                        <label>Khoảng thời gian</label>
                        <select id="timeRange" name="timeRange">
                            <option value="all">Tất cả</option>
                            <option value="morning">Buổi sáng (6:00-12:00)</option>
                            <option value="afternoon">Buổi chiều (12:00-18:00)</option>
                            <option value="evening">Buổi tối (18:00-23:59)</option>
                        </select>
                    </div>
                    <div class="areports-filter-group">
                        <label>Loại báo cáo</label>
                        <select id="reportType" name="reportType">
                            <option value="all">Tất cả</option>
                            <option value="sales">Doanh thu</option>
                            <option value="products">Sản phẩm</option>
                            <option value="orders">Đơn hàng</option>
                        </select>
                    </div>
                    <div class="areports-filter-group">
                        <label>Danh mục</label>
                        <select id="category" name="category">
                            <option value="all">Tất cả</option>
                            <option value="Áo thun">Áo thun</option>
                            <option value="Quần jeans">Quần jeans</option>
                            <option value="Áo khoác">Áo khoác</option>
                            <option value="Giày">Giày</option>
                            <option value="Váy">Váy</option>
                            <option value="Phụ kiện">Phụ kiện</option>
                        </select>
                    </div>
                    <div class="areports-filter-group">
                        <label> </label>
                        <button type="submit" class="areports-btn areports-btn-primary" id="applyFilter">Áp dụng</button>
                    </div>
                </form>
            </div>
            <div class="areports-summary">
                <div class="areports-summary-card">
                    <h4>Tổng doanh thu</h4>
                    <p id="totalRevenue">{{ number_format($totalRevenue, 0, ',', '.') }}VNĐ</p>
                </div>
                {{-- <div class="areports-summary-card">
                    <h4>Tổng lợi nhuận</h4>
                    <p id="totalRevenue">{{$totalProfit}} VNĐ</p>
                </div> --}}
                <div class="areports-summary-card">
                    <h4>Tổng đơn hàng</h4>
                    <p id="totalOrders">{{$totalDetails}}</p>
                </div>
            </div>
            <div class="areports-chart-card">
                <table class="areports-report-table" id="productTable">
                    <thead>
                        <tr>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Tên khách hàng</th>
                            <th>Số lượng bán</th>
                            <th>Giá bán (VNĐ)</th>
                            <th>Doanh thu (VNĐ)</th>
                            <th>Thời gian bán</th>
                        </tr>
                    </thead>
                    <tbody id="productList">
    @foreach ($orders as $order)
        @foreach ($order->orderDetails as $detail)
            <tr>
                {{-- Mã sản phẩm --}}
                <td>{{ $detail->productVariant->product->sku }}</td>

                {{-- Tên sản phẩm --}}
                <td>{{ $detail->productVariant->product->name }}</td>

                {{-- Tên khách hàng: ưu tiên user đăng nhập, nếu không thì lấy từ địa chỉ giao hàng --}}
                <td>{{ $order->user?->name ?? $order->shippingAddress?->receiver_name }}</td>

                {{-- Số lượng --}}
                <td>{{ $detail->quantity }}</td>

                {{-- Giá bán --}}
                <td>{{ number_format($detail->productVariant->product->price, 0, ',', '.') }}đ</td>

                {{-- Doanh thu = đơn giá x số lượng --}}
                <td>{{ number_format($detail->productVariant->product->price * $detail->quantity, 0, ',', '.') }}đ</td>

                {{-- Thời gian bán --}}
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
    @endforeach
</tbody>


                </table>
                <div class="areports-pagination" id="pagination"></div>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Lấy tất cả các dòng sản phẩm trong bảng có id là "productList"
        const rows = document.querySelectorAll('#productList tr');

        // Phần tử phân trang
        const pagination = document.getElementById('pagination');

        // Số sản phẩm hiển thị trên mỗi trang
        const itemsPerPage = 10;

        // Trang hiện tại
        let currentPage = 1;

        // Tổng số trang dựa vào tổng số dòng sản phẩm
        const totalPages = Math.ceil(rows.length / itemsPerPage);

        // ============ Responsive cho sidebar (nếu sidebar vẫn được dùng để hiển thị theo kích thước màn hình) ============
        function handleSidebarResponsive() {
            const sidebar = document.querySelector('.adbl-sidebar'); // Tìm sidebar
            if (!sidebar) return;

            if (window.innerWidth <= 768) {
                sidebar.style.width = '100%';
                sidebar.style.height = 'auto';
                sidebar.style.position = 'relative';
            } else if (window.innerWidth <= 1024) {
                sidebar.style.width = '80px';
            } else {
                sidebar.style.width = '280px';
            }
        }

        // Gọi hàm responsive khi resize trình duyệt
        window.addEventListener('resize', handleSidebarResponsive);
        // Gọi lần đầu khi trang vừa tải
        handleSidebarResponsive();

        // ============ Logic phân trang ============

        // Hiển thị các dòng tương ứng với trang hiện tại
        function showPage(page) {
            currentPage = page;

            rows.forEach((row, index) => {
                row.classList.remove('visible');

                if (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) {
                    row.classList.add('visible');
                }
            });

            renderPagination();
        }

        // Tạo các nút phân trang
        function renderPagination() {
            pagination.innerHTML = '';

            let startPage = Math.max(1, currentPage - 1);
            let endPage = Math.min(totalPages, currentPage + 1);

            if (currentPage <= 2) {
                endPage = Math.min(3, totalPages);
            } else if (currentPage >= totalPages - 1) {
                startPage = Math.max(1, totalPages - 2);
            }

            for (let i = startPage; i <= endPage; i++) {
                const button = document.createElement('a');
                button.href = '#';
                button.className = `areports-pagination-btn ${i === currentPage ? 'active' : ''}`;
                button.textContent = i;

                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    showPage(i);
                });

                pagination.appendChild(button);
            }
        }

        // Hiển thị trang đầu tiên khi tải trang
        showPage(1);
    });
</script>


@endsection
