<?php

namespace App\Livewire\Manager;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Carbon;

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

    public $bestSellingMenusHeaders = [];
    public $bestSellingMenusRows = [];

    public $recentOrdersHeaders = [];
    public $recentOrdersRows = [];

    public $productCategoryChart = [];
    public $grossRevenueChart = [];

    public function mount()
    {
        try {
            $authenticatedUser = Auth::user();
            if (!$authenticatedUser) {
                $this->error(
                    'Erreur',
                    'Vous devez être authentifié pour effectuer cette action.'
                );
                return;
            }

            // Revenu total
            $this->totalRevenue = Order::whereNotIn('status', ['cancelled'])
                ->sum('total_price') ?? 0;

            // Nombre total de commandes
            $this->totalOrders = Order::count() ?? 0;

            // Nouveaux clients (dernier mois)
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
                    $query->whereNotIn('status', ['cancelled']);
                }], 'total_price')
                ->orderByDesc('orders_sum_total_price')
                ->limit(5)
                ->get()
                ->map(function ($customer) {
                    return [
                        'name'    => $customer->full_name,
                        'email'   => is_string($customer->email) ? $customer->email : 'N/A',
                        'revenue' => $customer->orders_sum_total_price ?? 0,
                    ];
                })
                ->toArray();

            // Produits les plus vendus (fix the sales column so it matches the Blade table)
            $this->bestSellingProductsHeaders = [
                ['key' => 'name', 'label' => 'Produit'],
                ['key' => 'price', 'label' => 'Prix'],
                ['key' => 'sales', 'label' => 'Ventes'], // must match 'sales' in Blade
            ];
            
            $productsRaw = \App\Models\Product::topSellingProducts(5);
            $this->bestSellingProductsRows = collect($productsRaw)
                ->map(function ($product) {
                    return [
                        'name'  => $product->name,
                        'price' => $product->price,
                        'sales' => $product->total_sales,
                    ];
                })->toArray();

            // NEW: Menus les plus vendus
            $this->bestSellingMenusHeaders = [
                ['key' => 'name', 'label' => 'Menu'],
                ['key' => 'price', 'label' => 'Prix'],
                ['key' => 'sales', 'label' => 'Ventes'],
            ];
            
            $menusRaw = \App\Models\Menu::topSellingMenus(5);
            $this->bestSellingMenusRows = collect($menusRaw)
                ->map(function ($menu) {
                    return [
                        'name'  => $menu->name,
                        'price' => $menu->price,
                        'sales' => $menu->total_sales,
                    ];
                })->toArray();
            

            
            // Catégories de produits (chart)
            $categories = Category::withCount('products')->get();
            $this->productCategoryChart = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => $categories->pluck('name')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Produits par catégorie',
                            'data' => $categories->pluck('products_count')->toArray(),
                            // colors can remain if you want
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

            // Revenus bruts (gross revenue chart) ...
            $grossRevenueData = Order::where('status', '!=', 'cancelled')
                ->get()
                ->groupBy(function ($order) {
                    return $order->created_at->format('Y-m');
                })
                ->mapWithKeys(function ($orders, $monthYear) {
                    $date = Carbon::createFromFormat('Y-m', $monthYear);
                    $monthName = $date->format('M');
                    $year = $date->format('Y');
                    $revenue = $orders->sum('total_price');
                    return ["$monthName $year" => floatval($revenue)];
                });

            $this->grossRevenueChart = [
                'type' => 'line',
                'data' => [
                    'labels' => $grossRevenueData->keys()->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Revenus (CHF)',
                            'data' => $grossRevenueData->values()->toArray(),
                            'fill' => true,
                            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                            'borderColor' => 'rgba(75, 192, 192, 1)',
                            'borderWidth' => 1,
                        ]
                    ]
                ],
                'options' => [
                    'responsive' => true,
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                            'title' => ['display' => true, 'text' => 'Montant (CHF)'],
                        ],
                        'x' => [
                            'title' => ['display' => true, 'text' => 'Mois'],
                        ]
                    ],
                    'plugins' => [
                        'legend' => ['position' => 'top'],
                    ],
                ],
            ];
            
        } catch (\Exception $e) {
            $this->error(
                'Erreur',
                'Une erreur est survenue lors du chargement du tableau de bord.'
            );
            \Log::error('Dashboard mount error: ' . $e->getMessage());
        }
    }
    

    public function render()
    {
        return view('livewire.manager.dashboard', [
            'totalRevenue'                => $this->totalRevenue,
            'totalOrders'                 => $this->totalOrders,
            'newCustomers'                => $this->newCustomers,
            'activeProducts'              => $this->activeProducts,
            'topCustomersHeaders'         => $this->topCustomersHeaders,
            'topCustomersRows'            => $this->topCustomersRows,
            'bestSellingProductsHeaders'  => $this->bestSellingProductsHeaders,
            'bestSellingProductsRows'     => $this->bestSellingProductsRows,

           
            'bestSellingMenusHeaders'     => $this->bestSellingMenusHeaders,
            'bestSellingMenusRows'        => $this->bestSellingMenusRows,

            'recentOrdersHeaders'         => $this->recentOrdersHeaders,
            'recentOrdersRows'            => $this->recentOrdersRows,
            'productCategoryChart'        => $this->productCategoryChart,
            'grossRevenueChart'           => $this->grossRevenueChart,
        ]);
    }
}
