<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Create extends Component
{
    use WithFileUploads;
    use Toast;

    public $name, $price, $description, $allergens = [], $image, $category_id;
    public $categories;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'required|string',
        'allergens' => 'nullable|array',
        'image' => 'nullable|image|max:2048', // Taille maximale de 2MB
        'category_id' => 'required|exists:categories,id',
    ];

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get(); // Récupérer les catégories triées par nom
    }

    public function save()
    {
        $this->validate();

        try {
            // Téléchargement de l'image sur Cloudinary
            $imageUrl = $this->image
                ? Cloudinary::upload($this->image->getRealPath())->getSecurePath()
                : null;

            // Enregistrer le produit dans la base de données
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'allergens' => !empty($this->allergens) ? json_encode($this->allergens) : null,
                'image' => $imageUrl,
                'category_id' => $this->category_id,
            ]);

            // Réinitialiser les champs du formulaire
            $this->reset(['name', 'price', 'description', 'allergens', 'image', 'category_id']);

            // Afficher un message de succès et rediriger
            $this->success(
                title: 'Produit Créé avec Succès',
                description: 'Le produit a été créé avec succès.',
                redirectTo: route('admin.products.index')
            );
        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.components.products.create');
    }
}
