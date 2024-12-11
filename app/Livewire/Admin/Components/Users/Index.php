<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $roleFilter = 'all'; // Role filter with default value
    public array $sortBy = ['column' => 'first_name', 'direction' => 'asc'];
    public bool $showDeleteModal = false; // Controls modal visibility
    public ?int $deletingUserId = null; // ID of the user to be deleted
    public ?string $deletingUserName = null; // Name of the user to be deleted

    /**
     * Reset pagination when search or filter changes.
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
     * Change sorting column and direction.
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
     * Open the delete confirmation modal.
     */
    public function confirmDelete(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->deletingUserId = $user->id;
        $this->deletingUserName = "{$user->first_name} {$user->last_name}";
        $this->showDeleteModal = true;
    }

    /**
     * Close the delete modal.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingUserId', 'deletingUserName']);
    }

    /**
     * Delete the user along with their avatar.
     */
    public function deleteUser(): void
    {
        if ($this->deletingUserId) {
            $user = User::find($this->deletingUserId);

            if ($user) {
                // Delete avatar from Cloudinary if it exists
                if ($user->avatar) {
                    $publicId = $this->getPublicIdFromUrl($user->avatar);
                    Cloudinary::destroy($publicId);
                }

                // Delete the user
                $user->delete();
                $this->success('User deleted successfully.');
            }

            $this->cancelDelete();
        }
    }

    /**
     * Extract the public ID from the Cloudinary URL.
     */
    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Fetch filtered, sorted, and paginated users.
     */
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10)
            ->through(function ($user) {
                $user->full_name = $user->full_name; // Use the accessor
                return $user;
            });
    }
    

    /**
     * Define table headers.
     */
    public function headers(): array
    {
        return [
            ['key' => 'avatar', 'label' => 'Avatar', 'sortable' => false],
            ['key' => 'full_name', 'label' => 'Full Name', 'sortable' => true, 'column' => 'first_name'],
            ['key' => 'email', 'label' => 'Email Address', 'sortable' => true, 'column' => 'email'],
            ['key' => 'role', 'label' => 'Role', 'sortable' => true, 'column' => 'role'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
        ];
    }

    /**
     * Render the Livewire view.
     */
    public function render()
    {
        return view('livewire.admin.components.users.index', [
            'users' => $this->users(),
            'headers' => $this->headers(),
        ]);
    }
}
