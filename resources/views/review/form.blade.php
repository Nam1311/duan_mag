@extends('app')

@section('body')
    <style>
        .review-container {
            max-width: 850px;
            margin: 50px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            font-family: 'Segoe UI', sans-serif;
        }

        .review-container h2 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .product-review-item {
            display: flex;
            gap: 24px;
            border-bottom: 1px solid #f0f0f0;
            padding: 24px 0;
            align-items: flex-start;
        }

        .product-image {
            width: 110px;
            height: 110px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #dcdcdc;
        }

        .product-info {
            flex: 1;
        }

        .product-info h4 {
            font-size: 20px;
            margin-bottom: 6px;
            color: #34495e;
        }

        .size {
            font-size: 14px;
            margin-bottom: 12px;
            color: #7f8c8d;
        }

        .rating-stars {
            display: flex;
            flex-direction: row-reverse;
            gap: 5px;
            margin-bottom: 12px;
        }

        .rating-stars input {
            display: none;
        }

        .rating-stars label {
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-stars input:checked~label,
        .rating-stars label:hover,
        .rating-stars label:hover~label {
            color: #f1c40f;
        }

        .comment-box {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #dcdcdc;
            font-size: 14px;
            resize: vertical;
        }

        .submit-btn {
            display: block;
            margin: 30px auto 0;
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
            padding: 14px 32px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: linear-gradient(to right, #2980b9, #1f618d);
        }
    </style>

    <div class="review-container">
        <h2>Đánh giá đơn hàng #{{ $order->id }}</h2>

        <form action="{{ route('review.store', $order->id) }}" method="POST">
            @csrf

            @foreach ($productVariants as $index => $variant)
                <div class="product-review-item">
                    @php
                        $imagePath = $variant->product->thumbnail
                            ? asset('img/products/' . $variant->product->thumbnail->path)
                            : asset('img/no-image.png');
                    @endphp

                    <img src="{{ asset($variant->product->thumbnail->path) }}" class="product-image">

                    <div class="product-info">
                        <h4>{{ $variant->product->name }}</h4>
                        <div class="size">Size: {{ $variant->size->name ?? 'Không xác định' }}</div>

                        <input type="hidden" name="reviews[{{ $index }}][product_id]"
                            value="{{ $variant->product->id }}">

                        <div class="rating-stars">
                            @for ($star = 5; $star >= 1; $star--)
                                <input type="radio" id="star{{ $star }}-{{ $index }}"
                                    name="reviews[{{ $index }}][rating]" value="{{ $star }}" required>
                                <label for="star{{ $star }}-{{ $index }}">★</label>
                            @endfor
                        </div>

                        <textarea name="reviews[{{ $index }}][comment]" class="comment-box" rows="3"
                            placeholder="Nhận xét của bạn..." required></textarea>
                    </div>
                </div>
            @endforeach

            <button type="submit" class="submit-btn">Gửi đánh giá</button>
        </form>
    </div>
@endsection
