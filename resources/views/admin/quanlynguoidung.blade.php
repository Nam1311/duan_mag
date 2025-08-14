@extends('admin.app')
<style>
    .d-flex.justify-content-between.flex-fill.d-sm-none {
        display: none !important;
    }

    .acustomermanagement-phantrang nav {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .acustomermanagement-phantrang .pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
    }

    .acustomermanagement-phantrang .pagination li {
        display: inline-block;
    }

    .acustomermanagement-phantrang .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 6px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .acustomermanagement-phantrang .pagination .page-link:hover {
        background-color: #f0f0f0;
    }

    .acustomermanagement-phantrang .pagination .active .page-link {
        background-color: #4f46e5;
        color: #fff;
        border-color: #4f46e5;
    }

    .acustomermanagement-phantrang .pagination .disabled .page-link {
        color: #ccc;
        pointer-events: none;
        background-color: #f9f9f9;
    }
</style>
@section('admin.body')
    {{-- <link rel="stylesheet" href="{{ asset('css/admin/quanlynguoidung.css') }}"> --}}

    <div class="ausermanagement-main-content">
        <div class="ausermanagement-header">
            <!-- Đặt trong phần header hoặc nơi bạn muốn -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="ausermanagement-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="email" class="form-control" id="ausermanagement-searchInput"
                    placeholder="Tìm kiếm email..." value="{{ request('email') }}" />
                <button type="submit" class="ausermanagement-btn ausermanagement-btn-primary">Tìm kiếm</button>
            </form>

            <div class="ausermanagement-user-profile">
                <div class="ausermanagement-notification-bell">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="ausermanagement-profile-avatar">QT</div>
            </div>
        </div>

        <h1 class="ausermanagement-page-title">Quản lý người dùng</h1>
        <p class="ausermanagement-page-subtitle">Quản lý tài khoản nhân viên và quyền truy cập</p>

        <!-- Bộ lọc -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="ausermanagement-filter-bar" id="filterForm">
            <select class="ausermanagement-filter-select" name="role"
                onchange="document.getElementById('filterForm').submit()">
                <option value="">Tất cả vai trò</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                <option value="news_manager" {{ request('role') == 'news_manager' ? 'selected' : '' }}>Quản lý tin tức
                </option>
                <option value="products_manager" {{ request('role') == 'products_manager' ? 'selected' : '' }}>Quản lý sản
                    phẩm</option>
                <option value="customer_service" {{ request('role') == 'customer_service' ? 'selected' : '' }}>Chăm sóc
                    khách hàng</option>
            </select>
            <select class="ausermanagement-filter-select" name="status"
                onchange="document.getElementById('filterForm').submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>

            <button type="button" class="ausermanagement-btn ausermanagement-btn-primary" id="addUserBtn">Thêm người
                dùng</button>
        </form>

        <!-- Bảng danh sách -->
        <div class="ausermanagement-data-card">
            <table class="ausermanagement-data-table" id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <span
                                    class="acustomermanagement-status-badge {{ $user->is_active ? 'acustomermanagement-status-active' : 'acustomermanagement-status-inactive' }}">
                                    {{ $user->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <button class="ausermanagement-btn ausermanagement-btn-primary"
                                    onclick="openEditModal({{ $user->id }}, '{{ $user->role }}', '{{ $user->is_active ? 'active' : 'inactive' }}')">Sửa</button>
                                <button class="ausermanagement-btn ausermanagement-btn-danger"
                                    onclick="removeRole({{ $user->id }})">Xoá quyền</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal cập nhật -->
        <div class="ausermanagement-modal" id="editModal">
            <div class="ausermanagement-modal-content">
                <div class="ausermanagement-modal-header">
                    <h2>Cập nhật người dùng</h2>
                    <span class="ausermanagement-modal-close" onclick="closeEditModal()">×</span>
                </div>
                <div class="ausermanagement-modal-body">
                    <input type="hidden" id="editUserId">
                    <div class="form-group">
                        <label>Vai trò</label>
                        <select id="editRole">
                            <option value="admin">Quản trị viên</option>
                            <option value="news_manager">Quản lý tin tức</option>
                            <option value="products_manager">Quản lý sản phẩm</option>
                            <option value="customer_service">Chăm sóc khách hàng</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select id="editStatus">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="ausermanagement-modal-footer">
                    <button class="ausermanagement-btn ausermanagement-btn-secondary"
                        onclick="closeEditModal()">Hủy</button>
                    <button class="ausermanagement-btn ausermanagement-btn-primary" onclick="submitUpdate()">Lưu</button>
                </div>
            </div>
        </div>

        <!-- Modal thêm người dùng -->
        <div class="ausermanagement-modal" id="addUserModal">
            <div class="ausermanagement-modal-content">
                <div class="ausermanagement-modal-header">
                    <h2>Thêm người dùng mới</h2>
                    <span class="ausermanagement-modal-close" onclick="closeAddUserModal()">×</span>
                </div>
                <div class="ausermanagement-modal-body">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="addUserEmail" class="form-control" placeholder="Nhập email người dùng">
                    </div>
                    <div class="form-group">
                        <label>Vai trò</label>
                        <select id="addUserRole">
                            <option value="admin">Quản trị viên</option>
                            <option value="news_manager">Quản lý tin tức</option>
                            <option value="products_manager">Quản lý sản phẩm</option>
                            <option value="customer_service">Chăm sóc khách hàng</option>
                        </select>
                    </div>
                </div>
                <div class="ausermanagement-modal-footer">
                    <button class="ausermanagement-btn ausermanagement-btn-secondary"
                        onclick="closeAddUserModal()">Hủy</button>
                    <button class="ausermanagement-btn ausermanagement-btn-primary" onclick="submitAddUser()">Thêm</button>
                </div>
            </div>
        </div>

        <!-- Phân trang -->
        <div class="acustomermanagement-phantrang">
            {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script>
        function openEditModal(id, role, status) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editRole').value = role;
            document.getElementById('editStatus').value = status;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        async function submitUpdate() {
            const id = document.getElementById('editUserId').value;
            const role = document.getElementById('editRole').value;
            const status = document.getElementById('editStatus').value;

            const res = await fetch(`/admin/quanlynguoidung/${id}/update`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    role,
                    status
                })
            });

            const data = await res.json();
            if (res.ok) {
                alert(data.success);
                location.reload();
            } else {
                alert(data.error || 'Cập nhật thất bại');
            }
        }

        async function removeRole(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa quyền của người dùng này?')) return;

            const res = await fetch(`/admin/quanlynguoidung/${id}/remove-role`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (res.ok) {
                alert(data.success);
                location.reload();
            } else {
                alert(data.error || 'Xóa thất bại');
            }
        }

        document.getElementById('addUserBtn').addEventListener('click', function() {
            document.getElementById('addUserModal').style.display = 'flex';
        });

        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }

        async function submitAddUser() {
            const email = document.getElementById('addUserEmail').value.trim();
            const role = document.getElementById('addUserRole').value;

            if (!email) {
                alert('Vui lòng nhập email');
                return;
            }

            const res = await fetch(`{{ route('admin.users.add') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email,
                    role
                })
            });

            const data = await res.json();

            if (res.ok) {
                alert(data.success);
                closeAddUserModal();
                location.reload();
            } else {
                alert(data.error || 'Thêm thất bại');
            }
        }
    </script>
@endsection
