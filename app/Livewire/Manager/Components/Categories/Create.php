<?php

namespace App\Livewire\Manager\Components\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;

class Create extends Component
{
    use Toast;

    public string $name = '';
    public string $slug = '';
    public string $description = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
        'slug' => 'nullable|string|max:255|unique:categories,slug',
        'description' => 'nullable|string|max:500',
    ];

    public function save(): void
    {
        try {
            $user = Auth::user();
            if (!$user) {
                $this->error('Non autorisé', 'Vous devez être connecté.');
                return;
            }
            $this->validate();
            Category::create([
                'name' => $this->name,
                'slug' => $this->slug ?: Str::slug($this->name),
                'description' => $this->description,
            ]);
            $this->success('Catégorie créée', 'La catégorie a été créée avec succès.', redirectTo: route('manager.categories.index'));
        } catch (\Exception $e) {
            $this->error('Erreur', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.manager.components.categories.create');
    }
}
