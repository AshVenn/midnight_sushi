<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|string|in:admin,manager,customer,delivery_person',
    ];

    public function save(): void
    {
        $this->validate();

        User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        // Show toast notification and redirect
        $this->success(
            title: 'User Created',
            description: 'The new user has been added successfully.',
            redirectTo: route('admin.users.index') // Redirect after toast
        );
    }

    public function render()
    {
        return view('livewire.admin.components.users.create');
    }
}
