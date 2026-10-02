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
        Schema::create('product_exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('returned_product_size_id')->constrained('product_sizes')->onDelete('cascade');
            $table->foreignId('new_product_size_id')->constrained('product_sizes')->onDelete('cascade');
            $table->unique(['order_id', 'returned_product_size_id', 'new_product_size_id'], 'order_exchange_unique'); // Optional: prevent duplicate exchanges for same items
            $table->decimal('old_unit_price', 15, 2);
            $table->decimal('new_unit_price', 15, 2);
            $table->decimal('difference_amount', 15, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_exchanges');
    }
};
