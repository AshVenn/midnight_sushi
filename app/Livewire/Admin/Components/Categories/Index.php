<?php

namespace App\Livewire\Admin\Components\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $deletingCategoryId = null;
    public ?string $deletingCategoryName = null;
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    /**
     * Fonction explicite de recherche pour un déclenchement manuel.
     */
    public function search(): void
    {
        $this->resetPage();
    }

    /**
     * Ouvre la modal de confirmation de suppression.
     */
    public function confirmDelete(int $categoryId): void
    {
        $authenticatedUser = Auth::user();

        if (!$authenticatedUser) {
            $this->error(
                title: 'Erreur',
                description: 'Utilisateur non authentifié.'
            );
            return;
        }

        $category = Category::findOrFail($categoryId);

        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->showDeleteModal = true;
    }

    /**
     * Ferme la modal de suppression.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingCategoryId', 'deletingCategoryName']);
    }

    /**
     * Supprime la catégorie.
     */
    public function deleteCategory(): void
    {
        $authenticatedUser = Auth::user();

        if (!$authenticatedUser) {
            $this->error(
                title: 'Erreur',
                description: 'Utilisateur non authentifié.'
            );
            return;
        }

        if ($this->deletingCategoryId) {
            $category = Category::find($this->deletingCategoryId);

            if ($category) {
                $category->delete();
                $this->success(
                    title: 'Catégorie Supprimée',
                    description: 'La catégorie a été supprimée avec succès.'
                );
            }

            $this->cancelDelete();
        }
    }

    /**
     * Modifie le tri des colonnes et la direction.
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
     * Récupère les catégories filtrées, triées et paginées.
     */
    public function categories()
    {
        return Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
    }

    /**
     * Définit les en-têtes du tableau.
     */
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nom'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'actions', 'label' => 'Actions'],
        ];
    }

    /**
     * Rend la vue du composant Livewire.
     */
    public function render()
    {
        return view('livewire.admin.components.categories.index', [
            'categories' => $this->categories(),
            'headers' => $this->headers(),
        ]);
    }
}
