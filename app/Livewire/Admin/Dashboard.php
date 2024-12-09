<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalRevenue = 0;
    public $totalOrders = 0;
    public $newCustomers = 0;
    public $activeProducts = 0;
    public $topCustomersHeaders = [];
    public $topCustomersRows = [];
    public $bestSellingProductsHeaders = [];
    public $bestSellingProductsRows = [];
    public $recentOrdersHeaders = [];
    public $recentOrdersRows = [];
    public $productCategoryChart = [];
    public $grossRevenueChart = [];

    public function mount()
    {
        // Total Revenue
        $this->totalRevenue = Order::sum('total_price') ?? 0;

        // Total Orders
        $this->totalOrders = Order::count() ?? 0;

        // New Customers
        $this->newCustomers = User::where('role', 'customer')->count() ?? 0;

        // Active Products
        $this->activeProducts = Product::count() ?? 0;

        // Top Customers
        $this->topCustomersHeaders = [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'revenue', 'label' => 'Revenue'],
        ];
        $this->topCustomersRows = User::where('role', 'customer')
            ->withSum('orders', 'total_price')
            ->orderByDesc('orders_sum_total_price')
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'name' => ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''),
                    'email' => $customer->email ?? 'N/A',
                    'revenue' => $customer->orders_sum_total_price ?? 0,
                ];
            })
            ->toArray();

        // Best-Selling Products
        $this->bestSellingProductsHeaders = [
            ['key' => 'name', 'label' => 'Product'],
            ['key' => 'price', 'label' => 'Price'],
            ['key' => 'sales', 'label' => 'Sales'],
        ];
        $this->bestSellingProductsRows = Product::select('products.*')
            ->selectRaw('SUM(order_items.quantity) as total_sales')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name ?? 'N/A',
                    'price' => $product->price ?? 0,
                    'sales' => $product->total_sales ?? 0,
                ];
            })
            ->toArray();

        // Recent Orders
        $this->recentOrdersHeaders = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'date', 'label' => 'Date'],
            ['key' => 'customer', 'label' => 'Customer'],
            ['key' => 'total', 'label' => 'Total'],
            ['key' => 'status', 'label' => 'Status'],
        ];
        $this->recentOrdersRows = Order::with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id ?? 'N/A',
                    'date' => $order->created_at ? $order->created_at->format('M d, Y') : 'N/A',
                    'customer' => ($order->user->first_name ?? 'N/A') . ' ' . ($order->user->last_name ?? ''),
                    'total' => $order->total_price ?? 0,
                    'status' => $order->status ?? 'N/A',
                ];
            })
            ->toArray();

        // Product Categories Chart
        $categories = Category::withCount('products')->get();
        $this->productCategoryChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $categories->pluck('name')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Products by Category',
                        'data' => $categories->pluck('products_count')->toArray(),
                        'backgroundColor' => [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                        ],
                        'borderColor' => [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                        ],
                        'borderWidth' => 1,
                    ]
                ]
            ]
        ];

        // Gross Revenue Chart
        $this->grossRevenueChart = [
            'type' => 'line',
            'data' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                'datasets' => [
                    [
                        'label' => 'Revenue (€)',
                        'data' => [12000, 19000, 3000, 5000, 20000, 15000, 10000],
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'borderWidth' => 1,
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalRevenue' => $this->totalRevenue,
            'totalOrders' => $this->totalOrders,
            'newCustomers' => $this->newCustomers,
            'activeProducts' => $this->activeProducts,
            'topCustomersHeaders' => $this->topCustomersHeaders,
            'topCustomersRows' => $this->topCustomersRows,
            'bestSellingProductsHeaders' => $this->bestSellingProductsHeaders,
            'bestSellingProductsRows' => $this->bestSellingProductsRows,
            'recentOrdersHeaders' => $this->recentOrdersHeaders,
            'recentOrdersRows' => $this->recentOrdersRows,
            'productCategoryChart' => $this->productCategoryChart,
            'grossRevenueChart' => $this->grossRevenueChart,
        ]);
    }
}
