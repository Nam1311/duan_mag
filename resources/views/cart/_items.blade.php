<div class="gh-cart-items-container gh-cart2">
    <div class="ghcart3">
        <!-- Debug: Số lượng items: {{ $cartItems->count() }} -->
        @if($cartItems->isEmpty())
            <div class="cart-items">
                <div class="empty-cart-icon">
                    <div class="cart-icon-bg"></div>
                    <i class="fas fa-shopping-cart cart-icon"></i>
                </div>
                <h1 class="cart-title">Giỏ hàng của bạn đang trống</h1>
                <p class="cart-message">
                    Chưa có sản phẩm nào trong giỏ hàng của bạn. Hãy khám phá cửa hàng và thêm những sản phẩm yêu thích vào
                    giỏ hàng để bắt đầu mua sắm!
                </p>
                <a href="{{ route('home') }}">
                    <button class="continue-btn">
                        <i class="fas fa-arrow-right"></i> Tiếp tục mua sắm
                    </button>
                </a>
            </div>
        @else
            <div class="gh-cart-items-header">
                <h2 class="gh-cart-items-title">Sản phẩm</h2>
                <span class="gh-cart-item-count">{{ $cartItems->count() }} sản phẩm</span>
            </div>
            @foreach ($cartItems as $index => $item)
                @php
                    $variant = $item->productVariant;
                    $product = $variant->product;
                    $color = $variant->color;
                    $size = $variant->size;
                    $img = $variant->product->thumbnail ?? null;
                    $stock = $variant->quantity;
                    $availableVariants = \App\Models\product_variants::with(['color', 'size'])
                        ->where('product_id', $product->id)
                        ->where('quantity', '>', 0)
                        ->get();
                    $availableColors = $availableVariants->pluck('color')->unique('id')->filter();
                    $availableSizes = $availableVariants->pluck('size')->unique('id')->filter();
                    
                    // Kiểm tra xem sản phẩm này có cần size không (dựa vào category)
                    $categoryName = strtolower($product->category->name ?? '');
                    $needsSize = !str_contains($categoryName, 'phụ kiện') && 
                                !str_contains($categoryName, 'quần') &&
                                !str_contains($categoryName, 'accessories') &&
                                !str_contains($categoryName, 'pants');
                    
                    // Tạo cart ID nhất quán để giữ vị trí khi update variant
                    $cartItemId = 'cart-item-' . $index . '-' . $product->id;
                @endphp
                <div class="gh-cart-item" data-cart-id="{{ $cartItemId }}" data-index="{{ $index }}">
                    <div class="gh-cart-item-checkbox-wrapper">
                        <input type="checkbox" class="gh-cart-item-checkbox" data-variant-id="{{ $variant->id }}">
                    </div>
                    <a href="/detail/{{ $product->id }}" class="gh-cart-item-image-link">
                        <img src="{{ $img?->path }}" alt="" class="gh-cart-item-image">
                    </a>
                    <div class="gh-cart-item-details">
                        <a href="/detail/{{ $product->id }}" class="gh-cart-item-title-link">
                            <h3 class="gh-cart-item-title">{{ $product->name }}</h3>
                        </a>
                        <div class="gh-cart-item-variant">
                            <form action="{{ route('cart.updateVariant', $variant->id) }}" method="POST"
                                data-variant-id="{{ $variant->id }}" data-cart-id="{{ $cartItemId }}">
                                @csrf
                                @method('PUT')
                                
                                {{-- Luôn hiển thị màu sắc --}}
                                @if($availableColors->count() > 0)
                                <div class="variant-option">
                                    <label>Màu sắc:</label>
                                    <select name="color_id" class="gh-cart-select">
                                        @foreach ($availableColors as $c)
                                            <option value="{{ $c->id }}" {{ $c && $c->id == $variant->color_id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                
                                {{-- Chỉ hiển thị size khi sản phẩm cần size --}}
                                @if($needsSize && $availableSizes->count() > 0)
                                <div class="variant-option">
                                    <label>Kích thước:</label>
                                    <select name="size_id" class="gh-cart-select">
                                        @foreach ($availableSizes as $s)
                                            <option value="{{ $s->id }}" {{ $s && $s->id == $variant->size_id ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                
                                <input type="hidden" name="quantity" value="{{ $item->quantity }}">
                            </form>
                        </div>
                        <button class="gh-cart-remove-item" data-variant-id="{{ $variant->id }}"
                            data-cart-id="{{ $cartItemId }}">
                            <i class="fas fa-trash-alt"></i> Xóa
                        </button>
                    </div>
                    <div class="gh-cart-item-price">
                        {{ number_format($variant->price ?? $product->price ?? 0, 0, ',', '.') }}đ
                    </div>
                    <div class="gh-cart-quantity-control">
                        <button class="gh-cart-quantity-btn minus" data-variant-id="{{ $variant->id }}" {{ $item->quantity <= 1 ? 'disabled' : '' }}>−</button>
                        <input type="number" value="{{ $item->quantity }}" min="1" max="{{ $stock }}"
                            class="gh-cart-quantity-input" data-variant-id="{{ $variant->id }}">
                        <button class="gh-cart-quantity-btn plus" data-variant-id="{{ $variant->id }}" {{ $item->quantity >= $stock ? 'disabled' : '' }}>+</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
