<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
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

    public function placeOrder()
    {
        $this->validate();

        // Retrieve the cart from the session
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            $this->error('Erreur', 'Votre panier est vide.');
            return;
        }

        DB::beginTransaction();

        try {
            // 1) Calculate the total price
            $total_price = 0;
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    $total_price += $item['price'] * $item['quantity'];
                }
            }

            // 2) Create the order (unpaid & pending)
            $order = Order::create([
                'order_number'  => Order::generateOrderNumber(),
                'customer_id'   => Auth::id(),
                'total_price'   => $total_price,
                'status'        => 'pending',   // or 'preparing', your choice
                'payment_status'=> 'unpaid',
                'phone_number'  => $this->phone_number,
                'address_text'  => $this->address_text,
                'notes'         => $this->notes,
            ]);

            // 3) Add Order Items
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    // Use your correct morph type for Product or Menu
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'orderable_type' => $type === 'menu' ? 'App\Models\Menu' : 'App\Models\Product',
                        'orderable_id'   => $item['id'],
                        'quantity'       => $item['quantity'],
                        'price'          => $item['price'],
                    ]);
                }
            }

            DB::commit();

            // 4) Generate Stripe Checkout Session
            Stripe::setApiKey(config('services.stripe.secret'));
            
            // Build line items for Stripe
            $lineItems = [];
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'chf',  // or your currency
                            'product_data' => [
                                'name' => $item['name'], // or "T-Shirt" etc.
                            ],
                            // Stripe amounts are in cents
                            'unit_amount' => (int) ($item['price'] * 100),
                        ],
                        'quantity' => $item['quantity'],
                    ];
                }
            }

            // Create the session
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                // On success, we'll redirect to a route that checks payment status
                'success_url' => route('checkout.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('checkout.cancel'),
                // This metadata links the Stripe session back to the Order
                'metadata' => [
                    'order_id' => $order->id,
                ],
            ]);

            // 5) Redirect the user to Stripe Checkout
            return redirect()->away($session->url);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());
            $this->error('Erreur', 'Une erreur est survenue lors de la commande: ' . $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.checkout.checkout');
    }
}
