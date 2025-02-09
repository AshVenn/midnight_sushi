<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
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
        'notes'        => 'nullable|string|max:500',
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
            // Calculate the total price
            $total_price = 0;
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    $total_price += $item['price'] * $item['quantity'];
                }
            }

            // Create the order
            $order = Order::create([
                'customer_id'  => Auth::id(),
                'total_price'  => $total_price,
                'status'       => 'pending', // or another default status
                'phone_number' => $this->phone_number,
                'address_text' => $this->address_text,
                'notes'        => $this->notes,
            ]);

            // Create order items
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    // Map 'menu' => App\Models\Menu, 'product' => App\Models\Product
                    $orderableType = match ($type) {
                        'menu'    => \App\Models\Menu::class,
                        'product' => \App\Models\Product::class,
                        default   => throw new \Exception("Invalid orderable type: $type"),
                    };

                    OrderItem::create([
                        'order_id'       => $order->id,
                        'orderable_type' => $orderableType,
                        'orderable_id'   => $item['id'],
                        'quantity'       => $item['quantity'],
                        'price'          => $item['price'],
                    ]);
                }
            }

            DB::commit();

            // Send order confirmation email
            Mail::to(Auth::user()->email)->send(new OrderConfirmationMail($order));

            // Empty the cart
            Session::forget('cart');

            // Show success message and redirect
            $this->success(
                title: 'Commande validée !',
                description: "Votre commande #$order->id a été enregistrée.",
                redirectTo: route('home')
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

    public function render()
    {
        return view('livewire.checkout');
    }
}
