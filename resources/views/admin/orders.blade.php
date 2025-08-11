@extends('admin.app')

@section('admin.body')
<link rel="stylesheet" href="{{ asset('/css/admin/orders.css') }}">

<div class="aorders-main-content">
    <div class="aorders-header">
        <div class="aorders-search-bar">
            <form action="{{ route('admin.orders.index') }}" method="GET" id="search-form">
                <i class="fas fa-search"></i>
                <input type="text" name="search" id="order-search"
                       placeholder="Tìm kiếm tên khách hàng, số điện thoại..."
                       value="{{ request('search') }}" />
                <button type="submit" style="display: none;">Tìm kiếm</button>
            </form>
        </div>
        <div class="aorders-user-profile">
            <div class="aorders-notification-bell">
                <i class="fas fa-bell"></i>
            </div>
            <div class="aorders-profile-avatar">QT</div>
        </div>
    </div>

    <h1 class="aorders-page-title">Quản lý đơn hàng</h1>
    <p class="aorders-page-subtitle">Theo dõi và xử lý các đơn hàng của cửa hàng</p>

    <div class="aorders-filter-group">
        <form action="{{ route('admin.orders.index') }}" method="GET" id="status-filter-form">
            <label for="status-filter">Lọc theo trạng thái:</label>
            <select name="status" id="status-filter" class="aorders-form-control" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Tất cả trạng thái</option>
                <option value="Chờ xác nhận" {{ request('status') == 'Chờ xác nhận' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="Đã xác nhận" {{ request('status') == 'Đã xác nhận' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="Đang giao hàng" {{ request('status') == 'Đang giao hàng' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="Thành công" {{ request('status') == 'Thành công' ? 'selected' : '' }}>Thành công</option>
                <option value="Đã hủy" {{ request('status') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                <option value="Hoàn hàng" {{ request('status') == 'Hoàn hàng' ? 'selected' : '' }}>Hoàn hàng</option>
            </select>

            <label for="start_date">Từ ngày:</label>
            <input type="date" name="start_date" id="start_date" class="aorders-form-control"
                   value="{{ request('start_date') }}" onchange="this.form.submit()" />

            <label for="end_date">Đến ngày:</label>
            <input type="date" name="end_date" id="end_date" class="aorders-form-control"
                   value="{{ request('end_date') }}" onchange="this.form.submit()" />

            <button type="submit" style="display: none;">Lọc</button>
        </form>
    </div>

    <div class="aorders-data-card">
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

        <!-- Debug: Kiểm tra dữ liệu orders (bỏ chú thích nếu cần) -->
        {{-- <pre>{{ print_r($orders->toArray(), true) }}</pre> --}}

        <table class="aorders-data-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="order-table-body">
                @foreach ($orders as $order)
                    @php
                        $statusColors = [
                            'Chờ xác nhận' => 'gray',
                            'Đã xác nhận' => 'blue',
                            'Đang giao hàng' => 'orange',
                            'Thành công' => 'green',
                            'Đã hủy' => 'red',
                            'Hoàn hàng' => 'purple',
                        ];
                        $color = $statusColors[$order->status] ?? 'dark';
                        // Lấy tên khách hàng
                        $customerName = !empty($order->address_details['receiver_name']) ? $order->address_details['receiver_name'] : 'Không xác định';
                        // Lấy số điện thoại
                        $phone = !empty($order->address_details['phone']) ? $order->address_details['phone'] : 'Không xác định';
                    @endphp
                    <tr data-order-id="#DH-{{ $order->id }}">
                        <td>#DH-{{ $order->id }}</td>
                        <td>{{ $customerName }}</td>
                        <td>{{ $phone }}</td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                        <td>
                            @if ($order->status == 'Đã hủy' || $order->status == 'Thành công')
                                <span class="aorders-status-badge status-{{ $color }}">
                                    {{ $order->status }}
                                </span>
                            @else
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="status-update-form">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="aorders-status-select status-{{ $color }}" onchange="this.form.submit()">
                                        <option value="Chờ xác nhận" {{ $order->status == 'Chờ xác nhận' ? 'selected' : '' }}>Chờ xác nhận</option>
                                        <option value="Đã xác nhận" {{ $order->status == 'Đã xác nhận' ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="Đang giao hàng" {{ $order->status == 'Đang giao hàng' ? 'selected' : '' }}>Đang giao hàng</option>
                                        <option value="Thành công" {{ $order->status == 'Thành công' ? 'selected' : '' }}>Thành công</option>
                                        <option value="Đã hủy" {{ $order->status == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="Hoàn hàng" {{ $order->status == 'Hoàn hàng' ? 'selected' : '' }}>Hoàn hàng</option>
                                    </select>
                                </form>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.orders.softDelete', $order->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="aorders-btn aorders-btn-delete" {{ $order->status != 'Đã hủy' ? 'disabled' : '' }}>
                                    Xóa
                                </button>
                            </form>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="aorders-btn aorders-btn-primary">Xem</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aorders-pagination" id="pagination"></div>
    </div>
</div>

<div class="aorders-toast" id="toast"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.querySelector("#order-search");
        const tableBody = document.querySelector("#order-table-body");
        let rows = Array.from(tableBody.querySelectorAll("tr"));
        const itemsPerPage = 6;
        let currentPage = 1;

        function filterRows() {
            const searchTerm = searchInput.value.toLowerCase();
            rows = Array.from(tableBody.querySelectorAll("tr")).filter((row) => {
                const customer = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                const phone = row.querySelector("td:nth-child(3)").textContent.toLowerCase();
                const status = row.querySelector("td:nth-child(6)").textContent.toLowerCase();
                return customer.includes(searchTerm) || phone.includes(searchTerm) || status.includes(searchTerm);
            });
            currentPage = 1;
            renderPage();
            renderPagination();
        }

        function renderPage() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            rows.forEach((row, index) => {
                row.style.display = index >= start && index < end ? "" : "none";
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(rows.length / itemsPerPage);
            const pagination = document.getElementById("pagination");
            pagination.innerHTML = "";

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement("button");
                btn.className = `aorders-pagination-btn ${i === currentPage ? "active" : ""}`;
                btn.textContent = i;
                btn.addEventListener("click", () => {
                    currentPage = i;
                    renderPage();
                    document.querySelectorAll(".aorders-pagination-btn").forEach((b) => b.classList.remove("active"));
                    btn.classList.add("active");
                });
                pagination.appendChild(btn);
            }
        }

        searchInput.addEventListener("input", filterRows);
        renderPage();
        renderPagination();
    });
</script>
@endsection