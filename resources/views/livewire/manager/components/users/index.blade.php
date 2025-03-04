<x-card title="Gestion des Utilisateurs">
    <x-input wire:model.lazy="search" placeholder="Rechercher un utilisateur..." icon="o-magnifying-glass" wire:enter="search" />
    <x-table :headers="[
        ['key' => 'name', 'label' => 'Nom'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'role', 'label' => 'Rôle'],
        ['key' => 'actions', 'label' => 'Actions']
    ]" :rows="$users" with-pagination>

        @scope('cell_name', $user)
            <div>{{ $user->name }}</div>
        @endscope

        @scope('cell_email', $user)
            <div>{{ $user->email }}</div>
        @endscope

        @scope('cell_role', $user)
            <div class="badge bg-secondary">{{ ucfirst($user->role) }}</div>
        @endscope

        @scope('cell_actions', $user)
            <x-button link="{{ route('manager.users.edit', $user) }}" class="btn-primary" label="Modifier" />
        @endscope

    </x-table>
</x-card>
