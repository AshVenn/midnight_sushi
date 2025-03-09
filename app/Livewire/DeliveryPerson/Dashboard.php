<?php

namespace App\Livewire\DeliveryPerson;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Delivery;
use Mary\Traits\Toast;
use Illuminate\Support\Arr;

class Dashboard extends Component
{
    use Toast;

    public $latestDeliveriesHeaders= [
        ['key' => 'order_id',    'label' => 'Commande'],
        ['key' => 'status',      'label' => 'Statut'],
        ['key' => 'assigned_at', 'label' => "Date d'assignation"],
    ];

    public array $deliveryChart = [
        'type' => 'pie',
        'data' => [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Livraisons',
                    'data' => [],
                    'backgroundColor' => ['#529d74', '#eea289', '#88a0c4', '#f87171'],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ]
            ]
        ],
        'options' => [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Répartition des livraisons',
                    'font' => [
                        'size' => 16
                    ]
                ]
            ]
        ]
    ];

    public function mount()
    {
        $this->updateChartData();
    }

    protected function updateChartData()
    {
        $user = Auth::user();
        
        $statusDistribution = Delivery::where('delivery_person_id', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [
                $this->getStatusLabel($item->status) => $item->count
            ]);

        Arr::set($this->deliveryChart, 'data.labels', $statusDistribution->keys()->toArray());
        Arr::set($this->deliveryChart, 'data.datasets.0.data', $statusDistribution->values()->toArray());
    }

    protected function getStatusLabel($status): string
    {
        return match($status) {
            'assigned' => 'Attribuées',
            'picked_up' => 'Ramassées',
            'delivered' => 'Livrées',
            'returned' => 'Retournées',
            default => 'Inconnu'
        };
    }

    public function randomize()
    {
        $this->updateChartData();
        $this->success('Graphique actualisé!');
    }

    public function switchChartType()
    {
        $type = $this->deliveryChart['type'] === 'bar' ? 'pie' : 'bar';
        Arr::set($this->deliveryChart, 'type', $type);
        $this->success('Type de graphique changé!');
    }

    public function render()
    {
        $user = Auth::user();
        
        $stats = [
            'today' => Delivery::whereDate('assigned_at', today())
                ->where('delivery_person_id', $user->id)
                ->count(),
                
            'in_progress' => Delivery::where('delivery_person_id', $user->id)
                ->whereIn('status', ['assigned', 'picked_up'])
                ->count(),
                
            'completed' => Delivery::where('delivery_person_id', $user->id)
                ->where('status', 'delivered')
                ->count(),
        ];

        $recentDeliveries = Delivery::where('delivery_person_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.delivery-person.dashboard', [
            'stats' => $stats,
            'recentDeliveries' => $recentDeliveries,
            'latestDeliveriesHeaders'=>$this->latestDeliveriesHeaders
        ]);
    }
}