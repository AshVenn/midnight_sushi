<x-card title="Create User">
    <x-form wire:submit.prevent="save">
        {{-- First Name --}}
        <x-input wire:model="first_name" label="First Name" placeholder="Enter user's first name" />

        {{-- Last Name --}}
        <x-input wire:model="last_name" label="Last Name" placeholder="Enter user's last name" />

        {{-- Email --}}
        <x-input wire:model="email" label="Email" type="email" placeholder="Enter user's email address" />

        {{-- Password --}}
        <x-input wire:model="password" label="Password" type="password" placeholder="Enter a strong password" />

        {{-- Role --}}
        <x-select wire:model="role" label="Role" :options="[ 
            ['id' => 'admin', 'name' => 'Admin'], 
            ['id' => 'manager', 'name' => 'Manager'], 
            ['id' => 'customer', 'name' => 'Customer'], 
            ['id' => 'delivery_person', 'name' => 'Delivery Person'] 
        ]" placeholder="Select a role" />

        {{-- Action Buttons --}}
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('admin.users.index') }}" />
            <x-button label="Create User" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
