<style>
    .gh-cart2 {
        box-shadow: none !important;
        padding: 0px !important;
        padding-top: 10px !important;
        border-radius: 0 !important;
    }

    .ghcart3 {
        padding: 30px !important;
        box-shadow: none !important;
        height: fit-content;
        position: sticky;
        top: 100px;
    }
</style>
<div class="gh-cart-items-container gh-cart2">
    <div class="ghcart3">


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
                    $img = $variant->product->thumbnail;
                    $stock = $variant->quantity;
                    $availableVariants = \App\Models\product_variants::with(['color', 'size'])
                        ->where('product_id', $product->id)
                        ->where('quantity', '>', 0)
                        ->get();
                    $availableColors = $availableVariants->pluck('color')->unique('id');
                    $availableSizes = $availableVariants->pluck('size')->unique('id');
                    $cartItemId = $item->id ?? $variant->id; // Sử dụng id duy nhất
                @endphp
                <div class="gh-cart-item" data-cart-id="{{ $cartItemId }}" data-index="{{ $index }}">
                    <img src="{{ $img?->path }}" alt="" class="gh-cart-item-image">
                    <div class="gh-cart-item-details">
                        <h3 class="gh-cart-item-title">{{ $product->name }}</h3>
                        <div class="gh-cart-item-variant">
                            <form action="{{ route('cart.updateVariant', $variant->id) }}" method="POST"
                                data-variant-id="{{ $variant->id }}" data-cart-id="{{ $cartItemId }}">
                                @csrf
                                @method('PUT')
                                <select name="color_id" class="gh-cart-select">
                                    @foreach ($availableColors as $c)
                                        <option value="{{ $c->id }}" {{ $c->id == $variant->color_id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="size_id" class="gh-cart-select">
                                    @foreach ($availableSizes as $s)
                                        <option value="{{ $s->id }}" {{ $s->id == $variant->size_id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                            class="gh-cart-quantity-input" readonly>
                        <button class="gh-cart-quantity-btn plus" data-variant-id="{{ $variant->id }}" {{ $item->quantity >= $stock ? 'disabled' : '' }}>+</button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>