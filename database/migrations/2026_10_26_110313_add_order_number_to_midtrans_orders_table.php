
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('midtrans_orders', function (Blueprint $table) {
            $table->string('order_number', 30)
                ->nullable()
                ->unique()
                ->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('midtrans_orders', function (Blueprint $table) {
            $table->dropUnique(['order_number']);
            $table->dropColumn('order_number');
        });
    }
};
