<!-- resources/views/livewire/checkout.blade.php -->
<div class="grid lg:grid-cols-8 gap-10">
    <!-- IMAGE -->
    <div class="lg:col-span-2 flex items-center justify-center">
        <!-- Adaptez le chemin de l'image à vos besoins -->
        <img src="/images/checkout.png" width="300" class="mx-auto" alt="Checkout Illustration" />
    </div>

    @php
        // Récupération du panier en session
        $cart = Session::get('cart', []);
        $totalItems = 0;
        $totalPrice = 0;

        // Calcul du nombre total d'items et du prix total
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
                            :sub-value="'€' . number_format($item['price'], 2)"
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
                        €{{ number_format($totalPrice, 2) }}
                    </div>
                </div>
            </x-card>
        </div>

        <!-- FORMULAIRE 'ADRESSE / INFOS' ET BOUTON 'PASSER LA COMMANDE' -->
        <div class="lg:col-span-3">
            <x-card title="Finaliser la commande" separator>
                <!-- On utilise un formulaire Livewire -->
                <form wire:submit.prevent="placeOrder" class="space-y-4">
                    <!-- Téléphone -->
                    <div>
                        <label class="block font-medium text-sm mb-1" for="phone_number">Téléphone</label>
                        <x-input
                            id="phone_number"
                            wire:model="phone_number"
                            placeholder="Votre numéro de téléphone"
                            class="w-full"
                        />
                        @error('phone_number')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label class="block font-medium text-sm mb-1" for="address_text">Adresse</label>
                        <x-textarea
                            id="address_text"
                            wire:model="address_text"
                            placeholder="Votre adresse complète"
                            class="w-full"
                        />
                        @error('address_text')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block font-medium text-sm mb-1" for="notes">Notes (facultatif)</label>
                        <x-textarea
                            id="notes"
                            wire:model="notes"
                            placeholder="Notes pour la livraison / autres"
                            class="w-full"
                        />
                        @error('notes')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bouton SUBMIT -->
                    <div class="flex justify-end mt-5">
                        <x-button
                            label="Passer la commande"
                            icon="o-paper-airplane"
                            class="btn-primary"
                            type="submit"
                            spinner="placeOrder"
                        />
                    </div>
                </form>
            </x-card>
        </div>
    @else
        <!-- PANIER VIDE -->
        <div class="lg:col-span-6">
            <div class="font-bold text-3xl mb-8">Le panier est vide</div>
            <x-button label="Parcourir les produits" icon-right="o-arrow-right" link="/" />
        </div>
    @endif
</div>
