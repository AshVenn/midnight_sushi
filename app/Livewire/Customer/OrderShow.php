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

    public function mount(Order $order)
    {

        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette commande.');
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

    public function getStatusTimelineProperty(): array
    {
    
        $allStatuses = [
            'pending'          => 'En attente',
            'confirmed'        => 'Confirmée',
            'ready'            => 'Prête',
            'out_for_delivery' => 'En cours de livraison',
            'delivered'        => 'Livrée',
            'returned'         => 'Retournée',
        ];

        $timeline     = [];
        $foundCurrent = false;
        $currentKey   = $this->order->status;

        foreach ($allStatuses as $key => $displayName) {
            if ($currentKey === $key) {
                $timeline[] = [
                    'key'         => $key,
                    'title'       => $displayName,
                    'date'        => $this->order->updated_at,
                    'description' => null,
                    'icon'        => 'o-check-circle',
                    'is_current'  => true,
                    // Mark as done if delivered or returned
                    'is_done'     => in_array($key, ['delivered', 'returned']),
                ];
                $foundCurrent = true;
            } elseif (!$foundCurrent) {
                $timeline[] = [
                    'key'         => $key,
                    'title'       => $displayName,
                    'date'        => null,
                    'description' => null,
                    'icon'        => 'o-check-circle',
                    'is_current'  => false,
                    'is_done'     => true,
                ];
            } else {
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
