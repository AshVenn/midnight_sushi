<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public function render()
    {
        return view('livewire.customer.profile', [
            'user' => Auth::user()
        ]);
    }
}