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
                'image' => 'fashion.png',
            ],
            [
                'name' => 'Elektronik',
                'description' => 'Gadget, laptop, dan aksesoris elektronik',
                'image' => 'electronics.png',
            ],
            [
                'name' => 'Beauty & Skincare',
                'description' => 'Perawatan wajah, tubuh, dan kecantikan',
                'image' => 'beauty.png',
            ],
            [
                'name' => 'Home & Living',
                'description' => 'Peralatan dan dekorasi rumah',
                'image' => 'home-living.png',
            ],
            [
                'name' => 'Perlengkapan Bayi',
                'description' => 'Kebutuhan ibu dan bayi',
                'image' => 'baby.png',
            ],
            [
                'name' => 'Kesehatan',
                'description' => 'Vitamin, suplemen, dan alat kesehatan',
                'image' => 'health.png',
            ],
            [
                'name' => 'Makanan & Minuman',
                'description' => 'Makanan ringan, minuman, dan kebutuhan dapur',
                'image' => 'food-drink.png',
            ],
            [
                'name' => 'Sport & Outdoor',
                'description' => 'Perlengkapan olahraga dan aktivitas luar ruangan',
                'image' => 'sport-outdoor.png',
            ],
            [
                'name' => 'Otomotif',
                'description' => 'Aksesoris dan kebutuhan kendaraan',
                'image' => 'automotive.png',
            ],
            [
                'name' => 'Mainan & Hobi',
                'description' => 'Mainan anak, koleksi, dan hobi',
                'image' => 'toys-hobby.png',
            ],
            [
                'name' => 'Buku & Alat Tulis',
                'description' => 'Buku, alat tulis, dan perlengkapan sekolah',
                'image' => 'books-stationery.png',
            ],
        ]);
    }
}