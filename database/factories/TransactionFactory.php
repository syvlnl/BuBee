<?php

namespace Database\Factories;

use App\Models\User;
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
            'user_id' => User::factory(), // Menggunakan factory untuk membuat user baru
            'type' => $this->faker->randomElement(['income', 'expense']),
            'title' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['Food', 'Transport', 'Entertainment', 'Health', 'Education']),
            'amount' => $this->faker->randomFloat(2, 10, 10000000), // Angka acak antara 10 dan 10000000 dengan 2 desimal
            'transaction_date' => $this->faker->date(),
        ];
    }
}
