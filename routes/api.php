<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verifycode', [AuthController::class, 'verifycode'])->name('verifycode');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/me', [AuthController::class, 'me'])->name('me');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


use App\Http\Middleware\ValidateApiToken;

Route::prefix('blog')->name('blog.')->group(function () {
    // Listar todos os posts (não protegido pelo middleware)
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{id}', [BlogController::class, 'show'])->name('show');

    // Aplicar o middleware para as outras rotas
    Route::middleware(ValidateApiToken::class)->group(function () {
        Route::post('/', [BlogController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('edit');
        Route::delete('/{id}', [BlogController::class, 'destroy'])->name('destroy');
    });
});



Route::prefix('cotegories')->name('categories.')->group(function (){
    //aqui ficaram as rotas publicas
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');

    //rotas privadas que precisam de alterações com token
    Route::middleware(ValidateApiToken::class)->group(function () {
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/increment-relevant', [CategoryController::class, 'incrementRelevant'])->name('categories.incrementRelevant');
    });
});






