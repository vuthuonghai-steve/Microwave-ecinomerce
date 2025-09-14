<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Catalog web pages
use App\Http\Controllers\ProductController;
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Admin UI (Blade)
use App\Http\Controllers\Admin\ProductController as AdminProductWebController;
use App\Http\Controllers\Admin\BrandController as AdminBrandWebController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryWebController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\OrderController as AdminOrderWebController;
use App\Http\Controllers\Admin\ReportController as AdminReportWebController;

// Admin auth
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('admin')->group(function () {
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
