<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Models\Target;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'target_id' => Target::factory(),
            'is_saving' => $this->faker->boolean(),
            'date_transaction' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 10, 10000000),
            'note' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
