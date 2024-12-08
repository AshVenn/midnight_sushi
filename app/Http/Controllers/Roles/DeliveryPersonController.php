<?php

namespace App\Http\Controllers\Roles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryPersonController extends Controller
{
    public function dashboard()
    {
        return view('livewire.delivery-person.dashboard');
    }
}
