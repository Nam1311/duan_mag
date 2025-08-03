<div class="adnews-sidebar">
    <div class="adnews-sidebar-header">
        <h2><i class="fas fa-tshirt"></i> <span>Fashion Store</span></h2>
    </div>
    <div class="adnews-sidebar-section">
        <h3>Bảng điều khiển</h3>
        <a href="{{asset('/admin/')}}" class="adnews-sidebar-item {{ request()->is('admin') ? 'adnews-active' : '' }}"><i class="fas fa-home"></i><span>Trang chủ</span></a>
        <a href="{{asset('/admin/products')}}" class="adnews-sidebar-item {{ request()->is('admin/products*') ? 'adnews-active' : '' }}"><i class="fas fa-box-open"></i><span>Sản phẩm</span></a>
        <a href="{{asset('/admin/danhmuc')}}" class="adnews-sidebar-item {{ request()->is('admin/danhmuc*') ? 'adnews-active' : '' }}"><i class="fas fa-shopping-bag"></i><span>Danh mục</span></a>
        <a href="{{asset('/admin/orders')}}" class="adnews-sidebar-item {{ request()->is('admin/orders*') ? 'adnews-active' : '' }}"><i class="fas fa-shopping-bag"></i><span>Đơn hàng</span></a>
        <a href="{{asset('/admin/khuyenmai')}}" class="adnews-sidebar-item {{ request()->is('admin/khuyenmai*') ? 'adnews-active' : '' }}"><i class="fas fa-percentage"></i><span>Khuyến mãi</span></a>
        <a href="{{asset('/admin/countdown')}}" class="adnews-sidebar-item {{ request()->is('admin/countdown*') ? 'adnews-active' : '' }}"><i class="fas fa-percentage"></i><span>Count down</span></a>
        <a href="{{asset('/admin/baocao')}}" class="adnews-sidebar-item {{ request()->is('admin/baocao*') ? 'adnews-active' : '' }}"><i class="fas fa-chart-bar"></i><span>Báo cáo</span></a>
    </div>
    <div class="adnews-sidebar-section">
        <h3>Công cụ</h3>
        <a href="{{ asset('/admin/quanlybanner') }}"
            class="adnews-sidebar-item {{ request()->is('admin/quanlybanner') ? 'adnews-active' : '' }}"><i
                class="fas fa-images"></i><span>Quản lý banner</span></a>
        <a href="{{ asset('/admin/quanlykho') }}"
            class="adnews-sidebar-item {{ request()->is('admin/quanlykho*') ? 'adnews-active' : '' }}"><i
                class="fas fa-warehouse"></i><span>Quản lý kho</span></a>
        <a href="{{ asset('/admin/quanlyhinhanh') }}"
            class="adnews-sidebar-item {{ request()->is('admin/quanlyhinhanh*') ? 'adnews-active' : '' }}"><i
                class="fas fa-images"></i><span>Hình ảnh sản phẩm</span></a>
        <a href="{{ asset('/admin/quanlykhachhang') }}"
            class="adnews-sidebar-item {{ request()->is('admin/quanlykhachhang*') ? 'adnews-active' : '' }}"><i
                class="fas fa-users"></i><span>Khách hàng</span></a>
        <a href="{{ asset('/admin/quanlynguoidung') }}"
            class="adnews-sidebar-item {{ request()->is('admin/quanlynguoidung*') ? 'adnews-active' : '' }}"><i
                class="fas fa-user-cog"></i><span>Quản lý người dùng</span></a>
    </div>
    <div class="adnews-sidebar-section">
        <h3>Nội dung</h3>
        <a href="{{ asset('/admin/news') }}"
            class="adnews-sidebar-item {{ request()->is('admin/news*') ? 'adnews-active' : '' }}"><i
                class="fas fa-newspaper"></i><span>Tin tức</span></a>
    </div>
    <div class="adnews-sidebar-section">
        <h3>Hệ thống</h3>
        <a href="{{ asset('/admin/caidat') }}"
            class="adnews-sidebar-item {{ request()->is('admin/caidat*') ? 'adnews-active' : '' }}"><i
                class="fas fa-cog"></i><span>Cài đặt</span></a>
        <a href="{{ asset('/admin/hotro') }}"
            class="adnews-sidebar-item {{ request()->is('admin/hotro*') ? 'adnews-active' : '' }}"><i
                class="fas fa-headset"></i><span>Hỗ trợ</span></a>
        <a href="{{ asset('/admin/quanlylienhe') }}"
            class="adnews-sidebar-item {{ request()->is('admin/quanlylienhe*') ? 'adnews-active' : '' }}"><i
                class="fas fa-headset"></i><span>Lien he</span></a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Lưu trạng thái active vào localStorage khi click
    document.querySelectorAll('.adnews-sidebar-item').forEach(item => {
        item.addEventListener('click', function() {
            const path = this.getAttribute('href').replace("{{asset('')}}", '');
            localStorage.setItem('activeSidebarItem', path);
        });

        // Kiểm tra và thêm class active từ localStorage
        const activePath = localStorage.getItem('activeSidebarItem');
        if (activePath && item.getAttribute('href').replace("{{asset('')}}", '') === activePath) {
            item.classList.add('adnews-active');
        }
    });

    // Thêm active class dựa trên URL hiện tại nếu không có trong localStorage
    if (!localStorage.getItem('activeSidebarItem')) {
        const currentPath = window.location.pathname.replace("{{asset('')}}", '');
    document.addEventListener('DOMContentLoaded', () => {
        // Lưu trạng thái active vào localStorage khi click
        document.querySelectorAll('.adnews-sidebar-item').forEach(item => {
            item.addEventListener('click', function() {
                const path = this.getAttribute('href').replace("{{ asset('') }}", '');
                localStorage.setItem('activeSidebarItem', path);
            });

            // Kiểm tra và thêm class active từ localStorage
            const activePath = localStorage.getItem('activeSidebarItem');
            if (activePath && item.getAttribute('href').replace("{{ asset('') }}", '') ===
                activePath) {
                item.classList.add('adnews-active');
            }
        });
    }
});

        // Thêm active class dựa trên URL hiện tại nếu không có trong localStorage
        if (!localStorage.getItem('activeSidebarItem')) {
            const currentPath = window.location.pathname.replace("{{ asset('') }}", '');
            document.querySelectorAll('.adnews-sidebar-item').forEach(item => {
                if (item.getAttribute('href').replace("{{ asset('') }}", '') === currentPath) {
                    item.classList.add('adnews-active');
                }
            });
        }
    });
</script>
