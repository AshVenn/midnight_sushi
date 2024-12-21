<x-card title="Créer une Catégorie">
    <x-form wire:submit.prevent="save">
        {{-- Nom --}}
        <x-input 
            wire:model.defer="name" 
            label="Nom de la Catégorie" 
            placeholder="Entrez le nom de la catégorie" 
        />

        {{-- Description --}}
        <x-textarea 
            wire:model.defer="description" 
            label="Description" 
            placeholder="Entrez une brève description de la catégorie" 
            rows="5" 
        />

        {{-- Boutons d'Action --}}
        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.categories.index') }}" />
            <x-button label="Créer la Catégorie" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
