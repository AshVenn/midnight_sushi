<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class Orders extends Component
{
    use WithPagination; // Enable pagination

    public $filterStatus = ''; // Stores the selected status

    /**
     * Grab the current user's orders, optionally filtered by status.
     */
    public function getOrdersProperty()
    {
        $query = Order::where('customer_id', Auth::id())
            ->with(['orderItems.orderable', 'deliveries'])
            ->orderBy('created_at', 'desc');

      
        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(5); 
    }

    public function render()
    {
        return view('livewire.customer.orders', [
            'orders' => $this->orders, 
        ]);
    }
}
