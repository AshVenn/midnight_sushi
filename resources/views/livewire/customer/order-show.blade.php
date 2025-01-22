<div>
    <x-card title="Détails de la Commande #{{ $order->id }}" separator>
        {{-- Status --}}
        <div class="mb-4 text-sm">
            <span class="font-semibold">Statut:</span> 
            <span class="uppercase">{{ $order->status }}</span>
        </div>

        {{-- Order Date --}}
        <div class="mb-4 text-sm ">
            Commande passée le {{ $order->created_at->format('d/m/Y H:i') }}
        </div>

        {{-- Items --}}
        <div class="space-y-3 mb-4">
            @foreach($order->orderItems as $item)
                @php
                    $orderable = $item->orderable; // could be a Product or Menu
                @endphp
                <div class="p-3 border rounded flex justify-between items-center">
                    <div>
                        <strong>{{ $orderable->name ?? 'Article' }}</strong>
                        <span class=" ml-2">
                            (x{{ $item->quantity }})
                        </span>
                    </div>
                    <div class="font-semibold">
                        {{ number_format($item->price, 2) }} €
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Notes / Address / etc. --}}
        @if($order->notes)
            <div class="mt-4  p-3 rounded text-sm">
                <strong>Notes:</strong> {{ $order->notes }}
            </div>
        @endif

        @if($order->address_text)
            <div class="mt-4  p-3 rounded text-sm">
                <strong>Adresse:</strong> {{ $order->address_text }}
            </div>
        @endif

        {{-- Total --}}
        <div class="mt-6 text-right text-lg font-bold">
            Total: {{ number_format($order->total_price, 2) }} €
        </div>
    </x-card>
</div>
