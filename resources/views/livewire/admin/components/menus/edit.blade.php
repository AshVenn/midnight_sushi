<x-card title="Modifier le Menu">
    <x-form wire:submit.prevent="save">
        <div class="grid gap-4 md:grid-cols-2">
            {{-- Name --}}
            <x-input 
                wire:model="name" 
                label="Nom du Menu"
                placeholder="Entrez le nom du menu"
            />

            {{-- Slug --}}
            <x-input 
                wire:model="slug" 
                label="Slug (optionnel)"
                placeholder="Laissez vide pour auto-générer"
            />
        </div>

        {{-- Description --}}
        <x-textarea 
            wire:model="description" 
            label="Description" 
            placeholder="Entrez une description (optionnel)" 
        />

        <div class="grid gap-4 md:grid-cols-2 mt-4">
            {{-- Price --}}
            <x-input 
                wire:model="price" 
                label="Prix"
                type="number"
                step="0.01"
            />

            {{-- Discount Price --}}
            <x-input 
                wire:model="discount_price" 
                label="Prix Promo (optionnel)"
                type="number"
                step="0.01"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-2 mt-4">
            {{-- Promotion Start --}}
            <x-datepicker
                wire:model="promotion_start"
                label="Début Promo (optionnel)"
            />

            {{-- Promotion End --}}
            <x-datepicker
                wire:model="promotion_end"
                label="Fin Promo (optionnel)"
            />
        </div>

        {{-- Image Upload --}}
        <div class="mt-4">
            <label class="block font-semibold mb-2">Image du Menu (optionnel)</label>
            <x-file 
                wire:model="image" 
                accept="image/*" 
                class="block"
            />

            @if ($oldImageUrl)
                <div class="mt-2">
                    <span class="block font-semibold">Image Actuelle :</span>
                    <img src="{{ $oldImageUrl }}" alt="{{ $menu->name }}" class="h-40 rounded-lg">
                </div>
            @endif

            @if ($image)
                <div class="mt-2">
                    <span class="block font-semibold">Nouvelle Image :</span>
                    <img src="{{ $image->temporaryUrl() }}" alt="Nouvelle image" class="h-40 rounded-lg">
                </div>
            @endif
        </div>

        <hr class="my-4">

        <h3 class="text-lg font-semibold mb-2">Produits dans le Menu</h3>
        <p class="text-sm text-gray-500 mb-3">
            Ajoutez, supprimez ou modifiez les produits de ce menu avec la quantité souhaitée.
        </p>

        @foreach($menuItems as $index => $item)
            <div class="flex items-center space-x-4 mb-2">
                {{-- Select Product --}}
                <x-select
                    wire:model="menuItems.{{ $index }}.product_id"
                    :options="$allProducts->map(fn($p) => ['id' => $p->id, 'name' => $p->name])"
                    placeholder="Choisir un produit"
                    class="w-full md:w-1/2"
                />

                {{-- Quantity --}}
                <x-input
                    wire:model="menuItems.{{ $index }}.quantity"
                    type="number"
                    min="1"
                    label="Qté"
                    class="w-24"
                />

                <x-button
                    icon="o-trash"
                    class="btn-error"
                    wire:click="removeProductLine({{ $index }})"
                    label="Suppr."
                />
            </div>
        @endforeach

        <x-button 
            label="Ajouter un Produit" 
            class="btn-secondary" 
            wire:click.prevent="addProductLine"
            icon="o-plus"
        />

        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.menus.index') }}" />
            <x-button label="Enregistrer" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
