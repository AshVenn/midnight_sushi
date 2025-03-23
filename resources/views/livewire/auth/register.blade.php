<div class="w-full max-w-md mx-auto p-8 rounded shadow-sm">
    <h2 class="text-xl font-bold text-center mb-4">Créez Votre Compte</h2>

    <x-form wire:submit.prevent="register">
        <x-input label="Prénom" wire:model="first_name" required autocomplete="off"/>
        <x-input label="Nom" wire:model="last_name" required autocomplete="off"/>
        <x-input label="Email" type="email" wire:model="email" required autocomplete="off"/>
        <x-input label="Mot de Passe" type="password" wire:model="password" required />

        <div style="display: none;">
            <x-input wire:model="honeypot" autocomplete="off" tabindex="-1" />
        </div>

        <div class="mt-6">
            <x-button label="S'inscrire" class="btn-primary w-full" type="submit" spinner="register" />
        </div>
    </x-form>
</div>
