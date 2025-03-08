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

    public $searchTerm = '';
    public $selectedAllergen = '';
    protected $queryString = ['searchTerm', 'selectedAllergen'];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingSelectedAllergen()
    {
        $this->resetPage();
    }

    public function showMenuDetails($menuId)
    {
        $this->dispatch('openMenuModal', $menuId);
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
                'id' => $key->id,
                'name' => $key->name,
                'price' => $type === 'menu' ? $key->price : $key->final_price,
                'image' => $key->image,
                'quantity' => 1 // Add quantity field
            ];
        }
    
        Session::put('cart', $cart);
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        $menus = Menu::with(['products.category', 'reviews'])
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', "%{$this->searchTerm}%")
                      ->orWhere('description', 'like', "%{$this->searchTerm}%");
            })
            ->paginate(6, ['*'], 'menuPage');

        $products = Product::with(['category', 'reviews'])
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', "%{$this->searchTerm}%")
                      ->orWhere('description', 'like', "%{$this->searchTerm}%");
            })
            ->when($this->selectedAllergen, function ($query) {
                $query->whereJsonContains('allergens', $this->selectedAllergen);
            })
            ->where('is_available', true)
            ->paginate(8, ['*'], 'productPage');

        return view('livewire.menus-products', [
            'menus' => $menus,
            'products' => $products
        ]);
    }
}