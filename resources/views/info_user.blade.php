@extends('app')

@section('body')
    @if (session('success'))
        <div id="success-toast"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        font-weight: 500;
        display: flex;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    ">
            <span style="margin-right: 8px;">✔️</span>
            {{ session('success') }}
        </div>
    @endif


    <div class="user-info-body">
        <div class="user-info-container">
            <div class="user-info-sidebar">
                <ul>
                    <li class="active" data-section="personal"><i class="fas fa-user-circle"></i> Thông tin cá nhân</li>
                    <li data-section="address"><i class="fas fa-map-marker-alt"></i> Địa chỉ</li>
                    <li data-section="orders"><i class="fas fa-shopping-bag"></i>Lịch sử đơn hàng</li>
                </ul>
            </div>
            <div class="user-info-content">
                <div class="user-info-profile-content">
                    <section class="user-info-profile-section active" id="personal-section">
                        <div class="user-info-section-header">
                            <h2 class="user-info-section-title"><i class="fas fa-user-circle"></i> Thông tin cá nhân</h2>
                            <div>
                                @auth
                                    @if(auth()->user()->role === 'admin')
                                        <a href="/admin">
                                            <button class="user-info-edit-btn">
                                               <i class="fa fa-cogs" aria-hidden="true"></i> Truy cập quản trị
                                            </button>
                                        </a>
                                    @endif
                                @endauth
                                <button class="user-info-edit-btn" data-modal="personal"><i class="fas fa-edit"></i> Chỉnh sửa</button>
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                     <button class="user-info-edit-btn">
                                        <i class="fa fa-sign-out" aria-hidden="true"></i>   Đăng xuất
                                     </button>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                        <div class="user-info-section-content">
                            <div class="user-info-grid">
                                <div class="user-info-row">
                                    <span class="user-info-label"><i class="fas fa-user"></i> Họ và tên:</span>
                                    <span class="user-info-value">{{ $user->name ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="user-info-row">
                                    <span class="user-info-label"><i class="fas fa-envelope"></i> Email:</span>
                                    <span class="user-info-value">{{ $user->email ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="user-info-row">
                                    <span class="user-info-label"><i class="fas fa-phone"></i> Số điện thoại:</span>
                                    <span class="user-info-value">0{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="user-info-row">
                                    <span class="user-info-label"><i class="fas fa-lock"></i> Mật khẩu:</span>
                                    <button class="user-info-password-btn" data-modal="password">Đổi mật khẩu</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="user-info-profile-section" id="address-section">
                        <div class="user-info-section-header">
                            <h2 class="user-info-section-title"><i class="fas fa-map-marker-alt"></i> Địa chỉ</h2>
                            <button class="user-info-edit-btn" data-modal="add-address"><i class="fas fa-plus"></i> Thêm địa
                                chỉ</button>
                        </div>
                        <div class="user-info-section-content">
                            @foreach ($addresses as $addresses)
                                <div class="user-info-address-list">
                                    <div class="user-info-address-item" data-address-id="1">
                                        <span class="user-info-value">{{ $addresses->address ?? 'Chưa cập nhật' }} /
                                            {{ $addresses->ward ?? 'Chưa cập nhật' }} /
                                            {{ $addresses->district ?? 'Chưa cập nhật' }} <br>
                                            {{ $addresses->province ?? 'Chưa cập nhật' }}</span>
                                        <div class="user-info-address-actions">
                                            <button class="user-info-address-edit-btn" data-modal="edit-address"
                                                data-id="{{ $addresses->id }}" data-address="{{ $addresses->address }}"
                                                data-ward="{{ $addresses->ward }}"
                                                data-district="{{ $addresses->district }}"
                                                data-province="{{ $addresses->province }}"
                                                data-is_default="{{ $addresses->is_default }}">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <a href="{{ '/xoaaddress/' . $addresses->id }}"
                                                class="user-info-address-delete-btn"><i class="fas fa-trash"></i>
                                                Xóa</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="user-info-profile-section user-info-orders-section" id="orders-section">
                        <h2 style="margin: 20px 0px 0px 28px" class="user-info-section-title"><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây</h2>
                        <div class="user-info-section-header">
                            <div class="user-info-order-filter-tags">
                                <span class="user-info-order-filter-tag active" data-status="all">Tất cả</span>
                                <span class="user-info-order-filter-tag" data-status="Chờ xác nhận">Chờ xác nhận</span>
                                <span class="user-info-order-filter-tag" data-status="Đã xác nhận">Đã xác nhận</span>
                                <span class="user-info-order-filter-tag" data-status="Đang giao hàng">Đang giao hàng</span>
                                <span class="user-info-order-filter-tag" data-status="Thành công">Thành công</span>
                                <span class="user-info-order-filter-tag" data-status="Đã hủy">Đã hủy</span>
                            </div>
                        </div>
                        <div class="user-info-section-content">
                            <div class="user-info-orders-list">
                                @foreach ($order as $orderItem)
                                    <div class="user-info-order-item" data-status="{{ $orderItem->status }}">
                                        <div class="user-info-order-header">
                                            <span class="user-info-order-id">#{{ $orderItem->order_code ?? $orderItem->id . 'MAG' }}</span>
                                            <span
                                                class="user-info-order-date">{{ $orderItem->updated_at->format('d-m-Y') }}</span>
                                            @php
                                                $statusColors = [
                                                    'Chờ xác nhận' => 'gray',
                                                    'Đã xác nhận' => 'blue',
                                                    'Đang giao hàng' => 'orange',
                                                    'Thành công' => 'green',
                                                    'Đã hủy' => 'red',
                                                ];

                                                $color = $statusColors[$orderItem->status] ?? 'dark';
                                            @endphp
                                            <span class="user-info-order-status status-{{ $color }}">
                                                {{ $orderItem->status }}
                                            </span>
                                        </div>
                                        <div class="user-info-order-details">
                                            <div class="user-info-order-products">
                                                {{ $orderItem->orderDetails->pluck('productVariant.product.name')->join(', ') }}
                                            </div>
                                            <div class="user-info-order-total">
                                                {{ number_format($orderItem->orderDetails->sum('total_final'), 0, ',', '.') }} đ</div>
                                        </div>
                                        <div class="user-info-order-actions">
                                            <a class="user-info-order-detail-btn"
                                                href="{{ '/info-ctdh/' . $orderItem->id }}"><i class="fas fa-eye"></i> Xem
                                                chi tiết</a>
                                            @php
                                                $isCompleted = $orderItem->status === 'Thành công';
                                                $isReviewed = $orderItem->orderDetails->every(function ($detail) {
                                                    return $detail->productVariant->product
                                                        ->reviews()
                                                        ->where('user_id', auth()->id())
                                                        ->where('product_id', $detail->productVariant->product_id)
                                                        ->exists();
                                                });
                                            @endphp

                                            @if ($isCompleted && !$isReviewed)
                                                <a href="{{ route('review.form', $orderItem->id) }}"
                                                    class="user-info-order-review-btn">
                                                    <i class="fas fa-star"></i> Đánh giá
                                                </a>
                                            @endif

                                            @php
                                                $hideCancelButton = in_array($orderItem->status, [
                                                    'Đang giao hàng',
                                                    'Thành công',
                                                    'Đã hủy',
                                                ]);
                                            @endphp
                                            <a href="{{ '/huydon/' . $orderItem->id }}"
                                                class="user-info-order-cancel-btn {{ $hideCancelButton ? 'an_huy' : '' }}">
                                                <i class="fas fa-times"></i> Hủy đơn hàng
                                            </a>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </section>

                    <section class="user-info-profile-section user-info-favorites-section" id="favorites-section">
                        <div class="user-info-section-header">
                            <h2 class="user-info-section-title"><i class="fas fa-heart"></i> Sản phẩm yêu thích</h2>
                        </div>
                        <div class="user-info-section-content">
                            <div class="user-info-favorites-list">
                                <div class="user-info-favorite-item">
                                    <img src="https://via.placeholder.com/80" alt="Sản phẩm A"
                                        class="user-info-favorite-image">
                                    <div class="user-info-favorite-details">
                                        <div class="user-info-favorite-name">Sản phẩm A</div>
                                        <div class="user-info-favorite-price">1.200.000 đ</div>
                                    </div>
                                    <button class="user-info-favorite-remove-btn"><i class="fas fa-trash"></i>
                                        Xóa</button>
                                </div>
                                <div class="user-info-favorite-item">
                                    <img src="https://via.placeholder.com/80" alt="Sản phẩm B"
                                        class="user-info-favorite-image">
                                    <div class="user-info-favorite-details">
                                        <div class="user-info-favorite-name">Sản phẩm B</div>
                                        <div class="user-info-favorite-price">900.000 đ</div>
                                    </div>
                                    <button class="user-info-favorite-remove-btn"><i class="fas fa-trash"></i>
                                        Xóa</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Edit Personal Info Modal -->
            <div class="user-info-modal" id="user-info-personal-modal">
                <div class="user-info-modal-content">
                    <button class="user-info-close-btn" aria-label="Đóng">×</button>
                    <h2 class="user-info-modal-title">Chỉnh sửa thông tin cá nhân</h2>
                    <form action="{{ '/suainfo/' . $user->id }}" method="post" id="personal-form">
                        @csrf
                        <div class="user-info-form-group">
                            <div class="user-info-input-group">
                                <label for="personal-fullname" class="user-info-input-label">Họ và tên:</label>
                                <input type="text" id="personal-fullname" name="fullname"
                                    placeholder="{{ $user->name ?? 'Chưa cập nhật' }}" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="personal-email" class="user-info-input-label">Email:</label>
                                <input type="email" id="personal-email" name="email"
                                    placeholder="{{ $user->email ?? 'Chưa cập nhật' }}" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="personal-phone" class="user-info-input-label">Số điện thoại:</label>
                                <input type="tel" id="personal-phone" name="phone"
                                    placeholder="0{{ $user->phone ?? 'Chưa cập nhật' }}" class="user-info-input">
                            </div>
                        </div>
                        <div class="user-info-form-actions">
                            <button type="button" class="user-info-cancel-btn" data-modal="personal">Hủy</button>
                            <button type="submit" class="user-info-save-btn">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div class="user-info-modal" id="user-info-password-modal">
                <div class="user-info-modal-content">
                    <button class="user-info-close-btn" aria-label="Đóng">×</button>
                    <h2 class="user-info-modal-title">Đổi mật khẩu</h2>
                    <form action="{{ '/mkinfo/' . $user->id }}" method="POST" id="password-form">
                        @csrf
                        <div class="user-info-form-group">
                            <div class="user-info-input-group">
                                <label for="old-password" class="user-info-input-label">Mật khẩu cũ:</label>
                                <input type="password" id="old-password" name="old_password"
                                    placeholder="Nhập mật khẩu cũ" class="user-info-input" required>
                                @error('old_password')
                                    <div class="error-message" style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="user-info-input-group">
                                <label for="new-password" class="user-info-input-label">Mật khẩu mới:</label>
                                <input type="password" id="new-password" name="password" placeholder="Nhập mật khẩu mới"
                                    class="user-info-input">
                                @error('password')
                                    <div class="error-message" style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="user-info-input-group">
                                <label for="password_confirmation" class="user-info-input-label">Xác nhận mật khẩu
                                    mới:</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Nhập lại mật khẩu mới" class="user-info-input">
                            </div>
                        </div>
                        <div class="user-info-form-actions">
                            <button type="button" class="user-info-cancel-btn" data-modal="password">Hủy</button>
                            <button type="submit" class="user-info-save-btn">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add Address Modal -->
            <div class="user-info-modal" id="user-info-add-address-modal">
                <div class="user-info-modal-content">
                    <button class="user-info-close-btn" aria-label="Đóng">×</button>
                    <h2 class="user-info-modal-title">Thêm địa chỉ</h2>
                    <form action="{{ '/themaddress/' }}" method="POST" id="add-address-form">
                        @csrf
                        <div class="user-info-form-group">
                            <div class="user-info-input-group">
                                <label for="add-address-street" class="user-info-input-label">Địa chỉ:</label>
                                <input type="text" id="add-address-street" name="address"
                                    placeholder="Nhập số nhà, tên đường" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="add-address-ward" class="user-info-input-label">Thôn/Phường:</label>
                                <input type="text" id="add-address-ward" name="ward"
                                    placeholder="Nhập thôn/phường" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="add-address-district" class="user-info-input-label">Huyện/Quận:</label>
                                <input type="text" id="add-address-district" name="district"
                                    placeholder="Nhập huyện/quận" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="add-address-province" class="user-info-input-label">Tỉnh/Thành phố:</label>
                                <input type="text" id="add-address-province" name="province"
                                    placeholder="Nhập tỉnh/thành phố" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label class="user-info-input-label">Loại địa chỉ:</label>
                                <select name="address_type" class="user-info-select edit-address-type">
                                    <option value="1">Chính thức</option>
                                    <option value="0">Phụ</option>
                                </select>
                            </div>
                        </div>
                        <div class="user-info-form-actions">
                            <button type="button" class="user-info-cancel-btn" data-modal="add-address">Hủy</button>
                            <button type="submit" class="user-info-save-btn">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Address Modal -->
            <div class="user-info-modal" id="user-info-edit-address-modal">
                <div class="user-info-modal-content">
                    <button class="user-info-close-btn" aria-label="Đóng">×</button>
                    <h2 class="user-info-modal-title">Chỉnh sửa địa chỉ</h2>
                    <form action="{{ '/suaaddress/' }}" method="POST" id="edit-address-form">
                        @csrf
                        <div class="user-info-form-group">
                            <div class="user-info-input-group">
                                <label for="edit-address-street" class="user-info-input-label">Địa chỉ:</label>
                                <input type="text" id="edit-address-street" name="address"
                                    placeholder="Nhập số nhà, tên đường" class="user-info-input">
                                <input type="hidden" id="edit-address-id" name="address_id">
                            </div>
                            <div class="user-info-input-group">
                                <label for="edit-address-ward" class="user-info-input-label">Thôn/Phường:</label>
                                <input type="text" id="edit-address-ward" name="ward"
                                    placeholder="Nhập thôn/phường" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="edit-address-district" class="user-info-input-label">Huyện/Quận:</label>
                                <input type="text" id="edit-address-district" name="district"
                                    placeholder="Nhập huyện/quận" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label for="edit-address-province" class="user-info-input-label">Tỉnh/Thành phố:</label>
                                <input type="text" id="edit-address-province" name="province"
                                    placeholder="Nhập tỉnh/thành phố" class="user-info-input">
                            </div>
                            <div class="user-info-input-group">
                                <label class="user-info-input-label">Loại địa chỉ:</label>
                                <select name="address_type" class="user-info-select edit-address-type">
                                    <option value="1">Chính thức</option>
                                    <option value="0">Phụ</option>
                                </select>
                            </div>
                        </div>
                        <div class="user-info-form-actions">
                            <button type="button" class="user-info-cancel-btn" data-modal="edit-address">Hủy</button>
                            <button type="submit" class="user-info-save-btn">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            const toast = document.getElementById('success-toast');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500); // chờ hiệu ứng mờ dần rồi xóa
            }
        }, 3000); // 3 giây
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;

            // ========================
            // XỬ LÝ MENU TAB BÊN TRÁI
            // ========================
            const menuItems = document.querySelectorAll('.user-info-sidebar li');
            const sections = document.querySelectorAll('.user-info-profile-section');

            menuItems.forEach(item => {
                item.addEventListener('click', () => {
                    // 1. Bỏ class 'active' khỏi tất cả các tab
                    menuItems.forEach(i => i.classList.remove('active'));

                    // 2. Thêm class 'active' vào tab đang click
                    item.classList.add('active');

                    // 3. Ẩn tất cả nội dung
                    sections.forEach(section => section.classList.remove('active'));

                    // 4. Hiện phần nội dung tương ứng với tab được click
                    const sectionId = item.dataset.section + '-section'; // ví dụ "orders-section"
                    const section = document.getElementById(sectionId);
                    if (section) {
                        section.classList.add('active');
                    }
                });
            });

            // ========================
            // XỬ LÝ MODAL (popup form)
            // ========================

            // Hàm hiện modal
            const showModal = (modalId) => {
                const modal = document.getElementById(`user-info-${modalId}-modal`);
                if (modal) {
                    modal.classList.add('show');
                    body.style.overflow = 'hidden'; // Ngăn cuộn nền
                }
            };

            // Hàm ẩn modal
            const hideModal = (modalId) => {
                const modal = document.getElementById(`user-info-${modalId}-modal`);
                if (modal) {
                    modal.classList.remove('show');
                    body.style.overflow = ''; // Cho cuộn nền lại
                }
            };

            // ========================
            // MỞ MODAL CHỈNH SỬA THÔNG TIN CÁ NHÂN HOẶC ĐỔI MẬT KHẨU
            // ========================
            document.querySelectorAll('.user-info-edit-btn[data-modal="personal"], .user-info-password-btn')
                .forEach(button => {
                    button.addEventListener('click', () => {
                        showModal(button.dataset.modal); // modalId = 'personal' hoặc 'password'
                    });
                });

            // ========================
            // MỞ MODAL THÊM ĐỊA CHỈ MỚI (và reset các input)
            // ========================
            const addAddressBtn = document.querySelector('.user-info-edit-btn[data-modal="add-address"]');
            if (addAddressBtn) {
                addAddressBtn.addEventListener('click', () => {
                    // Reset các input khi mở form
                    document.getElementById('add-address-street').value = '';
                    document.getElementById('add-address-ward').value = '';
                    document.getElementById('add-address-district').value = '';
                    document.getElementById('add-address-province').value = '';
                    showModal('add-address');
                });
            }

            // ========================
            // MỞ MODAL SỬA ĐỊA CHỈ VÀ ĐỔ DỮ LIỆU
            // ========================
            document.querySelectorAll('.user-info-address-edit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    // Đổ dữ liệu vào input
                    document.getElementById('edit-address-id').value = button.dataset.id || '';
                    document.getElementById('edit-address-street').value = button.dataset.address ||
                        '';
                    document.getElementById('edit-address-ward').value = button.dataset.ward || '';
                    document.getElementById('edit-address-district').value = button.dataset
                        .district || '';
                    document.getElementById('edit-address-province').value = button.dataset
                        .province || '';

                    // Checkbox mặc định
                    const isDefaultCheckbox = document.getElementById('edit-address-is_default');
                    if (isDefaultCheckbox) {
                        isDefaultCheckbox.checked = button.dataset.is_default == "1";
                    }

                    // Select radio default
                    document.querySelectorAll('.edit-address-type').forEach(select => {
                        select.value = button.dataset.is_default == "1" ? "1" : "0";
                    });

                    showModal('edit-address');
                });
            });

            // ========================
            // NÚT ĐÓNG / HỦY MODAL
            // ========================
            document.querySelectorAll('.user-info-close-btn, .user-info-cancel-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.dataset.modal || button.closest('.user-info-modal').id
                        .replace('user-info-', '').replace('-modal', '');
                    hideModal(modalId);
                });
            });

            // ========================
            // CLICK RA NGOÀI ĐỂ ĐÓNG MODAL
            // ========================
            document.querySelectorAll('.user-info-modal').forEach(modal => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        const modalId = modal.id.replace('user-info-', '').replace('-modal', '');
                        hideModal(modalId);
                    }
                });
            });

            // ========================
            // XOÁ ĐỊA CHỈ (client-side, không gọi API hay form)
            // ========================
            document.querySelectorAll('.user-info-address-delete-btn').forEach(button => {
                button.addEventListener('click', () => {
                    if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
                        const addressItem = button.closest('.user-info-address-item');
                        if (addressItem) {
                            addressItem.remove(); // chỉ xóa trên giao diện
                            console.log('Address deleted');
                        }
                    }
                });
            });

            // ========================
            // XOÁ SẢN PHẨM YÊU THÍCH (client-side)
            // ========================
            document.querySelectorAll('.user-info-favorite-remove-btn').forEach(button => {
                button.addEventListener('click', () => {
                    if (confirm(
                            'Bạn có chắc chắn muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
                        const favoriteItem = button.closest('.user-info-favorite-item');
                        if (favoriteItem) {
                            favoriteItem.remove(); // chỉ xóa trên giao diện
                            console.log('Favorite deleted');
                        }
                    }
                });
            });

            // ========================
            // LỌC ĐƠN HÀNG THEO TRẠNG THÁI
            // ========================
            const filterTags = document.querySelectorAll('.user-info-order-filter-tag');
            const orderItems = document.querySelectorAll('.user-info-order-item');

            filterTags.forEach(tag => {
                tag.addEventListener('click', () => {
                    // Xóa active hiện tại
                    filterTags.forEach(t => t.classList.remove('active'));

                    // Thêm active mới
                    tag.classList.add('active');

                    const selectedStatus = tag.dataset.status;

                    // Lọc đơn hàng theo trạng thái
                    orderItems.forEach(item => {
                        const itemStatus = item.dataset.status;
                        if (selectedStatus === 'all' || selectedStatus === itemStatus) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
@endsection
