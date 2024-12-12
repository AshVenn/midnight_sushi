<?php

namespace App\Livewire\Auth;

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

            $user = Auth::user();

            switch ($user->role) {
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'customer':
                    return redirect()->intended('/customer/dashboard');
                case 'manager':
                    return redirect()->intended('/manager/dashboard');
                case 'delivery_person':
                    return redirect()->intended('/deliveryperson/dashboard');
                default:
                    return redirect()->intended('/dashboard');
            }
        }

        $this->addError('email', 'Invalid email or password.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
