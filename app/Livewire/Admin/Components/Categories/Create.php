<?php

namespace App\Livewire\Admin\Components\Categories;

use App\Models\Category;
use Livewire\Component;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast;

    public string $name = '';
    public string $description = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string|max:500',
    ];

    public function save(): void
    {
        $this->validate();
        Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->success('Category created successfully.', redirectTo: route('admin.categories.index'));
    }

    public function render()
    {
        return view('livewire.admin.components.categories.create');
    }
}
