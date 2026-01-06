<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $fashion     = Category::where('name', 'Fashion')->first();
        $books       = Category::where('name', 'Books')->first();

        Product::insert([
            [
                'name' => 'Laptop Asus Vivobook',
                'description' => 'Laptop suitable for work and study',
                'price' => 8500000,
                'stock' => 15,
                'image' => 'products/laptop.png',
                'category_id' => $electronics->id,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samsung Galaxy A15',
                'description' => 'Affordable Android smartphone',
                'price' => 3200000,
                'stock' => 30,
                'image' => 'products/phone.png',
                'category_id' => $electronics->id,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Casual T-Shirt',
                'description' => 'Comfortable cotton t-shirt',
                'price' => 75000,
                'stock' => 50,
                'image' => 'products/tshirt.png',
                'category_id' => $fashion->id,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laravel for Beginners',
                'description' => 'Beginner friendly Laravel book',
                'price' => 120000,
                'stock' => 20,
                'image' => 'products/book.png',
                'category_id' => $books->id,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
