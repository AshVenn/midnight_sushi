<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Relations\Relation;

class OrderShow extends Component
{
    use Toast;

    public Order $order;
    public bool $confirmingCancellation = false;

    public function mount(Order $order)
    {
        // Ensure user owns this order
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        
        $morphMap = Relation::morphMap();
        $order->load([
            'orderItems' => function ($q) use ($morphMap) {
                $q->whereIn('orderable_type', array_keys($morphMap)); 
                
            },
            'orderItems.orderable', 
        ]);

        $this->order = $order;
    }

    public function cancelOrder()
    {
        if ($this->order->can_be_cancelled) {
            $this->order->update(['status' => 'cancelled']);
            $this->success(
                title: 'Commande annulée avec succès',
                redirectTo: route('customer.orders.index')
            );
        }
        $this->confirmingCancellation = false;
    }

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
            'returned'         => 'Retournée',
            'cancelled'        => 'Annulée',
        ];

        $timeline     = [];
        $foundCurrent = false;
        $currentKey   = $this->order->status;

        foreach ($allStatuses as $key => $displayName) {
            // Check if it's the current order status
            if ($currentKey === $key) {
                $timeline[] = [
                    'key'         => $key,
                    'title'       => $displayName,
                    'date'        => $this->order->updated_at,
                    'description' => null,
                    'icon'        => 'o-check-circle',
                    'is_current'  => true,
                    'is_done'     => in_array($key, ['delivered', 'returned', 'cancelled']),
                ];
                $foundCurrent = true;
            }
            // If we haven't found current yet, it's a "past" status
            elseif (!$foundCurrent) {
                $timeline[] = [
                    'key'         => $key,
                    'title'       => $displayName,
                    'date'        => null,
                    'description' => null,
                    'icon'        => 'o-check-circle',
                    'is_current'  => false,
                    'is_done'     => true,
                ];
            }
            // Otherwise it’s a "future" (pending)
            else {
                $timeline[] = [
                    'key'         => $key,
                    'title'       => $displayName,
                    'date'        => null,
                    'description' => null,
                    'icon'        => 'o-clock',
                    'is_current'  => false,
                    'is_done'     => false,
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
