<?php

namespace App\Livewire\Admin\Components\Menus;

use App\Models\Menu;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Create extends Component
{
    use WithFileUploads, Toast;

    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public float $price = 0;
    public ?float $discount_price = null;
    public ?string $promotion_start = null;
    public ?string $promotion_end = null;

    /**
     * Repeater-like structure: each element is ['product_id' => X, 'quantity' => Y].
     */
    public array $menuItems = [];

    public $allProducts;

    public $image; // Image upload

    protected $rules = [
        'name'                     => 'required|string|max:255',
        'slug'                     => 'nullable|string|max:255|unique:menus,slug',
        'description'              => 'nullable|string',
        'price'                    => 'required|numeric|min:0',
        'discount_price'           => 'nullable|numeric|min:0',
        'promotion_start'          => 'nullable|date',
        'promotion_end'            => 'nullable|date|after_or_equal:promotion_start',
        'menuItems'                => 'array',
        'menuItems.*.product_id'   => 'required|exists:products,id',
        'menuItems.*.quantity'     => 'required|integer|min:1',
        'image'                    => 'nullable|image|max:2048', // Max 2MB
    ];

    /**
     * Initializes the component by loading all products.
     */
    public function mount()
    {
        $this->allProducts = Product::orderBy('name')->get();
    }

    /**
     * Adds a new product line to the menu.
     */
    public function addProductLine()
    {
        $this->menuItems[] = [
            'product_id' => null,
            'quantity'   => 1,
        ];
    }

    /**
     * Removes a product line by its index.
     */
    public function removeProductLine(int $index)
    {
        unset($this->menuItems[$index]);
        $this->menuItems = array_values($this->menuItems);
    }

    /**
     * Saves the new menu along with associated products and image.
     */
    public function save()
    {
        try {
            $authenticatedUser = Auth::user();
            if (!$authenticatedUser) {
                $this->error(
                    title: 'Non autorisé',
                    description: 'Vous devez être connecté pour effectuer cette action.'
                );
                return;
            }

            $this->validate();

            // Handle image upload if provided
            $imageUrl = null;
            if ($this->image) {
                $imageUrl = \Cloudinary::upload($this->image->getRealPath())->getSecurePath();
            }

            // Create the menu
            $menu = Menu::create([
                'name'            => $this->name,
                'slug'            => $this->slug ?: Str::slug($this->name),
                'description'     => $this->description,
                'price'           => $this->price,
                'discount_price'  => $this->discount_price,
                'promotion_start' => $this->promotion_start,
                'promotion_end'   => $this->promotion_end,
                'image'           => $imageUrl,
            ]);

            // Sync pivot data with products and quantities
            $syncData = [];
            foreach ($this->menuItems as $item) {
                $syncData[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $menu->products()->sync($syncData);

            // Reset form fields
            $this->reset(['name', 'slug', 'description', 'price', 'discount_price', 'promotion_start', 'promotion_end', 'menuItems', 'image']);

            $this->success(
                title: 'Menu Créé',
                description: "Le menu \"{$menu->name}\" a été créé avec succès.",
                redirectTo: route('admin.menus.index')
            );
        } catch (\Exception $e) {
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue : ' . $e->getMessage()
            );
        }
    }

    /**
     * Renders the Create view.
     */
    public function render()
    {
        return view('livewire.admin.components.menus.create');
    }
}
