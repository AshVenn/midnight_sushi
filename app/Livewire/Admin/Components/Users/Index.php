<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $roleFilter = 'all';
    public array $sortBy = ['column' => 'first_name', 'direction' => 'asc'];
    public bool $showBanModal = false;
    public bool $showDeleteModal = false;
    public ?int $banningUserId = null;
    public ?int $deletingUserId = null;
    public ?string $banningUserName = null;
    public ?string $deletingUserName = null;
    public ?string $currentStatus = null;

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

    private function hasOrders(User $user): bool
    {
        return Order::where('customer_id', $user->id)->exists();
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

    public function confirmBan($userId, $currentStatus): void
    {
        $user = User::findOrFail($userId);
        $this->banningUserId = $user->id;
        $this->banningUserName = $user->full_name;
        $this->currentStatus = $currentStatus;
        $this->showBanModal = true;
    }

    

    public function cancelBan(): void
    {
        $this->reset(['showBanModal', 'banningUserId', 'banningUserName', 'currentStatus']);
    }

    
    public function confirmDelete(int $userId): void
    {
        $user = User::findOrFail($userId);
        
        if ($user->role === 'admin') {
            $this->error(
                title: 'Erreur',
                description: 'Les administrateurs ne peuvent pas être supprimés.'
            );
            return;
        }

        if ($this->hasOrders($user)) {
            $this->error(
                title: 'Erreur',
                description: 'Impossible de supprimer un utilisateur avec des commandes.'
            );
            return;
        }

        $this->deletingUserId = $user->id;
        $this->deletingUserName = "{$user->first_name} {$user->last_name}";
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingUserId', 'deletingUserName']);
    }
   

    public function deleteUser(): void
    {
        try {
            $user = User::find($this->deletingUserId);

            if (!$user) {
                $this->error(
                    title: 'Erreur',
                    description: 'Utilisateur introuvable.'
                );
                return;
            }

            if ($user->role === 'admin') {
                $this->error(
                    title: 'Erreur',
                    description: 'Les administrateurs ne peuvent pas être supprimés.'
                );
                return;
            }

            if ($this->hasOrders($user)) {
                $this->error(
                    title: 'Erreur',
                    description: 'Impossible de supprimer un utilisateur avec des commandes.'
                );
                return;
            }

            // Delete avatar if exists
            if ($user->avatar) {
                $publicId = $this->getPublicIdFromUrl($user->avatar);
                Cloudinary::destroy($publicId);
            }

            $user->delete();
            $this->success(
                title: 'Suppression réussie',
                description: 'L\'utilisateur a été supprimé avec succès.'
            );

            $this->cancelDelete();
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue lors de la suppression.'
            );
        }
    }

    public function performBan(): void
    {
        $user = User::find($this->banningUserId);
        
        if ($user->role === 'admin') {
            $this->error(
                title: 'Erreur',
                description: 'Les administrateurs ne peuvent pas être bannis.'
            );
            $this->cancelBan();
            return;
        }

        if ($user) {
            $newStatus = $this->currentStatus === 'active' ? 'banned' : 'active';
            $user->update(['status' => $newStatus]);
            $this->success(
                title: 'Statut mis à jour',
                description: "Le statut de l'utilisateur a été modifié en {$newStatus}."
            );
        }

        $this->cancelBan();
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
                $user->hasOrders = Order::where('customer_id', $user->id)->exists();
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
            ['key' => 'status', 'label' => 'Statut', 'sortable' => true],
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
