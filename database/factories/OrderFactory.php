<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;
    
    public function definition(): array
    {
        $customer = User::where('role', 'customer')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'customer']);
        $address = Address::where('user_id', $customer->id)->inRandomOrder()->first() ?? Address::factory()->create(['user_id' => $customer->id]);

        return [
            'customer_id' => $customer->id,
            'shipping_address_id' => $address->id,
            'address_text' => $address->full_address,
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'preparing', 'ready','out_for_delivery', 'delivered', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'cash']),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
        ];
    }
}
