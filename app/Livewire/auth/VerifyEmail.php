<?php
namespace App\Livewire\Auth;

use Livewire\Component;

class VerifyEmail extends Component
{
    public function resend()
    {
        if (cache()->has('resent-'.auth()->id())) {
            $this->addError('resend', 'Please wait 60 seconds before resending');
            return;
        }
    
        cache()->put('resent-'.auth()->id(), true, now()->addMinute());
        
        auth()->user()->sendEmailVerificationNotification();
        session()->flash('status', 'Verification link resent!');
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}