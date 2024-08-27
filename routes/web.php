<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController; // Corrigido para importar a classe BlogController

Route::get('/', [BlogController::class, 'index'])->name('index');
