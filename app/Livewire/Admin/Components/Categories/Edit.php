<?php

namespace App\Livewire\Admin\Components\Categories;

use App\Models\Category;
use Livewire\Component;
use Mary\Traits\Toast;

class Edit extends Component
{
    use Toast;

    public Category $category;
    public string $name = '';
    public string $description = '';

    /**
     * Mount the component with the given category.
     */
    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Save the updated category.
     */
    public function save(): void
    {
        $this->validate();

        $this->category->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->success(
            title: 'Category Updated',
            description: 'The category has been updated successfully.',
            redirectTo: route('admin.categories.index')
        );
    }

    /**
     * Render the edit view.
     */
    public function render()
    {
        return view('livewire.admin.components.categories.edit');
    }
}
