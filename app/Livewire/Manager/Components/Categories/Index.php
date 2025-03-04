<?php

namespace App\Livewire\Manager\Components\Categories;

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

    public function search(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $categoryId): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->error('Erreur', 'Utilisateur non authentifié.');
            return;
        }
        $category = Category::findOrFail($categoryId);
        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingCategoryId', 'deletingCategoryName']);
    }

    public function deleteCategory(): void
    {
        if ($this->deletingCategoryId) {
            $category = Category::find($this->deletingCategoryId);
            if ($category) {
                $category->delete();
                $this->success('Catégorie Supprimée', 'La catégorie a été supprimée avec succès.');
            }
            $this->cancelDelete();
        }
    }

    public function sortByColumn(string $column): void
    {
        $sortableColumns = collect($this->headers())->filter(fn($header) => $header['sortable'] ?? false)
            ->pluck('key')->toArray();
        if (!in_array($column, $sortableColumns)) {
            $this->error('Erreur de tri', 'La colonne sélectionnée pour le tri est invalide.');
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

    public function categories()
    {
        return Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('slug', 'like', "%{$this->search}%");
            })
            ->when($this->sortBy['column'], function ($query) {
                $query->orderBy($this->sortBy['column'], $this->sortBy['direction']);
            })
            ->paginate(10);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Nom', 'sortable' => true],
            ['key' => 'slug', 'label' => 'Slug', 'sortable' => true],
            ['key' => 'description', 'label' => 'Description', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Créé le', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
        ];
    }

    public function render()
    {
        return view('livewire.manager.components.categories.index', [
            'categories' => $this->categories(),
            'headers' => $this->headers(),
        ]);
    }
}
