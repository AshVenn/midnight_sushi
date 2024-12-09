<?php

namespace App\Livewire\Admin\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Settings extends Component
{
    use WithFileUploads;
    use Toast;

    public string $first_name;
    public string $last_name;
    public string $email;
    public $avatar; // For avatar file upload
    public ?string $oldAvatarUrl = null; // For current avatar URL
    public string $password = '';
    public string $confirm_password = '';

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . 'user_id', // Replace dynamically in mount
        'avatar' => 'nullable|image|max:2048',
        'password' => 'nullable|string|min:6|same:confirm_password',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->oldAvatarUrl = $user->avatar;
    }

    public function save()
    {
        $this->validate();

        try {
            $user = Auth::user();

            // Handle avatar update
            if ($this->avatar) {
                if ($this->oldAvatarUrl) {
                    $publicId = $this->getPublicIdFromUrl($this->oldAvatarUrl);
                    Cloudinary::destroy($publicId);
                }
                $this->oldAvatarUrl = Cloudinary::upload($this->avatar->getRealPath())->getSecurePath();
            }

            // Update user data
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'avatar' => $this->oldAvatarUrl,
                'password' => $this->password ? Hash::make($this->password) : $user->password,
            ]);

            // Show success toast
            $this->success(
                title: 'Profile Updated',
                description: 'Your profile has been updated successfully.',
            );
        } catch (\Exception $e) {
            // Show error toast
            $this->error(
                title: 'Error',
                description: 'Something went wrong: ' . $e->getMessage()
            );
        }
    }

    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function render()
    {
        return view('livewire.admin.components.settings');
    }
}
