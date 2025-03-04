<x-card title="Créer une Catégorie">
    <x-form wire:submit.prevent="save">
        {{-- Nom --}}
        <x-input wire:model="name" label="Nom" placeholder="Entrez le nom de la catégorie" />

        {{-- Slug --}}
        <x-input wire:model="slug" label="Slug (optionnel)" placeholder="Laissez vide pour auto-générer" />

        {{-- Description --}}
        <x-textarea wire:model="description" label="Description" placeholder="Entrez une description" rows="4" />

        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('manager.categories.index') }}" />
            <x-button label="Créer la Catégorie" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
