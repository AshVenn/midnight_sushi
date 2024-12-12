<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Dashboard extends Component
{
    use Toast;

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
        try {
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser) {
                throw new \Exception("Utilisateur non authentifié.");
            }

            // Revenu total
            $this->totalRevenue = Order::whereNotIn('status', ['annulé'])
                ->sum('total_price') ?? 0;

            // Nombre total de commandes
            $this->totalOrders = Order::count() ?? 0;

            // Nouveaux clients
            $this->newCustomers = User::where('role', 'customer')
                ->whereDate('created_at', '>=', now()->subMonth())
                ->count() ?? 0;

            // Produits actifs
            $this->activeProducts = Product::count() ?? 0;

            // Meilleurs clients
            $this->topCustomersHeaders = [
                ['key' => 'name', 'label' => 'Nom'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'revenue', 'label' => 'Revenu'],
            ];

            $this->topCustomersRows = User::where('role', 'customer')
                ->withSum(['orders as orders_sum_total_price' => function ($query) {
                    $query->whereNotIn('status', ['annulé']);
                }], 'total_price')
                ->orderByDesc('orders_sum_total_price')
                ->limit(5)
                ->get()
                ->map(function ($customer) {
                    return [
                        'name' => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                        'email' => $customer->email ?? 'N/A',
                        'revenue' => $customer->orders_sum_total_price ?? 0,
                    ];
                })
                ->toArray();

            // Produits les plus vendus
            $this->bestSellingProductsHeaders = [
                ['key' => 'name', 'label' => 'Produit'],
                ['key' => 'price', 'label' => 'Prix'],
                ['key' => 'sales', 'label' => 'Ventes'],
            ];

            $this->bestSellingProductsRows = Product::select('products.*')
                ->selectRaw('SUM(order_items.quantity) as total_sales')
                ->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereNotIn('orders.status', ['annulé'])
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

            // Commandes récentes
            $this->recentOrdersHeaders = [
                ['key' => 'id', 'label' => '#'],
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'customer', 'label' => 'Client'],
                ['key' => 'total', 'label' => 'Total'],
                ['key' => 'status', 'label' => 'Statut'],
            ];

            $this->recentOrdersRows = Order::with('customer')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id ?? 'N/A',
                        'date' => $order->created_at ? $order->created_at->format('M d, Y') : 'N/A',
                        'customer' => ($order->customer->first_name ?? 'N/A') . ' ' . ($order->customer->last_name ?? ''),
                        'total' => $order->total_price ?? 0,
                        'status' => ucfirst($order->status) ?? 'N/A',
                    ];
                })
                ->toArray();

            // Catégories de produits (diagramme en anneau)
            $categories = Category::withCount('products')->get();
            $this->productCategoryChart = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => $categories->pluck('name')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Produits par catégorie',
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

            // Revenus bruts (diagramme linéaire)
            $grossRevenueData = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
                ->whereNotIn('status', ['annulé'])
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [date('M', mktime(0, 0, 0, $item->month, 1)) => $item->revenue];
                });

            $this->grossRevenueChart = [
                'type' => 'line',
                'data' => [
                    'labels' => $grossRevenueData->keys()->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Revenus (€)',
                            'data' => $grossRevenueData->values()->toArray(),
                            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                            'borderColor' => 'rgba(75, 192, 192, 1)',
                            'borderWidth' => 1,
                        ]
                    ]
                ]
            ];
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue : ' . $e->getMessage()
            );
        }
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
