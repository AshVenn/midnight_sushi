<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\Menu;
use App\Models\Product;

class MenusProducts extends Component
{
    public $menus = [];
    public $products = [];

    /**
     * Load menus and products on mount.
     */
    public function mount()
    {
        $this->menus = Menu::with('products')->get();
        $this->products = Product::with('category')->where('is_available', true)->get();
    }

    public function render()
    {
        return view('livewire.menus-products');
    }

    /**
     * 1) "Détails": dispatch event to open the menu modal in the other component.
     */
    public function showMenuDetails($menuId)
    {
        // Émet l'événement avec le nom attendu par MenuDetailsModal
        $this->dispatch('openMenuModal', $menuId);
    }

    /**
     * 2) "Ajouter": add either a menu or product to the cart (example).
     */
    public function addToCart($type, $id)
    {
        // Example cart logic (not mandatory; adapt to your app)
        $cart = Session::get('cart', []);

        if ($type === 'menu') {
            $item = Menu::find($id);
            if ($item) {
                if (isset($cart[$type][$id])) {
                    $cart[$type][$id]['quantity']++;
                } else {
                    $cart[$type][$id] = [
                        'id'       => $item->id,
                        'name'     => $item->name,
                        'price'    => $item->price,
                        'quantity' => 1,
                        'image'    => $item->image,
                    ];
                }
            }
        } elseif ($type === 'product') {
            $item = Product::find($id);
            if ($item) {
                if (isset($cart[$type][$id])) {
                    $cart[$type][$id]['quantity']++;
                } else {
                    $cart[$type][$id] = [
                        'id'       => $item->id,
                        'name'     => $item->name,
                        'price'    => $item->final_price,
                        'quantity' => 1,
                        'image'    => $item->image,
                    ];
                }
            }
        }

        Session::put('cart', $cart);

        $this->dispatch('cartUpdated')->to('cart');
    }
}
