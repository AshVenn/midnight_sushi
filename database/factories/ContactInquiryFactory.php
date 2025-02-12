<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ContactInquiry;

class ContactInquiryFactory extends Factory
{
    protected $model = ContactInquiry::class;

    public function definition(): array
    {
        return [
            'name'    => $this->faker->name(),
            'email'   => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(3),
            'message' => $this->faker->paragraph(),
            'status'  => $this->faker->randomElement(['open', 'resolved']),
        ];
    }
}
