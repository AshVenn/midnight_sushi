<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Address;

class Addresses extends Component
{
    public function render()
    {
        return view('livewire.customer.addresses', [
            'addresses' => auth()->user()->addresses()->latest()->paginate(5)
        ]);
    }
}