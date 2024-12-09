<?php

namespace App\Livewire\Admin\Components\Users;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Create extends Component
{
    use WithFileUploads;
    use Toast;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'customer';
    public $avatar; 

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,customer,manager,delivery_person',
        'avatar' => 'nullable|image|max:2048', 
    ];

    public function save()
    {
        $this->validate();

        try {
            // Upload avatar to Cloudinary if provided
            $avatarUrl = $this->avatar
                ? Cloudinary::upload($this->avatar->getRealPath())->getSecurePath()
                : null;

            // Create the user
            User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'avatar' => $avatarUrl,
            ]);

            // Reset the form
            $this->reset(['first_name', 'last_name', 'email', 'password', 'role', 'avatar']);

            // Show success message and redirect
            $this->success(
                title: 'User Created Successfully',
                description: 'The User has been created successfully.',
                redirectTo: route('admin.users.index')
            );
        } catch (\Exception $e) {
            $this->error('Error' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.components.users.create');
    }
}
