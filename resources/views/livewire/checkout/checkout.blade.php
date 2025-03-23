<!-- resources/views/livewire/checkout.blade.php -->
<div class="grid lg:grid-cols-8 gap-10">
    <!-- IMAGE -->
    <div class="lg:col-span-2 flex items-center justify-center">
        <img src="/images/midnight_sushi/checkout.png" width="250" class="mx-auto" alt="Illustration de paiement" />
    </div>

    @php
        $cart = Session::get('cart', []);
        $totalItems = 0;
        $totalPrice = 0;

        foreach ($cart as $type => $items) {
            foreach ($items as $item) {
                $totalItems += $item['quantity'];
                $totalPrice += ($item['price'] * $item['quantity']);
            }
        }
    @endphp

    @if($totalItems > 0)
        <!-- LISTE DES ITEMS DU PANIER -->
        <div class="lg:col-span-3">
            <x-card title="Votre Panier" separator>
                @foreach($cart as $type => $items)
                    @foreach($items as $item)
                        <x-list-item
                            :item="$item"
                            value="name"
                            :sub-value="'CHF' . number_format($item['price'], 2)"
                            avatar="image"
                            no-separator
                        >
                            <x-slot:actions>
                                <x-badge :value="$item['quantity']" class="badge-neutral" />
                            </x-slot:actions>
                        </x-list-item>
                    @endforeach
                @endforeach

                <hr class="my-5" />

                <!-- TOTAL -->
                <div class="flex justify-between items-center mx-3">
                    <div>Total</div>
                    <div class="font-black text-lg">
                        CHF{{ number_format($totalPrice, 2) }}
                    </div>
                </div>
            </x-card>
        </div>

        <!-- FORMULAIRE DE COMMANDE -->
        <div class="lg:col-span-3">
            <x-card title="Finaliser la commande" separator>
                <form wire:submit.prevent="placeOrder" class="space-y-4">
                    <!-- Téléphone -->
                    <div>
                        <label class="block font-medium text-sm mb-1" for="phone_number">Téléphone suisse</label>
                        <x-input
                            id="phone_number"
                            wire:model="phone_number"
                            placeholder="Ex: +41 79 123 45 67 ou 079 123 45 67"
                            class="w-full"
                            autocomplete="tel"
                        />
                        @error('phone_number')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div class="space-y-4">
                        <label class="block font-medium text-sm mb-1">Adresse de livraison</label>
                        
                        @if($addresses->isNotEmpty())
                            <!-- Radio Buttons for selecting address type -->
                            <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0">
                                <x-radio label="Adresse enregistrée" wire:model="address_type" value="saved" />
                                <x-radio label="Nouvelle adresse" wire:model="address_type" value="custom" />
                            </div>

                            <!-- Show select dropdown only when "saved" is selected -->
                            @if($address_type === 'saved')
                                <div class="mt-2">
                                    <x-select
                                        wire:model="selected_address"
                                        :options="$addresses"
                                        option-label="full_address"
                                        option-value="id"
                                        placeholder="Sélectionnez une adresse"
                                        wire:change="updateAddressText"
                                        class="w-full"
                                    />
                                </div>
                            @endif
                        @endif

                        <!-- Text area for address input (editable when "custom") -->
                        <div class="mt-2">
                            <x-textarea
                                wire:model="address_text"
                                placeholder="Entrez l'adresse complète de livraison"
                                rows="4"
                                :readonly="$address_type === 'saved'"
                                class="w-full"
                            />
                            @error('address_text')
                                <span class="text-error text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block font-medium text-sm mb-1" for="notes">Instructions spéciales</label>
                        <x-textarea
                            id="notes"
                            wire:model="notes"
                            placeholder="Informations supplémentaires pour le livreur..."
                            class="w-full"
                            rows="2"
                        />
                    </div>

                    <!-- Bouton de paiement -->
                    <div class="mt-6 text-center">
                        <x-button
                            label="Procéder au paiement sécurisé"
                            icon="o-lock-closed"
                            class="btn-primary w-full py-4 text-lg"
                            type="submit"
                            spinner="placeOrder"
                        />
                        <p class="text-sm text-gray-500 mt-2">
                            Vous serez redirigé vers le système de paiement sécurisé Stripe
                        </p>
                    </div>
                </form>
            </x-card>
        </div>
    @else
        <div class="lg:col-span-6">
            <div class="font-bold text-3xl mb-8">Votre panier est vide</div>
            <x-button 
                label="Retour à la carte" 
                icon-right="o-arrow-right" 
                link="/menus-products" 
                class="btn-ghost"
            />
        </div>
    @endif
</div>