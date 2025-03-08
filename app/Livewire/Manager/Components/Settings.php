<?php

namespace App\Livewire\Manager\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Mary\Traits\Toast; 

class Settings extends Component
{
    use WithFileUploads, Toast; 

    public string $first_name;
    public string $last_name;
    public string $email;
    public $avatar;
    public ?string $oldAvatarUrl = null;
    public string $password = '';
    public string $confirm_password = '';

    protected function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore(Auth::id())],
            'avatar'     => 'nullable|image|max:2048',
            'password'   => 'nullable|string|min:6|same:confirm_password',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name  = $user->last_name;
        $this->email      = $user->email;
        $this->oldAvatarUrl = $user->avatar;
    }

    public function save()
    {
        $this->validate();

        try {
            $user = Auth::user();

            
            if ($this->avatar) {
                if ($this->oldAvatarUrl) {
                    $publicId = $this->getPublicIdFromUrl($this->oldAvatarUrl);
                    if ($publicId) {
                        Cloudinary::destroy($publicId);
                    }
                }
                $this->oldAvatarUrl = Cloudinary::upload($this->avatar->getRealPath())->getSecurePath();
            }

            
            $user->update([
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
                'avatar'     => $this->oldAvatarUrl,
                'password'   => $this->password ? Hash::make($this->password) : $user->password,
            ]);

        
            $this->success(
                title: 'Profil mis à jour',
                description: 'Votre profil a été mis à jour avec succès.'
            );

        } catch (\Exception $e) {
        
            Log::error('Erreur lors de la mise à jour du profil : ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

        
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue : ' . $e->getMessage()
            );
        }
    }

    private function getPublicIdFromUrl(string $url): ?string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        $filename = pathinfo($path, PATHINFO_FILENAME);
        
        return $filename ? 'midnight_sushi/' . $filename : null;
    }

    public function render()
    {
        return view('livewire.manager.components.settings');
    }
}
