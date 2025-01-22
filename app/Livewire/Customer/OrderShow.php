<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderShow extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Ensure this order belongs to the logged in user
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Eager load items
        $this->order->load(['orderItems.orderable']);
    }

    public function render()
    {
        return view('livewire.customer.order-show', [
            'order' => $this->order,
        ]);
    }
}
