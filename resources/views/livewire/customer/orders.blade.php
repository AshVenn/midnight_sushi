<div>
    <div class="grid lg:grid-cols-8 gap-10 px-4 lg:px-0">
        <div class="lg:col-span-2 flex justify-center items-start">
            <img 
                src="/images/midnight_sushi/orders.png" 
                width="250" 
                class="mx-auto lg:mx-0" 
                alt="Orders"
            />
        </div>

        <div class="lg:col-span-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                <h1 class="text-2xl font-bold mb-2 lg:mb-0 text-center lg:text-left">
                    Mes Commandes
                </h1>

                <div>
                    <x-select 
                        wire:model="filterStatus"
                        :options="$filtres"
                        class="select select-bordered w-full max-w-xs"
                        wire:change="resetPageOnChange"
                    />
                </div>
            </div>

            @if ($orders->count())
                @foreach ($orders as $order)
                    <div class="border rounded-lg mb-5 p-4 shadow-sm bg-base-100">
                        <div class="flex flex-col md:flex-row items-center gap-4">
                            <div class="rounded-lg p-3 font-bold text-center min-w-[60px] bg-primary text-base-100">
                                {{ $order->order_number }}
                            </div>

                            <div class="flex-grow">
                                @if ($order->orderItems->count())
                                    @php
                                        $firstItem = $order->orderItems->first();
                                        $firstItemName = optional($firstItem->orderable)->name;
                                    @endphp
                                    <div class="text-lg font-semibold">
                                        {{ $firstItemName ?? 'Article' }}
                                        @if ($order->orderItems->count() > 1)
                                            <span class="text-sm">
                                                +{{ $order->orderItems->count() - 1 }} autres
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-lg font-semibold">
                                        Aucun article
                                    </div>
                                @endif

                                <div class="mt-1 flex flex-wrap gap-4 text-sm">
                                    <div>
                                        {{ $order->created_at->format('Y-m-d') }}
                                    </div>
                                    <div class="font-bold">
                                        {{ number_format($order->total_price, 2) }} CHF
                                    </div>
                                    <div>
                                        Statut:
                                        <span class="font-semibold capitalize">
                                            @switch($order->status)
                                                @case('pending')
                                                    En attente
                                                    @break
                                                @case('confirmed')
                                                    Confirmée
                                                    @break
                                                @case('ready')
                                                    Prête
                                                    @break
                                                @case('out_for_delivery')
                                                    En cours de livraison
                                                    @break
                                                @case('delivered')
                                                    Livrée
                                                    @break
                                                @case('returned')
                                                    Retournée
                                                    @break
                                                @default
                                                    Inconnu
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <x-button 
                                    label="Détails" 
                                    icon="o-chevron-right" 
                                    link="{{ route('customer.orders.show', $order->id) }}" 
                                    class="btn-secondary btn-sm"
                                />

                                @if($order->status === 'delivered')
                                    @if($order->is_reviewed)
                                        <x-button 
                                            label="Noté" 
                                            icon="o-check" 
                                            class="btn-success btn-sm" 
                                            disabled 
                                        />
                                    @else
                                        <x-button
                                            label="Noter"
                                            icon="o-pencil"
                                            wire:click="openReviewDrawer({{ $order->id }})"
                                            class="btn-primary btn-sm"
                                        />
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center">
                    <div class="font-bold text-3xl mb-4">Aucune commande</div>
                    <x-button 
                        label="Parcourir le Menu" 
                        icon-right="o-arrow-right" 
                        link="/menus-products"
                    />
                </div>
            @endif
        </div>
    </div>

    <!-- Drawer pour laisser un avis (sans champ commentaire) -->
    <x-drawer
    wire:model="showReviewDrawer"
    title="Laisser un avis"
    subtitle="Attribuez une note aux articles non notés"
    separator
    with-close-button
    close-on-escape
    class="w-11/12 lg:w-1/3"
>
    <div class="space-y-4">
        @if(count($reviewData))
            @foreach($reviewData as $index => $item)
                <div class="rounded p-3 border bg-base-100">
                    <div class="font-bold mb-2">{{ $item['name'] }}</div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Note</label>
                        <div class="rating rating-md">
                            @for ($i = 1; $i <= 5; $i++)
                                <input 
                                    type="radio" 
                                    name="rating-{{ $index }}" 
                                    class="mask mask-star-2 bg-primary"
                                    wire:model="reviewData.{{ $index }}.rating"
                                    value="{{ $i }}"
                                />
                            @endfor
                        </div>
                        @error("reviewData.{$index}.rating")
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endforeach
        @else
            <x-alert icon="o-check" class="bg-base-200">
                Tous les articles ont déjà été notés !
            </x-alert>
        @endif
    </div>

    <x-slot:actions>
        <x-button label="Fermer" @click="$wire.showReviewDrawer = false" />
        @if(count($reviewData))
            <x-button 
                label="Enregistrer" 
                class="btn-primary" 
                icon="o-check" 
                wire:click="saveReviews" 
                spinner
            />
        @endif
    </x-slot:actions>
</x-drawer>

</div>
