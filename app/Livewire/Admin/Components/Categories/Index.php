<?php

namespace App\Livewire\Admin\Components\Categories;

use App\Models\Category;
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
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Explicit search function to trigger manual search if needed.
     */
    public function search(): void
    {
        $this->resetPage();
    }

    /**
     * Open the delete confirmation modal.
     */
    public function confirmDelete(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->showDeleteModal = true;
    }

    /**
     * Close the delete modal.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingCategoryId', 'deletingCategoryName']);
    }

    /**
     * Delete the category.
     */
    public function deleteCategory(): void
    {
        if ($this->deletingCategoryId) {
            $category = Category::find($this->deletingCategoryId);

            if ($category) {
                $category->delete();
                $this->success('Category deleted successfully.');
            }

            $this->cancelDelete();
        }
    }

    /**
     * Fetch filtered and paginated categories.
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
     * Define table headers.
     */
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'actions', 'label' => 'Actions'],
        ];
    }

    /**
     * Render the Livewire view.
     */
    public function render()
    {
        return view('livewire.admin.components.categories.index', [
            'categories' => $this->categories(),
            'headers' => $this->headers(),
        ]);
    }
}