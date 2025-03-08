<x-card title="Gestion des Produits">
    <x-slot:menu>
        <x-button 
            label="Ajouter un Produit" 
            link="{{ route('admin.products.create') }}" 
            class="btn-primary" 
            icon="o-plus" 
            responsive 
        />
    </x-slot:menu>

    {{-- Filtres --}}
    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 w-full">
        {{-- Champ de recherche --}}
        <div class="w-full">
            <x-input 
                wire:model.lazy="search" 
                placeholder="Rechercher des produits (nom, slug)..." 
                icon="o-magnifying-glass" 
                autocomplete="off" 
            />
        </div>

        {{-- Filtre de catégorie --}}
        <div class="w-full md:w-1/3">
            <x-select 
                wire:model="categoryFilter" 
                :options="$categories" 
                placeholder="Filtrer par catégorie" 
                wire:change="updateCategoryFilter" 
            />
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
            <div class="text-xs text-gray-400">
                Slug: {{ $product->slug ?? '(none)' }}
            </div>
        @endscope

        {{-- Colonne Catégorie --}}
        @scope('cell_category.name', $product)
            {{ $product->category->name ?? 'Pas de catégorie' }}
        @endscope

        {{-- Colonne Prix Actuel (en tenant compte de la promotion) --}}
        @scope('cell_price', $product)
            @php
                // If there's a discount_price, you might want to show it, or show both
                $actualPrice = $product->price; // Using getPriceAttribute
            @endphp
            <div>
                {{ number_format($actualPrice, 2) }} CHF
            </div>
            @if ($product->discount_price)
                <div class="text-sm text-green-600">
                    Promo: {{ number_format($product->discount_price, 2) }} CHF
                </div>
            @endif
        @endscope

        {{-- Colonne Disponible ? --}}
        @scope('cell_is_available', $product)
            <x-button 
                wire:click="toggleAvailability({{ $product->id }})"
                :label="$product->is_available ? 'Oui (cliquer pour rendre indisponible)' : 'Non (cliquer pour rendre disponible)'" 
                class="{{ $product->is_available ? 'btn-success' : 'btn-warning' }}"
            />
        @endscope

        {{-- Colonne Actions --}}
        @scope('cell_actions', $product)
            <div class="flex space-x-2">
                {{-- Stop Promotion if discount_price is set --}}
                @if ($product->discount_price)
                    <x-button 
                        wire:click="stopPromotion({{ $product->id }})" 
                        class="btn-secondary" 
                        label="Stop Promo"
                    />
                @endif

                <x-button 
                    wire:click="confirmDelete({{ $product->id }})" 
                    class="btn-error" 
                    icon="o-trash" 
                />
                <x-button 
                    link="{{ route('admin.products.edit', $product) }}" 
                    class="btn-primary" 
                    icon="o-pencil" 
                />
            </div>
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
