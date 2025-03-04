<?php

namespace App\Livewire\Manager\Components\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Edit extends Component
{
    use WithFileUploads, Toast;

    public User $user;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone_number;
    public $avatar;
    public ?string $oldAvatarUrl = null;

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number,' . $this->user->id,
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    public function mount(User $user)
    {
        $this->user = $user;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->oldAvatarUrl = $user->avatar;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->avatar) {
                if ($this->oldAvatarUrl) {
                    $publicId = pathinfo($this->oldAvatarUrl, PATHINFO_FILENAME);
                    Cloudinary::destroy($publicId);
                }
                $this->oldAvatarUrl = Cloudinary::upload($this->avatar->getRealPath())->getSecurePath();
            }

            $this->user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'avatar' => $this->oldAvatarUrl,
            ]);

            $this->success(
                title: 'Manager mis à jour',
                description: 'Les informations ont été mises à jour avec succès.',
                redirectTo: route('manager.users.index')
            );
        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.manager.components.users.edit');
    }
}
