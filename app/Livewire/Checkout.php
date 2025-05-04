<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{Order, OrderItem, Address, Setting, CustomMenu};
use Illuminate\Support\Facades\{Auth, Session, DB};
use Stripe\{Stripe, Checkout\Session as StripeSession};
use Mary\Traits\Toast;

class Checkout extends Component
{
    use Toast;

    /* ----------------------- Champs publics ----------------------- */
    public $phone_number;
    public $addresses;
    public $address_type = 'custom';      // saved | custom
    public $selected_address = null;      // id when saved
    public $address_text = '';            // texte libre
    public $notes;
    public $deliveryFee;

    /* ----------------------- Validation --------------------------- */
    protected $rules = [
        'selected_address' => 'nullable|required_if:address_type,saved|exists:addresses,id',
        'phone_number'     => ['required','string','regex:/^(\+41|0)(\d{2} ?\d{3} ?\d{2} ?\d{2}|\d{9})$/'],
        'address_text'     => 'required_if:address_type,custom|string|max:500',
        'notes'            => 'nullable|string|max:500',
    ];
    
    protected $messages = [
        'selected_address.required_if' => 'Veuillez sélectionner une adresse enregistrée.',
        'address_text.required_if'     => 'Veuillez saisir une adresse de livraison.',
        'phone_number.required'        => 'Le numéro de téléphone est obligatoire.',
        'phone_number.regex'           => 'Format invalide. Exemple : +41 79 123 45 67 ou 079 123 45 67',
    ];

    /* ----------------------- Cycle de vie ------------------------- */
    public function mount()
    {
        $this->deliveryFee = Setting::getValue('delivery_fee', 0);

        $this->addresses = Auth::check()
            ? Auth::user()->addresses()->get()
            : collect();

        // Pré‑sélection de la première adresse enregistrée
        if ($this->addresses->isNotEmpty()) {
            $this->address_type      = 'saved';
            $this->selected_address  = $this->addresses->first()->id;
            $this->address_text      = $this->addresses->first()->full_address;
        }
    }

    /* ----------------------- Commande ----------------------------- */
    public function placeOrder()
    {
        // nettoie les espaces dans le numéro
        $this->phone_number = preg_replace('/\s+/', '', $this->phone_number);

        $this->validate();

        // Si adresse enregistrée : on remplit address_text
        if ($this->address_type === 'saved' && $this->selected_address) {
            $address            = Address::find($this->selected_address);
            $this->address_text = $address->full_address;
        }

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
            /* ---- Calcul des montants ---- */
            $subTotal = 0;
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {
                    $subTotal += $item['price'] * $item['quantity'];
                }
            }
            $totalPrice = $subTotal + $this->deliveryFee;

            /* ---- Création de la commande ---- */
            $order = Order::create([
                'order_number'   => Order::generateOrderNumber(),
                'customer_id'    => Auth::id(),
                'total_price'    => $totalPrice,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                'phone_number'   => $this->phone_number,
                'address_text'   => $this->address_text,
                'notes'          => $this->notes,
                'delivery_fee'   => $this->deliveryFee,
            ]);

            /* ---- Articles de commande ---- */
            foreach ($cart as $type => $items) {
                foreach ($items as $item) {

                    // On relie le type au modèle concerné
                    $orderableType = match ($type) {
                        'menu'         => 'App\Models\Menu',
                        'product'      => 'App\Models\Product',
                        'custom_menu'  => 'App\Models\CustomMenu',
                        default        => null,
                    };

                    if (!$orderableType) {
                        continue; // au cas où
                    }

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

            /* ---- Paiement Stripe ---- */
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => $this->prepareStripeLineItems($cart),
                'mode'                 => 'payment',
                'success_url'          => route('checkout.success', ['order' => $order->id]).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout.cancel'),
                'metadata'             => ['order_id' => $order->id],
            ]);

            return $this->redirect($session->url);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: '.$e->getMessage());

            $this->error(
                title: 'Erreur',
                description: 'Une erreur est survenue lors de la commande : '.$e->getMessage()
            );
        }
    }

    /* ------- Préparation des line items Stripe -------- */
    protected function prepareStripeLineItems(array $cart): array
    {
        $lineItems = [];

        foreach ($cart as $type => $items) {
            foreach ($items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'chf',
                        'product_data' => ['name' => $item['name']],
                        'unit_amount'  => (int) ($item['price'] * 100),
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
        }

        // Frais de livraison
        if ($this->deliveryFee > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'chf',
                    'product_data' => ['name' => 'Frais de livraison'],
                    'unit_amount'  => (int) ($this->deliveryFee * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }

    /* -------------- Mise à jour adresse ---------------- */
    public function updateAddressText()
    {
        if ($this->selected_address) {
            $address            = Address::find($this->selected_address);
            $this->address_text = $address->full_address;
        } else {
            $this->address_type = 'custom';
        }
    }

    /* ----------------------- Rendu ---------------------- */
    public function render()
    {
        return view('livewire.checkout.checkout');
    }
}
