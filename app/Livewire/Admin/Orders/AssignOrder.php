<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use App\Models\Order;
use App\Models\User;

class AssignOrder extends Component
{
    public $order;
    public $deliveryPersonId;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function assignOrder()
    {
        $this->validate([
            'deliveryPersonId' => 'required|exists:users,id',
        ]);

        $this->order->update([
            'delivery_person_id' => $this->deliveryPersonId,
            'status' => 'assigned',
        ]);

        session()->flash('success', 'Order assigned successfully.');
        return redirect()->route('admin.orders.index');
    }

    public function render()
    {
        return view('livewire.admin.orders.assign-order', [
            'deliveryPersons' => User::where('role', 'delivery_person')->get(),
        ]);
    }
}
