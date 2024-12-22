<?php

namespace App\Livewire\Admin\Components\Menus;

use App\Models\Menu;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Edit extends Component
{
    use WithFileUploads, Toast;

    public Menu $menu;

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

    public $image; // New image upload
    public ?string $oldImageUrl = null; // Existing image URL

    protected $rules = [
        'name'                     => 'required|string|max:255',
        'slug'                     => 'nullable|string|max:255|unique:menus,slug,' . 'menu.id',
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
     * Initializes the component with the existing menu data.
     */
    public function mount(Menu $menu)
    {
        $this->menu = $menu;

        $this->name = $menu->name;
        $this->slug = $menu->slug;
        $this->description = $menu->description ?? '';
        $this->price = $menu->price;
        $this->discount_price = $menu->discount_price;
        $this->promotion_start = $menu->promotion_start ? $menu->promotion_start->format('Y-m-d') : null;
        $this->promotion_end = $menu->promotion_end ? $menu->promotion_end->format('Y-m-d') : null;

        $this->allProducts = Product::orderBy('name')->get();

        // Initialize menuItems from pivot table
        $this->menuItems = $menu->products->map(function($p) {
            return [
                'product_id' => $p->id,
                'quantity'   => $p->pivot->quantity,
            ];
        })->toArray();

        $this->oldImageUrl = $menu->image;
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
     * Saves the updated menu along with associated products and image.
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

            // Handle image upload if a new image is provided
            if ($this->image) {
                // Delete the old image from Cloudinary if it exists
                if ($this->oldImageUrl) {
                    $publicId = $this->getPublicIdFromUrl($this->oldImageUrl);
                    \Cloudinary::destroy($publicId);
                }

                // Upload the new image to Cloudinary
                $imageUrl = \Cloudinary::upload($this->image->getRealPath())->getSecurePath();
                $this->menu->image = $imageUrl;
            }

            // Update menu attributes
            $this->menu->name = $this->name;
            $this->menu->slug = $this->slug ?: Str::slug($this->name);
            $this->menu->description = $this->description;
            $this->menu->price = $this->price;
            $this->menu->discount_price = $this->discount_price;
            $this->menu->promotion_start = $this->promotion_start;
            $this->menu->promotion_end = $this->promotion_end;

            $this->menu->save();

            // Sync pivot data with products and quantities
            $syncData = [];
            foreach ($this->menuItems as $item) {
                $syncData[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $this->menu->products()->sync($syncData);

            $this->success(
                title: 'Menu mis à jour',
                description: "Le menu \"{$this->menu->name}\" a été mis à jour avec succès.",
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
     * Helper method to extract the public ID from a Cloudinary URL.
     */
    private function getPublicIdFromUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '';
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Renders the Edit view.
     */
    public function render()
    {
        return view('livewire.admin.components.menus.edit');
    }
}
