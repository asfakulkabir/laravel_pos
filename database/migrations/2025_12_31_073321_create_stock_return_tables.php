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
        // Stock Return Sessions
        Schema::create('stock_return_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Stock Return Items
        Schema::create('stock_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('stock_return_sessions')->cascadeOnDelete();
            $table->string('sku');
            $table->string('product_name');
            $table->string('color_name');
            $table->string('size_name');
            $table->integer('quantity');
            $table->string('reason')->nullable(); // Reason for return
            $table->integer('previous_stock')->default(0);
            $table->integer('new_stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_return_items');
        Schema::dropIfExists('stock_return_sessions');
    }
};
