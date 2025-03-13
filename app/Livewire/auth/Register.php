<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mary\Traits\Toast; 
use Illuminate\Support\Facades\Auth;

class Register extends Component
{
    use Toast; 

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $honeypot = '';

    public function register()
    {
        // Protection anti-spam : si le champ honeypot est rempli, bloquer l'inscription
        if (!empty($this->honeypot)) {
            Log::warning('Bot détecté lors de l\'inscription.', ['email' => $this->email]);

            $this->error(
                title: 'Tentative bloquée',
                description: 'Votre inscription a échoué. Veuillez réessayer.'
            );

            return;
        }


        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8',
        ]);

        
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'password'   => Hash::make($this->password),
            'role'       => 'customer',
        ]);

        Auth::login($user);
        $user->sendEmailVerificationNotification();

        $this->success(
            title: 'Inscription réussie',
            description: 'Un lien de vérification a été envoyé à votre adresse email.'
        );

        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
