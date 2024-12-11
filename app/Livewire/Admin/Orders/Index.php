<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Models\Order;
use App\Models\User;

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

    public function mount(): void
    {
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

    public function assignDelivery(int $orderId): void
    {
        $this->orderToAssign = $orderId;
        $this->showAssignModal = true;
    }

    public function confirmAssign(): void
    {
        if ($this->orderToAssign && $this->selectedDeliveryPerson) {
            $order = Order::find($this->orderToAssign);
            $order->update(['delivery_person_id' => $this->selectedDeliveryPerson]);

            $this->success('Delivery person assigned successfully.');
            $this->reset(['showAssignModal', 'selectedDeliveryPerson', 'orderToAssign']);
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
            $order->update(['delivery_person_id' => $this->selectedDeliveryPerson]);

            $this->success('Delivery person reassigned successfully.');
            $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'orderToAssign']);
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
        return Order::with(['customer', 'shippingAddress', 'deliveryPerson'])
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($query) {
                    $query->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->deliveryStatusFilter !== 'all', function ($query) {
                if ($this->deliveryStatusFilter === 'assigned') {
                    $query->whereNotNull('delivery_person_id');
                } elseif ($this->deliveryStatusFilter === 'unassigned') {
                    $query->whereNull('delivery_person_id');
                } elseif ($this->deliveryStatusFilter === 'preparing') {
                    $query->where('status', 'preparing');
                } elseif ($this->deliveryStatusFilter === 'ready') {
                    $query->where('status', 'ready');
                }
            })
            ->paginate(10);
    }
    

    public function render()
    {

        // dd($this->orders());
        return view('livewire.admin.orders.index', [
            'orders' => $this->orders(),
            'headers' => $this->headers(),
        ]);
    }
}
