<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_dummies', function (Blueprint $table) {
            $table->id();
            $table->string('sku_ref')->unique();
            $table->string('sku_no')->nullable();
            $table->string('product_code')->nullable();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->decimal('unit_price_vat', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('id_m_warehouse')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_dummies');
    }
};


    /**
     * Reverse the migrations.
     */
  

