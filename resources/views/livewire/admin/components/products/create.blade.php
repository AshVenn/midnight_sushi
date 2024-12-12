<x-card title="Créer un Produit">
    <x-form wire:submit.prevent="save">
        {{-- Nom du Produit --}}
        <x-input wire:model="name" label="Nom" placeholder="Entrez le nom du produit" />

        {{-- Prix --}}
        <x-input wire:model="price" label="Prix" type="number" step="0.01" placeholder="Entrez le prix du produit" />

        {{-- Description --}}
        <x-textarea wire:model="description" label="Description" placeholder="Entrez la description du produit" rows="4" />

        {{-- Allergènes --}}
        <x-input wire:model="allergens" label="Allergènes" placeholder="Entrez les allergènes (facultatif)" />

        {{-- Catégorie --}}
        <x-select wire:model="category_id" label="Catégorie" :options="$categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])" placeholder="Sélectionnez une catégorie" />

        {{-- Téléchargement de l'Image --}}
        <x-file wire:model="image" label="Image" accept="image/*"/>
        
        @if ($image)
            <img src="{{ $image->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Aperçu" />
        @endif

        {{-- Boutons d'Action --}}
        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.products.index') }}" />
            <x-button label="Créer le Produit" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
