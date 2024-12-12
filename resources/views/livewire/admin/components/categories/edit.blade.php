<x-card title="Modifier la Catégorie">
    <x-form wire:submit.prevent="save">
        {{-- Nom --}}
        <x-input 
            wire:model="name" 
            label="Nom de la Catégorie" 
            placeholder="Entrez le nom de la catégorie" 
        />

        {{-- Description --}}
        <x-textarea 
            wire:model="description" 
            label="Description" 
            placeholder="Entrez une brève description de la catégorie" 
            rows="5" 
        />

        {{-- Boutons d'Action --}}
        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.categories.index') }}" />
            <x-button label="Enregistrer les Modifications" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
