<?php

namespace App\Http\Controllers\Roles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function dashboard()
    {
        return view('livewire.customer.dashboard');
    }
}
