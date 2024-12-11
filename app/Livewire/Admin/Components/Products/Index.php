<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = 'all'; // Category filter
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public bool $showDeleteModal = false;
    public ?int $deletingProductId = null;
    public ?string $deletingProductName = null;

    /**
     * Reset pagination when search or filter changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updateCategoryFilter(): void{
        $this->products();
    }

    
    /**
     * Confirm delete action for a specific product.
     */
    public function confirmDelete(int $productId): void
    {
        $product = Product::findOrFail($productId);

        $this->deletingProductId = $product->id;
        $this->deletingProductName = $product->name;
        $this->showDeleteModal = true;
    }

    /**
     * Cancel the delete action.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingProductId', 'deletingProductName']);
    }

    /**
     * Delete the selected product and its Cloudinary image.
     */
    public function deleteProduct(): void
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
     * Fetch paginated products with search and category filters.
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
            ->when($this->categoryFilter !== 'all', function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->sortBy['column'] !== 'category.name', function ($query) {
                $query->orderBy($this->sortBy['column'], $this->sortBy['direction']);
            })
            ->paginate(10);
    }
       

    /**
     * Get categories for the filter dropdown.
     */
    public function categories(): array
    {
        return Category::select(['id', 'name'])
            ->get()
            ->map(fn($category) => ['id' => $category->id, 'name' => $category->name])
            ->prepend(['id' => 'all', 'name' => 'All Categories'])
            ->toArray();
    }

    /**
     * Define the table headers.
     */
    public function headers(): array
    {
        return [
            ['key' => 'image', 'label' => 'Image', 'sortable' => false],
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'column' => 'name'],
            ['key' => 'category.name', 'label' => 'Category', 'sortable' => true, 'column' => 'category_id'],
            ['key' => 'price', 'label' => 'Price', 'sortable' => true, 'column' => 'price'],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
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
            'categories' => $this->categories(),
        ]);
    }
}
