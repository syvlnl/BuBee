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
        $target = $this->faker->randomFloat(2, 10, 10000000); // Angka acak antara 10 dan 10000000 dengan 2 desimal
        $amount = $this->faker->randomFloat(2, 10, $target); // Angka acak antara 10 dan 10000000 dengan 2 desimal
        $today = now()->toDateString();
        $target_date = $this->faker->date();
        if ($amount >= $target) {
            $status = 'completed';
        } elseif ($today > $target_date) {
            $status = 'failed';
        } else {
            $status = 'active';
        }
        return [
            'user_id' => User::factory(), // Menggunakan factory untuk membuat user baru
            'title' => $this->faker->sentence(),
            'amount' => $amount,
            'target' => $target,
            'target_date' => $this->faker->date(),
            'status' => $status, // Status berdasarkan logika yang telah ditentukan
        ];
    }
}
