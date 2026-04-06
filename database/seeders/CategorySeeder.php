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
                'name' => 'Best Seller',
        
                'description' => 'Produk terlaris',
                'image' => 'menu-bestseller.png',
            ],
            [
                'name' => 'Flash Sale',
                'description' => 'Promo terbatas',
                'image' => 'menu-flashsale.png',
            ],
            [
                'name' => 'Top Rated',
                'description' => 'Rating tertinggi',
                'image' => 'menu-toprated.png',
            ],
            [
                'name' => 'More',
                'description' => 'Kategori lainnya',
                'image' => 'menu-more.png',
            ],
        ]);
    }
}
