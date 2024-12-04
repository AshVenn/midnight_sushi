<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function login()
    {
        $credentials = ['email' => $this->email, 'password' => $this->password];

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'Invalid email or password.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
