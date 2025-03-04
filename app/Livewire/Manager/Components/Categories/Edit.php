<?php

namespace App\Livewire\Manager\Components\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Edit extends Component
{
    use Toast;

    public Category $category;
    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug ?? '';
        $this->description = $category->description ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $this->category->id,
            'description' => 'nullable|string|max:500',
        ];
    }

    public function save(): void
    {
        try {
            $user = Auth::user();
            if (!$user) {
                $this->error('Non autorisé', 'Vous devez être connecté.');
                return;
            }
            $this->validate();
            $this->category->update([
                'name' => $this->name,
                'slug' => $this->slug ?: Str::slug($this->name),
                'description' => $this->description,
            ]);
            $this->success('Catégorie mise à jour', 'La catégorie a été mise à jour avec succès.', redirectTo: route('manager.categories.index'));
        } catch (\Exception $e) {
            $this->error('Erreur', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.manager.components.categories.edit');
    }
}
