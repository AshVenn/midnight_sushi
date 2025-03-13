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
        $credentials = [
            'email'    => $this->email,
            'password' => $this->password,
        ];

        // 1) Attempt login
        if (!Auth::attempt($credentials)) {
            $this->addError('email', 'Invalid email or password.');
            return;
        }

        // 2) Get the authenticated user
        $user = Auth::user();

        // 3) Check if user’s email is verified
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            session()->flash('error', 'Please verify your email first.');
            return redirect()->route('verification.notice');
        }

        // 4) Regenerate session after successful login
        session()->regenerate();

        // 5) Role-based redirect
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

    public function render()
    {
        return view('livewire.auth.login');
    }
}
