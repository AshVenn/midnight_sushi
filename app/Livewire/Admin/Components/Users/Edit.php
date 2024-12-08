<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Livewire\Component;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast;

    public User $user;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $role;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->role;
    }
    

    /**
     * Dynamic validation rules.
     */
    protected function rules()
    {
        return [
            'user.first_name' => 'required|string|max:255',
            'user.last_name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email,' . $this->user->id,
            'user.role' => 'required|in:admin,customer,manager,delivery_person',
        ];
    }

    /**
     * Save the user data.
     */
    public function save(): void
    {
        $this->validate($this->rules());
    
        // Synchronize the updated data with the user model
        $this->user->first_name = $this->first_name;
        $this->user->last_name = $this->last_name;
        $this->user->email = $this->email;
        $this->user->role = $this->role;
    
        // Save the updated user model
        $this->user->save();
    
        // Show a success toast and redirect
        $this->success(
            title: 'User Updated',
            description: 'The user details have been updated successfully.',
            redirectTo: route('admin.users.index')
        );
    }
    

    public function render()
    {
        return view('livewire.admin.components.users.edit');
    }
}
