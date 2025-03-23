<div>
    <x-card title="Tableau de Bord Administrateur" subtitle="Vue d'ensemble des activités de l'administrateur" shadow separator>
        <x-slot:menu>
            <x-button 
                icon="o-cog-6-tooth" 
                label="Paramètres" 
                @click="$wire.showSettings = true" 
                class="btn-ghost" 
            />
        </x-slot:menu>
        {{-- Statistiques Principales --}}
        <div class="flex flex-wrap justify-between gap-4 mb-6">
            <x-stat title="Revenu Brut" value="CHF{{ number_format($totalRevenue ?? 0, 2) }}" icon="s-currency-euro" class="flex-1 min-w-[200px] drop-shadow-lg" />
            <x-stat title="Commandes" value="{{ $totalOrders ?? 0 }}" icon="s-gift" class="flex-1 min-w-[200px] drop-shadow-lg" />
            <x-stat title="Nouveaux Clients" value="{{ $newCustomers ?? 0 }}" icon="o-user-plus" class="flex-1 min-w-[200px] drop-shadow-lg" />
            <x-stat title="Produits Actifs" value="{{ $activeProducts ?? 0 }}" icon="m-archive-box-arrow-down" class="flex-1 min-w-[200px] drop-shadow-lg" />
        </div>

        {{-- Graphiques --}}
        <div class="flex flex-wrap justify-between gap-4 mb-6">
            <div class="flex-1 min-w-[300px] shadow-lg p-4">
                <h3 class="text-lg font-semibold mb-2">Revenu Brut</h3>
                <x-chart wire:model="grossRevenueChart" class="h-64" />
            </div>
            <div class="flex-1 min-w-[300px] shadow-lg p-4">
                <h3 class="text-lg font-semibold mb-2">Catégories de Produits</h3>
                <x-chart wire:model="productCategoryChart" class="h-64" />
            </div>
        </div>

        {{-- Tableaux --}}
        <div class="flex flex-wrap justify-between gap-4">
            {{-- Tableau des Meilleurs Clients --}}
            <div class="flex-1 min-w-[300px] shadow-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Meilleurs Clients</h3>
                    <x-button icon="o-arrow-long-right" link="{{ route('admin.users.index') }}" label="Voir Tout" class="btn-ghost btn-sm" />
                </div>
                <x-table :headers="$topCustomersHeaders" :rows="$topCustomersRows">
                    @scope('cell_name', $row)
                        {{ $row['name'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_email', $row)
                        {{ $row['email'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_revenue', $row)
                        CHF{{ number_format($row['revenue'] ?? 0, 2) }}
                    @endscope
                </x-table>
            </div>

            {{-- Tableau des Produits les Plus Vendus --}}
            <div class="flex-1 min-w-[300px] shadow-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Produits les Plus Vendus</h3>
                    <x-button icon="o-arrow-long-right" link="{{ route('admin.products.index') }}" label="Voir Tout" class="btn-ghost btn-sm" />
                </div>
                <x-table :headers="$bestSellingProductsHeaders" :rows="$bestSellingProductsRows">
                    @scope('cell_name', $row)
                        {{ $row['name'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_price', $row)
                        CHF{{ number_format($row['price'] ?? 0, 2) }}
                    @endscope

                    @scope('cell_sales', $row)
                        {{ $row['sales'] ?? 0 }}
                    @endscope
                </x-table>
            </div>

            {{-- NEW: Tableau des Menus les Plus Vendus --}}
            <div class="flex-1 min-w-[300px] shadow-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Menus les Plus Vendus</h3>
                    <x-button icon="o-arrow-long-right" link="{{ route('admin.menus.index') }}" label="Voir Tout" class="btn-ghost btn-sm" />
                </div>
                <x-table :headers="$bestSellingMenusHeaders" :rows="$bestSellingMenusRows">
                    @scope('cell_name', $row)
                        {{ $row['name'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_price', $row)
                        CHF{{ number_format($row['price'] ?? 0, 2) }}
                    @endscope

                    @scope('cell_sales', $row)
                        {{ $row['sales'] ?? 0 }}
                    @endscope
                </x-table>
            </div>
        </div>
    </x-card>
    {{-- Drawer des paramètres --}}
    <x-drawer 
        wire:model="showSettings" 
        title="Paramètres du site" 
        class="lg:w-1/3"
        separator
        with-close-button
    >
        <livewire:admin.settings />
    </x-drawer>
</div>
