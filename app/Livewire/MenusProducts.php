<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class MenusProducts extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $selectedAllergen = '';
    public $showMenuDrawer = false;
    public $showProductDrawer = false;
    public $selectedMenu;
    public $selectedProduct;
    protected $queryString = ['searchTerm', 'selectedAllergen'];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function showMenuDetails($menuId)
    {
        $this->selectedMenu = Menu::with(['products.category', 'reviews.user'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->find($menuId);
        $this->showMenuDrawer = true;
    }
    
    public function showProductDetails($productId)
    {
        $this->selectedProduct = Product::with(['category', 'reviews.user'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->find($productId);
        $this->showProductDrawer = true;
    }

    public function addToCart($type, $id)
    {
        $cart = Session::get('cart', []);
        $key = $type === 'menu' ? Menu::find($id) : Product::find($id);
    
        if (!$key) return;
    
        // Initialize type array if not exists
        if (!isset($cart[$type])) {
            $cart[$type] = [];
        }
    
        // Check if item already exists in cart
        if (isset($cart[$type][$id])) {
            $cart[$type][$id]['quantity']++;
        } else {
            $cart[$type][$id] = [
                'id'       => $key->id,
                'name'     => $key->name,
                'price'    => $type === 'menu' ? $key->price : $key->final_price,
                'image'    => $key->image,
                'quantity' => 1 
            ];
        }
    
        Session::put('cart', $cart);
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        // On peut aussi mettre en cache la liste des allergènes
        $allAllergens = Cache::remember('allAllergens', 600, function () {
            return Product::whereNotNull('allergens')
                ->pluck('allergens')
                ->flatten()
                ->unique()
                ->values()
                ->map(fn($a) => ['id' => $a, 'name' => "Sans $a"])
                ->prepend(['id' => '', 'name' => 'Tous les allergènes'])
                ->toArray();
        });

        // Génération d'une clé de cache pour les menus
        $menuPage = request('menuPage', 1);
        $menusCacheKey = "menus:search={$this->searchTerm}:allergen={$this->selectedAllergen}:page={$menuPage}";
        $menus = Cache::remember($menusCacheKey, 600, function () {
            return Menu::with(['products.category', 'reviews.user'])
                ->withAvg('reviews as reviews_avg_rating', 'rating')
                ->when($this->searchTerm, function ($query) {
                    $query->where('name', 'like', "%{$this->searchTerm}%")
                          ->orWhere('description', 'like', "%{$this->searchTerm}%");
                })
                ->when($this->selectedAllergen, function ($query) {
                    $query->whereHas('products', function($q) {
                        $q->whereJsonContains('allergens', $this->selectedAllergen);
                    });
                })
                ->paginate(6, ['*'], 'menuPage');
        });

        // Génération d'une clé de cache pour les produits
        $productPage = request('productPage', 1);
        $productsCacheKey = "products:search={$this->searchTerm}:allergen={$this->selectedAllergen}:page={$productPage}";
        $products = Cache::remember($productsCacheKey, 600, function () {
            return Product::with(['category', 'reviews.user'])
                ->withAvg('reviews as reviews_avg_rating', 'rating')
                ->when($this->searchTerm, function ($query) {
                    $query->where('name', 'like', "%{$this->searchTerm}%")
                          ->orWhere('description', 'like', "%{$this->searchTerm}%");
                })
                ->when($this->selectedAllergen, function ($query) {
                    $query->whereJsonContains('allergens', $this->selectedAllergen);
                })
                ->where('is_available', true)
                ->paginate(8, ['*'], 'productPage');
        });

        return view('livewire.menus-products', [
            'menus'        => $menus,
            'products'     => $products,
            'allAllergens' => $allAllergens,
        ]);
    }
}
