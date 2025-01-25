<div>
    <x-card title="Détails de la Commande #{{ $relativeOrderNumber }}" separator>
        {{-- Status --}}
        <div class="mb-4 text-sm">
            <span class="font-semibold">Statut:</span> 
            <span class="uppercase">
                @php
                    switch ($order->status) {
                        case 'pending':          echo 'En attente';          break;
                        case 'confirmed':        echo 'Confirmée';           break;
                        case 'preparing':        echo 'En préparation';      break;
                        case 'ready':            echo 'Prête';               break;
                        case 'out_for_delivery': echo 'En cours de livraison'; break;
                        case 'delivered':        echo 'Livrée';              break;
                        case 'cancelled':        echo 'Annulée';             break;
                        default:                 echo 'Inconnu';             break;
                    }
                @endphp
            </span>
        </div>

        {{-- Order Date --}}
        <div class="mb-4 text-sm">
            Commande passée le {{ $order->created_at->format('d/m/Y H:i') }}
        </div>

        {{-- Items --}}
        <div class="space-y-3 mb-4">
            @foreach($order->orderItems as $item)
                @php
                    $orderable = $item->orderable; // e.g. Product or Menu
                @endphp
                <div class="p-3 border rounded flex justify-between items-center">
                    <div>
                        <strong>{{ $orderable->name ?? 'Article' }}</strong>
                        <span class="ml-2">(x{{ $item->quantity }})</span>
                    </div>
                    <div class="font-semibold">
                        {{ number_format($item->price, 2) }} €
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Notes --}}
        @if($order->notes)
            <div class="mt-4 p-3 rounded text-sm">
                <strong>Notes:</strong> {{ $order->notes }}
            </div>
        @endif

        {{-- Address --}}
        @if($order->address_text)
            <div class="mt-4 p-3 rounded text-sm">
                <strong>Adresse:</strong> {{ $order->address_text }}
            </div>
        @endif

        {{-- Total --}}
        <div class="mt-6 text-right text-lg font-bold">
            Total: {{ number_format($order->total_price, 2) }} €
        </div>
    </x-card>

    {{-- TIMELINE EXAMPLE --}}
    <x-card title="Progression de la Commande" separator class="mt-6">
        <div class="px-4 py-5 space-y-4">
            @foreach ($statusTimeline as $index => $step)
                <x-timeline-item
                    :title="$step['title']"
                    :subtitle="$step['date'] 
                                ? 'Le '.$step['date']->format('d/m/Y H:i') 
                                : ''"
                    :description="$step['description']"
                    :icon="$step['icon']"
                    :first="$loop->first"
                    :last="$loop->last"
                    :pending="!$step['is_done'] && !$step['is_current']"
                />
            @endforeach
        </div>
    </x-card>
</div>
