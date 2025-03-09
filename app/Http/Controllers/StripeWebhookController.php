<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $secret     = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object; // \Stripe\Checkout\Session
                $this->handleCheckoutSessionCompleted($session);
                break;

            // Handle other event types if needed
            default:
                Log::info("Unhandled event type: {$event->type}");
        }

        return response('Webhook handled', Response::HTTP_OK);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        // Retrieve order ID from session metadata
        $orderId = $session->metadata->order_id ?? null;
        if (!$orderId) {
            Log::warning("Stripe session completed but no order_id in metadata.");
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning("Order not found for ID: $orderId");
            return;
        }

        // If order is still unpaid, mark it as paid, update status, etc.
        if ($order->payment_status === 'unpaid') {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed', // or 'preparing'
            ]);

            // Send email confirmation only now that payment is confirmed
            Mail::to($order->customer->email)
                ->send(new OrderConfirmationMail($order));

            Log::info("Order #{$order->id} was successfully paid via Stripe.");
        }
    }
}
