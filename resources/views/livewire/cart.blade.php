<div>
    <x-dropdown no-x-anchor right>
        <x-slot:trigger>
            <x-button 
                class="btn btn-circle relative" 
                icon="o-shopping-cart"
            />
            @if($totalItems > 0)
                <x-badge :value="$totalItems" class="badge-error absolute -right-2 -top-2" />
            @endif
        </x-slot:trigger>

        <x-card class="w-80 p-4">
            @if($totalItems > 0)
                <h3 class="text-lg font-semibold mb-4">Votre Panier</h3>
                <!-- Conteneur déroulant pour les articles du panier -->
                <div class="max-h-60 overflow-y-auto space-y-2">
                    @foreach($cart as $type => $items)
                        @foreach($items as $item)
                            <x-menu-item class="flex items-start">
                                <x-avatar 
                                    image="{{ $item['image'] }}" 
                                    class="w-12 h-12 object-cover rounded mr-2"
                                >
                                    <x-slot:title class="text-lg font-semibold">
                                        {{ $item['name'] }}
                                    </x-slot:title>

                                    <x-slot:subtitle class="text-sm flex flex-col gap-1 mt-1">
                                        <span>Quantité: {{ $item['quantity'] }}</span>
                                        <x-badge class="badge-primary" :value="'€' . number_format($item['price'], 2)" />
                                    </x-slot:subtitle>
                                </x-avatar>
                                
                                <x-button 
                                    icon="o-trash" 
                                    class="btn-ghost btn-sm ml-auto mt-1" 
                                    wire:click="removeItem('{{ $type }}', {{ $item['id'] }})"
                                    tooltip="SSSupprimer"
                                />
                            </x-menu-item>
                        @endforeach
                    @endforeach
                </div>
                <!-- Total du panier -->
                <div class="mt-4">
                    <p class="font-bold">Total: €{{ number_format($totalPrice, 2) }}</p>
                </div>
                <!-- Actions du panier -->
                <div class="mt-4 flex justify-between">
                    <x-button 
                        label="Vider le panier" 
                        class="btn-outline btn-sm" 
                        wire:click="clearCart"
                    />
                    <x-button 
                        label="Passer à la caisse" 
                        class="btn-primary btn-sm" 
                        wire:click="proceedToCheckout"
                    />
                </div>
            @else
                <p class="text-center">Votre panier est vide.</p>
            @endif
        </x-card>
    </x-dropdown>
</div>
