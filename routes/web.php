<?php

use Livewire\Volt\Volt;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\Roles\AdminController;
use App\Http\Controllers\Roles\CustomerController;
use App\Http\Controllers\Roles\DeliveryPersonController;
use App\Http\Controllers\Roles\ManagerController;
use App\Http\Livewire\Auth\Register as RegisterComponent;


Rout::view('/login','auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::get('/register', RegisterComponent::class)->name('register');

Route::middleware(['auth'])->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    });

    Route::middleware('role:manager')->group(function () {
        Route::get('/manager/overview', [ManagerController::class, 'overview'])->name('manager.overview');
    });

    Route::middleware('role:delivery_person')->group(function () {
        Route::get('/delivery/history', [DeliveryPersonController::class, 'history'])->name('delivery.history');
    });

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    });
});
