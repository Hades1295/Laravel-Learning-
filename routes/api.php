<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
// use App\Post;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('post', [PostController::class,'index']);
Route::get('post/{id}', [PostController::class,'show']);
Route::post('post', [PostController::class,'create']);
Route::put('post/{id}', [PostController::class,'update']);
Route::delete('post/{id}',[PostController::class,'destroy']);


Route::get('/testing-api',function(){
   return ['message' => 'hello'];
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
