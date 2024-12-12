<x-card title="Gestion des Utilisateurs" separator>
    <x-slot:menu>
        <x-button label="Ajouter un Utilisateur" link="{{ route('admin.users.create') }}" class="btn-primary" icon="o-plus" responsive />
    </x-slot:menu>

    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 items-center">
        {{-- Barre de recherche --}}
        <div class="flex-grow w-full">
            <x-input wire:model.lazy="search" placeholder="Rechercher des utilisateurs..." icon="o-magnifying-glass" autocomplete="off" />
        </div>

        {{-- Filtre des rôles --}}
        @php
            $roleOptions = [
                ['id' => 'all', 'name' => 'Tous les Rôles'],
                ['id' => 'admin', 'name' => 'Administrateur'],
                ['id' => 'manager', 'name' => 'Responsable'],
                ['id' => 'delivery_person', 'name' => 'Livreur'],
                ['id' => 'customer', 'name' => 'Client'],
            ];
        @endphp
        <div class="w-full md:w-1/3">
            <x-select wire:model="roleFilter" wire:change="updateFilter" :options="$roleOptions" placeholder="Filtrer par Rôle" />
        </div>
    </div>

    {{-- Table Component --}}
    <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
        {{-- Colonne Avatar --}}
        @scope('cell_avatar', $user)
            <x-avatar :image="$user->avatar" class="!w-16 !h-16" />
        @endscope

        {{-- Colonne Nom Complet --}}
        @scope('cell_full_name', $user)
            <div>{{ $user->full_name }}</div>
        @endscope

        {{-- Colonne Email --}}
        @scope('cell_email', $user)
            <div>{{ $user->email }}</div>
        @endscope

        {{-- Colonne Rôle --}}
        @scope('cell_role', $user)
            @php
                $roleTranslations = [
                    'admin' => 'Administrateur',
                    'manager' => 'Responsable',
                    'delivery_person' => 'Livreur',
                    'customer' => 'Client',
                ];
            @endphp
            <div>{{ $roleTranslations[$user->role] ?? ucfirst($user->role) }}</div>
        @endscope

        {{-- Colonne Actions --}}
        @scope('cell_actions', $user)
            <div class="flex gap-2">
                <x-button wire:click="confirmDelete({{ $user->id }})" class="btn-error" icon="o-trash" />
                <x-button link="{{ route('admin.users.edit', ['user' => $user->id]) }}" class="btn-primary" icon="o-pencil" />
            </div>
        @endscope
    </x-table>

    {{-- Modal de Confirmation de Suppression --}}
    <x-modal wire:model="showDeleteModal" title="Confirmer la Suppression">
        <p>Êtes-vous sûr(e) de vouloir supprimer <strong>{{ $deletingUserName }}</strong> ? Cette action est irréversible.</p>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelDelete" />
            <x-button label="Supprimer" class="btn-error" wire:click="deleteUser" />
        </x-slot:actions>
    </x-modal>
</x-card>
