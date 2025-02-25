<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return redirect()->route('login');
});

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
