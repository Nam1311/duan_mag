@extends('admin.app')

@section('admin.body')
    <link rel="stylesheet" href="{{ asset('/css/admin/products.css') }}">

    @if ($errors->any())
        @php
            session()->flash('error', $errors->first());
        @endphp
    @endif

    <style>
        /* CSS cho các trạng thái */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-active {
            background-color: #e6f7ee;
            color: #10b759;
            border: 1px solid #10b759;
        }

        .status-expired {
            background-color: #feeceb;
            color: #f04438;
            border: 1px solid #f04438;
        }

        .status-upcoming {
            background-color: #fff8e6;
            color: #f79009;
            border: 1px solid #f79009;
        }

        /* CSS cho status  */
        .voucher-status-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .voucher-status-not-started {
            background-color: rgba(234, 179, 8, 0.1);
            color: #ca8a04;
        }

        .voucher-status-running {
            background-color: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }

        .voucher-status-ended {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
    </style>
    <div class="apromotions-main-content">
        <div class="aindex-header">
            <div class="aindex-search-bar">
                <form action="{{ route('admin.vouchers.search') }}" method="GET">
                    <button style="background-color: rgba(255, 255, 255, 0); border: none; cursor: pointer;" type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <input type="text" name="keyword" id="searchInput" placeholder="Tìm kiếm mã voucher..." value="{{ request('keyword') }}" />



                </form>
            </div>
            <div></div>
            <div class="aindex-user-profile">
                <div class="aindex-notification-bell">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="aindex-profile-avatar">QT</div>
            </div>
        </div>
        <h1 class="apromotions-page-title">Quản lý khuyến mãi</h1>
        <p class="aindex-dashboard-subtitle">
            Tạo và quản lý các voucher của shop.
        </p>

{{-- <form method="GET" action="" class="amanagementimg-filter-bar">
            <div class="amanagementimg-filter-bar">
                <select name="category" class="amanagementimg-filter-select">
                    <option value="">Tất cả danh mục</option>

                        <option value="ff" >

                </select>

                <select name="count" class="amanagementimg-filter-select" id="amanagementimg-imageCountFilter">
                    <option value="">Số lượng hình ảnh</option>
                    <option value="1">1 hình</option>
                    <option value="2">2 hình</option>
                    <option value="3">3 hình</option>
                    <option value="4">4 hình</option>
                    <option value="5">5 hình</option>
                </select>
                <button style="background-color: rgb(229, 115, 115)" class="amanagementimg-filter-select" type="submit">Lọc</button>
            </div>
        </form> --}}

        <div class="apromotions-actions-container">
            <button class="apromotions-btn apromotions-btn-primary"
                onclick="document.getElementById('createModal').style.display='flex'">
                Tạo khuyến mãi
            </button>
        </div>
        <table class="apromotions-promotion-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã khuyến mãi</th>
                    <th>Giảm giá</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                    {{-- Xử lý cái kết thúc --}}
                    {{-- @php
                        $now = now();
                        //gt
                        if ($now->lt($voucher->start_date)) {
                            $voucher->status = 'Chưa bắt đầu';
                        } elseif ($now->between($voucher->start_date, $voucher->expiration_date)) {
                            $voucher->status = 'Đang chạy';
                        } else {
                            $voucher->status = 'Đã kết thúc';
                        }
                    @endphp --}}

                    @php
                        $now = now();

                        if ($now->lt($voucher->start_date)) {
                            $statusClass = 'voucher-status-not-started';
                            $statusText = 'Chưa bắt đầu';
                        } elseif ($now->between($voucher->start_date, $voucher->expiration_date)) {
                            $statusClass = 'voucher-status-running';
                            $statusText = 'Đang chạy';
                        } else {
                            $statusClass = 'voucher-status-ended';
                            $statusText = 'Đã kết thúc';
                        }
                    @endphp


                    <tr>
                        <td>{{ $voucher->id }}</td>
                        <td>{{ $voucher->code }}</td>
                        <td>{{ number_format($voucher->discount_amount) }}
                            {{ $voucher->value_type == 'percent' ? '%' : 'VND' }}</td>
                        <td>{{ $voucher->start_date }}</td>
                        <td>{{ $voucher->expiration_date }}</td>
                        <td>{{ $voucher->quantity }}</td>
                        <td class="status-cell">
                            <span class="voucher-status-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="actions">
                            <button class="apromotions-btn apromotions-btn-primary btn-edit" data-id="{{ $voucher->id }}"
                                data-code="{{ $voucher->code }}" data-amount="{{ $voucher->discount_amount }}"
                                data-type="{{ $voucher->value_type }}"
                                data-start="{{ \Carbon\Carbon::parse($voucher->start_date)->format('Y-m-d\TH:i') }}"
                                data-end="{{ \Carbon\Carbon::parse($voucher->expiration_date)->format('Y-m-d\TH:i') }}"
                                data-quantity="{{ $voucher->quantity }}">
                                <i class="fas fa-edit"></i> Sửa
                            </button>


                            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa voucher này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="apromotions-btn apromotions-btn-danger delete-btn"
                                    data-id="1">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Phân trang --}}
        <div class="products-pagination" id="products-pagination">
            @for ($i = 1; $i <= $vouchers->lastPage(); $i++)
                <a href="{{ $vouchers->url($i) }}"
                    class="products-pagination-btn {{ $vouchers->currentPage() == $i ? 'adnews-active' : '' }}">
                    {{ $i }}
                </a>
            @endfor
        </div>

    </div>




    <!-- Popup Edit cho từng voucher -->
    <div class="apromotions-modal" id="editModal" style="display: none;">
        <div class="apromotions-modal-content">
            <span class="apromotions-modal-close" onclick="closeEditModal()">×</span>
            <h2>Chỉnh sửa khuyến mãi</h2>
            <form id="editForm" method="POST" onsubmit="return validateForm(this)">
                @csrf
                @method('PUT')
                <div class="apromotions-form-group">
                    <label>Mã khuyến mãi</label>
                    <input type="text" name="code" id="edit-code" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Giá trị giảm giá</label>
                    <input type="number" name="discount_amount" id="edit-discount" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Loại giảm giá</label>
                    <select name="value_type" id="edit-type" required>
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Giá trị cố định (VND)</option>
                    </select>
                </div>
                <div class="apromotions-form-group">
                    <label>Ngày bắt đầu</label>
                    <input type="datetime-local" name="start_date" id="edit-start" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Ngày kết thúc</label>
                    <input type="datetime-local" name="expiration_date" id="edit-end" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" id="edit-quantity" required max="9999999999">
                </div>
                <div class="apromotions-modal-actions">
                    <button type="submit" class="apromotions-btn apromotions-btn-primary">Lưu</button>
                    <button type="button" class="apromotions-btn apromotions-btn-secondary"
                        onclick="closeEditModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>




    <!-- Popup Create -->
    <div class="apromotions-modal" id="createModal">
        <div class="apromotions-modal-content">
            <span class="apromotions-modal-close"
                onclick="document.getElementById('createModal').style.display='none'">×</span>
            <h2>Tạo khuyến mãi mới</h2>
            <form method="POST" action="{{ route('admin.vouchers.store') }}" onsubmit="return validateForm(this)">
                @csrf
                <div class="apromotions-form-group">
                    <label>Mã khuyến mãi</label>
                    <input type="text" name="code" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Giá trị giảm giá</label>
                    <input type="number" name="discount_amount" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Loại giảm giá</label>
                    <select name="value_type" required>
                        <option value="fixed">Giá trị cố định (VND)</option>
                        <option value="percent">Phần trăm (%)</option>
                    </select>

                </div>
                <div class="apromotions-form-group">
                    <label>Ngày bắt đầu</label>
                    <input type="datetime-local" name="start_date" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Ngày kết thúc</label>
                    <input type="datetime-local" name="expiration_date" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" required max="9999999999">
                </div>
                <div class="apromotions-modal-actions">
                    <button type="submit" class="apromotions-btn apromotions-btn-primary">Tạo</button>
                    <button type="button" class="apromotions-btn apromotions-btn-secondary"
                        onclick="document.getElementById('createModal').style.display='none'">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>



    <script>
        const editButtons = document.querySelectorAll('.btn-edit');
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const code = this.dataset.code;
                const amount = this.dataset.amount;
                const type = this.dataset.type;
                const start = this.dataset.start;
                const end = this.dataset.end;
                const quantity = this.dataset.quantity;

                // Gán dữ liệu vào form sửa
                editForm.action = `/admin/vouchers/${id}`;
                document.getElementById('edit-code').value = code;
                document.getElementById('edit-discount').value = amount;
                document.getElementById('edit-type').value = type;
                document.getElementById('edit-start').value = start;
                document.getElementById('edit-end').value = end;
                document.getElementById('edit-quantity').value = quantity;

                editModal.style.display = 'flex';
            });
        });

        function closeEditModal() {
            editModal.style.display = 'none';
        }

        // Hàm validate dùng chung
        function validateForm(form) {
            const code = form.code.value.trim();
            const discount = form.discount_amount.value;
            const type = form.value_type.value;
            const startDate = form.start_date.value;
            const endDate = form.expiration_date.value;
            const quantity = form.quantity.value;

            if (!code) {
                alert('Vui lòng nhập mã khuyến mãi');
                return false;
            }

            if (!discount || isNaN(discount) || parseFloat(discount) <= 0) {
                alert('Giá trị giảm giá phải là số dương');
                return false;
            }

            if (type === 'percent' && parseFloat(discount) > 100) {
                alert('Giảm giá theo phần trăm không được vượt quá 100%');
                return false;
            }

            if (!startDate || !endDate) {
                alert('Vui lòng chọn ngày bắt đầu và kết thúc');
                return false;
            }

            if (new Date(endDate) < new Date(startDate)) {
                alert('Ngày kết thúc phải sau ngày bắt đầu');
                return false;
            }

            if (!quantity || parseInt(quantity) <= 0) {
                alert('Số lượng phải lớn hơn 0');
                return false;
            }

            return true;
        }
    </script>
@endsection
