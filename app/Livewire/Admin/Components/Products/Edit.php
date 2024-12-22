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
    use WithFileUploads, Toast;

    public Product $product;

    public string $name;
    public ?string $slug = null;
    public float $price;
    public ?float $discount_price = null;
    public ?string $promotion_start = null;
    public ?string $promotion_end = null;
    public string $description;
    public string $allergensString = '';
    public $image;
    public ?string $oldImageUrl = null;
    public int $category_id;
    public bool $is_available;

    public $categories;

    protected function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'price'            => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0',
            'promotion_start'  => 'nullable|date',
            'promotion_end'    => 'nullable|date|after_or_equal:promotion_start',
            'description'      => 'required|string',
            'allergensString'  => 'nullable|string',
            'image'            => 'nullable|image|max:2048',
            'category_id'      => 'required|exists:categories,id',
            'is_available'     => 'boolean',
        ];
    }

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->price = $product->price;
        $this->discount_price = $product->discount_price;
        $this->promotion_start = $product->promotion_start?->format('Y-m-d');
        $this->promotion_end = $product->promotion_end?->format('Y-m-d');
        $this->description = $product->description;

        // Properly handle allergens
        if (is_array($product->allergens)) {
            $this->allergensString = implode(',', $product->allergens);
        } elseif (is_string($product->allergens)) {
            $this->allergensString = $product->allergens;
        } else {
            $this->allergensString = '';
        }

        $this->oldImageUrl = $product->image;
        $this->category_id = $product->category_id;
        $this->is_available = $product->is_available;

        $this->categories = Category::all();
    }

    public function save()
    {
        $this->validate();

        // Handle Image Upload
        if ($this->image) {
            if ($this->oldImageUrl) {
                $publicId = $this->getPublicIdFromUrl($this->oldImageUrl);
                Cloudinary::destroy($publicId);
            }

            $this->oldImageUrl = Cloudinary::upload($this->image->getRealPath())->getSecurePath();
        }

        // Process Allergens
        $allergensArray = array_filter(array_map('trim', explode(',', $this->allergensString)));

        // Update Product
        $this->product->update([
            'name'             => $this->name,
            'slug'             => $this->slug ?: \Str::slug($this->name),
            'price'            => $this->price,
            'discount_price'   => $this->discount_price,
            'promotion_start'  => $this->promotion_start,
            'promotion_end'    => $this->promotion_end,
            'description'      => $this->description,
            'allergens'        => $allergensArray,
            'image'            => $this->oldImageUrl,
            'category_id'      => $this->category_id,
            'is_available'     => $this->is_available,
        ]);

        $this->success(
            title: 'Produit mis à jour avec succès',
            description: 'Le produit a été mis à jour avec succès.',
            redirectTo: route('admin.products.index')
        );
    }

    private function getPublicIdFromUrl(string $url): string
    {
        return pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_FILENAME);
    }

    public function render()
    {
        return view('livewire.admin.components.products.edit');
    }
}
