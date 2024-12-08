<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDeleteModal = false;
    public ?int $deletingProductId = null;
    public ?string $deletingProductName = null;

    /**
     * Reset pagination when the search term changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Confirm delete action for a specific product.
     */
    public function confirmDelete(int $productId)
    {
        $product = Product::findOrFail($productId);

        $this->deletingProductId = $product->id;
        $this->deletingProductName = $product->name;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel the delete action.
     */
    public function cancelDelete()
    {
        $this->reset(['showDeleteModal', 'deletingProductId', 'deletingProductName']);
    }

    /**
     * Delete the selected product and its Cloudinary image.
     */
    public function deleteProduct()
    {
        if ($this->deletingProductId) {
            $product = Product::find($this->deletingProductId);

            if ($product) {
                // Delete image from Cloudinary
                if ($product->image) {
                    $publicId = pathinfo($product->image, PATHINFO_FILENAME);
                    Cloudinary::destroy($publicId);
                }

                // Delete the product
                $product->delete();

                session()->flash('message', 'Product deleted successfully.');
            }

            $this->cancelDelete();
        }
    }

    /**
     * Fetch paginated products with search functionality.
     */
    public function products()
    {
        return Product::query()
            ->with('category') // Load category relationship
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * Define the table headers.
     */
    public function headers(): array
    {
        return [
            ['key' => 'image', 'label' => 'Image'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'category.name', 'label' => 'Category'],
            ['key' => 'price', 'label' => 'Price'],
            ['key' => 'actions', 'label' => 'Actions', 'class' => 'text-center'],
        ];
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.admin.components.products.index', [
            'products' => $this->products(),
            'headers' => $this->headers(),
        ]);
    }
}
