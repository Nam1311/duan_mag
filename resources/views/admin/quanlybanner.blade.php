@extends('admin.app')

@section('admin.body')
    <link rel="stylesheet" href="{{ asset('/css/admin/banner.css') }}">

<div class="container">
    <h2 class="h2-banner">Quản lý Banner</h2>
    <button class="btn btn-primary" onclick="openModal()">+ Thêm Banner</button>

    {{-- Danh sách banner --}}
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tiêu đề</th>
                <th>Link</th>
                <th>CTA</th>
                <th>Trạng thái</th>
                <th>Thứ tự</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($banners as $banner)
            <tr>
                <td><img src="{{ asset($banner->image) }}" width="150"></td>
                <td>{{ $banner->title }}</td>
                <td>{{ $banner->link }}</td>
                <td>{{ $banner->cta_text}}</td>
                <td>{{ $banner->status ? 'Hiện' : 'Ẩn' }}</td>
                <td>{{ $banner->sort_order }}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editBanner({{ $banner }})"><i style="font-size: 13px" class="fa fa-pencil" aria-hidden="true"></i></button>
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa banner này?')"><i style="font-size: 13px; color: white;" class="fa fa-trash" aria-hidden="true"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Thêm/Sửa --}}
<div id="bannerModal" class="modal" style="display:none;">
    <div class="modal-content p-4">
        <h4 id="modalTitle">Thêm Banner</h4>
        <form id="bannerForm" action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="bannerId">

            <div class="form-group">
                <label>Tiêu đề</label>
                <input type="text" name="title" id="title" class="form-control">
            </div>

            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Ảnh</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="form-group">
                <label>Link</label>
                <input type="text" name="link" id="link" class="form-control" placeholder="VD: /product/123 hoặc https://...">
            </div>

            <div class="form-group">
                <label>Văn bản nút CTA</label>
                <input type="text" name="cta_text" id="cta_text" class="form-control">
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status" id="status" class="form-control">
                    <option value="1">Hiển thị</option>
                    <option value="0">Ẩn</option>
                </select>
            </div>

            <div class="form-group">
                <label>Thứ tự hiển thị</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
        </form>
    </div>
</div>

{{-- CSS đơn giản --}}
<style>
/* .modal {
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center;
}
.modal-content {
    background: white; border-radius: 8px; width: 500px;
} */
</style>

{{-- Script popup --}}
<script>
function openModal() {
    document.getElementById('bannerModal').style.display = 'flex';
    document.getElementById('modalTitle').innerText = 'Thêm Banner';
    document.getElementById('bannerForm').reset();
    document.getElementById('bannerForm').action = "{{ route('admin.banners.store') }}";
}

function closeModal() {
    document.getElementById('bannerModal').style.display = 'none';
}

function editBanner(banner) {
    openModal();
    document.getElementById('modalTitle').innerText = 'Sửa Banner';
    document.getElementById('bannerForm').action = `/admin/banners/${banner.id}`;
    document.querySelector('#bannerForm').insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');

    document.getElementById('bannerId').value = banner.id;
    document.getElementById('title').value = banner.title ?? '';
    document.getElementById('description').value = banner.description ?? '';
    document.getElementById('link').value = banner.link ?? '';
    document.getElementById('cta_text').value = banner.cta_text ?? '';
    document.getElementById('status').value = banner.status;
    document.getElementById('sort_order').value = banner.sort_order ?? 0;
}
</script>
@endsection
