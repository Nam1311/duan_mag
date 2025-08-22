@extends('admin.app')

@section('admin.body')
<link rel="stylesheet" href="{{ asset('/css/admin/products.css') }}">
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
    </style>
    <div class="apromotions-main-content">
        <div class="aindex-header">
            {{-- <div class="aindex-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm sản phẩm, đơn hàng..." />
            </div> --}}
            <div></div>
            <div class="aindex-user-profile">
                <div class="aindex-notification-bell">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="aindex-profile-avatar">QT</div>
            </div>
        </div>
        <h1 class="apromotions-page-title">Quản lý danh mục sản phẩm</h1>
        <p class="aindex-dashboard-subtitle">
            Tạo và quản lý các danh mục của sản phẩm.
        </p>
        <div class="apromotions-actions-container">
            <button class="apromotions-btn apromotions-btn-primary"
                onclick="document.getElementById('createModal').style.display='flex'">
                Thêm danh mục
            </button>
        </div>
        <table class="apromotions-promotion-table">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên</th>
                    <th>Đường dẫn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            <img src="{{ asset('img/categories/' . $category->image) }}" alt="Hình ảnh"
                                style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                        </td>
                        <td>{{ $category->name }}</td>

                        <td class="status-cell">{{ $category->slug }}</td>
                        <td class="dm-actions">
                            <div class="dm-action-td">
                                <button class="apromotions-btn apromotions-btn-primary btn-edit"
                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                    data-image="{{ $category->image }}">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>



                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="apromotions-btn apromotions-btn-danger delete-btn"
                                        data-id="1">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Phân trang --}}
        <div class="products-pagination" id="products-pagination">
            @for ($i = 1; $i <= $categories->lastPage(); $i++)
                <a href="{{ $categories->url($i) }}"
                    class="products-pagination-btn {{ $categories->currentPage() == $i ? 'adnews-active' : '' }}">
                    {{ $i }}
                </a>
            @endfor
        </div>

    </div>




    <!-- Popup edit  -->
    <div class="apromotions-modal" id="editModal">
        <div class="apromotions-modal-content">
            <span class="apromotions-modal-close"
                onclick="document.getElementById('editModal').style.display='none'">×</span>
            <h2>Chỉnh sửa danh mục</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="apromotions-form-group">
                    <label>Tên danh mục</label>
                    <input type="text" id="edit-name" name="name" required>
                </div>

                <div class="apromotions-form-group">
                    <label>Hình ảnh mới (nếu có)</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <div class="apromotions-modal-actions">
                    <button type="submit" class="apromotions-btn apromotions-btn-primary">Lưu</button>
                    <button type="button" class="apromotions-btn apromotions-btn-secondary"
                        onclick="document.getElementById('editModal').style.display='none'">Hủy</button>
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
            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data"
                onsubmit="return validateForm(this)">
                @csrf
                <div class="apromotions-form-group">
                    <label>Tên danh mục</label>
                    <input type="text" name="name" required>
                </div>
                <div class="apromotions-form-group">
                    <label>Hình ảnh</label>
                    <input type="file" name="image" accept="image/*" required />
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
        // const editNameInput = document.getElementById('edit-name');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const image = this.dataset.image;

                // Gán dữ liệu vào form sửa
                editForm.action = `/admin/categories/${id}`;
                // editNameInput.value = name;
                document.getElementById('edit-name').value = name;


                editModal.style.display = 'flex';
            });
        });


        function closeEditModal() {
            editModal.style.display = 'none';
        }
    </script>
@endsection
