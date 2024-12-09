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
        'image' => 'nullable|image|max:2048', // Max size 2MB
        'category_id' => 'required|exists:categories,id',
    ];

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get(); // Fetch categories sorted by name
    }

    public function save()
    {
        $this->validate();

        try {
            // Upload image to Cloudinary
            $imageUrl = $this->image
                ? Cloudinary::upload($this->image->getRealPath())->getSecurePath()
                : null;

            // Save product in database
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
                'allergens' => !empty($this->allergens) ? json_encode($this->allergens) : null,
                'image' => $imageUrl,
                'category_id' => $this->category_id,
            ]);

            // Reset form fields
            $this->reset(['name', 'price', 'description', 'allergens', 'image', 'category_id']);

            // Show success message and redirect
            $this->success(
                title: 'Product Created Successfully',
                description: 'The Product has been created successfully.',
                redirectTo: route('admin.products.index')
            );
        } catch (\Exception $e) {
            $this->error('Error' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.components.products.create');
    }
}
