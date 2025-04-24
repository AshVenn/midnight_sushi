<?php
// app/Livewire/MenusProducts.php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Menu, Product, MenuTemplate, CustomMenu};
use Illuminate\Support\Facades\Session;
use Mary\Traits\Toast;

class MenusProducts extends Component
{
    use WithPagination, Toast;

    public string $searchTerm = '';
    public string $selectedAllergen = '';

    // tiroirs/drawers
    public bool $showMenuDrawer    = false;
    public bool $showProductDrawer = false;
    public bool $showBuilderDrawer = false;

    public $selectedMenu;
    public $selectedProduct;
    public $buildingTemplate;          // MenuTemplate en cours de création
    public array $templateSelections = []; // [cat_id][product_id] => qty

    protected $queryString = ['searchTerm', 'selectedAllergen'];

    /* ------------------------------------------------------------------ */
    /*  Évènements Livewire                                               */
    /* ------------------------------------------------------------------ */
    public function updatingSearchTerm()        { $this->resetPage(); }
    public function updatingSelectedAllergen()  { $this->resetPage(); }

    /* ------------------------------------------------------------------ */
    /*  Affichage détails                                                 */
    /* ------------------------------------------------------------------ */
    public function showMenuDetails(int $id)
    {
        $this->selectedMenu = Menu::with(['products.category', 'reviews'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->find($id);

        $this->showMenuDrawer = true;
    }

    public function showProductDetails(int $id)
    {
        $this->selectedProduct = Product::with(['category', 'reviews'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->find($id);

        $this->showProductDrawer = true;
    }

    /* ------------------------------------------------------------------ */
    /*  Ajout au panier                                                   */
    /* ------------------------------------------------------------------ */
    public function addToCart(string $type, int $id)
    {
        $cart  = Session::get('cart', []);
        $model = $type === 'menu' ? Menu::find($id) : Product::find($id);
        if (!$model) {
            return;
        }

        $cart[$type][$id] = $cart[$type][$id] ?? [
            'id'       => $model->id,
            'name'     => $model->name,
            'price'    => $type === 'menu' ? $model->price : $model->final_price,
            'image'    => $model->image,
            'quantity' => 0,
        ];
        $cart[$type][$id]['quantity']++;

        Session::put('cart', $cart);
        $this->dispatch('cartUpdated');
    }

    /* ------------------------------------------------------------------ */
    /*  Builder de menu personnalisé                                      */
    /* ------------------------------------------------------------------ */
    public function buildMenu(int $templateId)
    {
        $this->buildingTemplate  = MenuTemplate::with('categories.products')->findOrFail($templateId);
        $this->templateSelections = [];
        $this->showBuilderDrawer  = true;
    }

    public function addCustomMenuToCart()
    {
        $tpl = $this->buildingTemplate;
        $errors = [];

        foreach ($tpl->categories as $cat) {
            $qty = collect($this->templateSelections[$cat->id] ?? [])->sum();
            if ($qty < $cat->pivot->min_items) {
                $errors[] = "Min {$cat->pivot->min_items} {$cat->name}";
            }
            if ($qty > $cat->pivot->max_items) {
                $errors[] = "Max {$cat->pivot->max_items} {$cat->name}";
            }
        }

        if ($errors) {
            $this->error(
                title: "Trop de tentatives",
                description: implode(', ', $errors)
            );
            return;
        }

        // Création + attachement produits
        $custom = \DB::transaction(function () use ($tpl) {
            $cm = CustomMenu::create([
                'template_id' => $tpl->id,
                'user_id'     => auth()->id(),
            ]);

            foreach ($this->templateSelections as $catId => $prods) {
                foreach ($prods as $pid => $q) {
                    if ($q > 0) {
                        $cm->products()->attach($pid, [
                            'category_id' => $catId,
                            'quantity'    => $q,
                        ]);
                    }
                }
            }
            return $cm;
        });

        // Ajout dans le panier
        $cart                         = Session::get('cart', []);
        $cart['custom_menu'][$custom->id] = [
            'id'       => $custom->id,
            'name'     => $tpl->name . ' (personnalisé)',
            'price'    => $custom->price,
            'image'    => $tpl->image,
            'quantity' => 1,
        ];
        Session::put('cart', $cart);

        $this->dispatch('cartUpdated');
        $this->showBuilderDrawer = false;
    }

    /* ------------------------------------------------------------------ */
    /*  Rendu                                                             */
    /* ------------------------------------------------------------------ */
    public function render()
    {
        $menus = Menu::withAvg('reviews as reviews_avg_rating', 'rating')
            ->when($this->searchTerm, fn ($q) => $q->where('name', 'like', "%{$this->searchTerm}%"))
            ->paginate(6, ['*'], 'menusPage');

        $templates = MenuTemplate::paginate(6, ['*'], 'templatesPage');

        $products = Product::withAvg('reviews as reviews_avg_rating', 'rating')
            ->when($this->searchTerm, fn ($q) => $q->where('name', 'like', "%{$this->searchTerm}%"))
            ->where('is_available', true)
            ->whereHas('category', fn ($q) => $q->where('slug', '!=', 'boissons'))
            ->paginate(8, ['*'], 'productsPage');

        $drinks = Product::withAvg('reviews as reviews_avg_rating', 'rating')
            ->whereHas('category', fn ($q) => $q->where('slug', 'boissons'))
            ->paginate(8, ['*'], 'drinksPage');

        $allAllergens = Product::whereNotNull('allergens')
            ->pluck('allergens')
            ->flatten()
            ->unique()
            ->values()
            ->map(fn ($a) => ['id' => $a, 'name' => ucfirst($a)])
            ->prepend(['id' => '', 'name' => 'Tous les allergènes'])
            ->toArray();

        return view('livewire.menus-products', compact(
            'menus',
            'templates',
            'products',
            'drinks',
            'allAllergens'
        ));
    }
}
