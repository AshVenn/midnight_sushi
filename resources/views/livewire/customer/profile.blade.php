<div class="p-4 md:p-6">
    <x-header title="Profil utilisateur" separator>
        <x-slot:actions>
            <x-button label="Enregistrer" icon="o-check" class="btn-primary" type="submit" />
        </x-slot:actions>
    </x-header>

    <x-form wire:submit="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input label="Prénom" wire:model="user.first_name" />
            <x-input label="Nom" wire:model="user.last_name" />
            <x-input label="Email" wire:model="user.email" />
            <x-input label="Téléphone" wire:model="user.phone_number" />
        </div>
    </x-form>
</div>