<?php

namespace App\Livewire\Admin\Deliveries;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Mary\Traits\Toast;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Order;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showFiltersModal = false;
    public bool $showReassignModal = false;
    public bool $showActionModal = false;

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

    public function mount(): void
    {
        // Fetch delivery persons
        $this->deliveryPeople = User::where('role', 'delivery_person')
            ->get()
            ->map(fn($user) => ['id' => $user->id, 'name' => $user->full_name])
            ->toArray();

        $this->deliveryStatusOptions = [
            ['id' => 'all', 'name' => 'All'],
            ['id' => 'assigned', 'name' => 'Assigned'],
            ['id' => 'picked_up', 'name' => 'Picked Up'],
            ['id' => 'delivered', 'name' => 'Delivered'],
        ];

        // Actions that can be performed on deliveries
        $this->actionsOptions = [
            ['id' => 'mark_picked_up', 'name' => 'Mark as Picked Up'],
            ['id' => 'mark_delivered', 'name' => 'Mark as Delivered'],
        ];
    }

    public function updatingSearch(): void
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
            ['key' => 'id', 'label' => 'ID'],
            ['key' => 'order_id', 'label' => 'Order ID'],
            ['key' => 'customer', 'label' => 'Customer'],
            ['key' => 'delivery_person', 'label' => 'Delivery Person'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'assigned_at', 'label' => 'Assigned At'],
            ['key' => 'delivered_at', 'label' => 'Delivered At'],
            ['key' => 'actions', 'label' => 'Actions'],
        ];
    }

    public function deliveries()
    {
        return Delivery::with(['order.customer', 'deliveryPerson'])
            ->when($this->search, function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('id', 'like', "%{$this->search}%")
                      ->orWhereHas('customer', function ($q2) {
                          $q2->where('first_name', 'like', "%{$this->search}%")
                             ->orWhere('last_name', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->deliveryPersonFilter, function ($query) {
                $query->where('delivery_person_id', $this->deliveryPersonFilter);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                // Filter by assigned_at date range
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('assigned_at', [$start, $end]);
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function openFiltersModal(): void
    {
        $this->showFiltersModal = true;
    }

    public function reassignDelivery(int $deliveryId): void
    {
        $this->selectedDeliveryId = $deliveryId;
        $this->selectedDeliveryPerson = null;
        $this->showReassignModal = true;
    }

    public function confirmReassign(): void
    {
        if ($this->selectedDeliveryId && $this->selectedDeliveryPerson) {
            $delivery = Delivery::find($this->selectedDeliveryId);
            if ($delivery) {
                $delivery->update([
                    'delivery_person_id' => $this->selectedDeliveryPerson,
                    'assigned_at' => now(),
                    'status' => 'assigned', // reassign sets it back to assigned
                ]);
                $this->success('Delivery person reassigned successfully.');
                $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'selectedDeliveryId']);
            } else {
                $this->error('Delivery not found.');
            }
        } else {
            $this->error('Please select a delivery person.');
        }
    }

    public function cancelReassign(): void
    {
        $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'selectedDeliveryId']);
    }

    public function performAction(int $deliveryId): void
    {
        $this->selectedDeliveryId = $deliveryId;
        $this->selectedAction = '';
        $this->showActionModal = true;
    }

    public function confirmAction(): void
    {
        if (!$this->selectedDeliveryId || !$this->selectedAction) {
            $this->error('No action selected or no delivery selected.');
            return;
        }

        $delivery = Delivery::find($this->selectedDeliveryId);

        if (!$delivery) {
            $this->error('Delivery not found.');
            return;
        }

        if ($this->selectedAction === 'mark_picked_up') {
            if ($delivery->status !== 'assigned') {
                $this->error('Cannot mark this delivery as picked up.');
                return;
            }
            $delivery->update([
                'status' => 'picked_up',
                'picked_up_at' => now(),
            ]);
            $this->success('Delivery marked as picked up.');
        } elseif ($this->selectedAction === 'mark_delivered') {
            if ($delivery->status !== 'picked_up') {
                $this->error('Cannot mark this delivery as delivered.');
                return;
            }
            $delivery->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
            $this->success('Delivery marked as delivered.');
        }

        $this->reset(['showActionModal', 'selectedAction', 'selectedDeliveryId']);
    }

    public function cancelAction(): void
    {
        $this->reset(['showActionModal', 'selectedAction', 'selectedDeliveryId']);
    }

    public function render()
    {
        return view('livewire.admin.deliveries.index', [
            'deliveries' => $this->deliveries(),
            'headers' => $this->headers(),
        ]);
    }
}
