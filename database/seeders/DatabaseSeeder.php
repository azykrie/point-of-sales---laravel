<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            "name" => "Dhikri Haikal",
            "email" => "dhikrihaikal1@gmail.com",
            "password" => bcrypt("adikdikri12345"),
            "role" => "admin",
        ]);

        // Create Cashier User
        User::create([
            "name" => "Cashier",
            "email" => "cashier@pos.com",
            "password" => bcrypt("cashier123"),
            "role" => "cashier",
        ]);

        // Create Categories
        $categories = ['Food', 'Beverage', 'Snack', 'Electronics', 'Others'];
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
