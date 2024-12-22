@php
  $dateConfig = [
    'altFormat'  => 'j F, Y',
    'dateFormat' => 'Y-m-d',
   ];
@endphp

<x-card title="Modifier le Produit">
    <x-form wire:submit.prevent="save">
        {{-- Nom --}}
        <x-input wire:model="name" label="Nom" placeholder="Entrez le nom du produit" />

        {{-- Slug --}}
        <x-input wire:model="slug" label="Slug (optionnel)" placeholder="Laissez vide pour auto-générer" />

        {{-- Prix --}}
        <x-input wire:model="price" label="Prix" type="number" step="0.01" placeholder="Entrez le prix du produit" />

        {{-- Discount Price + Promotion Start / End --}}
        <x-input wire:model="discount_price" label="Prix Promo" type="number" step="0.01" placeholder="Entrez le prix promo (facultatif)" />
        <x-datepicker  wire:model="promotion_start" label="Début Promotion (facultatif)" />
        <x-datepicker  wire:model="promotion_end" label="Fin Promotion (facultatif)" />

        {{-- Description --}}
        <x-textarea wire:model="description" label="Description" placeholder="Entrez la description du produit" rows="4" />


        {{-- Allergènes --}}
        <x-input 
            wire:model="allergensString" 
            label="Allergènes (séparés par des virgules)-facultatif"
            placeholder="Exemple: poisson, soja, crustacés"
        />


        {{-- Catégorie --}}
        <x-select 
            wire:model="category_id" 
            label="Catégorie" 
            :options="$categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])" 
            placeholder="Sélectionnez une catégorie" 
        />

        {{-- Disponibilité --}}
        <x-checkbox wire:model="is_available" label="Disponible ?" />

        {{-- Image --}}
        <x-file wire:model="image" label="Nouvelle Image (facultatif)" accept="image/*" />
        @if ($oldImageUrl)
            <img src="{{ $oldImageUrl }}" class="h-40 rounded-lg mt-2" alt="Image actuelle" />
        @endif
        @if ($image)
            <img src="{{ $image->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Aperçu de la nouvelle image" />
        @endif

        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.products.index') }}" />
            <x-button label="Enregistrer les Modifications" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
