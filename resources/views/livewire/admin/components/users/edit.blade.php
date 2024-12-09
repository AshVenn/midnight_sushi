<x-card title="Edit User">
    <x-form wire:submit.prevent="save">
        <x-input wire:model="first_name" label="First Name" />
        <x-input wire:model="last_name" label="Last Name" />
        <x-input wire:model="email" label="Email" type="email" />
        <x-select wire:model="role" label="Role" :options="[
            ['value' => 'admin', 'label' => 'Admin'],
            ['value' => 'customer', 'label' => 'Customer'],
            ['value' => 'manager', 'label' => 'Manager'],
            ['value' => 'delivery_person', 'label' => 'Delivery Person'],
        ]" placeholder="Select Role" />
        <x-file wire:model="avatar" label="Avatar" accept="image/*" />

        @if ($oldAvatarUrl)
            <img src="{{ $oldAvatarUrl }}" class="h-40 rounded-lg mt-2" alt="Current Avatar" />
        @endif

        @if ($avatar)
            <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="New Preview" />
        @endif

        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('admin.users.index') }}" />
            <x-button label="Update User" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
