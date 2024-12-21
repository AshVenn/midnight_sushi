<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Order;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        // Ensure we have a delivery person (role = delivery_person)
        $deliveryPerson = User::where('role', 'delivery_person')->inRandomOrder()->first();
        if (!$deliveryPerson) {
            $deliveryPerson = User::factory()->create(['role' => 'delivery_person']);
        }

        // Ensure we have an Order
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        return [
            'order_id' => $order->id,
            'delivery_person_id' => $deliveryPerson->id,
            'status' => $this->faker->randomElement(['assigned', 'picked_up', 'delivered']),
            'assigned_at' => now(),
            'picked_up_at' => null,
            'delivered_at' => null,
            'address_text' => $order->address_text,
            'phone_number' => $this->faker->phoneNumber(),
            'notes' => $this->faker->sentence(),
            'delivery_fee' => $this->faker->randomFloat(2, 0, 5),
        ];
    }
}
