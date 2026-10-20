<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title' => 'Promo Summer Sale',
            'description' => 'Diskon hingga 70% untuk semua produk pilihan.',
            'image' => 'banners/banner1.jpg',
            'link' => '/products',
            'is_active' => 1,
            'position' => 1,
        ]);

        Banner::create([
            'title' => 'Flash Sale',
            'description' => 'Flash Sale hanya hari ini.',
            'image' => 'banners/banner2.jpg',
            'link' => '/flash-sale',
            'is_active' => 1,
            'position' => 2,
        ]);

        Banner::create([
            'title' => 'Produk Terbaru',
            'description' => 'Lihat koleksi produk terbaru kami.',
            'image' => 'banners/banner3.jpg',
            'link' => '/new-arrivals',
            'is_active' => 1,
            'position' => 3,
        ]);

        Banner::create([
            'title' => 'Gratis Ongkir',
            'description' => 'Gratis ongkir ke seluruh Indonesia.',
            'image' => 'banners/banner4.jpg',
            'link' => '/promo',
            'is_active' => 1,
            'position' => 4,
        ]);
    }
}