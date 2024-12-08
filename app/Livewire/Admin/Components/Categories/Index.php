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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $categoryId): void
    {
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
                $this->success('Category deleted successfully.');
            }

            $this->cancelDelete();
        }
    }

    public function categories()
    {
        return Category::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'description', 'label' => 'Description'], // Added description
            ['key' => 'actions', 'label' => 'Actions'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.components.categories.index', [
            'categories' => $this->categories(),
            'headers' => $this->headers(),
        ]);
    }
}
