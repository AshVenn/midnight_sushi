<!-- resources/views/livewire/auth/login.blade.php -->
<div>
    <h2 class="text-2xl font-semibold text-center mb-4">Bienvenue de Retour !</h2>
    <x-form wire:submit="login">
        <x-input
            label="Email"
            type="email"
            wire:model="email"
            required
            autocomplete="off"
        />

        <x-input
            label="Mot de Passe"
            type="password"
            wire:model="password"
            required
        />

        <x-slot:actions>
            <div class="flex flex-col items-start space-y-2">
                <x-button
                    label="Se Connecter"
                    class="btn-primary"
                    type="submit"
                    spinner="login"
                />
                <x-button
                    label="Vous n'avez pas de compte ? Inscrivez-vous"
                    link="/register"
                    class="btn-ghost"
                />
            </div>
        </x-slot:actions>
    </x-form>
</div>
