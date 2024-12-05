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
        try {
            $credentials = ['email' => $this->email, 'password' => $this->password];
    
            if (Auth::attempt($credentials)) {
                session()->regenerate();
    
                // Get the authenticated user
                $user = Auth::user();
    
                // Redirect based on the user's role
                switch ($user->role) {
                    case 'admin':
                        return redirect()->intended('/admin/dashboard');
                    case 'customer':
                        return redirect()->intended('/customer/dashboard');
                    case 'manager':
                        return redirect()->intended('/manager/dashboard');
                    case 'delivery_person':
                        return redirect()->intended('/delivery-person/dashboard');
                    default:
                        return redirect()->intended('/dashboard'); // Fallback for unknown roles
                }
            }
    
            $this->addError('email', 'Invalid email or password.');
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            $this->addError('email', 'Something went wrong. Please try again later.');
        }
    }
    
    

    public function render()
    {
        return view('livewire.auth.login');
    }
    
}
