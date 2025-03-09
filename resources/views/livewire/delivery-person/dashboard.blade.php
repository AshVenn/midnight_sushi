<div>
    <!-- Stat Cards Section (unchanged) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <x-card class="">
            <div class="flex items-center gap-4">
                <x-icon name="o-truck" class="w-12 h-12 text-primary" />
                <div>
                    <div class="text-sm text-gray-500">Livraisons aujourd'hui</div>
                    <div class="text-3xl font-bold text-primary">{{ $stats['today'] }}</div>
                </div>
            </div>
        </x-card>

        <x-card class="">
            <div class="flex items-center gap-4">
                <x-icon name="o-clock" class="w-12 h-12 text-secondary" />
                <div>
                    <div class="text-sm text-gray-500">En cours</div>
                    <div class="text-3xl font-bold text-secondary">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </x-card>

        <x-card class="bg-mostard-50">
            <div class="flex items-center gap-4">
                <x-icon name="o-check-circle" class="w-12 h-12 text-mostard-600" />
                <div>
                    <div class="text-sm text-gray-500">Terminées</div>
                    <div class="text-3xl font-bold text-mostard-600">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart Section -->
        <x-card class="overflow-hidden">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                <h2 class="text-xl font-bold mb-2 sm:mb-0">Statistiques des livraisons</h2>
                <div class="flex gap-2">
                    <x-button 
                        label="Actualiser" 
                        wire:click="randomize" 
                        class="btn-primary" 
                        spinner 
                        icon="o-arrow-path"
                    />
                    <x-button 
                        label="Changer le type" 
                        wire:click="switchChartType" 
                        class="btn-secondary" 
                        spinner 
                        icon="o-arrows-right-left"
                    />
                </div>
            </div>
            <x-chart wire:model="deliveryChart" class="h-96" />
        </x-card>

        <!-- Recent Deliveries Section -->
        <x-card title="Dernières livraisons" class="overflow-x-auto">
            @php
                // Ensure your headers are defined in your component:
                // $this->latestDeliveriesHeaders = [
                //     ['key' => 'order_id', 'label' => 'Commande'],
                //     ['key' => 'status', 'label' => 'Statut'],
                //     ['key' => 'assigned_at', 'label' => "Date d'assignation"],
                // ];
            @endphp
            <x-table :headers="$this->latestDeliveriesHeaders" :rows="$recentDeliveries">
                @scope('cell_order_id', $row)
                    #{{ $row->order_id }}
                @endscope

                @scope('cell_status', $row)
                    @php
                        $colors = [
                            'assigned'   => 'badge-primary',
                            'picked_up'  => 'badge-warning',
                            'delivered'  => 'badge-success',
                            'returned'   => 'badge-error'
                        ];
                    @endphp
                    <x-badge :value="$row->status" :class="$colors[$row->status] ?? 'badge-neutral'" />
                @endscope

                @scope('cell_assigned_at', $row)
                    {{ $row->assigned_at ? $row->assigned_at->format('d/m/Y H:i') : '-' }}
                @endscope
            </x-table>
        </x-card>
    </div>
</div>
