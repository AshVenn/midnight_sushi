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
                <select 
                    wire:model="filterStatus"
                    class="select select-bordered w-full max-w-xs"
                >
                    <option value="">Toutes les commandes</option>
                    <option value="pending">En attente</option>
                    <option value="confirmed">Confirmée</option>
                    <option value="preparing">En préparation</option>
                    <option value="ready">Prête</option>
                    <option value="out_for_delivery">En livraison</option>
                    <option value="delivered">Livrée</option>
                    <option value="cancelled">Annulée</option>
                    <option value="returned">Retournée</option>
                </select>
            </div>
        </div>

        @if ($orders->count())
            @foreach ($orders as $index => $order)
                <div class="border rounded-lg mb-5 p-4 shadow-sm bg-base-100">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <div class="rounded-lg p-3 font-bold text-center min-w-[60px] bg-primary text-base-100">
                            {{ $order->order_number }}
                        </div>

                        <div class="flex-grow">
                            @if ($order->orderItems->count())
                                @php
                                    $firstItem     = $order->orderItems->first();
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
                                            @case('preparing')
                                                En préparation
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
                                            @case('cancelled')
                                                Annulée
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
                            @if($order->can_be_cancelled)
                                <x-button
                                    label="Annuler"
                                    icon="o-x-circle"
                                    wire:click="$dispatch('open-cancel-modal', { orderId: {{ $order->id }} })"
                                    class="btn-primary btn-sm"
                                />
                            @endif

                            <x-button 
                                label="Détails" 
                                icon="o-chevron-right" 
                                link="{{ route('customer.orders.show', $order->id) }}" 
                                class="btn-secondary btn-sm"
                            />

                            @if($order->status === 'delivered')
                                <x-button
                                    label="Noter"
                                    icon="o-pencil"
                                    wire:click="openReviewDrawer({{ $order->id }})"
                                    class="btn-primary btn-sm"
                                />
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
                    link="/"
                />
            </div>
        @endif
    </div>
</div>

<x-drawer
    wire:model="showReviewDrawer"
    title="Laisser un avis"
    subtitle="Partagez votre opinion"
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

                    <label class="block text-sm font-medium mb-1">Note (1 à 5)</label>
                    <input 
                        type="number" 
                        min="1" 
                        max="5" 
                        placeholder="1 à 5" 
                        class="input input-bordered w-full mb-3"
                        wire:model.defer="reviewData.{{ $index }}.rating"
                    />

                    <label class="block text-sm font-medium mb-1">Commentaire</label>
                    <textarea 
                        class="textarea textarea-bordered w-full"
                        placeholder="Vos impressions..."
                        wire:model.defer="reviewData.{{ $index }}.comment"
                    ></textarea>
                </div>
            @endforeach
        @else
            <p>Aucun article restant à noter.</p>
        @endif
    </div>

    <x-slot:actions>
        <x-button label="Fermer" @click="$wire.showReviewDrawer = false" />
        <x-button 
            label="Enregistrer" 
            class="btn-primary" 
            icon="o-check" 
            wire:click="saveReviews" 
            spinner
        />
    </x-slot:actions>
</x-drawer>
</div>