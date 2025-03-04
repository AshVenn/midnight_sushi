<?php

namespace App\Livewire\Manager\Components\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $categoryFilter = 'all'; 
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showDeleteModal = false;
    public ?int $deletingProductId = null;
    public ?string $deletingProductName = null;

    protected $listeners = [
        'stopPromotionConfirmed',
        'toggleAvailabilityConfirmed',
    ];

    /**
     * Réinitialiser la pagination lors de la recherche ou des changements de filtre.
     */
    public function search(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updateCategoryFilter(): void
    {
        $this->products();
    }

    /**
     * Confirmer la suppression d'un produit spécifique.
     */
    public function confirmDelete(int $productId): void
    {
        try {
            $product = Product::findOrFail($productId);

            $this->deletingProductId = $product->id;
            $this->deletingProductName = $product->name;
            $this->showDeleteModal = true;

        } catch (\Exception $e) {
            $this->error(title:'Erreur',description: 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Annuler l'action de suppression.
     */
    public function cancelDelete(): void
    {
        $this->reset(['showDeleteModal', 'deletingProductId', 'deletingProductName']);
    }

    /**
     * Supprimer le produit sélectionné et son image Cloudinary.
     */
    public function deleteProduct(): void
    {
        $authenticatedUser = Auth::user();

        if (!$authenticatedUser) {
            $this->error(
                title: 'Erreur',
                description: 'Utilisateur non authentifié.'
            );
            return;
        }

        if ($this->deletingProductId) {
            $product = Product::find($this->deletingProductId);

            if ($product) {
                // Supprime l'image Cloudinary si elle existe
                if ($product->image) {
                    $publicId = pathinfo($product->image, PATHINFO_FILENAME);
                    Cloudinary::destroy($publicId);
                }

                $product->delete();
                $this->success(
                    title: 'Produit supprimé.',
                    description: 'Le produit a été supprimé avec succès.'
                );
            }

            $this->cancelDelete();
        }
    }

    /**
     * Action: Stop Promotion (set discount_price to null, promotion_start/end to null)
     */
    public function stopPromotion(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            $this->error('Produit introuvable');
            return;
        }

        $product->update([
            'discount_price' => null,
            'promotion_start' => null,
            'promotion_end' => null,
        ]);

        $this->success(
            title: 'Promotion arrêtée',
            description: "La promotion pour {$product->name} a été arrêtée."
        );
    }

    /**
     * Action: Toggle Availability (flip is_available)
     */
    public function toggleAvailability(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) {
            $this->error('Produit introuvable');
            return;
        }

        $product->update(['is_available' => !$product->is_available]);

        $this->success(
            title: 'Disponibilité mise à jour',
            description: $product->is_available 
                ? "Le produit {$product->name} est maintenant disponible." 
                : "Le produit {$product->name} est maintenant indisponible."
        );
    }

    /**
     * Récupérer les produits paginés avec recherche et filtres de catégorie.
     */
    public function products()
    {
        return Product::query()
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%")
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->categoryFilter !== 'all', function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->sortBy['column'], function ($query) {
                $query->orderBy($this->sortBy['column'], $this->sortBy['direction']);
            })
            ->paginate(10);
    }

    /**
     * Récupérer les catégories pour le filtre de catégorie.
     */
    public function categories(): array
    {
        return Category::select(['id', 'name'])
            ->get()
            ->map(fn($category) => ['id' => $category->id, 'name' => $category->name])
            ->prepend(['id' => 'all', 'name' => 'Toutes les catégories'])
            ->toArray();
    }

    /**
     * Définir les en-têtes du tableau.
     */
    public function headers(): array
    {
        return [
            ['key' => 'image',         'label' => 'Image',         'sortable' => false],
            ['key' => 'name',          'label' => 'Nom',           'sortable' => true],
            ['key' => 'category.name', 'label' => 'Catégorie',     'sortable' => false],
            ['key' => 'price',         'label' => 'Prix Actuel',   'sortable' => true],
            ['key' => 'is_available',  'label' => 'Disponible ?',  'sortable' => false],
            ['key' => 'actions',       'label' => 'Actions',       'sortable' => false],
        ];
    }

    /**
     * Gérer le tri des colonnes.
     */
    public function sortBy(string $column): void
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column']    = $column;
            $this->sortBy['direction'] = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Afficher la vue du composant.
     */
    public function render()
    {
        return view('livewire.manager.components.products.index', [
            'products'   => $this->products(),
            'headers'    => $this->headers(),
            'categories' => $this->categories(),
        ]);
    }
}
