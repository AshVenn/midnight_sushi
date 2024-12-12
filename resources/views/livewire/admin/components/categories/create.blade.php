<x-card title="Gestion des Catégories" separator>
    <x-slot:menu>
        <x-button label="Ajouter une Catégorie" link="{{ route('admin.categories.create') }}" class="btn-primary" icon="o-plus" responsive/>
    </x-slot:menu>

    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 items-center">
        {{-- Barre de recherche --}}
        <div class="flex-grow">
            <x-input wire:model.lazy="search" placeholder="Rechercher des catégories..." icon="o-magnifying-glass" autocomplete="off" wire:enter="search" />
        </div>
    </div>

    {{-- Composant Table --}}
    <x-table :headers="$headers" :rows="$categories" :sort-by="$sortBy" with-pagination>
        {{-- Colonne Nom --}}
        @scope('cell_name', $category)
            <div>{{ $category->name }}</div>
        @endscope

        {{-- Colonne Description --}}
        @scope('cell_description', $category)
            <div>{{ $category->description }}</div>
        @endscope

        {{-- Colonne Actions --}}
        @scope('cell_actions', $category)
            <div class="flex gap-2">
                <x-button wire:click="confirmDelete({{ $category->id }})" class="btn-error" icon="o-trash" />
                <x-button link="{{ route('admin.categories.edit', $category) }}" class="btn-primary" icon="o-pencil" />
            </div>
        @endscope
    </x-table>

    {{-- Modal de Confirmation de Suppression --}}
    <x-modal wire:model="showDeleteModal" title="Confirmer la Suppression">
        <p>Êtes-vous sûr(e) de vouloir supprimer la catégorie "{{ $deletingCategoryName }}" ?</p>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelDelete" />
            <x-button label="Supprimer" class="btn-error" wire:click="deleteCategory" />
        </x-slot:actions>
    </x-modal>
</x-card>

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
