<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        $deliveryPerson = User::where('role', 'delivery_person')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'delivery_person']);
        $order = Order::factory()->create();
        
        return [
            'order_id' => $order->id,
            'delivery_person_id' => $deliveryPerson->id,
            'status' => $this->faker->randomElement(['assigned', 'picked_up', 'delivered']),
            'assigned_at' => now(),
            'address_text' => $order->address_text,
        ];
    }
}
