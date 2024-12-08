<?php

namespace App\Http\Controllers\Roles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManagerController extends Controller
{
    public function dashboard()
    {
        return view('livewire.manager.dashboard');
    }
}
