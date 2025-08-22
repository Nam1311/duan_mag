@extends('app')

@section('body')
    <link rel="stylesheet" href="{{ asset('css/newdetail.css') }}">
  <div class="newdetail-body">
    <main class="newdetail-container">
        <article>
            <div class="newdetail-article-header">
                <h1 class="newdetail-article-title">{{ $new_detail->title }}</h1>
                <div class="newdetail-article-meta">Đăng bởi: {{ $new_detail->author }} | Ngày: {{ $new_detail->posted_date->format('d/m/Y') }}</div>
                {{-- <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Thời trang 2023" class="newdetail-article-image"> --}}
            </div>

            <div class="newdetail-article-content">
                {!! $new_detail->content !!}
                {{-- <div class="newdetail-article-section">
                    <p class="newdetail-paragraph">Năm 2023 mang đến những xu hướng thời trang đầy sáng tạo, kết hợp giữa nét cổ điển và hiện đại. Từ những bộ suit thanh lịch đến phong cách streetwear cá tính, thời trang năm nay tập trung vào sự thoải mái nhưng không kém phần thời thượng.</p>
                </div>

                <div class="newdetail-article-section">
                    <h2 class="newdetail-section-title">1. Xu Hướng Màu Sắc Nổi Bật</h2>
                    <p class="newdetail-paragraph">Màu sắc luôn là yếu tố quan trọng tạo nên phong cách thời trang. Năm 2023 chứng kiến sự trở lại của những tone màu tươi sáng và nổi bật:</p>

                    <div class="newdetail-image-grid">
                        <img src="https://images.unsplash.com/photo-1539109136881-3be0616acf4b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80" alt="Màu đỏ rực rỡ" class="newdetail-grid-image">
                        <img src="https://images.unsplash.com/photo-1551232864-3f0890e580d9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80" alt="Màu xanh ngọc" class="newdetail-grid-image">
                    </div>

                    <ul class="newdetail-trend-list">
                        <li><strong>Đỏ rực rỡ:</strong> Màu đỏ tươi trở thành tâm điểm với sức hút mạnh mẽ, phù hợp cho cả trang phục công sở và dạo phố.</li>
                        <li><strong>Xanh ngọc bích:</strong> Tone màu mát mắt này mang lại cảm giác tươi mới, thường xuất hiện trong các thiết kế váy liền và áo khoác.</li>
                        <li><strong>Tím lavender:</strong> Sự kết hợp hoàn hảo giữa nữ tính và hiện đại, đặc biệt phổ biến trong các thiết kế mùa xuân-hè.</li>
                        <li><strong>Vàng mù tạt:</strong> Màu sắc ấm áp này tạo điểm nhấn độc đáo cho tổng thể trang phục.</li>
                    </ul>
                </div>

                <div class="newdetail-article-section">
                    <h2 class="newdetail-section-title">2. Phong Cách Thể Thao Nâng Cấp</h2>
                    <p class="newdetail-paragraph">Streetwear tiếp tục thống trị làng thời trang 2023 với phiên bản nâng cấp tinh tế hơn. Các thương hiệu lớn đã biến trang phục thể thao thành những item cao cấp phù hợp với nhiều dịp.</p>

                    <div class="newdetail-highlight">
                        <p>"Sự thoải mái là chìa khóa trong thời trang 2023. Người tiêu dùng ngày càng ưa chuộng những thiết kế vừa đẹp mắt vừa dễ vận động, phù hợp với lối sống năng động hiện đại." - Nhà thiết kế Trần Minh Anh</p>
                    </div>

                    <p class="newdetail-paragraph">Một số item không thể thiếu trong tủ đồ streetwear 2023:</p>

                    <ul class="newdetail-trend-list">
                        <li>Áo hoodie oversize với chất liệu cao cấp</li>
                        <li>Quần jogger thiết kế tinh tế, có thể mix với áo blazer</li>
                        <li>Giày thể thao limited edition kết hợp công nghệ</li>
                        <li>Áo khoác bomber phiên bản nâng cấp</li>
                    </ul>
                </div>

                <div class="newdetail-article-section">
                    <h2 class="newdetail-section-title">3. Thời Trang Bền Vững</h2>
                    <p class="newdetail-paragraph">Xu hướng thời trang bền vững ngày càng được quan tâm trong năm 2023. Nhiều thương hiệu lớn đã chuyển hướng sang sử dụng chất liệu thân thiện với môi trường:</p>

                    <div class="newdetail-image-grid">
                        <img src="https://images.unsplash.com/photo-1604176354204-9268737828e4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=880&q=80" alt="Vải tái chế" class="newdetail-grid-image">
                        <img src="https://images.unsplash.com/photo-1605000797499-95a51c5269ae?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80" alt="Thời trang tái chế" class="newdetail-grid-image">
                    </div>

                    <p class="newdetail-paragraph">Các chất liệu phổ biến bao gồm vải tái chế từ chai nhựa, sợi tre, bông hữu cơ và da thuần chay. Không chỉ thân thiện với môi trường, những chất liệu này còn mang lại cảm giác mặc thoải mái và bền đẹp theo thời gian.</p>
                </div>

                <div class="newdetail-article-section">
                    <h2 class="newdetail-section-title">4. Phụ Kiện Điểm Nhấn</h2>
                    <p class="newdetail-paragraph">Phụ kiện năm 2023 được chú trọng như một phần không thể thiếu để hoàn thiện phong cách:</p>

                    <ul class="newdetail-trend-list">
                        <li><strong>Túi mini:</strong> Những chiếc túi nhỏ xinh nhưng đựng vừa các vật dụng cần thiết</li>
                        <li><strong>Vòng cổ layer:</strong> Phong cách đeo nhiều vòng cổ cùng lúc tạo điểm nhấn</li>
                        <li><strong>Mũ bucket:</strong> Trở lại mạnh mẽ với nhiều chất liệu và màu sắc</li>
                        <li><strong>Kính mắt oversize:</strong> Thiết kế lớn, màu sắc nổi bật</li>
                    </ul>

                    <p class="newdetail-paragraph">Kết hợp phụ kiện đúng cách sẽ giúp nâng tầm bộ trang phục của bạn, biến outfit đơn giản thành phong cách ấn tượng.</p>
                </div>

                <div class="newdetail-article-section">
                    <h2 class="newdetail-section-title">Kết Luận</h2>
                    <p class="newdetail-paragraph">Thời trang 2023 mang đến nhiều sự lựa chọn đa dạng cho mọi phong cách. Dù bạn yêu thích vẻ ngoài thanh lịch, cá tính hay năng động, hãy lựa chọn những items phù hợp với cá tính và lối sống của bản thân. Đừng quên kết hợp các xu hướng mới với phong cách riêng để tạo nên dấu ấn cá nhân!</p>
                </div> --}}
            </div>
        </article>
    </main>
            </div>
@endsection
