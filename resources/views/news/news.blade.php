@extends('app')

@section('body')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tintuc.css') }}">
    <section id="featured" class="news-featured-section">
        <div class="news-container">
            <h2 class="news-section-title">Bài viết nổi bật</h2>
            <div class="news-featured-grid">
                <!-- Article 1 -->
                @foreach ($newListView as $newListView)
                    <a style="text-decoration: none" class="news-article-card" href="new_detail/{{ $newListView->id }}">
                        <span class="news-article-badge"><i class="fa-solid fa-eye"></i> {{ $newListView->views }}</span>
                        <div class="news-article-image">
                            <img src="{{ asset('img/' . $newListView->image) }}" alt="Ảnh {{ $newListView->id }}">
                        </div>
                        <div class="news-article-content">
                            <span class="news-article-category">{{ $newListView->new_category->name }}</span>
                            <h3 class="news-article-title">{{ $newListView->title }}</h3>
                            <p class="news-article-excerpt">{{ $newListView->description }}</p>
                            <div class="news-article-meta">
                                <div class="news-meta-item">
                                    <i class="far fa-user"></i>
                                    <span>{{ $newListView->author }}</span>
                                </div>
                                <div class="news-meta-item">
                                    <i class="far fa-calendar"></i>
                                    <span>{{ $newListView->posted_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="news-meta-item">
                                    {{-- <i class="fa-solid fa-eye"></i> --}}
                                    {{-- <span>{{ $newListView->views }}</span> --}}
                                </div>

                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </section>



    <!-- Stores Section -->
    <section class="news-stores-section">
        <div class="news-container">
            <h2 class="news-section-title">Tất cả bài viết</h2>

            <div class="news-articles-grid">
                <!-- Article 1 -->

                @foreach ($newList as $item)
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

                {{-- Hiển thị nút phân trang --}}
                {{-- <div class="chuyentrang">
                    {{ $newList->links('pagination') }}
                </div> --}}

            </div>
            <style>
                .new-more {
                    text-align: center;
                    padding: 30px 0px
                }
                .new-more a {
                    color: #000;
                    text-decoration: none;
                    font-size: 1.3rem;
                    font-weight: 600;
                    display: inline-block;
                    transition: var(--news-transition);
                    position: relative;
                }
                .new-more a:hover {
                    transform: translateY(-3px);
                }
            </style>
            <div class="new-more">
                <a href="/news_all">
                    <span>Xem tất cả bài viết >></span>
                </a>
            </div>
        </div>
    </section>


<!-- Trending Section -->

    <section id="trending" class="news-trending-section">
        <div class="news-container">
            <h2 class="news-section-title">Đang thịnh hành</h2>
            @if ($highlightNews)
                <div class="news-trending-container">
                    <div class="news-trending-main">
                        <div class="news-trending-image">
                            <img src="{{ asset('img/' . $highlightNews->image) }}" alt="Fashion week">
                        </div>
                        <div class="news-trending-content">
                            <span class="news-trending-category">{{ $highlightNews->new_category->name }}</span>
                            <h3 class="news-trending-title">{{ $highlightNews->title }}
                            </h3>
                            <p class="news-trending-excerpt">{{ $highlightNews->description }}</p>
                            <a href="new_detail/{{ $highlightNews->id }}" class="news-btn news-btn-primary">Xem chi
                                tiết</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>


    <!-- More Articles Section -->
    <section class="news-featured-section">
        <div class="news-container">
            <h2 class="news-section-title">Bài viết mới nhất</h2>

            <div class="news-articles-grid">
                <!-- Article 1 -->



                @foreach ($newestNew as $newestNew)
                    <a style="text-decoration: none" class="news-article-card" href="new_detail/{{ $newestNew->id }}">
                        <span class="news-article-badge"><i class="fa-solid fa-eye"></i> {{ $newestNew->views }}</span>
                        <div class="news-article-image">
                            <img src="{{ asset('img/' . $newestNew->image) }}" alt="Ảnh {{ $newestNew->id }}">
                        </div>
                        <div class="news-article-content">
                            <span class="news-article-category">{{ $newestNew->new_category->name }}</span>
                            <h3 class="news-article-title">{{ $newestNew->title }}</h3>
                            <p class="news-article-excerpt">{{ $newestNew->description }}</p>
                            <div class="news-article-meta">
                                <div class="news-meta-item">
                                    <i class="far fa-user"></i>
                                    <span>{{ $newestNew->author }}</span>
                                </div>
                                <div class="news-meta-item">
                                    <i class="far fa-calendar"></i>
                                    <span>{{ $newestNew->posted_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="news-meta-item">
                                    {{-- <i class="fa-solid fa-eye"></i> --}}
                                    {{-- <span>{{ $newestNew->views }}</span> --}}
                                </div>

                            </div>
                        </div>
                    </a>
                @endforeach



            </div>
        </div>
    </section>


@endsection
