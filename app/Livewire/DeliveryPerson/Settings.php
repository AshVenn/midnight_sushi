<?php

namespace App\Livewire\DeliveryPerson;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;
class Settings extends Component
{
    use WithFileUploads, Toast;

    public string $first_name;
    public string $last_name;
    public string $email;

    // Change from `public string $phone_number;` to a nullable type:
    public ?string $phone_number = null;

    public $avatar;
    public ?string $oldAvatarUrl = null;
    public string $password = '';
    public string $confirm_password = '';

    protected $rules = [
        'first_name'       => 'required|string|max:255',
        'last_name'        => 'required|string|max:255',
        'email'            => 'required|email|unique:users,email,' . 'user_id',
        'phone_number'     => 'required|string|max:15',
        'avatar'           => 'nullable|image|max:2048',
        'password'         => 'nullable|string|min:6|same:confirm_password',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        $this->first_name    = $user->first_name;
        $this->last_name     = $user->last_name;
        $this->email         = $user->email;

        $this->phone_number  = $user->phone_number;

        $this->oldAvatarUrl  = $user->avatar;
    }

    public function save()
    {
        $this->validate();

        try {
            $user = Auth::user();

            if ($this->avatar) {
                if ($this->oldAvatarUrl) {
                    $publicId = $this->getPublicIdFromUrl($this->oldAvatarUrl);
                    Cloudinary::destroy($publicId);
                }
                $this->oldAvatarUrl = Cloudinary::upload(
                    $this->avatar->getRealPath()
                )->getSecurePath();
            }

            $user->update([
                'first_name'   => $this->first_name,
                'last_name'    => $this->last_name,
                'email'        => $this->email,
                'phone_number' => $this->phone_number,
                'avatar'       => $this->oldAvatarUrl,
                'password'     => $this->password
                    ? Hash::make($this->password)
                    : $user->password,
            ]);

            $this->success(
                title: 'Profil mis à jour',
                description: 'Votre profil a été mis à jour avec succès.'
            );
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue : ' . $e->getMessage()
            );
        }
    }

    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path     = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function render()
    {
        return view('livewire.delivery-person.settings');
    }
}
