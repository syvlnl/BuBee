<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories for users
        $categories = Category::factory()->count(10)->create();

        User::factory()
            ->count(10)
            ->hasTransactions(5) // Setiap user memiliki 5 transaksi
            ->hasTargets(2) // Setiap user memiliki 2 target
            ->create();
        
        User::factory()
            ->count(25)
            ->hasTransactions(30) // Setiap user memiliki 5 transaksi
            ->hasTargets(3) // Setiap user memiliki 2 target
            ->create();

        User::factory()
            ->count(15)
            ->hasTransactions(45) // Setiap user memiliki 5 transaksi
            ->hasTargets(5) // Setiap user memiliki 2 target
            ->create();
        
        User::factory()
        ->count(5)
        ->hasTransactions(2) // Setiap user memiliki 5 transaksi
        ->hasTargets(0) // Setiap user memiliki 2 target
        ->create();
    }
}
