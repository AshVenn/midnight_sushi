<x-mail::layout>
    <x-mail::header />
    
    <x-mail::panel>
        <h1 class="text-lg font-bold">Votre commande #{{ $delivery->order->order_number }} a été retournée</h1>
        
        <div class="mt-4 space-y-2">
            <p><strong>Raison du retour:</strong> {{ $reason }}</p>
            <p><strong>Date du retour:</strong> {{ $delivery->returned_at->format('d/m/Y H:i') }}</p>
        </div>

        <x-mail::button :url="route('orders.show', $delivery->order)" color="primary" class="mt-4">
            Voir les détails de la commande
        </x-mail::button>
    </x-mail::panel>

    <x-mail::footer />
</x-mail::layout>