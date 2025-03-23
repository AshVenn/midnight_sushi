<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Login extends Component
{
    use Toast;

    public string $email = '';
    public string $password = '';

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (!Auth::attempt($credentials)) {
            $this->error(
                'Email ou mot de passe incorrect', 
                redirectTo: '/login'
            );
            return;
        }

        $user = Auth::user();

        // Check banned status
        if ($user->status === 'banned') {
            Auth::logout();
            $this->error(
                'Votre compte a été suspendu', 
                redirectTo: '/login'
            );
            return;
        }

        // Email verification check
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            $this->error(
                'Veuillez vérifier votre email avant de continuer',
                redirectTo: route('verification.notice')
            );
            return;
        }

        session()->regenerate();

        // Role-based redirect with success toast
        $this->success(
            'Connexion réussie!', 
            redirectTo: match($user->role) {
                'admin' => '/admin/dashboard',
                'customer' => '/customer/dashboard',
                'manager' => '/manager/dashboard',
                'delivery_person' => '/deliveryperson/dashboard',
                default => '/dashboard'
            }
        );
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}