<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PricingController;
use Illuminate\Support\Facades\Route;



Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::resource('/blog', BlogController::class);
Route::resource('/contact', ContactController::class);
Route::resource('/pricing', PricingController::class);



require __DIR__ . '/user.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/demo.php';
