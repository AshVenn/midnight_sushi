<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'total_price' => $this->faker->randomFloat(2, 20, 200), // e.g., 20.00 to 200.00
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['Stripe', 'PayPal']),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
        ];
    }
}
