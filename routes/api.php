<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CheckoutController as ApiCheckoutController;
use App\Http\Controllers\Api\AddressController as ApiAddressController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ShippingController as AdminShippingController;
use App\Http\Controllers\Api\AuthController;

// Public Catalog API per ../context/spec/Api/products.openapi.yaml
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/{slug}', [ApiProductController::class, 'show']);
Route::get('/categories', [ApiCategoryController::class, 'index']);

// Cart
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [ApiCartController::class, 'show']);
    Route::post('/cart/items', [ApiCartController::class, 'addItem']);
    Route::patch('/cart/items/{id}', [ApiCartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [ApiCartController::class, 'removeItem']);

    // Checkout
    Route::post('/checkout', [ApiCheckoutController::class, 'store']);

    // Addresses (current user)
    Route::get('/addresses', [ApiAddressController::class, 'index']);
});

// Auth (token-based)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Admin endpoints (protected by 'admin' middleware)
Route::prefix('admin')->middleware(['auth:sanctum','admin'])->group(function () {
    // Products
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);

    // Brands
    Route::get('/brands', [AdminBrandController::class, 'index']);
    Route::post('/brands', [AdminBrandController::class, 'store']);
    Route::get('/brands/{id}', [AdminBrandController::class, 'show']);
    Route::put('/brands/{id}', [AdminBrandController::class, 'update']);
    Route::delete('/brands/{id}', [AdminBrandController::class, 'destroy']);

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('/orders/{id}', [AdminOrderController::class, 'updateStatus']);

    // Shipping
    Route::post('/orders/{id}/push-to-shipping', [AdminShippingController::class, 'pushToShipping']);
});

// Shipping webhooks (public)
Route::post('/shipping/webhook', [AdminShippingController::class, 'webhook']);
