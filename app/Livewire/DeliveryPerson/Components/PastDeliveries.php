<?php

namespace App\Livewire\DeliveryPerson\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use App\Models\Delivery;

class PastDeliveries extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public array $sortBy = [
        'column'    => 'delivered_at',
        'direction' => 'desc',
    ];

    // For showing details in a modal
    public bool $showDeliveryDetailsModal = false;
    public ?Delivery $deliveryToShow = null;

    public function updatingSearch(): void
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

    protected function isSortable(string $column): bool
    {
        // Only certain columns are sortable
        $sortableColumns = ['id', 'delivered_at'];
        return in_array($column, $sortableColumns, true);
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
            $this->sortBy['column']    = $column;
            $this->sortBy['direction'] = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Show the details of a past delivery in a modal
     */
    public function showDeliveryDetails(int $deliveryId): void
    {
        // Retrieve the delivery (include related data for details)
        $delivery = Delivery::with([
                'order.customer',
                'order.orderItems.orderable',
            ])
            ->where('delivery_person_id', Auth::id())
            ->where('status', 'delivered')
            ->find($deliveryId);

        if (!$delivery) {
            $this->error('Livraison introuvable ou non autorisée.');
            return;
        }

        $this->deliveryToShow           = $delivery;
        $this->showDeliveryDetailsModal = true;
    }

    /**
     * Close the details modal
     */
    public function closeDeliveryDetailsModal(): void
    {
        $this->reset(['showDeliveryDetailsModal', 'deliveryToShow']);
    }

    /**
     * Property that returns the paginated list of past deliveries
     */
    public function getPastDeliveriesProperty()
    {
        return Delivery::where('delivery_person_id', Auth::id())
            ->where('status', 'delivered')
            // Search filters
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->search}%")
                      ->orWhere('order_id', 'like', "%{$this->search}%")
                      ->orWhereHas('order.customer', function ($customerQuery) {
                          $customerQuery->where('first_name', 'like', "%{$this->search}%")
                              ->orWhere('last_name', 'like', "%{$this->search}%")
                              ->orWhereRaw(
                                  "CONCAT(first_name, ' ', last_name) LIKE ?",
                                  ["%{$this->search}%"]
                              );
                      });
                });
            })
            ->when($this->startDate, function ($query) {
                $start = Carbon::parse($this->startDate)->startOfDay();
                $query->where('delivered_at', '>=', $start);
            })
            ->when($this->endDate, function ($query) {
                $end = Carbon::parse($this->endDate)->endOfDay();
                $query->where('delivered_at', '<=', $end);
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
    }

    public function headers(): array
    {
        return [
            ['key' => 'id',           'label' => 'ID Livraison', 'sortable' => true],
            ['key' => 'order_id',     'label' => 'ID Commande',  'sortable' => false],
            ['key' => 'customer',     'label' => 'Client',       'sortable' => false],
            ['key' => 'address_text', 'label' => 'Adresse',      'sortable' => false],
            ['key' => 'delivered_at', 'label' => 'Livré le',     'sortable' => true],
            ['key' => 'actions',      'label' => 'Actions',      'sortable' => false],
        ];
    }

    public function render()
    {
        return view('livewire.delivery-person.components.past-deliveries', [
            'deliveries' => $this->pastDeliveries,
            'headers'    => $this->headers(),
        ]);
    }
}
