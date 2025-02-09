<x-mail::message>
# Confirmation de commande

Bonjour {{ $order->customer->first_name }},

Merci pour votre commande ! Nous sommes en train de la traiter. Voici les détails de votre commande :

**Numéro de commande :** {{ $order->id }}  
**Total :** {{ number_format($order->total_price, 2) }} CHF

| Produit                                    | Quantité | Prix         |
| ------------------------------------------ |:--------:| ------------:|
@foreach($order->orderItems as $item)
| {{ optional($item->orderable)->name ?? 'Item supprimé' }} | {{ $item->quantity }} | {{ number_format($item->price, 2) }} CHF |
@endforeach

<x-mail::button :url="route('customer.orders.show', $order->id)">
Voir la commande
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
