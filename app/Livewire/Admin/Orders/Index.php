<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showAssignModal = false;
    public bool $showReassignModal = false;
    public bool $showFiltersModal = false;
    public ?int $selectedDeliveryPerson = null;
    public array $deliveryPeople = [];
    public ?int $orderToAssign = null;

    public array $deliveryStatusOptions = [];
    public string $deliveryStatusFilter = 'all';

    public ?int $deliveryPersonFilter = null; // New: Filter by delivery person
    public ?string $startDate = null; // New: Start date for filtering
    public ?string $endDate = null;   // New: End date for filtering

    public function mount(): void
    {
        // Fetch delivery people for filtering and assigning
        $this->deliveryPeople = User::where('role', 'delivery_person')
            ->get()
            ->map(fn($user) => ['id' => $user->id, 'name' => $user->full_name])
            ->toArray();

        $this->deliveryStatusOptions = [
            ['id' => 'all', 'name' => 'All'],
            ['id' => 'assigned', 'name' => 'Assigned'],
            ['id' => 'unassigned', 'name' => 'Unassigned'],
            ['id' => 'preparing', 'name' => 'Preparing'],
            ['id' => 'ready', 'name' => 'Ready'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDeliveryStatusFilter(): void
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
            ['key' => 'customer', 'label' => 'Customer'],
            ['key' => 'delivery_address', 'label' => 'Delivery Address'],
            ['key' => 'total_price', 'label' => 'Total Price'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'assigned_to', 'label' => 'Assigned To'],
            ['key' => 'actions', 'label' => 'Actions'],
        ];
    }

    public function markAsReady(int $orderId): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->error('Order not found.');
            return;
        }

        if ($order->status !== 'preparing') {
            $this->error('This order cannot be marked as ready.');
            return;
        }

        $order->update(['status' => 'ready']);
        $this->success('Order marked as ready!');
    }

    public function assignDelivery(int $orderId): void
    {
        $this->orderToAssign = $orderId;
        $this->showAssignModal = true;
    }

    public function confirmAssign(): void
    {
        if ($this->orderToAssign && $this->selectedDeliveryPerson) {
            $order = Order::find($this->orderToAssign);
            if ($order) {
                $delivery = $order->currentDelivery();
                $addressText = $order->address_text;
                if (!$delivery) {
                    Delivery::create([
                        'order_id' => $order->id,
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'address_text' => $addressText,
                    ]);
                } else {
                    $delivery->update([
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'address_text' => $addressText,
                    ]);
                }

                $this->success('Delivery person assigned successfully.');
                $this->reset(['showAssignModal', 'selectedDeliveryPerson', 'orderToAssign']);
            } else {
                $this->error('Order not found.');
            }
        } else {
            $this->error('Failed to assign delivery person.');
        }
    }

    public function reassignDelivery(int $orderId): void
    {
        $this->orderToAssign = $orderId;
        $this->showReassignModal = true;
    }

    public function confirmReassign(): void
    {
        if ($this->orderToAssign && $this->selectedDeliveryPerson) {
            $order = Order::find($this->orderToAssign);
            if ($order) {
                $delivery = $order->currentDelivery();
                $addressText = $order->address_text;
                if ($delivery) {
                    $delivery->update([
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'address_text' => $addressText,
                    ]);
                } else {
                    Delivery::create([
                        'order_id' => $order->id,
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'address_text' => $addressText,
                    ]);
                }

                $this->success('Delivery person reassigned successfully.');
                $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'orderToAssign']);
            } else {
                $this->error('Order not found.');
            }
        } else {
            $this->error('Failed to reassign delivery person.');
        }
    }

    public function cancelAssign(): void
    {
        $this->reset(['showAssignModal', 'selectedDeliveryPerson', 'orderToAssign']);
    }

    public function cancelReassign(): void
    {
        $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'orderToAssign']);
    }

    public function orders()
    {
        return Order::with(['customer', 'deliveries.deliveryPerson'])
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($query) {
                    $query->where('first_name', 'like', "%{$this->search}%")
                          ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->deliveryStatusFilter !== 'all', function ($query) {
                if ($this->deliveryStatusFilter === 'assigned') {
                    $query->whereHas('deliveries', function($q) {
                        $q->whereIn('status', ['assigned','picked_up']);
                    });
                } elseif ($this->deliveryStatusFilter === 'unassigned') {
                    $query->whereDoesntHave('deliveries', function($q) {
                        $q->whereIn('status', ['assigned','picked_up']);
                    });
                } elseif ($this->deliveryStatusFilter === 'preparing') {
                    $query->where('status', 'preparing');
                } elseif ($this->deliveryStatusFilter === 'ready') {
                    $query->where('status', 'ready');
                }
            })
            ->when($this->deliveryPersonFilter, function($query) {
                // Filter orders by the currently assigned delivery person
                // We assume an assigned or picked_up delivery = current delivery.
                $query->whereHas('deliveries', function($q) {
                    $q->where('delivery_person_id', $this->deliveryPersonFilter)
                      ->whereIn('status', ['assigned', 'picked_up']);
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            })
            ->paginate(10);
    }

    public function openFiltersModal(): void
    {
        $this->showFiltersModal = true;
    }

    public function render()
    {
        return view('livewire.admin.orders.index', [
            'orders' => $this->orders(),
            'headers' => $this->headers(),
        ]);
    }
}
