<!-- sidebar.html -->
<div class="adbl-sidebar">
    <div class="adbl-sidebar-header">
        <h2><i class="fas fa-tshirt"></i> <span>Fashion Store</span></h2>
    </div>
    <div class="adbl-sidebar-section">
        <h3>Bảng điều khiển</h3>
        <a href="{{ asset('/admin/') }}" class="adbl-sidebar-item {{ request()->is('admin') ? 'adbl-active' : '' }}"><i class="fas fa-home"></i><span>Trang chủ</span></a>
        <a href="{{ asset('/admin/products') }}" class="adbl-sidebar-item {{ request()->is('admin/products*') ? 'adbl-active' : '' }}"><i class="fas fa-box-open"></i><span>Sản phẩm</span></a>
        <a href="{{ asset('/admin/danhmuc') }}" class="adbl-sidebar-item {{ request()->is('admin/danhmuc*') ? 'adbl-active' : '' }}"><i class="fas fa-shopping-bag"></i><span>Danh mục</span></a>
        <a href="{{ asset('/admin/orders') }}" class="adbl-sidebar-item {{ request()->is('admin/orders*') ? 'adbl-active' : '' }}"><i class="fas fa-shopping-cart"></i><span>Đơn hàng</span></a>
        <a href="{{ asset('/admin/khuyenmai') }}" class="adbl-sidebar-item {{ request()->is('admin/khuyenmai*') ? 'adbl-active' : '' }}"><i class="fas fa-tags"></i><span>Khuyến mãi</span></a>
        <a href="{{ asset('/admin/countdown') }}" class="adbl-sidebar-item {{ request()->is('admin/countdown*') ? 'adbl-active' : '' }}"><i class="fas fa-clock"></i><span>Count down</span></a>
        <a href="{{ asset('/admin/baocao') }}" class="adbl-sidebar-item {{ request()->is('admin/baocao*') ? 'adbl-active' : '' }}"><i class="fas fa-chart-bar"></i><span>Báo cáo</span></a>
    </div>
    <div class="adbl-sidebar-section">
        <h3>Công cụ</h3>
        <a href="{{ asset('/admin/quanlykho') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlykho*') ? 'adbl-active' : '' }}"><i class="fas fa-warehouse"></i><span>Quản lý kho</span></a>
        <a href="{{ asset('/admin/quanlyhinhanh') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlyhinhanh*') ? 'adbl-active' : '' }}"><i class="fas fa-images"></i><span>Hình ảnh sản phẩm</span></a>
        <a href="{{ asset('/admin/quanlykhachhang') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlykhachhang*') ? 'adbl-active' : '' }}"><i class="fas fa-users"></i><span>Khách hàng</span></a>
        <a href="{{ asset('/admin/quanlybanner') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlybanner*') ? 'adbl-active' : '' }}"><i class="fas fa-user-cog"></i><span>Quản lý Banner</span></a>
        <a href="{{ asset('/admin/quanlynguoidung') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlynguoidung*') ? 'adbl-active' : '' }}"><i class="fas fa-user-cog"></i><span>Quản lý người dùng</span></a>
    </div>
    <div class="adbl-sidebar-section">
        <h3>Nội dung</h3>
        <a href="{{ asset('/admin/quanlytintuc') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlytintuc*') ? 'adbl-active' : '' }}"><i class="fas fa-newspaper"></i><span>Tin tức</span></a>
        <a href="{{ asset('/admin/comments') }}" class="adbl-sidebar-item {{ request()->is('admin/comments*') ? 'adbl-active' : '' }}"><i class="fas fa-comments"></i><span>Bình luận</span></a>
    </div>
    <div class="adbl-sidebar-section">
        <h3>Hệ thống</h3>
        <a href="{{ asset('/admin/caidat') }}" class="adbl-sidebar-item {{ request()->is('admin/caidat*') ? 'adbl-active' : '' }}"><i class="fas fa-cog"></i><span>Cài đặt</span></a>
        <a href="{{ asset('/admin/hotro') }}" class="adbl-sidebar-item {{ request()->is('admin/hotro*') ? 'adbl-active' : '' }}"><i class="fas fa-headset"></i><span>Hỗ trợ</span></a>
        <a href="{{ asset('/admin/quanlylienhe') }}" class="adbl-sidebar-item {{ request()->is('admin/quanlylienhe*') ? 'adbl-active' : '' }}"><i class="fas fa-envelope"></i><span>Liên hệ</span></a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebarItems = document.querySelectorAll('.adbl-sidebar-item');
    const assetPrefix = "{{ asset('') }}";

    sidebarItems.forEach(item => {
        // Save active state to localStorage when clicked
        item.addEventListener('click', function() {
            const path = this.getAttribute('href').replace(assetPrefix, '');
            localStorage.setItem('activeSidebarItem', path);
        });

        // Check and add active class from localStorage
        const activePath = localStorage.getItem('activeSidebarItem');
        if (activePath && item.getAttribute('href').replace(assetPrefix, '') === activePath) {
            item.classList.add('adbl-active');
        }
    });

    // If no localStorage, activate based on current URL
    if (!localStorage.getItem('activeSidebarItem')) {
        const currentPath = window.location.pathname.replace(assetPrefix, '');
        sidebarItems.forEach(item => {
            if (item.getAttribute('href').replace(assetPrefix, '') === currentPath) {
                item.classList.add('adbl-active');
            }
        });
    }

    // Responsive behavior
    function handleSidebarResponsive() {
        const sidebar = document.querySelector('.adbl-sidebar');
        if (!sidebar) return;

        if (window.innerWidth <= 768) {
            sidebar.style.width = '100%';
            sidebar.style.height = 'auto';
            sidebar.style.position = 'relative';
        } else if (window.innerWidth <= 1024) {
            sidebar.style.width = '80px';
        } else {
            sidebar.style.width = '280px';
        }
    }

    window.addEventListener('resize', handleSidebarResponsive);
    handleSidebarResponsive();
});
</script>
