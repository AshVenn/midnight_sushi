<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Mary\Traits\Toast;

class Checkout extends Component
{
    use Toast;

    public $phone_number;
    public $addresses;
    public $address_type = 'custom';
    public $selected_address = null;
    public $address_text = '';
    public $notes;
    public $deliveryFee;

    protected $rules = [
        'selected_address' => 'required_if:address_type,saved|exists:addresses,id',
        'phone_number' => [
            'required',
            'string',
            'regex:/^(\+41|0)(\d{2} ?\d{3} ?\d{2} ?\d{2}|\d{9})$/'
        ],
        'address_text' => 'required_if:address_type,custom|string|max:500',
        'notes' => 'nullable|string|max:500',
    ];
    
    protected $messages = [
        'selected_address.required_if' => 'Veuillez sélectionner une adresse enregistrée.',
        'address_text.required_if' => 'Veuillez entrer une adresse de livraison.',
        'phone_number.required' => 'Le numéro de téléphone est obligatoire.',
        'phone_number.regex' => 'Format invalide. Ex: +41 79 123 45 67 ou 079 123 45 67',
    ];

    public function mount()
    {
        $this->deliveryFee = Setting::getValue('delivery_fee', 0);
        $this->addresses = Auth::check() 
            ? Auth::user()->addresses()->get()
            : collect();

        if ($this->addresses->isNotEmpty()) {
            $this->address_type = 'saved';
            $this->selected_address = $this->addresses->first()->id;
            $this->address_text = $this->addresses->first()->full_address;
        }
    }

    public function placeOrder()
    {
        $this->phone_number = preg_replace('/\s+/', '', $this->phone_number);
        $this->validate();

        if ($this->address_type === 'saved') {
            $address = Address::find($this->selected_address);
            $this->address_text = $address->full_address;
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            $this->error('Erreur', 'Votre panier est vide.');
            return;
        }

        DB::beginTransaction();

        try {
            // Calculate prices
            $subTotal = 0;
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    $subTotal += $item['price'] * $item['quantity'];
                }
            }
            $totalPrice = $subTotal + $this->deliveryFee;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => Auth::id(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'phone_number' => $this->phone_number,
                'address_text' => $this->address_text,
                'notes' => $this->notes,
                'delivery_fee' => $this->deliveryFee,
            ]);

            // Create order items
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'orderable_type' => $type === 'menu' ? 'App\Models\Menu' : 'App\Models\Product',
                        'orderable_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }

            DB::commit();

            // Stripe integration
            Stripe::setApiKey(config('services.stripe.secret'));
            $lineItems = $this->prepareStripeLineItems($cart);
            
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'metadata' => ['order_id' => $order->id],
            ]);

            return redirect()->away($session->url);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());
            $this->error('Erreur', 'Une erreur est survenue lors de la commande: ' . $e->getMessage());
            return;
        }
    }

    protected function prepareStripeLineItems($cart): array
    {
        $lineItems = [];
        
        // Cart items
        foreach ($cart as $type => $items) {
            foreach ($items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'chf',
                        'product_data' => ['name' => $item['name']],
                        'unit_amount' => (int) ($item['price'] * 100),
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
        }

        // Delivery fee
        if ($this->deliveryFee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'chf',
                    'product_data' => ['name' => 'Frais de livraison'],
                    'unit_amount' => (int) ($this->deliveryFee * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }

    public function updateAddressText()
    {
        if ($this->selected_address) {
            $address = Address::find($this->selected_address);
            $this->address_text = $address->full_address;
        } else {
            $this->address_type = "custom";
        }
    }

    public function render()
    {
        return view('livewire.checkout.checkout');
    }
}