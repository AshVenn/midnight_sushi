<?php

namespace App\Livewire\Manager\Components\Deliveries;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Mary\Traits\Toast;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Order;
use App\Mail\DeliveryPickedUpMail;
use App\Mail\DeliveryDeliveredMail;
use App\Mail\DeliveryReturnedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showFiltersModal = false;
    public bool $showReassignModal = false;
    public bool $showActionModal = false;
    public bool $showReturnReasonModal = false;
    public bool $showDeliveryDetailsModal = false;
    
    public ?Delivery $deliveryToShow = null;
    public ?int $selectedDeliveryId = null;
    public ?int $selectedDeliveryPerson = null;
    
    public string $selectedAction = '';
    public string $selectedReturnReason = '';
    public string $customReturnReason = '';
    public bool $showCustomReason = false;

    public array $deliveryPeople = [];
    public array $deliveryStatusOptions = [];
    public array $actionsOptions = [];
    public array $returnReasons = [
        'Client absent',
        'Adresse incorrecte',
        'Problème de paiement',
        'Autre raison'
    ];

    public string $statusFilter = 'all';
    public ?string $deliveryPersonFilter = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    public function mount(): void
    {
        // Prepend an option for "Tous" to the delivery person options
        $this->deliveryPeople = collect([['id' => 'all', 'name' => 'Tous']])
            ->merge(
                User::where('role', 'delivery_person')
                    ->get()
                    ->map(fn($user) => ['id' => $user->id, 'name' => $user->full_name])
            )->toArray();

        $this->deliveryStatusOptions = [
            ['id' => 'all',       'name' => 'Tous'],
            ['id' => 'assigned',  'name' => 'Attribué'],
            ['id' => 'picked_up', 'name' => 'Ramassé'],
            ['id' => 'delivered', 'name' => 'Livré'],
            ['id' => 'returned',  'name' => 'Retourné'],
        ];

        $this->actionsOptions = [
            ['id' => 'mark_picked_up', 'name' => 'Marquer comme Ramassé'],
            ['id' => 'mark_delivered', 'name' => 'Marquer comme Livré'],
            ['id' => 'mark_returned',  'name' => 'Marquer comme Retourné'],
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
            ['key' => 'assigned_at',    'label' => 'Assignée à',      'sortable' => true],
            ['key' => 'delivered_at',   'label' => 'Livrée à',        'sortable' => true],
            ['key' => 'returned_at',    'label' => 'Retournée',       'sortable' => true],
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
            ->when($this->deliveryPersonFilter && $this->deliveryPersonFilter !== 'all', function ($query) {
                $query->where('delivery_person_id', $this->deliveryPersonFilter);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end   = Carbon::parse($this->endDate)->endOfDay();
                // Apply date filter on all three date fields
                $query->where(function ($q) use ($start, $end) {
                    $q->whereBetween('assigned_at', [$start, $end])
                      ->orWhereBetween('delivered_at', [$start, $end])
                      ->orWhereBetween('returned_at', [$start, $end]);
                });
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
                    'status'             => 'assigned',
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

    public function performAction(int $deliveryId): void
    {
        $this->selectedDeliveryId = $deliveryId;
        $this->selectedAction     = '';
        $this->showActionModal    = true;
    }

    public function updatedSelectedReturnReason($value): void
    {
        $this->showCustomReason = ($value === 'Autre raison');
        if ($value !== 'Autre raison') {
            $this->customReturnReason = '';
        }
    }

    public function requestReturnReason(): void
    {
        $this->validate(['selectedAction' => 'required']);
        
        if ($this->selectedAction === 'mark_returned') {
            $this->showReturnReasonModal = true;
        } else {
            $this->confirmAction();
        }
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
            switch ($this->selectedAction) {
                case 'mark_picked_up':
                    $this->handlePickUp($delivery);
                    break;
                case 'mark_delivered':
                    $this->handleDelivery($delivery);
                    break;
                case 'mark_returned':
                    $this->handleReturn($delivery);
                    break;
                default:
                    throw new \Exception('Action inconnue.');
            }
        } catch (\Exception $e) {
            Log::error("Delivery error: {$e->getMessage()}");
            $this->error("Erreur: {$e->getMessage()}");
        }
    
        $this->resetModals();
    }

    protected function handlePickUp(Delivery $delivery): void
    {
        if ($delivery->status !== 'assigned') {
            throw new \Exception('Impossible de marquer cette livraison comme ramassée.');
        }

        $delivery->update([
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);

        $delivery->order->update(['status' => 'out_for_delivery']);
        Mail::to($delivery->order->customer->email)->send(new DeliveryPickedUpMail($delivery));
        $this->success('Livraison marquée comme ramassée.');
    }

    protected function handleDelivery(Delivery $delivery): void
    {
        if ($delivery->status !== 'picked_up') {
            throw new \Exception('Impossible de marquer cette livraison comme livrée.');
        }

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $delivery->order->update(['status' => 'delivered']);
        Mail::to($delivery->order->customer->email)->send(new DeliveryDeliveredMail($delivery));
        $this->success('Livraison marquée comme livrée.');
    }

    protected function handleReturn(Delivery $delivery): void
    {
        if ($delivery->status !== 'picked_up') {
            throw new \Exception('Impossible de marquer cette livraison comme retournée.');
        }

        $this->validate([
            'selectedReturnReason' => 'required',
            'customReturnReason'   => $this->selectedReturnReason === 'Autre raison' ? 'required' : ''
        ]);

        $reason = $this->selectedReturnReason === 'Autre raison'
            ? $this->customReturnReason
            : $this->selectedReturnReason;

        $delivery->update([
            'status' => 'returned',
            'return_reason' => $reason,
            'returned_at' => now(),
        ]);

        $delivery->order->update(['status' => 'returned']);
        Mail::to($delivery->order->customer->email)
            ->send(new DeliveryReturnedMail($delivery, $reason));
        
        $this->success('Livraison marquée comme retournée.');
    }

    protected function resetModals(): void
    {
        $this->reset([
            'showActionModal',
            'showReturnReasonModal',
            'selectedAction',
            'selectedDeliveryId',
            'selectedReturnReason',
            'customReturnReason'
        ]);
    }

    public function cancelAction(): void
    {
        $this->resetModals();
    }

    public function showDeliveryDetails(int $deliveryId): void
    {
        $delivery = Delivery::with(['order.customer', 'order.orderItems.orderable', 'deliveryPerson'])
            ->find($deliveryId);

        if (!$delivery) {
            $this->error('Livraison non trouvée.');
            return;
        }

        $this->deliveryToShow = $delivery;
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
    
