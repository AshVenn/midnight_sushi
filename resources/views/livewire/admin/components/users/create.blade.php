<x-card title="Create User">
    <x-form wire:submit.prevent="save">
        <x-input wire:model="first_name" label="First Name" />
        <x-input wire:model="last_name" label="Last Name" />
        <x-input wire:model="email" label="Email" type="email" />
        <x-input wire:model="password" label="Password" type="password" />
        <x-select wire:model="role" label="Role" :options="[ 
            ['id' => 'admin', 'name' => 'Admin'], 
            ['id' => 'manager', 'name' => 'Manager'], 
            ['id' => 'customer', 'name' => 'Customer'], 
            ['id' => 'delivery_person', 'name' => 'Delivery Person'] 
        ]" placeholder="Select a role" />
        <x-file wire:model="avatar" label="Avatar" accept="image/*" />

        @if ($avatar)
            <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Preview" />
        @endif

        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('admin.users.index') }}" />
            <x-button label="Create User" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
