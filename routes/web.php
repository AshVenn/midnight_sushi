<?php

use App\Http\Controllers\AuthController;

use App\Livewire\Home\Home;
use App\Livewire\About\About;
use App\Livewire\FAQ\FAQ;

// Admin Dashboard Controller
use App\Livewire\Admin\Dashboard as AdminDashboard;

// Admin Settings Controller
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

// Admin Menu Management Controllers
use App\Livewire\Admin\Components\Menus\Index as MenuIndex;
use App\Livewire\Admin\Components\Menus\Create as MenuCreate;
use App\Livewire\Admin\Components\Menus\Edit as MenuEdit;
use App\Livewire\Admin\Components\Menus\Show as MenuShow;

// Admin Order Management Controllers
use App\Livewire\Admin\Components\Orders\Index as OrdersIndex;

// Admin Delivery Management Controllers
use App\Livewire\Admin\Components\Deliveries\Index as DeliveryIndex;

//Admin ContactInquiries Management Controller
use App\Livewire\Admin\ContactInquiries as AdminContactInquiries;

// DeliveryPerson Delivery Management Controllers
use App\Livewire\DeliveryPerson\Dashboard as DeliveryPersonDashboard;
use App\Livewire\DeliveryPerson\Settings as DeliveryPersonSettings;
use App\Livewire\DeliveryPerson\Components\Deliveries as DeliveryPersonDeliveries;
use App\Livewire\DeliveryPerson\Components\PastDeliveries as DeliveryPersonPastDeliveries;




// Customer Management Controllers
use App\Livewire\Customer\Dashboard as CustomerDashboardComponent;
use App\Livewire\Customer\Support as CustomerSupportComponent;
use App\Livewire\Customer\Profile as CustomerProfileComponent;
use App\Livewire\Customer\Orders as CustomerOrdersComponent;
use App\Livewire\Customer\OrderShow as CustomerOrderShowComponent;

// Customer Cart Controller
use App\Livewire\Cart as CartPage;
use App\Livewire\Checkout;
use App\Livewire\MenusProductsPage;

// Home View
Route::get('/', Home::class)->name('home');

// Public routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/about', About::class)->name('about');
Route::get('/faq', FAQ::class)->name('faq');

Route::get('/cart', CartPage::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout')->middleware('auth');
Route::get('/menus-products', MenusProductsPage::class)->name('menus-products');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/settings', AdminSettings::class)->name('admin.settings');
        Route::get('/admin/contactinquiries', AdminContactInquiries::class)->name('admin.contactinquiries');

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

        // Menu management routes
        Route::get('/admin/menus', MenuIndex::class)->name('admin.menus.index');
        Route::get('/admin/menus/create', MenuCreate::class)->name('admin.menus.create');
        Route::get('/admin/menus/{menu}/edit', MenuEdit::class)->name('admin.menus.edit');
        Route::get('/admin/menus/{menu}', MenuShow::class)->name('admin.menus.show');

        // Orders Management routes
        Route::get('/admin/orders', OrdersIndex::class)->name('admin.orders.index');

        // Delivery Management routes
        Route::get('/admin/deliveries', DeliveryIndex::class)->name('admin.deliveries.index');
    });

    // Customer routes
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/customer/profile', CustomerProfileComponent::class)->name('customer.profile');
        Route::get('/customer/support', CustomerSupportComponent::class)->name('customer.support');
        Route::get('/customer/dashboard', CustomerDashboardComponent::class)->name('customer.dashboard');
        Route::get('/customer/orders', CustomerOrdersComponent::class)->name('customer.orders.index');
        Route::get('/customer/orders/{order}', CustomerOrderShowComponent::class)->name('customer.orders.show');
    });

    // Manager routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager/dashboard', \App\Livewire\Manager\Dashboard::class)->name('manager.dashboard');
        Route::get('/manager/settings', \App\Livewire\Manager\Components\Settings::class)->name('manager.settings');

        Route::get('/manager/users', \App\Livewire\Manager\Components\Users\Index::class)->name('manager.users.index');
        Route::get('/manager/users/create', \App\Livewire\Manager\Components\Users\Create::class)->name('manager.users.create');
        Route::get('/manager/users/{user}/edit', \App\Livewire\Manager\Components\Users\Edit::class)->name('manager.users.edit');
        // Route::get('/users/{user}/show', \App\Livewire\Manager\Components\Users\Show::class)->name('manager.users.show');

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

    // Delivery Person routes
    Route::middleware(['role:delivery_person'])->group(function () {
        Route::get('/deliveryperson/dashboard', DeliveryPersonDashboard::class)->name('delivery-person.deliveries.index');
        Route::get('/deliveryperson/deliveries', DeliveryPersonDeliveries::class)->name('delivery-person.deliveries.index');
        Route::get('/deliveryperson/pastdeliveries', DeliveryPersonPastDeliveries::class)->name('delivery-person.past-deliveries.index');
        Route::get('/deliveryperson/settings', DeliveryPersonSettings::class)->name('delivery-person.settings.index');
    });
});
