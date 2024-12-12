<?php

namespace App\Livewire\Admin\Components\Users;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Méthode appelée au montage du composant.
     * Initialise les champs avec les données de l'utilisateur.
     */
    public function mount(User $user): void
    {
        $this->user = $user;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->oldAvatarUrl = $user->avatar;
    }

    /**
     * Sauvegarde les modifications apportées à l'utilisateur.
     */
    public function save()
    {
        $this->validate();

        try {
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser) {
                $this->error(
                    title: 'Erreur',
                    description: 'Vous devez être authentifié pour effectuer cette action.'
                );
                return;
            }

            // Gestion du remplacement de l'avatar
            if ($this->avatar) {
                if ($this->oldAvatarUrl) {
                    $publicId = $this->getPublicIdFromUrl($this->oldAvatarUrl);
                    Cloudinary::destroy($publicId);
                }
                $this->oldAvatarUrl = Cloudinary::upload($this->avatar->getRealPath())->getSecurePath();
            }

            // Mise à jour des données de l'utilisateur
            $this->user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'role' => $this->role,
                'avatar' => $this->oldAvatarUrl,
            ]);

            // Afficher un message de succès
            $this->success(
                title: 'Utilisateur mis à jour avec succès',
                description: 'Les informations de l\'utilisateur ont été mises à jour avec succès.',
                redirectTo: route('admin.users.index')
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
        return view('livewire.admin.components.users.edit');
    }
}
