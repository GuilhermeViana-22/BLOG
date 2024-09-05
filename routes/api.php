<?php
use App\Http\Middleware\ValidateApiToken;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verifycode', [AuthController::class, 'verifycode'])->name('verifycode');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/me', [AuthController::class, 'me'])->name('me');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('blog')->name('blog.')->group(function () {
    // Listar todos os posts (não protegido pelo middleware)
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{id}', [BlogController::class, 'show'])->name('show');

    // Aplicar o middleware para as outras rotas
    Route::middleware(ValidateApiToken::class)->group(function () {
        Route::delete('/delete', [BlogController::class, 'delete'])->name('delete');
        Route::post('/', [BlogController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('edit');
    });
});

Route::prefix('categories')->name('categories.')->group(function () {
    //rotas publicas
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    //rotas protegidas
    Route::middleware(ValidateApiToken::class)->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/increment-relevant', [CategoryController::class, 'incrementRelevant'])->name('incrementRelevant');
    });
});

//
Route::post('/tags', [TagsController::class, 'store']);


