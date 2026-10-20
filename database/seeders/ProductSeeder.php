<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::insert([
            [
                'name' => 'Wireless Earphone',
                'description' => 'Earphone bluetooth dengan kualitas suara jernih',
                'price' => 350000,
                'stock' => 50,
                'image' => 'uploads/products/earphone.png',
                'category_id' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'Keyboard mechanical RGB untuk gaming dan kerja',
                'price' => 750000,
                'stock' => 30,
                'image' => 'uploads/products/keyboard.png',
                'category_id' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Lampu LED Meja',
                'description' => 'Lampu meja LED hemat energi',
                'price' => 180000,
                'stock' => 40,
                'image' => 'uploads/products/lampu.png',
                'category_id' => 4,
                'is_available' => 1,
            ],
            [
                'name' => 'Macbook Pro M2',
                'description' => 'Laptop Apple performa tinggi',
                'price' => 28000000,
                'stock' => 10,
                'image' => 'uploads/products/mac.png',
                'category_id' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Macbook Air M1',
                'description' => 'Laptop ringan dan powerful',
                'price' => 19000000,
                'stock' => 12,
                'image' => 'uploads/products/mac2.png',
                'category_id' => 2,
                'is_available' => 1,
            ],
            [
                'name' => 'Sepatu Sneakers',
                'description' => 'Sepatu kasual pria dan wanita',
                'price' => 450000,
                'stock' => 60,
                'image' => 'uploads/products/sepatu.png',
                'category_id' => 1,
                'is_available' => 1,
            ],
            [
                'name' => 'Sepatu Sport',
                'description' => 'Sepatu olahraga nyaman dan ringan',
                'price' => 520000,
                'stock' => 45,
                'image' => 'uploads/products/sepatu2.png',
                'category_id' => 8,
                'is_available' => 1,
            ],
            [
                'name' => 'Google Chromecast',
                'description' => 'Streaming device dari Google',
                'price' => 900000,
                'stock' => 20,
                'image' => 'uploads/products/google.png',
                'category_id' => 2,
                'is_available' => 1,
            ],
   
        ]);
    }
}
