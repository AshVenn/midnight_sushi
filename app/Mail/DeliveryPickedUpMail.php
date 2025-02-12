<?php

namespace App\Mail;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeliveryPickedUpMail extends Mailable
{
    use Queueable, SerializesModels;

    public Delivery $delivery;

    /**
     * Create a new message instance.
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Votre commande a été ramassée')
                    ->markdown('emails.delivery_picked_up', ['delivery' => $this->delivery]);
    }
}