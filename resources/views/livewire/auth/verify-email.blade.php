<!-- resources/views/livewire/auth/verify-email.blade.php -->
 <div>
    @if(session('resent'))
    <div class="alert alert-success mb-4">
        Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
    </div>
    @endif


    @if(session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif
<x-card title="Vérification d'email requise" class="max-w-md mx-auto">
     
    <div class="space-y-4">
        <p>Un lien de vérification a été envoyé à votre adresse email.</p>
        <p>Si vous n'avez pas reçu l'email :</p>
        
        <x-button label="Renvoyer l'email de vérification" 
                 wire:click="resend" 
                 class="w-full" 
                 spinner="resend" />
                 
        <div class="text-center">
            <x-button label="Retour à l'accueil" link="/" variant="ghost" />
        </div>
    </div>
</x-card>

</div>