<?php

namespace App\Livewire\Manager\Components\Menus;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showDeleteModal = false;
    public ?int $deletingMenuId = null;
    public ?string $deletingMenuName = null;

    /**
     * Triggered when the search is submitted (e.g., pressing Enter).
     */
    public function search(): void
    {
        $this->resetPage();
    }

    /**
     * Resets pagination when the search term updates.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Handles column sorting.
     */
    public function sortByColumn(string $column): void
    {
        // Define sortable columns
        $sortableColumns = collect($this->headers())
            ->filter(fn($header) => $header['sortable'] ?? false)
            ->pluck('key')
            ->toArray();

        if (!in_array($column, $sortableColumns)) {
            $this->error(
                title: 'Erreur de tri',
                description: 'La colonne sélectionnée pour le tri est invalide.'
            );
            return;
        }

        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Retrieves the paginated list of menus with search and sorting applied.
     */
    public function getMenusProperty()
    {
        return Menu::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('slug', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
    }

    /**
     * Opens the confirmation modal for deleting a menu.
     */
    public function confirmDelete(int $menuId): void
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            $this->error(
                title: 'Erreur',
                description: 'Utilisateur non authentifié.'
            );
            return;
        }

        $menu = Menu::findOrFail($menuId);
        $this->deletingMenuId = $menu->id;
        $this->deletingMenuName = $menu->name;
        $this->showDeleteModal = true;
    }

    /**
     * Cancels the delete action and closes the modal.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingMenuId', 'deletingMenuName']);
    }

    /**
     * Deletes the selected menu and its image from Cloudinary.
     */
    public function deleteMenu(): void
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            $this->error(
                title: 'Erreur',
                description: 'Utilisateur non authentifié.'
            );
            return;
        }

        if ($this->deletingMenuId) {
            $menu = Menu::find($this->deletingMenuId);
            if ($menu) {
                // Delete associated image from Cloudinary if exists
                if ($menu->image) {
                    $publicId = $this->getPublicIdFromUrl($menu->image);
                    \Cloudinary::destroy($publicId);
                }

                $menu->delete();
                $this->success(
                    title: 'Menu Supprimé',
                    description: "Le menu \"{$menu->name}\" a été supprimé avec succès."
                );
            }
            $this->cancelDelete();
        }
    }

    /**
     * Stops the promotion for a specific menu.
     */
    public function stopPromotion(int $menuId): void
    {
        $menu = Menu::find($menuId);
        if (!$menu) {
            $this->error(
                title: 'Erreur',
                description: 'Menu introuvable.'
            );
            return;
        }

        $menu->update([
            'discount_price'  => null,
            'promotion_start' => null,
            'promotion_end'   => null,
        ]);

        $this->success(
            title: 'Promotion arrêtée',
            description: "La promotion du menu \"{$menu->name}\" a été arrêtée."
        );
    }

    /**
     * Defines the table headers.
     */
    public function headers(): array
    {
        return [
            ['key' => 'image',          'label' => 'Image',    'sortable' => false],
            ['key' => 'name',           'label' => 'Nom',      'sortable' => true],
            ['key' => 'slug',           'label' => 'Slug',     'sortable' => true],
            ['key' => 'price',          'label' => 'Prix',     'sortable' => true],
            ['key' => 'discount_price', 'label' => 'Promo',    'sortable' => true],
            ['key' => 'actions',        'label' => 'Actions',  'sortable' => false],
        ];
    }

    /**
     * Helper method to extract the public ID from a Cloudinary URL.
     */
    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Renders the Index view with menus and headers.
     */
    public function render()
    {
        return view('livewire.admin.components.menus.index', [
            'menus'   => $this->menus,
            'headers' => $this->headers(),
        ]);
    }
}
