<?php

namespace App\Livewire\Admin\Components\Orders;

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
    
    // New for viewing details
    public bool $showOrderDetailsModal = false;
    public ?Order $orderDetails = null;

    public ?int $selectedDeliveryPerson = null;
    public array $deliveryPeople = [];
    public ?int $orderToAssign = null;

    public array $deliveryStatusOptions = [];
    public string $deliveryStatusFilter = 'all';

    public ?int $deliveryPersonFilter = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    public array $sortBy = ['column' => 'created_at', 'direction' => 'asc'];

    public function mount(): void
    {
        $this->deliveryPeople = User::where('role', 'delivery_person')
            ->get()
            ->map(fn($user) => ['id' => $user->id, 'name' => $user->full_name])
            ->toArray();

        $this->deliveryStatusOptions = [
            ['id' => 'all', 'name' => 'Tous'],
            ['id' => 'assigned', 'name' => 'Attribué'],
            ['id' => 'unassigned', 'name' => 'Non attribué'],
            ['id' => 'ready', 'name' => 'Prêts'],
        ];
    }

    public function search(): void
    {
        $this->resetPage();
    }


    public function updatingDeliveryStatusFilter(): void { $this->resetPage(); }
    public function updatingDeliveryPersonFilter(): void { $this->resetPage(); }
    public function updatingStartDate(): void { $this->resetPage(); }
    public function updatingEndDate(): void { $this->resetPage(); }

 
    protected function isSortable(string $column): bool
    {
        $header = collect($this->headers())->firstWhere('key', $column);
        return $header && ($header['sortable'] ?? false);
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'order_number', 'label' => 'Num Commande', 'sortable' => true],
            ['key' => 'customer', 'label' => 'Client', 'sortable' => false],
            ['key' => 'address_text', 'label' => 'Adresse de livraison', 'sortable' => true],
            ['key' => 'total_price', 'label' => 'Prix total', 'sortable' => true],
            ['key' => 'status', 'label' => 'Statut', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Date et heure', 'sortable' => true],
            ['key' => 'assigned_to', 'label' => 'Attribué à', 'sortable' => false],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => false],
        ];
    }

    // Mark as Ready
    public function markAsReady(int $orderId): void
    {
        $order = Order::find($orderId);
        
        if (!$order) {
            $this->error('Commande introuvable.');
            return;
        }
        
        if ($order->status !== 'confirmed' || $order->payment_status !== 'paid') {
            $this->error('Cette commande ne peut pas être marquée comme prête.');
            return;
        }
    
        $order->update(['status' => 'ready']);
        $this->success('Commande marquée comme prête !');
    }

    // Show Assign Modal
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
                        'order_id'           => $order->id,
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status'             => 'assigned',
                        'assigned_at'        => now(),
                        'address_text'       => $addressText,
                        'phone_number'       => $order->phone_number,
                        'notes'              => $order->notes,
                        'delivery_fee'       => $order->delivery_fee,
                    ]);
                } else {
                   
                    $delivery->update([
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status'             => 'assigned',
                        'assigned_at'        => now(),
                        'address_text'       => $addressText,
                        'phone_number'       => $order->phone_number,
                        'notes'              => $order->notes,
                        'delivery_fee'       => $order->delivery_fee,
                    ]);
                }

                $this->success('Livreur attribué avec succès.');
                $this->reset(['showAssignModal', 'selectedDeliveryPerson', 'orderToAssign']);
            } else {
                $this->error('Commande introuvable.');
            }
        } else {
            $this->error("Echec de l'attribution du livreur.");
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
                        'status'             => 'assigned',
                        'assigned_at'        => now(),
                        'address_text'       => $addressText,
                        'phone_number'       => $order->phone_number,
                        'notes'              => $order->notes,
                    ]);
                } else {
                    Delivery::create([
                        'order_id'           => $order->id,
                        'delivery_person_id' => $this->selectedDeliveryPerson,
                        'status'             => 'assigned',
                        'assigned_at'        => now(),
                        'address_text'       => $addressText,
                        'phone_number'       => $order->phone_number,
                        'notes'              => $order->notes,
                    ]);
                }

                $this->success('Livreur réattribué avec succès.');
                $this->reset(['showReassignModal', 'selectedDeliveryPerson', 'orderToAssign']);
            } else {
                $this->error('Commande introuvable.');
            }
        } else {
            $this->error('Echec de la réattribution du livreur.');
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


    public function viewOrderDetails(int $orderId): void
    {
        $order = Order::with(['customer', 'orderItems.order', 'orderItems.orderable'])
                      ->find($orderId);

        if (!$order) {
            $this->error('Commande introuvable');
            return;
        }

        $this->orderDetails = $order;
        $this->showOrderDetailsModal = true;
    }

    public function orders()
    {
        return Order::with(['customer', 'deliveries.deliveryPerson'])
            ->when($this->search, function ($query) {
                $query->where('id', 'like', "%{$this->search}%")
                ->orWhere('order_number', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                          ->orWhere('last_name', 'like', "%{$this->search}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
                    });
            })
            ->when($this->deliveryStatusFilter !== 'all', function ($query) {
                if ($this->deliveryStatusFilter === 'assigned') {
                    $query->whereHas('deliveries', function ($q) {
                        $q->whereIn('status', ['assigned', 'picked_up']);
                    });
                } elseif ($this->deliveryStatusFilter === 'unassigned') {
                    $query->whereDoesntHave('deliveries', function ($q) {
                        $q->whereIn('status', ['assigned', 'picked_up']);
                    });
                } elseif ($this->deliveryStatusFilter === 'ready') {
                    $query->where('status', 'ready');
                }
            })
            ->when($this->deliveryPersonFilter, function ($query) {
                $query->whereHas('deliveries', function ($q) {
                    $q->where('delivery_person_id', $this->deliveryPersonFilter)
                      ->whereIn('status', ['assigned', 'picked_up']);
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $end   = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
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

    public function render()
    {
        return view('livewire.admin.orders.index', [
            'orders'  => $this->orders(),
            'headers' => $this->headers(),
        ]);
    }
}
