@extends('app')

@section('body')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tintuc.css') }}">
    <style>
        /* CSS chung cho header */
        .news-header-container {
            display: flex;
            justify-content: center;
            /* Căn giữa chính */
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            /* Để dropdown có thể absolute theo */
        }

        /* Tiêu đề ở giữa */
        .news-section-title {
            text-align: center;
            flex-grow: 1;
            /* Chiếm không gian còn lại */
        }

        /* Dropdown ở bên phải */
        .news-filter-dropdown {
            position: absolute;
            right: 0;
            /* Đẩy sang hết bên phải */
        }

        /* CSS cho dropdown */
        .filter-dropdown-btn {
            background-color: #f8f8f8;
            color: #333;
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-dropdown-btn:hover {
            background-color: #e7e7e7;
        }

        .dropdown-arrow {
            font-size: 10px;
            margin-left: 5px;
        }

        .filter-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: 4px;
            overflow: hidden;
        }

        .filter-dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .filter-dropdown-content a:hover {
            background-color: #e7e7e7;
        }

        .news-filter-dropdown:hover .filter-dropdown-content {
            display: block;
            z-index: 3;
        }
    </style>
    <!-- Stores Section -->
    <section class="news-stores-section">
        <div class="news-container">
            <div class="news-header-container">
                <h2 class="news-section-title">Tất cả bài viết</h2>
                <div class="news-filter-dropdown">
                    <button class="filter-dropdown-btn">Lọc bài viết <span class="dropdown-arrow">▼</span></button>
                    <div class="filter-dropdown-content">
                        @foreach ($news_category as $cate)
                            <a href="{{ route('news.all', $cate->id) }}">{{ $cate->name }}</a>
                            {{-- <a href="#">Xem nhiều nhất</a>
                            <a href="#">Theo chủ đề</a>
                            <a href="#">Theo tác giả</a> --}}
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="news-articles-grid">
                <!-- Article 1 -->

                @foreach ($news as $item)
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
                                    <span>{{ $item->posted_date->format('d/m/Y') }}</span>
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
                {{ $news->links('pagination') }}
            </div>
        </div>
    </section>
    <script>
        document.querySelector('.filter-dropdown-btn').addEventListener('click', function() {
            document.querySelector('.filter-dropdown-content').classList.toggle('show');
        });

        // Đóng dropdown khi click bên ngoài
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.filter-dropdown-btn')) {
                var dropdowns = document.querySelectorAll('.filter-dropdown-content');
                dropdowns.forEach(function(dropdown) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>
@endsection
