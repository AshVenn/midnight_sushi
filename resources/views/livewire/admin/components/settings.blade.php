<x-card title="Admin Settings">
    <x-form wire:submit.prevent="save">
        {{-- First Name --}}
        <x-input wire:model="first_name" label="First Name" placeholder="Enter your first name" />

        {{-- Last Name --}}
        <x-input wire:model="last_name" label="Last Name" placeholder="Enter your last name" />

        {{-- Email --}}
        <x-input wire:model="email" label="Email" type="email" placeholder="Enter your email" />

        {{-- Password --}}
        <x-input wire:model="password" label="Password" type="password" placeholder="Enter new password (optional)" />
        <x-input wire:model="confirm_password" label="Confirm Password" type="password" placeholder="Confirm your password" />

        {{-- Avatar Upload --}}
        <x-file wire:model="avatar" label="Profile Picture" accept="image/*" />

        {{-- Avatar Preview --}}
        @if ($oldAvatarUrl)
            <img src="{{ $oldAvatarUrl }}" class="h-40 rounded-lg mt-2" alt="Current Avatar" />
        @endif

        @if ($avatar)
            <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="New Avatar Preview" />
        @endif

        {{-- Action Buttons --}}
        <x-slot:actions>
            <x-button label="Save Changes" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>

    {{-- Toast Message Placeholder --}}
    <x-toast />
</x-card>
