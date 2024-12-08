<?php

namespace App\Livewire\Admin\Components\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showDeleteModal = false; // Controls modal visibility
    public ?int $deletingUserId = null; // ID of the user to be deleted
    public ?string $deletingUserName = null; // Name of the user to be deleted

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
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
     * Delete the user.
     */
    public function deleteUser(): void
    {
        if ($this->deletingUserId) {
            $user = User::find($this->deletingUserId);

            if ($user) {
                $user->delete();
                $this->success('User deleted successfully.');
            }

            $this->cancelDelete();
        }
    }

    /**
     * Fetch filtered and paginated users.
     */
    public function users()
    {
        return User::query()
            ->select(['users.*', \DB::raw("CONCAT(first_name, ' ', last_name) AS full_name")])
            ->when($this->search, function ($query) {
                $query->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderBy('first_name')
            ->paginate(10);
    }

    /**
     * Define table headers.
     */
    public function headers(): array
    {
        return [
            ['key' => 'full_name', 'label' => 'Full Name'],
            ['key' => 'email', 'label' => 'Email Address'],
            ['key' => 'role', 'label' => 'Role'],
            ['key' => 'actions', 'label' => 'Actions'],
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
