<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Target;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Target>
 */
class TargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount_needed = $this->faker->randomFloat(2, 100, 10000000);
        $amount_collected = $this->faker->randomFloat(2, 0, $amount_needed);
        $deadline = $this->faker->dateTimeBetween('now', '+2 years');
        $status = $amount_collected >= $amount_needed ? 'Completed' : 'On Progress';
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(),
            'amount_needed' => $amount_needed,
            'amount_collected' => $amount_collected,
            'deadline' => $deadline,
            'status' => $status,
        ];
    }
}
