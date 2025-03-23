<x-card title="Mot de passe oublié" class="max-w-md mx-auto p-6 md:p-8">
    <x-form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <x-input label="Adresse Email" name="email" type="email" required autofocus  autocomplete="off"/>

        <x-button label="Envoyer le lien de réinitialisation" type="submit" variant="primary" class="w-full" />

        @if (session('status'))
            <p class="text-green-500 text-sm mt-3 text-center">{{ session('status') }}</p>
        @endif
    </x-form>

    <div class="text-center mt-4">
        <x-button label="Retour à la connexion" link="{{ route('login') }}" variant="secondary" class="w-full sm:w-auto" />
    </div>
</x-card>
