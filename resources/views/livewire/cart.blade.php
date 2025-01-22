<div>
    <x-dropdown right>
        <x-slot:trigger>
            <!-- Bouton Panier -->
            <x-button
                icon="o-shopping-cart"
                class="btn-ghost btn-sm relative"
                :badge="$totalItems" 
                label="Cart"
            />
        </x-slot:trigger>

        @if($totalItems > 0)
            <!-- Liste des Items -->
            <div class="w-80 lg:w-96 mb-2">
                @foreach($cart as $type => $items)
                    @foreach($items as $item)
                        <x-list-item 
                            :item="$item" 
                            value="name"
                            :sub-value="'€' . number_format($item['price'], 2)" 
                            avatar="image"
                        >
                            <x-slot:actions>
                                <!-- Affiche la quantité dans un badge neutre -->
                                <x-badge 
                                    :value="$item['quantity']" 
                                    class="badge-neutral" 
                                />

                                <!-- Bouton pour supprimer l'item -->
                                <x-button
                                    icon="o-trash"
                                    wire:click.stop="removeItem('{{ $type }}', {{ $item['id'] }})"
                                    spinner="removeItem('{{ $type }}', {{ $item['id'] }})"
                                    class="btn-sm btn-circle btn-ghost text-error"
                                />
                            </x-slot:actions>
                        </x-list-item>
                    @endforeach
                @endforeach
            </div>

            <!-- Totaux -->
            <div class="flex justify-between items-center p-3">
                <div>{{ $totalItems }} item(s)</div>
                <div class="font-bold text-right text-lg">
                    €{{ number_format($totalPrice, 2) }}
                </div>
            </div>

            <x-menu-separator />

            <!-- Actions -->
            <div class="flex justify-between p-3">
                <x-button
                    label="Vider le panier"
                    icon="o-trash"
                    wire:click.stop="clearCart"
                    spinner="clearCart"
                    class="btn-ghost btn-sm text-error"
                />
                <x-button
                    label="Passer à la caisse"
                    icon-right="o-arrow-right"
                    wire:click="proceedToCheckout"
                    class="btn-primary btn-sm"
                />
            </div>
        @else
            <!-- Panier Vide -->
            <div class="p-2">
                <x-icon name="o-ellipsis-horizontal" label="Votre panier est vide" />
            </div>
        @endif
    </x-dropdown>
</div>
