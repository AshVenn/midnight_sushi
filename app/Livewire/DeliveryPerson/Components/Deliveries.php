<?php

namespace App\Livewire\DeliveryPerson\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use App\Models\Delivery;

class Deliveries extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $statusFilter = 'all';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public array $sortBy = ['column' => 'assigned_at', 'direction' => 'desc'];
    public ?int $selectedDeliveryId = null;
    public ?string $selectedAction = null;

    public function mount(): void
    {
        $this->statusFilter = 'all';
    }

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

    public function sortByColumn(string $column): void
    {
        if (!$this->isSortable($column)) {
            return;
        }

        if ($this->sortBy['column'] === $column) {
            $this->sortBy['direction'] = $this->sortBy['direction'] === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy['column'] = $column;
            $this->sortBy['direction'] = 'asc';
        }

        $this->resetPage();
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID Livraison', 'sortable' => true],
            ['key' => 'order_id', 'label' => 'ID Commande', 'sortable' => true],
            ['key' => 'customer', 'label' => 'Client', 'sortable' => false],
            ['key' => 'address_text', 'label' => 'Adresse', 'sortable' => false],
            ['key' => 'status', 'label' => 'Statut', 'sortable' => true],
            ['key' => 'assigned_at', 'label' => 'Assignée le', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Actions', 'sortable' => true],
        ];
    }

    protected function isSortable(string $column): bool
    {
        $header = collect($this->headers())->firstWhere('key', $column);
        return $header && ($header['sortable'] ?? false);
    }

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

        $delivery->update(['status' => 'picked_up', 'picked_up_at' => now()]);
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

        $delivery->update(['status' => 'delivered', 'delivered_at' => now()]);
        $this->success('Livraison marquée comme livrée.');
    }

    public function getDeliveriesProperty()
    {
        return Delivery::where('delivery_person_id', Auth::id())
            ->whereNotIn('status', ['delivered'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->search}%")
                      ->orWhere('order_id', 'like', "%{$this->search}%")
                      ->orWhereHas('order.customer', function ($customerQuery) {
                          $customerQuery->where('first_name', 'like', "%{$this->search}%")
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
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('assigned_at', [$start, $end]);
            })
            ->when($this->isSortable($this->sortBy['column']), function ($query) {
                $query->orderBy($this->sortBy['column'], $this->sortBy['direction']);
            })
            ->paginate(10);
    }
    

    public function render()
    {
        return view('livewire.delivery-person.components.deliveries', [
            'deliveries' => $this->deliveries,
            'headers' => $this->headers(),
        ]);
    }
}
