<?php

namespace App\Livewire\Manager\Components\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';

    public function search(): void
    {
        $this->resetPage();
    }

    public function users()
    {
        return User::whereNotIn('role', ['admin', 'manager']) // Restrict access to other managers/admins
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.manager.components.users.index', [
            'users' => $this->users()
        ]);
    }
}
