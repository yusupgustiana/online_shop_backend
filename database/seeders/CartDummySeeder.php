<?php

namespace Database\Seeders;

use App\Models\ProductDummy;
use App\Models\CustomerDummy;
use Illuminate\Database\Seeder;

class CartDummySeeder extends Seeder
{
    public function run(): void
    {
        CustomerDummy::updateOrCreate(
            ['id_m_customer' => 'CU001'],
            ['customer_name' => 'Customer Demo']
        );

        ProductDummy::updateOrCreate(
            ['sku_ref' => 'SKU001'],
            [
                'sku_no' => 'BRG001',
                'product_code' => 'PRD001',
                'name' => 'Oli Mesin 1 Liter',
                'brand' => 'Demo Brand',
                'unit_price_vat' => 150000,
                'stock' => 20,
                'id_m_warehouse' => 'WH001',
            ]
        );

        ProductDummy::updateOrCreate(
            ['sku_ref' => 'SKU002'],
            [
                'sku_no' => 'BRG002',
                'product_code' => 'PRD002',
                'name' => 'Filter Oli',
                'brand' => 'Demo Brand',
                'unit_price_vat' => 75000,
                'stock' => 15,
                'id_m_warehouse' => 'WH001',
            ]
        );

        ProductDummy::updateOrCreate(
            ['sku_ref' => 'SKU003'],
            [
                'sku_no' => 'BRG003',
                'product_code' => 'PRD003',
                'name' => 'Busi Motor',
                'brand' => 'Demo Brand',
                'unit_price_vat' => 25000,
                'stock' => 30,
                'id_m_warehouse' => 'WH002',
            ]
        );
    }
}
