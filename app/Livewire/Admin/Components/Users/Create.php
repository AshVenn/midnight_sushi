<?php

namespace App\Livewire\Admin\Components\Users;

use Illuminate\Support\Facades\Auth;
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

    public string $first_name = ''; // Prénom de l'utilisateur
    public string $last_name = ''; // Nom de famille de l'utilisateur
    public string $email = ''; // Email de l'utilisateur
    public string $password = ''; // Mot de passe de l'utilisateur
    public string $role = 'customer'; // Rôle par défaut
    public $avatar; // Avatar de l'utilisateur

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,customer,manager,delivery_person',
        'avatar' => 'nullable|image|max:2048', // Avatar facultatif avec une taille maximale de 2 Mo
    ];

    /**
     * Enregistrer un nouvel utilisateur.
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

            // Téléchargement de l'avatar sur Cloudinary, si fourni
            $avatarUrl = $this->avatar
                ? Cloudinary::upload($this->avatar->getRealPath())->getSecurePath()
                : null;

            // Création de l'utilisateur
            User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'avatar' => $avatarUrl,
            ]);

            // Réinitialisation du formulaire
            $this->reset(['first_name', 'last_name', 'email', 'password', 'role', 'avatar']);

            // Afficher un message de succès et redirection
            $this->success(
                title: 'Utilisateur créé avec succès',
                description: 'L\'utilisateur a été créé avec succès.',
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
     * Rendre la vue du composant.
     */
    public function render()
    {
        return view('livewire.admin.components.users.create');
    }
}
