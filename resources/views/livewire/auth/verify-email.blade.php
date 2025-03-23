<!-- resources/views/livewire/auth/verify-email.blade.php -->
<div>
    @if(session('status'))
        <x-toast type="success" :title="session('status')" />
    @endif

    <div class="text-center space-y-4">
        <p class="text-lg font-medium">Vérification d'email requise</p>
        
        @if($remainingSeconds > 0)
            <div class="text-error text-sm" 
                 x-data="{ countdown: {{ $remainingSeconds }} }"
                 x-init="setInterval(() => { if(countdown > 0) countdown-- }, 1000)">
                Vous pourrez renvoyer dans 
                <span x-text="`${Math.floor(countdown/3600).toString().padStart(2, '0')}h ${Math.floor((countdown%3600)/60).toString().padStart(2, '0')}m ${(countdown%60).toString().padStart(2, '0')}s`"></span>
            </div>
        @endif

        <x-button label="Renvoyer l'email de vérification" 
                  wire:click="resend"
                  :disabled="$remainingSeconds > 0"
                  spinner="resend" />
    </div>
</div>