<?php

namespace App\Livewire\Admin\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Settings extends Component
{
    use WithFileUploads;

    public string $first_name; // Prénom de l'utilisateur
    public string $last_name; // Nom de famille de l'utilisateur
    public string $email; // Email de l'utilisateur
    public $avatar; // Pour les fichiers d'avatar
    public ?string $oldAvatarUrl = null; // URL actuelle de l'avatar
    public string $password = ''; // Champ pour le mot de passe
    public string $confirm_password = ''; // Champ pour la confirmation du mot de passe

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . 'user_id', // Validation pour l'unicité de l'email
        'avatar' => 'nullable|image|max:2048', // Avatar facultatif avec une taille maximale de 2 Mo
        'password' => 'nullable|string|min:6|same:confirm_password', // Validation pour les mots de passe
    ];

    /**
     * Méthode appelée au montage du composant.
     * Initialise les champs avec les données de l'utilisateur authentifié.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->oldAvatarUrl = $user->avatar;
    }

    /**
     * Sauvegarder les modifications dans les paramètres de l'utilisateur.
     */
    public function save()
    {
        $this->validate();

        try {
            $user = Auth::user();

            // Gestion du remplacement de l'avatar
            if ($this->avatar) {
                if ($this->oldAvatarUrl) {
                    $publicId = $this->getPublicIdFromUrl($this->oldAvatarUrl);
                    Cloudinary::destroy($publicId);
                }
                $this->oldAvatarUrl = Cloudinary::upload($this->avatar->getRealPath())->getSecurePath();
            }

            // Mise à jour des données de l'utilisateur
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'avatar' => $this->oldAvatarUrl,
                'password' => $this->password ? Hash::make($this->password) : $user->password,
            ]);

            // Afficher un message de succès
            $this->success(
                title: 'Profil mis à jour',
                description: 'Votre profil a été mis à jour avec succès.'
            );
        } catch (\Exception $e) {
            // Afficher un message d'erreur
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue : ' . $e->getMessage()
            );
        }
    }

    /**
     * Extraire l'ID public de l'URL Cloudinary.
     */
    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Rendre la vue du composant Livewire.
     */
    public function render()
    {
        return view('livewire.admin.components.settings');
    }
}
