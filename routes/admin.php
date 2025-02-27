<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\Auth\{
    PasswordController,
    NewPasswordController,
    VerifyEmailController,
    RegisteredAdminController,
    PasswordResetLinkController,
    ConfirmablePasswordController,
    AuthenticatedSessionController,
    EmailVerificationPromptController,
    EmailVerificationNotificationController,
};

use App\Http\Controllers\Admin\{
    DashboardController,
    ProfileController,
    ProfilePasswordController,
    AdminUsersController,
    RoleController,
    UsersController,
    CurrenciesController,
};

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('home');

    Route::middleware('guest:admin')->group(function () {
        // Route::get('register', [RegisteredAdminController::class, 'create'])
        //             ->name('register');

        // Route::post('register', [RegisteredAdminController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('password.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        // ProfileController
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


        Route::get('/password', [ProfilePasswordController::class, 'edit'])->name('password.edit');
        Route::put('/password', [ProfilePasswordController::class, 'update'])->name('password.update');
    });

    Route::middleware(['auth:admin', 'verified:admin', 'admin.disabled', 'admin.banned', 'admin.last.activity'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/all-activity-logs', [DashboardController::class, 'allActivityLogs'])->name('allActivityLogs');
        Route::post('/store-export-activity-log', [DashboardController::class, 'storeExportActivityLog'])->name('store-export-activity-log');

        Route::resource('admins', AdminUsersController::class);
        Route::post('roles/{id}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::resource('roles', RoleController::class);

        Route::resource('currencies', CurrenciesController::class);

        // User Routes
        Route::get('users/{id}/activity-logs', [UsersController::class, 'activityLogs'])->name('users.activity-logs');
        Route::resource('users', UsersController::class);
    });
});
