<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verifycode', [AuthController::class, 'verifycode'])->name('verifycode');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{id}', [BlogController::class, 'show'])->name('show');
});

Route::prefix('blog')->name('blog.')->middleware('api.token')->group(function () {
    Route::post('/', [BlogController::class, 'store'])->name('store');
    Route::put('/{id}', [BlogController::class, 'update'])->name('update');
    Route::delete('/{id}', [BlogController::class, 'destroy'])->name('destroy');
});
