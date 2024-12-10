<?php

use App\Http\Controllers\AuthController;

use App\Livewire\About\About;

use App\Http\Controllers\Roles\CustomerController;
use App\Http\Controllers\Roles\ManagerController;
use App\Http\Controllers\Roles\DeliveryPersonController;


//Admin Dashboard Controller
use App\Livewire\Admin\Dashboard as AdminDashboard;

//Admin Settings Controller
use App\Livewire\Admin\Components\Settings as AdminSettings;

// Admin User Management Controllers
use App\Livewire\Admin\Components\Users\Index as ManageUserIndex;
use App\Livewire\Admin\Components\Users\Show as ManageUserShow;
use App\Livewire\Admin\Components\Users\Create as ManageUserCreate;
use App\Livewire\Admin\Components\Users\Edit as ManageUserEdit;

// Admin Category Management Controllers
use App\Livewire\Admin\Components\Categories\Index as CategoryIndex;
use App\Livewire\Admin\Components\Categories\Create as CategoryCreate;
use App\Livewire\Admin\Components\Categories\Edit as CategoryEdit;

// Admin Product Management Controllers
use App\Livewire\Admin\Components\Products\Index as ProductIndex;
use App\Livewire\Admin\Components\Products\Create as ProductCreate;
use App\Livewire\Admin\Components\Products\Edit as ProductEdit;





// Public routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/about', About::class)->name('about');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/settings', AdminSettings::class)->name('admin.settings');

        // User management routes
        Route::get('/admin/users', ManageUserIndex::class)->name('admin.users.index');
        Route::get('/admin/users/create', ManageUserCreate::class)->name('admin.users.create');
        Route::get('/admin/users/{user}', ManageUserShow::class)->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', ManageUserEdit::class)->name('admin.users.edit');

        // Categories management routes
        Route::get('/admin/categories', CategoryIndex::class)->name('admin.categories.index');
        Route::get('/admin/categories/create', CategoryCreate::class)->name('admin.categories.create');
        Route::get('/admin/categories/{category}/edit', CategoryEdit::class)->name('admin.categories.edit');

        // Product management routes
        Route::get('/admin/products', ProductIndex::class)->name('admin.products.index');
        Route::get('/admin/products/create', ProductCreate::class)->name('admin.products.create');
        Route::get('/admin/products/{product}/edit', ProductEdit::class)->name('admin.products.edit');
    });

    // Customer routes
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    });

    // Manager routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    });

    // Delivery Person routes
    Route::middleware(['role:delivery_person'])->group(function () {
        Route::get('/delivery-person/dashboard', [DeliveryPersonController::class, 'dashboard'])->name('delivery-person.dashboard');
    });
});
