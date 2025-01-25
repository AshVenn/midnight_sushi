<div class="grid lg:grid-cols-8 gap-10 px-4 lg:px-0">
    {{-- IMAGE --}}
    <div class="lg:col-span-2 flex justify-center items-start">
        <img 
            src="/images/sushi/orders.png" 
            width="250" 
            class="mx-auto lg:mx-0" 
            alt="Orders" 
        />
    </div>

    @if ($orders->count())
        <div class="lg:col-span-6">
            {{-- Title / Header --}}
            <h1 class="text-2xl font-bold mb-6 text-center lg:text-left">Mes Commandes</h1>

            {{-- ORDERS LIST --}}
            @foreach ($orders as $order)
                <div class="border rounded-lg mb-5 p-4 shadow-sm bg-base-100 dark:bg-base-200">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        {{-- Relative Order Number --}}
                        <div class="rounded-lg p-3 font-bold text-center min-w-[60px] bg-primary text-base-100">
                            {{-- Show the sequence number: #1, #2, #3, ... --}}
                            #{{ $loop->iteration }}
                        </div>

                        {{-- Main Info --}}
                        <div class="flex-grow">
                            {{-- Show the first item name, plus count of remaining items --}}
                            @if ($order->orderItems->count())
                                @php
                                    $firstItemName = optional($order->orderItems->first()->orderable)->name;
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
                                        @php
                                            switch ($order->status) {
                                                case 'pending':
                                                    echo 'En attente';
                                                    break;
                                                case 'confirmed':
                                                    echo 'Confirmée';
                                                    break;
                                                case 'preparing':
                                                    echo 'En préparation';
                                                    break;
                                                case 'ready':
                                                    echo 'Prête';
                                                    break;
                                                case 'out_for_delivery':
                                                    echo 'En cours de livraison';
                                                    break;
                                                case 'delivered':
                                                    echo 'Livrée';
                                                    break;
                                                case 'cancelled':
                                                    echo 'Annulée';
                                                    break;
                                                default:
                                                    echo 'Inconnu';
                                                    break;
                                            }
                                        @endphp
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div>
                            <x-button 
                                label="Détails" 
                                icon="o-chevron-right" 
                                link="{{ route('customer.orders.show', $order->id) }}" 
                                class="btn-secondary"
                            />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="lg:col-span-6 text-center">
            <div class="font-bold text-3xl mb-4">Aucune commande</div>
            <x-button 
                label="Parcourir le Menu" 
                icon-right="o-arrow-right" 
                link="/" 
            />
        </div>
    @endif
</div>
