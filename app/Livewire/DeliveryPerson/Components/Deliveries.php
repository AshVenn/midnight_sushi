<?php

namespace App\Livewire\DeliveryPerson\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Mary\Traits\Toast;

use App\Models\Delivery;
use App\Mail\DeliveryPickedUpMail;
use App\Mail\DeliveryDeliveredMail;
use App\Mail\DeliveryReturnedMail;

class Deliveries extends Component
{
    use WithPagination, Toast;

    // Filtering, searching, sorting
    public string $search = '';
    public string $statusFilter = 'all';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public array $sortBy = ['column' => 'assigned_at', 'direction' => 'desc'];

    // Existing single-action logic
    // (markAsPickedUp, markAsDelivered, etc.) – these remain for backward compatibility
    public ?int $selectedDeliveryId = null;

    // NEW: Unified approach for actions (picked up, delivered, returned)
    public ?string $selectedAction = null;
    public bool $showActionModal = false;
    public bool $showReturnReasonModal = false;
    public string $selectedReturnReason = '';
    public string $customReturnReason = '';

    public bool $showCustomReason = false;

    // Delivery details modal
    public bool $showDeliveryDetailsModal = false;
    public ?Delivery $deliveryToShow = null;

    // Possible actions (mirroring admin’s approach, but only the ones you allow for delivery person)
    public array $actionsOptions = [
        ['id' => 'mark_picked_up', 'name' => 'Marquer comme Ramassée'],
        ['id' => 'mark_delivered', 'name' => 'Marquer comme Livrée'],
        ['id' => 'mark_returned',  'name' => 'Marquer comme Retournée'],
    ];

    // Return reasons
    public array $returnReasons = [
        'Client absent',
        'Adresse incorrecte',
        'Problème de paiement',
        'Autre raison'
    ];

    /**
     * Mount
     */
    public function mount(): void
    {
        // Keep any initialization
        $this->statusFilter = 'all';
    }

    /**
     * Headers for the table.
     * (No changes, same sorting config.)
     */
    public function headers(): array
    {
        return [
            ['key' => 'id',           'label' => 'ID Livraison', 'sortable' => true],
            ['key' => 'order_id',     'label' => 'ID Commande',  'sortable' => true],
            ['key' => 'customer',     'label' => 'Client',       'sortable' => false],
            ['key' => 'address_text', 'label' => 'Adresse',      'sortable' => false],
            ['key' => 'status',       'label' => 'Statut',       'sortable' => true],
            ['key' => 'assigned_at',  'label' => 'Assignée le',  'sortable' => true],
            ['key' => 'actions',      'label' => 'Actions',      'sortable' => false],
        ];
    }

    // Keep the isSortable function
    protected function isSortable(string $column): bool
    {
        $header = collect($this->headers())->firstWhere('key', $column);
        return $header && ($header['sortable'] ?? false);
    }

    // Keep your existing search & filtering
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updateStatusFilter(): void
    {
        // Re-fetch deliveries if needed
        $this->getDeliveriesProperty();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }
    public function updatePage():void {
        $this->resetPage();
    }

    public function sortByColumn(string $column): void
    {
        if (!$this->isSortable($column)) {
            return;
        }

        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] =
                $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }

        $this->resetPage();
    }

    /**
     * ----------------------------------
     *  EXISTING: markAsPickedUp/Delivered
     * ----------------------------------
     * We keep these for legacy calls.
     */
    public function markAsPickedUp(int $deliveryId): void
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->delivery_person_id !== Auth::id()) {
            $this->error('Cette livraison ne vous est pas attribuée.');
            return;
        }

        if ($delivery->status !== 'assigned') {
            $this->error('Impossible de marquer cette livraison comme ramassée.');
            return;
        }

        $delivery->update([
            'status' => 'picked_up',
            'picked_up_at' => now()
        ]);

        // If you want to email the customer on pickup:
        Mail::to($delivery->order->customer->email)->send(new DeliveryPickedUpMail($delivery));

        $this->success('Livraison marquée comme ramassée.');
    }

    public function markAsDelivered(int $deliveryId): void
    {
        $delivery = Delivery::findOrFail($deliveryId);

        if ($delivery->delivery_person_id !== Auth::id()) {
            $this->error('Cette livraison ne vous est pas attribuée.');
            return;
        }

        if ($delivery->status !== 'picked_up') {
            $this->error('Impossible de marquer cette livraison comme livrée.');
            return;
        }

        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        // Email the customer
        Mail::to($delivery->order->customer->email)->send(new DeliveryDeliveredMail($delivery));

        $this->success('Livraison marquée comme livrée.');
    }

    /**
     * ----------------------------------
     *  NEW: Unified "Actions" Approach
     * ----------------------------------
     */

    // 1) Open the action modal
    public function performAction(int $deliveryId): void
    {
        $this->selectedDeliveryId = $deliveryId;
        $this->selectedAction     = null;
        $this->showActionModal    = true;
    }

    // 2) If the user chooses "mark_returned", we show the return reason modal
    public function requestReturnReason(): void
        {
            $this->validate(['selectedAction' => 'required']);
            
            if ($this->selectedAction === 'mark_returned') {
                $this->showReturnReasonModal = true;
                $this->showCustomReason = ($this->selectedReturnReason === 'Autre raison');
            } else {
                $this->confirmAction();
            }
        }

        // Add this method to handle the select change
        public function updatedSelectedReturnReason($value): void
        {
            $this->showCustomReason = ($value === 'Autre raison');
            
            // Clear custom reason when switching away from "Autre raison"
            if ($value !== 'Autre raison') {
                $this->customReturnReason = '';
            }
        }

    // 3) Perform the actual action after confirming
    public function confirmAction(): void
    {
        $this->validate([
            'selectedReturnReason' => 'required',
            'customReturnReason' => [
                'required_if:selectedReturnReason,Autre raison',
                'max:1000'
            ]
        ], [
            'customReturnReason.required_if' => 'Veuillez fournir une raison pour le retour.',
            'customReturnReason.max' => 'La raison ne doit pas dépasser 1000 caractères.'
        ]);
        
        if (!$this->selectedDeliveryId || !$this->selectedAction) {
            $this->error('Aucune action ou livraison sélectionnée.');
            return;
        }

        $delivery = Delivery::find($this->selectedDeliveryId);
        if (!$delivery) {
            $this->error('Livraison non trouvée.');
            return;
        }

        // Ensure it's the logged-in user's delivery
        if ($delivery->delivery_person_id !== Auth::id()) {
            $this->error('Cette livraison ne vous est pas attribuée.');
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
            Log::error("DeliveryPerson action error: {$e->getMessage()}");
            $this->error("Erreur: {$e->getMessage()}");
        }

        $this->resetModals();
    }

    // 4) The "handlePickUp", "handleDelivery", and "handleReturn" logic
    protected function handlePickUp(Delivery $delivery): void
    {
        if ($delivery->status !== 'assigned') {
            throw new \Exception('Impossible de marquer cette livraison comme ramassée.');
        }

        $delivery->update([
            'status'      => 'picked_up',
            'picked_up_at'=> now(),
        ]);

        // Optional: Email the customer
        Mail::to($delivery->order->customer->email)->send(new DeliveryPickedUpMail($delivery));
        $this->success('Livraison marquée comme ramassée.');
    }

    protected function handleDelivery(Delivery $delivery): void
    {
        if ($delivery->status !== 'picked_up') {
            throw new \Exception('Impossible de marquer cette livraison comme livrée.');
        }

        $delivery->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        // Email the customer
        Mail::to($delivery->order->customer->email)->send(new DeliveryDeliveredMail($delivery));
        $this->success('Livraison marquée comme livrée.');
    }

    protected function handleReturn(Delivery $delivery): void
    {
        if ($delivery->status !== 'picked_up') {
            throw new \Exception('Impossible de marquer cette livraison comme retournée.');
        }

        // Validate reason if it's "Autre raison"
        $this->validate([
            'selectedReturnReason' => 'required',
            'customReturnReason'   => $this->selectedReturnReason === 'Autre raison' ? 'required' : '',
        ]);

        $reason = $this->selectedReturnReason === 'Autre raison'
            ? $this->customReturnReason
            : $this->selectedReturnReason;

        $delivery->update([
            'status'        => 'returned',
            'returned_at'   => now(),
            'return_reason' => $reason,
        ]);

        // Email the customer about the return
        Mail::to($delivery->order->customer->email)->send(new DeliveryReturnedMail($delivery, $reason));
        $this->success('Livraison marquée comme retournée.');
    }

    // 5) Reset all modals
    protected function resetModals(): void
    {
        $this->reset([
            'showActionModal',
            'showReturnReasonModal',
            'selectedAction',
            'selectedDeliveryId',
            'selectedReturnReason',
            'customReturnReason',
        ]);
    }

    public function cancelAction(): void
    {
        $this->resetModals();
    }

    /**
     * ----------------------------------
     *  Show Delivery Details in a Modal
     * ----------------------------------
     */
    public function showDeliveryDetails(int $deliveryId): void
    {
        // Only fetch if it's the current user's delivery
        $delivery = Delivery::with([
                'order.customer',
                'order.orderItems.orderable',
            ])
            ->where('delivery_person_id', Auth::id())
            ->find($deliveryId);

        if (!$delivery) {
            $this->error('Livraison introuvable.');
            return;
        }

        $this->deliveryToShow = $delivery;
        $this->showDeliveryDetailsModal = true;
    }

    public function closeDeliveryDetailsModal(): void
    {
        $this->reset(['deliveryToShow', 'showDeliveryDetailsModal']);
    }

    /**
     * ----------------------------------
     *  The main query (getDeliveriesProperty)
     * ----------------------------------
     */
    public function getDeliveriesProperty()
    {
        return Delivery::where('delivery_person_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('id', 'like', "%{$this->search}%")
                        ->orWhere('order_id', 'like', "%{$this->search}%")
                        ->orWhereHas('order.customer', function ($customerQuery) {
                            $customerQuery
                                ->where('first_name', 'like', "%{$this->search}%")
                                ->orWhere('last_name', 'like', "%{$this->search}%")
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
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

    /**
     * Render
     */
    public function render()
    {
        return view('livewire.delivery-person.components.deliveries', [
            'deliveries' => $this->deliveries,
            'headers'    => $this->headers(),
        ]);
    }
}
