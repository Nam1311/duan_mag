@extends('app')

@section('body')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tintuc.css') }}">
    <style>
        /* Category Filter Dropdown Styles - Phiên bản căn giữa tiêu đề */
.news-header-container {
    display: flex;
    justify-content: center; /* Căn giữa chính */
    align-items: center;
    margin-bottom: 30px;
    position: relative; /* Tạo context cho absolute của nút lọc */
}

.news-section-title {
    margin: 0;
    font-size: 28px;
    color: #333;
    text-align: center; /* Đảm bảo văn bản căn giữa */
}

.news-category-filter {
    position: absolute;
    right: 0; /* Đặt nút lọc sang bên phải */
    top: 50%;
    transform: translateY(-50%);
    min-width: 200px;
}

.news-filter-button {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px 15px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    transition: all 0.3s ease;
}

.news-filter-button:hover {
    background-color: #e9ecef;
}

.news-filter-button i {
    margin-left: 10px;
    transition: transform 0.3s ease;
}

.news-filter-dropdown {
    position: absolute;
    top: 100%;
    right: 0; /* Căn phải dropdown */
    width: 100%;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 10;
    max-height: 300px;
    overflow-y: auto;
    display: none;
}

.news-filter-dropdown.show {
    display: block;
}

.news-filter-item {
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.news-filter-item:hover {
    background-color: #f8f9fa;
}

.news-filter-item.active {
    background-color: #e9ecef;
    font-weight: bold;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .news-header-container {
        flex-direction: column;
        padding-top: 40px; /* Tạo khoảng trống cho nút lọc */
    }

    .news-category-filter {
        position: static;
        transform: none;
        width: 100%;
        margin-top: 15px;
    }

    .news-filter-dropdown {
        right: auto;
        left: 0;
    }
}
    </style>

    <!-- Stores Section -->
    <section class="news-stores-section">
        <div class="news-container">
            <div class="news-header-container">
                <h2 class="news-section-title">Tất cả bài viết</h2>
                <div class="news-category-filter">
                    <button class="news-filter-button">
                        Lọc danh mục
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="news-filter-dropdown">
                        <div class="news-filter-item active" data-category="all">Tất cả</div>
                        @foreach ($categories as $category)
                            <div class="news-filter-item" data-category="{{ $category->id }}">
                                {{ $category->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="news-articles-grid">
                <!-- Article 1 -->

                @foreach ($news_all as $item)
                    <a style="text-decoration: none" class="news-article-card" href="new_detail/{{ $item->id }}">
                        <span class="news-article-badge"><i class="fa-solid fa-eye"></i> {{ $item->views }}</span>
                        <div class="news-article-image">
                            <img src="{{ asset('img/' . $item->image) }}" alt="Ảnh {{ $item->id }}">
                        </div>
                        <div class="news-article-content">
                            <span class="news-article-category">{{ $item->new_category->name }}</span>
                            <h3 class="news-article-title">{{ $item->title }}</h3>
                            <p class="news-article-excerpt">{{ $item->description }}</p>
                            <div class="news-article-meta">
                                <div class="news-meta-item">
                                    <i class="far fa-user"></i>
                                    <span>{{ $item->author }}</span>
                                </div>
                                <div class="news-meta-item">
                                    <i class="far fa-calendar"></i>
                                    <span>{{ $item->posted_date }}</span>
                                </div>
                                <div class="news-meta-item">
                                    {{-- <i class="fa-solid fa-eye"></i> --}}
                                    {{-- <span>{{ $item->views }}</span> --}}
                                </div>

                            </div>
                        </div>
                    </a>
                @endforeach

            </div>
            {{-- Hiển thị nút phân trang --}}
            <div class="chuyentrang">
                {{ $news_all->links('pagination') }}
            </div>
        </div>
    </section>
@endsection
