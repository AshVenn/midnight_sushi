<x-card title="Gestion des Utilisateurs" separator>
    <x-slot:menu>
        <x-button label="Ajouter un Utilisateur"
                  link="{{ route('manager.users.create') }}"
                  class="btn-primary"
                  icon="o-plus"
                  responsive />
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
                ['id' => 'delivery_person', 'name' => 'Livreur'],
                ['id' => 'customer', 'name' => 'Client'],
            ];
        @endphp
        <div class="w-full md:w-1/3">
            <x-select wire:model="roleFilter" wire:change="updateFilter" :options="$roleOptions" placeholder="Filtrer par Rôle" />
        </div>
    </div>

    {{-- Tableau des utilisateurs --}}
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
            <div>{{ $user->role_text }}</div>
        @endscope

        {{-- Colonne Statut --}}
        @scope('cell_status', $user)
            <x-badge :value="$user->status_text"
                     class="border {{ $user->status_color }} font-medium px-2 py-1 text-sm rounded-full" />
        @endscope

        {{-- Colonne Actions --}}
        @scope('cell_actions', $user)
            <div class="flex gap-2">
                {{-- Bannir / Débannir --}}
                <x-button icon="o-x-mark"
                          class="{{ $user->status === 'active' ? 'btn-error' : 'btn-success' }}"
                          wire:click="confirmBan({{ $user->id }}, '{{ $user->status }}')"
                          :label="$user->status === 'active' ? 'Bannir' : 'Débannir'"
                          spinner />
                {{-- Supprimer --}}
                @if (!$user->hasOrders)
                    <x-button icon="o-trash"
                              class="btn-error"
                              wire:click="confirmDelete({{ $user->id }})"
                              spinner />
                @endif
                {{-- Modifier --}}
                <x-button icon="o-pencil"
                          link="{{ route('manager.users.edit', ['user' => $user->id]) }}"
                          class="btn-primary" />
            </div>
        @endscope
    </x-table>

    {{-- Modal Bannissement --}}
    <x-modal wire:model="showBanModal"
             title="{{ $currentStatus === 'active' ? 'Confirmer le bannissement' : 'Confirmer le débannissement' }}"
             separator>
        <p>Êtes-vous sûr(e) de vouloir 
            {{ $currentStatus === 'active' ? 'bannir' : 'débannir' }} 
            <strong>{{ $banningUserName }}</strong> ?
        </p>
        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.cancelBan()" />
            <x-button :label="$currentStatus === 'active' ? 'Bannir' : 'Débannir'"
                      class="btn-error"
                      wire:click="performBan"
                      spinner />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Suppression --}}
    <x-modal wire:model="showDeleteModal"
             title="Confirmer la Suppression"
             separator>
        <p>Êtes-vous certain(e) de vouloir supprimer définitivement
            <strong>{{ $deletingUserName }}</strong> ?
        </p>
        <p class="text-red-500 text-sm mt-2">Cette action est irréversible !</p>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelDelete" />
            <x-button label="Supprimer" class="btn-error" wire:click="deleteUser" />
        </x-slot:actions>
    </x-modal>
</x-card>
