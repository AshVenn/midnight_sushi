<x-card title="User Management" separator>
    <x-slot:menu>
        <x-button label="Create User" link="{{ route('admin.users.create') }}" class="btn-primary" />
    </x-slot:menu>

    {{-- Search Input --}}
    <x-input wire:model="search" placeholder="Search users..." icon="o-magnifying-glass" />

    {{-- Table Component --}}
    <x-table :headers="$headers" :rows="$users" with-pagination>
        {{-- Render Full Name --}}
        @scope('full_name', $user)
            <div>{{ $user->full_name }}</div>
        @endscope

        {{-- Render Email --}}
        @scope('email', $user)
            <div>{{ $user->email }}</div>
        @endscope

        {{-- Render Role --}}
        @scope('role', $user)
            <div>{{ ucfirst($user->role) }}</div>
        @endscope

        {{-- Render Actions --}}
        @scope('actions', $user)
            <div class="flex gap-2">
                {{-- Delete Button --}}
                <x-button wire:click="confirmDelete({{ $user->id }})" class="btn-error" icon="o-trash" />

                {{-- Edit Button --}}
                <x-button link="{{ route('admin.users.edit', ['user' => $user->id]) }}" class="btn-primary" icon="o-pencil" />
            </div>
        @endscope
    </x-table>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="showDeleteModal" title="Confirm Deletion" separator>
        <div class="text-center">
            Are you sure you want to delete <strong>{{ $deletingUserName }}</strong>? This action cannot be undone.
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.cancelDelete()" />
            <x-button label="Delete" class="btn-error" wire:click="deleteUser" />
        </x-slot:actions>
    </x-modal>
</x-card>
