<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        $deliveryPerson = User::where('role', 'delivery_person')->inRandomOrder()->first();
        if (!$deliveryPerson) {
            $deliveryPerson = User::factory()->create(['role' => 'delivery_person']);
        }

        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        $status = $this->faker->randomElement(['assigned', 'picked_up', 'delivered', 'returned']);

        $assignedAt = Carbon::now()->subDays(rand(1, 5));
        $pickedUpAt = in_array($status, ['picked_up', 'delivered', 'returned']) ? $assignedAt->copy()->addHours(rand(1, 6)) : null;
        $deliveredAt = $status === 'delivered' ? $pickedUpAt->copy()->addHours(rand(1, 6)) : null;
        $returnedAt = $status === 'returned' ? $pickedUpAt->copy()->addHours(rand(1, 6)) : null;

        $returnReasons = [
            'Client absent',
            'Adresse incorrecte',
            'Problème de paiement',
            'Autre raison'
        ];

        return [
            'order_id'          => $order->id,
            'delivery_person_id'=> $deliveryPerson->id,
            'status'            => $status,
            'assigned_at'       => $assignedAt,
            'picked_up_at'      => $pickedUpAt,
            'delivered_at'      => $deliveredAt,
            'returned_at'       => $returnedAt,
            'return_reason'     => $status === 'returned' ? $this->faker->randomElement($returnReasons) : null,
            'address_text'      => $order->address_text,
            'phone_number'      => $this->faker->phoneNumber(),
            'notes'             => $this->faker->sentence(),
            'delivery_fee'      => $this->faker->randomFloat(2, 0, 5),
        ];
    }
}
