@extends('admin.app')
<style>
    .acustomermanagement-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .acustomermanagement-search-bar input {
        padding: 8px 12px;
        width: 280px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .acustomermanagement-subheader {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .acustomermanagement-page-title {
        font-size: 26px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }

    .acustomermanagement-filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .acustomermanagement-filter-actions select,
    .acustomermanagement-filter-actions button {
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    /* Nâng cấp modal form */
    .acustomermanagement-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .acustomermanagement-modal-content {
        background: #fff;
        width: 100%;
        max-width: 550px;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        max-height: 90vh;
        overflow-y: auto;
    }

    .acustomermanagement-modal-content h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .acustomermanagement-form-group {
        margin-bottom: 15px;
    }

    .acustomermanagement-form-group label {
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
        color: #333;
    }

    .modal-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s;
    }

    .modal-input:focus {
        border-color: #409eff;
        outline: none;
    }

    .acustomermanagement-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 25px;
    }
</style>

@section('admin.body')
    <link rel="stylesheet" href="{{ asset('css/admin/quanlykhachhang.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="acustomermanagement-main-content">
        <div class="acustomermanagement-header">
            <div class="acustomermanagement-search-bar">
                <input type="text" id="searchInput" placeholder="Tìm theo tên, email, mã KH...">
            </div>

            <div class="ausermanagement-user-profile">
                <div class="ausermanagement-notification-bell"><i class="fas fa-bell"></i></div>
                <div class="ausermanagement-profile-avatar">QT</div>
            </div>
        </div>

        <h1 class="acustomermanagement-page-title">Quản lý khách hàng</h1>

        <div class="acustomermanagement-subheader">
            <div class="acustomermanagement-filter-actions">
                <select id="activityFilter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1">Hoạt động</option>
                    <option value="0">Tạm khóa</option>
                </select>

                <button id="sendMailBtn" class="acustomermanagement-btn acustomermanagement-btn-secondary">
                    📧 Gửi tin nhắn
                </button>

                <button id="addCustomerBtn" class="acustomermanagement-btn acustomermanagement-btn-primary">
                    + Thêm khách hàng
                </button>
            </div>
        </div>

        <div class="acustomermanagement-data-card">
            <table class="acustomermanagement-data-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll" /></th>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Trạng thái</th>
                        <th>Kích hoạt</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    @foreach ($customers as $user)
                        <tr>
                            <td><input type="checkbox" class="user-checkbox" value="{{ $user->id }}"></td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if ($user->is_locked)
                                    <span class="acustomermanagement-status-badge acustomermanagement-status-active">Hoạt
                                        động</span>
                                @else
                                    <span class="acustomermanagement-status-badge acustomermanagement-status-inactive">Tạm
                                        khóa</span>
                                @endif
                            </td>
                            <td>{{ $user->is_active ? 'Đã kích hoạt' : 'Chưa kích hoạt' }}</td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <button onclick="viewUser({{ $user->id }})"
                                    class="acustomermanagement-btn acustomermanagement-btn-secondary">Xem</button>
                                <button onclick="editUser({{ $user->id }})"
                                    class="acustomermanagement-btn acustomermanagement-btn-primary">Sửa</button>
                                <button onclick="deleteUser({{ $user->id }})"
                                    class="acustomermanagement-btn acustomermanagement-btn-danger">Xoá</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="acustomermanagement-pagination mt-3">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal gửi tin nhắn -->
    <div class="acustomermanagement-modal" id="sendMailModal" style="display: none">
        <div class="acustomermanagement-modal-content">
            <h2>Gửi tin nhắn</h2>
            <div class="acustomermanagement-form-group">
                <label>Chủ đề:</label>
                <input type="text" id="mailSubject" class="modal-input">
            </div>
            <div class="acustomermanagement-form-group">
                <label>Nội dung:</label>
                <textarea id="mailContent" class="modal-input" rows="6"></textarea>
            </div>
            <div class="acustomermanagement-modal-footer">
                <button id="sendMailSubmitBtn" class="acustomermanagement-btn acustomermanagement-btn-primary">Gửi</button>
                <button onclick="closeSendMailModal()"
                    class="acustomermanagement-btn acustomermanagement-btn-secondary">Đóng</button>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const activityFilter = document.getElementById('activityFilter');
        const tableBody = document.getElementById('customerTableBody');

        searchInput.addEventListener('input', filterTable);
        activityFilter.addEventListener('change', filterTable);

        function filterTable() {
            const keyword = searchInput.value.toLowerCase();
            const status = activityFilter.value;

            Array.from(tableBody.rows).forEach(row => {
                const id = row.cells[1].innerText.toLowerCase();
                const name = row.cells[2].innerText.toLowerCase();
                const email = row.cells[3].innerText.toLowerCase();
                const isActive = row.cells[5].innerText.includes('Hoạt');

                const matchSearch = id.includes(keyword) || name.includes(keyword) || email.includes(keyword);
                const matchStatus = status === "" || (status === "1" && isActive) || (status === "0" && !isActive);

                row.style.display = (matchSearch && matchStatus) ? "" : "none";
            });
        }

        // Check all
        document.getElementById("checkAll").addEventListener("click", function() {
            document.querySelectorAll(".user-checkbox").forEach(cb => cb.checked = this.checked);
        });

        // Gửi tin nhắn
        document.getElementById("sendMailBtn").addEventListener("click", () => {
            const selected = Array.from(document.querySelectorAll(".user-checkbox:checked")).map(cb => cb.value);
            if (selected.length === 0) return alert("Vui lòng chọn người nhận.");
            document.getElementById("sendMailModal").style.display = "flex";
        });

        function closeSendMailModal() {
            document.getElementById("sendMailModal").style.display = "none";
        }

        document.getElementById("sendMailSubmitBtn").addEventListener("click", () => {
            const selectedIds = Array.from(document.querySelectorAll(".user-checkbox:checked")).map(cb => cb.value);
            const subject = document.getElementById("mailSubject").value;
            const content = document.getElementById("mailContent").value;

            if (!subject || !content) return alert("Vui lòng nhập đủ chủ đề và nội dung");

            fetch(`/admin/send-bulk-mail`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ids: selectedIds,
                        subject,
                        content
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message || "Gửi thành công!");
                    closeSendMailModal();
                })
                .catch(() => alert("Gửi thất bại"));
        });

        function viewUser(id) {
            fetch(`/admin/khachhang/${id}`)
                .then(res => res.json())
                .then(data => {
                    openModal(data, true);
                });
        }

        function editUser(id) {
            fetch(`/admin/khachhang/${id}`)
                .then(res => res.json())
                .then(data => {
                    openModal(data, false);
                });
        }

        function deleteUser(id) {
            if (!confirm("Bạn chắc chắn muốn xóa khách hàng này?")) return;

            fetch(`/admin/khachhang/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
        }
    </script>
@endsection
