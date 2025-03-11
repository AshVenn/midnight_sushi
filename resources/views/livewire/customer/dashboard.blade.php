<div class="space-y-8 p-4 md:p-6">
    {{-- En-tête de bienvenue --}}
    <x-header title="Bonjour, {{ auth()->user()->first_name }} 👋" subtitle="Votre espace personnel">
        <x-slot:middle class="!justify-end">
            <x-badge value="{{ auth()->user()->status }}" class="badge-primary" />
        </x-slot:middle>
    </x-header>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat title="Commandes totales" :value="$totalOrders" icon="o-shopping-cart" class="bg-base-200 p-4 rounded-box" />
        <x-stat title="En cours" :value="$pendingOrders" icon="o-clock" class="bg-base-200 p-4 rounded-box" />
        <x-stat title="Adresses enregistrées" :value="$addressCount" icon="o-map-pin" class="bg-base-200 p-4 rounded-box" />
    </div>

    {{-- Dernières commandes --}}
    <x-card title="Vos dernières commandes" shadow>
        @if($recentOrders->isEmpty())
            <x-alert icon="o-face-frown" class="text-center">
                Aucune commande récente. Parcourez notre menu !
            </x-alert>
        @else
            <x-table :headers="$headers" :rows="$recentOrders" link="{{ route('customer.orders.index') }}">
                @scope('cell_status', $order)
                    <x-badge :value="$order->status_text" class="{{ $statusColors[$order->status_text] ?? 'badge-neutral' }}" />
                @endscope
            </x-table>
        @endif

        <x-slot:menu>
            <x-button label="Voir toutes les commandes" link="{{ route('customer.orders.index') }}" icon-right="o-arrow-right" class="btn-ghost" />
        </x-slot:menu>
    </x-card>

    {{-- Suivi des livraisons --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Suivi des livraisons" shadow>
            @forelse($currentDeliveries as $delivery)
                <x-list-item :item="$delivery" sub-value="status" no-separator>
                    <x-slot:value>
                        Commande #{{ $delivery->order_id }}
                        <div class="text-xs opacity-75">
                            {{ $delivery->address_text }}
                        </div>
                    </x-slot:value>
                    <x-slot:actions>
                        <x-badge :value="$delivery->status" class="badge-primary" />
                    </x-slot:actions>
                </x-list-item>
            @empty
                <x-alert icon="o-truck" class="text-center">
                    Aucune livraison en cours
                </x-alert>
            @endforelse
        </x-card>

        {{-- Vos adresses --}}
        <x-card title="Vos adresses" shadow>
            @forelse($recentAddresses as $address)
                <x-list-item :item="$address" sub-value="full_address" no-separator>
                    <x-slot:avatar>
                        <x-icon name="o-map-pin" class="text-primary text-xl" />
                    </x-slot:avatar>
                </x-list-item>
            @empty
                <x-alert icon="o-map-pin" class="text-center">
                    Aucune adresse enregistrée
                </x-alert>
            @endforelse
            
            <x-slot:menu>
                <x-button label="Gérer les adresses" link="{{ route('customer.addresses') }}" icon="o-plus" class="btn-ghost" />
            </x-slot:menu>
        </x-card>
    </div>

    {{-- Actions rapides --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
        <x-button icon="o-plus" label="Nouvelle commande" link="{{ route('menus-products') }}" class="btn-primary" />
        <x-button icon="o-envelope" label="Support" link="{{ route('customer.support') }}" class="btn-secondary" />
        <x-button icon="o-user" label="Profil" link="{{ route('customer.profile') }}" class="btn-accent" />
        <x-button icon="s-shopping-cart" label="Commandes" link="{{ route('customer.orders.index') }}" class="btn-neutral" />
    </div>

</div>
