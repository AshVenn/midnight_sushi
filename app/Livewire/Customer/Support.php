<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ContactInquiry;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class Support extends Component
{
    use Toast, WithPagination; // Add pagination trait

    public $subject;
    public $message;

    public function render()
    {
        $userEmail = Auth::user()->email ?? 'guest@example.com';
        $inquiries = ContactInquiry::where('email', $userEmail)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(5); // Add pagination

        return view('livewire.customer.support', compact('inquiries'));
    }

    protected function rules()
    {
        return [
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ];
    }

    public function submitInquiry()
    {
        $this->validate();

        $user = Auth::user();

        ContactInquiry::create([
            'name'    => $user ? $user->full_name : 'Guest',
            'email'   => $user ? $user->email : 'guest@example.com',
            'subject' => $this->subject,
            'message' => $this->message,
            'status'  => 'open',
        ]);

        $this->reset(['subject', 'message']);

        $this->success(
            title: 'Support',
            description: 'Votre demande a été soumise avec succès.'
        );
    }
}
