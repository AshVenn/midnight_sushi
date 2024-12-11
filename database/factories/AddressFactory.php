<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $user = User::where('role', 'customer')->inRandomOrder()->first() ?? User::factory()->create(['role' => 'customer']);

        return [
            'user_id' => $user->id,
            'recipient_name' => $this->faker->name,
            'street_address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
        ];
    }
}
