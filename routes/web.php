<?php

use App\Http\Controllers\Admin\HomeAdminController;

use App\Http\Controllers\Admin\NewAdminController;


use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PaymentController;
use App\Models\Cart;

use App\Http\Controllers\NewController;
use App\Http\Controllers\UserInfoController;
use App\Http\Controllers\UserOrderController;

use App\Http\Controllers\TryOnController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\CountDownController;
use App\Http\Controllers\Admin\ProductAdminController;

//admin
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ImageAdminController;
use App\Http\Controllers\Admin\ContactAdminController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\admin\VoucherAdminController;
use App\Http\Controllers\Admin\BannerAdminController;
use League\Uri\Contracts\UserInfoInterface;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminBaocaoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminSettingController;


Route::get('about', function () {
    return view('about');
});

Route::get('contact', function () {
    return view('contact');
});
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('cart', function () {
    return view('cart');
});

// Route::get('login', function () {
//     return view('login');
// });

// login bằng web
Route::get('/showlogin', [LoginController::class, 'showLogin'])->name('showlogin');
Route::post('/login', [LoginController::class, 'login'])->name('login');
// login gg
Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google')->middleware('guest');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->middleware('guest');
// login face
Route::get('/auth/facebook', [SocialLoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);
// đăng ký
Route::post('/register', [LoginController::class, 'register'])->name('register');
// quên mật khẩu
Route::get('/password/reset/{id}/{hash}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [LoginController::class, 'ForgotPassword'])->name('password.update');
Route::post('/password/update', [LoginController::class, 'updatePassword'])->name('password.forgot');
// check mail
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed');


// kiểm trạng thái đăng nhập
Route::middleware('auth')->group(function () {

    // mạnh trang info {
    Route::get('infouser', [UserInFoController::class, 'ShowInFo'])->middleware('auth')->name('infouser');
    Route::post('suainfo/{id}', [UserInFoController::class, 'suainfo'])->middleware('auth');
    Route::post('themaddress', [UserInFoController::class, 'themaddress'])->middleware('auth');
    Route::post('suaaddress', [UserInFoController::class, 'suaaddress'])->middleware('auth');
    Route::post('mkinfo/{id}', [UserInFoController::class, 'mkinfo'])->middleware('auth');
    Route::get('xoaaddress/{id}', [UserInFoController::class, 'xoaaddress'])->middleware('auth');
    Route::get('huydon/{id}', [UserInFoController::class, 'huydon'])->middleware('auth');
    // chi tiết đơn hàng
    Route::get('info-ctdh/{id}', [UserInFoController::class, 'Showorder'])->middleware('auth')->name('info-ctdh');
    // }
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');


    // Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');

    // Route::get('infouser', [UserInFoController::class, 'ShowInFo'])->middleware('auth')->name('infouser');
    // Route::post('suainfo/{id}', [UserInFoController::class, 'suainfo'])->middleware('auth');
    // Route::post('themaddress/{id}', [UserInFoController::class, 'themaddress'])->middleware('auth');
    // Route::post('mkinfo/{id}', [UserInFoController::class, 'mkinfo'])->middleware('auth');
    // Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // Route::get('/user/orders/{order}', [UserOrderController::class, 'show'])->name('user.order.details')->middleware('auth');
});

Route::post('/reviews/reply', [PageController::class, 'reply'])->name('reviews.reply');



Route::get('products', function () {
    return view('product');
});
Route::get('pagereturn', function () {
    return view('page_return');
});

Route::get('payment', function () {
    return view('payment');
});

Route::get('info-ctdh', function () {
    return view('info_ctdh');
});

Route::get('favourite_product', function () {
    return view('favourite_product');
});


// load san pham
Route::get('/products', [ProductController::class, 'ProductAll'])->name('product.filter');

// sx nổi bậtbật
Route::get('productFeatured', [ProductController::class, 'ProductFeatured']);
// sx bán chạy
Route::get('productBestseller', [ProductController::class, 'ProductBestseller']);
// sx gias cao -> thấp
Route::get('productPriceLowToHight', [ProductController::class, 'ProductPriceLowToHight']);
Route::get('productPriceHightToLow', [ProductController::class, 'ProductPriceHightToLow']);
//tìm kiếm
Route::get('/search-suggestions', [ProductController::class, 'searchSuggestions']);
Route::get('/search', [ProductController::class, 'search'])->name('search');


// page -> home
Route::get('/', [PageController::class, 'home'])->name('home');
// detail product
Route::get('/detail/{id}', [PageController::class, 'detail']);
// Route::get('/detail/{id}', [ProductController::class, 'show']);
// detail-color-sizesize

Route::get('/get-variant-quantity', [PageController::class, 'getVariantQuantity'])->name('getVariantQuantity');

// cart
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
// mua ở trang chủ
Route::post('/cart/add/home', [CartController::class, 'BuyInHome']);


Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
// lưu session
Route::post('/cart/session-add', [CartController::class, 'storeSessionCart']);
// voucher
Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.applyVoucher');
// xóa và tăng số lượng
Route::delete('/cart/remove/{variantId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::delete('/cart/remove-multiple', [CartController::class, 'removeMultiple'])->name('cart.removeMultiple');
Route::put('/cart/update/{variantId}', [CartController::class, 'updateQuantity'])->name('cart.update');
// Đếm số lượng sản phẩm trong giỏ hàng
Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');
// update variant
Route::put('/cart/update-variant/{variantId}', [CartController::class, 'updateVariant'])->name('cart.updateVariant');
// thanh toán
Route::post('/checkout-direct', [CartController::class, 'checkoutDirect'])->name('checkout.direct');
Route::get('/payment', [CartController::class, 'proceedToCheckout'])->name('payment.add');
Route::get('/showpayment', [PaymentController::class, 'showPayment'])->name('payment.show');
Route::post('/paymentstore', [PaymentController::class, 'paymentStore'])->name('payment.store');
Route::get('/payment/result', [PaymentController::class, 'result'])->name('payment.result');
Route::get('/order/{order_code}', [OrderController::class, 'showPublic'])->name('orders.public.show');
// momo payment
// Route::get('/payment/momo/return', [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
// Route::post('/payment/momo/ipn', [PaymentController::class, 'momoIPN'])->name('payment.momo.ipn');


Route::get('news', [NewController::class, 'show_new']);
Route::get('new_detail/{id}', [NewController::class, 'new_detail']);
Route::get('news_all', [NewController::class, 'news_all']);

Route::get('/wishlist/remove/{productId}', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/wishlist/add/{id}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::get('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');


// ai mặc thử sản phẩm
Route::get('/try-on', [TryOnController::class, 'showForm'])->name('tryon.form');
Route::post('/try-on', [TryOnController::class, 'process'])->name('tryon.process');

// ai box chat
// Route::get('/a', function (Request $request) {
//     return view('a', ['request' => $request]);
// });

// ========================================== admin


Route::get('/admin/', [HomeAdminController::class, 'show_home']);
Route::post('/admin/reply-comment', [HomeAdminController::class, 'replyComment'])->name('admin.reply-comment');

// Route::get('/admin/', function () {
//     return view('admin.home');
// });
Route::get('/admin/baocao', function () {
    return view('admin.baocao');
});
Route::get('/admin/caidat', function () {
    return view('admin.caidat');
});
Route::get('/admin/hotro', function () {
    return view('admin.hotro');
});
Route::get('/admin/khuyenmai', function () {
    return view('admin.khuyenmai');
});
// Route::get('/admin/countdown', function () {
//     return view('admin.countdown');
// });
Route::get('/admin/orders', function () {
    return view('admin.orders');
});
Route::get('/admin/products', function () {
    return view('admin.products');
});
Route::get('/admin/quanlyhinhanh', function () {
    return view('admin.quanlyhinhanh');
});
Route::get('/admin/quanlykhachhang', function () {
    return view('admin.quanlykhachhang');
});
Route::get('/admin/quanlykho', function () {
    return view('admin.quanlykho');
});
Route::get('/admin/quanlynguoidung', function () {
    return view('admin.quanlynguoidung');
});
Route::get('/admin/quanlytintuc', function () {
    return view('admin.quanlytintuc');
});



Route::get('/admin/news', [NewAdminController::class, 'index'])->name('admin.new.index');
Route::post('/api/upload-image', [NewAdminController::class, 'ImageUpload'])->name('upload.image');
Route::post('/admin/news/add', [NewAdminController::class, 'store'])->name('admin.new.add');
Route::get('/admin/news/edit/{id}', [NewAdminController::class, 'edit'])->name('admin.new.edit');
Route::put('/admin/news/update/{id}', [NewAdminController::class, 'update'])->name('admin.new.update');
Route::delete('/admin/news/delete/{id}', [NewAdminController::class, 'destroy'])->name('admin.new.delete');
Route::patch('/api/news/{id}/status', [NewAdminController::class, 'updateStatus']);

//admin diep

Route::get('/payment/momo', function () {
    return view('payment.momo');
});

Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
Route::get('/admin/orders/{id}/edit', [AdminOrderController::class, 'edit'])->name('admin.orders.edit');
Route::put('/admin/orders/{id}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
Route::delete('/admin/orders/{id}', [AdminOrderController::class, 'softDelete'])->name('admin.orders.softDelete');
Route::get('/admin/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');



Route::post('/admin/countdown', [PromotionController::class, 'store'])->name('admin.countdown.store');
// Route::get('/admin/countdown', [PromotionController::class, 'index']);
Route::get('/admin/countdown', [PromotionController::class, 'index'])->name('admin.countdown.index');
// Route::post('/admin/countdown/create', [PromotionController::class, 'store']);
Route::put('/admin/countdown/{promotion}', [PromotionController::class, 'update'])->name('admin.countdown.update');
Route::delete('/admin/countdown/{promotion}', [PromotionController::class, 'destroy'])->name('admin.countdown.destroy');

// kiểm tra reload khi đến giờ và khi kết thúc
// Route::get('/apply-countdown', [CountDownController::class, 'applyCountdown'])->name('ajax.applyCountdown');
// Route::get('/check-reset-countdown', [CountDownController::class, 'resetCountdownSale'])->name('ajax.resetCountdown');

Route::get('/kiem_tra_flashsale', [CountDownController::class, 'kiem_tra_flashsale'])->name('ajax.kiem_tra_flashsale');




// router trung
Route::get('/admin/quanlyhinhanh', [ImageAdminController::class, 'index'])->name('admin.images.index');
Route::post('/admin/images', [ImageAdminController::class, 'store'])->name('admin.images.store');
Route::delete('/admin/images/destroy/{id}', [ImageAdminController::class, 'destroy'])->name('admin.images.destroy');
Route::put('/admin/images/{id}', [ImageAdminController::class, 'update'])->name('admin.images.update');
Route::get('/admin/khuyenmai', [VoucherAdminController::class, 'index'])->name('admin.vouchers.index');
Route::post('/admin/vouchers', [VoucherAdminController::class, 'store'])->name('admin.vouchers.store');
Route::delete('/admin/vouchers/{id}', [VoucherAdminController::class, 'destroy'])->name('admin.vouchers.destroy');
Route::put('/admin/vouchers/{id}', [VoucherAdminController::class, 'update'])->name('vouchers.update');


Route::get('/admin/khuyenmai', [VoucherAdminController::class, 'index'])->name('admin.vouchers.index');
Route::post('/admin/vouchers', [VoucherAdminController::class, 'store'])->name('admin.vouchers.store');
Route::delete('/admin/vouchers/{id}', [VoucherAdminController::class, 'destroy'])->name('admin.vouchers.destroy');
Route::put('/admin/vouchers/{id}', [VoucherAdminController::class, 'update'])->name('vouchers.update');


Route::get('/admin/danhmuc', [CategoryAdminController::class, 'index'])->name('admin.categories.index');
Route::post('/admin/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
Route::delete('/admin/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');
Route::put('/admin/categories/{id}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');




Route::get('/admin/danhmuc', [CategoryAdminController::class, 'index'])->name('admin.categories.index');
Route::post('/admin/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
Route::delete('/admin/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');
Route::put('/admin/categories/{id}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');


// product admin
Route::get('/admin/products', [ProductAdminController::class, 'index'])->name('admin.products.index');
Route::get('/admin/products/{id}', [ProductAdminController::class, 'viewDetail']);
Route::post('/admin/products/store', [ProductAdminController::class, 'store'])->name('admin.products.store');
Route::delete('/admin/products/{id}', [ProductAdminController::class, 'destroy'])->name('admin.products.destroy');
// Route hiển thị popup cập nhật sản phẩm (trả về HTML)
Route::get('/admin/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('admin.products.edit');

// Route xử lý submit form cập nhật
Route::put('/admin/products/{id}', [ProductAdminController::class, 'update'])->name('admin.products.update');
Route::delete('/admin/variants/{id}', [ProductAdminController::class, 'deletevariant']);

// lọc
Route::get('/products/category/{id}', [ProductAdminController::class, 'LocDanhMuc'])->name('products.TheoDanhMuc');
Route::get('/products/status/{status}', [ProductAdminController::class, 'LocTrangThai'])->name('products.TheoTrangThai');

// tìm
Route::get('/products/search', [ProductAdminController::class, 'search'])->name('admin.products.search');



// router của Khôi
//lienhe
Route::get('/admin/quanlylienhe', [ContactAdminController::class, 'index'])->name('admin.quanlylienhe.index');
Route::get('/admin/quanlylienhe/{id}', [ContactAdminController::class, 'show'])->name('admin.quanlylienhe.show');
Route::post('/admin/quanlylienhe/{id}/reply', [ContactAdminController::class, 'reply'])->name('admin.quanlylienhe.reply');
Route::delete('/admin/quanlylienhe/{id}', [ContactAdminController::class, 'destroy'])->name('admin.quanlylienhe.destroy');
//ql user
Route::get('/admin/quanlykhachhang', [AdminCustomerController::class, 'index'])->name('admin.customers.index');
Route::get('/admin/khachhang/{id}', [AdminCustomerController::class, 'show']);
Route::post('/admin/khachhang', [AdminCustomerController::class, 'store']);
Route::put('/admin/khachhang/{id}', [AdminCustomerController::class, 'update']);
Route::delete('/admin/khachhang/{id}', [AdminCustomerController::class, 'destroy']);
Route::patch('/admin/khachhang/{id}/lock', [AdminCustomerController::class, 'lockToggle']);
//ql role
Route::get('/admin/quanlynguoidung', [AdminUserController::class, 'index'])->name('admin.users.index');
Route::post('/admin/quanlynguoidung/add', [AdminUserController::class, 'add'])->name('admin.users.add');
Route::put('/admin/quanlynguoidung/{id}/update', [AdminUserController::class, 'updateRoleAndStatus'])->name('admin.users.update');
Route::delete('/admin/quanlynguoidung/{id}/remove-role', [AdminUserController::class, 'removeRole'])->name('admin.users.removeRole');
Route::get('/admin/quanlynguoidung/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');


// Danh sách banner
Route::get('/admin/quanlybanner', [BannerAdminController::class, 'index'])->name('admin.banners.index');

// Thêm mới banner (POST)
Route::post('/admin/banners', [BannerAdminController::class, 'store'])->name('admin.banners.store');

// Cập nhật banner (PUT hoặc PATCH)
Route::put('/admin/banners/{id}', [BannerAdminController::class, 'update'])->name('admin.banners.update');

// Xóa banner
Route::delete('/admin/banners/{id}', [BannerAdminController::class, 'destroy'])->name('admin.banners.destroy');

Route::get('/check-login', [UserInfoController::class, 'Kiem_tra_login']);
// chung theem
Route::get('/admin/khuyenmai', [VoucherAdminController::class, 'index'])->name('admin.vouchers.index');
Route::post('/admin/vouchers', [VoucherAdminController::class, 'store'])->name('admin.vouchers.store');
Route::delete('/admin/vouchers/{id}', [VoucherAdminController::class, 'destroy'])->name('admin.vouchers.destroy');
Route::put('/admin/vouchers/{id}', [VoucherAdminController::class, 'update'])->name('vouchers.update');



Route::get('/admin/danhmuc', [CategoryAdminController::class, 'index'])->name('admin.categories.index');
Route::post('/admin/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
Route::delete('/admin/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');
Route::put('/admin/categories/{id}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');


// manh
// Route::get('/admin/comments', function () {
//     return view('admin.comments');
// });
Route::get('/admin/comments', [AdminReviewController::class, 'index']);
Route::post('/admin/reply-comments', [AdminReviewController::class, 'replyComments'])->name('reply-comments');
Route::get('/admin/comment/delete/{id}', [AdminReviewController::class, 'destroy'])->name('admin.comment.delete');

// Route::get('/admin/baocao', function () {
//     return view('admin.baocao');
// });
Route::get('/admin/baocao', [AdminBaocaoController::class, 'index']);
Route::post('/admin/reports/filter', [AdminBaocaoController::class, 'filter'])->name('admin.reports.filter');

Route::post('/admin/send-bulk-mail', [AdminCustomerController::class, 'sendBulkMail']);
Route::get('/review/{order}', [ReviewController::class, 'create'])->name('review.form');
Route::post('/review/{order}', [ReviewController::class, 'store'])->name('review.store');

// Route::get('/', [PageController::class, 'homepage'])->name('homepage');

Route::get('/admin/caidat', [AdminSettingController::class, 'index']);
Route::post('/admin/settings/update', [AdminSettingController::class, 'update'])->name('admin.settings.update');
