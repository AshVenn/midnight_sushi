<?php

namespace App\Mail;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryReturnedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public string $reason
    ) {}

    public function build()
    {
        return $this->subject("Retour de votre commande #{$this->delivery->order->order_number}")
            ->markdown('emails.deliveries.returned');
    }
}