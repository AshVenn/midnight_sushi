<?php

namespace App\Livewire\Checkout;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Session;

class Success extends Component
{
    public Order $order;

    /**
     * Mount receives the order model from the route parameter.
     * Example route definition: '/checkout/success/{order}'
     */
    public function mount(Order $order)
    {
        // Optional: If you want to clear the cart upon success,
        // this is a good place to do it.
        Session::forget('cart');
    }

    public function render()
    {
        return view('livewire.checkout.success', [
            'order' => $this->order,
        ]);
    }
}
