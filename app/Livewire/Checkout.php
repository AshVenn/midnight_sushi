<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Mary\Traits\Toast;

class Checkout extends Component
{
    use Toast;

    public $phone_number;
    public $address_text;
    public $notes;

    protected $rules = [
        'phone_number' => 'required|string|max:15',
        'address_text' => 'required|string|max:255',
        'notes' => 'nullable|string|max:500',
    ];

    public function placeOrder()
    {
        $this->validate();

        $cart = Session::get('cart', []);

        if(empty($cart)){
            $this->error('Erreur', 'Votre panier est vide.');
            return;
        }

        DB::beginTransaction();

        try {
            $total_price = 0;

            foreach($cart as $type => $items){
                foreach($items as $item){
                    $total_price += $item['price'] * $item['quantity'];
                }
            }

            $order = Order::create([
                'customer_id' => Auth::id(),
                'total_price' => $total_price,
                'status' => 'pending',
                'phone_number' => $this->phone_number,
                'address_text' => $this->address_text,
                'notes' => $this->notes,
            ]);

            foreach($cart as $type => $items){
                foreach($items as $item){
                    OrderItem::create([
                        'order_id' => $order->id,
                        'orderable_type' => ucfirst($type),
                        'orderable_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }

            DB::commit();

            Session::forget('cart');

            $this->success('Commande passée avec succès !');
            return redirect()->route('home');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erreur', 'Erreur lors de la commande. Veuillez réessayer.');
            \Log::error('Checkout Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
