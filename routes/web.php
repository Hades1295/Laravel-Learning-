<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
/*------------------------
Product Controller for CRUD Operations  
-----------------------------------*/
Route::get('products', [ProductController::class,'index'])->name('products.index');
Route::get('create', [ProductController::class,'create']);
Route::post('products', [ProductController::class,'store'])->name('products.store');
Route::get('products/show/{id}', [ProductController::class,'show'])->name('products.show');
Route::get('products/delete/{id}', [ProductController::class,'destroy'])->name('products.delete');
Route::get('products/edit/{id}', [ProductController::class,'edit'])->name('products.edit');
Route::post('products/update/{id}', [ProductController::class,'update'])->name('products.update');
/*------------------------
Product Controller for CRUD Operations  
-----------------------------------*/