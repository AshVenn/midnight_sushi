<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Mary\Traits\Toast;

class Edit extends Component
{
    use WithFileUploads;
    use Toast;

    public Product $product;
    public string $name;
    public float $price;
    public string $description;
    public array $allergens = [];
    public $image; // New uploaded image
    public ?string $oldImageUrl = null; // To track the existing image URL
    public int $category_id;
    public $categories;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'allergens' => 'nullable|array',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->description = $product->description;
        $this->allergens = $product->allergens ? json_decode($product->allergens, true) : [];
        $this->oldImageUrl = $product->image; // Set the old image URL
        $this->category_id = $product->category_id;
        $this->categories = Category::all();
    }

    public function save()
    {
        $this->validate();

        // Handle image replacement
        if ($this->image) {
            // Delete old image if it exists
            if ($this->oldImageUrl) {
                $publicId = $this->getPublicIdFromUrl($this->oldImageUrl);
                Cloudinary::destroy($publicId);
            }

            // Upload the new image
            $this->oldImageUrl = Cloudinary::upload($this->image->getRealPath())->getSecurePath();
        }

        // Update the product
        $this->product->update([
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'allergens' => json_encode($this->allergens),
            'image' => $this->oldImageUrl,
            'category_id' => $this->category_id,
        ]);

        $this->success(
            title: 'Product updated Successfully',
            description: 'The Product has been updated successfully.',
            redirectTo: route('admin.products.index')
        );
    }

    private function getPublicIdFromUrl(string $url): string
    {
        // Extract the public ID from the Cloudinary URL
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function render()
    {
        return view('livewire.admin.components.products.edit');
    }
}
