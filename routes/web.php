<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PricingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::resource('/blog', BlogController::class);
Route::resource('/contact', ContactController::class);
Route::resource('/pricing', PricingController::class);


Route::get('/clear', function (Request $request) {
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    if ($request->server('HTTP_REFERER')) {
        \RealRashid\SweetAlert\Facades\Alert::success('Success!', 'Admin updated successfully.')->hideCloseButton()->autoClose(3000);
        return redirect()->back();
    }

    dd('fresh migrate done!');
})->name('clear');

Route::get('/migrate:fresh', function (Request $request) {
    Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);

    if ($request->server('HTTP_REFERER')) {
        \RealRashid\SweetAlert\Facades\Alert::success('Success!', 'Admin updated successfully.')->hideCloseButton()->autoClose(3000);
        return redirect()->back();
    }

    dd('fresh migrate done!');
})->name('migrate.fresh');

require __DIR__ . '/user.php';
require __DIR__ . '/admin.php';
