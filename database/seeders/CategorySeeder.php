<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Espresso Based',
                'slug' => 'espresso-based',
                'description' => 'Minuman kopi berbasis espresso.',
            ],
            [
                'name' => 'Manual Brew',
                'slug' => 'manual-brew',
                'description' => 'Kopi yang diseduh secara manual.',
            ],
            [
                'name' => 'Non Coffee',
                'slug' => 'non-coffee',
                'description' => 'Minuman selain kopi.',
            ],
            [
                'name' => 'Signature Coffee',
                'slug' => 'signature-coffee',
                'description' => 'Minuman kopi khas Rasa Kopi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
