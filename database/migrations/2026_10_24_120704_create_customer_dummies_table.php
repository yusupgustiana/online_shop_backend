
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_dummies', function (Blueprint $table) {
            $table->id();
            $table->string('id_m_customer')->unique();
            $table->string('customer_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_dummies');
    }
};
