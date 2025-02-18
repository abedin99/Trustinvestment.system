<?php

use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');



require __DIR__.'/user.php';
require __DIR__.'/admin.php';
require __DIR__.'/demo.php';
