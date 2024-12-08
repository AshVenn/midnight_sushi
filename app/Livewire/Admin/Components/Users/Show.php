<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    public function render()
    {
        return view('livewire.admin.components.users.show');
    }
}
