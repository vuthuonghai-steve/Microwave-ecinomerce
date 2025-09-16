<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Catalog web pages
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Customer Authentication (web session)
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\CustomerAuthController::class, 'login']);
    Route::get('/register', [\App\Http\Controllers\Auth\CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\CustomerAuthController::class, 'register']);

    // Password reset
    Route::get('/password/forgot', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/password/email', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/password/reset', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.update');
});

// Cart & Checkout (web)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/items/{id}/update', [\App\Http\Controllers\CartController::class, 'updateItem'])->name('cart.items.update');
    Route::post('/cart/items/{id}/remove', [\App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.items.remove');

    Route::get('/checkout', [\App\Http\Controllers\CheckoutWebController::class, 'create'])->middleware('verified')->name('checkout.create');
    Route::post('/checkout', [\App\Http\Controllers\CheckoutWebController::class, 'store'])->middleware('verified')->name('checkout.store');

    Route::get('/my/orders', [\App\Http\Controllers\CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/my/orders/{id}', [\App\Http\Controllers\CustomerOrderController::class, 'show'])->name('orders.show');
    Route::get('/my/orders/{id}/pay', [\App\Http\Controllers\OrderPaymentController::class, 'showForm'])->name('orders.pay.form');
    Route::post('/my/orders/{id}/pay', [\App\Http\Controllers\OrderPaymentController::class, 'pay'])->name('orders.pay');

    // Addresses
    Route::get('/my/addresses', [\App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::get('/my/addresses/create', [\App\Http\Controllers\AddressController::class, 'create'])->name('addresses.create');
    Route::post('/my/addresses', [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::get('/my/addresses/{id}/edit', [\App\Http\Controllers\AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/my/addresses/{id}', [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/my/addresses/{id}', [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');

    // Cart mini count (JSON)
    Route::get('/cart/count', [\App\Http\Controllers\CartController::class, 'count'])->name('cart.count');

    // Profile
    Route::get('/my/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/my/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Wishlist
    Route::get('/my/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/items/{id}/remove', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.items.remove');
    Route::get('/wishlist/count', [\App\Http\Controllers\WishlistController::class, 'count'])->name('wishlist.count');
});

Route::get('/payment/vnpay/return', [\App\Http\Controllers\OrderPaymentController::class, 'handleReturn'])->name('payment.vnpay.return');

Route::post('/logout', [\App\Http\Controllers\Auth\CustomerAuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin UI (Blade)
use App\Http\Controllers\Admin\ProductController as AdminProductWebController;
use App\Http\Controllers\Admin\BrandController as AdminBrandWebController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryWebController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\OrderController as AdminOrderWebController;
use App\Http\Controllers\Admin\ReportController as AdminReportWebController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\OrderPaymentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
// Admin auth
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware(['auth:admin','admin'])->group(function () {
    Route::get('/', function () { return redirect()->route('admin.products.index'); })->name('admin.dashboard');

    // Products
    Route::get('/products', [AdminProductWebController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductWebController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductWebController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminProductWebController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [AdminProductWebController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminProductWebController::class, 'destroy'])->name('admin.products.destroy');

    // Brands
    Route::get('/brands', [AdminBrandWebController::class, 'index'])->name('admin.brands.index');
    Route::get('/brands/create', [AdminBrandWebController::class, 'create'])->name('admin.brands.create');
    Route::post('/brands', [AdminBrandWebController::class, 'store'])->name('admin.brands.store');
    Route::get('/brands/{id}/edit', [AdminBrandWebController::class, 'edit'])->name('admin.brands.edit');
    Route::put('/brands/{id}', [AdminBrandWebController::class, 'update'])->name('admin.brands.update');
    Route::delete('/brands/{id}', [AdminBrandWebController::class, 'destroy'])->name('admin.brands.destroy');

    // Categories
    Route::get('/categories', [AdminCategoryWebController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [AdminCategoryWebController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [AdminCategoryWebController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [AdminCategoryWebController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{id}', [AdminCategoryWebController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminCategoryWebController::class, 'destroy'])->name('admin.categories.destroy');

    // Orders
    Route::get('/orders', [AdminOrderWebController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminOrderWebController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/status', [AdminOrderWebController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/orders/{id}/push-to-shipping', [AdminOrderWebController::class, 'pushToShipping'])->name('admin.orders.push_shipping');

    // Reports
    Route::get('/reports', [AdminReportWebController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/revenue-data', [AdminReportWebController::class, 'revenue'])->name('admin.reports.revenue_data');
    Route::get('/reports/best-selling-data', [AdminReportWebController::class, 'bestSelling'])->name('admin.reports.best_selling_data');
    Route::get('/reports/export', [AdminReportWebController::class, 'export'])->name('admin.reports.export');
});

// Email verification (Laravel built-in)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'Email da duoc xac thuc truoc do.');
        }
        \App\Jobs\SendVerificationEmail::dispatch($request->user());
        return back()->with('status', 'Da xep lich gui email xac thuc. Vui long kiem tra hop thu.');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->intended('/')->with('status', 'Xac thuc email thanh cong!');
})->middleware(['auth','signed'])->name('verification.verify');
