<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use Illuminate\Support\Facades\Session;
use Mary\Traits\Toast;

class MenuDetailsModal extends Component
{
    use Toast;
    public ?Menu $menu = null;
    public bool $show = false;

    protected $listeners = ['openMenuModal' => 'loadMenu'];

    public function loadMenu($menuId)
    {
        $this->menu = Menu::with(['products', 'reviews'])->find($menuId);
        $this->show = true;
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
        $this->success('Élément ajouté du panier.');
        $this->dispatch('cartUpdated');
    }


    public function close()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.menu-details-modal');
    }
}