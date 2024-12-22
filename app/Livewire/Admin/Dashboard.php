<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
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
                    'Erreur', // Changed to positional arguments
                    'Vous devez être authentifié pour effectuer cette action.'
                );
                return;
            }
    
            // Revenu total (Total Revenue)
            $this->totalRevenue = Order::whereNotIn('status', ['cancelled']) // Updated status
                ->sum('total_price') ?? 0;
    
            // Nombre total de commandes (Total Orders)
            $this->totalOrders = Order::count() ?? 0;
    
            // Nouveaux clients (New Customers in the last month)
            $this->newCustomers = User::where('role', 'customer')
                ->whereDate('created_at', '>=', now()->subMonth())
                ->count() ?? 0;
    
            // Produits actifs (Active Products)
            $this->activeProducts = Product::count() ?? 0;
    
            // Meilleurs clients (Top Customers)
            $this->topCustomersHeaders = [
                ['key' => 'name', 'label' => 'Nom'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'revenue', 'label' => 'Revenu'],
            ];
    
            $this->topCustomersRows = User::where('role', 'customer')
                ->withSum(['orders as orders_sum_total_price' => function ($query) {
                    $query->whereNotIn('status', ['cancelled']); // Updated status
                }], 'total_price')
                ->orderByDesc('orders_sum_total_price')
                ->limit(5)
                ->get()
                ->map(function ($customer) {
                    return [
                        'name' => $customer->full_name,
                        'email' => is_string($customer->email) ? $customer->email : 'N/A',
                        'revenue' => $customer->orders_sum_total_price ?? 0,
                    ];
                })
                ->toArray();
                
    
            // Produits les plus vendus (Best Selling Products)
            $this->bestSellingProductsHeaders = [
                ['key' => 'name', 'label' => 'Produit'],
                ['key' => 'price', 'label' => 'Prix'],
                ['key' => 'sales', 'label' => 'Ventes'],
            ];
    
            $this->bestSellingProductsRows = Product::with('orderItems.order') // Charge les relations nécessaires
            ->withCount(['orderItems as total_sales' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', '!=', 'cancelled'); // Exclut les commandes annulées
                });
            }])
            ->orderByDesc('total_sales') // Trie par les ventes totales
            ->limit(5) // Limite aux 5 meilleurs produits
            ->get()
            ->map(function ($product) {
                return [
                    'name' => isset($product->name) && is_string($product->name) ? $product->name : 'N/A',
                    'price' => isset($product->price) ? $product->price : 0,
                    'sales' => isset($product->total_sales) ? $product->total_sales : 0,
                ];
            })
            ->toArray();
        
            // Commandes récentes (Recent Orders)
            $this->recentOrdersHeaders = [
                ['key' => 'id', 'label' => '#'],
                ['key' => 'date', 'label' => 'Date'],
                ['key' => 'customer', 'label' => 'Client'],
                ['key' => 'total', 'label' => 'Total'],
                ['key' => 'status', 'label' => 'Statut'],
            ];
    
            $this->topCustomersRows = User::where('role', 'customer')
            ->withSum(['orders as orders_sum_total_price' => function ($query) {
                $query->whereNotIn('status', ['annulé']);
            }], 'total_price')
            ->orderByDesc('orders_sum_total_price')
            ->limit(5)
            ->get()
            ->toArray();

        
    
            // Catégories de produits (Product Categories Chart - Doughnut)
            $categories = Category::withCount('products')->get();
            $this->productCategoryChart = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => $categories->pluck('name')->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Produits par catégorie',
                            'data' => $categories->pluck('products_count')->toArray(),
                        ]
                    ]
                ]
            ];
    
            // Revenus bruts (Gross Revenue Chart - Line)
            $grossRevenueData = Order::where('status', '!=', 'cancelled') // Exclut les commandes annulées
            ->get()
            ->groupBy(function ($order) {
                // Regroupe les commandes par année et mois
                return $order->created_at->format('Y-m');
            })
            ->mapWithKeys(function ($orders, $monthYear) {
                // Crée une date Carbon pour extraire mois et année
                $date = Carbon::createFromFormat('Y-m', $monthYear);
                $monthName = $date->format('M'); // Mois en texte (ex: Jan)
                $year = $date->format('Y');     // Année (ex: 2024)
        
                // Calcule la somme des prix pour le groupe
                $revenue = $orders->sum('total_price');
        
                // Retourne sous forme de clé-valeur
                return ["$monthName $year" => floatval($revenue)];
            });
    
            $this->grossRevenueChart = [
                'type' => 'line',
                'data' => [
                    'labels' => $grossRevenueData->keys()->toArray(),
                    'datasets' => [
                        [
                            'label' => 'Revenus (€)',
                            'data' => $grossRevenueData->values()->toArray(),
                        ]
                    ]
                ]
            ];
        } catch (\Exception $e) {
            // Handle exceptions and optionally show an error toast
            $this->error(
                'Erreur', // Changed to positional arguments
                'Une erreur est survenue lors du chargement du tableau de bord.'
            );
    
            // Optionally log the error for debugging
            \Log::error('Dashboard mount error: ' . $e->getMessage());
        }
    }
    
    

    /**
     * Renders the Dashboard view with all necessary data.
     */
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
