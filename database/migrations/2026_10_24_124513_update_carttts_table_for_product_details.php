
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carttts', function (Blueprint $table) {
            $table->string('product_code_ref', 150)->nullable();
            $table->string('mf')->nullable();
            $table->string('car_model')->nullable();

            $table->renameColumn('quantity', 'qty');
            $table->renameColumn('unit_price', 'price');
            $table->renameColumn('brand', 'brand');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn([
                'product_code_ref',
                'mf',
                'car_model',
            ]);

            $table->renameColumn('qty', 'quantity');
            $table->renameColumn('price', 'unit_price');
        });
    }
};
