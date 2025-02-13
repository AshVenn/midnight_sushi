<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';

    public function register()
    {
        // Validate input fields
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8',
        ]);

        // Create a new user with the 'customer' role by default
        User::create([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'password'   => Hash::make($this->password),
            'role'       => 'customer',
        ]);

        session()->flash('success', 'Account successfully created!');
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
