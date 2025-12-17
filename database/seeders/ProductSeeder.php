<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop Gaming',
            'description' => 'Laptop spesifikasi tinggi untuk gaming',
            'price' => 15000000,
            'stock' => 5,
        ]);

        Product::create([
            'name' => 'Mouse Wireless',
            'description' => 'Mouse tanpa kabel sensitivitas tinggi',
            'price' => 250000,
            'stock' => 50,
        ]);

        Product::create([
            'name' => 'Mechanical Keyboard',
            'description' => 'Keyboard mekanik dengan switch biru',
            'price' => 750000,
            'stock' => 20,
        ]);
    }
}
