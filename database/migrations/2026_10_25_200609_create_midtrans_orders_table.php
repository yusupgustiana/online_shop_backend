<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midtrans_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_id')->unique();
            $table->string('id_m_customer')->index();

            $table->string('customer_name');
            $table->string('customer_email');

            $table->decimal('subtotal_produk', 15, 2);
            $table->string('ekspedisi_code');
            $table->string('ekspedisi_name');
            $table->decimal('ekspedisi_price', 15, 2);
            $table->decimal('total', 15, 2);

            $table->string('payment_type');
            $table->string('payment');

            $table->text('address_shipping')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();

            $table->text('snap_token')->nullable();
            $table->text('redirect_url')->nullable();

            $table->string('status')->default('pending')->index();
            $table->string('payment_status')->default('pending')->index();

            $table->timestamp('expiry_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_orders');
    }
};
