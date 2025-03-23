<x-card title="Gestion des Utilisateurs" separator>
    <x-slot:menu>
        <x-button label="Ajouter un Utilisateur"
                  link="{{ route('admin.users.create') }}"
                  class="btn-primary"
                  icon="o-plus"
                  responsive />
    </x-slot:menu>

    {{-- Barre de recherche et filtre --}}
    <div class="flex flex-col md:flex-row md:items-center md:gap-4 gap-2 mb-4">
    <div class="w-full md:w-3/5">
        <x-input wire:model.lazy="search"
                 placeholder="Rechercher..."
                 icon="o-magnifying-glass"
                 class="w-full"
                 autocomplete="off" />
    </div>

    <div class="w-full md:w-2/5">
        <x-select wire:model="roleFilter"
                  wire:change="updateFilter"
                  :options="[
                      ['id' => 'all', 'name' => 'Tous les rôles'],
                      ['id' => 'admin', 'name' => 'Administrateurs'],
                      ['id' => 'manager', 'name' => 'Responsables'],
                      ['id' => 'delivery_person', 'name' => 'Livreurs'],
                      ['id' => 'customer', 'name' => 'Clients'],
                  ]"
                  option-label="name"
                  option-value="id"
                  class="w-full" />
    </div>
</div>

    {{-- Tableau des utilisateurs --}}
    <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
        @scope('cell_avatar', $user)
            <x-avatar :image="$user->avatar" class="!w-10 !h-10 rounded-full" />
        @endscope

        @scope('cell_role', $user)
            <x-badge :value="$user->role_text"
                    class="border {{ $user->role_color }} font-medium px-2 py-1 text-sm rounded-full" />
        @endscope


        @scope('cell_status', $user)
            <x-badge :value="$user->status_text"
                     class="border {{ $user->status_color }} font-medium px-2 py-1 text-sm rounded-full" />
        @endscope

        @scope('cell_actions', $user)
            <div class="flex gap-2">
                {{-- Bannir / Débannir --}}
                @if ($user->role !== 'admin')
                    <x-button icon="o-x-mark"
                              class="{{ $user->status === 'active' ? 'btn-error' : 'btn-success' }}"
                              wire:click="confirmBan({{ $user->id }}, '{{ $user->status }}')"
                              :label="$user->status === 'active' ? 'Bannir' : 'Débannir'"
                              spinner />
                @endif

                {{-- Supprimer --}}
                @if ($user->role !== 'admin' && !$user->hasOrders)
                    <x-button icon="o-trash"
                              class="btn-error"
                              wire:click="confirmDelete({{ $user->id }})"
                              spinner />
                @endif

                {{-- Modifier --}}
                <x-button icon="o-pencil"
                          link="{{ route('admin.users.edit', $user) }}"
                          class="btn-primary" />
            </div>
        @endscope
    </x-table>

    {{-- Modal Bannissement --}}
    <x-modal wire:model="showBanModal"
             title="{{ $currentStatus === 'active' ? 'Confirmer le bannissement' : 'Confirmer le débannissement' }}"
             separator>
        <p>Êtes-vous sûr de vouloir
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
             title="Confirmer la suppression"
             separator>
        <p>Êtes-vous certain de vouloir supprimer définitivement
            <strong>{{ $deletingUserName }}</strong> ?
        </p>
        <p class="text-red-500 text-sm mt-2">Cette action est irréversible !</p>

        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.cancelDelete()" />
            <x-button label="Supprimer"
                      class="btn-error"
                      wire:click="deleteUser"
                      spinner />
        </x-slot:actions>
    </x-modal>
</x-card>
