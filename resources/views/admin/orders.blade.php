@extends('admin.app')

@section('admin.body')
    <link rel="stylesheet" href="{{ asset('/css/admin/orders.css') }}">
    <div class="aorders-main-content">
        <div class="aorders-header">
            <div class="aorders-search-bar">
                <form action="{{ route('admin.orders.index') }}" method="GET" id="search-form">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" id="order-search" placeholder="Tìm kiếm tên khách hàng, số điện thoại..." value="{{ request('search') }}" />
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
        <p class="aorders-page-subtitle">
            Theo dõi và xử lý các đơn hàng của cửa hàng
        </p>
        <!-- Form lọc trạng thái và ngày -->
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
                <input type="date" name="start_date" id="start_date" class="aorders-form-control" value="{{ request('start_date') }}" onchange="this.form.submit()" />
                <label for="end_date">Đến ngày:</label>
                <input type="date" name="end_date" id="end_date" class="aorders-form-control" value="{{ request('end_date') }}" onchange="this.form.submit()" />
                <button type="submit" style="display: none;">Lọc</button>
            </form>
        </div>
        <div class="aorders-data-card">
            <!-- Hiển thị thông báo thành công hoặc lỗi -->
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
                            $customerName = $order->user->name ?? ($order->address->receiver_name ?? 'Không xác định');
                        @endphp
                        <tr data-order-id="#DH-{{ $order->id }}">
                            <td>#DH-{{ $order->id }}</td>
                            <td>{{ $customerName }}</td>
                            <td>{{ $order->address->phone ?? 'Không xác định' }}</td>
                            <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                            <td>{{ $order->created_at->format('d-m-Y') }}</td>
                            <td>
                                <span class="aorders-status-badge status-{{ $color }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>
                                <button class="aorders-btn aorders-btn-edit"
                                        onclick="openEditModal('#DH-{{ $order->id }}', '{{ $order->status }}', '{{ $customerName }}', '{{ number_format($order->total_price, 0, ',', '.') }}đ', '{{ $order->created_at->format('Y-m-d') }}', '{{ $order->address->phone ?? 'Không xác định' }}')"
                                        {{ $order->status == 'Đã hủy' || $order->status == 'Thành công' ? 'disabled' : '' }}>
                                    Sửa
                                </button>
                                <form action="{{ route('admin.orders.softDelete', $order->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="aorders-btn aorders-btn-delete" {{ $order->status == 'Thành công' ? 'disabled' : '' }}>
                                        Xóa
                                    </button>
                                </form>
                                <button class="aorders-btn aorders-btn-primary" onclick="viewOrder('{{ $order->id }}')">Xem</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aorders-pagination" id="pagination"></div>
        </div>
    </div>
    <!-- Modal chỉnh sửa trạng thái -->
    <div class="aorders-modal" id="editOrderModal">
        <div class="aorders-modal-content">
            <div class="aorders-modal-header">
                <h2 class="aorders-modal-title">
                    Chỉnh sửa trạng thái đơn hàng
                </h2>
                <span class="aorders-modal-close" onclick="closeEditModal()">×</span>
            </div>
            <form action="" method="POST" id="editOrderForm">
                @csrf
                @method('PUT')
                <div class="aorders-modal-body">
                    <p>
                        <strong>Mã đơn:</strong> <span id="modalOrderId"></span>
                    </p>
                    <p>
                        <strong>Khách hàng:</strong>
                        <span id="modalCustomer"></span>
                    </p>
                    <p>
                        <strong>Số điện thoại:</strong> <span id="modalPhone"></span>
                    </p>
                    <p>
                        <strong>Tổng tiền:</strong>
                        <span id="modalTotal"></span>
                    </p>
                    <p>
                        <strong>Ngày đặt:</strong> <span id="modalDate"></span>
                    </p>
                    <label for="orderStatus">Trạng thái:</label>
                    <select id="orderStatus" name="status" class="aorders-form-control">
                        <option value="Chờ xử lý">Chờ xử lý</option>
                        <option value="Đã xác nhận">Đã xác nhận</option>
                        <option value="Đang giao hàng">Đang giao hàng</option>
                        <option value="Thành công">Thành công</option>
                        <option value="Đã hủy">Đã hủy</option>
                    </select>
                    @error('status')
                        <span class="aorders-error">{{ $message }}</span>
                    @enderror
                    <input type="hidden" id="orderId" name="order_id" />
                </div>
                <div class="aorders-modal-footer">
                    <button type="submit" class="aorders-btn aorders-btn-primary">Lưu</button>
                    <button type="button" class="aorders-btn" onclick="closeEditModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Toast thông báo -->
    <div class="aorders-toast" id="toast"></div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Xử lý active sidebar
            const sidebarItems = document.querySelectorAll(".aorders-sidebar-item");
            sidebarItems.forEach((item) => {
                item.addEventListener("click", function(e) {
                    e.preventDefault();
                    sidebarItems.forEach((i) => i.classList.remove("aorders-active"));
                    this.classList.add("aorders-active");
                });
            });

            // Xử lý tìm kiếm
            const searchInput = document.querySelector("#order-search");
            const tableBody = document.querySelector("#order-table-body");
            let rows = tableBody.querySelectorAll("tr");

            searchInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase();
                rows = Array.from(tableBody.querySelectorAll("tr")).filter(
                    (row) => {
                        const customer = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                        const phone = row.querySelector("td:nth-child(3)").textContent.toLowerCase();
                        const status = row.querySelector("td:nth-child(6) span").textContent.toLowerCase();
                        return (
                            customer.includes(searchTerm) ||
                            phone.includes(searchTerm) ||
                            status.includes(searchTerm)
                        );
                    }
                );
                renderPage();
                renderPagination();
            });

            // Xử lý phân trang
            const itemsPerPage = 6;
            let currentPage = 1;

            function renderPagination() {
                const totalPages = Math.ceil(rows.length / itemsPerPage);
                const pagination = document.getElementById("pagination");
                pagination.innerHTML = "";

                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement("button");
                    btn.className = "aorders-pagination-btn" + (i === currentPage ? " active" : "");
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

            function renderPage() {
                rows.forEach((row, index) => {
                    const start = (currentPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;
                    row.style.display = index >= start && index < end ? "" : "none";
                });
            }

            renderPagination();
            renderPage();

            // Xử lý modal
            window.openEditModal = function(orderId, currentStatus, customer, total, date, phone) {
                const modal = document.getElementById("editOrderModal");
                const statusSelect = document.getElementById("orderStatus");
                const orderIdInput = document.getElementById("orderId");
                const form = document.getElementById("editOrderForm");
                document.getElementById("modalOrderId").textContent = orderId;
                document.getElementById("modalCustomer").textContent = customer;
                document.getElementById("modalPhone").textContent = phone;
                document.getElementById("modalTotal").textContent = total;
                document.getElementById("modalDate").textContent = date;
                orderIdInput.value = orderId.replace('#DH-', '');
                statusSelect.value = currentStatus;
                form.action = `/admin/orders/${orderIdInput.value}`;
                modal.classList.add("show");
            };

            window.closeEditModal = function() {
                const modal = document.getElementById("editOrderModal");
                modal.classList.remove("show");
            };

            window.viewOrder = function(orderId) {
                window.location.href = `/admin/orders/${orderId}`;
            };
        });
    </script>
@endsection
