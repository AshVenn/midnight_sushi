<!-- resources/views/livewire/auth/register.blade.php -->
<div class="w-full max-w-md mx-auto p-8 rounded shadow-sm">
    <h2 class="text-xl font-bold text-center mb-4">Create Your Account</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <x-form wire:submit="register">
        <x-input label="First Name" wire:model="first_name" required />
        <x-input label="Last Name" wire:model="last_name" required />
        <x-input label="Email" type="email" wire:model="email" required />
        <x-input label="Password" type="password" wire:model="password" required />

        @php
            $roles = [
                ['id' => 'admin', 'name' => 'Admin'],
                ['id' => 'customer', 'name' => 'Customer'],
                ['id' => 'manager', 'name' => 'Manager'],
                ['id' => 'delivery_person', 'name' => 'Delivery Person'],
            ];
        @endphp

        <x-select label="Role" :options="$roles" placeholder="Select a Role" wire:model="role" required />

        <div class="mt-6">
            <x-button label="Register" class="btn-primary w-full" type="submit" spinner="register" />
        </div>
    </x-form>
</div>
