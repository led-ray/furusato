<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 記事一覧（ホーム画面）
Route::get('/', [ArticleController::class, 'index']);

// 記事の投稿画面を表示
Route::get('/editor', [ArticleController::class, 'showEditor'])->name('editor.new');

// 記事を投稿
Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');

// 記事を表示
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

// タグに基づいて記事をフィルタリング
Route::get('/articles/tag/{tag}', [ArticleController::class, 'indexByTag'])->name('articles.indexByTag');

// 記事にコメントを投稿
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

// 記事の編集画面を表示
Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');

// 編集された記事を更新
Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');

// 記事を削除
Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
