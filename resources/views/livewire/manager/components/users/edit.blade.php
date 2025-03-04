<x-card title="Modifier l'Utilisateur">
    <x-form wire:submit="save">
        <x-input wire:model.defer="user.name" label="Nom" required />
        <x-input wire:model.defer="user.email" label="Email" type="email" required />
        <x-select wire:model.defer="user.role" label="Rôle" required>
            <option value="client">Client</option>
            <option value="employee">Employé</option>
        </x-select>

        <x-button type="submit" class="btn-primary" label="Enregistrer" />
    </x-form>
</x-card>
