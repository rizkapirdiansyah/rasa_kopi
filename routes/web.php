<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'about'])->name('about');

Route::get('/menu', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/menu/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

// Shopping Cart
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/keranjang/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/keranjang/hapus/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/keranjang/kupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/keranjang/kupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

// Order confirmation & invoice
Route::get('/pesanan/konfirmasi/{orderNumber}', [OrderController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/pesanan/invoice/{orderNumber}', [OrderController::class, 'invoice'])->name('orders.invoice');

/*
|--------------------------------------------------------------------------
| Guest Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Customer Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Profile & Address
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/alamat', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::delete('/profil/alamat/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.addresses.delete');

    // Customer Orders
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan-saya/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan-saya/{orderNumber}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/pesanan-saya/{orderNumber}/upload-bukti', [OrderController::class, 'uploadPaymentProof'])->name('orders.payment-proof');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/pindah-keranjang/{productId}', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

    // Reviews
    Route::post('/ulasan/{productId}', [ReviewController::class, 'store'])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Products Management
    Route::resource('products', AdminProductController::class);

    // Categories Management
    Route::resource('categories', AdminCategoryController::class);

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/verify-payment', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');

    // Customer Management
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');

    // Stock Management
    Route::get('/stock', [AdminStockController::class, 'index'])->name('stock.index');
    Route::post('/stock/{product}', [AdminStockController::class, 'update'])->name('stock.update');

    // Promo & Coupons Management
    Route::resource('coupons', AdminCouponController::class);

    // Reviews Management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/toggle', [AdminReviewController::class, 'toggleApproval'])->name('reviews.toggle');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Payment Methods Management
    Route::get('/payment-methods', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('/payment-methods/{paymentMethod}/edit', [AdminPaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    Route::put('/payment-methods/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::post('/payment-methods/{paymentMethod}/toggle', [AdminPaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');

    // Reports Management
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [AdminReportController::class, 'exportCsv'])->name('reports.export');

    // Store Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
