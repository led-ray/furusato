<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 記事の取得
Route::get('/articles/{id}', [ArticleController::class, 'show_API']);

// 記事の作成
Route::post('/articles', [ArticleController::class, 'store_API']);

// 記事の更新
Route::put('/article/{slug}', [ArticleController::class, 'update_API'])->middleware('auth:api');

// 記事の削除
Route::delete('/articles/{slug}', [ArticleController::class, 'destroy_API'])->middleware('auth:api');

// 記事の取得
Route::get('/list', [ArticleController::class, 'list']);
