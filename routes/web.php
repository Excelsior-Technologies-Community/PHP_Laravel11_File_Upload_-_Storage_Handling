<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerProductsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TempImageController;
use App\Http\Controllers\ImageDownloadController;

Route::middleware(['auth'])->group(function () {

    // Product CRUD
    Route::resource('products', ProductController::class);

    // Temporary image upload
    Route::post(
        '/products/temp-upload',
        [TempImageController::class, 'upload']
    )->name('products.temp-upload');

    Route::delete(
        '/products/temp-upload/{filename}',
        [TempImageController::class, 'destroy']
    )->name('products.temp-destroy');

    // Image download
    Route::get(
        '/products/image/download/{filename}',
        [ImageDownloadController::class, 'download']
    )->name('products.image.download');
});

// Customer product viewing
Route::get(
    '/customer/products',
    [CustomerProductsController::class, 'index']
)->name('customer.products');

Route::resource('tags', TagController::class);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';