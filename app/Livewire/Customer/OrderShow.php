<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Mary\Traits\Toast;
class OrderShow extends Component
{
    public Order $order;
    public bool $confirmingCancellation = false;

    /**
     * Holds the "relative" order number,
     * e.g. #1 for earliest, #2 for second, etc.
     */
    public int $relativeOrderNumber = 0;

    public function mount(Order $order)
    {
        // Ensure this order belongs to the logged in user
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Eager load the order with items
        $order->load(['orderItems.orderable']);

        // Assign the loaded model to our public property
        $this->order = $order;

        // Compute the "relative" order number (i.e., 1-based index)
        $this->relativeOrderNumber = $this->computeRelativeOrderNumber($order);
    }

    

    public function cancelOrder()
    {
        if ($this->order->can_be_cancelled) {
            $this->order->update(['status' => 'cancelled']);
            $this->success(
                title: 'Commande annulée avec succès',
                redirectTo: route('customer.orders')
            );
            
        }
        
        $this->confirmingCancellation = false;
    }

    /**
     * Helper method to get the "relative" order number
     * for the current user. The earliest (by created_at) is #1, etc.
     */
    private function computeRelativeOrderNumber(Order $order): int
    {
        // Get all orders of this user in ascending order by creation date
        $allOrderIds = Order::where('customer_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->pluck('id');

        // zero-based index of the current order within the collection
        $index = $allOrderIds->search($order->id);

        // Convert to 1-based. If not found, default to 1
        return $index !== false ? ($index + 1) : 1;
    }

    /**
     * Build a timeline array for the status progression.
     * Each step includes keys like 'title', 'icon', 'is_current', etc.
     */
    public function getStatusTimelineProperty(): array
    {
        // The possible statuses in chronological order
        $allStatuses = [
            'pending'          => 'En attente',
            'confirmed'        => 'Confirmée',
            'preparing'        => 'En préparation',
            'ready'            => 'Prête',
            'out_for_delivery' => 'En cours de livraison',
            'delivered'        => 'Livrée',
            'cancelled'        => 'Annulée',
        ];

        $timeline     = [];
        $foundCurrent = false;
        $currentKey   = $this->order->status;

        foreach ($allStatuses as $key => $displayName) {
            // Check if it's the current order status
            if ($currentKey === $key) {
                $timeline[] = [
                    'key'        => $key,
                    'title'      => $displayName,
                    'date'       => $this->order->updated_at,
                    'description'=> null,
                    'icon'       => 'o-check-circle',
                    'is_current' => true,
                    'is_done'    => in_array($key, ['delivered', 'cancelled']),
                ];
                $foundCurrent = true;
            }
            // If we haven't found current yet, these are "past" statuses
            elseif (!$foundCurrent) {
                $timeline[] = [
                    'key'        => $key,
                    'title'      => $displayName,
                    'date'       => null,
                    'description'=> null,
                    'icon'       => 'o-check-circle',
                    'is_current' => false,
                    'is_done'    => true,
                ];
            }
            // Else it's "future" (pending)
            else {
                $timeline[] = [
                    'key'        => $key,
                    'title'      => $displayName,
                    'date'       => null,
                    'description'=> null,
                    'icon'       => 'o-clock',
                    'is_current' => false,
                    'is_done'    => false,
                ];
            }
        }

        return $timeline;
    }

    public function render()
    {
        return view('livewire.customer.order-show', [
            'order'          => $this->order,
            'statusTimeline' => $this->statusTimeline,
        ]);
    }
}
