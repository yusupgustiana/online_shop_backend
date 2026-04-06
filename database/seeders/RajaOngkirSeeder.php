<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RajaOngkirSeeder extends Seeder
{
    public function run()
    {
        // Province
        DB::table('provinces')->insert([
            ['id' => 5, 'name' => 'Jawa Barat']
        ]);

        // City
        DB::table('cities')->insert([
            [
                'id' => 60,
                'province_id' => 5,
                'name' => 'Kabupaten Bogor'
            ]
        ]);

        // District
        DB::table('districts')->insert([
            [
                'id' => 6053,
                'city_id' => 60,
                'name' => 'Cibinong'
            ]
        ]);
    }
}