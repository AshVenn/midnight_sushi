<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;
use Mary\Traits\Toast;

class VerifyEmail extends Component
{
    use Toast;

    public $remainingSeconds = 0;

    public function resend()
    {
        $key = 'email-verification:' . auth()->id();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->remainingSeconds = RateLimiter::availableIn($key);
            
            $this->error(
                title: 'Trop de tentatives',
                description: "Veuillez patienter avant de renvoyer. Temps restant: {$this->formatCountdown()}",
                redirectTo: '/email/verify'
            );
            return;
        }


        RateLimiter::hit($key, 86400);

        auth()->user()->sendEmailVerificationNotification();

        $this->success(
            title: 'Email renvoyé',
            description: 'Un nouveau lien de vérification a été envoyé à votre adresse email.',
            redirectTo: '/email/verify'
        );
    }

    private function formatCountdown(): string 
    {
        $hours = floor($this->remainingSeconds / 3600);
        $minutes = floor(($this->remainingSeconds % 3600) / 60);
        $seconds = $this->remainingSeconds % 60;

        return sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}