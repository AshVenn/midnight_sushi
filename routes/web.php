<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', \App\Livewire\Home\Home::class)->name('home');

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);

    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.update');
});


Route::get('/about', \App\Livewire\About\About::class)->name('about');
Route::get('/faq', \App\Livewire\FAQ\FAQ::class)->name('faq');

Route::get('/cart', \App\Livewire\Cart::class)->name('cart');
Route::get('/checkout', \App\Livewire\Checkout::class)->name('checkout')->middleware('auth');
Route::get('/menus-products', \App\Livewire\MenusProductsPage::class)->name('menus-products');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/settings', \App\Livewire\Admin\Components\Settings::class)->name('admin.settings');
        Route::get('/admin/contactinquiries', \App\Livewire\Admin\ContactInquiries::class)->name('admin.contactinquiries');

        // User management routes
        Route::get('/admin/users', \App\Livewire\Admin\Components\Users\Index::class)->name('admin.users.index');
        Route::get('/admin/users/create', \App\Livewire\Admin\Components\Users\Create::class)->name('admin.users.create');
        Route::get('/admin/users/{user}', \App\Livewire\Admin\Components\Users\Show::class)->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', \App\Livewire\Admin\Components\Users\Edit::class)->name('admin.users.edit');

        // Categories management routes
        Route::get('/admin/categories', \App\Livewire\Admin\Components\Categories\Index::class)->name('admin.categories.index');
        Route::get('/admin/categories/create', \App\Livewire\Admin\Components\Categories\Create::class)->name('admin.categories.create');
        Route::get('/admin/categories/{category}/edit', \App\Livewire\Admin\Components\Categories\Edit::class)->name('admin.categories.edit');

        // Product management routes
        Route::get('/admin/products', \App\Livewire\Admin\Components\Products\Index::class)->name('admin.products.index');
        Route::get('/admin/products/create', \App\Livewire\Admin\Components\Products\Create::class)->name('admin.products.create');
        Route::get('/admin/products/{product}/edit', \App\Livewire\Admin\Components\Products\Edit::class)->name('admin.products.edit');

        // Menu management routes
        Route::get('/admin/menus', \App\Livewire\Admin\Components\Menus\Index::class)->name('admin.menus.index');
        Route::get('/admin/menus/create', \App\Livewire\Admin\Components\Menus\Create::class)->name('admin.menus.create');
        Route::get('/admin/menus/{menu}/edit', \App\Livewire\Admin\Components\Menus\Edit::class)->name('admin.menus.edit');
        Route::get('/admin/menus/{menu}', \App\Livewire\Admin\Components\Menus\Show::class)->name('admin.menus.show');

        // Orders Management routes
        Route::get('/admin/orders', \App\Livewire\Admin\Components\Orders\Index::class)->name('admin.orders.index');

        // Delivery Management routes
        Route::get('/admin/deliveries', \App\Livewire\Admin\Components\Deliveries\Index::class)->name('admin.deliveries.index');
    });

    // Customer routes
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/customer/profile', \App\Livewire\Customer\Profile::class)->name('customer.profile');
        Route::get('/customer/support', \App\Livewire\Customer\Support::class)->name('customer.support');
        Route::get('/customer/dashboard', \App\Livewire\Customer\Dashboard::class)->name('customer.dashboard');
        Route::get('/customer/orders', \App\Livewire\Customer\Orders::class)->name('customer.orders.index');
        Route::get('/customer/orders/{order}', \App\Livewire\Customer\OrderShow::class)->name('customer.orders.show');
    });

    // Manager routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager/dashboard', \App\Livewire\Manager\Dashboard::class)->name('manager.dashboard');
        Route::get('/manager/settings', \App\Livewire\Manager\Components\Settings::class)->name('manager.settings');

        Route::get('/manager/users', \App\Livewire\Manager\Components\Users\Index::class)->name('manager.users.index');
        Route::get('/manager/users/create', \App\Livewire\Manager\Components\Users\Create::class)->name('manager.users.create');
        Route::get('/manager/users/{user}/edit', \App\Livewire\Manager\Components\Users\Edit::class)->name('manager.users.edit');

        // Categories
        Route::get('/manager/categories', \App\Livewire\Manager\Components\Categories\Index::class)->name('manager.categories.index');
        Route::get('/manager/categories/create', \App\Livewire\Manager\Components\Categories\Create::class)->name('manager.categories.create');
        Route::get('/manager/categories/{category}/edit', \App\Livewire\Manager\Components\Categories\Edit::class)->name('manager.categories.edit');

        // Products
        Route::get('/manager/products', \App\Livewire\Manager\Components\Products\Index::class)->name('manager.products.index');
        Route::get('/manager/products/create', \App\Livewire\Manager\Components\Products\Create::class)->name('manager.products.create');
        Route::get('/manager/products/{product}/edit', \App\Livewire\Manager\Components\Products\Edit::class)->name('manager.products.edit');

        // Menus
        Route::get('/manager/menus', \App\Livewire\Manager\Components\Menus\Index::class)->name('manager.menus.index');
        Route::get('/manager/menus/create', \App\Livewire\Manager\Components\Menus\Create::class)->name('manager.menus.create');
        Route::get('/manager/menus/{menu}/edit', \App\Livewire\Manager\Components\Menus\Edit::class)->name('manager.menus.edit');
        Route::get('/manager/menus/{menu}', \App\Livewire\Manager\Components\Menus\Show::class)->name('manager.menus.show');

        // Orders
        Route::get('/manager/orders', \App\Livewire\Manager\Components\Orders\Index::class)->name('manager.orders.index');

        // Deliveries
        Route::get('/manager/deliveries', \App\Livewire\Manager\Components\Deliveries\Index::class)->name('manager.deliveries.index');
    });

    Route::middleware(['role:delivery_person'])->group(function () {
        Route::get('/deliveryperson/dashboard', \App\Livewire\DeliveryPerson\Dashboard::class)->name('delivery-person.dashboard');
        Route::get('/deliveryperson/deliveries', \App\Livewire\DeliveryPerson\Components\Deliveries::class)->name('delivery-person.deliveries.index');
        Route::get('/deliveryperson/pastdeliveries', \App\Livewire\DeliveryPerson\Components\PastDeliveries::class)->name('delivery-person.past-deliveries.index');
        Route::get('/deliveryperson/settings', \App\Livewire\DeliveryPerson\Settings::class)->name('delivery-person.settings.index');
    });
});
