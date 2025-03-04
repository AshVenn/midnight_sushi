<?php

namespace App\Livewire\Manager\Components\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Create extends Component
{
    use WithFileUploads, Toast;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $phone_number = '';
    public $avatar;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'phone_number' => 'nullable|string|max:15|unique:users,phone_number',
        'avatar' => 'nullable|image|max:2048',
    ];

    public function save()
    {
        $this->validate();

        try {
            $authenticatedUser = Auth::user();
            if (!$authenticatedUser || $authenticatedUser->role !== 'admin') {
                $this->error(
                    title: 'Accès refusé',
                    description: 'Seuls les administrateurs peuvent créer des managers.'
                );
                return;
            }

            $avatarUrl = $this->avatar
                ? Cloudinary::upload($this->avatar->getRealPath())->getSecurePath()
                : null;

            User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone_number' => $this->phone_number,
                'avatar' => $avatarUrl,
                'role' => 'manager',
            ]);

            $this->reset();
            $this->success(
                title: 'Manager ajouté',
                description: 'Le manager a été créé avec succès.',
                redirectTo: route('manager.users.index')
            );
        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.manager.components.users.create');
    }
}
