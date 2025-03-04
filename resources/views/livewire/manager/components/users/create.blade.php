<x-card title="Créer un nouvel utilisateur">
    <x-form wire:submit="save">
        <x-input wire:model.defer="name" label="Nom" required />
        <x-input wire:model.defer="email" label="Email" type="email" required />
        <x-input wire:model.defer="password" label="Mot de passe" type="password" required />
        <x-select wire:model.defer="role" label="Rôle" required>
            <option value="client">Client</option>
            <option value="employee">Employé</option>
        </x-select>
        <x-button type="submit" class="btn-primary" label="Créer" />
    </x-form>
</x-card>
