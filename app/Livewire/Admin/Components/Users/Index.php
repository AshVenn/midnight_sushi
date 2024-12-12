<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = ''; // Recherche
    public string $roleFilter = 'all'; // Filtre par rôle avec valeur par défaut
    public array $sortBy = ['column' => 'first_name', 'direction' => 'asc']; // Tri par défaut
    public bool $showDeleteModal = false; // Contrôle de la visibilité du modal
    public ?int $deletingUserId = null; // ID de l'utilisateur à supprimer
    public ?string $deletingUserName = null; // Nom de l'utilisateur à supprimer

    /**
     * Réinitialise la pagination lorsque la recherche ou le filtre change.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateFilter():void {
        $this->users();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Met à jour le tri des colonnes.
     */
    public function sortByColumn(string $column): void
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Ouvre la fenêtre de confirmation de suppression.
     */
    public function confirmDelete(int $userId): void
    {
        try {
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser) {
                $this->error(
                    title: 'Erreur',
                    description: 'Vous devez être authentifié pour effectuer cette action.'
                );
                return;
            }

            $user = User::findOrFail($userId);
            $this->deletingUserId = $user->id;
            $this->deletingUserName = "{$user->first_name} {$user->last_name}";
            $this->showDeleteModal = true;
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue lors de la récupération des données de l\'utilisateur.'
            );
        }
    }

    /**
     * Ferme le modal de suppression.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingUserId', 'deletingUserName']);
    }

    /**
     * Supprime un utilisateur et son avatar.
     */
    public function deleteUser(): void
    {
        try {
            $authenticatedUser = Auth::user();

            if (!$authenticatedUser) {
                $this->error(
                    title: 'Erreur',
                    description: 'Vous devez être authentifié pour effectuer cette action.'
                );
                return;
            }

            if ($this->deletingUserId) {
                $user = User::find($this->deletingUserId);

                if ($user) {
                    // Supprime l'avatar sur Cloudinary s'il existe
                    if ($user->avatar) {
                        $publicId = $this->getPublicIdFromUrl($user->avatar);
                        Cloudinary::destroy($publicId);
                    }

                    // Supprime l'utilisateur
                    $user->delete();
                    $this->success(
                        title: 'Utilisateur supprimé',
                        description: 'L\'utilisateur a été supprimé avec succès.'
                    );
                }

                $this->cancelDelete();
            }
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue lors de la suppression de l\'utilisateur.'
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
     * Récupère les utilisateurs filtrés, triés et paginés.
     */
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
                });
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10)
            ->through(function ($user) {
                $user->full_name = "{$user->first_name} {$user->last_name}";
                return $user;
            });
    }

    /**
     * Définit les en-têtes de la table.
     */
    public function headers(): array
    {
        return [
            ['key' => 'avatar', 'label' => 'Avatar', 'sortable' => false],
            ['key' => 'full_name', 'label' => 'Nom Complet', 'sortable' => false],
            ['key' => 'email', 'label' => 'Adresse Email', 'sortable' => true],
            ['key' => 'role', 'label' => 'Rôle', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
        ];
    }

    /**
     * Rend la vue du composant Livewire.
     */
    public function render()
    {
        return view('livewire.admin.components.users.index', [
            'users' => $this->users(),
            'headers' => $this->headers(),
        ]);
    }
}
