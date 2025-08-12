{{-- Sản phẩm gợi ý và lịch sử đã xem với Tab --}}
<div class="gh-cart-suggestions-section">
    
    {{-- Tab Navigation --}}
    <div class="gh-tab-navigation">
        <div class="gh-tab-buttons">
            <button class="gh-tab-btn active" onclick="switchTab('suggestions')">
                <i class="fas fa-lightbulb"></i>
                Dành cho bạn
            </button>
            <button class="gh-tab-btn" onclick="switchTab('history')">
                <i class="fas fa-history"></i>
                Đã xem gần đây
            </button>
        </div>
    </div>

    {{-- Tab Content: Gợi ý cho bạn --}}
    <div id="suggestions-tab" class="gh-tab-content active">
        @if(!empty($suggestedProducts) && $suggestedProducts->count() > 0)
            <div class="gh-suggestions-grid">
                @foreach($suggestedProducts->take(8) as $product)
                <div class="gh-suggestion-item" onclick="window.location.href='/detail/{{ $product->id }}'">
                    <div class="gh-suggestion-image">
                        @if($product->thumbnail)
                            <img src="{{ $product->thumbnail->path }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div class="gh-no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                    <div class="gh-suggestion-content">
                        <h4>{{ $product->name }}</h4>
                        <div class="gh-suggestion-actions">
                            <span class="gh-price">{{ number_format($product->price) }}đ</span>
                            <button class="gh-add-btn" onclick="event.stopPropagation(); addToCartFromSuggestion({{ $product->id }})">
                                <i class="fas fa-plus"></i>
                                Thêm
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="gh-suggestions-placeholder">
                <i class="fas fa-lightbulb" style="font-size: 48px; color: var(--gh-text-muted); margin-bottom: 16px;"></i>
                <h3>Chưa có gợi ý</h3>
                <p>Thêm sản phẩm vào giỏ hàng để nhận gợi ý phù hợp</p>
            </div>
        @endif
    </div>

    {{-- Tab Content: Lịch sử đã xem --}}
    <div id="history-tab" class="gh-tab-content">
        <div id="viewed-products-grid" class="gh-viewed-grid">
            <!-- Sẽ được load bằng JavaScript từ localStorage -->
        </div>
        
        <div id="viewed-products-placeholder" class="gh-viewed-placeholder" style="display: none;">
            <i class="fas fa-history" style="font-size: 48px; color: var(--gh-text-muted); margin-bottom: 16px;"></i>
            <h3>Chưa có lịch sử</h3>
            <p>Xem sản phẩm để theo dõi lịch sử của bạn</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; display: none;" id="clear-history-section">
            <button onclick="clearViewHistory()" style="background: var(--gh-danger); color: var(--gh-secondary); border: none; border-radius: var(--gh-radius-sm); padding: 10px 16px; font-size: 13px; cursor: pointer; transition: var(--gh-transition);">
                <i class="fas fa-trash-alt"></i> Xóa lịch sử
            </button>
        </div>
    </div>
</div>

<script>
// Quản lý lịch sử sản phẩm đã xem - Optimized Version
const ViewedProductsManager = {
    storageKey: 'viewedProducts',
    maxItems: 12,

    // Lấy danh sách sản phẩm đã xem
    getViewedProducts() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            return stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Error getting viewed products:', error);
            return [];
        }
    },

    // Thêm sản phẩm vào lịch sử
    addViewedProduct(product) {
        try {
            let viewed = this.getViewedProducts();
            
            // Xóa sản phẩm nếu đã tồn tại
            viewed = viewed.filter(item => item.id !== product.id);
            
            // Thêm vào đầu danh sách
            viewed.unshift({
                ...product,
                viewedAt: new Date().toISOString()
            });
            
            // Giới hạn số lượng
            if (viewed.length > this.maxItems) {
                viewed = viewed.slice(0, this.maxItems);
            }
            
            localStorage.setItem(this.storageKey, JSON.stringify(viewed));
            this.renderViewedProducts();
        } catch (error) {
            console.error('Error adding viewed product:', error);
        }
    },

    // Render danh sách sản phẩm đã xem
    renderViewedProducts() {
        const viewed = this.getViewedProducts();
        const grid = document.getElementById('viewed-products-grid');
        const placeholder = document.getElementById('viewed-products-placeholder');
        const clearSection = document.getElementById('clear-history-section');
        
        if (!grid || !placeholder) return;
        
        if (viewed.length === 0) {
            grid.style.display = 'none';
            placeholder.style.display = 'block';
            if (clearSection) clearSection.style.display = 'none';
            return;
        }
        
        grid.style.display = 'grid';
        placeholder.style.display = 'none';
        if (clearSection) clearSection.style.display = 'block';
        
        grid.innerHTML = viewed.map(product => `
            <div class="gh-viewed-item" onclick="window.location.href='/detail/${product.id}'">
                <div class="gh-viewed-image">
                    ${product.image ? 
                        `<img src="${product.image}" alt="${product.name}" loading="lazy">` : 
                        `<div class="gh-no-image"><i class="fas fa-image"></i></div>`
                    }
                </div>
                <div class="gh-viewed-content">
                    <h4>${product.name}</h4>
                    <div class="gh-viewed-actions">
                        <span class="gh-price">${this.formatPrice(product.price)}</span>
                        <button class="gh-add-btn" onclick="event.stopPropagation(); addToCartFromHistory(${product.id})">
                            <i class="fas fa-plus"></i>
                            Thêm
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    },

    // Format giá tiền
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    },

    // Xóa lịch sử
    clearHistory() {
        try {
            localStorage.removeItem(this.storageKey);
            this.renderViewedProducts();
        } catch (error) {
            console.error('Error clearing history:', error);
        }
    }
};

// Tab switching functionality - Enhanced
function switchTab(tab) {
    // Remove active from all tabs and buttons
    document.querySelectorAll('.gh-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.gh-tab-content').forEach(content => content.classList.remove('active'));
    
    // Add active to selected
    if (tab === 'suggestions') {
        document.querySelector('.gh-tab-btn:first-child').classList.add('active');
        const suggestionsTab = document.getElementById('suggestions-tab');
        if (suggestionsTab) suggestionsTab.classList.add('active');
    } else if (tab === 'history') {
        document.querySelector('.gh-tab-btn:last-child').classList.add('active');
        const historyTab = document.getElementById('history-tab');
        if (historyTab) historyTab.classList.add('active');
        // Load viewed products when switching to history tab
        setTimeout(() => ViewedProductsManager.renderViewedProducts(), 50);
    }
}

// Enhanced toast function
function showToast(type, title, message) {
    const toast = Toastify({
        text: `
            <div class="toastify-content">
                <div class="toast-icon ${type}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                </div>
                <div class="toast-text">
                    <span class="toast-title">${title}</span>
                    <span class="toast-message">${message}</span>
                </div>
                <button class="toast-close" type="button">&times;</button>
            </div>
        `,
        duration: 3000,
        close: false,
        gravity: "top",
        position: "right",
        className: `custom-toast ${type}`,
        escapeMarkup: false
    });
    toast.showToast();
    
    // Add close functionality
    setTimeout(() => {
        const toastElement = document.querySelector('.custom-toast');
        const closeBtn = toastElement?.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                toastElement.style.opacity = '0';
                toastElement.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toastElement.parentNode) {
                        toastElement.parentNode.removeChild(toastElement);
                    }
                }, 300);
            });
        }
    }, 100);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    ViewedProductsManager.renderViewedProducts();
    
    // Initialize tab state
    const activeTab = document.querySelector('.gh-tab-btn.active');
    if (activeTab && activeTab.textContent.includes('For You')) {
        switchTab('suggestions');
    }
});

// Function thêm sản phẩm vào giỏ hàng từ gợi ý
function addToCartFromSuggestion(productId) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    $.ajax({
        url: '/cart/add',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: 1,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showToast('success', 'Thành công!', response.message || 'Đã thêm sản phẩm vào giỏ hàng');
            updateCartCount();
            reloadCartContent();
        },
        error: function(xhr) {
            let message = "Có lỗi xảy ra";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', 'Lỗi!', message);
        },
        complete: function() {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

// Function thêm sản phẩm vào giỏ hàng từ lịch sử
function addToCartFromHistory(productId) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    $.ajax({
        url: '/cart/add',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: 1,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showToast('success', 'Thành công!', response.message || 'Đã thêm sản phẩm vào giỏ hàng');
            updateCartCount();
            reloadCartContent();
        },
        error: function(xhr) {
            let message = "Có lỗi xảy ra";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', 'Lỗi!', message);
        },
        complete: function() {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

// Function để reload cart content mà không reload toàn trang
function reloadCartContent() {
    $.ajax({
        url: '/cart',
        method: 'GET',
        success: function(response) {
            const tempDiv = $('<div>').html(response);
            const newCartItems = tempDiv.find('.gh-cart-items-section').html();
            const newCartSummary = tempDiv.find('.gh-cart-summary').html();
            
            if (newCartItems) {
                $('.gh-cart-items-section').html(newCartItems);
            }
            if (newCartSummary) {
                $('.gh-cart-summary').html(newCartSummary);
            }
        },
        error: function(xhr) {
            console.log('Failed to reload cart content:', xhr);
            // Fallback to page reload if AJAX fails
            setTimeout(() => window.location.reload(), 1000);
        }
    });
}

// Function xóa lịch sử xem - Enhanced
function clearViewHistory() {
    if (confirm('Bạn có chắc muốn xóa toàn bộ lịch sử xem?')) {
        ViewedProductsManager.clearHistory();
        showToast('success', 'Thành công!', 'Đã xóa lịch sử xem');
    }
}

// Function để track sản phẩm đã xem - Enhanced
function trackViewedProduct(productId, productName, productPrice, productImage) {
    const product = {
        id: parseInt(productId),
        name: productName,
        price: parseInt(productPrice),
        image: productImage
    };
    
    ViewedProductsManager.addViewedProduct(product);
}

// Export functions to global scope
window.trackViewedProduct = trackViewedProduct;
window.ViewedProductsManager = ViewedProductsManager;
window.switchTab = switchTab;
window.showToast = showToast;
</script>
