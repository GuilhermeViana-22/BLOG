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


Route::prefix('blog')->name('blog.')->group(function () {
    // Listar todos os posts
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::post('/', [BlogController::class, 'store'])->name('store');
    Route::get('/{id}', [BlogController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('edit');
    Route::delete('/{id}', [BlogController::class, 'destroy'])->name('destroy');
});

// Rotas para gerenciamento de categorias
Route::get  ('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
Route::post('/categories/increment-relevant', [CategoryController::class, 'incrementRelevant'])->name('categories.incrementRelevant');




