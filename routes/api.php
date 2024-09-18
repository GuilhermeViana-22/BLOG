<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TagsController;
use App\Http\Middleware\ValidateApiToken;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verifycode', [AuthController::class, 'verifycode'])->name('verifycode');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/me', [AuthController::class, 'me'])->name('me');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/teste', [AuthController::class, 'teste'])->name('teste');


Route::prefix('post')->name('post.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{id}', [BlogController::class, 'show'])->name('show');
    Route::middleware(ValidateApiToken::class)->group(function () {

     // Listar todos os posts (não protegido pelo middleware)
     Route::post('/update', [BlogController::class, 'update'])->name('update');
     Route::post('/', [BlogController::class, 'store'])->name('store');
     Route::delete('/delete', [BlogController::class, 'delete'])->name('delete');
     Route::delete('/delete', [BlogController::class, 'delete'])->name('delete');
    });
});

Route::prefix('categories')->name('categories.')->group(function () {
    //rotas publicas
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    //rotas protegidas
    Route::middleware(ValidateApiToken::class)->group(function () {
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::post('/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('delete', [CategoryController::class, 'delete'])->name('delete');
        Route::post('/increment-relevant', [CategoryController::class, 'incrementRelevant'])->name('incrementRelevant');
    });
});

Route::prefix('comment')->name('comment.')->group(function () {
    //rotas protegidas
    Route::get('/', [CommentController::class, 'index'])->name('index');
    Route::middleware(ValidateApiToken::class)->group(function () {
        Route::post('/comentar', [CommentController::class, 'comentar'])->name('comentar');
   });
});

//
Route::post('/tags', [TagsController::class, 'store']);


