<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Order;
use App\Models\Address;
use App\Models\Delivery;

class Dashboard extends Component
{
    public $statusColors = [
        'pending' => 'badge-warning',
        'confirmed' => 'badge-info',
        'delivered' => 'badge-success',
        'cancelled' => 'badge-error',
    ];

    public function render()
    {
        $user = auth()->user();

        return view('livewire.customer.dashboard', [
            'totalOrders' => $user->orders()->count(),
            'pendingOrders' => $user->orders()->whereIn('status', ['pending', 'confirmed'])->count(),
            'addressCount' => $user->addresses()->count(),
            'recentOrders' => $user->orders()
                ->with(['orderItems'])
                ->latest()
                ->take(3)
                ->get(),
            'currentDeliveries' => $user->orders()
                ->whereHas('deliveries')
                ->with(['deliveries'])
                ->latest()
                ->take(2)
                ->get()
                ->pluck('deliveries')
                ->flatten(),
            'recentAddresses' => $user->addresses()
                ->latest()
                ->take(3)
                ->get(),
            'headers' => [
                ['key' => 'id', 'label' => 'Commande'],
                ['key' => 'created_at', 'label' => 'Date'],
                ['key' => 'status', 'label' => 'Statut'],
                ['key' => 'total_price', 'label' => 'Total']
            ]
        ]);
    }
}