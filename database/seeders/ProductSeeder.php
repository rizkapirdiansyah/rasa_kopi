<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $espresso = Category::where('slug', 'espresso-based')->first();
        $manualBrew = Category::where('slug', 'manual-brew')->first();
        $nonCoffee = Category::where('slug', 'non-coffee')->first();
        $signature = Category::where('slug', 'signature-coffee')->first();

        $products = [
            [
                'category_id' => $espresso->id,
                'name' => 'Americano',
                'slug' => 'americano',
                'description' => 'Espresso dengan tambahan air panas.',
                'price' => 18000,
                'stock' => 30,
            ],
            [
                'category_id' => $espresso->id,
                'name' => 'Cafe Latte',
                'slug' => 'cafe-latte',
                'description' => 'Perpaduan espresso dan susu steamed.',
                'price' => 22000,
                'stock' => 25,
            ],
            [
                'category_id' => $manualBrew->id,
                'name' => 'V60',
                'slug' => 'v60',
                'description' => 'Kopi seduh manual dengan metode V60.',
                'price' => 25000,
                'stock' => 15,
            ],
            [
                'category_id' => $nonCoffee->id,
                'name' => 'Matcha Latte',
                'slug' => 'matcha-latte',
                'description' => 'Minuman matcha dengan susu.',
                'price' => 23000,
                'stock' => 20,
            ],
            [
                'category_id' => $signature->id,
                'name' => 'Kopi Susu Gula Aren',
                'slug' => 'kopi-susu-gula-aren',
                'description' => 'Kopi susu dengan perpaduan gula aren.',
                'price' => 20000,
                'stock' => 35,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
