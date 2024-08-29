<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;


Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{id}', [BlogController::class, 'show'])->name('show');
});

Route::prefix('blog')->name('blog.')->middleware('auth:api')->group(function () {
    Route::post('/', [BlogController::class, 'store'])->name('store');
    Route::put('/{id}', [BlogController::class, 'update'])->name('update');
    Route::delete('/{id}', [BlogController::class, 'destroy'])->name('destroy');
});
