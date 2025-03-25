<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class MenusProducts extends Component
{
    use WithPagination;

    // Livewire will track these in the query string if you want:
    public $searchTerm = '';
    public $selectedAllergen = '';

    // For drawers
    public $showMenuDrawer = false;
    public $showProductDrawer = false;
    public $selectedMenu;
    public $selectedProduct;

    // If you want the search term & allergen to appear in the URL,
    // you can keep these in the query string:
    protected $queryString = ['searchTerm', 'selectedAllergen'];

    // Livewire auto-resets pagination when you change these props:
    public function updatingSearchTerm()
    {
        $this->resetPage(); // ensures you go back to page 1
    }
    public function updatingSelectedAllergen()
    {
        $this->resetPage(); 
    }

    // Show one menu's details in a drawer
    public function showMenuDetails($menuId)
    {
        $this->selectedMenu = Menu::with(['products.category', 'reviews.user'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->find($menuId);

        $this->showMenuDrawer = true;
    }

    // Show one product's details in a drawer
    public function showProductDetails($productId)
    {
        $this->selectedProduct = Product::with(['category', 'reviews.user'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->find($productId);

        $this->showProductDrawer = true;
    }

    // Add either a Menu or Product to the cart session
    public function addToCart($type, $id)
    {
        $cart = Session::get('cart', []);

        // fetch either Menu or Product by ID
        $model = $type === 'menu' 
            ? Menu::find($id) 
            : Product::find($id);

        // if not found, do nothing
        if (!$model) return;

        // ensure the sub-array exists
        if (!isset($cart[$type])) {
            $cart[$type] = [];
        }

        // if item already in cart, increment; otherwise create
        if (isset($cart[$type][$id])) {
            $cart[$type][$id]['quantity']++;
        } else {
            $cart[$type][$id] = [
                'id'       => $model->id,
                'name'     => $model->name,
                'price'    => ($type === 'menu') 
                    ? $model->price 
                    : $model->final_price, // handle discount logic
                'image'    => $model->image,
                'quantity' => 1,
            ];
        }

        Session::put('cart', $cart);

        // if you have a Livewire listener in your layout
        // you can dispatch an event:
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        // 1) Menus, each page has 6 items. 
        //    We specify the custom page name 'menusPage' 
        //    to keep it separate from products.
        $menus = Menu::with(['products.category', 'reviews.user'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->searchTerm}%")
                      ->orWhere('description', 'like', "%{$this->searchTerm}%");
                });
            })
            ->when($this->selectedAllergen, function ($query) {
                $query->whereHas('products', function ($q) {
                    $q->whereJsonContains('allergens', $this->selectedAllergen);
                });
            })
            ->paginate(6, ['*'], 'menusPage');

        // 2) Products, each page has 8 items. 
        //    We specify the custom page name 'productsPage' 
        //    so menus/products pagination don’t conflict.
        $products = Product::with(['category', 'reviews.user'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->searchTerm}%")
                      ->orWhere('description', 'like', "%{$this->searchTerm}%");
                });
            })
            ->when($this->selectedAllergen, function ($query) {
                $query->whereJsonContains('allergens', $this->selectedAllergen);
            })
            ->where('is_available', true)
            ->paginate(8, ['*'], 'productsPage');

        // 3) Build a list of allergens (optional). Just an example:
        //    If you do want caching, you can add Cache::remember() around it.
        $allAllergens = Product::whereNotNull('allergens')
            ->pluck('allergens')
            ->flatten()
            ->unique()
            ->values()
            ->map(fn($a) => ['id' => $a, 'name' => "Sans $a"])
            ->prepend(['id' => '', 'name' => 'Tous les allergènes'])
            ->toArray();

        return view('livewire.menus-products', [
            'menus'        => $menus,
            'products'     => $products,
            'allAllergens' => $allAllergens,
        ]);
    }
}
