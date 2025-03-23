<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function retrieveSession($sessionId)
    {
        return StripeSession::retrieve($sessionId);
    }
}