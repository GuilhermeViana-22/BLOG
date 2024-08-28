<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('blog')->name('blog.')->group(function(){
    Route::get('/', [BlogController::class, 'index'])->name('index'); 
    Route::get('/create', [BlogController::class, 'create'])->name('create'); 
    Route::post('/', [BlogController::class, 'store'])->name('store'); 
    Route::get('/{id}', [BlogController::class, 'show'])->name('show'); 
    Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('edit'); 
    Route::put('/{id}', [BlogController::class, 'update'])->name('update'); 
    Route::delete('/{id}', [BlogController::class, 'destroy'])->name('destroy');
});