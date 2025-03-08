<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Mary\Traits\Toast;
use Livewire\Attributes\On;

class Cart extends Component
{
    
    use Toast;

    public $cart = [];
    public $totalItems = 0;
    public $totalPrice = 0;

    public function mount()
    {
        $this->updateCart();
    }

    #[On('cartUpdated')]
    public function handleCartUpdated()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        $this->cart = Session::get('cart', []);
        $this->calculateTotals();
    }

    public function removeItem($type, $id)
    {
        if(isset($this->cart[$type][$id])){
            unset($this->cart[$type][$id]);
            Session::put('cart', $this->cart);
            $this->calculateTotals();
            $this->success('Élément supprimé du panier.');
            $this->dispatch('cartUpdated'); // Dispatch de l'événement pour notifier le panier
        }
    }

    public function clearCart()
    {
        Session::forget('cart');
        $this->cart = [];
        $this->totalItems = 0;
        $this->totalPrice = 0;
        $this->success('Panier vidé.');
        $this->dispatch('cartUpdated'); // Dispatch de l'événement pour notifier le panier
    }

    public function proceedToCheckout()
    {
        return redirect()->route('checkout');
    }

    private function calculateTotals()
    {
        $this->totalItems = 0;
        $this->totalPrice = 0;

        foreach($this->cart as $type => $items){
            foreach($items as $item){
                $this->totalItems += $item['quantity'];
                $this->totalPrice += $item['price'] * $item['quantity'];
            }
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
