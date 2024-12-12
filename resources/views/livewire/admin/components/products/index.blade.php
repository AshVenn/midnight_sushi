<x-card title="Gestion des Produits">
    <x-slot:menu>
        <x-button label="Ajouter un Produit" link="{{ route('admin.products.create') }}" class="btn-primary" icon="o-plus" responsive />
    </x-slot:menu>

    {{-- Filtres --}}
    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 w-full">
        {{-- Champ de recherche --}}
        <div class="w-full">
            <x-input wire:model.lazy="search" placeholder="Rechercher des produits..." icon="o-magnifying-glass" autocomplete="off" />
        </div>

        {{-- Filtre de catégorie --}}
        <div class="w-full md:w-1/3">
            <x-select wire:model="categoryFilter" :options="$categories" placeholder="Filtrer par catégorie" wire:change="updateCategoryFilter" />
        </div>
    </div>

    {{-- Tableau --}}
    <x-table :headers="$headers" :rows="$products" :sort-by="$sortBy" with-pagination>
        {{-- Colonne Image --}}
        @scope('cell_image', $product)
            <x-avatar image="{{ $product->image ?? '/empty-image.jpg' }}" class="!w-16 !h-16" />
        @endscope

        {{-- Colonne Nom --}}
        @scope('cell_name', $product)
            {{ $product->name }}
        @endscope

        {{-- Colonne Catégorie --}}
        @scope('cell_category.name', $product)
            {{ $product->category->name ?? 'Pas de catégorie' }}
        @endscope

        {{-- Colonne Prix --}}
        @scope('cell_price', $product)
            {{ number_format($product->price, 2) }} €
        @endscope

        {{-- Colonne Actions --}}
        @scope('cell_actions', $product)
            <x-button wire:click="confirmDelete({{ $product->id }})" class="btn-error" icon="o-trash" />
            <x-button link="{{ route('admin.products.edit', $product) }}" class="btn-primary" icon="o-pencil" />
        @endscope
    </x-table>

    {{-- Modal de confirmation de suppression --}}
    <x-modal wire:model="showDeleteModal" title="Confirmer la suppression">
        <p>Êtes-vous sûr de vouloir supprimer <strong>{{ $deletingProductName }}</strong> ? Cette action est irréversible.</p>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelDelete" />
            <x-button label="Supprimer" class="btn-error" wire:click="deleteProduct" />
        </x-slot:actions>
    </x-modal>
</x-card>
