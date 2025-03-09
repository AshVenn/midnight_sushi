<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $customer = User::where('role', 'customer')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'customer']);

        return [
            'customer_id' => $customer->id,
            'address_text' => $this->faker->address,
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'cash']),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
            'phone_number'=>$this->faker->phoneNumber(),
            'notes'=>$this->faker->sentence(),
        ];
    }
}
