<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->integer('address_id')->nullable();
            $table->json('shipping_address');
            $table->string('payment_method');
            $table->integer('total_quantity');
            $table->integer('tax');
            $table->decimal('shipping_fees', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->string('promo_code')->nullable();
            $table->integer('points_used')->default(0);
            $table->boolean('is_counted_in_cache')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
