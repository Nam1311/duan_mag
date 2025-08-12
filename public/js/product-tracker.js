// Global Product Tracker - Track viewed products across all pages
(function() {
    'use strict';
    
    // Product tracker manager
    window.ProductTracker = {
        storageKey: 'viewedProducts',
        maxItems: 8,
        
        // Track a viewed product
        track: function(productId, productName, productPrice, productImage = null) {
            if (!productId || !productName || !productPrice) {
                console.warn('ProductTracker: Missing required parameters');
                return false;
            }
            
            try {
                const product = {
                    id: parseInt(productId),
                    name: String(productName).trim(),
                    price: parseInt(String(productPrice).replace(/[^\d]/g, '')) || 0,
                    image: productImage || null,
                    viewedAt: new Date().toISOString(),
                    timestamp: Date.now()
                };
                
                let viewed = this.getAll();
                
                // Remove if already exists
                viewed = viewed.filter(item => item.id !== product.id);
                
                // Add to beginning
                viewed.unshift(product);
                
                // Limit items
                if (viewed.length > this.maxItems) {
                    viewed = viewed.slice(0, this.maxItems);
                }
                
                localStorage.setItem(this.storageKey, JSON.stringify(viewed));
                console.log('ProductTracker: Tracked product -', product.name);
                return true;
            } catch (e) {
                console.error('ProductTracker: Error tracking product -', e);
                return false;
            }
        },
        
        // Get all viewed products
        getAll: function() {
            try {
                const stored = localStorage.getItem(this.storageKey);
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                console.error('ProductTracker: Error getting viewed products -', e);
                return [];
            }
        },
        
        // Clear all viewed products
        clear: function() {
            localStorage.removeItem(this.storageKey);
            console.log('ProductTracker: Cleared all viewed products');
        },
        
        // Auto-track current product (for product detail pages)
        autoTrack: function() {
            const selectors = {
                id: [
                    'meta[name="product-id"]',
                    '[data-product-id]',
                    'input[name="product_id"]'
                ],
                name: [
                    'meta[name="product-name"]',
                    '[data-product-name]',
                    '.product-title',
                    '.product-name', 
                    'h1.title',
                    'h1'
                ],
                price: [
                    'meta[name="product-price"]',
                    '[data-product-price]',
                    '.product-price',
                    '.price',
                    '.current-price'
                ],
                image: [
                    'meta[name="product-image"]',
                    '[data-product-image]',
                    '.product-image img',
                    '.main-image img',
                    '.product-gallery img:first-child'
                ]
            };
            
            let productData = {};
            
            // Get product ID
            for (let selector of selectors.id) {
                const el = document.querySelector(selector);
                if (el) {
                    productData.id = el.content || el.dataset.productId || el.value;
                    break;
                }
            }
            
            // Get product name
            for (let selector of selectors.name) {
                const el = document.querySelector(selector);
                if (el) {
                    productData.name = el.content || el.dataset.productName || el.textContent?.trim();
                    break;
                }
            }
            
            // Get product price
            for (let selector of selectors.price) {
                const el = document.querySelector(selector);
                if (el) {
                    productData.price = el.content || el.dataset.productPrice || el.textContent?.replace(/[^\d]/g, '');
                    break;
                }
            }
            
            // Get product image
            for (let selector of selectors.image) {
                const el = document.querySelector(selector);
                if (el) {
                    productData.image = el.content || el.dataset.productImage || el.src;
                    break;
                }
            }
            
            // Track if we have required data
            if (productData.id && productData.name && productData.price) {
                return this.track(productData.id, productData.name, productData.price, productData.image);
            } else {
                console.log('ProductTracker: Could not auto-track - missing required data');
                return false;
            }
        }
    };
    
    // Auto-initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-track on product detail pages
        if (window.location.pathname.includes('/detail/') || 
            window.location.pathname.includes('/product/')) {
            setTimeout(function() {
                window.ProductTracker.autoTrack();
            }, 1000);
        }
    });
    
    // Legacy support
    window.trackViewedProduct = function(id, name, price, image) {
        return window.ProductTracker.track(id, name, price, image);
    };
    
})();
