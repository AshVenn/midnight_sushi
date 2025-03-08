<x-mail::message>
# Votre commande #{{ $delivery->order->order_number }} a été retournée

**Raison du retour:** {{ $reason }}  
**Date du retour:** {{ $delivery->returned_at->format('d/m/Y H:i') }}

<x-mail::button :url="route('customer.orders.show', $delivery->order_id)">
    Voir les détails de la commande
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
