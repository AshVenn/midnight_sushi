<?php

namespace App\Livewire\Admin\Components\Products;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(Product $product)
    {
        $product->delete();
        $this->success('Product deleted successfully.');
    }

    public function products()
    {
        return Product::query()
            ->with('category') // Eager load the category
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhereHas('category', function ($q) {
                          $q->where('name', 'like', "%{$this->search}%");
                      });
            })
            ->orderBy('name')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.components.products.index', [
            'products' => $this->products(),
        ]);
    }
}
