<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class Orders extends Component
{
    /**
     * Retrieve the current user's orders.
     * 
     * We load orderItems->orderable if needed 
     * (like a product, menu, etc.)
     */
    public function getOrdersProperty()
    {
        // If you want the most recent first, orderBy('created_at', 'desc')
        return Order::where('customer_id', Auth::id())
        ->orderBy('created_at', 'asc')
        ->with('orderItems.orderable')
        ->get();
    }

    public function render()
    {
        return view('livewire.customer.orders', [
            'orders' => $this->orders // using the getOrdersProperty() "magic" accessor
        ]);
    }
}
