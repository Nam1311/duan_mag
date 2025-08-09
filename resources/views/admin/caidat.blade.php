@extends('admin.app')

@section('admin.body')
<div class="adsupot-main-content">
    <div class="adsupot-header">
        <div class="adsupot-search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Tìm kiếm cài đặt..." id="adsupot-searchInput">
        </div>
        <div class="adsupot-user-profile">
            <div class="adsupot-notification-bell adsupot-tooltip" data-tooltip="Thông báo"><i class="fas fa-bell"></i></div>
            <div class="adsupot-profile-avatar">QT</div>
        </div>
    </div>
    <h1 class="adsupot-page-title">Cài đặt cửa hàng</h1>
    <p class="adsupot-page-subtitle">Quản lý thông tin, tùy chỉnh và bảo mật cửa hàng</p>

    <div class="adsupot-tab-nav">
        <button class="adsupot-tab-btn adsupot-active" data-tab="adsupot-store-info">Thông tin cửa hàng</button>
        <button class="adsupot-tab-btn" data-tab="adsupot-shipping">Giao hàng</button>
    </div>

    {{-- Form --}}
    <form id="settingsForm" enctype="multipart/form-data">
        @csrf
        {{-- Tab 1 --}}
        <div class="adsupot-tab-content adsupot-active" id="adsupot-store-info">
            <div class="adsupot-form-group">
                <label>Tên cửa hàng <i class="fas fa-store"></i></label>
                <input type="text" name="store_name" value="{{ $setting->store_name }}">
            </div>
            <div class="adsupot-form-group">
                <label>Địa chỉ email <i class="fas fa-envelope"></i></label>
                <input type="email" name="email" value="{{ $setting->email }}">
            </div>
            <div class="adsupot-form-group">
                <label>Số điện thoại <i class="fas fa-phone"></i></label>
                <input type="tel" name="phone" value="{{ $setting->phone }}">
            </div>
            <div class="adsupot-form-group">
                <label>Địa chỉ <i class="fas fa-map-marker-alt"></i></label>
                <input type="text" name="address" value="{{ $setting->address }}">
            </div>
            <div class="adsupot-form-group">
                <label>Giờ làm việc <i class="fas fa-clock"></i></label>
                <input type="text" name="working_hours" value="{{ $setting->working_hours }}">
            </div>
            <div class="adsupot-form-group">
                <label>Mô tả cửa hàng <i class="fas fa-info-circle"></i></label>
                <textarea name="description">{{ $setting->description }}</textarea>
            </div>
            <div class="adsupot-form-group adsupot-file-group">
                <div>
                    <label>Logo cửa hàng <i class="fas fa-image"></i></label>
                    <input type="file" name="logo" accept="image/*">
                </div>
                @if($setting->logo)
                    <img src="{{ asset('storage/'.$setting->logo) }}" alt="Logo" style="width:80px">
                @else
                    <img src="https://via.placeholder.com/80" alt="Logo">
                @endif
            </div>
        </div>

        {{-- Tab 2 --}}
        <div class="adsupot-tab-content" id="adsupot-shipping">
            <div class="adsupot-form-group">
                <label>Phí giao hàng mặc định (VND) <i class="fas fa-truck"></i></label>
                <input type="number" name="ship_price" value="{{ $setting->ship_price }}">
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="adsupot-form-actions">
            <button type="submit" class="adsupot-btn adsupot-btn-primary">Lưu</button>
            <button type="reset" class="adsupot-btn adsupot-btn-secondary">Hủy</button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Tab navigation
    const tabBtns = document.querySelectorAll(".adsupot-tab-btn");
    const tabContents = document.querySelectorAll(".adsupot-tab-content");
    tabBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            tabBtns.forEach(b => b.classList.remove("adsupot-active"));
            tabContents.forEach(c => c.classList.remove("adsupot-active"));
            btn.classList.add("adsupot-active");
            document.getElementById(btn.dataset.tab).classList.add("adsupot-active");
        });
    });

    // Submit form via AJAX
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("{{ route('admin.settings.update') }}", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success'){
                alert(data.message);
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message ?? 'Không xác định'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi kết nối máy chủ!');
        });
    });
});
</script>
@endsection
