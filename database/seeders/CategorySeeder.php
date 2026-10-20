<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::insert([
            [
                'name' => 'Fashion',
                'description' => 'Pakaian, sepatu, tas, dan aksesoris',
                'image' => 'categories/fashion.png',
            ],
            [
                'name' => 'Elektronik',
                'description' => 'Gadget, laptop, dan aksesoris elektronik',
                'image' => 'categories/electronics.png',
            ],
            [
                'name' => 'Beauty & Skincare',
                'description' => 'Perawatan wajah, tubuh, dan kecantikan',
                'image' => 'categories/beauty.png',
            ],
            [
                'name' => 'Home & Living',
                'description' => 'Peralatan dan dekorasi rumah',
                'image' => 'categories/home-living.png',
            ],
            [
                'name' => 'Perlengkapan Bayi',
                'description' => 'Kebutuhan ibu dan bayi',
                'image' => 'categories/baby.png',
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Vitamin, suplemen, dan alat kesehatan',
                'image' => 'categories/health.png',
            ],
            [
                'name' => 'Makanan & Minuman',
                'description' => 'Makanan ringan, minuman, dan kebutuhan dapur',
                'image' => 'categories/food-drink.png',
            ],
            [
                'name' => 'Sport & Outdoor',
                'description' => 'Perlengkapan olahraga dan aktivitas luar ruangan',
                'image' => 'categories/sport-outdoor.png',
            ],
            [
                'name' => 'Otomotif',
                'description' => 'Aksesoris dan kebutuhan kendaraan',
                'image' => 'categories/automotive.png',
            ],
            [
                'name' => 'Mainan & Hobi',
                'description' => 'Mainan anak, koleksi, dan hobi',
                'image' => 'categories/toys-hobby.png',
            ],
            [
                'name' => 'Buku & Alat Tulis',
                'description' => 'Buku, alat tulis, dan perlengkapan sekolah',
                'image' => 'categories/books-stationery.png',
            ],
        ]);
    }
}