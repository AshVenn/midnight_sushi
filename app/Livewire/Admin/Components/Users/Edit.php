<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Edit extends Component
{
    use WithFileUploads;
    use Toast;

    public User $user;
    public $avatar;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $role;
    public ?string $oldAvatarUrl = null;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . 'user.id',
        'role' => 'required|in:admin,customer,manager,delivery_person',
        'avatar' => 'nullable|image|max:2048',
    ];

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->oldAvatarUrl = $user->avatar;
    }

    public function save()
    {
        $this->validate();

        // Handle avatar replacement
        if ($this->avatar) {
            if ($this->oldAvatarUrl) {
                $publicId = $this->getPublicIdFromUrl($this->oldAvatarUrl);
                Cloudinary::destroy($publicId);
            }

            $this->oldAvatarUrl = Cloudinary::upload($this->avatar->getRealPath())->getSecurePath();
        }

        $this->user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->oldAvatarUrl,
        ]);

        $this->success(
            title: 'User updated Successfully',
            description: 'The User has been updated successfully.',
            redirectTo: route('admin.users.index')
        );
    }

    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function render()
    {
        return view('livewire.admin.components.users.edit');
    }
}