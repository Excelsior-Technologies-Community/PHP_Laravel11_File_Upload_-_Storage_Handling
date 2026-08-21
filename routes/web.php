<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerProductsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TempImageController;
use App\Http\Controllers\ImageDownloadController;


/*
|--------------------------------------------------------------------------
| Welcome
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Customer Product Viewing
|--------------------------------------------------------------------------
*/

Route::get(
    '/customer/products',
    [CustomerProductsController::class, 'index']
)->name('customer.products');


/*
|--------------------------------------------------------------------------
| Tags
|--------------------------------------------------------------------------
*/

Route::resource(
    'tags',
    TagController::class
);


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get(
    '/dashboard',
    function () {
        return view('dashboard');
    }
)
    ->middleware([
        'auth',
        'verified'
    ])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Product Routes
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | "show" is excluded because ProductController does not contain show().
    |
    */

    Route::resource(
        'products',
        ProductController::class
    )->except([
        'show'
    ]);


    /*
    |--------------------------------------------------------------------------
    | CSV Export
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/products/export/csv',
        [ProductController::class, 'exportCsv']
    )->name('products.export');


    /*
    |--------------------------------------------------------------------------
    | Recycle Bin
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/products/recycle-bin',
        [ProductController::class, 'trash']
    )->name('products.trash');


    /*
    |--------------------------------------------------------------------------
    | Restore Product
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/products/recycle-bin/{id}/restore',
        [ProductController::class, 'restore']
    )->name('products.restore');


    /*
    |--------------------------------------------------------------------------
    | Force Delete Product
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/products/recycle-bin/{id}/force-delete',
        [ProductController::class, 'forceDelete']
    )->name('products.force-delete');


    /*
    |--------------------------------------------------------------------------
    | Temporary Image Upload
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/products/temp-upload',
        [TempImageController::class, 'upload']
    )->name('products.temp-upload');


    Route::delete(
        '/products/temp-upload/{filename}',
        [TempImageController::class, 'destroy']
    )->name('products.temp-destroy');


    /*
    |--------------------------------------------------------------------------
    | Image Download
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/products/image/download/{filename}',
        [ImageDownloadController::class, 'download']
    )->name('products.image.download');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');


    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');


    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
