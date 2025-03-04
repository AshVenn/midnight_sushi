<?php

namespace App\Livewire\Manager\Components\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Create extends Component
{
    use WithFileUploads, Toast;

    public $name, $slug;
    public $price, $discount_price;
    public $promotion_start, $promotion_end;
    public $description, $allergens = [];
    public $image;
    public $category_id;
    public $is_available = true;

    public $categories;

    protected $rules = [
        'name'             => 'required|string|max:255',
        'slug'             => 'nullable|string|max:255',
        'price'            => 'required|numeric|min:0',
        'discount_price'   => 'nullable|numeric|min:0',
        'promotion_start'  => 'nullable|date',
        'promotion_end'    => 'nullable|date|after_or_equal:promotion_start',
        'description'      => 'required|string',
        'allergens'        => 'nullable|array',
        'image'            => 'nullable|image|max:2048',
        'category_id'      => 'required|exists:categories,id',
        'is_available'     => 'boolean',
    ];

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get(); // Récupérer les catégories triées par nom
    }

public function save()
    {
        $this->validate();

        try {
            $imageUrl = $this->image
                ? Cloudinary::upload($this->image->getRealPath())->getSecurePath()
                : null;

            Product::create([
                'name'             => $this->name,
                'slug'             => $this->slug,  
                'price'            => $this->price,
                'discount_price'   => $this->discount_price,
                'promotion_start'  => $this->promotion_start,
                'promotion_end'    => $this->promotion_end,
                'description'      => $this->description,
                'allergens'        => !empty($this->allergens) ? json_encode($this->allergens) : null,
                'image'            => $imageUrl,
                'category_id'      => $this->category_id,
                'is_available'     => $this->is_available,
            ]);

            $this->reset([
                'name', 'slug', 'price', 'discount_price', 'promotion_start', 'promotion_end',
                'description', 'allergens', 'image', 'category_id', 'is_available'
            ]);

            $this->success(
                title: 'Produit Créé avec Succès',
                description: 'Le produit a été créé avec succès.',
                redirectTo: route('manager.products.index')
            );
        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.manager.components.products.create');
    }
}
