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

    /**
     * Place the order by reading data from the session cart.
     */
    public function placeOrder()
    {
        $this->validate();

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            $this->error(
                title: 'Erreur',
                description: 'Votre panier est vide.'
            );
            return;
        }

        DB::beginTransaction();

        try {
            // Calculer le total
            $total_price = 0;
            foreach($cart as $type => $items){
                foreach($items as $item){
                    $total_price += $item['price'] * $item['quantity'];
                }
            }

            // Créer la commande
            $order = Order::create([
                'customer_id'  => Auth::id(),
                'total_price'  => $total_price,
                'status'       => 'pending', // ou tout autre statut par défaut
                'phone_number' => $this->phone_number,
                'address_text' => $this->address_text,
                'notes'        => $this->notes,
            ]);

            // Ajouter les items de la commande
            foreach($cart as $type => $items){
                foreach($items as $item){
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'orderable_type' => ucfirst($type),
                        'orderable_id'   => $item['id'],
                        'quantity'       => $item['quantity'],
                        'price'          => $item['price'],
                    ]);
                }
            }

            DB::commit();

            // Vider le panier
            Session::forget('cart');

            // Toast + redirection
            $this->success(
                title: 'Commande validée !',
                description: "Votre commande #$order->id a été enregistrée.",
                redirectTo: route('home') // Ou route('orders.show', $order->id) si vous avez un détail commande
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue lors de la commande. Veuillez réessayer.'
            );
            \Log::error('Checkout Error: ' . $e->getMessage());
        }
    }

    /**
     * Affiche la vue du composant.
     */
    public function render()
    {
        return view('livewire.checkout');
    }
}
    