<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'image' => 'categories/electronics.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing and fashion products',
                'image' => 'categories/fashion.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Books',
                'description' => 'Books and learning materials',
                'image' => 'categories/books.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
