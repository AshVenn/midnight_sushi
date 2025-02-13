<?php

namespace App\Livewire\Admin\Components\Deliveries;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Mary\Traits\Toast; 
use App\Models\Delivery;
use App\Models\User;
use App\Models\Order;

use App\Mail\DeliveryPickedUpMail;
use App\Mail\DeliveryDeliveredMail;
use Illuminate\Support\Facades\Mail;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showFiltersModal = false;
    public bool $showReassignModal = false;
    public bool $showActionModal = false;

    // NEW: For showing delivery details in a modal
    public bool $showDeliveryDetailsModal = false;
    public ?Delivery $deliveryToShow = null;

    public ?int $selectedDeliveryId = null;
    public ?int $selectedDeliveryPerson = null;
    public string $selectedAction = '';

    public array $deliveryPeople = [];
    public array $deliveryStatusOptions = [];
    public array $actionsOptions = [];

    public string $statusFilter = 'all';
    public ?string $deliveryPersonFilter = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    public function mount(): void
    {
        $this->deliveryPeople = User::where('role', 'delivery_person')
            ->get()
            ->map(fn($user) => ['id' => $user->id, 'name' => $user->full_name])
            ->toArray();

        $this->deliveryStatusOptions = [
            ['id' => 'all',       'name' => 'Tous'],
            ['id' => 'assigned',  'name' => 'Attribué'],
            ['id' => 'picked_up', 'name' => 'Ramassé'],
            ['id' => 'delivered', 'name' => 'Livré'],
        ];

        $this->actionsOptions = [
            ['id' => 'mark_picked_up', 'name' => 'Marquer comme Ramassé'],
            ['id' => 'mark_delivered', 'name' => 'Marquer comme Livré'],
        ];
    }

    public function search(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDeliveryPersonFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id',             'label' => 'ID Livraison',    'sortable' => true],
            ['key' => 'order_id',       'label' => 'ID Commande',     'sortable' => true],
            ['key' => 'customer',       'label' => 'Client',          'sortable' => false],
            ['key' => 'delivery_person','label' => 'Livreur',         'sortable' => false],
            ['key' => 'status',         'label' => 'Statut',          'sortable' => true],
            ['key' => 'assigned_at',    'label' => 'Assigné à',       'sortable' => true],
            ['key' => 'delivered_at',   'label' => 'Livré à',         'sortable' => true],
            ['key' => 'actions',        'label' => 'Actions',         'sortable' => false],
        ];
    }

    protected function isSortable(string $column): bool
    {
        $header = collect($this->headers())->firstWhere('key', $column);
        return $header && ($header['sortable'] ?? false);
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column']    = $column;
            $this->sortBy['direction'] = 'asc';
        }

        $this->resetPage();
    }

    public function deliveries()
    {
        return Delivery::with(['order.customer', 'deliveryPerson', 'order.orderItems.orderable'])
            ->when($this->search, function ($query) {
                $query->where('id', 'like', "%{$this->search}%")
                    ->orWhere('order_id', 'like', "%{$this->search}%")
                    ->orWhereHas('order.customer', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                          ->orWhere('last_name', 'like', "%{$this->search}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
                    });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->deliveryPersonFilter, function ($query) {
                $query->where('delivery_person_id', $this->deliveryPersonFilter);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end   = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('assigned_at', [$start, $end]);
            })
            ->when($this->isSortable($this->sortBy['column']), function ($query) {
                $query->orderBy($this->sortBy['column'], $this->sortBy['direction']);
            })
            ->paginate(10);
    }

    public function openFiltersModal(): void
    {
        $this->showFiltersModal = true;
    }

    // ======================
    //  REASSIGN DELIVERY
    // ======================

    public function reassignDelivery(int $deliveryId): void
    {
        $this->selectedDeliveryId    = $deliveryId;
        $this->selectedDeliveryPerson = null;
        $this->showReassignModal     = true;
    }

    public function confirmReassign(): void
    {
        if ($this->selectedDeliveryId && $this->selectedDeliveryPerson) {
            $delivery = Delivery::find($this->selectedDeliveryId);
            if ($delivery) {
                $delivery->update([
                    'delivery_person_id' => $this->selectedDeliveryPerson,
                    'assigned_at'        => now(),
                    'status'             => 'assigned', // reassign sets it back to assigned
                ]);
                $this->success('Livreur réassigné avec succès.');
                $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'selectedDeliveryId']);
            } else {
                $this->error('Livraison non trouvée.');
            }
        } else {
            $this->error('Veuillez sélectionner un livreur.');
        }
    }

    public function cancelReassign(): void
    {
        $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'selectedDeliveryId']);
    }

    // ======================
    //  ACTIONS ON DELIVERY
    // ======================
    public function performAction(int $deliveryId): void
    {
        $this->selectedDeliveryId = $deliveryId;
        $this->selectedAction     = '';
        $this->showActionModal    = true;
    }

    public function confirmAction(): void
    {
        if (!$this->selectedDeliveryId || !$this->selectedAction) {
            $this->error('Aucune action sélectionnée ou aucune livraison sélectionnée.');
            return;
        }
    
        $delivery = Delivery::find($this->selectedDeliveryId);
    
        if (!$delivery) {
            $this->error('Livraison non trouvée.');
            return;
        }
    
        try {
            if ($this->selectedAction === 'mark_picked_up') {
                if ($delivery->status !== 'assigned') {
                    $this->error('Impossible de marquer cette livraison comme ramassée.');
                    return;
                }
                // Update the Delivery
                $delivery->update([
                    'status'       => 'picked_up',
                    'picked_up_at' => now(),
                ]);
    
                // Update the Order status to "out_for_delivery"
                $delivery->order->update([
                    'status' => 'out_for_delivery'
                ]);
    
                // Envoi l'email "Marqué comme ramassé"
                Mail::to($delivery->order->customer->email)->send(new DeliveryPickedUpMail($delivery));
    
                $this->success('Livraison marquée comme ramassée.');
            } elseif ($this->selectedAction === 'mark_delivered') {
                if ($delivery->status !== 'picked_up') {
                    $this->error('Impossible de marquer cette livraison comme livrée.');
                    return;
                }
                // Update the Delivery
                $delivery->update([
                    'status'       => 'delivered',
                    'delivered_at' => now(),
                ]);
    
                // Update the Order status to "delivered"
                $delivery->order->update([
                    'status' => 'delivered'
                ]);
    
                // Envoi l'email "Marqué comme livré"
                Mail::to($delivery->order->customer->email)->send(new DeliveryDeliveredMail($delivery));
    
                $this->success('Livraison marquée comme livrée.');
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email pour la livraison {$delivery->id}: " . $e->getMessage());
            $this->error("Une erreur est survenue lors de l'envoi de l'email.");
        }
    
        $this->reset(['showActionModal', 'selectedAction', 'selectedDeliveryId']);
    }
    
    public function cancelAction(): void
    {
        $this->reset(['showActionModal', 'selectedAction', 'selectedDeliveryId']);
    }

    // ======================
    //  NEW: SHOW DETAILS
    // ======================
    /**
     * Show detailed info about the delivery,
     * including the order, items, customer, phone, notes, etc.
     */
    public function showDeliveryDetails(int $deliveryId): void
    {
        $delivery = Delivery::with(['order.customer', 'order.orderItems.orderable', 'deliveryPerson'])
            ->find($deliveryId);

        if (!$delivery) {
            $this->error('Livraison non trouvée.');
            return;
        }

        $this->deliveryToShow           = $delivery;
        $this->showDeliveryDetailsModal = true;
    }

    public function closeDeliveryDetailsModal(): void
    {
        $this->reset(['showDeliveryDetailsModal', 'deliveryToShow']);
    }

    public function render()
    {
        return view('livewire.admin.deliveries.index', [
            'deliveries' => $this->deliveries(),
            'headers'    => $this->headers(),
        ]);
    }
}