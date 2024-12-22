<?php

namespace App\Livewire\Admin\Components\Categories;

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

    /**
     * Méthode d'initialisation du composant avec la catégorie donnée.
     */
    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug ?? '';
        $this->description = $category->description ?? '';
    }

    /**
     * Règles de validation.
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $this->category->id,
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Enregistrer les modifications de la catégorie.
     */
    public function save(): void
    {
        try {
            $authenticatedUser = Auth::user();
            if (!$authenticatedUser) {
                $this->error(
                    title: 'Non autorisé',
                    description: 'Vous devez être connecté pour effectuer cette action.'
                );
                return;
            }

            $this->validate();

            $this->category->update([
                'name' => $this->name,
                'slug' => $this->slug ?: Str::slug($this->name),
                'description' => $this->description,
            ]);

            $this->success(
                title: 'Catégorie mise à jour',
                description: 'La catégorie a été mise à jour avec succès.',
                redirectTo: route('admin.categories.index')
            );
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue : ' . $e->getMessage()
            );
        }
    }

    /**
     * Rendre la vue d'édition.
     */
    public function render()
    {
        return view('livewire.admin.components.categories.edit');
    }
}
