<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Edit extends Component
{
    use WithFileUploads;

    public Product $product;
    public $image, $categories;

    protected function rules()
    {
        return [
            'product.name' => 'required|string|max:255',
            'product.price' => 'required|numeric|min:0',
            'product.description' => 'required|string',
            'product.allergens' => 'nullable|array',
            'product.category_id' => 'required|exists:categories,id',
        ];
    }

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->validate();

        if ($this->image) {
            $imageUrl = Cloudinary::upload($this->image->getRealPath())->getSecurePath();
            $this->product->image = $imageUrl;
        }

        $this->product->save();

        session()->flash('message', 'Product updated successfully!');
        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.admin.components.products.edit');
    }
}
