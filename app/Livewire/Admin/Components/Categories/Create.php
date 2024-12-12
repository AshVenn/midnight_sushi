<?php

namespace App\Livewire\Admin\Components\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast;

    public string $name = '';
    public string $description = '';

    /**
     * Règles de validation.
     */
    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string|max:500',
    ];

    /**
     * Enregistrer une nouvelle catégorie.
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

            Category::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $this->success(
                title: 'Catégorie créée',
                description: 'La catégorie a été créée avec succès.',
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
     * Rendre la vue de création.
     */
    public function render()
    {
        return view('livewire.admin.components.categories.create');
    }
}
