@extends('admin.app')

@section('admin.body')
    <div class="adnews-main-content">
        <div class="adnews-header">
            <div class="adnews-search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm tin tức..." id="adnews-searchInput" onkeyup="searchNews()" oninput="searchNews()">
            </div>
            <div class="adnews-user-profile">
                <div class="adnews-notification-bell adnews-tooltip" data-tooltip="Thông báo"><i class="fas fa-bell"></i>
                </div>
                <div class="adnews-profile-avatar">QT</div>
            </div>
        </div>
        <h1 class="adnews-page-title">Quản lý tin tức</h1>
        <p class="adnews-page-subtitle">Tạo, chỉnh sửa và quản lý bài viết tin tức cho cửa hàng</p>
        <div class="adnews-tab-nav">
            <button class="adnews-tab-btn adnews-active" data-tab="adnews-list">Danh sách tin tức</button>
            <button class="adnews-tab-btn" data-tab="adnews-stats">Thống kê</button>
            <button class="adnews-tab-btn" data-tab="adnews-chat">Chat tạo tin tức</button>

            <button class="adnews-tab-btn" data-tab="adnews-settings">Quản lý danh mục</button>
        </div>
        <div class="adnews-tab-content adnews-active" id="adnews-list">
            <div class="adnews-actions">
                <form action="{{route('admin.new.index')}} " method="GET">
                    <div class="adnews-filters">



                        <div class="adnews-filter-group">
                            <label>Danh mục <i class="fas fa-list"></i></label>
                            <select id="adnews-categoryFilter" name="category_id" onchange="this.form.submit()">
                                <option value="">Tất cả</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $uniqueStatuses = $news->pluck('status')->unique();
                        @endphp


                        <div class="adnews-filter-group">
                            <label>Trạng thái <i class="fas fa-info-circle"></i></label>
                            <select id="adnews-statusFilter" name="status" onchange="this.form.submit()">
                                <option value="">Tất cả</option>
                                @foreach ($uniqueStatuses as $status)
                                    <option value=" {{ $status }} " {{request('status') == $status ? 'selected' : ''}}>{{$status}}
                                    </option>

                                @endforeach
                                {{-- <option value="Bản nháp">Bản nháp</option> --}}
                            </select>
                        </div>


                        {{--
                        <div class="adnews-filter-group">
                            <label>Ngày <i class="fas fa-calendar"></i></label>
                            <input type="date" name="dateFillter" id="adnews-dateFilter" value=""
                                onchange="this.form.submit()">
                        </div> --}}


                        {{-- <div class="adnews-filter-group">
                            <label>Tác giả <i class="fas fa-user"></i></label>
                            <select id="adnews-authorFilter">
                                <option value="">Tất cả</option>
                                <option value="Quản Trị">Quản Trị</option>
                                <option value="Nhân Viên">Nhân Viên</option>
                            </select>
                        </div> --}}



                    </div>
                </form>
                <button class="adnews-btn adnews-btn-primary adnews-tooltip" data-tooltip="Thêm tin tức"
                    onclick="openAddModal('')">Thêm tin tức</button>
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="adnews-data-card">
                <div class="adnews-table-container">
                    <table class="adnews-data-table">
                        <thead>
                            <tr>
                                <th>Hình</th>
                                <th>Tiêu đề</th>
                                <th>Danh mục</th>
                                <th>Tác giả</th>
                                <th>Lượt xem</th>
                                {{-- <th>Ngày Tạo</th> --}}
                                <th>Ngày đăng</th>
                                <th>Ngày sửa</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="adnews-newsTableBody">

                            @foreach ($news as $new)
                                <tr data-id="{{$new->id}}" data-title="{{$new->title}}"
                                    data-category="{{$new->new_category->name}}" data-author="{{$new->author}}"
                                    data-date="{{$new->created_at}}" data-status="{{$new->status}}"
                                    data-excerpt="{{$new->description}}" data-content="{{ $new->content }}"
                                    data-tags="thời trang, thu đông" data-image="{{asset('')}}img/{{$new->image}}">
                                    <td><img src="{{asset('')}}img/{{$new->image}}" alt="Thumbnail" class="adnews-thumbnail">
                                    </td>
                                    <td>{{$new->title}}</td>
                                    <td>{{$new->new_category->name}}</td>
                                    <td>{{$new->author}}</td>
                                    <td>{{$new->views}}</td>
                                    {{-- <td>{{$new->created_at}}</td> --}}
                                    <td>{{$new->posted_date}}</td>
                                    <td>{{$new->updated_at}}</td>
                                    <td><span class="adnews-status-badge" data-id="{{ $new->id }}">{{ $new->status }}</span></td>
                                    <td>
                                        <button class="adnews-btn adnews-btn-secondary adnews-tooltip" data-tooltip="Xem trước"
                                            onclick="adnewsPreviewNews({{$new->id}})">Xem</button>
                                        <button class="adnews-btn adnews-btn-primary adnews-tooltip"
                                            onclick="openEditModal({{ $new->id }})">Sửa</button>
                                        <button class="adnews-btn adnews-btn-outline adnews-tooltip" data-tooltip="Xóa"
                                            onclick="adnewsDeleteNews({{$new->id}})">Xóa</button>

                                        


                                        <button class="adnews-btn adnews-btn-toggle adnews-tooltip""
                                            onclick="adnewsTogglePublish({{ $new->id }}, '{{ $new->status }}')">
                                            {{ $new->status == "Đã xuất bản" ? "Hủy" : "Xuất bản" }}
                                        </button>

                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                </div>
            </div>

            <div class="adnews-pagination" id="adnews-pagination">
                @for ($i = 1; $i <= $news->lastPage(); $i++)
                    <a href="{{ $news->url($i) }}"
                        class="adnews-pagination-btn {{ $news->currentPage() == $i ? 'adnews-active' : '' }}">
                        {{ $i }}
                    </a>
                @endfor

            </div>
            {{-- <div class="adnews-pagination" id="adnews-pagination"></div> --}}

        </div>
        <div class="adnews-tab-content" id="adnews-stats">
            <div class="adnews-data-card">
                <div class="adnews-table-container">
                    <table class="adnews-data-table">
                        <thead>
                            <tr>
                                <th>Danh mục</th>
                                <th>Số bài viết đã đăng</th>
                                <th>Lượt xem tổng</th>
                                <th>Lượt xem trung bình</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{$category->name}}</td>
                                    <td>{{ $category->news_count }}</td>

                                    <td>{{$category->total_views ?? 0}}</td>
                                    <td>{{$category->total_views > 0 ? round($category->total_views / $category->news_count, 0) : 0}}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- <div id="notificationArea"></div> --}}
        <div class="adnews-tab-content" id="adnews-chat">
            <!-- Form thông tin bổ sung -->

            <!-- Khu vực chat -->
            <div class="content">
                <section class="form-section">
                    <h2 class="section-title"><i class="fas fa-edit"></i> Tạo tin tức với AI của MAG</h2>

                    <div class="form-group">
                        <label for="basicIdea"><i class="fas fa-lightbulb"></i> Ý tưởng ban đầu</label>
                        <textarea id="basicIdea" placeholder="Nhập ý tưởng ban đầu..."></textarea>
                        <div class="char-count" id="ideaCount">0/500 ký tự</div>
                    </div>

                    <div class="form-group">
                        <label for="genre"><i class="fas fa-book"></i> Thể loại</label>
                        <select id="genre">
                            <option value="Giải trí" selected>Giải trí</option>
                            <option value="Trò chơi">Trò chơi</option>
                            <option value="Tâm lý">Tâm lý</option>
                            <option value="Phiêu lưu">Phiêu lưu</option>
                            <option value="Hành động">Hành động</option>
                            <option value="Tài chính - Kinh doanh">Tài chính - Kinh doanh</option>
                            <option value="Đời sống - Xã hội">Đời sống - Xã hội</option>
                            <option value="Tâm linh - Tôn giáo">Tâm linh - Tôn giáo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="length"><i class="fas fa-ruler"></i> Độ dài mong muốn (ký tự)</label>
                        <input type="number" id="length" min="1000" max="10000" value="4000">
                    </div>

                    <div class="form-group">
                        <label for="ageGroup"><i class="fas fa-users"></i> Độ tuổi</label>
                        <select id="ageGroup">
                            <option value="Mọi lứa tuổi">Mọi lứa tuổi</option>
                            <option value="Trẻ em">Trẻ em</option>
                            <option value="Thiếu niên">Thiếu niên</option>
                            <option value="Thanh niên">Thanh niên</option>
                            <option value="Người lớn">Người lớn</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-ban"></i> Nội dung bị cấm</label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="violence" checked>
                                <label for="violence">Bạo lực</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="racism" checked>
                                <label for="racism">Phân biệt chủng tộc</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="sex" checked>
                                <label for="sex">Tình dục</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bannedWords"><i class="fas fa-exclamation-triangle"></i> Từ ngữ bị cấm (cách nhau bằng
                            dấu phẩy)</label>
                        <input type="text" id="bannedWords" placeholder="Ví dụ: chết chóc, máu me, khiêu dâm...">
                    </div>

                    <button id="generateBtn">
                        <i class="fas fa-magic"></i> Tạo với AI
                    </button>

                    <div class="ai-indicator">
                        <i class="fas fa-brain"></i> AI đang sử dụng công nghệ GPT-4 để tạo nội dung
                    </div>
                </section>

                <section class="result-section">
                    {{-- <h2 class="section-title"><i class="fas fa-book-open"></i> Câu chuyện được tạo</h2> --}}

                    <div id="notificationArea" class="notification"></div>

                    <div class="result-container">
                        <div id="story_img"></div>
                        <div id="storyOutput">
                            <div class="instruction-text">Nhập thông tin và nhấn "Tạo truyện với AI" để bắt đầu quá trình
                                tạo
                                truyện tự động.</div>
                        </div>
                    </div>
                </section>
            </div>
        </div>







        {{-- Quản lý danh mục --}}
        <div class="adnews-tab-content" id="adnews-settings">
            <h2 class="section-title"><i class="fas fa-tags"></i> Quản lý danh mục tin tức</h2>
            
            <!-- Form thêm danh mục mới -->
            <div class="adnews-data-card">
                <h3>Thêm danh mục mới</h3>
                <form id="addCategoryForm" class="category-form">
                    @csrf
                    <div class="adnews-form-row">
                        <div class="adnews-form-group">
                            <label for="categoryName">Tên danh mục <span class="required">*</span></label>
                            <input type="text" id="categoryName" name="name" placeholder="Nhập tên danh mục" required>
                        </div>
                        <div class="adnews-form-group">
                            <label for="categoryDescription">Mô tả</label>
                            <textarea id="categoryDescription" name="description" placeholder="Nhập mô tả danh mục" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="adnews-form-actions">
                        <button type="submit" class="adnews-btn adnews-btn-primary">
                            <i class="fas fa-plus"></i> Thêm danh mục
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danh sách danh mục -->
            <div class="adnews-data-card">
                <div class="category-header">
                    <h3>Danh sách danh mục</h3>
                    <div class="category-search">
                        <input type="text" id="categorySearchInput" placeholder="Tìm kiếm danh mục..." class="search-input" onkeyup="searchCategories()">
                        <button type="button" class="search-btn" onclick="searchCategories()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="adnews-table-container">
                    <table class="adnews-data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th>Số bài viết</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            @foreach($categories as $category)
                            <tr data-id="{{ $category->id }}">
                                <td>{{ $category->id }}</td>
                                <td class="category-name">{{ $category->name }}</td>
                                <td class="category-description">{{ $category->description ?? 'Không có mô tả' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $category->news_count }} bài</span>
                                </td>
                                <td>{{ $category->created_at ? $category->created_at->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="adnews-btn adnews-btn-outline adnews-btn-sm" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}')">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <button class="adnews-btn adnews-btn-danger adnews-btn-sm" onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}', {{ $category->news_count }})">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal sửa danh mục -->
        <div class="adnews-modal" id="editCategoryModal" style="display: none;">
            <div class="adnews-modal-content">
                <h2>Chỉnh sửa danh mục</h2>
                <form id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editCategoryId">
                    <div class="adnews-form-group">
                        <label for="editCategoryName">Tên danh mục <span class="required">*</span></label>
                        <input type="text" id="editCategoryName" name="name" required>
                    </div>
                    <div class="adnews-form-group">
                        <label for="editCategoryDescription">Mô tả</label>
                        <textarea id="editCategoryDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="adnews-modal-actions">
                        <button type="button" class="adnews-btn adnews-btn-secondary" onclick="closeEditCategoryModal()">Hủy</button>
                        <button type="submit" class="adnews-btn adnews-btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>



        {{-- thêm --}}
        <div class="adnews-modal" id="addModal" style="display:none;">
            <div class="adnews-modal-content">
                <form action="{{ route('admin.new.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h2>Thêm bài viết tin tức</h2>

                    <!-- Title -->
                    <div class="adnews-form-group">
                        <label>Tiêu đề</label>
                        <input type="text" name="title" placeholder="Nhập tiêu đề">
                    </div>

                    <!-- Description -->
                    <div class="adnews-form-group">
                        <label>Mô tả</label>
                        <textarea name="description"></textarea>
                    </div>

                    <!-- Content -->
                    <div class="adnews-form-group">
                        <label>Nội dung</label>
                        <textarea name="content"></textarea>
                    </div>

                    <!-- Author -->
                    <div class="adnews-form-group">
                        <label>Tác giả</label>
                        <input type="text" name="author">
                    </div>

                    <!-- Category -->
                    <div class="adnews-form-group">
                        <label>Danh mục</label>
                        <select name="category_id">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Posted Date -->
                    <div class="adnews-form-group">
                        <label>Ngày đăng</label>
                        <input type="datetime-local" name="posted_date">
                    </div>

                    <!-- Image -->
                    <div class="adnews-form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="image" accept="image/*">
                    </div>

                    <div class="adnews-modal-actions">
                        <button type="button" onclick="closeAddModal()">Hủy</button>
                        <button type="submit">Lưu</button>
                    </div>
                </form>
            </div>
        </div>


        {{-- sửa --}}

        <div class="adnews-modal" id="editModal" style="display:none;">
            <div class="adnews-modal-content">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <h2>Chỉnh sửa bài viết tin tức</h2>

                    <!-- Title -->
                    <div class="adnews-form-group">
                        <label>Tiêu đề</label>
                        <input type="text" name="title" id="editTitle">
                    </div>

                    <!-- Description -->
                    <div class="adnews-form-group">
                        <label>Mô tả</label>
                        <textarea name="description" id="editDescription"></textarea>
                    </div>

                    <!-- Content -->
                    <div class="adnews-form-group">
                        <label>Nội dung</label>
                        <textarea name="content" id="editContent"></textarea>
                    </div>

                    <!-- Author -->
                    <div class="adnews-form-group">
                        <label>Tác giả</label>
                        <input type="text" name="author" id="editAuthor">
                    </div>

                    <!-- Category -->
                    <div class="adnews-form-group">
                        <label>Danh mục</label>
                        <select name="category_id" id="editCategory">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Posted Date -->
                    <div class="adnews-form-group">
                        <label>Ngày đăng</label>
                        <input type="datetime-local" name="posted_date" id="editDate">
                    </div>

                    <!-- Image -->
                    <div class="adnews-form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="image" id="editImage" accept="image/*">
                    </div>

                    <div class="adnews-modal-actions">
                        <button type="button" onclick="closeEditModal()">Hủy</button>
                        <button type="submit">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>


        {{-- xem bài viết --}}
        <div class="adnews-modal" id="adnews-previewModal">
            <div class="adnews-modal-content">
                <h2 id="adnews-previewTitle">Xem bài viết</h2>
                <img id="adnews-previewImage" class="adnews-image-preview" alt="News Image">
                <p id="adnews-previewExcerpt" style="margin: 1rem 0; color: var(--text-muted);"></p>
                <div id="adnews-previewContent" style="font-size: 0.9rem;"></div>
                <div class="adnews-modal-actions">
                    <button class="adnews-btn adnews-btn-primary adnews-tooltip" data-tooltip="Đóng"
                        onclick="adnewsCloseModal('preview')">Đóng</button>
                </div>
            </div>
        </div>


        <style>
            /* Search bar enhancements */
            .adnews-search-bar {
                position: relative;
                display: flex;
                align-items: center;
            }

            .adnews-search-bar #adnews-searchInput {
                transition: all 0.3s ease;
                border-radius: 25px;
                padding-left: 45px;
                padding-right: 45px;
            }

         

            .adnews-search-bar .fas.fa-search {
                position: absolute;
                left: 15px;
                color: #6c757d;
                z-index: 2;
            }

            .search-clear-btn:hover {
                background-color: #f8f9fa !important;
                color: #495057 !important;
            }

            .search-result-message {
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Highlight search results */
            .adnews-data-table tr:not([style*="display: none"]) {
                transition: background-color 0.3s ease;
            }

            .adnews-data-table tr:not([style*="display: none"]):hover {
                background-color: #f8f9fa;
            }

            /* CSS cho quản lý danh mục */
            .category-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .category-search {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .search-input {
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 6px;
                width: 250px;
                font-size: 14px;
            }

            .search-input:focus {
                outline: none;
                border-color: #007bff;
                box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
            }

            .search-btn {
                padding: 8px 12px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .search-btn:hover {
                background: #0056b3;
            }
            .category-form {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            
            .adnews-form-row {
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 20px;
                margin-bottom: 15px;
            }
            
            .section-title {
                color: #333;
                margin-bottom: 20px;
                font-size: 1.5rem;
                font-weight: 600;
            }
            
            .required {
                color: #dc3545;
            }
            
            .badge {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: 500;
            }
            
            .badge-info {
                background-color: #d1ecf1;
                color: #0c5460;
            }
            
            .action-buttons {
                display: flex;
                gap: 8px;
            }
            
            .adnews-btn-sm {
                padding: 4px 8px;
                font-size: 0.75rem;
            }
            
            .adnews-btn-danger {
                background-color: #dc3545;
                color: white;
                border: 1px solid #dc3545;
            }
            
            .adnews-btn-danger:hover {
                background-color: #c82333;
                border-color: #bd2130;
            }
            
            .adnews-table-container {
                overflow-x: auto;
            }
            
            .adnews-data-table th,
            .adnews-data-table td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #dee2e6;
            }
            
            .adnews-data-table th {
                background-color: #f8f9fa;
                font-weight: 600;
                color: #495057;
            }
            
            .category-name {
                font-weight: 500;
                color: #007bff;
            }
            
            .category-description {
                color: #6c757d;
                font-style: italic;
            }

            /* Toast styles từ hệ thống có sẵn */
            .custom-toast {
                position: fixed;
                top: 24px;
                right: 24px;
                background: #fff;
                color: #1f2937;
                padding: 0;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                font-size: 14px;
                animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                z-index: 9999;
                width: 350px;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                transition: all 0.3s ease;
            }

            .custom-toast:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            }

            .toast-content {
                display: flex;
                align-items: center;
                padding: 16px;
                position: relative;
            }

            .toast-icon {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #10b981;
                border-radius: 50%;
                color: white;
                flex-shrink: 0;
                margin-right: 12px;
                padding: 4px;
            }

            .toast-icon-error {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #ef4444;
                border-radius: 50%;
                color: white;
                flex-shrink: 0;
                margin-right: 12px;
                padding: 4px;
            }

            .toast-icon-warning {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f59e0b;
                border-radius: 50%;
                color: white;
                flex-shrink: 0;
                margin-right: 12px;
                padding: 4px;
            }

            .toast-icon-info {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #3b82f6;
                border-radius: 50%;
                color: white;
                flex-shrink: 0;
                margin-right: 12px;
                padding: 4px;
            }

            .toast-icon svg,
            .toast-icon-error svg,
            .toast-icon-warning svg,
            .toast-icon-info svg {
                width: 16px;
                height: 16px;
            }

            .toast-message {
                flex: 1;
                line-height: 1.5;
                padding-right: 8px;
            }

            .toast-close {
                background: none;
                border: none;
                color: #9ca3af;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s ease;
            }

            .toast-close:hover {
                color: #6b7280;
            }

            .toast-close svg {
                width: 14px;
                height: 14px;
            }

            .toast-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 4px;
                background: linear-gradient(90deg, #10b981, #34d399);
                animation: progress 5s linear;
                border-radius: 0 0 12px 12px;
            }

            @keyframes progress {
                from { width: 100%; }
                to { width: 0%; }
            }

            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        </style>


        <script>
            // document.addEventListener('DOMContentLoaded', () => {

            // đóng
            window.adnewsCloseModal = function (type = 'news') {
                document.getElementById(type === 'preview' ? 'adnews-previewModal' : 'adnews-newsModal').style.display = 'none';
                const formGroups = document.querySelectorAll('#adnews-newsModal .adnews-form-group');
                formGroups.forEach(group => group.classList.remove('adnews-error'));
            };
            // Open/Close Add
            function openAddModal() {
                document.getElementById('addModal').style.display = 'flex';
            }
            function closeAddModal() {
                document.getElementById('addModal').style.display = 'none';
            }

            // Open/Close Edit
            function openEditModal(id) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                const form = document.getElementById('editForm');
                form.action = `/admin/news/update/${id}`;
                document.getElementById('editTitle').value = row.dataset.title;
                document.getElementById('editDescription').value = row.dataset.excerpt;
                document.getElementById('editContent').value = row.dataset.content;
                document.getElementById('editAuthor').value = row.dataset.author;
                document.getElementById('editCategory').value = row.dataset.categoryId;
                document.getElementById('editDate').value = row.dataset.postedDate;
                document.getElementById('editModal').style.display = 'flex';
            }
            function closeEditModal() {
                document.getElementById('editModal').style.display = 'none';
            }
            // Sidebar interaction


            document.querySelectorAll('.adnews-sidebar-item').forEach(item => {
                item.addEventListener('click', e => {
                    e.preventDefault();
                    document.querySelectorAll('.adnews-sidebar-item').forEach(i => i.classList.remove('adnews-active'));
                    item.classList.add('adnews-active');
                });
            });

            // Tab navigation

            document.querySelectorAll('.adnews-tab-btn').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('.adnews-tab-btn').forEach(btn => btn.classList.remove('adnews-active'));
                    document.querySelectorAll('.adnews-tab-content').forEach(content => content.classList.remove('adnews-active'));
                    button.classList.add('adnews-active');
                    document.getElementById(button.dataset.tab).classList.add('adnews-active');
                });
            });




            // Tab navigation
            document.querySelectorAll('.adnews-tab-btn').forEach(button => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('.adnews-tab-btn').forEach(btn => btn.classList.remove('adnews-active'));
                    document.querySelectorAll('.adnews-tab-content').forEach(content => content.classList.remove('adnews-active'));
                    button.classList.add('adnews-active');
                    document.getElementById(button.dataset.tab).classList.add('adnews-active');
                });
            });
            // Delete news

            window.adnewsDeleteNews = function (id) {
                if (confirm('Bạn có chắc muốn xóa bài viết này?')) {
                    fetch(`/admin/news/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`tr[data-id="${id}"]`).remove();
                                showNotification('Tin tức đã được xóa.', 'success');
                            }
                        });
                }
            };



            // window.adnewsTogglePublish = function(id, status){
            //     if(status == "Đã xuất bản"){
            //         if(confirm('Bạn có chắc muốn hủy xuất bản không?')){

            //         }
            //     }else{
            //         if(confirm('Bạn có chắc muốn xuất bản không?')){

            //         }
            //     }
            // }

            // window.adnewsTogglePublish = function (id, currentLabel) {
            //     // Xác định hành động và thông báo
            //     const newStatus = currentLabel === 'Chưa xuất bản' ? 'Đã xuất bản' : 'Chưa xuất bản';


            //     const confirmMsg = newStatus === 'Đã xuất bản'
            //         ? 'Bạn có chắc muốn xuất bản không?'
            //         : 'Bạn có chắc muốn hủy xuất bản không?';


            //     if (!confirm(confirmMsg)) return;

            //     // Lấy CSRF token
            //     const token = document.querySelector('meta[name="csrf-token"]').content;

            //     fetch(`/api/news/${id}/status`, {
            //         method: 'PATCH',
            //         headers: {
            //             'Content-Type': 'application/json',
            //             'X-CSRF-TOKEN': token
            //         },
            //         body: JSON.stringify({ status: newStatus })
            //     })
            //         .then(res => res.ok
            //             ? res.json()
            //             : res.json().then(err => Promise.reject(err.message || 'Lỗi server'))


            //         )
            //         .then(data => {
            //             if (!data.success) throw data.message;
            //             // Cập nhật badge và button styles
            //             const badges = document.querySelectorAll(".adnews-status-badge");
            //             const buttons = document.querySelectorAll(".adnews-btn-toggle");

            //             badges.forEach(badge => {
            //                 // badge.className = 'adnews-status-badge';
            //                 badge.style.cssText = newStatus === 'Đã xuất bản'
            //                     ? 'background-color: #DCFCE7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 0.25rem;'
            //                     : 'background-color: #F3F4F6; color: #1F2937; padding: 0.25rem 0.5rem; border-radius: 0.25rem;';
            //                 badge.textContent = newStatus;
            //             });

            //             buttons.forEach(btn => {
            //                 btn.className = 'adnews-btn adnews-btn-toggle';
            //                 btn.style.cssText = newStatus === 'Đã xuất bản'
            //                     ? 'border: 1px solid #EF4444; color: #EF4444;'
            //                     : 'background-color: #2563EB; color: #FFFFFF;';
            //                 btn.textContent = newStatus === 'Đã xuất bản' ? 'Hủy xuất bản' : 'Xuất bản';
            //                 btn.setAttribute('data-tooltip',
            //                     newStatus === 'Đã xuất bản' ? 'Hủy xuất bản tin' : 'Xuất bản tin');
            //                 btn.setAttribute('onclick',
            //                     `adnewsTogglePublish(${id}, '${newStatus}')`);
            //             });



            //             location.reload();

            //         })
            //         .catch(err => {
            //             console.error(err);
            //             alert(err);
            //         });
            // };



            window.adnewsTogglePublish = function (id, currentLabel) {
                // Xác định trạng thái mới
                const newStatus = currentLabel === 'Chưa xuất bản' ? 'Đã xuất bản' : 'Chưa xuất bản';
                // Thông báo xác nhận
                const confirmMsg = newStatus === 'Đã xuất bản'
                    ? 'Bạn có chắc muốn xuất bản không?'
                    : 'Bạn có chắc muốn hủy xuất bản không?';

                if (!confirm(confirmMsg)) return;

                // Lấy CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`/api/news/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                    .then(res => res.ok
                        ? res.json()
                        : res.json().then(err => Promise.reject(err.message || 'Lỗi server'))
                    )
                    .then(data => {
                        if (!data.success) throw data.message;

                        // Cập nhật badge và button styles
                        // Bỏ location.reload() để giữ các cập nhật giao diện
                        // Nếu vẫn muốn reload, có thể thêm vào đây, nhưng không khuyến khích
                        location.reload();
                    })
                    .catch(err => {
                        console.error(err);
                        showNotification(err, 'error');
                    });
            };

            // Add category
            window.adnewsAddCategory = function () {
                const newCategory = document.getElementById('adnews-newCategory').value.trim();
                if (newCategory) {
                    // Demo function - đã được thay thế bằng AJAX thực
                    document.getElementById('adnews-newCategory').value = '';
                } else {
                    showNotification('Vui lòng nhập tên danh mục.', 'error');
                }
            };

            // Delete category
            window.adnewsDeleteCategory = function (category) {
                if (confirm(`Bạn có chắc muốn xóa danh mục "${category}"?`)) {
                    // Demo function - đã được thay thế bằng AJAX thực
                }
            };

            // Cancel settings
            window.adnewsCancelSettings = function () {
                showNotification('Đã hủy thay đổi cài đặt.', 'info');
                document.getElementById('adnews-postsPerPage').value = '10';
                document.querySelectorAll('#adnews-settings input[type="checkbox"]').forEach(cb => cb.checked = cb.defaultChecked);
                document.getElementById('adnews-newCategory').value = '';
                adnewsPostsPerPage = 10;
                adnewsUpdateTable();
            };

            // ==== TÌM KIẾM TIN TỨC ====
            
            // Chức năng tìm kiếm tin tức
            window.searchNews = function() {
                const searchTerm = document.getElementById('adnews-searchInput').value.toLowerCase();
                const rows = document.querySelectorAll('#adnews-newsTableBody tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase(); // Tiêu đề
                    const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Danh mục
                    const author = row.querySelector('td:nth-child(4)').textContent.toLowerCase(); // Tác giả
                    const status = row.querySelector('td:nth-child(8)').textContent.toLowerCase(); // Trạng thái
                    
                    // Tìm kiếm trong tiêu đề, danh mục, tác giả và trạng thái
                    if (searchTerm.trim() === '' || 
                        title.includes(searchTerm) || 
                        category.includes(searchTerm) || 
                        author.includes(searchTerm) ||
                        status.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                        
                        // Highlight từ khóa nếu có tìm kiếm
                        if (searchTerm.trim() !== '') {
                            highlightText(row, searchTerm);
                        } else {
                            removeHighlight(row);
                        }
                    } else {
                        row.style.display = 'none';
                        removeHighlight(row);
                    }
                });
                
                // Hiển thị thông báo nếu có tìm kiếm
                if (searchTerm.trim() !== '') {
                    showSearchResult(visibleCount, searchTerm);
                } else {
                    // Xóa thông báo khi không có tìm kiếm
                    const existingMessage = document.querySelector('.search-result-message');
                    if (existingMessage) {
                        existingMessage.remove();
                    }
                }
            };

            // Highlight từ khóa tìm kiếm
            function highlightText(row, searchTerm) {
                const textColumns = [2, 3, 4, 8]; // Tiêu đề, danh mục, tác giả, trạng thái
                
                textColumns.forEach(columnIndex => {
                    const cell = row.querySelector(`td:nth-child(${columnIndex})`);
                    if (cell) {
                        let originalText = cell.getAttribute('data-original-text');
                        if (!originalText) {
                            originalText = cell.textContent;
                            cell.setAttribute('data-original-text', originalText);
                        }
                        
                        const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
                        const highlightedText = originalText.replace(regex, '<mark style="background-color: #fff3cd; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                        cell.innerHTML = highlightedText;
                    }
                });
            }

            // Xóa highlight
            function removeHighlight(row) {
                const textColumns = [2, 3, 4, 8];
                
                textColumns.forEach(columnIndex => {
                    const cell = row.querySelector(`td:nth-child(${columnIndex})`);
                    if (cell) {
                        const originalText = cell.getAttribute('data-original-text');
                        if (originalText) {
                            cell.textContent = originalText;
                        }
                    }
                });
            }

            // Escape special regex characters
            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            // Hiển thị kết quả tìm kiếm
            function showSearchResult(count, searchTerm) {
                // Xóa thông báo cũ nếu có
                const existingMessage = document.querySelector('.search-result-message');
                if (existingMessage) {
                    existingMessage.remove();
                }

                if (searchTerm.trim() === '') {
                    return; // Không hiển thị gì khi search rỗng
                }

                // Tạo thông báo kết quả
                const messageDiv = document.createElement('div');
                messageDiv.className = 'search-result-message';
                messageDiv.style.cssText = `
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 6px;
                    padding: 12px 16px;
                    margin: 10px 0;
                    color: #495057;
                    font-size: 14px;
                `;

                if (count === 0) {
                    messageDiv.innerHTML = `
                        <i class="fas fa-search" style="color: #6c757d; margin-right: 8px;"></i>
                        Không tìm thấy tin tức nào với từ khóa "<strong>${searchTerm}</strong>"
                    `;
                    messageDiv.style.borderColor = '#ffc107';
                    messageDiv.style.background = '#fff3cd';
                } else {
                    messageDiv.innerHTML = `
                        <i class="fas fa-check-circle" style="color: #28a745; margin-right: 8px;"></i>
                        Tìm thấy <strong>${count}</strong> tin tức với từ khóa "<strong>${searchTerm}</strong>"
                    `;
                    messageDiv.style.borderColor = '#28a745';
                    messageDiv.style.background = '#d4edda';
                }

                // Thêm vào trước bảng
                const tableContainer = document.querySelector('.adnews-data-card');
                tableContainer.insertBefore(messageDiv, tableContainer.firstChild);

                // Tự động xóa sau 3 giây
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 3000);
            }

            // Reset tìm kiếm khi clear input
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('adnews-searchInput');
                
                // Keyboard shortcuts
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        searchNews();
                        this.blur();
                        showNotification('Đã xóa tìm kiếm', 'info');
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const visibleRows = document.querySelectorAll('#adnews-newsTableBody tr:not([style*="display: none"])');
                        if (visibleRows.length === 1) {
                            // Nếu chỉ có 1 kết quả, tự động mở xem chi tiết
                            const newsId = visibleRows[0].getAttribute('data-id');
                            if (newsId) {
                                adnewsPreviewNews(newsId);
                                showNotification('Đã mở tin tức duy nhất tìm thấy', 'success');
                            }
                        }
                    }
                });

                // Global search shortcut (Ctrl + F)
                document.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && e.key === 'f') {
                        e.preventDefault();
                        searchInput.focus();
                        searchInput.select();
                        showNotification('Sử dụng tìm kiếm nhanh', 'info');
                    }
                });

                // Thêm nút clear cho search input
                const searchBar = document.querySelector('.adnews-search-bar');
                const clearBtn = document.createElement('button');
                clearBtn.innerHTML = '<i class="fas fa-times"></i>';
                clearBtn.className = 'search-clear-btn';
                clearBtn.title = 'Xóa tìm kiếm (ESC)';
                clearBtn.style.cssText = `
                    position: absolute;
                    right: 40px;
                    top: 50%;
                    transform: translateY(-50%);
                    background: none;
                    border: none;
                    color: #6c757d;
                    cursor: pointer;
                    padding: 5px;
                    border-radius: 3px;
                    display: none;
                    transition: all 0.3s ease;
                `;
                clearBtn.onclick = function() {
                    searchInput.value = '';
                    searchNews();
                    searchInput.focus();
                    this.style.display = 'none';
                    showNotification('Đã xóa tìm kiếm', 'info');
                };
                
                searchBar.style.position = 'relative';
                searchBar.appendChild(clearBtn);

                // Hiển thị/ẩn nút clear
                searchInput.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        clearBtn.style.display = 'block';
                    } else {
                        clearBtn.style.display = 'none';
                    }
                });

                // Thêm placeholder animation
                let placeholderTexts = [
                    'Tìm kiếm tin tức...',
                    'Tìm theo tiêu đề...',
                    'Tìm theo tác giả...',
                    'Tìm theo danh mục...',
                    'Tìm theo trạng thái...'
                ];
                let currentPlaceholder = 0;
                
                setInterval(() => {
                    if (!searchInput.matches(':focus') && searchInput.value === '') {
                        searchInput.placeholder = placeholderTexts[currentPlaceholder];
                        currentPlaceholder = (currentPlaceholder + 1) % placeholderTexts.length;
                    }
                }, 2000);

                // Thêm tooltip cho search input
                searchInput.title = 'Tìm kiếm tin tức theo tiêu đề, tác giả, danh mục hoặc trạng thái.\nShortcuts: Ctrl+F để focus, ESC để xóa, Enter để mở tin duy nhất';
            });

            // ==== QUẢN LÝ DANH MỤC ====
            
            // Chức năng tìm kiếm danh mục
            window.searchCategories = function() {
                const searchTerm = document.getElementById('categorySearchInput').value.toLowerCase();
                const rows = document.querySelectorAll('#categoriesTableBody tr');
                
                rows.forEach(row => {
                    const categoryName = row.querySelector('.category-name').textContent.toLowerCase();
                    const categoryDescription = row.querySelector('.category-description').textContent.toLowerCase();
                    
                    if (categoryName.includes(searchTerm) || categoryDescription.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            };
            
            // Thêm danh mục mới
            document.getElementById('addCategoryForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const token = document.querySelector('meta[name="csrf-token"]').content;
                
                try {
                    const response = await fetch('/admin/news/categories', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showNotification(result.message, 'success');
                        this.reset(); // Reset form
                        setTimeout(() => location.reload(), 1500); // Reload sau 1.5s để user thấy thông báo
                    } else {
                        showNotification('Có lỗi xảy ra: ' + result.message, 'error');
                    }
                } catch (error) {
                    showNotification('Có lỗi xảy ra khi thêm danh mục', 'error');
                    console.error(error);
                }
            });

            // Mở modal sửa danh mục
            window.editCategory = function(id, name, description) {
                document.getElementById('editCategoryId').value = id;
                document.getElementById('editCategoryName').value = name;
                document.getElementById('editCategoryDescription').value = description || '';
                document.getElementById('editCategoryModal').style.display = 'flex';
            };

            // Đóng modal sửa danh mục
            window.closeEditCategoryModal = function() {
                document.getElementById('editCategoryModal').style.display = 'none';
            };

            // Xử lý sửa danh mục
            document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const categoryId = document.getElementById('editCategoryId').value;
                const formData = new FormData(this);
                const token = document.querySelector('meta[name="csrf-token"]').content;
                
                try {
                    const response = await fetch(`/admin/news/categories/${categoryId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: formData.get('name'),
                            description: formData.get('description')
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showNotification(result.message, 'success');
                        closeEditCategoryModal();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Có lỗi xảy ra: ' + result.message, 'error');
                    }
                } catch (error) {
                    showNotification('Có lỗi xảy ra khi cập nhật danh mục', 'error');
                    console.error(error);
                }
            });

            // Xóa danh mục
            window.deleteCategory = function(id, name, newsCount) {
                if (newsCount > 0) {
                    showNotification(`Không thể xóa danh mục "${name}" vì đang có ${newsCount} bài viết sử dụng danh mục này.`, 'error');
                    return;
                }
                
                if (confirm(`Bạn có chắc muốn xóa danh mục "${name}"?`)) {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    
                    fetch(`/admin/news/categories/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            showNotification(result.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showNotification('Có lỗi xảy ra: ' + result.message, 'error');
                        }
                    })
                    .catch(error => {
                        showNotification('Có lỗi xảy ra khi xóa danh mục', 'error');
                        console.error(error);
                    });
                }
            };


            // Image preview
            // document.getElementById('adnews-newsImage').addEventListener('change', () => {
            //     const file = document.getElementById('adnews-newsImage').files[0];
            //     if (file) {
            //         const reader = new FileReader();
            //         reader.onload = function (e) {
            //             document.getElementById('adnews-imagePreview').src = e.target.result;
            //             document.getElementById('adnews-imagePreview').style.display = 'block';
            //         };
            //         reader.readAsDataURL(file);
            //     } else {
            //         document.getElementById('adnews-imagePreview').style.display = 'none';
            //     }

            //     // });

            //     // Initial render
            //     adnewsUpdateTable();



            // });

        </script>


        <script>




            const basicIdea = document.getElementById('basicIdea');
            const generateBtn = document.getElementById('generateBtn');
            const storyOutput = document.getElementById('storyOutput');
            const notificationArea = document.getElementById('notificationArea');
            const ideaCount = document.getElementById('ideaCount');
            const storyImg = document.getElementById('story_img');
            // Cập nhật số ký tự
            basicIdea.addEventListener('input', function () {
                ideaCount.textContent = `${basicIdea.value.length}/500 ký tự`;
            });
            ideaCount.textContent = `${basicIdea.value.length}/500 ký tự`;

            // Xử lý sự kiện khi nhấn nút "Tạo truyện với AI"
            generateBtn.addEventListener('click', async function () {
                const idea = basicIdea.value.trim();
                const genre = document.getElementById('genre').value;
                const length = document.getElementById('length').value;
                const ageGroup = document.getElementById('ageGroup').value;
                const bannedWords = document.getElementById('bannedWords').value;
                const bannedContent = {
                    violence: document.getElementById('violence').checked,
                    racism: document.getElementById('racism').checked,
                    sex: document.getElementById('sex').checked
                };

                // Kiểm tra đầu vào
                if (!idea) {
                    showNotification('Vui lòng nhập ý tưởng ban đầu cho câu chuyện!', 'error');
                    return;
                }
                if (idea.length > 500) {
                    showNotification('Ý tưởng ban đầu không được vượt quá 500 ký tự!', 'error');
                    return;
                }

                // Hiển thị loading
                storyOutput.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

                try {
                    // Gửi yêu cầu đến backend
                    const response = await fetch('http://127.0.0.1:8000/api/create-blog-ai', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            basic_idea: idea,
                            genre: genre,
                            story_length: length,
                            age_group: ageGroup,
                            banned_words: bannedWords,
                            banned_content: JSON.stringify(bannedContent)
                        })
                    });

                    const data = await response.json();

                    if (data.error) {
                        showNotification(data.error, 'error');
                        storyOutput.innerHTML = '';
                    } else {
                        const story = data.story;
                        const imageUrl = data.image_url; // Lấy URL của ảnh từ backend



                        const titleMatch = story.match(/<TIEUDE>(.*?)<\/TIEUDE>/);
                        const contentMatch = story.match(/<NOIDUNG>(.*?)<\/NOIDUNG>/s);
                        const title = titleMatch ? titleMatch[1] : "Câu chuyện của bạn";
                        const content = contentMatch ? contentMatch[1] : story;


                        const Output = `
                                                                        <div class="story-title">${title}</div>
                                                                         <img src="${imageUrl}" alt="Hình ảnh" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                                                                         >${content}
                                                                    `;

                        // storyImg.innerHTML = imgOutput
                        storyOutput.innerHTML = Output;
                        showNotification('Câu chuyện đã được tạo thành công!', 'success');
                    }
                } catch (error) {
                    showNotification('Đã xảy ra lỗi khi kết nối đến server!', 'error');
                    storyOutput.innerHTML = '';
                }
            });

            // Hàm hiển thị thông báo sử dụng toast system của web
            function showNotification(message, type = 'info') {
                // Xóa toast cũ nếu có
                const existingToasts = document.querySelectorAll('.custom-toast');
                existingToasts.forEach(toast => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                });

                // Tạo toast mới theo template của web
                const toast = document.createElement('div');
                toast.className = 'custom-toast';
                toast.id = `toast-${type}`;
                
                const iconSvg = getIconSvgForType(type);
                const iconClass = getIconClassForType(type);
                
                toast.innerHTML = `
                    <div class="toast-content">
                        <div class="${iconClass}">
                            ${iconSvg}
                        </div>
                        <div class="toast-message">${message}</div>
                        <button class="toast-close" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="toast-progress"></div>
                `;
                
                // Thêm vào body
                document.body.appendChild(toast);
                
                // Auto dismiss sau 5 giây
                const dismissTimeout = setTimeout(() => {
                    dismissToast();
                }, 5000);

                // Click để đóng
                const closeButton = toast.querySelector('.toast-close');
                closeButton.addEventListener('click', () => {
                    clearTimeout(dismissTimeout);
                    dismissToast();
                });

                // Hàm đóng toast
                function dismissToast() {
                    toast.style.animation = 'slideOutRight 0.5s ease forwards';
                    toast.addEventListener('animationend', () => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, { once: true });
                }

                // Pause progress khi hover
                toast.addEventListener('mouseenter', () => {
                    const progress = toast.querySelector('.toast-progress');
                    if (progress) {
                        progress.style.animationPlayState = 'paused';
                    }
                });

                toast.addEventListener('mouseleave', () => {
                    const progress = toast.querySelector('.toast-progress');
                    if (progress) {
                        progress.style.animationPlayState = 'running';
                    }
                });
            }

            // Hàm lấy SVG icon theo loại
            function getIconSvgForType(type) {
                switch(type) {
                    case 'success':
                        return `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>`;
                    case 'error':
                        return `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>`;
                    case 'warning':
                        return `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>`;
                    case 'info':
                    default:
                        return `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>`;
                }
            }

            // Hàm lấy class cho icon
            function getIconClassForType(type) {
                switch(type) {
                    case 'success': return 'toast-icon';
                    case 'error': return 'toast-icon-error';
                    case 'warning': return 'toast-icon-warning';
                    case 'info': 
                    default: return 'toast-icon-info';
                }
            }











            // Preview news
            window.adnewsPreviewNews = function (id) {
                const modal = document.getElementById('adnews-previewModal');
                const previewTitle = document.getElementById('adnews-previewTitle');
                const previewImage = document.getElementById('adnews-previewImage');
                const previewExcerpt = document.getElementById('adnews-previewExcerpt');
                const previewContent = document.getElementById('adnews-previewContent');

                const row = document.querySelector(`tr[data-id="${id}"]`);
                previewTitle.textContent = row.dataset.title;
                previewImage.src = row.dataset.image;
                previewImage.style.display = 'block';
                previewExcerpt.textContent = row.dataset.excerpt;
                // Sử dụng innerHTML để hiển thị nội dung HTML
                previewContent.innerHTML = row.dataset.content;
                modal.style.display = 'flex';
            };



            // });



            // function adnewsUpdateTable() {
            //     const start = (adnewsCurrentPage - 1) * adnewsPostsPerPage;
            //     const end = start + adnewsPostsPerPage;
            //     adnewsRows.forEach((row, index) => {
            //         row.style.display = (index >= start && index < end) ? '' : 'none';
            //     });
            //     adnewsUpdatePagination();
            // }










            // // Save settings
            // window.adnewsSaveSettings = function () {
            //     const saveBtn = document.querySelector('#adnews-settings .adnews-btn-primary');
            //     saveBtn.classList.add('adnews-loading');
            //     saveBtn.disabled = true;
            //     setTimeout(() => {
            //         saveBtn.classList.remove('adnews-loading');
            //         saveBtn.disabled = false;
            //         alert('Cài đặt đã được lưu (demo, không lưu thực tế).');
            //     }, 1000);
            // };



        </script>
@endsection