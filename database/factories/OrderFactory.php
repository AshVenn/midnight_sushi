<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::where('role', 'customer')->inRandomOrder()->first()->id, // Random customer ID
            'delivery_person_id' => $this->faker->boolean(50) // 50% chance for null or a delivery person ID
                ? User::where('role', 'delivery_person')->inRandomOrder()->value('id')
                : null,
            'shipping_address_id' => Address::inRandomOrder()->first()->id, // Random address ID
            'total_price' => $this->faker->randomFloat(2, 10, 500), // Random total price
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'preparing', 'ready','out_for_delivery', 'delivered', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'cash']),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
        ];
    }
}
