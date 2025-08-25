@extends('admin.app')

@section('admin.body')
    <link rel="stylesheet" href="{{ asset('/css/admin/products.css') }}">
    <div class="aproducts-main-content">
        <div class="aproducts-header">
            <div class="aproducts-search-bar">
                <form action="{{ route('admin.products.search') }}" method="GET">
                    <input type="text" name="keyword" id="searchInput" placeholder="Tìm kiếm sản phẩm..." value="{{ request('keyword') }}" />
                    <button style="background-color: white; border: none; cursor: pointer; margin-left: 63px " type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="aproducts-user-profile">
                <div class="aproducts-notification-bell">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="aproducts-profile-avatar">QT</div>
            </div>
        </div>

        <h1 class="aproducts-page-title">Quản lý sản phẩm</h1>
        <p class="aproducts-page-subtitle">
            Xem và chỉnh sửa danh sách sản phẩm của cửa hàng
        </p>

        <div class="aproducts-actions">
            <div class="aproducts-filter-group">
                <!-- Dropdown danh mục -->
                <div class="dropdown">
                    <button class="dropdown-toggle" id="categoryDropdown">
                        {{ isset($category) ? $category->name : 'Tất cả danh mục' }} <i class="fa fa-angle-down" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu" id="categoryMenu">
                        <a href="{{ route('admin.products.index') }}">Tất cả</a>
                        @foreach ($categories as $category)
                            <a href="{{ route('products.TheoDanhMuc', ['id' => $category->id]) }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Dropdown trạng thái -->
                <div class="dropdown">
                    <button class="dropdown-toggle" id="statusDropdown">
                        {{ isset($status) ? $status : 'Tất cả trạng thái' }}<i class="fa fa-angle-down" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('admin.products.index') }}">Tất cả trạng thái</a>
                        <a href="{{ route('products.TheoTrangThai', ['status' => 'Còn hàng']) }}">Còn hàng</a>
                        <a href="{{ route('products.TheoTrangThai', ['status' => 'Hết hàng']) }}">Hết hàng</a>
                        <a href="{{ route('products.TheoTrangThai', ['status' => 'Sắp hết hàng']) }}">sắp hết hàng</a>
                        <a href="{{ route('products.TheoTrangThai', ['status' => 'Đang kinh doanh']) }}">Đang kinh doanh</a>
                        <a href="{{ route('products.TheoTrangThai', ['status' => 'Ngừng kinh doanh']) }}">Ngừng kinh doanh</a>
                    </div>
                </div>
            </div>
            <button class="aproducts-btn aproducts-btn-primary" onclick="openModal()">
                Thêm sản phẩm
            </button>
        </div>

        @if(isset($keyword) && $keyword != '')
            <p>Kết quả tìm kiếm cho: "<strong>{{ $keyword }}</strong>"</p>
        @endif

        <div class="aproducts-data-card">
            <table class="aproducts-data-table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá gốc</th>
                        <th>Giá bán</th>
                        {{-- <th>Flsale</th> --}}
                        <th>Sale</th>
                        <th>Kho</th>
                        <th>Trạng thái</th>
                        <th>Bussiness</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <!-- Dữ liệu sản phẩm sẽ hiển thị tại đây -->
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <div style="position: relative; display: inline-block; width: 50px; height: 50px;">
                                <img src="{{ asset($product->images->first()->path ?? '/img/default.jpg') }}"
                                    alt="Hình ảnh"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">

                                <a href="/admin/quanlyhinhanh?product_id={{$product->id}}"
                                style="position: absolute; bottom: -15px; right: -5px; color: #050505; padding: 3px; font-size: 15px;">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                            </div>
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Không có danh mục' }}</td>
                        <td><del style="text-decoration: line-through">{{ number_format($product->original_price) }}đ</del></td>
                        <td>{{ number_format($product->price) }}đ</td>
                        {{-- <td>{{ $product->sale }}%</td> --}}
                        <td>{{ $product->base_sale }}%</td>
                        <td>{{ $product->variants->sum('quantity') }}</td>
                        {{-- <td>
                            @foreach ($product->variants as $variant)
                                <div>{{ $variant->size->name ?? '' }}/{{ $variant->color->name ?? '' }}: {{ $variant->quantity }}</div>
                            @endforeach
                        </td> --}}
                        <td>
                            <span class="aproducts-status-badge {{ $product->variants->sum('quantity') > 0 ? 'aproducts-status-active' : 'aproducts-status-inactive' }}">
                                {{ $product->variants->sum('quantity') > 0 ? 'Còn hàng' : 'Hết hàng' }}
                            </span>
                        </td>
                        <td>
                            <span class="aproducts-status-badge {{ $product->is_active > 0 ? 'aproducts-biz-active' : 'aproducts-biz-inactive' }}">
                                {{ $product->is_active > 0 ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                            </span>
                        </td>

                        {{-- <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $product->description }}
                        </td> --}}
                        <td>
                            <button style="background-color: rgb(96, 214, 135)" class="aproducts-btn aproducts-btn-primary"onclick="showDetailModal({{ $product->id }})"><i style="font-size: 13px" class="fa fa-eye" aria-hidden="true"></i></button>
                            <button class="aproducts-btn aproducts-btn-primary" onclick='openEditModal(@json($product))'><i style="font-size: 13px" class="fa fa-pencil" aria-hidden="true"></i></button>
                            <button style="background-color: rgb(234, 83, 83)" class="aproducts-btn aproducts-btn-outline" onclick="deleteProduct({{ $product->id }})"><a href=""><i style="font-size: 13px; color: white;" class="fa fa-trash" aria-hidden="true"></i></a></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- phân trang --}}
            <div class="products-pagination" id="products-pagination">
                @for ($i = 1; $i <= $products->lastPage(); $i++)
                    <a href="{{ $products->url($i) }}"
                        class="products-pagination-btn {{ $products->currentPage() == $i ? 'adnews-active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor
            </div>
        </div>


        {{-- forrm thêm --}}
        <div class="aproducts-modal" id="productModal">
            <div class="aproducts-modal-content">
                <h2 id="modalTitle">Thêm sản phẩm mới</h2>
                <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="aproducts-form-group">
                        <label>Hình ảnh sản phẩm</label>
                        <input type="file" name="images[]" id="productImage" accept="image/*" multiple />
                        <div id="imagePreviewContainer" class="image-preview-container" style="display: flex; gap: 10px; flex-wrap: wrap;"></div>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Tên sản phẩm</label>
                        <input type="text" name="name" id="productName" placeholder="Nhập tên sản phẩm" required />
                    </div>

                    <div class="aproducts-form-group">
                        <label>Danh mục</label>
                        <select name="category_id" id="productCategory" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Giá gốc</label>
                        <input type="number" name="original_price" id="productOriginalPrice" placeholder="Giá gốc" min="0" required />
                    </div>

                    <div class="aproducts-form-group">
                        <label>Base Sale (%)</label>
                        <input type="number" name="base_sale" id="productBaseSale" placeholder="Giảm giá gốc" min="0" max="100" />
                    </div>

                    <div class="aproducts-form-group">
                        <label>Mô tả</label>
                        <textarea name="description" id="productDescription" placeholder="Nhập mô tả sản phẩm"></textarea>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Biến thể</label>
                        <div id="variantContainer">
                            <div class="variant-row">
                                <select class="variant-size">
                                    <option value="">-- Chọn size --</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                </select>
                                <select class="variant-color">
                                    <option value="">-- Chọn màu --</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}" data-hex="{{ $color->hex_code }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" placeholder="Số lượng" class="variant-quantity" min="0" />
                                <button type="button" onclick="removeVariant(this)">X</button>
                            </div>
                        </div>
                        <button type="button" onclick="addVariant()">+ Thêm biến thể</button>
                    </div>
                    <input type="hidden" name="variants" id="variantsInput" />

                    <div class="aproducts-modal-actions">
                        <button type="button" class="aproducts-btn aproducts-btn-outline" onclick="closeModal()">Hủy</button>
                        <button type="submit" class="aproducts-btn aproducts-btn-primary" onclick="beforeSubmit(event)">Lưu</button>
                    </div>
                    <script>
                        document.getElementById('productImage').addEventListener('change', function (event) {
                            const previewContainer = document.getElementById('imagePreviewContainer');
                            previewContainer.innerHTML = '';
                            Array.from(event.target.files).forEach(file => {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.className = 'aproducts-image-preview';
                                    previewContainer.appendChild(img);
                                };
                                reader.readAsDataURL(file);
                            });
                        });
                    </script>
                </form>
            </div>
        </div>


        <style>
            .variant-row {
                display: flex;
                gap: 10px;
                margin-bottom: 10px;
                align-items: center;
            }

            .variant-row select,
            .variant-row input {
                flex: 1;
                padding: 8px;
                border-radius: 4px;
                border: 1px solid #ddd;
            }

            .image-preview-container img {
                border-radius: 6px;
                margin-right: 10px;
                margin-bottom: 10px;
            }
        </style>
        <!-- Modal Cập Nhật Sản Phẩm -->
        <div class="aproducts-modal" id="updateProductModal" style="display: none;">
            <div class="aproducts-modal-content">
                <h2 id="modalTitle">Cập nhật sản phẩm</h2>
                <form id="updateProductForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="product_id" id="editProductId">

                    <div class="aproducts-form-group">
                        <label>Tên sản phẩm</label>
                        <input type="text" name="name" id="editProductName" required>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Giá gốc</label>
                        <input type="number" name="original_price" id="editProductOriginalPrice" min="0" required>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Sale (%)</label>
                        <input type="number" name="base_sale" id="editProductSale" min="0" max="100">
                    </div>

                    <div class="aproducts-form-group">
                        <label>Mô tả</label>
                        <textarea name="description" id="editProductDescription"></textarea>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Danh mục</label>
                        <select name="category_id" id="editProductCategory" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Hình ảnh sản phẩm</label>
                        <div id="editImagePreview" class="image-preview-container" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <!-- Ảnh preview sẽ render ở đây bằng JS -->

                        </div>
                       <a id="editImageLink" href="#" target="_blank">
                            <i class="fas fa-images"></i> Chỉnh sửa hình ảnh sản phẩm
                        </a>
                    </div>

                    <div class="aproducts-form-group">
                        <label>Biến thể</label>
                        <div id="editVariantContainer">
                            <!-- Biến thể sẽ render ở đây bằng JS -->
                        </div>
                        <button type="button" onclick="addVariantToEdit()">+ Thêm biến thể</button>
                    </div>


                    <div class="aproducts-form-group">
                        <label>Trạng thái kinh doanh</label>
                        <select name="is_active" id="editProductStatus">
                            <option value="1">Đang kinh doanh</option>
                            <option value="0">Ngừng kinh doanh</option>
                        </select>
                    </div>

                    <div class="aproducts-modal-actions">
                        <button type="button" class="aproducts-btn aproducts-btn-outline" onclick="closeUpdateModal()">Hủy</button>
                        <button type="submit" class="aproducts-btn aproducts-btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>






        {{-- form xem chi tiết --}}
        <style>
            .aproducts-detail-info p {
                margin: 4px 0;
            }

            #productDetailModal img {
                margin-top: 8px;
                max-height: 100px;
            }
        </style>

        @foreach($products as $product)
            <!-- Modal chi tiết riêng cho sản phẩm -->
            <div class="aproducts-modal" id="productDetailModal-{{ $product->id }}" style="display: none;">
                <div class="aproducts-modal-content">
                    <h2>Chi tiết sản phẩm</h2>
                    <div class="aproducts-modal-actions">
                        <button style="border: none; background-color: white" onclick="closeDetailModal({{ $product->id }})"><i style="font-size: 20px" class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <div class="aproducts-detail-info">
                        <div>
                            <strong>Ảnh:</strong><br>
                            <div class="flex gap-2 flex-wrap">
                                @foreach ($product->images as $img)
                                    <img src="{{ asset($img->path) }}" style="width:100px; border-radius:6px;">
                                @endforeach

                                @if ($product->images->isEmpty())
                                    <img src="{{ asset('/img/default.jpg') }}" style="width:100px; border-radius:6px;">
                                @endif
                            </div>
                        </div>
                        <p><strong>Tên:</strong> {{ $product->name }}</p>
                        <p><strong>Danh mục:</strong> {{ $product->category->name ?? 'Không có' }}</p>
                        <p><Strong>Sku:</Strong> {{$product->sku}}</p>
                        <p><strong>Biến thể:</strong>
                            @foreach ($product->variants as $variant)
                                <div>
                                    Màu <strong>{{ $variant->color->name ?? 'ko xác định' }}</strong> /
                                    <strong>{{ $variant->size->name ?? 'ko xác định' }}</strong> -
                                    Còn lại: <strong>{{ $variant->quantity }}</strong> sản phẩm
                                </div>
                            @endforeach
                        </p>
                        <p><strong>Giá gốc:</strong> {{ number_format($product->original_price) }}đ</p>
                        <p><strong>Giá giảm:</strong> {{ number_format($product->price) }}đ</p>
                        <p><strong>Sale:</strong> {{ $product->sale }}%</p>
                        <p><strong>Kho:</strong> {{ $product->variants->sum('quantity') }}</p>
                        <p><strong>Trạng thái:</strong> {{ $product->variants->sum('quantity') > 0 ? 'Còn hàng' : 'Hết hàng' }}</p>
                        <p><strong>Kinh doanh:</strong> {{ $product->is_active > 0 ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}</p>
                        <p><strong>Mô tả:</strong> {{ $product->description }}</p>
                    </div>
                </div>
            </div>
        @endforeach



        <div class="aproducts-modal" id="confirmModal">
            <div class="aproducts-modal-content">
                <h2>Xác nhận chuyển trang</h2>
                <p>Bạn có chắc muốn chuyển sang trang khác? Các thay đổi chưa lưu sẽ bị mất.</p>
                <div class="aproducts-modal-actions">
                    <button class="aproducts-btn aproducts-btn-outline" onclick="closeConfirmModal()">Hủy</button>
                    <button class="aproducts-btn aproducts-btn-primary" id="confirmNavigateBtn">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
    <template id="variant-template">
        <div class="variant-row">
            <select class="variant-size">
                <option value="">-- Chọn size --</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
            <select class="variant-color">
                <option value="">-- Chọn màu --</option>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}" data-hex="{{ $color->hex_code }}" style="background-color: {{ $color->hex_code }}; color: #fff;">
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
            <input type="number" placeholder="Số lượng" class="variant-quantity" min="0" />
            <button type="button" onclick="removeVariant(this)">X</button>
        </div>
    </template>


    <script>
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function () {
                const dropdown = this.parentElement;
                dropdown.classList.toggle('show');
            });
        });

        // Tự đóng dropdown nếu click ngoài
        window.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown').forEach(drop => {
                if (!drop.contains(e.target)) {
                    drop.classList.remove('show');
                }
            });
        });
    </script>


    <script>
        function openModal() {
            document.getElementById("productModal").style.display = "flex";
            document.getElementById("modalTitle").textContent = "Thêm sản phẩm mới";
            document.getElementById("productName").value = "";
            document.getElementById("productPrice").value = "";
        }

        function closeModal() {
            document.getElementById("productModal").style.display = "none";
        }

        function editProduct(name, price) {
            document.getElementById("productModal").style.display = "flex";
            document.getElementById("modalTitle").textContent = "Sửa sản phẩm";
            document.getElementById("productName").value = name;
            document.getElementById("productPrice").value = price;
        }
    </script>

    <script>
        function showDetailModal(id) {
            document.getElementById('productDetailModal-' + id).style.display = 'flex';
        }

        function closeDetailModal(id) {
            document.getElementById('productDetailModal-' + id).style.display = 'none';
        }
    </script>




    <script>
        function addVariant() {
            const container = document.getElementById('variantContainer');
            const template = document.getElementById('variant-template');
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
        }

        function removeVariant(button) {
            button.parentElement.remove();
        }

        function beforeSubmit(event) {
            event.preventDefault();

            const variants = [];
            const rows = document.querySelectorAll('.variant-row');

            rows.forEach(row => {
                const size = row.querySelector('.variant-size').value;
                const color = row.querySelector('.variant-color').value;
                const quantity = row.querySelector('.variant-quantity').value;

                if (size && color && quantity) {
                    variants.push({ size, color, quantity });
                }
            });

            document.getElementById('variantsInput').value = JSON.stringify(variants);
            document.getElementById('productForm').submit();
        }
        function removeVariant(btn) {
            btn.closest('.variant-row').remove();
        }
        function beforeSubmit(e) {
            e.preventDefault();
            const rows = document.querySelectorAll('.variant-row');
            const data = [];
            rows.forEach(row => {
                const size = row.querySelector('.variant-size').value;
                const color = row.querySelector('.variant-color').value;
                const qty = row.querySelector('.variant-quantity').value;
                if (size && color && qty) {
                    data.push({ size, color, quantity: qty });
                }
            });
            document.getElementById('variantsInput').value = JSON.stringify(data);
            document.getElementById('productForm').submit();
        }

    </script>

    {{-- xoas  --}}
    <script>
        function deleteProduct(id) {
            if (!confirm('Bạn có chắc muốn xoá sản phẩm này?')) return;

            fetch(`/admin/products/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Tải lại trang để cập nhật danh sách
                } else {
                    alert('Xóa thất bại!');
                }
            })
            .catch(() => alert('Có lỗi xảy ra!'));
        }
    </script>

    <script>
        function closeUpdateModal() {
            document.getElementById('updateProductModal').style.display = 'none';
        }
        function openEditModal(product) {
            // Hiện modal
            document.getElementById('updateProductModal').style.display = 'flex';

            // Gán thông tin cơ bản vào form
            document.getElementById('editProductId').value = product.id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductOriginalPrice').value = product.original_price;
            document.getElementById('editProductSale').value = product.base_sale;
            document.getElementById('editProductDescription').value = product.description;
            document.getElementById('editProductCategory').value = product.category_id;
            document.getElementById('editProductStatus').value = product.is_active ? '1' : '0';
            // Cập nhật link sửa ảnh theo đúng sản phẩm
            document.getElementById('editImageLink').href = `/admin/quanlyhinhanh?product_id=${product.id}`;


            // Render ảnh cũ
            const editImagePreview = document.getElementById('editImagePreview');
            editImagePreview.innerHTML = '';

            product.images.forEach(img => {
                const imgDiv = document.createElement('div');
                imgDiv.style.marginBottom = '10px';
                imgDiv.innerHTML = `
                    <img src="${img.path.startsWith('http') ? img.path : '/' + img.path}" width="100" style="border-radius: 6px;">
                `;
                editImagePreview.appendChild(imgDiv);
            });

            // Render biến thể
            const editVariantContainer = document.getElementById('editVariantContainer');
            editVariantContainer.innerHTML = '';

            product.variants.forEach(variant => {
                const variantDiv = document.createElement('div');
                variantDiv.className = 'variant-row';
                variantDiv.style.marginBottom = '10px';
                variantDiv.style.display = 'flex';
                variantDiv.style.gap = '10px';
                variantDiv.style.alignItems = 'center';

                // Tạo select cho size
                const sizeSelect = document.createElement('select');
                sizeSelect.name = `variants[${variant.id}][size_id]`;
                sizeSelect.style.flex = '1';
                sizeSelect.innerHTML = `<option value="">-- Chọn size --</option>`;

                // Thêm options cho size
                @foreach($sizes as $size)
                    sizeSelect.innerHTML += `
                        <option value="{{ $size->id }}"
                            ${variant.size_id == {{ $size->id }} ? 'selected' : ''}>
                            {{ $size->name }}
                        </option>`;
                @endforeach

                // Tạo select cho color
                const colorSelect = document.createElement('select');
                colorSelect.name = `variants[${variant.id}][color_id]`;
                colorSelect.style.flex = '1';
                colorSelect.innerHTML = `<option value="">-- Chọn màu --</option>`;

                // Thêm options cho color
                @foreach($colors as $color)
                    colorSelect.innerHTML += `
                        <option value="{{ $color->id }}"
                            ${variant.color_id == {{ $color->id }} ? 'selected' : ''}
                            style="background-color: {{ $color->hex_code }}; color: white;">
                            {{ $color->name }}
                        </option>`;
                @endforeach

                // Tạo input cho quantity
                const quantityInput = document.createElement('input');
                quantityInput.type = 'number';
                quantityInput.name = `variants[${variant.id}][quantity]`;
                quantityInput.value = variant.quantity;
                quantityInput.min = '0';
                quantityInput.style.flex = '1';
                quantityInput.placeholder = 'Số lượng';

                // Tạo nút xóa
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.textContent = 'X';
                removeBtn.style.background = '#ff4444';
                removeBtn.style.color = 'white';
                removeBtn.style.border = 'none';
                removeBtn.style.borderRadius = '4px';
                removeBtn.style.padding = '5px 10px';
                removeBtn.style.cursor = 'pointer';

                // GÁN ID BIẾN THỂ để JS lấy khi xoá
                removeBtn.dataset.variantId = variant.id;

                // Gọi hàm xóa
                removeBtn.onclick = function () {
                    deleteVariant(this);
                };



                // Thêm các phần tử vào div variant
                variantDiv.appendChild(sizeSelect);
                variantDiv.appendChild(colorSelect);
                variantDiv.appendChild(quantityInput);
                variantDiv.appendChild(removeBtn);

                // Thêm variant vào container
                editVariantContainer.appendChild(variantDiv);
            });
            // Cập nhật form action
            document.getElementById('updateProductForm').action = `/admin/products/${product.id}`;
        }

        function addVariantToEdit() {
            const container = document.getElementById('editVariantContainer');
            const template = document.getElementById('variant-template');
            const clone = template.content.cloneNode(true);

            // Cập nhật name cho các input/select
            const index = container.querySelectorAll('.variant-row').length;

            const sizeSelect = clone.querySelector('.variant-size');
            sizeSelect.name = `new_variants[${index}][size_id]`;

            const colorSelect = clone.querySelector('.variant-color');
            colorSelect.name = `new_variants[${index}][color_id]`;

            const quantityInput = clone.querySelector('.variant-quantity');
            quantityInput.name = `new_variants[${index}][quantity]`;

            container.appendChild(clone);
        }
    </script>
    <script>
        function deleteVariant(button) {
            const variantId = button.dataset.variantId;

            if (!confirm('Bạn có chắc muốn xóa biến thể này?')) return;

            fetch(`/admin/variants/${variantId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Xóa dòng HTML chứa biến thể khỏi modal
                    button.closest('.variant-row').remove();
                } else {
                    alert('Xóa biến thể thất bại!');
                }
            })
            .catch(() => {
                alert('Có lỗi xảy ra khi xoá biến thể!');
            });
        }
    </script>

@endsection
