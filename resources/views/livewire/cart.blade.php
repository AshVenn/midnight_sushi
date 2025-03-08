<div>
    <x-dropdown right class="w-full">
        <x-slot:trigger>
            <x-button
                icon="o-shopping-cart"
                class="btn-ghost btn-sm relative"
                :badge="$totalItems" 
                label="Cart"
            />
        </x-slot:trigger>

        @if($totalItems > 0)
            <div class="w-80 lg:w-96 flex flex-col">
                <!-- Scrollable items list -->
                <div class="overflow-y-auto max-h-[60vh] mb-2">
                    @foreach($cart as $type => $items)
                        @foreach($items as $item)
                            <x-list-item 
                                :item="$item" 
                                value="name"
                                :sub-value="'CHF' . number_format($item['price'], 2)" 
                                avatar="image"
                                class="hover:bg-base-200"
                            >
                                <x-slot:actions>
                                    <x-badge 
                                        :value="$item['quantity']" 
                                        class="badge-neutral mr-2" 
                                    />
                                    <x-button
                                        icon="o-trash"
                                        wire:click.stop="removeItem('{{ $type }}', {{ $item['id'] }})"
                                        class="btn-sm btn-circle btn-ghost text-error"
                                    />
                                </x-slot:actions>
                            </x-list-item>
                        @endforeach
                    @endforeach
                </div>

                <!-- Fixed bottom section -->
                <div class="flex-0">
                    <div class="flex justify-between items-center p-3 bg-base-100">
                        <div>{{ $totalItems }} item(s)</div>
                        <div class="font-bold text-lg">
                            CHF{{ number_format($totalPrice, 2) }}
                        </div>
                    </div>

                    <x-menu-separator />

                    <div class="flex justify-between p-3 bg-base-100">
                        <x-button
                            label="Vider"
                            icon="o-trash"
                            wire:click="clearCart"
                            class="btn-ghost btn-sm text-error"
                        />
                        <x-button
                            label="Checkout"
                            icon-right="o-arrow-right"
                            wire:click="proceedToCheckout"
                            class="btn-primary btn-sm"
                        />
                    </div>
                </div>
            </div>
        @else
            <div class="p-4">
                <x-icon name="o-shopping-cart" class="w-12 h-12 text-gray-400 mx-auto" />
                <p class="text-center mt-2 text-gray-500">Votre panier est vide</p>
            </div>
        @endif
    </x-dropdown>
</div>