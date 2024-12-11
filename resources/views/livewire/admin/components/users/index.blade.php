<x-card title="User Management" separator>
    <x-slot:menu>
        <x-button label="Add User" link="{{ route('admin.users.create') }}" class="btn-primary" icon="o-plus" responsive />
    </x-slot:menu>

    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 items-center">
        {{-- Search Input --}}
        <div class="flex-grow w-full">
            <x-input wire:model.lazy="search" placeholder="Search users..." icon="o-magnifying-glass" autocomplete="off"/>
        </div>

        {{-- Role Filter --}}
        @php
            $roleOptions = [
                ['id' => 'all', 'name' => 'All Roles'],
                ['id' => 'admin', 'name' => 'Admin'],
                ['id' => 'manager', 'name' => 'Manager'],
                ['id' => 'delivery_person', 'name' => 'Delivery Person'],
                ['id' => 'customer', 'name' => 'Customer'],
            ];
        @endphp
        <div class="w-full md:w-1/3">
            <x-select wire:model="roleFilter" wire:change="updateFilter" :options="$roleOptions" placeholder="Filter by Role" />
        </div>
    </div>

    {{-- Table Component --}}
    <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
        {{-- Render Avatar --}}
        @scope('cell_avatar', $user)
            <x-avatar :image="$user->avatar" class="!w-16 !h-16" />
        @endscope

        {{-- Render Full Name --}}
        @scope('cell_full_name', $user)
            <div>{{ $user->full_name }}</div>
        @endscope

        {{-- Render Email --}}
        @scope('cell_email', $user)
            <div>{{ $user->email }}</div>
        @endscope

        {{-- Render Role --}}
        @scope('cell_role', $user)
            <div>{{ ucfirst($user->role) }}</div>
        @endscope

        {{-- Render Actions --}}
        @scope('cell_actions', $user)
            <div class="flex gap-2">
                <x-button wire:click="confirmDelete({{ $user->id }})" class="btn-error" icon="o-trash" />
                <x-button link="{{ route('admin.users.edit', ['user' => $user->id]) }}" class="btn-primary" icon="o-pencil" />
            </div>
        @endscope
    </x-table>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="showDeleteModal" title="Confirm Deletion">
        <p>Are you sure you want to delete <strong>{{ $deletingUserName }}</strong>? This action cannot be undone.</p>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="cancelDelete" />
            <x-button label="Delete" class="btn-error" wire:click="deleteUser" />
        </x-slot:actions>
    </x-modal>
</x-card>
