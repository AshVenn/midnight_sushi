<div class="grid lg:grid-cols-8 gap-10 px-4 lg:px-0">
    {{-- IMAGE / Left Column --}}
    <div class="lg:col-span-2 flex justify-center items-start">
        <img 
            src="/images/sushi/orders.png" 
            width="250" 
            class="mx-auto lg:mx-0" 
            alt="Orders" 
        />
    </div>

    {{-- Right Column: Orders List --}}
    <div class="lg:col-span-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-2 lg:mb-0 text-center lg:text-left">
                Mes Commandes
            </h1>

            {{-- Simple Status Filter --}}
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
                </select>
            </div>
        </div>

        @if ($orders->count())
            {{-- ORDERS LIST --}}
            @foreach ($orders as $index => $order)
                <div class="border rounded-lg mb-5 p-4 shadow-sm bg-base-100 dark:bg-base-200">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        {{-- Relative # e.g. #1, #2, etc. --}}
                        <div class="rounded-lg p-3 font-bold text-center min-w-[60px] bg-primary text-base-100">
                            #{{ $orders->firstItem() + $index }}
                            {{-- 
                                Explanation:
                                $orders->firstItem() is the numeric index of the 
                                first item on this page, so first iteration is that + 0. 
                            --}}
                        </div>

                        {{-- Main Info --}}
                        <div class="flex-grow">
                            @if ($order->orderItems->count())
                                @php
                                    $firstItem = $order->orderItems->first();
                                    $firstItemName = optional($firstItem->orderable)->name;
                                @endphp
                                <div class="text-lg font-semibold">
                                    {{ $firstItemName ?? 'Article' }}
                                    @if ($order->orderItems->count() > 1)
                                        <span class="text-sm text-neutral">
                                            +{{ $order->orderItems->count() - 1 }} autres
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="text-lg font-semibold text-neutral">
                                    Aucun article
                                </div>
                            @endif

                            {{-- Order creation date, total price, etc. --}}
                            <div class="mt-1 flex flex-wrap gap-4 text-sm">
                                <div class="text-neutral">
                                    {{ $order->created_at->format('Y-m-d') }}
                                </div>
                                <div class="font-bold">
                                    {{ number_format($order->total_price, 2) }} €
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
                                            @default
                                                Inconnu
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            {{-- Show "Annuler" if possible (for demonstration) --}}
                            @if($order->canBeCancelled())
                                <x-button
                                    label="Annuler"
                                    icon="o-x-circle"
                                    wire:click="$emit('openCancelModal', {{ $order->id }})"
                                    class="btn-error btn-sm"
                                />
                            @endif

                            <x-button 
                                label="Détails" 
                                icon="o-chevron-right" 
                                link="{{ route('customer.orders.show', $order->id) }}" 
                                class="btn-secondary btn-sm"
                            />
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            {{-- No Orders --}}
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
