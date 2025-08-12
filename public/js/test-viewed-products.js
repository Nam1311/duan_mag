// Test script for viewed products tracking
console.log('Testing Viewed Products Tracking...');

// Test 1: Check if localStorage is available
if (typeof Storage !== 'undefined') {
    console.log('✅ localStorage is available');
} else {
    console.log('❌ localStorage is not available');
}

// Test 2: Check current viewed products
function checkViewedProducts() {
    const viewed = localStorage.getItem('viewedProducts');
    if (viewed) {
        const products = JSON.parse(viewed);
        console.log('📦 Current viewed products:', products.length, products);
        return products;
    } else {
        console.log('📭 No viewed products found');
        return [];
    }
}

// Test 3: Add a test product
function addTestProduct() {
    const testProduct = {
        id: 999,
        name: "Test Product Mobile Responsive",
        price: 299000,
        image: "/img/test-product.jpg",
        viewedAt: new Date().toISOString()
    };
    
    if (typeof ViewedProductsManager !== 'undefined') {
        ViewedProductsManager.addViewedProduct(testProduct);
        console.log('✅ Test product added via ViewedProductsManager');
    } else {
        // Fallback method
        let viewed = checkViewedProducts();
        viewed = viewed.filter(item => item.id !== testProduct.id);
        viewed.unshift(testProduct);
        localStorage.setItem('viewedProducts', JSON.stringify(viewed));
        console.log('✅ Test product added via fallback method');
    }
}

// Test 4: Check if tracking function exists
function checkTrackingFunction() {
    if (typeof trackViewedProduct === 'function') {
        console.log('✅ trackViewedProduct function is available');
        // Test the function
        trackViewedProduct(998, 'Test Tracking Function', 199000, '/img/test-track.jpg');
        console.log('✅ trackViewedProduct function executed');
    } else {
        console.log('❌ trackViewedProduct function is not available');
    }
}

// Run tests
console.log('=== Viewed Products Test Results ===');
checkViewedProducts();
addTestProduct();
checkTrackingFunction();
checkViewedProducts();

// Test mobile responsive detection
if (window.innerWidth <= 768) {
    console.log('📱 Mobile view detected (width:', window.innerWidth + 'px)');
} else {
    console.log('🖥️ Desktop view detected (width:', window.innerWidth + 'px)');
}

// Export for manual testing
window.testViewedProducts = {
    check: checkViewedProducts,
    addTest: addTestProduct,
    checkFunction: checkTrackingFunction,
    clear: () => {
        localStorage.removeItem('viewedProducts');
        console.log('🗑️ Viewed products cleared');
    }
};

console.log('🧪 Test functions available at window.testViewedProducts');
console.log('Usage: testViewedProducts.check(), testViewedProducts.addTest(), testViewedProducts.clear()');
