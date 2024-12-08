<x-card title="Edit User">
    <x-form wire:submit.prevent="save">
        {{-- First Name --}}
        <x-input 
            wire:model="first_name" 
            label="First Name" 
            placeholder="Enter the user's first name" 
        />

        {{-- Last Name --}}
        <x-input 
            wire:model="last_name" 
            label="Last Name" 
            placeholder="Enter the user's last name" 
        />

        {{-- Email --}}
        <x-input 
            wire:model="email" 
            label="Email" 
            type="email" 
            placeholder="Enter the user's email address" 
        />

        {{-- Role --}}
        <x-select 
            wire:model="role" 
            label="Role" 
            :options="[ 
                ['id' => 'admin', 'name' => 'Admin'], 
                ['id' => 'manager', 'name' => 'Manager'], 
                ['id' => 'customer', 'name' => 'Customer'], 
                ['id' => 'delivery_person', 'name' => 'Delivery Person'] 
            ]" 
            placeholder="Select a role" 
        />

        {{-- Action Buttons --}}
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('admin.users.index') }}" />
            <x-button label="Save Changes" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
