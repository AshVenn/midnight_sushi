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

        foreach ($this->reviewData as $data) {
            if ($data['rating'] !== null && $data['rating'] >= 1 && $data['rating'] <= 5) {
                $fqcn = $morphMap[$data['reviewable_type']] ?? $data['reviewable_type'];

                Review::create([
                    'user_id'         => Auth::id(),
                    'reviewable_type' => $fqcn,
                    'reviewable_id'   => $data['reviewable_id'],
                    'rating'          => $data['rating'],
                    'comment'         => $data['comment'],
                ]);
            }
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
        ]);
    }
}
