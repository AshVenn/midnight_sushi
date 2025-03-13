<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Review;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Relations\Relation;

class Orders extends Component
{
    use WithPagination, Toast;

    public $filterStatus = '';
    public bool $showReviewDrawer = false;
    public ?int $reviewOrderId = null;
    public array $reviewData = [];

    protected $messages = [
        'reviewData.*.rating.required' => 'La note est obligatoire',
        'reviewData.*.rating.min' => 'La note minimale est 1 étoile',
        'reviewData.*.rating.max' => 'La note maximale est 5 étoiles',
        'reviewData.*.comment.max' => 'Le commentaire ne doit pas dépasser 500 caractères'
    ];

    public $filtres = [
        ['id' => '', 'name' => 'Toutes les commandes'],
        ['id' => 'pending', 'name' => 'En attente'],
        ['id' => 'confirmed', 'name' => 'Confirmée'],
        ['id' => 'preparing', 'name' => 'En préparation'],
        ['id' => 'ready', 'name' => 'Prête'],
        ['id' => 'out_for_delivery', 'name' => 'En livraison'],
        ['id' => 'delivered', 'name' => 'Livrée'],
        ['id' => 'cancelled', 'name' => 'Annulée'],
        ['id' => 'returned', 'name' => 'Retournée'],
    ];
    

    public function resetPageOnChange():void{
        $this->resetPage();
    }


    public function getOrdersProperty()
    {
        return Order::where('customer_id', Auth::id())
            ->with(['orderItems.orderable', 'deliveries'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('created_at', 'desc')
            ->paginate(5);
    }

    public function openReviewDrawer(int $orderId)
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($orderId);

        if ($order->status !== 'delivered') {
            return;
        }

        $this->reviewOrderId = $orderId;

        $morphMap        = Relation::morphMap();  
        $reverseMorphMap = array_flip($morphMap);

        $uniqueItems = $order->orderItems->unique(fn($item) => $item->orderable_type.$item->orderable_id);

        $this->reviewData = $uniqueItems->map(function ($item) use ($reverseMorphMap) {
            $alias = $reverseMorphMap[$item->orderable_type] ?? $item->orderable_type;

            $alreadyReviewed = Review::where('user_id', Auth::id())
                ->where('reviewable_type', $alias)
                ->where('reviewable_id', $item->orderable_id)
                ->exists();

            return [
                'order_item_id'   => $item->id,
                'reviewable_type' => $alias,
                'reviewable_id'   => $item->orderable_id,
                'name'            => $item->orderable->name ?? 'Article',
                'has_review'      => $alreadyReviewed,
                'rating'          => null,
                'comment'         => '',
            ];
        })
        ->filter(fn($data) => !$data['has_review'])
        ->values()
        ->toArray();

        $this->showReviewDrawer = true;
    }

    public function saveReviews()
    {
        $morphMap = Relation::morphMap();
        $validatorData = [];
    
        foreach ($this->reviewData as $index => $data) {
            $validatorData["reviewData.$index.rating"] = [
                'required',
                'integer',
                'min:1',
                'max:5'
            ];
        }
    
        $this->validate([
            'reviewData.*.rating' => 'required|integer|min:1|max:5',
            'reviewData.*.comment' => 'nullable|string|max:500'
        ]);
    
        foreach ($this->reviewData as $data) {
            $fqcn = $morphMap[$data['reviewable_type']] ?? $data['reviewable_type'];
            
            Review::create([
                'user_id'         => Auth::id(),
                'reviewable_type' => $fqcn,
                'reviewable_id'   => $data['reviewable_id'],
                'rating'          => $data['rating'],
                'comment'         => $data['comment']
            ]);
        }

        $this->showReviewDrawer = false;
        $this->reviewOrderId    = null;
        $this->reviewData       = [];
        $this->success('Vos avis ont été enregistrés avec succès!');
    }

    public function render()
    {
        return view('livewire.customer.orders', [
            'orders' => $this->orders,
            'filtres'=>$this->filtres,
        ]);
    }
}
