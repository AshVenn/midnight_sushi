<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Create extends Component
{
    use WithFileUploads;

    public $name, $price, $description, $allergens = [], $image, $category_id;
    public $categories;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'required|string',
        'allergens' => 'nullable|array',
        'image' => 'nullable|image|max:2048',
        'category_id' => 'required|exists:categories,id',
    ];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->validate();

        $imageUrl = $this->image
            ? Cloudinary::upload($this->image->getRealPath())->getSecurePath()
            : null;

        Product::create([
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'allergens' => $this->allergens,
            'image' => $imageUrl,
            'category_id' => $this->category_id,
        ]);

        $this->reset(['name', 'price', 'description', 'allergens', 'image', 'category_id']);

        session()->flash('message', 'Product created successfully!');
        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.admin.components.products.create');
    }
}
