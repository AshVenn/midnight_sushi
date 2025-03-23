<?php

namespace App\Livewire\Checkout;


use App\Services\StripeService;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class Success extends Component
{
    use Toast;

    public Order $order;
    
  public function mount(Order $order, StripeService $stripeService)
        {
            // Validate order ownership
            if ($order->customer_id !== auth()->id()) {
                abort(403, 'Unauthorized access to this order.');
            }

            // Forget the cart session
            Session::forget('cart');

            // Retrieve Stripe session ID from the request
            $sessionId = request()->query('session_id');

            if ($sessionId) {
                try {
                    // Retrieve Stripe Session
                    $stripeSession = $stripeService->retrieveSession($sessionId);

                    // Ensure the payment was successful before updating the order
                    if ($stripeSession->payment_status === 'paid') {
                        // Update order status
                        $order->update([
                            'status'         => 'confirmed',  // Change order status to confirmed
                            'payment_status' => 'paid',      // Mark payment as completed
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Stripe Payment Confirmation Error: ' . $e->getMessage());
                }
            }
        }

    public function render()
    {
        return view('livewire.checkout.success', [
            'order' => $this->order,
        ]);
    }
}
