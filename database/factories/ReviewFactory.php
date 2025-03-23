<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;
use App\Models\Review;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $reviewableType = $this->faker->randomElement([Product::class, Menu::class]);

        $reviewable = $reviewableType::inRandomOrder()->first();

        // Ensure that the user hasn't already reviewed this item
        while (Review::where('user_id', $user->id)
            ->where('reviewable_id', $reviewable->id)
            ->where('reviewable_type', $reviewable instanceof Product ? 'Product' : 'Menu')
            ->exists()) {
            $reviewable = $reviewableType::inRandomOrder()->first();
        }

        return [
            'user_id'          => $user->id,
            'reviewable_id'    => $reviewable->id,
            'reviewable_type'  => $reviewable instanceof Product ? 'Product' : 'Menu',
            'rating'           => $this->faker->numberBetween(1, 5),
                ];
    }
}
