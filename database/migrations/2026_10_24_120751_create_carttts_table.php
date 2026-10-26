
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carttts', function (Blueprint $table) {
            $table->id();

            $table->string('id_m_customer', 50);
            $table->string('id_sales_inquiry', 50)->nullable();

            $table->string('sku_ref', 100);
            $table->string('sku_no', 100)->nullable();
            $table->string('product_code', 100)->nullable();
            $table->string('product_name');
            $table->string('brand')->nullable();

            $table->decimal('unit_price', 15, 2)->default(0);
            $table->unsignedInteger('quantity')->default(1);

            $table->string('id_m_warehouse', 100)->nullable();

            $table->timestamps();

            $table->index([
                'id_m_customer',
                'id_sales_inquiry',
                'sku_ref',
            ], 'carts_customer_inquiry_sku_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
