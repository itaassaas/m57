<?php

use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/categories/{categoryId}', [StorefrontController::class, 'category'])->name('categories.show');
Route::get('/categories/{categoryId}/products', [StorefrontController::class, 'categoryProducts'])->name('categories.products');
Route::get('/products/{productId}', [StorefrontController::class, 'show'])->name('products.show');
Route::get('/products/{productId}/state', [StorefrontController::class, 'productState'])->name('products.state');
Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart.show');
Route::post('/cart', [StorefrontController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/{itemKey}', [StorefrontController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/{itemKey}', [StorefrontController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout.show');
Route::get('/checkout/departments', [StorefrontController::class, 'checkoutDepartments'])->name('checkout.departments');
Route::get('/checkout/departments/{department}/cities', [StorefrontController::class, 'checkoutCities'])->name('checkout.cities');
Route::get('/checkout/map-preview', [StorefrontController::class, 'checkoutMapPreview'])->name('checkout.map-preview');
Route::post('/checkout', [StorefrontController::class, 'placeOrder'])->name('checkout.place');
Route::get('/success/{marketplaceOrder}', [StorefrontController::class, 'success'])->name('checkout.success');

Route::get('/docs', fn () => view('docs.index'))->name('docs.index');
Route::get('/docs/swagger', fn () => view('docs.swagger'))->name('docs.swagger');
Route::get('/docs/openapi.yaml', function () {
    $path = base_path('docs/openapi.yaml');
    abort_unless(File::exists($path), 404);

    return response(File::get($path), 200, [
        'Content-Type' => 'application/yaml; charset=UTF-8',
    ]);
})->name('docs.openapi');
