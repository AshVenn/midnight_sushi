{{-- resources/views/livewire/checkout/checkout.blade.php --}}
<div class="grid lg:grid-cols-8 gap-10">
    {{-- ILLUSTRATION --}}
    <div class="lg:col-span-2 flex items-center justify-center">
        <img src="/images/midnight_sushi/checkout.png"
             width="250"
             class="mx-auto"
             alt="Illustration de paiement"/>
    </div>

    @php
        $cart       = Session::get('cart', []);
        $totalItems = 0;
        $subTotal   = 0;

        foreach ($cart as $type => $items) {
            foreach ($items as $item) {
                $totalItems += $item['quantity'];
                $subTotal   += $item['price'] * $item['quantity'];
            }
        }

        $deliveryFee = $this->deliveryFee;
        $finalTotal  = $subTotal + $deliveryFee;
    @endphp

    @if($totalItems > 0)
        {{-- PANIER ------------------------------------------------------ --}}
        <div class="lg:col-span-3">
            <x-card title="Votre panier" separator>
                @foreach($cart as $type => $items)
                    @foreach($items as $item)
                        <x-list-item
                            :item="$item"
                            value="name"
                            :sub-value="'CHF' . number_format($item['price'], 2)"
                            avatar="image"
                            no-separator>
                            <x-slot:actions>
                                <x-badge :value="$item['quantity']" class="badge-neutral"/>
                            </x-slot:actions>
                        </x-list-item>
                    @endforeach
                @endforeach

                <hr class="my-5"/>

                {{-- Récapitulatif prix --}}
                <div class="space-y-2 mx-3">
                    <div class="flex justify-between">
                        <span>Sous‑total</span><span>CHF{{ number_format($subTotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Frais de livraison</span><span>CHF{{ number_format($deliveryFee, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2">
                        <span>Total</span><span>CHF{{ number_format($finalTotal, 2) }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- FORMULAIRE -------------------------------------------------- --}}
        <div class="lg:col-span-3">
            <x-card title="Finaliser la commande" separator>
                <form wire:submit.prevent="placeOrder" class="space-y-4">

                    {{-- Téléphone --}}
                    <div>
                        <label class="block font-medium text-sm mb-1" for="phone_number">Téléphone suisse</label>
                        <x-input id="phone_number"
                                 wire:model="phone_number"
                                 placeholder="+41 79 123 45 67"
                                 class="w-full"/>
                        @error('phone_number') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Adresse --}}
                    <div class="space-y-4">
                        <label class="block font-medium text-sm">Adresse de livraison</label>

                        @if($addresses->isNotEmpty())
                            <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0">
                                <x-radio label="Adresse enregistrée" wire:model="address_type" value="saved"/>
                                <x-radio label="Nouvelle adresse"     wire:model="address_type" value="custom"/>
                            </div>

                            @if($address_type === 'saved')
                                <x-select wire:model="selected_address"
                                          :options="$addresses"
                                          option-label="full_address"
                                          option-value="id"
                                          placeholder="Sélectionnez une adresse"
                                          wire:change="updateAddressText"
                                          class="w-full mt-2"/>
                            @endif
                        @endif

                        <x-textarea wire:model="address_text"
                                    placeholder="Entrez l'adresse complète"
                                    rows="4"
                                    class="w-full {{ $address_type==='saved' ? 'opacity-75' : '' }}"
                                    :readonly="$address_type==='saved'"/>
                        @error('address_text') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block font-medium text-sm mb-1" for="notes">Notes livreur</label>
                        <x-textarea id="notes" wire:model="notes" rows="2" class="w-full"/>
                    </div>

                    {{-- Bouton paiement --}}
                    <div class="mt-6 text-center">
                        <x-button type="submit"
                                  spinner="placeOrder"
                                  label="Paiement sécurisé"
                                  icon="o-lock-closed"
                                  class="btn-primary w-full py-4 text-lg"/>
                        <p class="text-xs text-gray-500 mt-2">
                            Vous serez redirigé vers notre partenaire Stripe
                        </p>
                    </div>
                </form>
            </x-card>
        </div>
    @else
        {{-- PANIER VIDE -------------------------------------------------- --}}
        <div class="lg:col-span-6">
            <h2 class="text-3xl font-bold mb-6">Votre panier est vide</h2>
            <x-button label="Retour à la carte"
                      link="/menus-products"
                      icon-right="o-arrow-right"
                      class="btn-ghost"/>
        </div>
    @endif
</div>
